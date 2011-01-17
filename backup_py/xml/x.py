#!/bin/env python2.6
#-*- conding: UTF-8 -*-

import feedparser
import sys
url = 'http://news.baidu.com/n?cmd=1&class=civilnews&tn=rss&sub=0'
feed = feedparser.parse(url)
items = feed['items'][0]
print items
sys.exit()
#items = feed['items'][0]
for item in items:
    print item
