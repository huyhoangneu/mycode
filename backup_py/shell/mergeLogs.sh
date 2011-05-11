#!/bin/sh
#最简单的想法是将日志一一读出来，然后按日志中的时间字段排序
#cat log1 log2 log3 |sort -k 4 -t ” ”
#注释：
#-t ” “: 日志字段分割符号是空格
#-k 4: 按第4个字段排序，也就是：[03/Apr/2002:10:30:17 +0800] 这个字段
#-o log_all: 输出到log_all这个文件中
#其实有一个优化的途径，要知道：即使单个日志本身已经是一个“已经按照时间排好序“的文件了，而sort对于这种文件的排序合并提供了一个优化合并算法： 使用 -m merge合并选项，
#因此：合并这样格式的3个日志文件log1 log2 log3并输出到log_all中比较好方法是：
#sort -m -t ” ” -k 4 -o log_all log1 log2 log3

date=$(date -d "yesterday" +"%Y_%m_%d")
logs_path="/data/www/logs/"
logfiles="www.xx.net.log api.xx.net.log app.xx.net.log apps.xx.net.log"
for log in ${logfiles}
    do 
        for file in `ls ${logs_path}/${log}/${date}_*`
            do
                if [ -f "$file" ]; then 
                    gzip -d ${file}
                fi 
            done
        sort -m -t " " -k 4 -o ${logs_path}/${log}/${date}.log ${logs_path}/${log}/${date}_*
        gzip ${logs_path}/${log}/${date}.log
        rm -rf ${logs_path}/${log}/${date}_*
    done
chown -R www-data.www-data /data/www/logs
echo "gzip suss"
#sort -m -t " " -k 4 -o log_all log1 log2 log3

