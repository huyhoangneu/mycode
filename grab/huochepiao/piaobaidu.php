<?php
$base_url = 'http://news.qq.com/';
$url = 'http://news.qq.com/zt/2010/2010chunyun/';
//$url = 'http://news.baidu.com/z/2010newyear/';
$contents = getHtmlContents($url);
//兼容 baidu news 页面 变化
//<ul style="padding: 0pt 0pt 0pt 10px;"></ul>
preg_match("'<div\s+class=\"txtlist14\"[^>]*>(.*?)</div>'is", $contents, $block);
preg_match_all("'<a\s+target=\"_blank\" href=\"(.*?)\">(.*?)</a>'is", $block['1'], $blocks);

//print_r($blocks);exit;
if(!empty($blocks['0']))
{
		foreach($blocks['0'] AS $k => $v)
		{
				if($v)
				{
					$all[] = array('url' => $base_url.$blocks['1'][$k], 'title' => trim(iconv('gbk', 'utf-8', $blocks['2'][$k])));
				}
		}
		if(!empty($v))
		{
				$js = 'var righnews=[';
				foreach($all AS $k => $v)
				{
						$tmp[] = "['".$v['url']."', '".$v['title']."']";
				}
				$js .= implode(',', $tmp).'];';
				//echo $js;exit;
				file_put_contents('/data/grab/data/huochepiao/righnews.js', $js);
				print_r($all);exit;
				print_r($blocks['1']);exit;
		}
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
