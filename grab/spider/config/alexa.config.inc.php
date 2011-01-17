<?php
define('SPIDER_PATH', dirname(dirname(__FILE__)));
include_once(SPIDER_PATH.'/helper/Alexa.php');
include_once(SPIDER_PATH.'/db/db.class.php');
date_default_timezone_set ('Etc/GMT-8');
$mysql = new MYSQL(
array(	
'host' => 'localhost',
	'db' => 'cms',
	'user' => 'root',
	'password' => 'root'
	)
);
$mysql->query("SET NAMES 'utf8'");
?>