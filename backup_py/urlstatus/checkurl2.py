#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, time, os,string
import re 
import httplib 
import urllib, urllib2
from urlparse import urlparse
#import urlparse
import socket

'''
dict : ["%s=%s" % (k, v) for (k,v) in myparams.items()]
#list,dict,tuple（数组，字典，元组）
'''
def getStatus(url, body):
    #httplib.HTTPConnection.debuglevel = 1
    #cookies = urllib2.HTTPCookieProcessor()
    #opener = urllib2.build_opener(cookies)
    try:
        params = urllib.urlencode({})
        user_agent = 'Mozilla/5.0(Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/20090729 Firefox/3.5.2'
        headers = {
            "Host":url,
            "Accept":"text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
            "Accept-Language": "zh-cn,zh;q=0.5",
            "Accept-Charset": "GB2312,utf-8;q=0.7,*;q=0.7",
            "Accept-Encoding":"gzip,deflate",
            "Keep-Alive":"",
            "Cache-Control":"no-cache",
            "Connection": "close",
            "User-Agent":user_agent,
            "cookie":""
        }
        #print url, boy
        conn = httplib.HTTPConnection(url, strict=True, timeout=20)
        #conn.set_debuglevel(1)
        #conn.request("HEAD", body, params, headers)
        conn.request("GET", body, params, headers)
        #conn.set_debuglevel(1)
        respons = conn.getresponse()
        
        
        #d = response.read()
        #print d
        #print respons.getheaders()
        #print respons.reason
        #print conn.getreply()
        #status, reason, headers = resopns.getreply()
        #nil, netloc, nil, nil, nil = urlsplit(url)
        #print respons.status
        conn.close()
        falg = list(str(respons.status))[0]
        if falg in ['4', '5']:
            try:
                conn = httplib.HTTPConnection(url, timeout=20)
                conn.request("GET", body, params, headers)
                respons = conn.getresponse()
                conn.close()
            except socket.error, msg:
                return {"status": '404', "url": "%s||%s" % (url,msg)}
    except socket.error, msg:
        '''
        errno, errstr = sys.exc_info()[:2] 
            if errno == socket.timeout: 
        '''
        #print msg
        conn.close()
        conn = None
        respons = None
        try:
            conn = httplib.HTTPConnection(url, timeout=5)
            conn.request("GET", body, params, headers)
            respons = conn.getresponse()
            conn.close()
        except socket.error, msg:
            print msg
            return {"status": '404', "url": "%s||%s" % (url,msg)}
    except Exception, e:
        print e.__class__, e, url
        conn.close()
        conn = None
        #sys.exit()
        return {"status": '404', "url": "%s||%s" % (url,e)}
    #if   'ss '   in   locals().keys():
    # list -> dictionary
    #print dict(respons.getheaders()).keys()
    #print respons.status, respons.reason
    return {"status": respons.status, "url": "http://%s%s" % (url, body)}

regex = re.compile(
        r'^https?://' # http:// or https://
        r'(?:(?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+[A-Z]{2,6}\.?|' #domain...
        r'localhost|' #localhost...
        r'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})' # ...or ip
        r'(?::\d+)?' # optional port
        r'(?:/?|[/?]\S+)$', re.IGNORECASE)
p = re.compile(regex)

#from urlparse import urlparse
def checkurl(url):
    #tmp = urlparse.urlsplit(url)
    #print tmp
    if p.match(url):
        o = urlparse(url, "http")
        url = o.netloc
        if o.query:
            body = '%s?%s' % (o.path, o.query)
        elif o.path:
            body ='%s' % (o.path)
        else:
            body = '/'
        info = getStatus(url, body)
        #print info
        status = info['status']
        #print status
        fist = list(str(status))[0]
        if fist in ['4', '5']:
            return False
        else:
            return True
if __name__ == "__main__":
#info {502, 404}
#<class 'socket.timeout'> timed out pinyin.sogou.com
    #print checkurl('http://www.xmldbzj.gov.cn')
    #sys.exit()
    myfile = open("/home/zouzhihai/backup_py/urlstatus/log/error_url.txt")
    error_url2='/home/zouzhihai/backup_py/urlstatus/log/error_url2.txt'
    if os.path.isfile( error_url2 ):
        os.remove( error_url2 )
    file = open(error_url2, 'w+')
    for line in myfile.readlines():
        title = url = ''
        title, url = line.split()[0:2]
        #url = 'http://www.yoger.com.cn/main.html'
        #url = 'http://p.yiqifa.com/c?s=fefa84f4&w=88243&c=4264&i=4562&l=0&e=0&t=http://home.3gm.com.cn/do.php?ac=mcsd'
        if checkurl(url) == False:#True:
            file.write( title + ' ' + url + '\n')
            print "name: %s, %s" % (title, url)
        '''
        sys.exit()
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
        '''
    file.close()
#vim:ts=4:sw=4:et
