#!/data/opt/php/bin/php
<?php
//set_include_path('./rss/');
define('NEWS_PATH', '/data/grab/data/news');
ini_set('default_socket_timeout','600');
include 'lastRSS0.9.1.php';
$rss_xml = array(
	'gn' => 'http://news.baidu.com/n?cmd=1&class=civilnews&tn=rss&sub=0', //国内焦点
	'gj' => 'http://news.baidu.com/n?cmd=1&class=internews&tn=rss&sub=0', //国际焦点
	'cj' => 'http://news.baidu.com/n?cmd=1&class=finannews&tn=rss&sub=0', //财经新闻
	'ty' => 'http://news.baidu.com/n?cmd=1&class=sportnews&tn=rss&sub=0', //体育新闻
	'yl' => 'http://news.baidu.com/n?cmd=1&class=enternews&tn=rss&sub=0', //娱乐新闻GBK
	'kj' => 'http://news.baidu.com/n?cmd=1&class=technnews&tn=rss&sub=0', //科技新闻
	'sh' => 'http://news.baidu.com/n?cmd=1&class=socianews&tn=rss&sub=0',  //社会
);
$pic = array(
        'gn' => 'http://www.xinhuanet.com/photo/gn.htm', //国内焦点dgb
        'gj' => 'http://www.xinhuanet.com/photo/gj.htm', //国际焦点utf-8
        'ty' => 'http://www.xinhuanet.com/photo/ty.htm', //体育新闻utf-8
        'yl' => 'http://www.xinhuanet.com/photo/wy.htm', //娱乐新闻GBK
        'kj' => 'http://www.xinhuanet.com/photo/kj.htm', //科技新闻utf-8
        'sh' => 'http://www.xinhuanet.com/photo/jzxs.htm',  //社会
);
$dics = array(
	'gn' => '国内',
        'gj' => '国际',
        'cj' => '财经',
        'ty' => '体育',
        'yl' => '娱乐',
        'kj' => '科技',
        'sh' => '社会'
);
cms_mkdir(date('Y/md', time()).'/img');
$path = NEWS_PATH.'/'.date('Y/md', time());
//if(!is_dir($path)) mkdir($path); 
$rss = new lastRSS;
$rss->CDATA = 'content';
//cms_mkdir(date('Y/md', time()).'/img');
foreach($rss_xml as $name => $url)
{
	$def = $rss->Get($url);	
	//开始解析
	if (!empty($def)) 
	{
		foreach ($def['items'] as  $id => $k)
		{
			if( $id < 20)
			{
				$title = iconv("GBK", "UTF-8", $k['title']);
				//$date = substr($k['pubDate'], 17, 5);
				//$date = Date('Y-m-d H:i:s',strtotime($k['pubDate']));
				$date = strtotime($k['pubDate']);
				$textList[$name][]=array('title' => addslashes($title),'url' => $k['link'], 'date' => $date, 'c' => $dics[$name], 'cy' => $name);
			}
		}
	}		
	//开始输出
}
$shouye = $rss->Get('http://www.people.com.cn/rss/politics.xml');
foreach ($shouye['items'] AS $id => $k)
{
        $item[] = array('title' => $k['title'],'des' => trim($k['title']),'link' => $k['link']);
}
$d = json_encode($item);
file_put_contents('/data/grab/data/news/huanqiu.js',$d);
foreach ($pic AS $pname => $purl)
{
	$contents = file_get_contents($purl);
	preg_match("'<tr bgcolor=\'\#EEEEEE\' align=\'center\' valign=\'top\'>(.*?)</tr>'is", $contents, $tmp);
	preg_match_all("'<a\s*href=(.*?)\s*target=\'_blank\'[^>]*>\s*<img\s*src=(.*?)\s*border[^>]*>(.*?)</a>'is", $tmp['1'], $tmp2);
	foreach($tmp2['0'] AS $k => $v)
	{
		//
		if( $k < 3)
		{
			$pic_content = file_get_contents('http://www.xinhuanet.com/photo/'.$tmp2['2'][$k]);
			file_put_contents($path.'/img/'.$tmp2['2'][$k], $pic_content);
			$title = iconv('gb2312', 'UTF-8', trim(str_replace('<br>', '', $tmp2['3'][$k])));
			$imgList[$pname][$k] = array(addslashes($title), $tmp2['1'][$k], '/img/'.$tmp2['2'][$k]);
		}
	}
	//$imgList[$pname] = array(
}

