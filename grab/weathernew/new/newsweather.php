<?php
define('WPATH', dirname(__FILE__));
define('WEATHER_PATH', '/data/grab/data/weather/');
list($img_weath, ) = include_once ('ImagInfo.php');
include_once('utf8topy.class.php');
$py = new py_class();
$time_start = microtime_float();
$code = file(WPATH.'/code.txt');
foreach ($code as $code_key => $code_value) 
{
	$codeinfo = explode('|', $code_value);
	$codeinfo = str_replace(array('\r','\r\n'), '', $codeinfo);
	$cityname = trim($codeinfo['1']);
	$codeid = trim($codeinfo['0']);
	$flag = substr($codeid, 0, 3);
	//if ( $codeid != '101081202') continue;
	if ($flag != '101') 
	{
		$other_weather_info = getOtherInfo($codeid, $cityname);
		$weather_one = setTemplate($other_weather_info);
		$filename = $cityname;
	}
	else 
	{
		//continue;
		$p_cityname = trim($codeinfo['2']);//城市名称
		//if(isWeather($codeid) || $codeid == '101081000' )
		if(1)
		{
			$weather_info = getChinaInfo($codeid, $cityname, $p_cityname);
			if(empty($weather_info))
			{
				//$weather_one = error_weather($cityname, $p_cityname);
				$weather_one = '';
			}
			else
			{
				$weather_one = setTemplate($weather_info);
			}
		}	
		else
		{
			$weather_one = error_weather($cityname, $p_cityname);
		}
		if($p_cityname != $cityname)
		{
				$filename = $p_cityname.$cityname;
		}
		else
		{
				$filename = $cityname;
		}
	}

//echo $weather_one;exit;
	echo WEATHER_PATH.urlencode($filename)."\n";
	if($weather_one) file_put_contents(WEATHER_PATH.urlencode($filename) . '.js', $weather_one, LOCK_EX);
	echo $filename."\n";
}
$time_end = microtime_float();
echo $time = $time_end - $time_start;
exit;
function error_weather($cityname, $pcityname)
{
	global $py;
	if($cityname == $pcityname)
        {
                $p = $py->str2py($cityname);
                $n = $cityname;
        }
	else
	{
                $p = $py->str2py($pcityname).$py->str2py($cityname);
                $n = $pcityname.$cityname;
        }
	return 'var J1616_weather_info ={city:["'.$p.'", "'.$n.'"],info: {today: {气温: "",日期: "",图标: [],描述: []},next: [],index: [],pubdate:"'.releas_time().'"},simple:[]};if(typeof(w_callback)=="function")w_callback();';
	//return 'var J1616_weather_info ={city:["'.$p.'", "'.$n.'"],info: {today: {气温: "",日期: "",图标: [],描述: []},next: [{气温: "",日期: "",图标: [],描述: []}],index: [{标题: "", 概要: "", 详述: ""}],pubdate:"'.releas_time().'"}};';
}
/*
* 国外 天气
*
*
*/
function getOtherInfo($codeid, $cityname)
{
	global $py,$img_weath;
	$weatherInfos = array();
	$weatherInfos['city'] = $weatherInfos['today'] = $weatherInfos['next']= $weatherInfos['index'] = $weatherInfos['pubdate'] = array();
	$weatherInfos['city'] = array($py->str2py($cityname), $cityname);
	$contents = file_get_contents('http://www.weather.com.cn/html/weather/'.$codeid.'.shtml');
	preg_match("'<div\s+class=\"shouerWeatherBox\"[^>]*>(.*?)</div>'is", $contents, $other_tmp);
	if(empty($other_tmp[1]))
	{
		preg_match_all("'<div\s+class=\"fut_weatherbox\"[^>]*>(.*?)</div>'is", $contents, $other_tmp);
		foreach ( $other_tmp[1] AS $k1 => $v1)
		{
			preg_match_all("'<h4\s+[^>]*>(.*?)</h4>'is", $v1, $other_tmp2);
			$other_t1 = trim(strip_tags($other_tmp2[1][0]));//天气情况
			$other_t3 = trim(strip_tags($other_tmp2[1][3]));//风力情况
			$other_t2 = array('0' => $other_t1,'1' => strip_tags($other_tmp2[1][1])//高温
				, '2' => strip_tags($other_tmp2[1][2]), '3' => $other_t3);//低温
			$other_tmp3 = str_replace(array('高温：', '低温：'), '', $other_t2);
			if('暂无预报' != $other_t1)
			{
				$image = explode('转', $other_t1);
				if (1 == count($image)) 
				{
					$wimage = '"'.$img_weath[$image['0']].'"';
				}
				else 
				{
					$wimage = '"'.$img_weath[$image['0']].'","'.$img_weath[$image['1']].'"';
				}
				if ($k1 < 4) 
				{
					$weatherInfos['next'][] = array($other_tmp3['1'].'~'.$other_tmp3['2'],get_date($k1),$wimage, $other_tmp3['0'], '"'.$other_tmp3['3'].'"');
				}
				if($k1 == 0) $weatherInfos['today'] = array($other_tmp3['1'].'~'.$other_tmp3['2'],get_date(0), $wimage, $other_tmp3['3'], '"'.$other_tmp3['3'].'"');
				if(1 == $k1) $weatherInfos['simple'][] = '今天 '.$other_tmp3['0'].' '.$other_tmp3['1'].'~'.$other_tmp3['2'];
				if(2 == $k1) $weatherInfos['simple'][] = '明天 '.$other_tmp3['0'].' '.$other_tmp3['1'].'~'.$other_tmp3['2'];
				if(3 == $k1) $weatherInfos['simple'][] = '后天 '.$other_tmp3['0'].' '.$other_tmp3['1'].'~'.$other_tmp3['2'];
			}
			else 
			{
				$weatherInfos['next'][] = array();
			}
		}
	}
	else
	{
		preg_match_all("'<table[^>]*>(.*?)</table>'is", $other_tmp[1], $other_tmp1);
		//print_r($other_tmp1); exit;
		foreach ($other_tmp1['1'] as $k1 => $v1) 
		{
			preg_match_all("'<tr[^>]*>(.*?)</tr>'is", $v1, $other_tmp2);
			$other_t1 = trim(strip_tags($other_tmp2[1][2]));//天气情况
			$other_t3 = trim(strip_tags($other_tmp2[1][5]));//风力情况
			$other_t2 = array('0' => $other_t1,'1' => strip_tags($other_tmp2[1][3])//高温
				, '2' => strip_tags($other_tmp2[1][4]), '3' => $other_t3);//低温
			$other_tmp3 = str_replace(array('高温：', '低温：'), '', $other_t2);

			//print_r($other_t2);exit;
			//$other_tmp3 = $other_tmp2['1'];
			//$other_tmp3 = array_map('trim', $other_tmp3);
			//print_r($other_tmp3);exit;
			if('暂无预报' != $other_t1)
			{
				//$other_tmp3 = str_replace(array('高温：', '低温：'), '', $other_tmp3);
				$image = explode('转', $other_t1);
				if (1 == count($image)) 
				{
					$wimage = '"'.$img_weath[$image['0']].'"';
				}
				else 
				{
					$wimage = '"'.$img_weath[$image['0']].'","'.$img_weath[$image['1']].'"';
				}
				if ($k1 < 4) 
				{
					$weatherInfos['next'][] = array($other_tmp3['1'].'~'.$other_tmp3['2'],get_date($k1),$wimage, $other_tmp3['0'], '"'.$other_tmp3['3'].'"');
				}
				if($k1 == 0) $weatherInfos['today'] = array($other_tmp3['1'].'~'.$other_tmp3['2'],get_date(0), $wimage, $other_tmp3['3'], '"'.$other_tmp3['3'].'"');
				if(1 == $k1) $weatherInfos['simple'][] = '今天 '.$other_tmp3['0'].' '.$other_tmp3['1'].'~'.$other_tmp3['2'];
				if(2 == $k1) $weatherInfos['simple'][] = '明天 '.$other_tmp3['0'].' '.$other_tmp3['1'].'~'.$other_tmp3['2'];
				if(3 == $k1) $weatherInfos['simple'][] = '后天 '.$other_tmp3['0'].' '.$other_tmp3['1'].'~'.$other_tmp3['2'];
			}
			else 
			{
				$weatherInfos['next'][] = array();
			}
		}
	}
	$weatherInfos['pubdate'] = releas_time();
	//print_r($weatherInfos);exit;
	return $weatherInfos;
}
//$other_c = getOtherInfo('322010100', '塔那那利佛');
//echo setTemplate($other_c);
//exit;
/* 
* 无天气情况 的判断
*/
function isWeather($codeid)
{
	// 无天气情况 的判断 ********
	//http://www.weather.com.cn/data/sk/101340801.html 用于 判断是有天气
	$contents = file_get_contents('http://www.weather.com.cn/data/sk/'.$codeid.'.html');
	$c = json_decode($contents, true);
	return !empty($c['weatherinfo']['temp']);
	//print_r( );
}
//echo isWeather('101010100');exit;
//国内天气
function getChinaInfo($codeid, $cityname, $pcityname)
{
	global $py,$img_weath;	
	//2009-09-11 08:00
	if($codeid == '101250105')
	{
		$codeid = '101250104';
	}
	
	$contents = file_get_contents('http://www.weather.com.cn/html/weather/'.$codeid.'.shtml');
	if(!preg_match("'title'is", $contents))
	{
		//$contents = file_get_contents('http://www.weather.com.cn/html/weather/101090101.shtml');
		return ;
	}
	//$contents = file_get_contents('101.htm');
	$weatchInfos = $weatchInfo = array();
	//$cityname = '北京';
	if( $cityname == $pcityname)
	{
		$weatherInfos['city'] = array($py->str2py($cityname), $cityname);
	}
	else
	{
		$weatherInfos['city'] = array($py->str2py($pcityname).$py->str2py($cityname), $pcityname.$cityname);
	}
	//http://www.weather.com.cn/data/sk/101010100.html //事实天气情况
	preg_match_all("'<ul\s+class=\"content\d+\"[^>]*>(.*?)</ul>'is", $contents, $blocks);
	$count = count($blocks['1']);
	if(!$count)
	{
		preg_match_all("'<table\s+class=\"yuBaoTable\"[^>]*>(.*?)</table>'is", $contents, $blocks_other);
		if(empty($blocks_other[1])) return ;
		foreach ($blocks_other[1] as $key_other => $key_v) 
		{
			preg_match_all("'<tr>(.*?)</tr>'is", $key_v, $key1_v);
			$w_other = '';
			foreach ($key1_v[1] as $key2_other => $key2_v) 
			{
				preg_match_all("'<td[^>]*>(.*?)</td>'is", $key2_v, $key3_v);
				if($key2_other == 0 && $key_other == 0) 
				{//有可能是空
				}
				unset($key3_v[0]);
				if($key2_other == 0)
				{//高
					$w_other[$key2_other][] = trim(strip_tags($key3_v[1][3]));
					preg_match("'<a[^>]*>(.*?)</a>'is", $key3_v[1][4], $h_gaowen);
					//print_r($h_gaowen);exit;
					$temper = str_replace(array('高温','低温','&nbsp;'), '', strip_tags($h_gaowen[1]));
					$w_other[$key2_other][] = trim($temper);
					preg_match("'<a[^>]*>(.*?)</a>'is", $key3_v[1][5], $feng);//风向 情况
					$f = str_replace('无持续风向', '', $feng[1]);
					$w_other[$key2_other][] = trim($f);
					preg_match("'<a[^>]*>(.*?)</a>'is", $key3_v[1][6], $fengli);//风力 情况
					$w_other[$key2_other][] = trim($fengli[1]);
				}
				else 
				{
					$w_other[$key2_other][] = trim(strip_tags($key3_v[1][2]));
					preg_match("'<a[^>]*>(.*?)</a>'is", $key3_v[1][3], $h_gaowen);
					$temper = str_replace(array('高温','低温','&nbsp;'), '', strip_tags($h_gaowen[1]));
					$w_other[$key2_other][] = trim($temper);
					preg_match("'<a[^>]*>(.*?)</a>'is", $key3_v[1][4], $feng);//风向 情况
					$f = str_replace('无持续风向', '', $feng[1]);
					$w_other[$key2_other][] = trim($f);
					preg_match("'<a[^>]*>(.*?)</a>'is", $key3_v[1][5], $fengli);//风力 情况
					$w_other[$key2_other][] = trim($fengli[1]);

				}
				//print_r($key3_v);
			}
			$w_all_other[$key_other] = $w_other;
		}
		//print_r($w_all_other);exit;
		foreach ($w_all_other as $key_all => $value_all) 
		{//echo $value_all[1][0];
			//print_r($value_all);exit;
			//天气描述  晴 多雨 等等
			//echo  $value_all[1][0];exit;
			if(isset($value_all[1][0]))
			{
				if ($value_all[0][0] == $value_all[1][0]) 
				{
					$w = $value_all[0]['0'];
					$wimage = '"'.$img_weath[$w].'"';//天气 图片
				}
				else 
				{
					//
					$w = $value_all[0]['0'].'转'.$value_all['1'][0];
					$wimage = '"'.$img_weath[$value_all['0'][0]].'","'.$img_weath[$value_all['1'][0]].'"';
				}
			}
			else 
			{
				$w = $value_all[0]['0'];
				$wimage = '"'.$img_weath[$w].'"';//天气 图片
			}
			//气温 整理
			if(isset($value_all[1][1]))
			{
				if($value_all[0][1] == $value_all[1][1] )
				{
					//低温->高温
					$t = strip_tags($value_all[0][1]);
				}
				else 
				{
					$t = $value_all[0][1].'~'.$value_all[1][1].' ';
				}
			}
			else 
			{
				$t = $value_all[0][1].'';
			}

			if(isset($value_all[1][2]))
			{
				//风向
				if( $value_all[0][2] == $value_all[1][2] )
				{
					//风力
					if ($value_all[0][3] == $value_all[1][3]) 
					{
						$wind = '"'.$value_all[0][2].$value_all[0][3].'"';
					}
					else 
					{
						$wind = '"'.$value_all[0][2].$value_all[0][3].'转'.$value_all[1][3].'"';
					}
				}
				else 
				{
					if ($value_all[0][3] == $value_all[1][3]) 
					{
						$wind = '"'.$value_all[0][2].$value_all[0][3].'转'.$value_all[1][2].'"';
					}
					else 
					{
						$wind = '"'.$value_all[0][2].$value_all[0][3].'转'.$value_all[1][2].$value_all[1][3].'"';
					}
				}//可能 有其他情况 暂时 不记得了
			}
			else 
			{
				$wind = '"'.$value_all[0][2].$value_all[0][3].'"';
			}
			
			if (0 == $key_all) 
			{
				$weatherInfos['today'] = array($t,get_date($key_all),$wimage, $w,$wind);
				$weatherInfos['simple'][] = '今天 '.$wind.' '.$t;
			}
			else 
			{
				if(1 == $key_all) { $weatherInfos['simple'][] = '明天 '.$wind.' '.$t;}
				if(2 == $key_all) {$weatherInfos['simple'][] = '后天 '.$wind.' '.$t;}
				if($key_all <=7) $weatherInfos['next'][] = array($t,get_date($key_all),$wimage, $w,$wind);
			}
		}
		//print_r($weatherInfos);exit;
		//生活指数
		preg_match("'<div\s+class=\"todayLiving\"[^>]*>(.*?)</div>'is", $contents, $lifes);
		preg_match_all("'<dd[^>]*>(.*?)</dd>'is", $lifes[1], $life);
		foreach ($life[1] as $key_life => $value_life) 
		{
			preg_match("'<a[^>]*>(.*?)</a>'si", $value_life, $block_title);
			$tmp_life2 = strip_tags($block_title[1]);
			preg_match("'<blockquote>(.*?)</blockquote>'is", $value_life, $f);
			//print_r($tmp_life);exit;
			$index = explode('：', strip_tags($tmp_life2));//&
			$index = array_map('trim', $index);
			$m = trim($index['0']);
			
			if ('穿衣指数' == $m || '舒适度指数' == $m || '紫外线指数' == $m || '空气污染扩散条件指数' == $m) 
			{
				$weatherInfos['index'][] = array($index['0'], $index['1'], trim($f['1']));
			}
		}
		$weatherInfos['pubdate'] = releas_time();
		return $weatherInfos;
		//print_r($weatherInfos);exit;
		//处理 weather 新的 模板
	}

	$j = 0;
	$weatherInfo = array();
	for($i =0; $i < $count; $i++)
	{
		if($i%5 == 0)
		{
			$j ++;
			//echo $j."<br \>";
		}
		$weatherInfo[$j][] =  $blocks['1'][$i];
	}
	/************ 无天气情况 的判断 ********/
	//http://www.weather.com.cn/data/sk/101340801.html 用于 判断是有天气
	foreach ($weatherInfo as $k => $oneDay) 
	{
		//preg_match_all("'<a\s+[^>]*>(.*?)</a>'is",$oneDay['0'], $info);
		//
		preg_match_all("'<a\s+[^>]*>(.*?)</a>'is",$oneDay['1'], $info);
		$winfo = $info['1'];//天气描述  晴 多雨 等等
		$winfo = array_map('trim', $winfo);
		if(empty($winfo['0']))
		{
			return '';
		}
		//$winfo = $info['1'];//天气描述  晴 多雨 等等
		
		if (1 == count($winfo) || ($winfo['0'] == $winfo['1'])) 
		{
			$w = $winfo['0'];
			$wimage = '"'.$img_weath[$w].'"';//天气 图片
		}
		else 
		{
			//
			$w = $winfo['0'].'转'.$winfo['1'];
			$wimage = '"'.$img_weath[$winfo['0']].'","'.$img_weath[$winfo['1']].'"';//$img_weath[$w];
		}
		//print_r($winfo);exit;
		//<a href="/static/html/knowledge/20090615/5701.shtml" target="_blank">高温25℃</a>
		preg_match_all("'<strong\s*>(.*?)</strong>'is", $oneDay['2'], $temperature);//温度  ℃
		//preg_match_all("'<a[^>]*>(.*?)</a>'is", $oneDay['2'], $temperature);//温度  ℃
		$temper = str_replace(array('高温','低温','&nbsp;'), '',$temperature['1']);
		if(1 == count($temper))
		{
			//低温->高温
			//$t = strip_tags($temper['0']);
			$t = strip_tags($temper['0']).'℃';
			//$wimage =
		}
		else 
		{
			//$t = strip_tags($temper['0']).'~'.strip_tags($temper['1']);
			//$t = strip_tags($temper['0']).'℃~'.strip_tags($temper['11']).'℃';
			$t = strip_tags($temper['1']).'℃~'.strip_tags($temper['0']).'℃';
		}
		preg_match_all("'<a[^>]*>(.*?)</a>'is", $oneDay['3'], $tmp2);//风向
		$direction = $tmp2['1'];
		//print_r($direction);exit;
		preg_match_all("'<a[^>]*>(.*?)</a>'is", $oneDay['4'], $tmp3);//风力
		$power = $tmp3['1'];
		$direction = str_replace('无持续风向', '', $direction);
		if ( 1 == count($direction)  || ($direction['0'] == $direction['1']) ) 
		//if ( (1 == count($direction) && '无持续风向' == $direction['0']) || (($direction['0'] == $direction['1']) && ($direction['0'] == '无持续风向') )) 
		{
			//$direction = str_replace('无持续风向', '', $direction);
			if (1 == count($power) || ($power['0'] == $power['1']) ) 
			{
				if( '微风' == $power['0'])
				{
					$wind = '"'.$power['0'].'"';
				}
				else
				{
					$wind = '"'.$direction['0'].$power['0'].'"';
				}
			}
			else 
			{
				if( '微风' == $power['0'] )
				{
				$wind = '"'.$power['0'].'转'.$direction['0'].$power['1'].'"';
				}
				else
				{
				$wind = '"'.$direction['0'].$power['0'].'转'.$power['1'].'"';
				}
				//$wind = '"'.$power['0'].'转'.$power['1'].'"';
			}
		}
		else 
		{
			//
			//$direction = str_replace('无持续风向', '', $direction);
			if($direction['0'] == $direction['1'])
			{
				if( '微风' == $power['0'])
				{
					$wind = '"'.$power['0'].'"';
				}
				else
				{
					if( '微风' == $power['0'])
					{
					$wind = '"'.$power['0'].'转'.$direction['0'].$power['1'].'"';
					}
					else
					{
					$wind = '"'.$direction['0'].$power['0'].'转'.$power['1'].'"';
					}
				}
			}
			else
			{
				if( '微风' == $power['0'])
				{
					$wind = '"'.$power['0'].'"';
				}
				else
				{
					$wind = '"'.str_replace('微风','', $direction['0']).$power['0'].'转'.$direction['1'].$power['1'].'"';
				}
			}
		}


		if (1 == $k) 
		{
			//$weatherInfos['today'] = array($t['0']."℃~".$t['1']."℃","4月14日 星期二",array("a18.gif","a0.gif"),array(,$wind));
			$weatherInfos['today'] = array($t,get_date($k-1),$wimage, $w,$wind);
			$weatherInfos['simple'][] = '今天 '.$wind.' '.$t;
		}
		else 
		{
			if(2 == $k) { $weatherInfos['simple'][] = '明天 '.$wind.' '.$t;}
			if(3 == $k) {$weatherInfos['simple'][] = '后天 '.$wind.' '.$t;}
			if($k <=7) $weatherInfos['next'][] = array($t,get_date($k-1),$wimage, $w,$wind);
		}
	}
	//生活指数
	preg_match_all("'<div\s+class=\"box_lifeboxin\"[^>]*>(.*?)</div>'is", $contents, $lifes);
	foreach ($lifes['1'] as $lk => $life) 
	{
		preg_match("'<dd>(.*?)</dd>'is", $life, $tmp_life);
		preg_match("'<a[^>]*>(.*?)</a>'is",$tmp_life['1'],$tmp_life2);
		//print_r($tmp_life);exit;
		$index = explode('：', strip_tags($tmp_life2['1']));
		$f = explode('<br />', $tmp_life['1']);
		//print_r($f);exit;
		//穿衣指数 舒适度指数 紫外线强度指数 空气污染扩散条件指数
		$m = trim($index['0']);
		//$weatherInfos['index'][] = array($index['0'], $index['1'], $f['1']);
		if ('穿衣指数' == $m || '舒适度指数' == $m || '紫外线强度' == $m || '空气污染扩散条件指数' == $m) 
		{
			$weatherInfos['index'][] = array($index['0'], $index['1'], $f['1']);
		}
	}
	//print_r($weatherInfos);exit;
	/*发布时间*/
	$weatherInfos['pubdate'] = releas_time();
	return $weatherInfos;
}

