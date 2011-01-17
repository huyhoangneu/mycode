#!/bin/bash
## hotnews crawl script

SOURCE=/data/grab/data                  # The root directory of the source data
SOURCE_FILE=/data/grab/data/i/grab/news.js
TARGET=/data/wwwroot/outer/wwwdebug     # The root directory of the target data

## rsync to remote host

R_HOST1='www-data@124.40.117.135'
R_TARGET1=/data/wwwroot

R_HOST2=202.98.23.120
R_TARGET2=/home/wwwroot

## No need to change this regional
target_path[1]=$TARGET/i

## Remote target 1
remote_target1[1]=$R_TARGET1/i

# Remote target 2
remote_target2[1]=$R_TARGET2/i

case "$1" in
  local)
	/data/opt/php/bin/php /data/grab/topnews/hotnews.php
	for i in ${target_path[*]}
	do
		if [ ! -x "$i/grab" ]; then
			mkdir -p "$i/grab"
		fi
		rsync -az $SOURCE/i/grab/ $i/grab/
		chmod -R 777 $i/grab/
	done
	;;
  remote)
	## Remote 1 running
	a=`stat -c %Y ${SOURCE_FILE} |awk '{printf  $0" "; system("date +%s")}'|awk '{print $2-$1}'`;
	if [ -f ${SOURCE_FILE} ] && [ $a -le 3600 ];then
		for i in ${remote_target1[*]}
		do
			ssh -p50022 $R_HOST1 "if [ ! -x "$i/grab" ]; then mkdir -p "$i/grab";fi"
			rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/i/grab/ $R_HOST1:$i/grab/
		done
		## Remote 2 running
		for i in ${remote_target2[*]}
		do
			ssh $R_HOST2 "if [ ! -x "$i/grab" ]; then mkdir -p "$i/grab";fi"
			rsync -avz --rsh=ssh $SOURCE/i/grab/ $R_HOST2:$i/grab/
		done
		/data/opt/php/bin/php /data/grab/cli/cli.php -u i.9533.com/grab/news.js
	fi
	;;
  *)
	/data/opt/php/bin/php /data/grab/topnews/hotnews.php
	for i in ${target_path[*]}
	do
		if [ ! -x "$i/grab" ]; then
			mkdir -p "$i/grab"
		fi
		rsync -az $SOURCE/i/grab/ $i/grab/
		chmod -R 777 $i/grab/
	done
	;;
esac

exit 0
