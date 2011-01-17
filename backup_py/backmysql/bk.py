#!/usr/bin/env python
# -*- coding: UTF-8 -*-
# Filename: backup.py
# Date: 
'''
ChangeLog:
	2010/05/26		Mysql数据库备份
    2010/05/28      增加导出 参数
'''
__author__ = "seacoastboy (seacoastboy@gmail.com)"
__version__ = "$Revision: 0.1 $"
__date__ = "$Date: 2010/05/26 15:56:17 $"
__copyright__ = "Copyright (c) 2010-2010 seacoastboy"
__license__ = "null"

import os,time,commands,sys
#ttserver backup


#wwwroot backup

#rsync scp
'''
rsync_if = 1 :rsync else: scp
todo:
    1.一对一 同步 支持 rsync,scp
    2.需要修改 一对多 同步 支持多个ip 多用户名 不同端口, 这里是通过 ssh 认证 同步不需要密码
    3.考虑多线程同步*
    4.日志 记录功能 error waring info ,通过邮件提醒 每天备份的情况
    5.
'''
rsync_config = {'rsync_if':'1','rsync_ip':'', 'rsync_user':'www-data', 'rsync_port':'50022', 'rsync_backup':'/data/backup/', 'rsync_remote_backup':'/data/backup'}
class rsync:
    def __init__(self, config):
        self.rsync_if = config['rsync_if']
        self.user = config['rsync_user']
        self.port = config['rsync_port']
        self.ip = config['rsync_ip']
        self.backup = config['rsync_backup']
        self.remote_backup = config['rsync_remote_backup']
        if os.path.isfile(self.backup):
            self.rsync_if = '0'
        elif os.path.isdir(self.backup):
            self.rsync_if = '1'
        else:
            self.rsync_if =''
    @staticmethod
    def cmd(cmd):
        print cmd
        return commands.getstatusoutput(cmd)
    def run(self):
        if self.rsync_if == '1':
            cmd = "rsync -avz --progress -rsh=ssh -e='ssh -p%s' %s %s@%s:%s" % \
                 (self.port, self.backup, self.user, self.ip, self.remote_backup)
        elif self.rsync_if == '0':
            cmd = 'scp -P %s %s %s@%s:%s' % (self.post, self.filename, self.user, self.ip, self.remote_backup)
        else:
            cmd = ''
        cmd = 'ls'
        if cmd:
            status, output = rsync.cmd(cmd)
            if status == 0:
                print 'suss'
            else:
                print 'fail'
                #write log
            print 'status: %s, output: %s' % (status, output)


#mysql backup
##默认的备份list路径 (确定目录有写权限)
backup_days = 2 #备份保留月数，默认2个月
log_days = 7   #系统日志保留天数
'''
需要考虑一下 备份 策略  
todo:
    1.增量 备份 实现按照 天 星期 月份
    2.考虑实现 增量 备份 通过binlog
    3.* 需要 增加记录日志功能, 同时增加邮箱提示功能
    4.
'''
#mysql_cmd = '`which mysqldump`' #不同系统可能有所不同，尤其是自己编译的
# 也可自己加入路径，如： '/usr/bin/mysqldump'
mysql_charset = 'utf8' #数据库编码
mysql_config = {'mysql_user':'root', 'mysql_pass': 'root', 'mysql_host':'', 'backup_path':'/data/backup/databases', 'mysql_inv_db':['information_schema', 'wbjz'], 'mysqldump_cmd': '/usr/local/mysql/bin/mysqldump', 'mysql_cmd': '/usr/local/mysql/bin/mysql', 'mysql_charset': 'utf8'}
####################
try:
   import cPickle as pickle
except:
   import pickle
import subprocess
class databasesbackup:
    def __init__(self, conf):
        '''初始化 数据库 连接信息'''
        self.mysql_user = conf['mysql_user']
        self.mysql_pass = conf['mysql_pass']
        if not conf['mysql_host']: 
            self.mysql_host = 'localhost' 
        else: 
            self.mysql_host = conf['mysql_host']
        self.mysql_inv_db = conf['mysql_inv_db']
        self.mysql_cmd = conf['mysql_cmd']
        self.mysqldump_cmd = conf['mysqldump_cmd']
        self.backup_path = conf['backup_path']
        self.mysql_charset = conf['mysql_charset']
    def getDB(self):
        DB_names_command="%s --user=%s --password=%s --host=%s --batch --skip-column-names -e 'show databases'" % \
                    (self.mysql_cmd, self.mysql_user, self.mysql_pass, self.mysql_host) 
        p = subprocess.Popen(DB_names_command, shell=True, close_fds=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE) 
        stdoutdata, stderrdata = p.communicate() 
        if p.returncode != 0:
            return False, error_response(DB_names_command, stderrdata)
        #| sed 's/ /%/g'`"
        for dbname in stdoutdata.split('\n'):
            if dbname and dbname not in self.mysql_inv_db:
                print dbname
                self.getsoure(dbname)
    def getsoure(self, db_name):
        db_time=time.strftime('%Y%m%d%H%M%S')
        if os.path.exists(self.backup_path) != True:
            os.makedirs(self.backup_path)
        db_name_dir=self.backup_path + os.sep + db_name + os.sep + db_time
        if os.path.exists(db_name_dir) != True:
            os.makedirs(db_name_dir)
        sql_name = db_name_dir + os.sep + db_name + '_' + db_time + '.sql'
        
        '''
        /data/opt/mysql/bin/mysqldump -uroot -proot --skip-opt -R --triggers database | gzip > database.sql
        --no-create-info，-t 只导出数据，而不添加 CREATE TABLE 语句
        --no-data，-d 不导出任何数据，只导出数据库表结构
        --routines --routines，-R 导出存储过程以及自定义函数
        --triggers 同时导出触发器。该选项默认启用，用 --skip-triggers 禁用它
        delimiter_db_sql_command = "mysqldump -uroot -proot --default-character-set=%s --skip-opt -t -d -R %s > %s" % \
                (self.mysqldump_cmd, self.mysql_user,  self.mysql_pass, self.mysql_charset, db_name, sql_name)
        查看 mysqldump 备份情况 
        tail -n 1 /backup/mysqlbackup/tmppp/$db1.$date.sql 
        '''
        db_sql_command =  "%s -u%s -p%s --default-character-set=%s --opt %s > %s" % \
               (self.mysqldump_cmd, self.mysql_user, self.mysql_pass, self.mysql_charset, db_name, sql_name)
        if subprocess.call(db_sql_command, shell=True) == 0:
            #compression(sql_name)
            print db_sql_command
            print 'suss'
        else:
            pass
        print db_sql_command
        print sql_name
        print db_name_dir
    def compession(self, sql_name):
        pass
#dbback = databasesbackup(mysql_config)
#dbback.getDB()
#sys.exit()
def write_logs(filename,msg):
	""" 把msg记录在file中
	"""
	f = file(filename, 'aw')
	f.write(msg+'\n')
	f.close()

if __name__ == "__main__":
    sy = rsync(rsync_config)
    sy.run()
    sys.exit()
    dbback = databasesbackup(mysql_config)
    dbback.getDB()
    db_time=time.strftime('%Y%m%d%H%M%S')
    print db_time
# 6.删除过期日志
#log_command="find /var/log/ -mtime +%s | xargs rm -rf " %log_days
