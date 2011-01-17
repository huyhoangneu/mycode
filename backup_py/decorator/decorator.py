#!/bin/evn python2.6
#-*- coding: utf-8 -*-
#这是一个装饰器函数
from time import ctime, sleep

def DecoratorFunc(func):
    #Function就是对传入的func函数的包装，以便加入更多的功能
    def Function(self):
        print '[%s] %s () called' % ( ctime(), func.__name__)
        print 'hello world' #简单的做一些额外操作，可以是其它操作
        return func(self)
    return Function

@DecoratorFunc
def run():
    print 'my run function'

class c:
    @DecoratorFunc
    def get(self):
        print 'my class function'

'''
闭包 计数器
'''
def counter(start_at = 0):
    count = [start_at]
    def incr():
        count[0] += 1
        return count[0]
    return incr

if __name__ == '__main__':
    count = counter(5)
    print count()
    print count()
    C = c()
    C.get()
#    run()
