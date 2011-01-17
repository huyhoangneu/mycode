#!/usr/bin/env python2.6
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
#import ConfigParser
from threading import Thread
from Queue import Queue
import time, sys

"""
A threaded ssh based command dispatch system
"""
JOBS = 5
start = time.time()
queue = Queue()

def launcher(i,q):
	"""Spawns command in a thread to an ip"""
	while True:
		ip = q.get()
		#do_somthing_using(ip)
		print "Thread %s: Running: %s" % (i, ip)
		q.task_done()

if __name__ == '__main__':
	from optparse import OptionParser
	parser = OptionParser()
	parser.add_option("-p", "--product", dest="product")
	(options, args) = parser.parse_args()
	if options.product:
		print "options: %s, args: %s" % (options, args)
		ips =[] 
		num_threads = 5
		flag = 0
		if flag == 0:
			#Start thread pool
			for i in range(num_threads):
				worker = Thread(target=launcher, args=(i, queue))
				worker.setDaemon(True)
				worker.start()
			print "Main Thread Waiting"
			for job in range(JOBS):
				queue.put(job)
			queue.join()
		else:
			msg_err = 'cmd: %s failes!' % (cmds[0])
			sys.exit()
		end = time.time()
		print "Dispatch Completed in %s seconds" % float(end - start)
	else:
		parser.print_help()

# vim:ts=4:sw=4:et
