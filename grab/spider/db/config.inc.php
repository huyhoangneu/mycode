<?php
include_once('db.class.php');
define('GREB_PATH', dirname(dirname(__FILE__)).'/data/');
date_default_timezone_set ('Etc/GMT-8');
$mysql = new MYSQL(
array(	
'host' => '211.100.36.229',
	'db' => 'cms',
	'user' => '1616cms',
	'password' => '1616#$%^cms^%$#'
	)
);
$mysql->query("SET NAMES 'utf8'");
?>
