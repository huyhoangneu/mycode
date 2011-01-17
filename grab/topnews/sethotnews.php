#!/data/opt/php/bin/php
<?php
$hotnews_path = '/data/grab/data/i/grab/';
if (is_file($hotnews_path.'news.js')) 
{
	$time1 = filemtime($hotnews_path.'news.js');
	$time2 = time();
	if(($time2-$time1) <= 60*60)
	{
		echo "1";
	}
	else 
	{
		echo "0";
	}
}
else 
{
	echo "0";
}
