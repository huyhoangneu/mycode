<?php
define('CONFIG_PATH', dirname(dirname(__FILE__)));
ini_set('default_socket_timeout','600');
$path = '/data/grab/data/news/';
$url ="http://www.huanqiu.com/hezuo/1616.xml";

$contents = file_get_contents($url);
preg_match_all("'<channel>(.*?)</channel>'is", $contents, $tmp);

foreach ($tmp['1'] as $k => $info) 
{
	//<title>
	preg_match("'<title>(.*?)</title>'is", $info, $title);
	preg_match("'<link>(.*?)</link>'is", $info, $link);
	if ($title['1'] == '头条') 
	{
		//hot news
		preg_match_all("'<item>(.*?)</item>'is", $info, $hot);
		foreach ($hot['1'] as $hot_v) 
		{
			preg_match("'<title>(.*?)</title>'is", $hot_v, $hot_title);
			preg_match("'<link>(.*?)</link>'is", $hot_v, $hot_url);
			$news_cb['hot_news'][] = array('title'=>trim($hot_title['1']), 'url'=>trim($hot_url['1']));
		}
		
	}
	else if($title['1'] == '图片新闻')
	{
		preg_match_all("'<item>(.*?)</item>'is", $info, $img);
		foreach ($img['1'] as $img_v) 
		{
			preg_match("'<title>(.*?)</title>'is", $img_v, $img_title);
			preg_match("'<link>(.*?)</link>'is", $img_v, $img_url);
			preg_match("'<imgUrl>(.*?)</imgUrl>'is", $img_v, $pic_url);
			$news_cb['pic_news'][] = array('title'=>trim($img_title['1']), 'url'=>trim($img_url['1']),  'pic_url'=> trim($pic_url['1']) );
		}
	}
	else 
	{
		$category = $title['1'];
		$category_url = $link['1'];
		//
		preg_match_all("'<item>(.*?)</item>'is", $info, $img);
		foreach ($img['1'] as $img_v) 
		{
			preg_match("'<title>(.*?)</title>'is", $img_v, $img_title);
			preg_match("'<link>(.*?)</link>'is", $img_v, $img_url);
			preg_match("'<imgUrl>(.*?)</imgUrl>'is", $img_v, $pic_url);
			$news_cb['news'][] = array('title'=>trim($img_title['1']), 'url'=>trim($img_url['1']),  'cate'=> trim($category), 'category_url' => $category_url);
		}
	}
}
//print_r($news_cb);exit;
$hotnews = 'news_cb({hot_news: [';
foreach ($news_cb as $key => $value) 
{
	if($key == 'hot_news')
	{
		foreach ($value as $v_hot) 
		{
			$hot_news[] = '{title: "'.addslashes($v_hot['title']).'", url: "'.$v_hot['url'].'"}'; 
		}
	}
	else if($key == 'pic_news')
	{
		foreach ($value as $v_pic) 
		{
			$img_news[] = '{title: "'.addslashes($v_pic['title']).'", url: "'.$v_pic['url'].'", pic_url: "'.$v_pic['pic_url'].'"}';
		}
	}
	else if($key == 'news')
	{
		foreach ($value as $v_news) 
		{
			$focus_news[] = '{title: "'.addslashes($v_news['title']).'", url: "'.$v_news['url'].'", cate: "'.$v_news['cate'].'",cate_url:"'.$v_news['category_url'].'"}';
		}
	}
}
$hotnews .= empty($hot_news) ? '' : implode(',', $hot_news);
$hotnews .= '],pic_news: [';
$hotnews .= empty($img_news) ? '' : implode(',', $img_news);
$hotnews .= '],news: [';
$hotnews .= empty($focus_news) ? '' : implode(',', $focus_news);
$hotnews .= ']});';
//echo $hotnews;exit;
file_put_contents($path.'huanqiunews.js', $hotnews, LOCK_EX);
exit;
