<?php
include('simple_html_dom.php');
function get_day($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame0_$starflag.html";
	$string = mb_convert_encoding(file_get_contents(strtolower($source_url)), 'UTF-8', 'GBK');
	$html = str_get_html($string);
	$info['date'] = $html->find('li[class=datea]', 0)->innertext;
	$block = $html->find('div[class=tab]');
	foreach ($block as $k => $v) 
	{
		if ($k < 4) 
		{
			$info['star'][$k] = array('title' => $v->find('h4', 0)->innertext, 'star' => count($v->find('img')));
		}
		elseif ($k >= 4 && $k < 9) 
		{
			$info['content'][$k] = array('title' => $v->find('h4', 0)->innertext, 'content' => $v->find('p', 0)->innertext);
		}
		elseif ($k == 9) 
		{
			$info['comment'] = trim(strip_tags($html->find('div[class=lotconts]', 0)->innertext));
		}
	}
	$html->clear();
	return  $info;
}

function get_week($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame1_$starflag.html";
	$string = mb_convert_encoding(file_get_contents(strtolower($source_url)), 'UTF-8', 'GBK');
	$html = str_get_html($string);
	$info['date'] = $html->find('li[class=date]', 0)->innertext;
	//teams
	$block = $html->find('div[class=right]');
	foreach ($block as $k => $v) 
	{
		if( 0 < $k && $k <= 5)
		{
			if($k == 2)
			{
			}
			else 
			{
				$info['info'][$k] = array('title' => $v->find('h4', 0)->plaintext, 'star' => count($v->find('img')), 'content' => strip_tags($v->find('p', 0)->innertext));
			}
		}
		elseif($k > 5) 
		{
			$tmp = explode('<br />', $v->find('p', 0)->innertext);
			$info['info'][$k] = array('title' => $v->find('h4', 0)->plaintext, 'date' => trim($tmp['0']), 'content' => strip_tags(trim($tmp['1'])));
		}
	}
	return  $info;
}

function get_month($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame2_$starflag.html";
	$string = mb_convert_encoding(file_get_contents(strtolower($source_url)), 'UTF-8', 'GBK');
	$html = str_get_html($string);
	$info['date'] = $html->find('li[class=date]', 0)->innertext;

	$block = $html->find('div[class=right]');
	foreach ($block as $k => $v) 
	{	
		if( 0 < $k && $k <= 3)
		{
			$contents = preg_replace("/<br \/>/", "\n", $v->find('p', 0)->innertext);
			$contents = preg_replace("/\s+/", "\n", $contents);
			$info['info'][$k] = array('title' => $v->find('h4', 0)->plaintext, 'star' => count($v->find('img')), 'content' => $contents);			
		}
		elseif( $k > 3)
		{
			$info['info'][$k] = array('title' => $v->find('h4', 0)->innertext, 'content' =>  strip_tags($v->find('p', 0)->innertext));
		}
	}
	return  $info;
}

function get_year($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame3_$starflag.html";
	$string = mb_convert_encoding(file_get_contents(strtolower($source_url)), 'UTF-8', 'GBK');
	$html = str_get_html($string);
	$info['date'] = $html->find('li[class=date]', 0)->innertext;
	$info['summary'] = $html->find('li[class=notes]', 0)->innertext;

	$block = $html->find('div[class=right]');
	foreach ($block as $k => $v) 
	{	
		if( 0 < $k )
		{
			$info['info'][$k] = array('title' => $v->find('h4', 0)->plaintext, 'star' => count($v->find('img')), 'content' => strip_tags($v->find('p', 0)->innertext));			
		}		
	}
	return  $info;
}

function get_love($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame4_$starflag.html";
	$string = mb_convert_encoding(file_get_contents(strtolower($source_url)), 'UTF-8', 'GBK');
	$html = str_get_html($string);
	$info['date'] = $html->find('li[class=date]', 0)->innertext;
	$contents = $html->find('div[class=lotconts]', 0)->innertext;
	$contents = preg_replace("/<br \/>/", "\n", $contents);
	$contents = preg_replace("/\s+/", "\n", $contents);
	$info['content'] = preg_replace("/^\n/", '', $contents);
	$girl = $html->find('div[class=m_left]', 0)->innertext;
	$info['girl'] = preg_replace("/^\n/", '', strip_tags($girl));
	$boy = $html->find('div[class=m_right]', 0)->innertext;
	$info['boy'] = preg_replace("/^\n/", '', strip_tags($boy));
	return  $info;
}

function read_templat($temp_file, $info)
{
	ob_start();
	include($temp_file);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function get_content($url) {
	$a = strtolower(file_get_contents($url));
	$a = mb_convert_encoding($a, 'UTF-8', 'GBK');
	return $a;
}
?>
