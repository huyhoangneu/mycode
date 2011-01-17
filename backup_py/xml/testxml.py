#!/bin/evn python2.6
#!-*- coding:utf-8 -*-
#from xml.etree.cElementTree import ElementTree
try:
    from xml.etree.cElementTree import ElementTree
except:
    from xml.etree.ElementTree import ElementTree
    
import sys
tree = ElementTree()
tree.parse("index.xhtml")
p = tree.find("body/p") 
print p.iter()
sys.exit()
links = list(p.iter("a")) 
sys.exit()
for i in links:
    print i
