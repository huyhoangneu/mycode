<?php
define('ROOT_PATH', dirname(__FILE__));
include_once(ROOT_PATH.'/config.php');
include_once(ROOT_PATH.'/function1.php');
set_time_limit(0);

//配置目录
if(!is_dir(ROOT))	mkdir(ROOT);

if(!is_dir(ROOT . 'today'))	mkdir(ROOT . 'today');//天

if(!is_dir(ROOT . 'tomorrow'))	mkdir(ROOT . 'tomorrow');//明天
if(!is_dir(ROOT . 'week'))	mkdir(ROOT . 'week');//本周
if(!is_dir(ROOT . 'month'))	mkdir(ROOT . 'month');// 本月
if(!is_dir(ROOT . 'year'))	mkdir(ROOT . 'year');// 本年
if(!is_dir(ROOT . 'love'))	mkdir(ROOT . 'love');// 爱情

/**/
// create today star files
foreach ($star_array as $key => $value) 
{
	//这里是 天的 星座 
	/*二次 改进*/
	$filename = $key .'.htm';
	$a = read_templat('template/day.inc.php', get_day($key));
	file_put_contents(ROOT . 'today/' . $filename, $a);
}

// create tomorrow star files
foreach ($star_array as $key => $value) 
{
	$filename = $key .'.htm';
	$a = read_templat('template/day.inc.php', get_day($key . '_1'));
	file_put_contents(ROOT . 'tomorrow/' . $filename, $a);
}

// create week star files
foreach ($star_array as $key => $value) 
{
	$filename = $key .'.htm';
	$a = read_templat('template/week.inc.php', get_week($key));
	file_put_contents(ROOT . 'week/' . $filename, $a);
}

// create month star files
foreach ($star_array as $key => $value) 
{
	$filename = $key .'.htm';
	$a = read_templat('template/month.inc.php', get_month($key));
	file_put_contents(ROOT . 'month/' . $filename, $a);
}

// create year star files
foreach ($star_array as $key => $value) 
{
	$filename = $key .'.htm';
	$a = read_templat('template/year.inc.php', get_year($key));
	file_put_contents(ROOT . 'year/' . $filename, $a);
}
/**/

// create love star files
foreach ($star_array as $key => $value) 
{
	$filename = $key .'.htm';
	$a = read_templat('template/love.inc.php', get_love($key));
	file_put_contents(ROOT . 'love/' . $filename, $a);
}
?>
