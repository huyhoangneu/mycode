#!/bin/bash
## Weather crawl script

## Modified here in order to adapt to different environments

SOURCE=/data/grab/data			# The root directory of the source data
TARGET=/data/wwwroot/weather/grab/weather/ 	                # The root directory of the target data

## rsync to remote host
R_HOST1='www-data@211.100.36.226'
R_TARGET1=/data/wwwroot/weather/grab/

#debug - test-
R_HOST2='www-data@211.100.36.229'
R_TARGET2=/data/wwwroot/outer/wwwdebug/weather/grab

## No need to change this regional

#target_path[3]=$TARGET/www
target_path[1]=$TARGET/weather

## Remote target 1
remote_target1=$R_TARGET1/weather

## Remote target 2
remote_target2=$R_TARGET2/weather

#/data/opt/php/bin/php /data/grab/weathernew/get_weath.php

/data/opt/php/bin/php /data/grab/weathernew/new/weather.php
#/data/opt/php/bin/php /data/grab/weathernew/new/newsweather.php
if [ ! -x "$TARGET" ]; then 
		mkdir -p "$TARGET"
fi
rsync -az $SOURCE/weather/ $TARGET
chmod -R 777 $TARGET
## Remote 1 running
ssh -p50022 $R_HOST1 "if [ ! -x "$R_TARGET1" ]; then mkdir -p "$R_TARGET1/weather/";fi"
rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/weather/ $R_HOST1:$R_TARGET1/weather/

ssh -p 22 $R_HOST2 "if [ ! -x "$R_TARGET2" ]; then mkdir -p "$R_TARGET2/weather/";fi"
rsync -avz --rsh=ssh -e 'ssh -p 22' $SOURCE/weather/ $R_HOST2:$R_TARGET2/weather/
