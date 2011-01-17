#!/usr/bin/env python
# -*- coding: UTF-8 -*-
# Filename: backedit.py
# Date: 2007/09/30 --- 2008/01/25 
'''
py写的arch配置文件备份程序 --=-- part2.配置部分
ChangeLog:
	2008/07/31		默认的备份list路径</media/data/arch>
	2008/04/19	/ect/abs.conf<abs2.0>
'''
__author__ = "kldoo (kldscs@gmail.com)"
__version__ = "$Revision: 0.5 $"
__date__ = "$Date: 2008/01/25 15:52:33 $"
__copyright__ = "Copyright (c) 2007-2008 kldoo"
__license__ = "LGPL"

##默认的备份list路径 (确定目录有写权限)
backup_path = '/media/data/arch'
####################

def help():
	print '''
        backedit.py default 恢复默认设置
	backedit.py show 查看备份列表
	backedit.py help 显示此页面
	backedit.py add [parameter] [...] 添加备份路径 
	backedit.py del [number] [...] 通过编号删除多余路径\n'''
	print " author: %s \n %s \n %s \n %s \n license: %s \n" \
		%(__author__,__version__,__date__,__copyright__,__license__)

import sys
import os
import cPickle
if len(sys.argv) <= 1 or sys.argv[1] == 'help':
        print " Nothing happened ? ..." 
	help()
	sys.exit()
# if Done

#判断当前脚本的工作目录
script_path=os.path.dirname(os.path.abspath(__file__))

#默认的备份配置
#backup_db=script_path+os.sep+'/backup.db'
backup_db=backup_path+os.sep+'backup.list'
print "Here is our backup DB:",backup_db

# 参数default是备份配置还原
if sys.argv[1]=='default' or not os.path.exists(backup_db):
	try:
		f1=file(backup_db,'w')
	except IOError:
		print "   No configuration file found! Creating..."
	except :
		print "   Something happened, and i don't know why."
	source={ 0  : 11,
		1  : '/etc/inittab', 
		2  : '/etc/fstab',
		3  : '/etc/fonts',
		4  : '/etc/X11/xorg.conf',
		5  : '/etc/passwd',
		6  : '/etc/group',
		7  : '/etc/resolv.conf',
		8  : '/etc/hosts',
		9  : '/etc/hosts.allow',
		10 : '/etc/hosts.deny',
		11 : script_path}
# ArchLinux 还可以加入以下几项
#		12 : '/etc/rc.conf',
#		13 : '/etc/rc.local',
#		14 : '/etc/rc.local.shutdown',
#		15 : '/etc/modprobe.conf',
#		16 : '/etc/mkinitcpio.conf', 
#		17 : '/etc/updatedb.conf',
#		18 : '/etc/makepkg.conf',
#		19  : '/etc/abs.conf',
#		20  : '/etc/pacman.d',
#		21  : '/etc/pacman.conf',		
		
	cPickle.dump(source,f1)
	f1.close()
	print "   Default configuration."
	if sys.argv[1]=='default':
		sys.exit()  #完成设置退出
	# if(default) Done

# show add del 所需的零时变量
f2=file(backup_db)
source_temp=cPickle.load(f2)
f2.close()

if sys.argv[1]=='show':  # 输出备份路径
	for  a,b in source_temp.items():
		if a == 0: 
			continue
		print "[%3d] -- %s" %(a,b)
	print "         Oh,that's all."
		# if (show) Dnoe

elif sys.argv[1]=='add':
	# 循环把新的备份路径加入source_temp中
	for number in range(2,len(sys.argv)):
		if not os.path.exists(sys.argv[number]):
			print '  File path error,ignore (%s)' %sys.argv[number]
			continue
		else:
			source_temp[0] += 1  # 人口加一
			source_temp[source_temp[0]]=sys.argv[number]
			#       source_temp.append(sys.argv[number])
			print "  Add %s to source list. " %sys.argv[number]
		f3=file(backup_db,'w')  #打开文件
		cPickle.dump(source_temp,f3)   #写入文件
		f3.close()
# if (add) Done

elif sys.argv[1]=='del':
	# 循环加入备份路径
	for number in range(2,len(sys.argv)):
		del_path=int(sys.argv[number]) # 转换成数字
		if not source_temp.has_key(del_path) or del_path <= 0:
			print '  File path error,ignore (%s)' %sys.argv[number]
			continue
		else:
			print "  Delete %s in source list. " \
				%source_temp[del_path]
			del(source_temp[del_path])  # 删除无用路径

	f4=file(backup_db,'w')  #打开文件
	cPickle.dump(source_temp,f4)   #写入文件
	f4.close()
# if (del) Done
else:
	print "   Wrong option!!! "
	help()

# file completed
