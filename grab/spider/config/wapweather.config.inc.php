<?php
define('SPIDER_PATH', dirname(dirname(__FILE__)));
define('DATA_PATH', dirname(dirname(__FILE__)).'/data/wapweather/');
include_once(SPIDER_PATH.'/helper/template.php');
date_default_timezone_set ('Etc/GMT-8');
$v = new Template();
$v->template_dir = SPIDER_PATH.'/wapweather/view';
$v->compile_dir = SPIDER_PATH.'/wapweather/compile';
?>