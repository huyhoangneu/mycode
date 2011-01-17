import os
import httplib
url = 'www.zg2sc.cn'
conn = httplib.HTTPConnection(url, timeout=50)
conn.request("GET", '/')
respons = conn.getresponse()
conn.close()
'''
file_name='/home/zouzhihai/backup_py//urlstatus/log/error_url.txt'
if os.path.isfile( file_name):
    os.remove(file_name)
'''
