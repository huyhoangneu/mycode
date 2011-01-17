#!/bin/bash
#
# Web Server Install Script
# Created by wdlinux QQ:12571192
# Url:http://www.wdlinux.cn
# 2010.04.08
# Last Updated 2010.05.27
# 

IN_PWD=$(pwd)
IN_SRC=${IN_PWD}/soft
IN_LOG=${IN_SRC}/wdl_install.log
IN_DIR="/usr/local"
SERVER="apache"
RE_INS=0
X86=0
SOFT_DOWN=0

#soft url and down
HTTPD_DU="http://www.eu.apache.org/dist/httpd/httpd-2.2.15.tar.gz"
NGINX_DU="http://nginx.org/download/nginx-0.8.40.tar.gz"
MYSQL_DU="http://mirrors.sohu.com/mysql/MySQL-5.1/mysql-5.1.47.tar.gz"
PHP_DU="http://cn2.php.net/get/php-5.2.13.tar.gz/from/cn.php.net/mirror"
EACCELERATOR_DU="http://bart.eaccelerator.net/source/0.9.6/eaccelerator-0.9.6.tar.bz2"
ZEND_DU="http://downloads.zend.com/optimizer/3.3.3/ZendOptimizer-3.3.3-linux-glibc23-i386.tar.gz"
ZENDX86_DU="http://downloads.zend.com/optimizer/3.3.3/ZendOptimizer-3.3.3-linux-glibc23-x86_64.tar.gz"
PHP_FPM_DU="http://php-fpm.org/downloads/php-5.2.13-fpm-0.5.14.diff.gz"
VSFTPD_DU="http://hbdx.wdlinux.cn:8080/vsftpd-2.2.2.tar.gz"
PHPMYADMIN_DU="http://hbdx.wdlinux.cn:8080/phpMyAdmin-3.3.3-all-languages.tar.gz"

if [[ ! -d $IN_SRC ]];then
        mkdir $IN_SRC
fi
if [[ ! -d $IN_DIR ]];then
        mkdir -p $IN_DIR
fi
if [[ `uname -m | grep "x86_64"` ]];then
	X86=1
fi
if [[ $1 == "nginx" ]];then
	SERVER="nginx"
else
	SREVER="apache"
fi
cd $IN_SRC

function make_clean {
	if [[ $RE_INS == 1 ]];then
		make clean
	fi
}
function wget_down {
	if [[ $SOFT_DOWN == 1 ]];then
	echo "start down..."
        for i in $*; do
                [ `wget -c $i` ] && exit
        done
	fi
}

if [[ $SERVER == "apache" ]];then
	wget_down $HTTPD_DU
elif [[ $SERVER == "nginx" ]];then
	wget_down $NGINX_DU $PHP_FPM
fi
if [[ $X86 == "1" ]];then
	wget_down $ZENDX86_DU
else
	wget_down $ZEND_DU
fi
wget_down $MYSQL_DU $PHP_DU $EACCELERATOR_DU $VSFTPD_DU $PHPMYADMIN_DU

function error {
        echo "ERROR: "$1
        exit
}

function file_cp {
	if [[ -f $2 ]];then
		mv $2 $2`date +%Y%m%d%H`
	fi
	cp $1 $2
}
function file_bk {
	if [[ -f $1 ]];then
		mv $1 $1"_"`date +%Y%m%d%H`
	fi
}
sed -i '/wdl_install.sh/d' /etc/rc.d/rc.local

