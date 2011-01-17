#!/usr/bin/env python
# -*- coding: utf-8 -*-

'''
ConfigParser 在 python3 之前版本 返回的 dict 是无序的
比较郁闷 不得不自己 排序 了！

主要功能 是 用于 同步文件 到多台服务器上
通过添加*.config.ini文件 我管理同步过程
参考 news.config.ini 文件
todo:
	加入 同步 端口
通过多线程 来同步多台 服务器, 现在
todo:
	1. 现在只有全部 同步， 没有考虑 多文件同步的时候 带来的 效率问题
	2. 完善 日志功能, 每个config 对应 一个 日志文件 这样方面 监控
	3. 完善 以上功能 后，考虑 代码重构
'''
__author__ = "seacoastboy <seacoastboy@gmail.com>"
__date__ = "2010/1/8"
__version__ = "0.1"
__license__ = ""

import subprocess
import ConfigParser
from threading import Thread
from Queue import Queue
import time, sys
import log

"""
A threaded ssh based command dispatch system
"""
CONFIG_PATH = '/data/grab/rsyncscript/'
start = time.time()
queue = Queue()

def readConfig(file):
	"""Extract IP addresses and CMDS from config file and returns tuple"""
	ips = []
	cmds = []
	lp = []
	ps = []
	conf_file = CONFIG_PATH + 'config/' + file + '.config.ini'
	Config = ConfigParser.ConfigParser()
	Config.read(conf_file)

	machines = dict(Config.items("MACHINES"))
	commands = Config.items("COMMANDS")
	localPath = Config.items("LOCALPATH")
	posts = dict(Config.items("POST"))


	m= sorted(machines.items(), key=lambda machines:machines[0])
	i= sorted(posts.items(), key=lambda posts:posts[0])
	#print zip([k[1] for k in m], [x[1] for x in i])
	#sys.exit()
	'''
	for post in p:
		posts.append(post[1])
	'''
	#for ip in machines:
		#ips.append(ip[1])
	for p in localPath:
		lp.append(p[1])
	for cmd in commands:
		cmds.append(cmd[1])
	return cmds, lp, zip([k[1] for k in m], [x[1] for x in i])
def launcher(i,q):
	"""Spawns command in a thread to an ip"""
	while True:
		#grabs ip, cmd from queue
		ip = q.get()
		p = ip[1]
		m = ip[0]
		print "Thread %s: Running %s to %s" % (i, lps[0], m)
		#logging.info("Thread %s: Running %s to %s" % (i, lps[0], ip))
		if int(p):
			cmd = "rsync -avz --progress --rsh=ssh -e 'ssh -p %s' %s %s" % (p, lps[0], m)
			#cmd = "rsync -avz --delete --progress --rsh=ssh -e 'ssh -p %s' %s %s" % (p, lps[0], m)
		else:
			cmd = "rsync -avz --progress %s %s" %  (lps[0], m)
			#cmd = "rsync -avz --delete --progress %s %s" %  (lps[0], m)
		#cmd = "rsync -avz --delete --progress %s %s" % (lps[0], ip)
		print cmd
		status = subprocess.call(cmd, shell=True)
		q.task_done()
		if status != 0:
			#print "Stream Failed"
			msg = "Stream Failed , Running %s to %s" % (lps[0], m)
			logging.error(msg)
			sys.exit()
        else:
            logging.info(cmd)

if __name__ == '__main__':
	from optparse import OptionParser
	parser = OptionParser()
	parser.add_option("-p", "--product", dest="product")
	(options, args) = parser.parse_args()
	if options.product:
		print 'options: %s, args: %s' % (options, args)
		#grab ips and cmds from config
		logging = log.initlog(options.product)
		cmds, lps, ips = readConfig(options.product)
		#Determine Number of threads to use, but max out at 25
		if len(ips) < 25:
			num_threads = len(ips)
		else:
			num_threads = 25
		#start commands
		flag = subprocess.call(cmds[0], shell=True)
		#if flag is 0 go on thread
		if flag == 0:
			#Start thread pool
			for i in range(num_threads):
				#worker = Thread(target=test, args=(i, queue))
				worker = Thread(target=launcher, args=(i, queue))
				worker.setDaemon(True)
				worker.start()
			print "Main Thread Waiting"
			#for ip in ips:
			for ip in ips:
				queue.put(ip)
			queue.join()
		else:
			msg_err = 'cmd: %s failes!' % (cmds[0])
			logging.error(msg_err)
			sys.exit()
		end = time.time()
		print "Dispatch Completed in %s seconds" % float(end - start)
	else:
		parser.print_help()

