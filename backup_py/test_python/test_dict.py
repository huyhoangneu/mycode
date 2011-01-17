#!/usr/bin/env python2.6

import sys
dict = {}
file_name = '/usr/share/dict/american-english'
file = open(file_name, 'r')
wordList = file.readlines()
for word in wordList:
    if word.strip():
        letter = word[0].lower()
        if(96 < ord(letter) < 123 ):
            if(dict.has_key(letter)):
                dict[letter] += 1
            else:
                dict[letter] = 1
    print dict.has_key()
    #sys.exit()
