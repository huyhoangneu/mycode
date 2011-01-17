<?php
define('CONFIG_PATH', dirname(dirname(__FILE__)).'/spider/config');
include (CONFIG_PATH.'/qiushibaike.config.inc.php');
ini_set('pcre.backtrack_limit', '-1');
ini_set('default_socket_timeout','600');
$trans = array('糗百', '糗事');
$u = 'http://www.qiushibaike.com/groups/2/latest/page/';
for($i=1; $i <=10; $i++)
{
	$blocks = '';
	$contents = getHtmlContents($u.$i);
	preg_match_all("'<div\s+class=\"datetime\">(.*?)</div>'is", $contents, $block);
	$blocks = $block[1];
	foreach ($blocks AS $value)
	{
		preg_match("'<a\s+[^>]*>#(\d+)</a>'is", $value, $id);
		$urls[] = $id[1]."\n";
	}
}

//$urls = file('./urls_id.txt');
$urls_count = count($urls);
for($i = $urls_count; $i>0; $i--)
{
	$url_id = trim($urls[$i-1]);
//checkID($url_id);exit;
	if(!checkID($url_id))
	{
		$data = '';
		$url = 'http://www.qiushibaike.com/articles/'.$url_id.'.htm';
		//$url = 'http://www.qiushibaike.com/articles/199490.htm';
		//echo $url_id.' ---- '.$url."\n";
		//exit;
		$c = getHtmlContents($url);
		if(!$c) $c = getHtmlContents($url);
		//contents
		preg_match("'<div\s+class=\"qiushi_body\s+[^>]*>(.*?)</div>'is", $c, $c_block);
		//print_r($c_block);exit;
		$c_text = trim($c_block[1]);
		$data = '';
		preg_match("'(.*?)<p[^>]*>(.*?)</p>'is", $c_text, $tag);
		//print_r($tag);exit;
		if(!empty($tag))
		{
			$tag[1] = preg_replace("'<a[^>]*>.*?</a>'", "", $tag[1]);
			$data['content'] = strtr(addslashes($tag[1]), $trans);
			//$data['content'] = addslashes(htmlspecialchars($tag[1]));
			$data['tags'] = addslashes(preg_replace("/\r\n/i", " ", trim(strip_tags($tag[2]))));
			//$data['tags'] = addslashes(preg_replace("/\r\n/i", " ", trim(strip_tags($tag[2]))));
		}
		else
		{
			$c_text = preg_replace("'<a[^>]*>.*?</a>'", "", $c_text);
			$data['content'] = strtr(addslashes($c_text), $trans);
			//$data['content'] = addslashes(htmlspecialchars($c_text));
			$data['tags'] = '';
		}
		$data['state'] = 1;
		$data['create_datetime'] = time();
		$data['member_user'] = '';//'发布用户
		$data['comment_number'] = '';// '评论数',
		$data['offset'] = $url_id;
		$data = array_map('trim', $data);
		//print_r($data);exit;
		if($data['content'])
		{
			echo $url_id.' ---- '.$url."\n";
			//$data['content'] = preg_replace("'<a[^>]*>.*?</a>'", "", $data['content']);
			$mysql->autoExecute('embarrass_content', $data);
			$e_id = $mysql->instid();

			//echo $e_id;exit;
			//抓取 留言
			$contents = '';
			preg_match("'<div\s+id=\"qiushi_comments_\d+\"\s+class=\"qiushi_comments\">(.*?)</div>'is", $c, $article);
			preg_match_all("'<li[^>]*>(.*?)</li>'is", $article[1], $articles);
			foreach($articles[1] AS $key => $li)
			{
				preg_match("'<a[^>]*>(.*?)</a>'is", $li, $username);
				$contents[$key]['member_user'] = addslashes($username[1]);
				//$contents[$key]['member_user'] = addslashes(htmlspecialchars($username[1]));
				$contents[$key]['create_datetime'] = time();
				$contents[$key]['state'] = 1;
				preg_match("'<span[^>]*>(.*?)</span>'is", $li, $content);
				$contents[$key]['content'] = addslashes($content[1]);
				//$contents[$key]['content'] = addslashes(htmlspecialchars($content[1]));
				$contents[$key]['content_id'] = $e_id;
			}
			$count = count($contents);
			if(!empty($contents))
			{
				foreach( $contents AS $k => $v)
				{
					$mysql->autoExecute('embarrass_comment', $v);
				}
				$mysql->query("UPDATE  `wap`.`embarrass_content` SET  `comment_number` =  '".$count."' WHERE  `embarrass_content`.`id` =".$e_id." LIMIT 1");
			}
		}
		else
		{
			echo $url_id."\n";
		}
	}
}
echo 'aaa';exit;
function checkID($id)
{
	global $mysql;
	$one = $mysql->getRow("SELECT * FROM `embarrass_content` WHERE `offset` = ".$id." LIMIT 1");
	//print_r($one);exit;
	return $one;
}

function getHtmlContents($url)
{
	$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/20090729 Firefox/3.5.2';
	$data = '';
	if( !isset($ch) && $url)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_USERAGENT, $agent );
	}
	$data = curl_exec ( $ch );
	return $data;
}
