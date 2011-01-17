#!/usr/bin/env python2.6
# -*- coding: utf-8 -*-
# vim:ts=4:sw=4
#__name__ = 'down_html'
__version__ = '0.1'
__author__ = 'seacoastboy (seacoastboy@gmail.com)'
__copyright__ = "Copyright (c) 2004-2009 Leonard Richardson"
__license__ = "New-style BSD"
__all__ = []

import os, sys, datetime
#import json
import urllib2, re

class HtmlReader:
    def __init__(self):
        self.html = None
    def get_url_html(self, url, need_pre_deal=True):
        try:
            opener = urllib2.build_opener()
            user_agent = 'Mozilla/5.0(Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/20090729 Firefox/3.5.2'
            opener.addheaders = [ 
                #"Host":url,
                ("Accept", "text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"),
                ("Accept-Language", "zh-cn,zh;q=0.5"),
                ("Accept-Charset", "GB2312,utf-8;q=0.7,*;q=0.7"),
                ("Connection", "close"),
                ("User-Agent", user_agent)
            ]
            fp = opener.open(url, timeout = 1)
            headers = fp.info()
            #print headers
            if "content-length" in headers and "Status" in headers:
                size = int(headers['content-length'])
                status = int(headers['Status'])
                #print "size: %s, status: %s" % (size, status)
            self.html = fp.read()
        except urllib2.HTTPError, e:
            print 'Error code: ', e.code
        except urllib2.URLError, e:
            print 'Reason: ', e.reason
            return
        except:
            return
        return self.html

if __name__ == '__main__':
    hurl="http://www.weather.com.cn/textFC/hb.shtml"
    hr = HtmlReader()
    contents = hr.get_url_html(hurl)
    contents = '''<h>dfdf</h><b>bbbbbbb</b>'''
    reg = re.compile('<(?P<tagname>\w*)>.*</(?P=tagname)>')
    m = reg.match(contents) 
    #m = re.match('<(?P<div>\w*)>.*</(?P=div)>', contents) 
    #m = re.match('<div\s+[^>]*>(.*?)</div>', contents)
    #pattern = "<head>(.*)<\/head>"
    #m = re.match(pattern, contents)
    print m
    if m:
        print m.group()
    #print contents
