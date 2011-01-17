<?php
class weath
{
	public $weathImgInfo;
	public $cities;
	public $py;
	public $path;
	public $citysname;
	public $url = 'http://www.weather.com.cn/static/html/weather_list.shtml';
	public $un = array();
	function __construct()
	{
		//include ('utf8topy.class.php');
		$this->py = new py_class();
		list($this->weathImgInfo, ) = include ('ImagInfo.php');
		$this->path = '/data/grab/data/weather/';
		//$this->path = 'tmp/';
		//$url = 'http://www.weather.com.cn/static/html/weather_list.shtml';
		$this->citysname = $this->get_city($this->url);  //print_r($this->citysname);exit;
		//$this->cities = include ('city.php');
	}
	function get_city($url,$name='')
	{
		$cityContent = file_get_contents($url);
		$cityContent = preg_replace("/<\!--(.|\n)*?-->/", "", $cityContent);
		preg_match_all("'<div class=\"tmapin_citylist\d?\">(.*)</div>'siU",$cityContent,$tmp);
		//print_r($tmp[1]);exit;
		foreach ($tmp[1] AS $k => $v)
		{
			preg_match("'<p>(.*?)</p>'",$v,$cN);
			//城市名称
			$cityName = $cN[1];
			preg_match_all("'<a href=\"(.*)\" target=\"_blank\">(.*)</a>'siU",$v,$urlName);
			$this->un[$cityName] = $this->MergeArray($urlName[2],$urlName[1]);
			
		}
		return $this->un;
	}
	function MergeArray($array1,$array2)
	{
		//print_r($array1);print_r($array2);
		$tmp_array = array();
		//print_r($array2);exit;
		foreach ($array1 AS $k => $v)
		{
			$tmp_array[$v] = $array2[$k];
		}//print_r($tmp_array);exit;
		return $tmp_array;
	}


