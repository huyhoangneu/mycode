#!/usr/bin/env python2.6
# -*- coding: utf-8 -*-
# vim:ts=4:sw=4
__name__ = 'down_html'
__version__ = '0.1'
__author__ = 'seacoastboy (seacoastboy@gmail.com)'
__copyright__ = "Copyright (c) 2004-2009 Leonard Richardson"
__license__ = "New-style BSD"
__all__ = []

import os, sys, copy, time, logging, hashlib, datetime
import json
import re
import urllib, urllib2
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
            #opener.addheaders= [('User-agent', 'Mozilla/5.0')]
            fp = opener.open(url, timeout = 1)
            #print fp.getresponse()
            headers = fp.info()
            print headers
            #sys.exit()
            if "content-length" in headers and "Status" in headers:
                size = int(headers['content-length'])
                status = int(headers['Status'])
                print "size: %s, status: %s" % (size, status)
            self.html = fp.read()
        except urllib2.HTTPError, e:
            print 'Error code: ', e.code
        except urllib2.URLError, e:
            print 'Reason: ', e.reason
            return
        except:
            return
        return self.html

def profile_time(start = None, pref = None):
    n = datetime.datetime.now()
    print n
    if start != None and pref != None:
        print pref, ':', n - start
        pass
    return n
s = profile_time()
hurl="http://www.qiushibaike.com/"
hr = HtmlReader()
contents = hr.get_url_html(hurl)
print contents
sys.exit()
'''
<div title="2010.06.11 13:40" id="article315431" class="qiushi_body article">
  宿舍楼下面新建了一个篮球场  
  <p style="" class="tags">谁还有心思继续啊混蛋！</p>
</div>
'''
from re import S, sub, compile
import sys, time, os,string
'''
get url contents
'''
def get_url_content(url):
    content = hr.get_url_html(url)
    results = complie('<div\s+class=\"qiushi_body\s+[^>]*>(.*?)</div>', S).findall(content)

print get_url_content('http://www.qiushibaike.com/articles/315520.htm')
sys.exit()
pageurl = 'http://www.qiushibaike.com/articles/%s.htm'
urllist = []
#regex = ur'&x='
#p = re.compile(ur'&x=', re.I|re.S)
results = compile('<div\s+class="datetime">(.*?)</div>', S).findall(contents)
#results = compile('<div\s+class="qiushi_body\s+article"\s+id="article\d+"[^>]*>(.*?)</div>', S).findall(contents)
for item in results:
    id = compile('<a[^>]*>#(\d+)</a>', S).findall(item)
    urllist.append( pageurl % id[0])
print urllist
e = profile_time(s, 'whole')
