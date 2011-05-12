#!/usr/bin/evn python
# -*- coding: utf-8 -*-  

import urllib, urllib2, cookielib
import re, os, random, sys
from BeautifulSoup import BeautifulSoup

'''
headers = {
			'Referer' : 'http://www.baidu.com',
			'User-Agent' : 'Mozilla/5.0 (X11; Linux i686; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
		}
#headers.update(obj)		

f = open("c:\sina.cookie", 'r')
c = f.read()
f.close()
print dict(Cookie = c)
sys.exit()
'''

fans_user_tpl = 'http://t.sina.com.cn/attention/att_list.php?action=1&uid=%s&page=%s'

class BaseHandler(object):
    pass

class AuthLoginHandler(object):
	
    def __init__(self, url):
		#opener.addheaders = [('User-agent', 'Mozilla/5.0')]
		self.login_url = url
		self.debug = True
		self.headers = {
			'Referer' : 'http://www.baidu.com',
			'User-Agent' : 'Mozilla/5.0 (X11; Linux i686; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
			'Referer' : 'http://t.sina.com.cn/login.php?url=http%3A%2F%2Ft.sina.com.cn%2Fi55m411'
		}
		self.params = ((("service","miniblog"), ("client","ssologin.js(v1.3.5)"), ("entry","miniblog"), ("encoding","utf-8"), ("gateway","1"), ("savestate","7"), ("from",""), ("useticket","0"), ("username", 'seacoastboy@gmail.com'), ("password", 'zouzhihai'), ("url","http://t.sina.com.cn/ajaxlogin.php?framelogin=1&callback=parent.sinaSSOController.feedBackUrlCallBack"), ("returntype","META")))
		if self.debug:
			httpHandler = urllib2.HTTPHandler(debuglevel = 1)
		else:
			httpHandler = urllib2.HTTPHandler(debuglevel = 0)
		self.cj = cookielib.LWPCookieJar()
		try:  
			self.cj.revert('./weibo.cookie')
		except Exception, e:
			print e
		self.opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(self.cj), httpHandler)
		urllib2.install_opener(self.opener)
    
    def get_headers(self):
        return self.headers
    
    def _response(self, url):
        request=urllib2.Request(url, urllib.urlencode(self.params), headers = self.headers)
        response = self.opener.open(request)
        self.set_cookie('./weibo.cookie')
        #self.cj.save('./weibo.cookie')
        return response
    
    def set_header(self, *args):
        self.headers.update(*args)
    
    def get_cookie(self):
        pass
    
    def set_cookie(self, path):
        self.cj.save(path)
    
    def login(self):
        self._response(self.login_url)
        #
        request = urllib2.Request('http://t.sina.com.cn', headers = self.headers)
        self.response = self.opener.open(request)
        self.contents = self.response.read()
        self.set_cookie('./weibo.cookie')
        #print self.contents
        #print self.response.geturl()
    
    def get_contents(self, url):
        return self._response(url).read()

class FansHandler():
    
    def __init__(self, contents, userid):
        self.contents = contents
        self.userid = userid
        self.soup = BeautifulSoup(contents, fromEncoding="utf-8")
    
    def get_page(self):
        pages = self.soup.findAll('a', attrs={'class' : 'btn_num'})
        page_count = pages[-1].find('em').contents[0]
        return page_count
        #<a href="/attention/att_list.php?action=1&amp;uid=1782823832&amp;page=2" class="btn_num"><em>2</em></a>
    def get_fans_info(self, userid):
        count = self.get_page(contents)
        for num in count:
            fans_url = fans_usr_tmp % (1782823832, num)
            pass
        #[[username: 随意飘得风, uid: 1959449094, home_url: http://t.sina.com.cn/1959449094],[]]
        pass
    def get_user_info(self, url):
        pass


login = AuthLoginHandler('http://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.3.12)')
login.login()
fans_url = fans_usr_tmp % (1782823832, 1)
c = login.get_contents('http://t.sina.com.cn/attention/att_list.php?action=1&uid=1782823832&page=1')
soup = BeautifulSoup(c, fromEncoding="utf-8")
#while True:
#    user_item = soup.findNext('span',attrs={"class" : "name"})
#    if user_item:
#        print user_item
#    else:
#        break
pages = soup.findAll('a', attrs={'class' : 'btn_num'})

page_count = pages[-1].find('em').contents[0]
print page_count
sys.exit()
user_items = soup.findAll('span',attrs={"class" : "name"})
print len(user_items)
sys.exit()
for item in user_items:
    info = item.find('a')
    print "username: %s, uid: %s, home_url: %s" % (info['title'], info['uid'], info['href'])



