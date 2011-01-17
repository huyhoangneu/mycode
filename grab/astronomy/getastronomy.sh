#!/bin/bash
## yuncheng crawl script

## Modified here in order to adapt to different environments

SOURCE=/data/grab/data                  # The root directory of the source data
TARGET=/data/wwwroot     # The root directory of the target data

## rsync to remote host


R_HOST2='www-data@211.100.36.226'
R_TARGET2=/data/wwwroot

## No need to change this regional

## Local target
target_path[1]=$TARGET/yuncheng
target_path[2]=$TARGET/wannianli

## Remote target 2
remote_target2[1]=$R_TARGET2/yuncheng
remote_target2[2]=$R_TARGET2/wannianli

/data/opt/php/bin/php /data/grab/astronomy/getstar.php
#/data/opt/php/bin/php /data/grab/astronomy/getstar_sns.php
/data/opt/php/bin/php /data/grab/astronomy/getstar_i.php
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ www-data@211.100.36.229:/data/wwwroot/outer/wwwdebug/yuncheng/grab/yuncheng/
#test-121cha
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ www-data@211.100.36.229:/data/wwwroot/outer/wwwdebug/121cha/grab/yuncheng/
#121cha
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ www-data@211.100.36.226:/data/wwwroot/121cha/grab/yuncheng/
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ www-data@111.67.199.167:/data/wwwroot/121cha/grab/yuncheng/

rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ www-data@125.32.112.44:/data/wwwroot/yuncheng/grab/yuncheng/
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ www-data@125.32.112.44:/data/wwwroot/wannianli/grab/yuncheng/

#237 236
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/i/ www-data@211.100.36.238:/data/wwwroot/apps/xingzuo/www/grab/yuncheng/
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/i/ www-data@211.100.36.236:/data/wwwroot/apps/xingzuo/www/grab/yuncheng/
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/i/ www-data@211.100.36.237:/data/wwwroot/apps/xingzuo/www/grab/yuncheng/
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/i/ www-data@211.100.36.232:/data/wwwroot/apps/xingzuo/www/grab/yuncheng/
#test 229 new
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/i/ www-data@211.100.36.229:/data/platform/svn/ncms/product/test/apps/xingzuo/www/grab/yuncheng/
## Local running
for i in ${target_path[*]}
do
        if [ ! -x "$i/grab" ]; then
                mkdir -p "$i/grab"
        fi
        rsync -avz $SOURCE/yuncheng/ $i/grab/yuncheng/
	chmod -R 777 $i/grab/yuncheng/
done

## Remote 1 running
for i in ${remote_target2[*]}
do
	ssh -p50022 $R_HOST2 "if [ ! -x "$i/grab" ]; then mkdir -p "$i/grab";fi"
        rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/yuncheng/ $R_HOST2:$i/grab/yuncheng/
done
