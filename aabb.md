安装软件前需要更新apt源，`aptitude update`,保证有root权限，或者 `sudo aptitude update`
一下说明是拥有了root权限的操作过程

Mysql 安装 & 配置
--------------------------------------

Nginx 安装 & 配置
--------------------------------------

python 安装
--------------------------------------

Django 安装
--------------------------------------
  * 安装django，`aptitude install python-django`
  * web程序是基于Django开发，同时使用了第三方库`python-mysqldb `、`python-django-south`
    * aptitude install python-mysqldb
    * aptitude install python-django-south
  
uwsgi & uwsgi-plugin-python 安装
--------------------------------------
  * aptitude install uwsgi-plugin-python
  * aptitude install uwsgi
  * 添加修改配置文件
  * vim /etc/uwsgi/uwsgi.ini
   
``` ini
[uwsgi]
projectname = www
projectdomain = example.com
base = /data/wwwroot/website/www
protocol = uwsgi
master=True
processes = 1
threads = 20
harakiri=20
#limit-as=64
max-requests=5000
vacuum=True
enable-threads=True
socket = 127.0.0.1:49152
pythonpath = %(base)
module = %(projectname).wsgi
socket = /tmp/%(projectdomain).sock
logto = /var/log/uwsgi.log 
daemonize=/var/log/mysite.log
```
  * uwsgi管理
    * 启动 /usr/bin/uwsgi /etc/uwsgi/uwsgi.ini
    * 停止 killall -9 uwsgi

website 部署
--------------------------------------
  * 导出项目文件到/data/wwwroot/目录下
  * 修改数据库配置文件
  * 初始化项目导入sql

