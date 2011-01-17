#!/usr/bin/env python2.6
#-*- coding:utf-8 -*-
from time import time
def logged(when):
    def log(f, *args, **kargs):
        print '''called: 
    function : %s 
    args: %r
    kargs: %r''' %  (f, args, kargs)

    def pre_logged(f):
        def wrapper(*args, **kargs):
            log(f, *args, **kargs)
            return wrapper
    def post_logged(f):
        def wrapper(*args, **kargs):
            now = time ()
            try:
                return f(*args, **kargs)
            finally:
                log(f, *args, **kargs)
                print "time delta: %s" % (time() - now)
        return wrapper
    try:
        return {"pre": pre_logged,
            "post": post_logged}[when]
    except KeyError, e:
        raise ValueError(s), 'moust be "pre" or "post"'

@logged("post")
def hello(name):
    print "Hello,", name

hello("World!")
   
