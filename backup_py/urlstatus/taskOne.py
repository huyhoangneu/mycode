#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, time, os,string
import re 
import httplib 
import urllib
from urlparse import urlparse
'''
dict : ["%s=%s" % (k, v) for (k,v) in myparams.items()]
#list,dict,tuple（数组，字典，元组）
'''
'''
myfile = open("url.txt")
for line in myfile.readlines():
    title, url = line.split()
    url ='http://www.fanrry.cn/?hmsr=1616&hmmd=neiye-gouwu&hmpl=neiye&hmkw=neiye&hmci='
    o = urlparse(url, "http")
    print o

    #url = 'http://www.google.cn/?zhonguo=http://www.t.com.cn'
    #sub = sub('http://','',url);
    print title, url
    sys.exit()
'''
def getStatus(url, body):
    try:
        params = urllib.urlencode({})
        user_agent = 'Mozilla/5.0(Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/20090729 Firefox/3.5.2'
        headers = {
            "Accept":"text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
            "Accept-Language": "en-us,en;q=0.5",
            "Accept-Charset": "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
            "Connection": "close",
            "User-Agent":user_agent,
        }
        conn = httplib.HTTPConnection(url, timeout=1)
        #print httplib.HTTPS_PORT
        conn.request("GET", body, params, headers)
        respons = conn.getresponse()
    except Exception, e:
        #print e.__class__, e, url
        #sys.exit()
        return {"status": '404', "url": "%s||%s" % (url,e)}
    #if   'ss '   in   locals().keys():
    # list -> dictionary
    #print dict(respons.getheaders()).keys()
    #print respons.status, respons.reason
    return {"status": respons.status, "url": "http://%s%s" % (url, body)}

if __name__ == "__main__":
#info {502, 404}
    regex = re.compile(
        r'^https?://' # http:// or https://
        r'(?:(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+[A-Z]{2,6}\.?|' #domain...
        r'localhost|' #localhost...
        r'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})' # ...or ip
        r'(?::\d+)?' # optional port
        r'(?:/?|[/?]\S+)$', re.IGNORECASE)
    p = re.compile(regex)
    myfile = open("url.txt")
    for line in myfile.readlines():
        title = url = ''
        title, url = line.split()[0:2]
        '''
        try:
            #line = u'adfdsf http://tech.163.com'
            title =''
            url = ''
            title, url = line.split()
            if not p.match(url):
                break
        except ValueError, e:
            print  line, url
            break
        '''
        if p.match(url):
            o = urlparse(url, "http")
            #o.netloc, o.path, o.query
            url = o.netloc
            if o.query:
                body = "%s?%s" % (o.path, o.query) 
            else:
                body = "%s" % (o.path)
            info = getStatus(url, body)
            status = info['status']
            #str -> list
            fist = list(str(status))[0]
            #取得 状态的 第一个字母
            if fist in ['5', '4']:
                print "name: %s, %s" % (title, getStatus(url, body))
            #else:
            #    print "l-name: %s, %s" % (title, getStatus(url, body))
        else:
            print "error line: %s, url: %s" % (line, url)
#vim:ts=4:sw=4:et
