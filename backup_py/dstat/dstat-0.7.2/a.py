from __future__ import generators
import os,sys
import MySQLdb
def addlist(alist):
    for i in alist:
        yield i + 1
'''
alist = [1, 2, 3, 4]
for x in addlist(alist):
    print x,
'''

def proc_pidlist():
    "Return a list of process IDs"
    dstat_pid = str(os.getpid())
    for pid in os.listdir('/proc/'):
        try:
            int(pid)
            if pid == dstat_pid: continue
                yield pid
        except ValueError:
            continue


mysql_user='root'
mysql_pwd='wangchao901'
mysql_host ='localhost'
try:
    db = MySQLdb.connect(host = mysql_host, user=mysql_user, passwd=mysql_pwd)
except Exception, e:
    raise Exception, 'Cannot interface with MySQL server, %s' % e
