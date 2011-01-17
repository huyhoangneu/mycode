#!/usr/bin/python
# -*- coding: utf-8 -*-

__author__ = "seacoastboy <seacoastboy@gmail.com>"
__date__ = "2010/1/6"
__version__ = "0.1"
__license__ = ""

'''
日志 记录
http://blog.csdn.net/JGood/archive/2009/07/11/4340740.aspx
'''
LOG_PATH = '/data/grab/rsyncscript/'

def initlog(logfile = 'sendlog'):
    import logging
    # 生成一个日志对象
    logger = logging.getLogger()
    # 生成一个Handler。logging支持许多Handler，
    # 象FileHandler, SocketHandler, SMTPHandler等，我由于要写
    # 文件就使用了FileHandler。
    # logfile是一个全局变量，它就是一个文件名，如：'crawl.log'
    logfilename = LOG_PATH + 'log/' + logfile+'.log'
    hdlr = logging.FileHandler(logfilename)
    # 成一个格式器，用于规范日志的输出格式。如果没有这行代码，那么缺省的
    # 格式就是："%(message)s"。也就是写日志时，信息是什么日志中就是什么，
    # 没有日期，没有信息级别等信息。logging支持许多种替换值，详细请看
    # Formatter的文档说明。这里有三项：时间，信息级别，日志信息
    formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')
    # 将格式器设置到处理器上
    hdlr.setFormatter(formatter)
    # 将处理器加到日志对象上
    logger.addHandler(hdlr)
    # 设置日志信息输出的级别。logging提供多种级别的日志信息，如：NOTSET, 
    # DEBUG, INFO, WARNING, ERROR, CRITICAL等。每个级别都对应一个数值。
    # 如果不执行此句，缺省为30(WARNING)。可以执行：logging.getLevelName
    # (logger.getEffectiveLevel())来查看缺省的日志级别。日志对象对于不同
    # 的级别信息提供不同的函数进行输出，如：info(), error(), debug()等。当
    # 写入日志时，小于指定级别的信息将被忽略。因此为了输出想要的日志级别一定
    # 要设置好此参数。这里我设为NOTSET（值为0），也就是想输出所有信息
    logger.setLevel(logging.NOTSET)
    return logger
#logging=initlog()
#logging.setLevel(30)
#logging.info('注册')
#logging.error('注册')
