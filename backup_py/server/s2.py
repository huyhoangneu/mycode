#!/usr/bin/env python
import threading
import SocketServer
import pyip
users = []
class MyTCPHandler(SocketServer.StreamRequestHandler):
    def handle(self):
        while True:
            rv = self.request.recv(32).strip() 
            #print rv
            #cur_thread = threading.currentThread()
            #print "RECV from ", self.client_address[0]
            if rv == None or len(rv) < 7:
                break
            (c, a) = i.getIPAddr(rv)
            
            try:
                self.request.send('%s/%s\n' % (c, a))
                self.request.close()
                break
            except:
                #print 'error'
                self.request.close()
                break
        #print ' closed.'

class ThreadedTCPServer(SocketServer.ThreadingMixIn, SocketServer.TCPServer):
    pass

if __name__ == "__main__":
    i = pyip.IPInfo('qqwry.dat')
    #(c, a) = i.getIPAddr(ip)
    #print '%s %s/%s' % (ip, c, a)
    HOST, PORT = "localhost",10000 
    server = ThreadedTCPServer((HOST, PORT), MyTCPHandler)
    server_thread = threading.Thread(target=server.serve_forever)
    server_thread.setDaemon(True)
    server_thread.start()
    server.serve_forever()
