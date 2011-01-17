#!/data/opt/php/bin/php
<?php
//set_include_path('./rss/');
define('NEWS_PATH', '/data/wwwroot/news/grab/news');
ini_set('default_socket_timeout','600');
include 'lastRSS0.9.1.php';
$rss_xml = array(
	'gn' => 'http://news.baidu.com/n?cmd=1&class=civilnews&tn=rss&sub=0', //国内焦点
	'gj' => 'http://news.baidu.com/n?cmd=1&class=internews&tn=rss&sub=0', //国际焦点
	'cj' => 'http://news.baidu.com/n?cmd=1&class=finannews&tn=rss&sub=0', //财经新闻
	'ty' => 'http://news.baidu.com/n?cmd=1&class=sportnews&tn=rss&sub=0', //体育新闻
	'yl' => 'http://news.baidu.com/n?cmd=1&class=enternews&tn=rss&sub=0', //娱乐新闻GBK
	'sh' => 'http://news.baidu.com/n?cmd=1&class=socianews&tn=rss&sub=0',  //社会	
	'北京' => 'http://news.baidu.com/n?cmd=7&loc=0&name=%B1%B1%BE%A9&tn=rss',
	'上海' => 'http://news.baidu.com/n?cmd=7&loc=2354&name=%C9%CF%BA%A3&tn=rss',
	'天津' => 'http://news.baidu.com/n?cmd=7&loc=125&name=%CC%EC%BD%F2&tn=rss',
	'重庆' => 'http://news.baidu.com/n?cmd=7&loc=6425&name=%D6%D8%C7%EC&tn=rss',
	'广东' => 'http://news.baidu.com/n?cmd=7&loc=5495&name=%B9%E3%B6%AB&tn=rss',
	'河北' => 'http://news.baidu.com/n?cmd=7&loc=250&name=%BA%D3%B1%B1&tn=rss',
	'辽宁' => 'http://news.baidu.com/n?cmd=7&loc=1481&name=%C1%C9%C4%FE&tn=rss',
	'吉林' => 'http://news.baidu.com/n?cmd=7&loc=1783&name=%BC%AA%C1%D6&tn=rss',
	'甘肃' => 'http://news.baidu.com/n?cmd=7&loc=8534&name=%B8%CA%CB%E0&tn=rss',
	'山西' => 'http://news.baidu.com/n?cmd=7&loc=812&name=%C9%BD%CE%F7&tn=rss',
	'四川' => 'http://news.baidu.com/n?cmd=7&loc=6692&name=%CB%C4%B4%A8&tn=rss',
	'陕西' => 'http://news.baidu.com/n?cmd=7&loc=8205&name=%C9%C2%CE%F7&tn=rss',
	'河南' => 'http://news.baidu.com/n?cmd=7&loc=4371&name=%BA%D3%C4%CF&tn=rss',
	'山东' => 'http://news.baidu.com/n?cmd=7&loc=3996&name=%C9%BD%B6%AB&tn=rss',
	'湖南' => 'http://news.baidu.com/n?cmd=7&loc=5161&name=%BA%FE%C4%CF&tn=rss',
	'湖北' => 'http://news.baidu.com/n?cmd=7&loc=4811&name=%BA%FE%B1%B1&tn=rss',
	'江西' => 'http://news.baidu.com/n?cmd=7&loc=3636&name=%BD%AD%CE%F7&tn=rss',
	'江苏' => 'http://news.baidu.com/n?cmd=7&loc=2493&name=%BD%AD%CB%D5&tn=rss',
	'浙江' => 'http://news.baidu.com/n?cmd=7&loc=2809&name=%D5%E3%BD%AD&tn=rss',
	'安徽' => 'http://news.baidu.com/n?cmd=7&loc=3072&name=%B0%B2%BB%D5&tn=rss',
	'福建' => 'http://news.baidu.com/n?cmd=7&loc=3372&name=%B8%A3%BD%A8&tn=rss',
	'广西' => 'http://news.baidu.com/n?cmd=7&loc=5886&name=%B9%E3%CE%F7&tn=rss',
	'贵州' => 'http://news.baidu.com/n?cmd=7&loc=7230&name=%B9%F3%D6%DD&tn=rss',
	'海南' => 'http://news.baidu.com/n?cmd=7&loc=6245&name=%BA%A3%C4%CF&tn=rss',
	'云南' => 'http://news.baidu.com/n?cmd=7&loc=7527&name=%D4%C6%C4%CF&tn=rss',
	'内蒙古' => 'http://news.baidu.com/n?cmd=7&loc=1167&name=%C4%DA%C3%C9%B9%C5&tn=rss',
	'青海' => 'http://news.baidu.com/n?cmd=7&loc=8782&name=%C7%E0%BA%A3&tn=rss',
	'宁夏' => 'http://news.baidu.com/n?cmd=7&loc=8907&name=%C4%FE%CF%C4&tn=rss',
	'新疆' => 'http://news.baidu.com/n?cmd=7&loc=9001&name=%D0%C2%BD%AE&tn=rss',
	'西藏' => 'http://news.baidu.com/n?cmd=7&loc=7915&name=%CE%F7%B2%D8&tn=rss',
	'黑龙江' => 'http://news.baidu.com/n?cmd=7&loc=1967&name=%BA%DA%C1%FA%BD%AD&tn=rss',
	'香港' => 'http://news.baidu.com/n?cmd=7&loc=9337&name=%CF%E3%B8%DB&tn=rss',
	'澳门' => 'http://news.baidu.com/n?cmd=7&loc=9436&name=%B0%C4%C3%C5&tn=rss',
	'台湾' => 'http://news.baidu.com/n?cmd=7&loc=9442&name=%CC%A8%CD%E5&tn=rss'
);
$pic = array(
        'gn' => 'http://www.xinhuanet.com/photo/gn.htm', //国内焦点dgb
        'gj' => 'http://www.xinhuanet.com/photo/gj.htm', //国际焦点utf-8
        'ty' => 'http://www.xinhuanet.com/photo/ty.htm', //体育新闻utf-8
        'yl' => 'http://www.xinhuanet.com/photo/wy.htm', //娱乐新闻GBK
        'sh' => 'http://www.xinhuanet.com/photo/jzxs.htm',  //社会
        'df' => 'http://www.xinhuanet.com/local/left.htm', //地方新闻utf-8
);
$dics = array(
	'gn' => '国内',
        'gj' => '国际',
        'cj' => '财经',
        'ty' => '体育',
        'yl' => '娱乐',
        'sh' => '社会',
		'df_bj' => '北京',
		'df_sh' => '上海', 
		'df_tj' => '天津',
		'df_cq' => '重庆',
		'df_gd' => '广东',
		'df_hb' => '河北',
		'df_ln' => '辽宁',
		'df_jl' => '吉林',
		'df_gs' => '甘肃',
		'df_sx' => '山西', 
		'df_sc' => '四川',
		'df_sx2' => '陕西',
		'df_hn' => '河南',
		'df_sd' => '山东',
		'df_hn' => '湖南',
		'df_hb' => '湖北',
		'df_jn' => '江西',
		'df_js' => '江苏',
		'df_zj' => '浙江',
		'df_ah' => '安徽',
		'df_fj' => '福建',
		'df_gx' => '广西',
		'df_gz' => '贵州',
		'df_xg' => '香港',
		'df_am' => '澳门',
		'df_hainan' => '海南',
		'df_taiwan' => '台湾',
		'df_yunnan' => '云南',
		'df_nmg' => '内蒙古', 
		'df_qinghai' => '青海', 
		'df_ningxia' => '宁夏', 
		'df_xinjiang' => '新疆', 
		'df_xizhang' => '西藏', 
		'df_hlj' => '黑龙江'
);
cms_mkdir(date('Y/md', time()).'/img');
$path = NEWS_PATH.'/'.date('Y/md', time());
//if(!is_dir($path)) mkdir($path); 
$rss = new lastRSS;
$rss->CDATA = 'content';
//cms_mkdir(date('Y/md', time()).'/img');
$textList = '';
foreach($rss_xml as $name => $url)
{
	//if($pname == 'gn' ||$pname == 'gj' || $pname == 'cj' || $pname == 'ty' || $pname == 'yl' || $pname == 'sh') 
	//{
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
					if(preg_match("'dayoo\.com'", $k['link'])) continue;
					$textList[$name][]=array('title' => addslashes($title),'url' => $k['link'], 'date' => $date, 'c' => $dics[$name], 'cy' => $name);
				}
			}
		}
	//}
	//开始输出
}
if(empty($textList)) exit;else echo "f";
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
	if($pname == 'gn' ||$pname == 'gj' || $pname == 'cj' || $pname == 'ty' || $pname == 'yl' || $pname == 'sh' )//|| $pname == 'df') 
	{
		//$contents = file_get_contents($purl);
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
	}
	else 
	{
		preg_match_all("'<tr\s+align=\"center\"\s+valign=\"top\">(.*?)</tr>'", $contents, $df_pic);
		foreach ( $df_pic['1'] AS $key_df => $info_df)
		{
			if($key_df < 3)
			{
				preg_match("'<a\s+href=.*?\s+class=\"bt01\"\s+target=\"_blank\">(.*?)</a>\s+</td>'", $info_df, $info_title);
				$title = iconv('gbk', 'UTF-8', $info_title['1']);
				preg_match("'<td><a\s+href=(.*?)\s+target=\"_blank\"><img\s+src=(.*?)\s+[^>]*></a>'", $info_df, $info_pic_url);
				$pic_url = $info_pic_url['2'];
				$title_url = $info_pic_url['1'];

				$pic_content = file_get_contents('http://www.xinhuanet.com/local/'.$pic_url);
				file_put_contents($path.'/img/'.$pic_url, $pic_content);
				//$title = iconv('gb2312', 'UTF-8', trim(str_replace('<br>', '', $tmp2['3'][$k])));
				$imgList[$pname][$key_df] = array(addslashes($title), $title_url, '/img/'.$pic_url);

			}
		}
	}
	//$imgList[$pname] = array(
}
//hotwords
$hoturl = 'http://news.baidu.com/';
$hotcontents = file_get_contents($hoturl);
//preg_match("'<dl\s*style="border:1px solid #B6C7DB;"  class="hotwords tophotwords">'is", $hotcontents, $hot);
preg_match("'<dl\s*style=\"border:1px\s+solid\s+#B6C7DB;\"\s*class=\"hotwords\">(.*?)</dl>'is", $hotcontents, $hot);
preg_match("'<dd><div>(.*?)</div>'is", $hot[1], $hot2);
//print_r($hot2);exit;
//preg_match("'<dd[^>]*>(.*?)</dd>'is", $hot['1'], $hot2);
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
if(!empty($hotKey))
{
		foreach ( $hotKey AS $hotkey => $hotname)
		{
				$hotinfo[] = '["'.$hotname['0'].'", "'.$hotname['1'].'"]';
		}
		//print_r($hotinfo);exit;
		$hotwords .= implode(',', $hotinfo);
}
$hotwords .= ']';
$textall = array();
$imgall = array();

