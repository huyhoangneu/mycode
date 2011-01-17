<?php
define('DB_PATH', dirname(dirname(__FILE__)));
define('SPIDER_PATH', dirname(dirname(__FILE__)));
date_default_timezone_set ('Etc/GMT-8');
include_once(DB_PATH.'/db/db.class.php');
date_default_timezone_set ('Etc/GMT-8');
$mysql = new MYSQL(
	array(
		//'host' => 'localhost',
		//'db' => 'wap',
		//'user' => 'root',
		//'password' => 'wangchao901'
		'host' => '211.100.36.226',
		'db' => 'wap',
		'user' => 'wap',
		'password' => 'wap1616**',
	)
);
$mysql->query("SET NAMES 'utf8'");
?>
