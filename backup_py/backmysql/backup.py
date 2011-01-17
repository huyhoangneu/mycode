#!/usr/bin/env python
# -*- coding: UTF-8 -*-
# Filename: backup.py
# Date: 2007/08/31 - 2007/09/02 - 2007/09/30
'''
用于备份Archlinux的配置文件,   cron定期自动运行
ChangeLog:
	2008/07/31		添加Mysql数据库备份
	2008/07/18		可以删除过期的备份
	2008/02/02		修改备份目标文件的文件名（%Y%m%d_%H:%M:%S）
	2008/01/25		添加自动判断脚本路径
'''
__author__ = "kldoo (kldscs@gmail.com)"
__version__ = "$Revision: 0.5 $"
__date__ = "$Date: 2008/01/25 15:56:17 $"
__copyright__ = "Copyright (c) 2007-2008 kldoo"
__license__ = "LGPL"

##默认的备份list路径 (确定目录有写权限)
backup_path = '/data/backup'
backup_days = 2 #备份保留月数，默认2个月
log_days = 7   #系统日志保留天数
mysql_user = 'root' #Mysql用户，留空则不进行数据库保存，使用root可以避免权限问题
mysql_pass = 'root' #上面指定用户的密码
mysql_db = ['mysql','test'] #数据库，可以多个，保证上面那个用户有权限
#mysql_cmd = '`which mysqldump`' #不同系统可能有所不同，尤其是自己编译的
mysql_cmd = '/usr/local/mysql/bin/mysqldump'
# 也可自己加入路径，如： '/usr/bin/mysqldump'
mysql_charset = 'utf8' #数据库编码
####################
try:
   import cPickle as pickle
except:
   import pickle
import os,time,sys
#sys.exit()
def write_logs(filename,msg):
	""" 把msg记录在file中
	"""
	f = file(filename, 'aw')
	f.write(msg+'\n')
	f.close()
'''
# 1. 默认的备份配置
#backup_db=os.path.dirname(os.path.abspath(__file__))+os.sep+'backup.db'
backup_db=backup_path+os.sep+'backup.list'
f1=file(backup_db)
source_temp = pickle.load(f1) 
#source_temp=cPickle.load(f1)  #载入字典
f1.close()
del source_temp[0]  # 删除记录项
source=source_temp.values()   # 导入路径列表
'''
# 2. 用年月作为目录,用日期和时间作为备份文件的名称
month=backup_path+os.sep+time.strftime('%Y%m')
backup_log=month+os.sep+time.strftime('%Y%m')+'_backup.log'
now=time.strftime('%Y%m%d_%H:%M:%S')

# 3. 判断是否是这个月第一次备份，并创建目录
if not os.path.exists(month):
	os.mkdir(month)
	write_logs(backup_log,now+" PATH DONE: "+ month)
	print "Make DIR:",month
	
	# 3.2 判断时间，删除两月的备份目录
	num_month=int(time.strftime('%m'))-backup_days
	num_year=int(time.strftime('%Y'))
	# 纠正过年后的时间差
	if num_month < 1: 
		num_month+=12
		num_year-=1
	num_month+=100 # 截取后两位就是二位的月数
	backup_old=backup_path+os.sep+str(num_year)+str(num_month)[1:]
	# 判断过期辈份文件夹是否存在
	if os.path.exists(backup_old):
		if os.system('rm -rf '+backup_old) == 0:
			write_logs(backup_log,now+" PATH DONE: "+ 'rm -rf '+backup_old)
			print "Remove DIR:",backup_old
		else:
			write_logs(backup_log,now+" PATH ERROR: "+ 'rm -rf '+backup_old)			
# if 新建目录 END

# 4.Msql数据库
db_path='/tmp'
if mysql_user == '' or len(mysql_db) == 0:
	pass #数据库不保存
else:
	os.chdir(db_path)  #切换文件夹
	db_dir='mysql_db'
	db_time=time.strftime('%Y%m%d%H%M%S')
	if os.path.exists(db_dir):
		db_dir=db_dir+db_time
	os.mkdir(db_dir) #创建零时文件夹
	db_path=db_path+os.sep+db_dir
	for i in range(len(mysql_db)):
		db_name=db_path+os.sep+mysql_db[i]+'_'+db_time+'.sql'
		db_command="%s -u%s -p%s --default-character-set=%s --opt %s > %s" % \
				(mysql_cmd,mysql_user,mysql_pass,mysql_charset,mysql_db[i],db_name)
		if os.system(db_command) == 0:
			write_logs(backup_log,db_time+" MySQL DONE: "+ db_name)
		else:
			write_logs(backup_log,db_time+" MySQL ERROR: "+ db_name)
	# for END
	#source.append(db_dir) #加入路径

now=time.strftime('%Y%m%d_%H:%M:%S')
# 备份文件绝对路径
target=month+os.sep+now+'.tar.gz'

# 5. 把列表source中的文件和目录压缩成文件
gz_command="tar -zcf %s %s " %(target,' '.join(source))
if os.system(gz_command) == 0:
	write_logs(backup_log,now+" FILE DONE: "+ gz_command)
	print "Tar finished. ",target
else:
	write_logs(backup_log,now+" FILE ERROR: "+ gz_command)

# 6.删除过期日志
log_command="find /var/log/ -mtime +%s | xargs rm -rf " %log_days
if os.system(log_command) == 0:
	write_logs(backup_log,now+" LOG DONE: "+ log_command)
	print "Log Clean."
else:
	write_logs(backup_log,now+" LOG ERROR : "+ log_command)

# 7.清除mysql零时文件夹
if db_path != '/tmp':
	tmp_command="rm -rf "+db_path
	if os.system(tmp_command) == 0:
		write_logs(backup_log,now+" TMP DONE: "+ tmp_command)
		print "Tmp Clean."
	else:
		write_logs(backup_log,now+" TMP ERROR : "+ tmp_command)

# file Done
# vim:ts=4:sw=4:et
