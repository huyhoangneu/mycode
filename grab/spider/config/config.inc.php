<?php
define('DB_PATH', dirname(dirname(__FILE__)));
include_once(DB_PATH.'/db/db.class.php');
define('GREB_PATH', dirname(dirname(__FILE__)).'/data/');
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