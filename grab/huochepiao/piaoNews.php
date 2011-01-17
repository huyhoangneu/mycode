<?php
$url = 'http://news.qq.com/zt/2010/2010chunyun/';
$contents = getHtmlContents($url);
preg_match("'<div\s+class=\"piclist\"[^>]*>(.*?)</div></td>'is", $contents, $block);
//img src
preg_match_all("'<img\s+src=\"(.*?)\"[^>]*>'is", $block['1'], $src);
$srcs = $src['1'];
//url title
preg_match_all("'<div\s+class=\"name\"><a\s+href=\"(.*?)\">(.*?)</a></div>'is", $block['1'], $title);
$titles = $title['2'];
$urls = $title['1'];
if(!empty($titles) && !empty($urls))
{
		foreach($urls AS $k => $v)
		{
				if(!empty($v)) $data[] = array('title' => iconv('gbk', 'utf-8', $titles[$k]), 'url' => $urls[$k], 'img' => downImg($srcs[$k], 'news_'.$k));
		}
		$js = 'var news=[';
		foreach($data AS $k => $v)
		{
				if(!empty($v)) $tmp[] = "['".$v['url']."', './grab/huochepiao/img/".$v['img']."', '".$v['title']."']";
		}
		if(!empty($tmp)) 
		{
				$js .= implode(',', $tmp).'];';
				file_put_contents('/data/grab/data/huochepiao/footernews.js', $js);
				echo $js;
				exit;
		}
}
function downImg($url, $name)
{
	$path = '/data/grab/data/huochepiao/img/';
	$c = getHtmlContents($url);
	file_put_contents($path.$name.'.jpg', $c);
	return $name.'.jpg';
}

function getHtmlContents($url)
{
	$data = '';
	if( !isset($ch) && $url)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0' );
	}
	$data = curl_exec ( $ch );
	return $data;
}
