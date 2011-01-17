#!/bin/env python2.6
#-*- coding:utf-8 -*-
from __future__ import generators
from sendmail import sendmail
import os,sys
import MySQLdb
import MySQLdb.cursors
import checkurl
import datetime
#mail_list=['seacoastboy@gmail.com']
#smDemo=sendmail("211.100.36.228","mailer","1616@myhome")
#sendmsg= smDemo.get_smtp_client()

#smDemo.send_mail(s, "136030112@qq.com","seacoastboy@gmail.com","明天过节了","就是 明天真的就过节了！")
#sys.exit()

mysql_user=''
mysql_pwd=''
mysql_host =''
mysql_charset = 'utf8'
mysql_db = 'cms'
sql = {}
show_1616 = {}
site={}
#错误 列表
error={}
errormsg=[]
#site['1616'] =[]
#site['7999'] = []
#site['wo116']=[]
def profile_time(start = None, pref = None):
    n = datetime.datetime.now()
    print n
    if start != None and pref != None:
        print pref, ':', n - start
        pass
    return n
try:
    conn = MySQLdb.connect(host = mysql_host, user=mysql_user, passwd=mysql_pwd, db= mysql_db, charset = mysql_charset,
    cursorclass =MySQLdb.cursors.DictCursor)
    '''
    MySQLdb默认查询结果都是返回tupl
    cursorclass = MySQLdb.cursors.DictCursor
    cursor=conn.cursor(cursorclass = MySQLdb.cursors.DictCursor)
    '''
    cursor=conn.cursor()
    #名站
    sql['famous'] = "SELECT * FROM  `cms_index_famoussite`"
    cursor.execute(sql['famous'])
    famous=cursor.fetchall()
    show_1616['名站'] = []
    for famou in famous:
        tmp_show_1616 = {}
        tmp_show_1616['Text'] = famou['Text']
        tmp_show_1616['Link'] = famou['Link']
        tmp_show_1616['show'] = {'1616': famou['Show_1616'],'7999': famou['Show_7999'],'wo116': famou['Show_wo116']}
        show_1616['名站'].append(tmp_show_1616)

    #酷站
    sql['cool_catalog'] = "SELECT * FROM  `cms_index_cool_catalog`"
    sql['cool_site'] = "SELECT `Text`, `Link` FROM  `cms_index_cool_site` WHERE `Catalog` = %s AND %s = 1"
    cursor.execute(sql['cool_catalog'])
    cool_catalog=cursor.fetchall()
    show_1616['酷站'] = []
    for row in cool_catalog:
        tmp_show_1616 = {}
        #print sql['cool_site'] % (row['cid'], "`Show_1616`")
        cursor.execute(sql['cool_site'] % (row['cid'], "`Show_1616`"))
        cool_catalog_site=cursor.fetchall()
        tmp_show_1616['name'] = row['cname']
        tmp_show_1616['site'] = list(cool_catalog_site)
        tmp_show_1616['show'] = {'1616': row['Show_1616'],'7999': row['Show_7999'],'wo116': row['Show_wo116']}
        show_1616['酷站'].append(tmp_show_1616)
            #print tmp_show_1616
            #print "cid: %s , cname: %s" % (row['cid'], row['cname'])
            #sys.exit()
    #新站推荐 and 底部酷站推荐
    sql['new_site'] = "SELECT * FROM  `cms_index_newsite` "
    cursor.execute(sql['new_site'])
    new_site = cursor.fetchall()
    show_1616['新站'] = []
    for row in new_site:
        tmp_show_1616 = {}
        tmp_show_1616['Text'] = row['Text']
        tmp_show_1616['Link'] = row['Link']
        tmp_show_1616['show'] = {'1616': row['Show_1616'],'7999': row['Show_7999'],'wo116': row['Show_wo116']}
        show_1616['新站'].append(tmp_show_1616)
    #游戏专区
    sql['game_site'] = "SELECT * FROM  `cms_index_game`"
    cursor.execute(sql['game_site'])
    game_site = cursor.fetchall()
    show_1616['游戏'] = []
    for row in game_site:
        tmp_show_1616 = {}
        tmp_show_1616['Text'] = row['Text']
        tmp_show_1616['Link'] = row['Link']
        tmp_show_1616['show'] = {'1616': row['Show_1616'],'7999': row['Show_7999'],'wo116': row['Show_wo116']}
        show_1616['游戏'].append(tmp_show_1616)
    #软件
    sql['soft_site'] = "SELECT * FROM  `cms_index_soft` "
    cursor.execute(sql['soft_site'])
    soft_site = cursor.fetchall()
    show_1616['软件'] = []
    for row in soft_site:
        tmp_show_1616 = {}
        tmp_show_1616['Text'] = row['Text']
        tmp_show_1616['Link'] = row['Link']
        tmp_show_1616['show'] = {'1616': row['Show_1616'],'7999': row['Show_7999'],'wo116': row['Show_wo116']}
        show_1616['软件'].append(tmp_show_1616)

    #使用工具
    sql['chaxun_site'] = "SELECT * FROM  `cms_index_chaxun`"
    cursor.execute(sql['chaxun_site'])
    chaxun_site = cursor.fetchall()
    show_1616['工具'] = []
    for row in chaxun_site:
        tmp_show_1616 = {}
        tmp_show_1616['Text'] = row['Text']
        tmp_show_1616['Link'] = row['Link']
        tmp_show_1616['show'] = {'1616': row['Show_1616'],'7999': row['Show_7999'],'wo116': row['Show_wo116']}
        show_1616['工具'].append(tmp_show_1616)
    #内页
    sql['in_site'] = "SELECT * FROM  `sites` WHERE `state` = 1"
    cursor.execute(sql['in_site'])
    in_site = cursor.fetchall()
    show_1616['内页'] = []
    for row in in_site:
        tmp_show_1616 = {}
        tmp_show_1616['Text'] = row['name']
        tmp_show_1616['Link'] = row['url']
        show_1616['内页'].append(tmp_show_1616)
    cursor.close()
    conn.close()
    for k, v in show_1616.iteritems():
        if k == '游戏':
            error['游戏'] = []
            #s = profile_time()
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n" % ('游戏开始'))
            for one in v:
                sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( k, one['Text'].encode('utf8'),one['Link'].encode('utf8')))
                #print u"检查 分类 '%s' 中的 '%s' 网址 '%s'" % ( k.decode('utf-8'), one['Text'], one['Link'])
                #sys.stdout.write(u"检查 分类 '%s' 中的 '%s' 网址 '%s'" % ( k.decode('utf-8'), one['Text'], one['Link']))
                #print url.conn(one['Link'])
                if checkurl.checkurl(one['Link']) == True:
                    '''
                    error:'ascii' codec can't decode byte 0xe6 in position 9: ordinal not in range(128)
                    http://www.cnblogs.com/fengmk2/archive/2008/08/01/1257771.html
                    分清encode和decode。str --> decode(c) --> unicode, unicode --> encode(c) -->
                    str，其中编码类型c必须相同。
                    将unicode字符串写入文件前先使用特定编码对其进行编码(如unicodestr.encode('utf-8'))得到str，
                    保证写入文件的是str；从文件读取到str，然后对其进行解码(如encodestr.decode('utf-8'))得到unicode。
                    这是互逆的两个操作，编码类型一定要一致，否则会出现异常。
                    '''
                    #err = "catalog: %s, status:true, name: %s, url: %s, show: %s" % (k.decode('utf-8'), one['Text'], one['Link'], one['show'])
                    #error['游戏'].append(err)
                else:
                     err = "catalog: %s, status:false, name: %s, url: %s, show: %s" % (k.decode('utf-8'), one['Text'], one['Link'], one['show'])
                     error['游戏'].append(err)
            if error['游戏']:
                #for one in error['游戏']:
                    #print ' '.join([url_name.split(':', 1)[1].encode('utf8') for url_name in one.split(',')[2:4]])
                    #print one['name'].encode('utf8'), one['url'].encode('utf8')
                #sys.exit()
                mailmsg =  "\n".join(error['游戏']).encode('utf8')
                #smDemo.send_mail(sendmsg, "136030112@qq.com","seacoastboy@gmail.com","1616网址-报错",mailmsg)
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['游戏']).encode('utf8')))
                #print "title: %s, link: %s, show: %s" % (one['Text'], one['Link'], one['show'])
            #e = profile_time(s, '游戏')
        elif k == '新站':
            error['新站'] =[]
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n"% ('新站开始'))
            for one in v:
                sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( k, one['Text'].encode('utf8'), one['Link'].encode('utf8')))
                #print u"检查 分类 '%s' 中的 '%s' 网址 '%s'" % ( k.decode('utf-8'), one['Text'], one['Link'])
                if checkurl.checkurl(one['Link']) == False:
                     err = "catalog: %s, status:false, name: %s, url: %s, show: %s" % (k.decode('utf-8'), one['Text'], one['Link'], one['show'])
                     error['新站'].append(err)
            if error['新站']:
                mailmsg = "\n".join(error['新站']).encode('utf8')
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['新站']).encode('utf8')))
        elif k == '软件':
            error['软件'] = []
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n"%
            ('软件始'))
            for one in v:
                sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( k, one['Text'].encode('utf8'), one['Link'].encode('utf8')))
                #print u"检查 分类 '%s' 中的 '%s' 网址 '%s'" % ( k.decode('utf-8'), one['Text'], one['Link'])
                if checkurl.checkurl(one['Link']) == False:
                     err = "catalog: %s, status:false, name: %s, url: %s, show: %s" % (k.decode('utf-8'), one['Text'], one['Link'], one['show'])
                     error['软件'].append(err)
            if error['软件']:
                mailmsg = "\n".join(error['软件']).encode('utf8')
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['软件']).encode('utf8')))
        elif k == '工具':
            error['工具'] = []
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n" % ('工具开始'))
            for one in v:
                sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( k, one['Text'].encode('utf8'), one['Link'].encode('utf8')))
                if checkurl.checkurl(one['Link']) == False:
                    err = "catalog: %s, status:false, name: %s, url: %s, show: %s" % (k.decode('utf-8'), one['Text'], one['Link'], one['show'])
                    error['工具'].append(err)
            if error['工具']:
                mailmsg = "\n".join(error['工具']).encode('utf8')
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['软件']).encode('utf8')))
        elif k == '酷站':
            error['酷站'] = []
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n" % ('酷站开始'))
            for one in v:
                for row in one['site']:
                    sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 '%s' 分类 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % (
                    k, one['name'].encode('utf8'), row['Text'].encode('utf8'), row['Link'].encode('utf8')))
                    #print u"检查 分类 '%s' 中的 '%s'分类 中的 '%s' 网址 %s" % ( k.decode('utf-8'), one['name'],row['Text'], row['Link'])
                    if checkurl.checkurl(row['Link']) == False:
                        err = "catalog: %s, status:false, name: %s, url: %s" % (k.decode('utf-8'),
                        row['Text'], row['Link'])
                        error['酷站'].append(err)
            if error['酷站']:
                mailmsg = "\n".join(error['酷站']).encode('utf8')
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['酷站']).encode('utf8')))
        elif k == '名站':
            error['名站'] = []
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n"
            % ('名站开始'))
            for one in v:
                sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( k, one['Text'].encode('utf8'), one['Link'].encode('utf8')))
                if checkurl.checkurl(one['Link']) == False:
                    err = "catalog: %s, status:false, name: %s, url: %s, show: %s" % (k.decode('utf-8'), one['Text'], one['Link'], one['show'])
                    error['名站'].append(err)
            if error['名站']:
                mailmsg = "\n".join(error['名站']).encode('utf8')
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['名站']).encode('utf8')))
        elif k == '内页':
            error['内页'] = []
            sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n"
            % ('内页开始'))
            for one in v:
                sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( k, one['Text'].encode('utf8'), one['Link'].encode('utf8')))
                if checkurl.checkurl(one['Link']) == False:
                    err = "catalog: %s, status:false, name: %s, url: %s" % (k.decode('utf-8'), one['Text'], one['Link'])
                    error['内页'].append(err)
                    errormsg.append(one)
            if error['内页']:
                mailmsg = "\n".join(error['内页']).encode('utf8')
                sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['内页']).encode('utf8')))
                #print 'title: %s, link: %s' % (one['Text'], one['Link'])
    #对内页网址 列表 进行 二次 检查
    if errormsg:
        error['内页'] = []
        sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n" %
        ('内页二次开始'))
        for one in errormsg:
            sys.stdout.write("检查 分类\33[32m'%s'\33[0m 中的 \33[32m'%s'\33[0m 网址 '%s'\n" % ( '内页', one['Text'].encode('utf8'), one['Link'].encode('utf8')))
            if checkurl.checkurl(one['Link']) == False:
                err = "catalog: %s, status:false, name: %s, url: %s" % ('内页'.decode('utf-8'), one['Text'], one['Link'])
                error['内页'].append(err)
        if error['内页']:
            file_name='/home/zouzhihai/backup_py/urlstatus/log/error_url.txt'
            if os.path.isfile( file_name):
                os.remove(file_name)
            file = open(file_name, 'w+')
            for one in error['内页']:
                file.write( ' '.join([url_name.split(':', 1)[1].encode('utf8') for url_name in one.split(',')[2:4]]) + '\n')
            file.close()
            mailmsg = "\n".join(error['内页']).encode('utf8')
            sys.stdout.write("\33[31m'%s'\33[0m\n" % ("\n".join(error['内页']).encode('utf8')))

    sys.stdout.write("\33[31m-----------------------------------------%s-------------------------------\33[0m\n" %
    ('错误信息输入'))
    for k, v in error.iteritems():
        sys.stdout.write("分类\33[31m----------------'%s'----------\33[0m中的error网址\n" % (k))
        sys.stdout.write("\33[31m '%s' \33[0m\n" % ("\n".join(v).encode('utf8')))
except Exception, e:
    print Exception, '%s' % e
except KeyboardInterrupt, e:
    sys.stdout.write('\n')
sys.exit(0)

#vim:ts=4:sw=4:et
