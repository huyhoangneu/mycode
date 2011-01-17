#!/usr/bin/env python
# -*- coding: utf-8 -*-
import logging,time,os
class xLogging:
    @staticmethod
    def debug(logfile, log):
        strTime = time.strftime("%Y%m%d", time.gmtime())
        logfiles = "%s.%s" % (logfile, strTime)
        dirname = os.path.dirname(logfiles)
        if os.path.exists(dirname) == False:
            os.mkdir(dirname)
        logging.basicConfig(filename=logfiles,level=logging.DEBUG,)
        logging.debug( log )
    
    @staticmethod
    def t():
        print '?'
    
    @staticmethod
    def handler(mes, logFile=None , type='debug', tag='xlib',logPage='/tmp',types='log'):
        if logFile == None:
            logFile = '%s/xlib.%s.%s' % (logPage,types,time.strftime("%Y%m%d", time.gmtime()))
        logger = logging.getLogger( tag )
        logger.setLevel(logging.DEBUG)
        # create file handler which logs even debug messages
        fh = logging.FileHandler( logFile )
        fh.setLevel(logging.DEBUG)
        # create console handler with a higher log level
        ch = logging.StreamHandler()
        ch.setLevel(logging.ERROR)
        # create formatter and add it to the handlers
        formatter = logging.Formatter("%(asctime)s - %(name)s - %(levelname)s - %(message)s")
        ch.setFormatter(formatter)
        fh.setFormatter(formatter)
        # add the handlers to logger
        logger.addHandler(ch)
        logger.addHandler(fh)
        if type == 'debug':
            logger.debug( mes )
        elif type == 'info':
            logger.info( mes )
        elif type == 'warn':
            logger.warn( mes )
        elif type == 'error':
            logger.error( mes )
        else:
            logger.critical( mes )