#!/bin/env python
"""Simple tool for rotating nginx log file

@author: Victor Lin (bornstub@gmail.com) blog: http://blog.ez2learn.com
"""
import os
import shutil
import optparse
import datetime
import logging
import subprocess
import sys
log = logging.getLogger(__name__)
nginx_config ={
        "script": "/etc/init.d/nginx",
        "nginbase": "",
        "log":"",
        "pid":""
        }
def main():
    print nginx_config["script"]
    usage = "usage: %prog [options] /path/to/iptables_rule_file"
    parser = optparse.OptionParser(usage=usage)
    #parser = optparse.OptionParser()
    parser.add_option('-p', '--pid', dest='pidFile', metavar="FILE", help='/path/to/nginx.pid')
    parser.add_option('-l', '--log', dest='logFile', metavar="FILE", help='/path/to/logfile')
    parser.add_option('-f', '--format', dest='nameFormat', help='format of rotated log file name' )
    parser.add_option('-d', '--dir', dest='nameDir',  default='/data/logs/', help='log file to dir' )
    parser.add_option('-o', '--owner', dest='owner', default='www-data', help='the owner user of log file to set')
    (options, args) = parser.parse_args()
    if options == None or not options:
        parser.print_help()
        return
    if options.pidFile == None:
        parser.print_help()
        return
    elif not os.path.exists(options.pidFile):
        log.info('The pid file %s does not exits' , options.pidFile)
        return
    if not os.path.exists(options.logFile):
        log.info('The log file %s does not exist', options.logFile)
        return
    if not os.path.exists(options.nameDir):
        os.mkdir(options.nameDir)
        #mkdir
        log.info('create log dir %s', options.nameDir)
    else:
        log.info('The log dir %s does exist', options.nameDir)

    # move the log file
    newName = datetime.date.today().strftime(options.nameFormat)
    if not os.path.exists(newName):
        log.info('Move log file %s to %s', options.logFile, newName)
        subprocess.check_call('mv %s %s' % (options.logFile, newName), shell=True)
        if options.owner:
            log.info('Set owner of %s to %s', newName, options.owner)
            subprocess.check_call('chown %s %s' % (options.owner, newName), shell=True)
        # tell nginx to reopen the file
        log.info('Reopen log file')
    else:
        log.info('log file %s exist' , newName)
        return

    pid = int(open(options.pidFile, 'rt').read())
    os.kill(pid, 10)
    log.info('done')
if __name__ == '__main__':
    logging.basicConfig(level=logging.INFO)
    main()
    '''
    python nginx_log_rotate.py -p /usr/nginx/logs/nginx.pid -l "/usr/nginx/logs/YOURDOMAIN.log" -f "/home/USER/logs/YOURDOMAIN.%Y-%m-%d" -o OWNER_USER
    0 0 * * * python nginx_log_rotate.py -p /usr/nginx/logs/nginx.pid -l "/usr/nginx/logs/YOURDOMAIN.log" -f "/home/USER/logs/YOURDOMAIN.%Y-%m-%d" -o OWNER_USER
    python nginx_log_rotate.py -p /data/opt/nginx/logs/nginx.pid -l "/data/opt/nginx/logs/access.log" -f "/home/zouzhihai/logs/z.log"
    '''