//hotwords
$hoturl = 'http://news.baidu.com/';
$hotcontents = file_get_contents($hoturl);
//preg_match("'<dl\s*style=\"border:1px\s*solid\s*\#B6C7DB;\"\s*id=\"hotwords\"[^>]*>(.*?)</dl>'is", $hotcontents, $hot);
preg_match("'<dl\s*style=\"border\:1px\s*solid\s*\#B6C7DB\;\"\s*class=\"hotwords\">(.*?)</dl>'is", $hotcontents, $hot);
preg_match("'<dd[^>]*>(.*?)</dd>'is", $hot['1'], $hot2);
preg_match_all("'<a\s*href=\"(.*?)\"[^>]*>(.*?)</a>'is", $hot2['1'], $hot3);
foreach( $hot3['0'] AS $hkey => $kname)
{
	//
	if(!empty($hot3['2'][$hkey]))
	{
	$title = iconv('gb2312', 'UTF-8', trim($hot3['2'][$hkey]));
	$hotKey[] = array($title, $hot3['1'][$hkey]); 
	}
}
$hotwords = 'hotKey: [';
foreach ( $hotKey AS $hotkey => $hotname)
{
	$hotinfo[] = '["'.$hotname['0'].'", "'.$hotname['1'].'"]';
}
$hotwords .= implode(',', $hotinfo);
$hotwords .= ']';
$textall = array();
$imgall = array();

foreach ($dics AS $dkey => $dname)
{
	$news = 'var J1616_news_data = {';
	$news .= 'textList: [';
	if(!empty($textList[$dkey]))
	{
		//
		$textinfo = $textinfo1 = array();
		$textList[$dkey] = sortByMultiCols($textList[$dkey], array('date' => SORT_DESC));
		foreach($textList[$dkey] AS $tkey => $tname)
		{
        		$textinfo[] = '["'.$tname['title'].'", "'.$tname['url'].'", "'.date('H:i', $tname['date']).'",[]]';
        		//$textinfo1[] = '["'.$tname['0'].'", "'.$tname['1'].'", "'.$tname['2'].'", ["'.$tname['3'].'","'.$tname['4'].'"]]';
        		$allinfo[] = array('name' => $tname['title'], 'url' => $tname['url'], 'date' => $tname['date'], 'c' => $tname['c'],'cy' => $tname['cy']);
		}
		$news .= implode(',', $textinfo);	
		//$textall[] = implode(',', $textinfo1);
	}
	$news .= '],';
        $news .= 'imgList: [';
	if(!empty($imgList[$dkey]))
        {
                //
		$imginfo = array();
                foreach($imgList[$dkey] AS $ikey => $iname)
                {
                	$imginfo[] = '["'.$iname['0'].'", "'.$iname['1'].'", "'.$iname['2'].'"]';
                }
		$imgall[] = implode(',', $imginfo);
		$news .= implode(',', $imginfo);	
        }
	$news .= '],';
	
	$news .= $hotwords;	
	$news .= '};';
	file_put_contents($path.'/'.$dkey.'.js',$news);
}
//时间 排序
$allinfo = sortByMultiCols($allinfo, array('date' => SORT_DESC));
foreach ( $allinfo AS $akey => $avalue)
{
	if($akey < 20)
	{
        	$textall[] = '["'.$avalue['name'].'", "'.$avalue['url'].'", "'.date('H:i', $avalue['date']).'", ["'.$avalue['c'].'","'.$avalue['cy'].'"]]';
	}
}
//print_r($allinfo);exit;
//print_r($allinfo);
$newsall = 'var J1616_news_data = {';
$newsall .= 'textList: [';
$newsall .= implode(',', $textall);
$newsall .= '],';
$newsall .= 'imgList: [';
$newsall .= implode(',', $imgall);
$newsall .= '],';
$newsall .= $hotwords;
$newsall .= '};';
file_put_contents($path.'/qb.js',$newsall);
//print_r($imgList);exit;
/**
 * 逐一检测并创建每一级目录
 * @param string $dir_path 目录路径
 *
 * @return int $mod      目录权限
 */
function cms_mkdir($dir_path, $mod=0777)
{
    $cur_dir = NEWS_PATH.'/';
    if (is_dir($cur_dir . $dir_path))
    {
        return true;
    }
    $arr_path = explode('/', $dir_path);
    foreach ($arr_path as $val)
    {
        if (!empty($val))
        {
            $cur_dir .= $val;

            if (is_dir($cur_dir))
            {
                $cur_dir .= '/';
                continue;
            }
            if (@mkdir($cur_dir, $mod))
            {
                $cur_dir .= '/';
                //fclose(fopen($cur_dir.'index.htm', 'w'));
            }
            else
            {
                return false;
            }
        }
    }
    return true;
}
/**
 * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * 用法：
 * @code php
 * $rows = sortByMultiCols($rows, array(
 *     'parent' => SORT_ASC, 
 *     'name' => SORT_DESC,
 * ));
 * @endcode
 *
 * @param array $rowset 要排序的数组
 * @param array $args 排序的键
 *
 * @return array 排序后的数组
 */
function sortByMultiCols($rowset, $args)
{
	$sortArray = array();
	$sortRule = '';
	foreach ($args as $sortField => $sortDir) 
	{
		foreach ($rowset as $offset => $row) 
		{
			$sortArray[$sortField][$offset] = $row[$sortField];
		}
		$sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
	}
	if (empty($sortArray) || empty($sortRule)) { return $rowset; }
	eval('array_multisort(' . $sortRule . '$rowset);');
	return $rowset;
}
