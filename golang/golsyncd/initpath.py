#!/usr/bin/python

import os

path = raw_input("please input path:\n")

if os.path.isdir(path):
	os.chdir(path)
	for parent_dir in range(256):
		if parent_dir<=0xf:
			dir1 =  "0%x" % parent_dir 
		else:
			dir1 = "%x" % parent_dir 
		print "creating dir:", dir1 
		os.mkdir(dir1)
