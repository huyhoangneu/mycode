#!/bin/bash
## yuncheng crawl script

## Modified here in order to adapt to different environments

SOURCE=/data/grab/data                  # The root directory of the source data
TARGET=/data/wwwroot     # The root directory of the target data

## rsync to remote host

R_HOST1='www-data@211.100.36.229'
R_TARGET1=/data/wwwroot

R_HOST2='www-data@211.100.36.226'
R_TARGET2=/data/wwwroot
/data/opt/php/bin/php /data/grab/astronomy/index_getstar.php
rsync -avz --rsh=ssh -e 'ssh -p 22' $SOURCE/xingzuo/ www-data@211.100.36.229:/data/wwwroot/outer/wwwdebug/www/grab/xingzuo/
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/xingzuo/ www-data@211.100.36.226:/data/wwwroot/www/grab/xingzuo/
