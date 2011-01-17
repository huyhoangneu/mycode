#!/data/opt/php/bin/php
<?php
//set_include_path('./rss/');
define('NEWS_PATH', '/data/grab/data/news');
ini_set('default_socket_timeout','600');
define('maxage', time());
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
        //'gn' => 'http://search.news.cn/mb/xinhua/picmore2/search/?styleurl=http://www.xinhuanet.com/photo/static/style/tupian3_css.css&nodeid=115480&nodetype=3&rp=20', //国内焦点dgb
        'gn' => 'http://search.news.cn/mb/xinhua/picmore2/search/?styleurl=http://www.xinhuanet.com/photo/static/style/tupian3_css.css&nodeid=115480&nodetype=3&rp=20',//国内焦点
        'gj' => 'http://search.news.cn/mb/xinhua/picmore2/search/?styleurl=http://www.xinhuanet.com/photo/static/style/tupian3_css.css&nodeid=115479&nodetype=3&rp=20', //国际焦点utf-8
        'ty' => 'http://search.news.cn/mb/xinhua/picmore2/search/?styleurl=http://www.xinhuanet.com/photo/static/style/tupian3_css.css&nodeid=115505&nodetype=3&rp=20', //体育新闻utf-8
        'yl' => 'http://search.news.cn/mb/xinhua/picmore2/search/?styleurl=http://www.xinhuanet.com/photo/static/style/tupian3_css.css&nodeid=115502&nodetype=3&rp=20', //娱乐新闻GBK
        'sh' => 'http://search.news.cn/mb/xinhua/picmore2/search/?styleurl=http://www.xinhuanet.com/photo/static/style/tupian3_css.css&nodeid=115508&nodetype=3&rp=20',  //社会
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
		'df_hen' => '河南',
		'df_sd' => '山东',
		'df_hun' => '湖南',
		'df_hub' => '湖北',
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
//illegality url
$illegality = array('qianlong.com');
//$textList = '';
foreach($rss_xml as $name => $url)
{
    $def = $rss->Get($url); 
    //开始解析
    if (!empty($def)) 
    {
        $t = 0;
        foreach ($def['items'] as  $id => $k)
        {
            if( $t < 20)
            {
                $title = iconv("GBK", "UTF-8", $k['title']);
                $date = strtotime($k['pubDate']);
                if(preg_match("'(\.dayoo\.com|\.qianlong\.com)'", $k['link'])) continue;
                if(preg_match("'搬家公司'", $title)) continue;
                $textList[$name][]=array('title' => addslashes($title),'url' => iconv('GBK', 'UTF-8', $k['link']), 'date' => $date, 'c' => $dics[$name], 'cy' => $name);
                $t++;
            }
        }
    }
    //开始输出
}
//print_r($textList);exit;
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
    $contents = str_replace(array("'"), array(''), $contents);
	if($pname == 'gn' ||$pname == 'gj' || $pname == 'cj' || $pname == 'ty' || $pname == 'yl' || $pname == 'sh' )//|| $pname == 'df') 
	{
		preg_match("'<div\s+class=\"main\">(.*?)</div>'is", $contents, $tmp);
        #$tmp[1] = str_replace("\r\n", "\n", $tmp[1]);
        #$tmp[1] = strtr($tmp[1], array("\n\r" => '',"\r" => ''));
        
		preg_match_all("'<li>.*?<img\s+src=\"(.*?)\"\s+/>.*?<a\s+href=(.*?)\s+target=_blank>(.*?)</a>.*?</li>'is", $tmp[1], $tmp2);
        //print_r($tmp2);
        #print_r($tmp2);exit;
       /* 
        <li> 
         
          
          <img src="http://misc.search.news.cn/image/image_sub1/2010/06/29/e228d32e-6df3-419a-b68c-2a773f92745a_0.jpg" /> 
           
            
            <a href=http://news.xinhuanet.com/photo/2010-06/29/c_12277464.htm target=_blank>河北习三内画博物馆开馆</a> 
            </li> 
             
              <li> 

        <li><img src="http://misc.search.news.cn/image/image_sub1/2010/06/24/319a69e4-f018-41dc-b0aa-c5db9230587c_0.jpg" />
        <a href=http://news.xinhuanet.com/photo/2010-06/24/c_12258889.htm target=_blank>[高清组图] 乌云压城“城欲摧”</a>
        </li>
		print_r($tmp2);exit;
		//$contents = file_get_contents($purl);
		preg_match("'<tr bgcolor=\'\#EEEEEE\' align=\'center\' valign=\'top\'>(.*?)</tr>'is", $contents, $tmp);
		preg_match_all("'<a\s*href=(.*?)\s*target=\'_blank\'[^>]*>\s*<img\s*src=(.*?)\s*border[^>]*>(.*?)</a>'is", $tmp['1'], $tmp2);
print_r($tmp1);exit;
*/
        $count_pic = 0;
		foreach($tmp2['0'] AS $k => $v)
		{
			//
			if( $count_pic < 3)
			{
				$pic_content = file_get_contents($tmp2['1'][$k]);
				$pic_name = '/img/new_pic_'.$pname.'_'.$k.'.png';
				if(!empty($pic_content))
				{
				    file_put_contents($path.$pic_name, $pic_content);
				    $title = $tmp2['3'][$k];
				    //$title = iconv('gb2312', 'UTF-8', trim(str_replace('<br>', '', $tmp2['3'][$k])));
				    $imgList[$pname][$k] = array(addslashes($title), $tmp2['2'][$k], $pic_name.'?v='.maxage);
                }
			}
            $count_pic ++;
		}
	}
	else 
	{
        $count_pic = 0;
		preg_match_all("'<tr\s+align=\"center\"\s+valign=\"top\">(.*?)</tr>'is", $contents, $df_pic);
		foreach ( $df_pic['1'] AS $key_df => $info_df)
		{
			if($count_pic < 3)
			{
				preg_match("'\/><a\s+href=\".*?\"\s+target=\"_blank\">(.*?)</a>\s+</td>'", $info_df, $info_title);
				if(empty($info_title[1])) 
				{
					preg_match("'<br\s+/>\s+<a href=\".*?\"\s+target=\"_blank\">(.*?)</a>'is", $info_df, $info_title);
				}
				//preg_match("'<a\s+href=.*?\s+class=\"bt01\"\s+target=\"_blank\">(.*?)</a>\s+</td>'", $info_df, $info_title);
				$title = $info_title['1'];
				preg_match("'<td\s+class=\"bt01\"><a\s+href=\"(.*?)\"\s+target=\"_blank\"><img\s+src=\"(.*?)\"\s+[^>]*></a>'", $info_df, $info_pic_url);
				$pic_url = $info_pic_url['2'];
				$title_url = $info_pic_url['1'];

				$pic_content = file_get_contents('http://www.xinhuanet.com/local/'.$pic_url);
				$pic_name = '/img/new_pic_'.$pname.'_'.$key_df.'.png';
				if(!empty($pic_content))
				{
				    file_put_contents($path.$pic_name, $pic_content);
				    $imgList[$pname][$key_df] = array(addslashes($title), $title_url, $pic_name.'?v='.maxage);
				}
				//$imgList[$pname][$key_df] = array(addslashes($title), $title_url, $pic_name.'?v='.maxage);
			}
            $count_pic++;
		}
	}
}
//hotwords
$hoturl = 'http://news.baidu.com/';
$hotcontents = file_get_contents($hoturl);
#<dl style="border:1px solid #B6C7DB;background-color:#F9F9F9;width:322px;"  class="hotwords"> 
preg_match("'<dl\s*style=\"border\:1px\s*solid\s*\#B6C7DB\;\"\s*class=\"hotwords\">(.*?)</dl>'is", $hotcontents, $hot);
if(empty($hot[1]))
{
preg_match("'<dl\s*style=\"border:1px\s*solid\s*#B6C7DB;background-color:#F9F9F9;width:322px;\"\s*class=\"hotwords\">(.*?)</dl>'is", $hotcontents, $hot);
}
#print_r($hot);exit;
preg_match("'<dd[^>]*>(.*?)</dd>'is", $hot['1'], $hot2);
preg_match_all("'<a\s*href=\"(.*?)\"[^>]*>(.*?)</a>'is", $hot2['1'], $hot3);
foreach( $hot3['0'] AS $hkey => $kname)
{
	//
	if(!empty($hot3['2'][$hkey]))
	{
	    $title = iconv('gb2312', 'UTF-8', trim($hot3['2'][$hkey]));
	    $hotKey[] = array(addslashes($title), $hot3['1'][$hkey]); 
	}
}
#print_r($hotKey); exit;
$hotwords = 'hotKey: [';
#$keyurl = 'http://www.baidu.com/s?ie=utf-8&wd={keywrod}&tn=16site_5_pg';
foreach ( $hotKey AS $hotkey => $hotname)
{
	$hotinfo[] = '["'.$hotname['0'].'", "http://www.baidu.com/s?ie=utf-8&wd='.urlencode($hotname['0']).'&tn=16site_5_pg"]';
}
#print_r($hotinfo);exit;
$hotwords .= implode(',', $hotinfo);
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
		$imgall[] = implode(',', $imginfo);
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
	echo $dname.' '. urlencode($dname)."\n";
}
//时间 排序
include_once '/data/grab/spider/db/config.inc.php';
$hotnews_path = '/data/grab/data/news/';
$dicstype = array(
        '国内' => 'gn',
        '国际' => 'gj',
        '财经' => 'cj',
        '体育' => 'ty',
        '娱乐' => 'yl',
        '科技' => 'kj',
        '社会' => 'sh'
        );  
$rs = $mysql->getAll("SELECT * FROM `hotnews2`");
foreach ($rs as $k => $v) 
{
    if( $k <= 1)
    {
        $textall[] = '["'.$v['title'].'", "'.$v['url'].'", "'.date('H:i').'", ["热点", "0"]]';
    }
    elseif( 2<= $k && $k <= 13) 
    {   
        $textall[] = '["'.$v['title'].'", "'.$v['url'].'", "'.date('H:i').'", ["'.$v['category'].'", "'.$dicstype[$v['category']].'"]]';
//        $focus[] = $v; 
    }   
}
$allinfo = sortByMultiCols($allinfo, array('date' => SORT_DESC));
foreach ( $allinfo AS $akey => $avalue)
{
        	$textall[] = '["'.$avalue['name'].'", "'.$avalue['url'].'", "'.date('H:i', $avalue['date']).'", ["'.$avalue['c'].'","'.$avalue['cy'].'"]]';
}
//print_r($allinfo);exit;
//print_r($allinfo);
//后台新闻加入全部新闻中
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
exit;
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