foreach ($dics AS $dkey => $dname)
{
	$news = 'var J1616_news_data = {';
	$news .= 'textList: [';
	//if(!empty($textList[$dkey]))
	//{
		//
		$textinfo = $textinfo1 = array();
		if($dkey == 'gn' ||$dkey == 'gj' || $dkey == 'cj' || $dkey == 'ty' || $dkey == 'yl' || $dkey == 'sh') 
		{
			$textList[$dkey] = sortByMultiCols($textList[$dkey], array('date' => SORT_DESC));
			foreach($textList[$dkey] AS $tkey => $tname)
			{
				$textinfo[] = '["'.$tname['title'].'", "'.$tname['url'].'", "'.date('H:i', $tname['date']).'",[]]';
				$allinfo[] = array('name' => $tname['title'], 'url' => $tname['url'], 'date' => $tname['date'], 'c' => $tname['c'],'cy' => $tname['cy']);
			}
		}
		else
		{
			$textList[$dname] = sortByMultiCols($textList[$dname], array('date' => SORT_DESC));
			if(empty($textList[$dname])) echo $dname;
			foreach($textList[$dname] AS $tkey => $tname)
			{
				$textinfo[] = '["'.$tname['title'].'", "'.$tname['url'].'", "'.date('H:i', $tname['date']).'",[]]';
			}
		}
		$news .= implode(',', $textinfo);	
		//$textall[] = implode(',', $textinfo1);
	//}
	$news .= '],';
    $news .= 'imgList: [';
	if($dkey == 'gn' ||$dkey == 'gj' || $dkey == 'cj' || $dkey == 'ty' || $dkey == 'yl' || $dkey == 'sh')
	{
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
	}
	else
	{
		$imginfo = array();
		foreach($imgList['df'] AS $ikey => $iname)
             	{       
                         $imginfo[] = '["'.$iname['0'].'", "'.$iname['1'].'", "'.$iname['2'].'"]';
                }
		//$imgall[] = implode(',', $imginfo);
		$news .= implode(',', $imginfo);	
	}
	$news .= '],';
	
	$news .= $hotwords;	
	$news .= '};';
	if($dkey == 'gn' ||$dkey == 'gj' || $dkey == 'cj' || $dkey == 'ty' || $dkey == 'yl' || $dkey == 'sh') 
	{
		file_put_contents($path.'/'.$dkey.'.js',$news);
	}
	else 
	{
		file_put_contents($path.'/'.urlencode($dname).'.js',$news);
	}
}
//时间 排序
$allinfo = sortByMultiCols($allinfo, array('date' => SORT_DESC));
foreach ( $allinfo AS $akey => $avalue)
{
	//if($akey < 20)
	//{
        	$textall[] = '["'.$avalue['name'].'", "'.$avalue['url'].'", "'.date('H:i', $avalue['date']).'", ["'.$avalue['c'].'","'.$avalue['cy'].'"]]';
	//}
}
//print_r($allinfo);exit;
//print_r($allinfo);
$newsall = 'var J1616_news_data = {';
$newsall .= 'textList: [';
$newsall .= implode(',', $textall);
$newsall .= '],';
$newsall .= 'imgList: [';
foreach($imgall AS $img_k => $img_v)
{
		if($img_k < 1) $tmp_array[] = $img_v;
}
$newsall .= implode(',', $tmp_array);
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

