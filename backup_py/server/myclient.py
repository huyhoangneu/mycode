#!/usr/bin/env python2.6
#-*- coding:utf-8 -*-
import socket, time
class MyClient:   
    def __init__(self):   
        print 'Prepare for connecting...'   
  
    def connect(self):   
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)   
        sock.connect(('localhost', 9999))   
        #sock.sendall('Hi, server')   
        #self.response = sock.recv(8192)   
        #print 'Server:', self.response   
  
        self.s = raw_input("Server: Do you want get the 'thinking in python' file?(y/n):")   
        if self.s == 'y':   
            while True:   
                self.name = raw_input('Server: input our name:')   
                sock.sendall(self.name.strip())   
                self.response = sock.recv(8192)   
                if self.response != None:   
                    print self.response
                    break  
        sock.close()   
        print 'Disconnected'   
if __name__ == '__main__':   
    try:
        client = MyClient()   
        client.connect()   
    except KeyboardInterrupt, e:
        sys.stdout.write('\n')
    sys.exit(0)
#vim:ts=4:sw=4:et
