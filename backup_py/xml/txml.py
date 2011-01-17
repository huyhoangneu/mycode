#!/usr/bin/python2.6
# -*- coding: utf-8 -*-
from BeautifulSoup import BeautifulSoup          # For processing HTML
from BeautifulSoup import BeautifulStoneSoup     # For processing XML
import BeautifulSoup                             # To get everything

import urllib2
#, string
import sys
'''
http://news.baidu.com/n?cmd=1&class=civilnews&tn=rss&sub=0
[child['value'] for child in soup.find('parent', attrs={'name': name}).findAll('child')]
'''

rss_config = {
            'sina':{'conding':'gbk', 'url':{'国内':'http://www.sina.com.cn/rss/1', '娱乐':'http://www.sina.com.cn/rss/2'}},
            '163': {},
            '263': {}
}
url = 'http://news.baidu.com/n?cmd=1&class=civilnews&tn=rss&sub=0'
xml = urllib2.urlopen(url).read().decode("gbk")
#print xml.encode('utf8')
#sys.exit()
soup = BeautifulStoneSoup(xml, fromEncoding='utf-8')
#print soup.item
#sys.exit()
print soup
news = []
#bookList.append(book)
items = soup.findAll('item')
sys.exit()
can_contain_dangerous_markup = ['content', 'title', 'summary', 'info', 'tagline', 'subtitle', 'copyright', 'rights', 'descr iption']
for item in items:
    '''
    print item.findAll(text=True)[1].encode('utf8')
    for c in item.findAll(text=True):
        print c#.encode('utf8')
    sys.exit()
    ''' 
    i = {}
    i['title'] = item.find('title').contents[0].string.strip()#.encode('utf8')#.string().strip()
    i['pubdate'] = item.find('pubdate').contents[0].string.strip()
    i['link'] = item.find('link').contents[0].string.strip()
    i['description'] = item.find('description').contents[0].string.strip()
    i['source'] = item.find('source').contents[0].string.strip()
    i['author'] = item.find('author').contents[0].string.strip()
    #i['author3'] = item.find('author3').contents[0].string.strip()
    news.append(i)

for new in news:
    print 'tetle: %s\npubdate: %s\nlink: %s\ndescroption: %s\n' % (new['title'], new['pubdate'], new['link'], new['description'])

    #print "title: %s, pubDate: %s" % (title, pubdate) 
if __name__ == "__main__":
    print 'test'
