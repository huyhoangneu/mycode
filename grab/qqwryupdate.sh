#!/bin/bash
## update qqwry database

URL='http://zzidc.onlinedown.net/down/qqwry.rar'
PATH='/data/wwwroot/chaxun/data'
REMOTEPATH='www-data@211.100.36.226:/data/wwwroot/chaxun/data'
#REMOTEPATH2='www-data@211.100.36.227:/data/www/tjt'

cd /data/grab/qqwry
/usr/bin/wget $URL
/data/grab/qqwry/rar/unrar e -o+ qqwry.rar
/bin/cp QQWry.Dat $PATH/qqwry.dat
/usr/bin/scp -P 50022 QQWry.Dat $REMOTEPATH/qqwry.dat
#/usr/bin/scp -P 50022 QQWry.Dat $REMOTEPATH2/QQWry.Dat

/bin/rm qqwry.rar