//$i =  getChinaInfo($codeid, $cityname);
//print_r( getChinaInfo($codeid, $cityname) );exit;
//echo setTemplate($weatherInfos);
//file_put_contents(urlencode($cityname) . '.js', setTemplate($i), LOCK_EX);
//exit;
/**
 *	生成 天气的 js数组
 *	@params array $data 天气
 *	@return string
 */	
function setTemplate($data = array())
{
	$cityWeathInfo = $cityWeathInfos = $next_inde = '';
	$city = $data['city'];
	//$info = $data['info'];
	$today = $data['today'];
	$next = $data['next'];
	$index = $data['index'];
	$pubdate = $data['pubdate'];
	$simple = $data['simple'];
	$index_weath = array();
	$cityWeathInfo .= 
		'var J1616_weather_info ={'.
		'city:["'.$city['0'].'", "'.$city['1'].'"],'.
		'info: {'.
			'today: {';
	if(!empty($today))
	{
				$cityWeathInfo .= '气温: "'.$today['0'].'",日期: "'.$today['1'].'",图标: ['.$today['2'].'],描述: ["'.$today['3'].'",'.$today['4'].']';
	}
	else 
	{
		$cityWeathInfo .= '气温: "",日期: "",图标: [],描述: []';
	}
	$cityWeathInfo .= '},next: [';
	if(!empty($next) && is_array($next) )
	{	
		foreach ($next as $k => $v) 
		{
			//print_r($v);exit;
			if (!empty($v)) 
			{
				$next_inde .= '{气温:"'.$v['0'].'",日期:"'.$v['1'].'",图标:['.$v['2'].'],描述: ["'.$v['3'].'", '.$v['4'].']},';
			}
			else 
			{
				//$next_inde .= '{气温: "",日期: "",图标: [],描述: []},';
				$next_inde .= '';
			}
		}
	}
	else
	{
		$next_inde .= '';
	}
	if(!empty($next_inde))  $cityWeathInfo .= substr($next_inde, 0, -1);

	$cityWeathInfo .= '],'.
        'index: [';
	if(is_array($index) && !empty($index))
	{
		foreach ($index as $index_key => $index_value) 
		{
			if (!empty($index_value)) 
			{
				$cityWeathInfos .= '{标题: "'.$index_value['0'].'", 概要: "'.$index_value['1'].'", 详述: "'.$index_value['2'].'"},';
			}
			else 
			{
				$cityWeathInfos .= '';
				//$cityWeathInfos .= '{标题: "", 概要: "", 详述: ""},';
			}
		}
	}
	else 
	{
		$cityWeathInfos .= '';
	}
	if(!empty($cityWeathInfos))  $cityWeathInfo .= substr($cityWeathInfos, 0, -1);
        $cityWeathInfo .= '],'.
        'pubdate:"'.$pubdate.'"},simple:["';
	if(!empty($simple))
	{
		$simple = str_replace(array('"'), '', $simple);
		$cityWeathInfo .= implode('","', $simple);
	}
	$cityWeathInfo .= '"]};if(typeof(w_callback)=="function")w_callback();';
        
	return $cityWeathInfo;
}
//生成js文件
//print_r($weatherInfos);exit;
//print_r($life_index);exit;
//print_r($lifes);exit;

//print_r($weatherInfo);
//exit;
//print_r($blocks);exit;
function get_date($n)
{
	$nextWeek = time() + (24 * 60 * 60)*$n;
	return date('n月j日', $nextWeek).' '.getWeek($n);
}
/**
 *	取得 星期
 *	@params int $data
 *	@return string 
 */		
function getWeek($data ='0')
{
	$week = date('N')+$data;
	switch ($week)
	{
		case '8':		
		case '1':
			return "星期一";
		break;
		case '9':
		case '2':
			return "星期二";
		break;
		case '3':
		case '10':
			return "星期三";
		break;
		case '4':
		case '11':
			return "星期四";
		break;
		case '5':
		case '12':
			return "星期五";
		break;
		case '6':
		case '13':
			return "星期六";
		break;
		case '14':
		case '7':
			return "星期日";
		break;
	}
}
// 获取 发布时间
function releas_time()
{
	//抓取天气发布时间
	$release_time = file_get_contents('http://www.weather.com.cn/data/cityinfo/101010100.html');
	$release_time = json_decode($release_time, true);
	return date('Y-m-d').' '.$release_time['weatherinfo']['ptime'];
}
function microtime_float()
{
        return microtime(true);
}
