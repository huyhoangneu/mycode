#!/usr/bin/python
# -*- coding: UTF-8 -*-
import smtplib,string,sys
from smtplib import SMTPAuthenticationError
from smtplib import SMTP
from email.base64MIME import encode as encode_base64
from email.MIMEText import MIMEText
import socket

timeout_value = 1.0
socket.setdefaulttimeout(timeout_value)
try:
    server=smtplib.SMTP('127.0.0.1')
except socket.error, msg: 
    print msg
