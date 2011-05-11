#!/bin/bash

logs_path="/data/opt/nginx/logs/"
logfiles="www.xxx.net.log api.xxx.net.log app.xxx.net.log apps.xxx.net.log disk.xxx.net.log"
date=$(date -d "yesterday" +"%Y_%m_%d")
save_path="/data/logs"
mkdir -p ${save_path}

for log in ${logfiles}
do 
    mkdir -p ${save_path}/${log}/
    mv ${logs_path}/${log} ${save_path}/${log}/${date}_238.log
done
/etc/init.d/nginx reload
for log in ${logfiles}
do 
        gzip ${save_path}/${log}/${date}_238.log
        chown -R www-data.www-data /data/logs/
        cp ${save_path}/${log}/${date}_238.log.gz /data/www/logs/${log}/
        scp -P 50022 ${save_path}/${log}/${date}_238.log.gz www-data@10.0.0.237:/data/www/logs/${log}
done
#chown -R www-data.www-data /data/logs/
#rsync -avz /data/logs/ /data/www/logs/

#find /data/logs/apps.log/ -mtime +2 -type f -name "*.log.gz" | xargs rm -rf
#find /data/logs/i.log/ -mtime +2 -type f -name "*.log.gz" | xargs rm -rf 
