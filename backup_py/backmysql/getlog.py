#!/usr/local/bin/python2.6
#-*- coding: utf-8 -*-
# CDN日志下载过滤

import sys, os, pprint, datetime, re,commands, time
import gzip, string, threading
from urlparse import urlparse
from multiprocessing import Process

DOWN_PATH = '/home/zouzhihai/backup_py/backmysql/log'
yesterday_LOG_DATE=(datetime.date.today() - datetime.timedelta(days=1)).strftime("%Y%m%d")
LOG_DATE=(datetime.date.today() - datetime.timedelta(days=1)).strftime("%Y%m%d")
#http://storage-2.chinacache.com/ftpDown/1616/13106/201006/13106_20100624_ncsa.gz
#command_down = os.path.abspath(os.path.dirname(sys.argv[0]))+'/mytget'
cdn_log_url={
        '5258': 'http://storage-2.chinacache.com/ftpDown/1616/13106/201006/13106_%s_ncsa.gz' % (LOG_DATE),
        }
def wgetLog():
    for k, v in cdn_log_url.iteritems():
        #cmd = "%s -n 4 %s -f %s/%s_%s.log.gz " % (command_down, v, DOWN_PATH, k, LOG_DATE)
        cmd = "wget %s -O %s/%s_%s.log.gz -o /dev/null" % (v, DOWN_PATH, k, LOG_DATE)
        status, output = commands.getstatusoutput(cmd)
        if status == 0:
            print 'down log suss'
        else:
            print "error: %s" % (output)

if __name__ == '__main__':
    from optparse import OptionParser
    parser = OptionParser()
    parser.add_option("-t", "--time", dest="time")
    (options, args) = parser.parse_args()
    if options.time:
        LOG_DATE = options.time.replace('-','').replace('_', '')
        #print time.strptime(LOG_DATE, '%Y%m%d')
        if yesterday_LOG_DATE >= LOG_DATE:
            LOG_DATE = LOG_DATE
        else:
            print '时间错误'
            sys.exit()
    else:
        LOG_DATE=LOG_DATE
    wgetLog()
# vim:ts=4:sw=4:et
