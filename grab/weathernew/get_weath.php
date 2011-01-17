<?php
set_time_limit(1800);
//get cctv weath
include ('utf8topy.class.php');
include('getWeath.php');
define('TOGTIME',date('Y-m-d H'));

//生成数据的存放路径与日志存放的路径
//$weath_path = '/data/grab/data/weather/';
//$log_path = '/data/grab/log/';
//$weathImgInfo = include ('ImagInfo.php');
//list( , $weathImgInfo) = include ('ImagInfo.php');
//$cities = include ('city.php');
//$city_other = include ('city_other.php');
list($cities, $city_other) = $citys = include('city.php');
//测试源
$source = array(
	'get_cctv' => 'http://cctv.weather.com.cn/detail.php?city=',
	'weath' => 'http://www.weather.com.cn/static/html/weather_list.shtml'
);
$t1 = new weath();
$t1->get('台北县');
//$t1->get('长沙');
foreach ($source as $S => $U) {
	$gettest = @file_get_contents($U);
	if ($gettest) {
		if($S == 'get_cctv')
		{
			get_cctv($citys);
		}
		elseif($S == 'weath')
		{
			$test = new weath();
			$test->get();
		}
		break;
	}
}


//$weath_path = 'tmp/';
//$log_path = 'tmp/';
$log_path = '/data/grab/log/';
$start = microtime_float();
function get_cctv($citys)
{
	list( , $weathImgInfo) = include ('ImagInfo.php');
	$weath_path = '/data/grab/data/weather/';
	$log_path = '/data/grab/log/';
	$py = new py_class();
	list($cities, $city_other) = $citys;
	foreach($cities AS $k => $cityName)
	{
		$time_start = microtime_float();
		if( $cityName == '佛山')
		{
			$name = urlencode('广州');
			$name_city = urlencode($cityName);
		}
		else
		{
			$name = urlencode($cityName);
			$name_city = urlencode($cityName);
		}
		$url = 'http://cctv.weather.com.cn/detail.php?city=';
		$cityPY = $pinyin=$py->str2py($cityName);
		if(empty($cityPY)) {file_put_contents($log_path.'py_error.txt.'.TOGTIME, $k.' '.$cityName."\n", FILE_APPEND);}

		$contents = file_get_contents($url.$name);
		if(empty($contents))
		{
			$contents = file_get_contents($url.$name);
			//if(empty($contents)) file_put_contents($log_path.'contents_empty.txt.'.TOGTIME, $k.' '.$cityName."\n", FILE_APPEND);
		}
		//判断是否存在天气预报信息
		preg_match("'<td width=\"222\" class=\"newstitle04\">(.*?)->(.*?)</td>'si", $contents, $ifweather);
		if(empty($ifweather[1]))
		{
			get_other($cityName);
			$time_end = microtime_float();$once = $time_end - $time_start;
			file_put_contents($log_path.'other_weath_process.txt.'.TOGTIME, $once.' '.$cityName."\n", FILE_APPEND);
		}
		else 
		{
			preg_match("/<span class=\"big-cn\">预报发布时间：(.*?)<\/span>/si",$contents, $time);
			$release_time = $time[1];
			preg_match_all("/<td><table width=\"100%\" border=\"0\">(.*?)<\/table><\/td>/si",$contents, $value);
			//发布时间
			$weath_result = $value[1];
			//天气指数
			preg_match_all("'<td valign=\"top\" ><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">(.*?)<\/table><\/td>'si",$contents, $tmp2);
			$weath_infos = array();
			foreach ($tmp2[1] AS $k => $v)
			{
				preg_match_all("'<td.*?>(.*?)</td>'si",$v,$weath_info);
				$weath_infos[$k] = $weath_info[1];
			}
			//print_r($weath_infos);

			$result = array();
			foreach ($weath_result AS $k => $v)
			{
				preg_match_all("'<td.*?>(.*?)</td>'si", $v, $tmp);
				$info = $tmp[1];//print_r($info);
				$weath_img = array();
				if (!empty($info[1])) 
				{
					preg_match_all("'<img src=\"/img/(.*)\.gif\"'siU",$info[1],$img);		
					$weath_img = $img[1];
				}
				$info[1] = $weath_img;
				$result[$k] = $info;
			}
			//判断 抓取的时间与当前的时间对比
			if(date_legal($result[0][0])) continue;
			//print_r($result);
			$cityWeathInfo = '';
			$index_weath = array();
			$cityWeathInfo .= 
				'var weatherJSON = {'.
				'城市: ["'.$cityPY.'", "'.$cityName.'"],'.
				'详情: {';
					foreach ($result as $k => $v) 
					{
						if(!empty($v[1])) 
						{
							if($v[1][0] != $v[1][1]) $weath_describe = $weathImgInfo[$v[1][0]].'转'.$weathImgInfo[$v[1][1]]; else $weath_describe = $weathImgInfo[$v[1][0]];
						}
						if('0' == $k)
						{
							$cityWeathInfo .='今天: { 气温: "'.strtr($v[2], array(' / ' => '~')).'", 日期: "'.$v[0].' '. getWeek($v[0]).'", 图标: ["'.$v[1][0].'.png", "'.$v[1][1].'.png"], 描述: ["'.$weath_describe.'", "'.$v[3].'"] }';
							$index_weath[$k] = '今天'.' '.$weath_describe.' '.strtr($v[2], array(' / ' => '~'));
						}elseif('1' == $k){
							$cityWeathInfo .=',明天: { 气温: "'.strtr($v[2], array(' / ' => '~')).'", 日期: "'.$v[0].' '. getWeek($v[0]).'", 图标: ["'.$v[1][0].'.png", "'.$v[1][1].'.png"], 描述: ["'.$weath_describe.'", "'.$v[3].'"] }';
							$index_weath[$k] = '明天'.' '.$weath_describe.' '.strtr($v[2], array(' / ' => '~'));
						}elseif('2' == $k){
							$cityWeathInfo .=',后天: { 气温: "'.strtr($v[2], array(' / ' => '~')).'", 日期: "'.$v[0].' '. getWeek($v[0]).'", 图标: ["'.$v[1][0].'.png", "'.$v[1][1].'.png"], 描述: ["'.$weath_describe.'", "'.$v[3].'"] }';
							$index_weath[$k] = '后天'.' '.$weath_describe.' '.strtr($v[2], array(' / ' => '~'));
						}
					}
				$cityWeathInfo .= '},'.
				'指数: [';
				foreach ($weath_infos as $content) 
				{
					$c = explode("  ", $content[0]);
					$cityWeathInfo .= '{标题: "'.$c[0].'", 概要: "'.str_replace('暂无','',$c[1]).'", 详述: "'.str_replace('暂无','',$content[1]).'" },';
				}
				if(!empty($cityWeathInfo))  $cityWeathInfo =substr($cityWeathInfo, 0, -1);
				$cityWeathInfo .= '],'.
				'发布时间: "'.$release_time.'",';
				$cityWeathInfo .= '首页: ["'.$index_weath[0].'", "'.$index_weath[1].'", "'.$index_weath[2].'"]'. 
			'};';
			$cityWeathInfo .= 'if(typeof(w_callback)=="function")w_callback();';
			file_put_contents($weath_path.$name_city. '.js', $cityWeathInfo, LOCK_EX);
			$time_end = microtime_float();$once = $time_end - $time_start;
			//file_put_contents($log_path.'weath_process.txt.'.TOGTIME, $once.' '.$cityName."\n", FILE_APPEND);
		}
	}

	foreach($city_other AS $k => $cityName)
	{
		$time_start = microtime_float();
		$name = urlencode($cityName);
		$url = 'http://cctv.weather.com.cn/detail.php?city=';
		$cityPY = $pinyin=$py->str2py($cityName);
		if(empty($cityPY)) {file_put_contents($log_path.'py_error.txt.'.TOGTIME, $k.' '.$cityName."\n", FILE_APPEND);}

		$contents = file_get_contents($url.$name);
		if(empty($contents))
		{
			$contents = file_get_contents($url.$name);
			if(empty($contents)) file_put_contents($log_path.'contents_empty.txt.'.TOGTIME, $k.' '.$cityName."\n", FILE_APPEND);
		}
		preg_match("/<span class=\"big-cn\">预报发布时间：(.*?)<\/span>/si",$contents, $time);
		$release_time = $time[1];
		preg_match_all("/<td><table width=\"100%\" border=\"0\">(.*?)<\/table><\/td>/si",$contents, $value);
		//发布时间
		$weath_result = $value[1];
		//天气指数
		preg_match_all("'<td valign=\"top\" ><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">(.*?)<\/table><\/td>'si",$contents, $tmp2);
		$weath_infos = array();
		foreach ($tmp2[1] AS $k => $v)
		{
			preg_match_all("'<td.*?>(.*?)</td>'si",$v,$weath_info);
			$weath_infos[$k] = $weath_info[1];
		}
		//print_r($weath_infos);

		$result = array();
		foreach ($weath_result AS $k => $v)
		{
			preg_match_all("'<td.*?>(.*?)</td>'si", $v, $tmp);
			$info = $tmp[1];//print_r($info);
			$weath_img = array();
			if (!empty($info[1])) 
			{
				preg_match_all("'<img src=\"/img/(.*)\.gif\"'siU",$info[1],$img);		
				$weath_img = $img[1];
			}
			$info[1] = $weath_img;
			$result[$k] = $info;
		}
		//print_r($result);
		$cityWeathInfo = '';
		$index_weath = array();
		$cityWeathInfo .= 
			'var weatherJSON = {'.
			'城市: ["'.$cityPY.'", "'.$cityName.'"],'. 
			'详情: {';
				foreach ($result as $k => $v) 
				{
					if(!empty($v[1])) 
					{
						if($v[1][0] != $v[1][1]) $weath_describe = $weathImgInfo[$v[1][0]].'转'.$weathImgInfo[$v[1][1]]; else $weath_describe = $weathImgInfo[$v[1][0]];
					}
					if('0' == $k)
					{
						$cityWeathInfo .='今天: { 气温: "'.strtr($v[2], array(' / ' => '~')).'", 日期: "'.$v[0].' '. getWeek($v[0]).'", 图标: ["'.$v[1][0].'.png", "'.$v[1][1].'.png"], 描述: ["'.$weath_describe.'", "'.$v[3].'"] }';
						$index_weath[$k] = '今天'.' '.$weath_describe.' '.strtr($v[2], array(' / ' => '~'));
					}elseif('1' == $k){
						$cityWeathInfo .=',明天: { 气温: "'.strtr($v[2], array(' / ' => '~')).'", 日期: "'.$v[0].' '. getWeek($v[0]).'", 图标: ["'.$v[1][0].'.png", "'.$v[1][1].'.png"], 描述: ["'.$weath_describe.'", "'.$v[3].'"] }';
						$index_weath[$k] = '明天'.' '.$weath_describe.' '.strtr($v[2], array(' / ' => '~'));
					}elseif('2' == $k){
						$cityWeathInfo .=',后天: { 气温: "'.strtr($v[2], array(' / ' => '~')).'", 日期: "'.$v[0].' '. getWeek($v[0]).'", 图标: ["'.$v[1][0].'.png", "'.$v[1][1].'.png"], 描述: ["'.$weath_describe.'", "'.$v[3].'"] }';
						$index_weath[$k] = '后天'.' '.$weath_describe.' '.strtr($v[2], array(' / ' => '~'));
					}
				}
			$cityWeathInfo .= '},'.
			'指数: [';
			foreach ($weath_infos as $content) 
			{
				$c = explode("  ", $content[0]);
				$cityWeathInfo .= '{标题: "'.$c[0].'", 概要: "", 详述: "" },';
			}
			if(!empty($cityWeathInfo))  $cityWeathInfo =substr($cityWeathInfo, 0, -1);
			$cityWeathInfo .= '],'.
			'发布时间: "'.$release_time.'",';
			$cityWeathInfo .= '首页: ["'.$index_weath[0].'", "'.$index_weath[1].'", "'.$index_weath[2].'"]'. 
		'};';
		$cityWeathInfo .= 'if(typeof(w_callback)=="function")w_callback();';
		//echo $cityWeathInfo;
		file_put_contents($weath_path . $name . '.js', $cityWeathInfo, LOCK_EX);
		$time_end = microtime_float();$once = $time_end - $time_start;
		//file_put_contents($log_path.'weath_process.txt.'.TOGTIME, $once.' '.$cityName."\n", FILE_APPEND);
	}
}


$end = microtime_float();
$over = $start-$end;
file_put_contents($log_path.'weath_process.txt.'.TOGTIME, "over time".' '.$over."\n", FILE_APPEND);
function getWeek($data)
{
	$data = preg_replace('/\s*|　/', "", $data);
	$data = date('Y').'-'.strtr($data, array('月' => '-','日' => ''));
	$week = date('N', strtotime($data));
	//$week = date('N')+$data;
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
			return "星期三";
		break;
		case '4':
			return "星期四";
		break;
		case '5':
			return "星期五";
		break;
		case '6':
			return "星期六";
		break;
		case '7':
			return "星期日";
		break;
	}
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function get_other($name)
{
    $test = new weath();
	$test->get($name);
}

/*抓取的日期与当前的日期比较*/
function date_legal($data)
{
	$data = preg_replace('/\s*|　/', "", $data);
	$data = strtr($data, array('月' => '-','日' => ''));//抓取的时间
	$today = date('n-d');//当前时间
	return $data > $today;
}
