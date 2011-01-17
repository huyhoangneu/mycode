#!/bin/bash
## Weather crawl script
SOURCE=/data/grab/data
R_HOST1='www-data@124.40.117.135'
R_HOST2='202.98.23.120'
CITYSJSON1=/data/wwwroot/www
CITYSJSON2=/home/wwwroot/www
        ssh -p50022 $R_HOST1 "if [ ! -x "$CITYSJSON1/grab" ]; then mkdir -p "$CITYSJSON1/grab";fi"
        rsync -avz --rsh=ssh -e 'ssh -p 50022' $SOURCE/weather/citysJSON.js $R_HOST1:$CITYSJSON1/grab/weather/citysJSON.js

        ssh -p22 $R_HOST2 "if [ ! -x "$CITYSJSON2/grab" ]; then mkdir -p "$CITYSJSON2/grab";fi"
        rsync -avz --rsh=ssh -e 'ssh -p 22' $SOURCE/weather/citysJSON.js $R_HOST2:$CITYSJSON2/grab/weather/citysJSON.js