# install function
function mysql_ins {
	echo "installing mysql..."
	cd $IN_SRC
	useradd -d /dev/null -s /sbin/nologin mysql >&2 > /dev/null
	tar zxvf mysql-5.1.47.tar.gz >&2 > /dev/null
	if [[ $X86 == 1 ]];then
		LIBNCU="/usr/lib64/libncursesw.so.5"
	else
		LIBNCU="/usr/lib/libncursesw.so.5"
	fi		
	cd mysql-5.1.47/
	make_clean	>&2 > /dev/null
	./configure --prefix=$IN_DIR/mysql-5.1.47 --enable-assembler --enable-thread-safe-client --with-extra-charsets=complex --with-ssl --with-embedded-server --with-named-curses-libs=$LIBNCU	>&2 > /dev/null
	[ $? != 0 ] && exit
	make	>&2 > /dev/null
	[ $? != 0 ] && exit
	make install	>&2 > /dev/null
	[ $? != 0 ] && exit
	ln -s $IN_DIR/mysql-5.1.47 $IN_DIR/mysql
	file_cp "support-files/my-large.cnf" "/etc/my.cnf"
	$IN_DIR/mysql/bin/mysql_install_db >&2 > /dev/null
	chown -R mysql.mysql $IN_DIR/mysql/var
	file_cp "support-files/mysql.server" "/etc/rc.d/init.d/mysqld"
	chmod 755 /etc/rc.d/init.d/mysqld
	chkconfig --add mysqld
	/etc/rc.d/init.d/mysqld start
	PATH=$PATH:$IN_DIR/mysql/bin
	$IN_DIR/mysql/bin/mysql_secure_installation
}


function apache_ins {
	echo "installing httpd..."
	cd $IN_SRC
	tar zxvf httpd-2.2.15.tar.gz	>&2 > /dev/null
	cd httpd-2.2.15
	make_clean	>&2 > /dev/null
	./configure --prefix=$IN_DIR/httpd-2.2.15 --with-mpm=worker --enable-rewrite --enable-deflate --disable-userdir --enable-so	>&2 > /dev/null
	[ $? != 0 ] && exit
	make	>&2 > /dev/null
	[ $? != 0 ] && exit
	make install	>&2 > /dev/null
	[ $? != 0 ] && exit
	ln -s $IN_DIR/httpd-2.2.15 $IN_DIR/apache
	cd $IN_DIR/apache/conf/extra
	mv httpd-vhosts.conf httpd-vhost.conf.bk
	#wget_down http://www.wdlinux.cn/conf/httpd22/httpd-wdl.conf
	#wget_down http://www.wdlinux.cn/conf/httpd22/httpd-vhosts.conf
	sed -i 's|#Include conf/extra/httpd-vhosts.conf|Include conf/extra/httpd-vhosts.conf|g' $IN_DIR/apache/conf/httpd.conf
	echo "Include conf/extra/httpd-wdl.conf" >> $IN_DIR/apache/conf/httpd.conf
	echo "$IN_DIR/apache/bin/apachectl start" >> /etc/rc.d/rc.local 
	#echo "<? phpinfo();?>" > $IN_DIR/apache/htdocs/phpinfo.php
	mkdir -p /home/wdlinux
	echo "<? phpinfo();?>" > /home/wdlinux/phpinfo.php
}

function nginx_ins {
	echo "installing nginx..."
	cd $IN_SRC
	tar zxvf nginx-0.8.40.tar.gz	>&2 > /dev/null
	cd nginx-0.8.40
	make_clean	>&2 > /dev/null
	./configure --user=www --group=www --prefix=$IN_DIR/nginx-0.8.40 --with-http_stub_status_module --with-http_ssl_module	>&2 > /dev/null
	[ $? != 0 ] && exit
	make	>&2 > /dev/null
	[ $? != 0 ] && exit
	make install	>&2 > /dev/null
	[ $? != 0 ] && exit
	ln -s $IN_DIR/nginx-0.8.40 $IN_DIR/nginx
	mkdir -p /home/wdlinux	>&2 > /dev/null
	#cd $IN_DIR/nginx/conf
	#file_bk nginx.conf
	#wget_down http://www.wdlinux.cn/conf/nginx/nginx.conf
	#file_bk fcgi.conf
	#wget http://www.wdlinux.cn/conf/nginx/fcgi.conf
	echo "$IN_DIR/php/sbin/php-fpm start" >> /etc/rc.d/rc.local
	#cd /etc/rc.d/init.d/
	#wget_down http://www.wdlinux.cn/conf/nginx/nginxd
	#chmod 755 nginxd
	#chkconfig --add nginxd
	#echo "<? phpinfo();?>" > $IN_DIR/nginx/html/phpinfo.php	
	echo "<? phpinfo();?>" > /home/wdlinux/phpinfo.php
	cp $IN_DIR/nginx/html/index.html /home/wdlinux
}

