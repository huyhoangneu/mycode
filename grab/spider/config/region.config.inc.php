<?php
define('DB_PATH', dirname(dirname(__FILE__)));
define('DATA_PATH', dirname(dirname(__FILE__)).'/data/phone/');
include_once(DB_PATH.'/db/db.class.php');
date_default_timezone_set ('Etc/GMT-8');
$mysql = new MYSQL(
array(	
'host' => 'localhost',
	'db' => 'spider',
	'user' => 'root',
	'password' => 'root'
	)
);
$mysql->query("SET NAMES 'utf8'");
?>