<?php
include_once('db.class.php');
date_default_timezone_set ('Etc/GMT-8');
$mysql = new MYSQL(
array(	
'host' => 'localhost',
	'db' => 'wap',
	'user' => 'root',
	'password' => 'root'
	)
);
$mysql->query("SET NAMES 'utf8'");
?>