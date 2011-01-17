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
                rv = self.request.recv(32).strip() 
                if rv != None:
                    self.request.send('%s\n' % (rv))
            except:
                print 'debug log: %s' % (rv)
            self.request.close()
            break

class ThreadedTCPServer(SocketServer.ThreadingMixIn, SocketServer.TCPServer):
    pass

if __name__ == "__main__":
    try:
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
