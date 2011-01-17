#!/usr/bin/env python 


import sys
import os
import socket
import select
import string

import msnlib
import msncb


"""
MSN Client Daemon

This is a MSN client that reads commands from a named pipe, using
a little text-only protocol. It's main use is to serve as a 'glue'
to implement clients in other languages.

This is yet experimental because lack of testing, please let me know if you
try it out.
"""


def null(s):
	"Null function, useful to void debug ones"
	pass
		

#
# This are the callback replacements, which only handle the output and then
# call the original callbacks to do the lower level stuff
#

# basic classes
m = msnlib.msnd()
m.cb = msncb.cb()

# status change
def cb_iln(md, type, tid, params):
	t = params.split()
	status = msnlib.reverse_status[t[0]]
	email = t[1]
	equeue.append('STCH %s %s\n' % (email, status))
	msncb.cb_iln(md, type, tid, params)
m.cb.iln = cb_iln

def cb_nln(md, type, tid, params):
	status = msnlib.reverse_status[tid]
	t = string.split(params)
	email = t[0]
	equeue.append('STCH %s %s\n' % (email, status))
	msncb.cb_nln(md, type, tid, params)
m.cb.nln = cb_nln

def cb_fln(md, type, tid, params):
	email = tid
	u = m.users[email]
	discarded = 0
	if u.sbd and u.sbd.msgqueue:
		discarded = len(u.sbd.msgqueue)
	equeue.append('STCH %s offline %d\n' % (email, discarded))
	msncb.cb_fln(md, type, tid, params)
m.cb.fln = cb_fln

# server disconnect
def cb_out(md, type, tid, params):
	equeue.append('ERR SERV_DISC Server sent disconnect\n')
	msncb.cb_out(md, type, tid, params)
m.cb.out = cb_out


# message
def cb_msg(md, type, tid, params, sbd):
	t = string.split(tid)
	email = t[0]
	
	# messages from hotmail are only when we connect, and send things
	# regarding, aparently, hotmail issues. we ignore them (basically
	# because i couldn't care less; however if somebody has intrest in
	# these and provides some debug output i'll be happy to implement
	# parsing).
	if email == 'Hotmail':
		return

	# parse
	lines = string.split(params, '\n')
	headers = {}
	eoh = 1
	for i in lines:
		# end of headers
		if i == '\r':
			break
		tv = string.split(i, ':')
		type = tv[0]
		value = string.join(tv[1:], ':')
		value = string.strip(value)
		headers[type] = value
		eoh += 1
	
	if headers.has_key('Content-Type') and headers['Content-Type'] == 'text/x-msmsgscontrol':
		# the typing notices
		equeue.append('TYPING %s\n' % email)
	else:
		# messages
		equeue.append('MSG %d %d %s\n%s\n' % \
			(len(lines), eoh, email, string.join(lines, '\n')) )
	
	msncb.cb_msg(md, type, tid, params, sbd)
m.cb.msg = cb_msg


# join a conversation and send pending messages
def cb_joi(md, type, tid, params, sbd):
	email = tid
	if len(sbd.msgqueue) > 0:
		equeue.append('MFLUSH %s\n' % email)
	msncb.cb_joi(md, type, tid, params, sbd)
m.cb.joi = cb_joi

# server errors
def cb_err(md, errno, params):
	if not msncb.error_table.has_key(errno):
		desc = 'Unknown'
	else:
		desc = msncb.error_table[errno]
	equeue.append('ERR %s %s\n' % (errno, desc))
	msncb.cb_err(md, errno, params)
m.cb.err = cb_err
	
# users add, delete and modify
def cb_add(md, type, tid, params):
	t = params.split()
	type = t[0]
	if type == 'RL' or type == 'FL':
		email = t[2]
	if type == 'RL':
		equeue.append('UADD %s\n' % email)
	elif type == 'FL':
		equeue.append('ADDFL %s\n' % email)
	msncb.cb_add(md, type, tid, params)
m.cb.add = cb_add

def cb_rem(md, type, tid, params):
	t = params.split()
	type = t[0]
	if type == 'RL' or type == 'FL':
		email = t[2]
	if type == 'RL':
		equeue.append('UDEL %s\n' % email)
	elif type == 'FL':
		equeue.append('DELFL %s\n' % email)
	msncb.cb_rem(md, type, tid, params)
m.cb.rem = cb_rem


def login(email, password):
	# login to msn
	printl('Logging in... ', c.green, 1)
	try:
		m.login()
		printl('done\n', c.green, 1)
	except 'AuthError', info:
		errno = int(info[0])
		if not msncb.error_table.has_key(errno):
			desc = 'Unknown'
		else:
			desc = msncb.error_table[errno]
		perror('Error: %s\n' % desc)
		quit(1)
	except KeyboardInterrupt:
		quit()
	except ('SocketError', socket.error), info:
		perror('Network error: ' + str(info) + '\n')
		quit(1)
	except:
		pexc('Exception logging in\n')
		quit(1)


#
# the pipe read
#

# first, a small send wrapper to avoid repeating 'addr' all over the place
# note that as they are implemented using udp, if more than one client
# connects it can get quite quite messy
addr = ()
def psend(pipe, s):
	print '-->', s,
	return pipe.sendto(s, addr)

