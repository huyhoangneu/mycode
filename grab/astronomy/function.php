<?php
function get_day($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame0_$starflag.html";
	$a = get_content($source_url);
	preg_match("/<div class=\"lotstars\">(.+)<\/div>/is", $a, $b);
	preg_match("/<li class=\"datea\">(.+)<\/li>/is", $a, $adate);
	$info['date'] = $adate[1];
	$b = explode('<div class="tab">', $b[0]);
	for ($i = 1; $i < 11; $i++) {
		if ($i < 5) {
			preg_match("/<h4>(.*)<\/h4>/", $b[$i], $c);
			$title = $c[1];
			$number = substr_count($b[$i], 'star.gif');
			$info['star'][$i] = array('title' => $title, 'star' => $number);
		}
		if ($i >= 5 && $i < 10) {
			preg_match("/<h4>(.*)<\/h4>/", $b[$i], $c);
			$title = $c[1];
			preg_match("/<p>(.*)<\/p>/", $b[$i], $d);
			$content = trim($d[1]);
			$info['content'][$i] = array('title' => $title, 'content' => $content);
		}
		if ($i == 10) {
			preg_match("/<div class=\"lotconts\">(.+?)<\/div>/is", $a, $e);
			$info['comment'] = trim(strip_tags($e[1]));
		}
	}
	return  $info;
}

function get_week($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame1_$starflag.html";
	$a = get_content($source_url);
	preg_match("/<div class=\"lotstars\">(.+)<\/div>/is", $a, $b);
	preg_match("/<li class=\"date\">(.+)<\/li>/is", $a, $adate);
	$info['date'] = $adate[1];	
	$b = explode('<div class="right">', $b[0]);
	for ($i = 1; $i < count($b); $i++) {
		if ($i < 6) {
			if ($i == 2) {
				preg_match("/<h4>(.*)+?<\/h4>/", $b[$i], $c);
				$title = $c[1];
				$e = explode('<em>', $b[$i]);
				$contents[1] = preg_replace("/\n/", '', trim(strip_tags($e[1])));
				$contents[2] = preg_replace("/\n/", '', trim(strip_tags($e[2])));
				$numbers[1] = substr_count($e[1], 'star.gif');
				$numbers[2] = substr_count($e[2], 'star.gif');
				//$info[$i] = array('title' => $title, 'star' => $numbers, 'content' => $contents);
			}else {
				preg_match("/<h4>(.*)+?<\/h4>/", $b[$i], $c);
				preg_match("/<p>(.+?)<\/?p>/is", $b[$i], $d);
				$title = preg_replace("/<img(.*)/is", '', $c[1]);
				$number = substr_count($c[1], 'star.gif');
				$content = preg_replace("/\n/", '', strip_tags($d[1]));
				if ($title && $number && $content) {
					$info['info'][$i] = array('title' => $title, 'star' => $number, 'content' => $content);
				}
			}
		}
		else {
			preg_match("/<h4>(.*)+?<\/h4>/", $b[$i], $c);
			$title = trim($c[1]);
			preg_match("/<p>(.+?)<\/?p>/is", $b[$i], $d);
			$e = explode('<br />', $d[1]);
			$month = trim($e[0]);
			$content = trim($e[1]);
			$info['info'][$i] = array('title' => $title, 'date' => $month, 'content' => $content);
		}
	}
	return  $info;
}

function get_month($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame2_$starflag.html";
	$a = get_content($source_url);
	preg_match("/<div class=\"lotstars\">(.*)<\/div>/is", $a, $b);
	preg_match("/<li class=\"date\">(.+)<\/li>/is", $a, $adate);
	$info['date'] = $adate[1];		
	$b = explode('<div class="right">', $b[0]);
	for ($i = 1; $i < count($b); $i++) {
		if ($i < 4) {
			preg_match("/<h4>(.*)+?<\/h4>/", $b[$i], $c);
			preg_match("/<p>(.+?)(<\/?p>|\n)/is", $b[$i], $d);
			$title = preg_replace("/<img(.*)/is", '', $c[1]);
			$title = preg_replace("/\s+?/", '', $title);
			$number = substr_count($c[1], 'star.gif');
			$content = trim(preg_replace("/\n/", '', $d[1]));
			$content = strip_tags(preg_replace("/　　/", '', $content));
			if ($title && $number && $content) {
				$info['info'][$i] = array('title' => $title, 'star' => $number, 'content' => $content);
			}
		}else {
			preg_match("/<h4>(.*)+?<\/h4>/", $b[$i], $c);
			preg_match("/<p>(.+?)(<\/?p>|\n)/is", $b[$i], $d);
			$title = preg_replace("/<img(.*)/is", '', $c[1]);
			$content = preg_replace("/^(.*)\n+/", '', $d[1]);
			$content = strip_tags(preg_replace("/　　/", '', $content));
			$info['info'][$i] = array('title' => $title, 'content' => $content);
		}
	}
	return  $info;
}

function get_year($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame3_$starflag.html";
	$a = get_content($source_url);
	preg_match("/<div class=\"lotstars\">(.*)<\/div>/is", $a, $b);
	preg_match("/<li class=\"date\">(.+)<\/li>/is", $a, $adate);
	$info['date'] = $adate[1];
	preg_match("/<li class=\"notes\">(.+?)<\/li>/is", $a, $summary);
	$summary = $summary[1];
	$b = explode('<div class="right">', $b[0]);
	for ($i = 1; $i < count($b); $i++) {
		preg_match("/<h4>(.*)+?<\/h4>/", $b[$i], $c);
		preg_match("/<p>(.+?)(<\/?p>|\n)/is", $b[$i], $d);
		$title = preg_replace("/<img(.*)/is", '', $c[1]);
		$number = substr_count($c[1], 'star.gif');
		$content = preg_replace("/^(.*)\n+/", '', trim($d[1]));
			if ($title && $number && $content) {
				$info['info'][$i] = array('title' => $title, 'star' => $number, 'content' => $content);
			}
	}
	$info['summary'] = $summary;
	return  $info;
}

function get_love($starflag)
{
	$source_url = "http://astro.sina.com.cn/pc/west/frame4_$starflag.html";
	$a = get_content($source_url);
	preg_match("/<div class=\"lotconts\">(.+?)<\/div>/is", $a, $b);
	preg_match("/<li class=\"date\">(.+)<\/li>/is", $a, $adate);
	$info['date'] = $adate[1];	
	preg_match("/<div class=\"m_left\">(.+?)<\/div>/is", $a, $girl);
	preg_match("/<div class=\"m_right\">(.+?)<\/div>/is", $a, $boy);
	$c = preg_replace("/<br \/>/", "\n", $b[1]);
	$c = preg_replace("/\s+/", "\n", $c);
	$info['content'] = preg_replace("/^\n/", '', $c);
	$info['girl'] = preg_replace("/^\n/", '', strip_tags($girl[1]));
	$info['boy'] = preg_replace("/^\n/", '', strip_tags($boy[1]));
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
