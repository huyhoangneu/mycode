#!/bin/env python
import os
import time
import signal
import optparse
import subprocess

def backup(backupFile):
    print 'Backup iptables to %s' % backupFile
    subprocess.call("iptables-save > %s" % backupFile, shell=True)

def restore(backupFile):
    print 'Restore iptables from %s' % backupFile
    subprocess.call("iptables-restore < %s" % backupFile, shell=True)

def main():
    usage = "usage: %prog [options] /path/to/iptables_rule_file"
    parser = optparse.OptionParser(usage=usage)
    parser.add_option('-t', '--timeout',
                      type='int', dest='timeout', default=15,
                      help='How many seconds to wait before expiring the change'
                           ', default value is 15 seconds')
    parser.add_option('-b', '--backup-file',
                      dest='backupFile', default='iptables-backup',
                      help='Filename of iptables backup file',
                      metavar='FILE')

    (options, args) = parser.parse_args()
    if not args:
        parser.print_help()
        return

    backup(options.backupFile)

    ruleFile = args[0]
    print 'Apply firewall rule file %s' % ruleFile
    subprocess.call("sh %s" % ruleFile, shell=True)

    print 'Are you sure to keep the change? (yes/no) '
    pid = os.fork()
    if pid > 0:
        answer = raw_input()
        if answer.lower().startswith('y'):
            print 'Firewall rule is applied'
        else:
            restore(options.backupFile)

        os.kill(pid, signal.SIGTERM)
        os.wait()
    else:
        try:
            for i in range(options.timeout):
                print 'Count down %d seconds to restore firewall' % \
                    (options.timeout - i)
                time.sleep(1)
            restore(options.backupFile)
        except KeyboardInterrupt:
            print 'Count down canceled.'

if __name__ == '__main__':
    main()