# read from the pipe, c being the pipe socket passed from the caller
def pipe_read(c):
	global m
	global addr
	global equeue	
	
	# we don't worry about lines too much in this implementation because
	# we use datagrams. however, when using stream sockets you should
	s, addr = c.recvfrom(4 * 1024)	# input buffer, should be enough
	print '<--', s,
	try:
		s = s.split(' ', 1)
		if len(s) == 2:
			cmd, params = s
		else:
			cmd = s[0]
			params = ''

		cmd = cmd.strip()
		if params:
			params = params.strip()
			params = params.split(' ')
	except:
		psend(c, 'ERR EINVAL\n')
		return
	
	if cmd == 'LOGIN':
		if len(params) != 2:
			psend(c, 'ERR PARAMS\n')
			return
		try:
			email, pwd = params
			m.email = email
			m.pwd = pwd
			m.login()
			m.sync()
		except 'AuthError', info:
			errno = int(info[0])
			if not msncb.error_table.has_key(errno):
				desc = 'Unknown'
			else:
				desc = msncb.error_table[errno]
			psend(c, 'ERR MSN %d %s\n' % (errno, desc))
			return
		except ('SocketError', socket.error), info:
			psend(c, 'ERR SOCK %s\n' % str(info))
			return
		psend(c, 'OK\n')
		return
		
	elif cmd == 'LOGOFF':
		m.disconnect()
		psend(c, 'OK\n')
		return
		
	# if we are not connected, the following commands are not available
	if not m.fd:
		psend(c, 'ERR ENOTCONN\n')
		return
	
	
	if cmd == 'STATUS':
		status = string.join(params, ' ')
		if not m.change_status(status):
			psend(c, 'ERR UNK STATUS\n')
		else:
			psend(c, 'OK\n')
		return
		
	if cmd == 'POLL':
		equeue.append('POLLEND\n')
		for evt in equeue:
			psend(c, evt)
		equeue = []
		return
	
	if cmd == 'GETCL':
		psend(c, 'CL %d\n' % len(m.users.keys()) )
		for email in m.users.keys():
			u = m.users[email]
			status = msnlib.reverse_status[u.status]
			psend(c, '%s %s %s\n' % (status, email, u.nick))
		return
	
	if cmd == 'GETRCL':
		psend(c, 'CL %d\n' % len(m.reverse.keys()) )
		for email in m.reverse.keys():
			u = m.reverse[email]
			status = msnlib.reverse_status[u.status]
			psend(c, '%s %s %s\n' % (status, email, u.nick))
		return
	
	if cmd == 'INFO':
		if len(params) != 1:
			psend(c, 'ERR PARAMS\n')
			return
		if not m.users.has_key(email):
			psend(c, 'ERR UNK USER\n')
		u = m.users[email]
		psend(c, 'email = %s\n' % email)
		psend(c, 'nick = %s\n' % u.nick)
		psend(c, 'homep = %s\n' % u.homep)
		psend(c, 'workp = %s\n' % u.workp)
		psend(c, 'mobilep = %s\n' % u.mobilep)
		psend(c, '\n')
		return
	
	if cmd == 'ADD':
		if len(params) != 2:
			psend(c, 'ERR PARAMS\n')
			return
		nick, email = params
		m.useradd(email, nick)
		psend(c, 'OK\n')
		return
	
	if cmd == 'DEL':
		if len(params) != 1:
			psend(c, 'ERR PARAMS\n')
			return
		m.userdel(params)
		psend(c, 'OK\n')
		return
	
	if cmd == 'NICK':
		if len(params) != 1:
			psend(c, 'ERR PARAMS\n')
			return
		m.change_nick(params)
		psend(c, 'OK\n')
		return
	
	if cmd == 'PRIV':
		if len(params) != 2:
			psend(c, 'ERR PARAMS\n')
			return
		try:
			public = int(p[0])
			auth = int(p[1])
			if public not in (0, 1) or auth not in (0, 1):
				raise
		except:
			psend(c, 'ERR EINVAL\n')
			return
		m.privacy(public, auth)
		psend(c, 'OK\n')
		return
	
	if cmd == 'SENDMSG':
		params = string.join(params, ' ')
		params = string.split(params, '\n', 2)
		params, msg = params
		params = string.split(params, ' ')
		if len(params) < 2:
			psend(c, 'ERR PARAMS\n')
			return
		lines = params[0]
		email = params[1]
		msg = msg
		m.sendmsg(email, msg)
		psend(c, 'OK\n')
		return
		
	# if we got here is because the command is unknown		
	psend(c, 'ERR UNK\n')
	return
	
		

#
# now the real thing
#

# void the debug
msnlib.debug = null
msncb.debug = null

# POLL event queue
# We implement it in a very, very efficient way: text =)
# Yes, it's actually a list, but just because .append() is readable
# and allow us to keep track of the number of pending events
equeue = []

# open the socket for local communication
# we use datagram sockets to avoid complex reads and writes for now, but the
# protocol is line-oriented and perfectly capable of working over a stream
# socket.
pipe = socket.socket(socket.AF_INET, socket.SOCK_DGRAM, 0)
pipe.bind(('127.0.0.1', 3030))


# loop, waiting for connections
while 1:
	infd = outfd = []
	# if we are connected, poll from msn
	if m.fd != None:
		t = m.pollable()
		infd = t[0]
		outfd = t[1]
	infd.append(pipe)

	fds = select.select(infd, outfd, [], 0)
	
	for i in fds[0] + fds[1]:	# see msnlib.msnd.pollable.__doc__
		if i == pipe:
			# read from the pipe
			pipe_read(pipe)
		else:
			try:
				m.read(i)
			except ('SocketError', socket.error), err:
				if i != m:
					# user closed a connection
					# note that messages can be
					# lost here
					equeue.append('SCLOSE USER %s %d\n' % (i.emails[0], len(i.msgqueue)) )
					m.close(i)
				else:
					# main socket closed
					# report
					equeue.append('SCLOSE MAIN\n')
					quit(1)