function php_ins {
	echo "installing php..."
	cd $IN_SRC
	tar zxvf php-5.2.13.tar.gz	>&2 > /dev/null
	NV=""
	[ $SERVER == "nginx" ] && NV="--enable-fastcgi --enable-fpm" && gzip -cd php-5.2.13-fpm-0.5.14.diff.gz | patch -fd php-5.2.13 -p1	>&2 > /dev/null
	[ $SERVER == "apache" ] && NV="--with-apxs2=$IN_DIR/apache/bin/apxs"
	cd php-5.2.13/
	make_clean	>&2 > /dev/null
	./configure --prefix=$IN_DIR/php-5.2.13 --with-mysql=$IN_DIR/mysql --with-iconv-dir=/usr --with-freetype-dir --with-jpeg-dir --with-png-dir --with-zlib --with-libxml-dir=/usr --enable-xml --disable-rpath --enable-discard-path --enable-inline-optimization --with-curl --enable-mbregex --enable-mbstring --with-mcrypt --with-gd --enable-gd-native-ttf --with-openssl --with-mhash $NV	>&2 > /dev/null
	[ $? != 0 ] && exit
	make	>&2 > /dev/null
	[ $? != 0 ] && exit
	make install	>&2 > /dev/null
	[ $? != 0 ] && exit
	ln -s $IN_DIR/php-5.2.13 $IN_DIR/php
	cp php.ini-dist $IN_DIR/php/lib/php.ini
	if [[ $SERVER == "nginx" ]];then
		sed -i '/nobody/s#<!--##g' $IN_DIR/php/etc/php-fpm.conf
		sed -i '/nobody/s#-->##g' $IN_DIR/php/etc/php-fpm.conf
	fi
}


function eaccelerator_ins {
	echo "installing eaccelerator..."
	cd $IN_SRC
	tar jxvf eaccelerator-0.9.6.tar.bz2	>&2 > /dev/null
	cd eaccelerator-0.9.6/
	make_clean	>&2 > /dev/null
	$IN_DIR/php/bin/phpize	>&2 > /dev/null
	./configure --enable-eaccelerator=shared --with-php-config=$IN_DIR/php/bin/php-config	>&2 > /dev/null
	[ $? != 0 ] && exit
	make	>&2 > /dev/null
	[ $? != 0 ] && exit
	make install	>&2 > /dev/null
	[ $? != 0 ] && exit
	mkdir $IN_DIR/eaccelerator_cache
	if [[ $SERVER == "nginx" ]];then
		EA_DIR="$IN_DIR/php/lib/php/extensions/no-debug-non-zts-20060613"
	else
		EA_DIR="$IN_DIR/php/lib/php/extensions/no-debug-zts-20060613"
	fi
echo '[eaccelerator]
extension_dir="'$EA_DIR'"
extension="/eaccelerator.so"
eaccelerator.shm_size="64"
eaccelerator.cache_dir="'$IN_DIR'/eaccelerator_cache"
eaccelerator.enable="1"
eaccelerator.optimizer="1"
eaccelerator.check_mtime="1"
eaccelerator.debug="0"
eaccelerator.filter=""
eaccelerator.shm_max="0"
eaccelerator.shm_ttl="3600"
eaccelerator.shm_prune_period="3600"
eaccelerator.shm_only="0"
eaccelerator.compress="1"
eaccelerator.compress_level="9"' >> $IN_DIR/php/lib/php.ini
}