	function getCityWeath($url, $name='', $pinyin='')
	{
		
		$cityContent = file_get_contents($url);
		//$cityContent = preg_replace("/<\!--(.|\n)*?-->/", "", $cityContent);echo $cityWeathInfo;exit;
		$cityContent = str_replace(array("\r","\n", "\t"), "", $cityContent);
		preg_match("'<h3><strong>(.*?)</strong>(.*?)</h3>'si",$cityContent,$cN);
		//today weath
		preg_match("'<em><strong><a[^>]*?>(.*?)</a></strong></em><em class=\"no_today\">(.*?)</em><em>(.*?)</em>'si",$cityContent,$today);
		preg_match("'<a[^>]*?>(.*?)</a>'is",$today['3'],$tmp);
		$today['3'] = $tmp['1'];
		$today[1] = preg_replace("/<a[^>]*?>/", "", $today[1]);
		$today[1] = str_replace(array("</a>"), "", $today[1]);

		$today_weath[] = array($today[1],$today[2],$today[3]);
		//next day <div class="fut_weatherbox7">

		preg_match_all("'<div class=\"fut_weatherbox7\">(.*)</div>'siU",$cityContent,$nextday);
		preg_match("'<h3>(.*)</h3>(.*)<h4 class=\"temp00_dn\"><a[^>]*?>(.*?)</a></h4><h4 class=\"temp01_dn\"><a[^>]*?>高温：(.*?)</a></h4><h4 class=\"temp02_dn\"><a[^>]*?>低温：(.*?)</a></h4><h4 class=\"temp03_dn\"><a[^>]*?>(.*?)</a>'si",$nextday[1][0],$secondday);
		$secondday[3] = preg_replace("/<a[^>]*?>/", "", $secondday[3]);
		$secondday[3] = str_replace(array("</a>"), "", $secondday[3]);

		preg_match("'<h3>(.*)</h3>(.*)<h4 class=\"temp00_dn\"><a[^>]*?>(.*?)</a></h4><h4 class=\"temp01_dn\"><a[^>]*?>高温：(.*?)</a></h4><h4 class=\"temp02_dn\"><a[^>]*?>低温：(.*?)</a></h4><h4 class=\"temp03_dn\"><a[^>]*?>(.*?)</a>'si",$nextday[1][1],$thirdday);
		$thirdday[3] = preg_replace("/<a[^>]*?>/", "", $thirdday[3]);
		$thirdday[3] = str_replace(array("</a>"), "", $thirdday[3]);

		$today_weath[] = array($secondday[3],$secondday[4].'/ '.$secondday[5],$secondday[6], $secondday[1]);
		$today_weath[] = array($thirdday[3],$thirdday[4].'/ '.$thirdday[5],$thirdday[6], $thirdday[1]);
		//print_r($today_weath);exit;
		//生活指数
		preg_match_all("'<div class=\"box_lifeboxin\"[^>]*?>(.*?)</div>'i", $cityContent, $lifes);
		foreach($lifes['1'] AS $k => $life)
		{
			preg_match("'<dd><strong><a[^>]*?>(.*)\：(<i>|<em>)(.*)(</i>|</em>)</a></strong><br />(.*)</dd></dl>'si",$life, $tmp1);			
			$lifeindex[$k] = array($tmp1[1],$tmp1[3],$tmp1[5]);
		}
		//PRINT_R($this->weathImgInfo);
		//print_r($today_weath);exit;
		//$pinyin = $py->str2py($name);

		//shengc
		$cityWeathInfo = '';
		$index_weath = array();
		$cityWeathInfo .= 
			'var weatherJSON = {'.
			'城市: ["'.$pinyin.'", "'.$name.'"],'. //[城市拼音,城市中文名]
			'详情: {';//含图标
			
				foreach ($today_weath as $k => $v) 
				{				
					$imgs = explode("转", $v[0]);
					if('1' == count($imgs)) $imgs[1] = $imgs[0];
					//print_r($imgs);					
					if('0' == $k)
					{
						$cityWeathInfo .='今天: { 气温: "'.strtr($v[1], array('/ ' => '~')).'", 日期: "'.$this->get_date($k).'", 图标: ["'.$this->weathImgInfo[$imgs[0]].'.png", "'.$this->weathImgInfo[$imgs[1]].'.png"], 描述: ["'.$v[0].'", "'.$v[2].'"] }';
						$index_weath[$k] = '今天'.' '.$v[0].' '.strtr($v[1], array('/ ' => '~'));
					}elseif('1' == $k){
						$cityWeathInfo .=',明天: { 气温: "'.strtr($v[1], array('/ ' => '~')).'", 日期: "'.$this->get_date($k).'", 图标: ["'.$this->weathImgInfo[$imgs[0]].'.png", "'.$this->weathImgInfo[$imgs[1]].'.png"], 描述: ["'.$v[0].'", "'.$v[2].'"] }';
						$index_weath[$k] = '明天'.' '.$v[0].' '.strtr($v[1], array('/ ' => '~'));
					}elseif('2' == $k){
						$cityWeathInfo .=',后天: { 气温: "'.strtr($v[1], array('/ ' => '~')).'", 日期: "'.$this->get_date($k).'", 图标: ["'.$this->weathImgInfo[$imgs[0]].'.png", "'.$this->weathImgInfo[$imgs[1]].'.png"], 描述: ["'.$v[0].'", "'.$v[2].'"] }';
						$index_weath[$k] = '后天'.' '.$v[0].' '.strtr($v[1], array('/ ' => '~'));
					}
				}
			$cityWeathInfo .= '},'.
			'指数: [';//各种指数
			if(!empty($lifeindex))
			{
				foreach ($lifeindex as $content) 
				{
					$cityWeathInfo .= '{标题: "'.$content[0].'", 概要: "'.$content[1].'", 详述: "'.$content[2].'" },';
				}
				if(!empty($cityWeathInfo))  $cityWeathInfo =substr($cityWeathInfo, 0, -1);
			}
			$cityWeathInfo .= '],'.
			'发布时间: "'.$this->releas_time().'",';
			$cityWeathInfo .= '首页: ["'.$index_weath[0].'", "'.$index_weath[1].'", "'.$index_weath[2].'"]'. //供首页调用，不含图标,html代码
		'};';
		$cityWeathInfo .= 'if(typeof(w_callback)=="function")w_callback();';
		//echo $cityWeathInfo;
		//echo urlencode($name);
		file_put_contents($this->path.urlencode($name) . '.js', $cityWeathInfo, LOCK_EX);
		//print_r($lifeindex);
	}

	function twotoone($array)
	{
		$tmp = array();
		foreach ($array AS $k => $v)
		{
			foreach ($v AS $name => $url)
			{	
				$tmp[$name] = $url;
			}
		}
		return $tmp;
	}
	function get_date($n)
	{
		$nextWeek = time() + (24 * 60 * 60)*$n;
		return date('n月j日', $nextWeek).' '.$this->getWeek($n);
	}
	function releas_time()
	{
		//抓取天气发布时间
		$release_time = file_get_contents('http://www.weather.com.cn/data/cityinfo/101010100.html');
		$release_time = json_decode($release_time, true);
		return date('Y-m-d').' '.$release_time['weatherinfo']['ptime'];
	}
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
	function get($name = '')
	{
		//$this->getCityWeath('http://www.weather.com.cn/html/weather/101130501.shtml','北京');
		if(empty($name))
		{
			foreach ($this->citysname as $p => $c) 
			{
				foreach ($c as $n => $url) 
				{
					$pinyin = $this->py->str2py($n);
					$this->getCityWeath($url, $n, $pinyin);
				}
			}
		}
		else 
		{
			if ( $name == '台北县')
			{
				$this->getCityWeath('http://www.weather.com.cn/html/weather/101340101.shtml', '台北', 'taibei');   
			}
			foreach ($this->citysname as $p => $c) 
			{
				foreach ($c as $n => $url) 
				{
					if($name == $n) 
					{
						$pinyin = $this->py->str2py($n);
						$this->getCityWeath($url, $n, $pinyin);
					}
				}
			}
		}
	}
}
//$test = new weath();
//$test->get('延边');
//getCityWeath('http://www.weather.com.cn/html/weather/101130501.shtml','北京');
//print_r(get_city($url,$v));
