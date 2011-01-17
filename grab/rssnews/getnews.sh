#!/bin/bash

SOURCE=/data/grab/data
target_path=/data/wwwroot/news
H_NEWS=/data/grab/data/news/huanqiu.js
R_HOST[1]='www-data@211.100.36.226'
R_HOST[2]='www-data@211.100.36.229'

/data/opt/php/bin/php /data/grab/rssnews/rss.php
rsync -avz --rsh=ssh -e 'ssh -p 50022' $H_NEWS www-data@211.100.36.226:/data/wwwroot/wo116/grab/news/huanqiu.js
rsync -avz --rsh=ssh -e 'ssh -p 50022' $H_NEWS www-data@211.100.36.226:/data/wwwroot/www/grab/news/huanqiu.js
rsync -avz --rsh=ssh -e 'ssh -p 50022' $H_NEWS www-data@211.100.36.229:/data/wwwroot/outer/wwwdebug/www/grab/news/huanqiu.js
rsync -avz --rsh=ssh -e 'ssh -p 50022' $H_NEWS www-data@211.100.36.229:/data/wwwroot/outer/wwwdebug/wo116/grab/news/huanqiu.js

#rsync -avz --rsh=ssh -e 'ssh -p 50022' $H_NEWS www-data@125.32.112.44:/data/wwwroot/wo116/grab/news/huanqiu.js
#rsync -avz --rsh=ssh -e 'ssh -p 50022' $H_NEWS www-data@211.94.131.46:/data/wwwroot/wo116/grab/news/huanqiu.js
exit 0
