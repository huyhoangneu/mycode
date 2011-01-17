#!/usr/bin/env python2.6
#-*- coding:utf-8 -*-
import cgitb
import cgi
cgitb.enable()

form = cgi.FieldStorage()
if "name" not in form or "addr" not in form:
    print "<H1>Error</H1>"
    print "Please fill in the name and addr fields."

#print "<p>name:", form["name"].value
#print "<p>addr:", form["addr"].value
'''further form processing here'''
