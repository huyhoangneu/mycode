#!/usr/bin/env python2.6
#-*- coding:utf-8 -*-
import threading
import SocketServer
import pyip
import re, sys
class MyTCPHandler(SocketServer.StreamRequestHandler):
    def handle(self):
        rv = None
        while True:
            try:
                #raise RuntimeError('this is the error message')
                rv = self.request.recv(32).strip() 
                #cur_thread = threading.currentThread()
                #print "RECV from ", self.client_address[0]
                if rv != None and len(rv) > 7 and checkip(rv) == True:
                    (c, a) = i.getIPAddr(rv)
                    self.request.send('%s/%s\n' % (c, a))
                #print 'ipadd: %s, port: %s' % (self.request.getpeername())
                #ipaddr, port = sock.getpeername()
            except:
                print 'debug log: %s' % (rv)
            self.request.close()
            break

class ThreadedTCPServer(SocketServer.ThreadingMixIn, SocketServer.TCPServer):
    pass
def checkip(ip):
    pattern='^([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])\.([01]?\d\d?|2[0-4]\d|25[0-5])$'
    p = re.compile(pattern)
    if p.match(ip):
        return True
    else:
        return False
if __name__ == "__main__":
    try:
        #ip = '127.0.0.1'
        #print checkip(ip)
        #sys.exit()
        i = pyip.IPInfo('qqwry.dat')
        #(c, a) = i.getIPAddr(ip)
        #print '%s %s/%s' % (ip, c, a)
        #HOST, PORT = '211.100.36.235', 9999
        HOST, PORT = "localhost", 9999
        server = ThreadedTCPServer((HOST, PORT), MyTCPHandler)
        server_thread = threading.Thread(target=server.serve_forever)
        server_thread.setDaemon(True)
        server_thread.start()
        server.serve_forever()
    except KeyboardInterrupt, e:
        sys.stdout.write('\n')
    sys.exit(0)

#vim:ts=4:sw=4:et