function zend_ins {
        echo "Zend installing..."
        cd $IN_SRC
	if [[ $X86 == "1" ]];then
	        tar zxvf ZendOptimizer-3.3.3-linux-glibc23-x86_64.tar.gz	>&2 > /dev/null
	        cd ZendOptimizer-3.3.3-linux-glibc23-x86_64
        	sh install.sh
	else
	        tar zxvf ZendOptimizer-3.3.3-linux-glibc23-i386.tar.gz	>&2 > /dev/null
	        cd ZendOptimizer-3.3.3-linux-glibc23-i386
        	sh install.sh
	fi
}

function vsftpd_ins {
	echo "vsftpd installing..."
	cd $IN_SRC
	tar zxvf vsftpd-2.2.2.tar.gz	>&2 > /dev/null
	cd vsftpd-2.2.2
	make	>&2 > /dev/null
	mkdir /usr/share/empty
	mkdir -p $IN_DIR/vsftpd
	install -m 755 vsftpd $IN_DIR/vsftpd/vsftpd
	install -m 644 vsftpd.8 /usr/share/man/man8
	install -m 644 vsftpd.conf.5 /usr/share/man/man5
	install -m 644 vsftpd.conf /etc/vsftpd.conf
	cd /etc/
	file_bk vsftpd.conf
	#wget_down http://www.wdlinux.cn/conf/vsftpd.conf
	#wget_down http://www.wdlinux.cn/conf/vsftpd.denyuser
	echo "$IN_DIR/vsftpd/vsftpd &" >> /etc/rc.d/rc.local
}

function phpmyadmin_ins {
	echo "phpmyadmin installing..."
	cd $IN_SRC
	tar zxvf phpMyAdmin-3.3.3-all-languages.tar.gz	>&2 > /dev/null
	#mv phpMyAdmin-3.3.3-all-languages $IN_DIR/apache/htdocs/phpSqlAdmin
	mv phpMyAdmin-3.3.3-all-languages /home/wdlinux/phpSqlAdmin
}

function conf {
	cd $IN_PWD/conf
	file_cp my.cnf /etc/my.cnf
	file_cp vsftpd.conf /etc/vsftpd.conf
	file_cp vsftpd.denyuser /etc/vsftpd.denyuser
	if [[ $SERVER == "apache" ]];then
		file_cp httpd-vhosts.conf $IN_DIR/apache/conf/extra/httpd-vhosts.conf
		file_cp httpd-wdl.conf $IN_DIR/apache/conf/extra/httpd-wdl.conf
	else
		file_cp fcgi.conf $IN_DIR/nginx/conf/fcgi.conf
		file_cp nginx.conf $IN_DIR/nginx/conf/nginx.conf
		file_cp nginxd /etc/rc.d/init.d/nginxd
		chmod 755 /etc/rc.d/init.d/nginxd
		chkconfig --add nginxd
	fi
}

function start {
	echo "start..."
	if [[ $SERVER == "nginx" ]];then
		$IN_DIR/php/sbin/php-fpm start
		/etc/rc.d/init.d/nginxd start
	else
		$IN_DIR/apache/bin/apachectl start
	fi
	$IN_DIR/vsftpd/vsftpd &
}

echo "Select Install
        1 apache + php + mysql
        2 nginx + php + mysql
	3 don't install is now
"
read -p "Please Input 1,2,3: " SERVER_ID
if [[ $SERVER_ID == 2 ]];then
        SERVER="nginx"
elif [[ $SERVER_ID == 1 ]];then
        SERVER="apache"
else
	exit
fi 
if [[ $SOFT_DOWN == 0 ]];then
	cd $IN_PWD
	if [[ -f soft.tar.gz ]];then
		tar zxvf soft.tar.gz >&2 > /dev/null
	else
		wget -c http://hbdx.wdlinux.cn:8080/soft.tar.gz
		tar zxvf soft.tar.gz >&2 > /dev/null
	fi
fi
mysql_ins
${SERVER}_ins
php_ins	
eaccelerator_ins
zend_ins
vsftpd_ins
phpmyadmin_ins
conf
start
