<?php
define('SPIDER_PATH', dirname(dirname(__FILE__)));
define('DATA_PATH', dirname(dirname(__FILE__)).'/data/weather/');
include (SPIDER_PATH.'/helper/lastRSS0.9.1.php');
include_once(SPIDER_PATH.'/db/db.class.php');
date_default_timezone_set ('Etc/GMT-8');
/*
$mysql = new MYSQL(
array(	
'host' => 'localhost',
	'db' => 'wap',
	'user' => 'root',
	'password' => 'root'
	)
);
$mysql->query("SET NAMES 'utf8'");
*/
?>