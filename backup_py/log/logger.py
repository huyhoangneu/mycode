#!/bin/env python2.6
#-*- coding: UTF-8 -*-

import logging, logging.config
#from logging import logging
def initlog(logfile='/var/log/tat_worker.log', level=logging.NOTSET):
    ''' 初始化日志，返回一个日志对象 '''
    logger = logging.getLogger()
    handler = logging.FileHandler(logfile)
    formatter = logging.Formatter('%(asctime)s %(levelname)s %(message)s')
    handler.setFormatter(formatter)
    logger.addHandler(handler)
    logger.setLevel(level)
    return logger

logger = initlog()
'''
logging.getLogger()
创建一个日志对象
logging.FileHandler(logfile)
创建一个日志处理器，即日志会怎样存放
logging.Formatter()
日志格式化
setFormatter()
将一个格式化信息应用到刚才创建的日志处理器上
addHandler()
将一个日志处理器添加到最开始创建的日志对象上
setLevel()
设置日志级别

logger.debug('DEBUG 级别的信息')
logger.error('ERROR 级别的信息')
logger.info('INFO 级别的信息')
'''
# vim:ts=4:sw=4:et
