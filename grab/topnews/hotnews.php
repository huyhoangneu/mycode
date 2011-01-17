#!/data/opt/php/bin/php
<?php
include_once '/data/grab/db/config.inc.php';
$hotnews_path = '/data/grab/data/i/grab/';
$rs = $mysql->get_all("SELECT * FROM `hotnews`");
$hotnews = "var hot_news = [";
foreach ($rs as $k => $v) 
{
	$hotnews .= "'".$v[title]."' ,'".$v[url]."',";
}
$hotnews = substr($hotnews, 0, -1);
$hotnews .=']; if(typeof(news_cb) == "function") news_cb();';
file_put_contents($hotnews_path.'news.js', $hotnews, LOCK_EX);
