#!/data/opt/php/bin/php
<?php
include_once('utf8topy.class.php');
list($china, $other) = include('filter.php');
$py = new py_class();
$city_content = file_get_contents('weather_list.shtml');

//国内
preg_match_all("'<div\s+class=\"tmapin_citylist\">(.*?)</div>'is", $city_content, $china_city);
foreach ($china_city['1'] AS $china_key => $china_value) 
{//print_r($china_value);exit;
	preg_match("'<p>(.*?)</p>'is", $china_value, $china_p);
	$name = $china_p['1'];
	preg_match_all("'<a\s+href=\"(.*?)\"[^>]*>(.*?)</a>'is", $china_value, $china_city2);
	//print_r($china_city2);exit;
	foreach ($china_city2['0'] AS $k2 => $v2) 
	{
		$city3 = file_get_contents($china_city2['1'][$k2]);
		$name1 = $china_city2['2'][$k2];
		if('北京' == $china_city2['2'][$k2] || '南京' == $china_city2['2'][$k2])
		{
		preg_match_all("'<option\s+value=\"http\:\/\/www\.weather\.com\.cn\/weather\/(\d+)\.shtml\">(.*?)</option>'is", $city3, $tmp3);
		}
		else 
		{
			preg_match_all("'<option\s+value=\"http:\/\/www\.weather\.com\.cn\/weather\/(\d+)\.shtml\">(.*?)</option>'is", $city3, $tmp3);
		}
		//print_r($tmp3);exit;
		foreach ($tmp3['0'] as $k4 => $v4) 
		{
			if( $tmp3[2][$k4] == '阿左旗')
			{  
				$tmp3[2][$k4] = '阿拉善左旗';
			}
			//if($china_city2[2][$k2] == '内蒙古'){ echo "aaa"; print_r($china);exit;}
			if( !empty($china[$china_city2['2'][$k2]]))
            {

				//echo $tmp3['2'][$k4];exit;
				//print_r($china[$china_city2['2'][$k2]]);exit;
				foreach($china[$china_city2['2'][$k2]] AS $k5 => $v5)
				{
					if($tmp3['2'][$k4] == $v5) unset($tmp3['2'][$k4]);
				}
            }
			if(!empty($tmp3['2'][$k4])) $code[$name][$name1][] = $tmp3['1'][$k4].'|'.$tmp3['2'][$k4].'|'.$china_city2['2'][$k2];
		}
		$c_py = strtoupper($py->str2pyOne($china_p['1']));
		$n_py = strtoupper($py->str2pyOne($china_city2['2'][$k2]));
		if( '直辖市' == $china_p['1'] || '特区' == $china_p['1'])
		{
			$info[] = '{n:"'.$china_city2['2'][$k2].'",py:"'.$n_py.'",i:0,pv:["'.$china_p['1'].'","'.substr($c_py, 0,1).'"],pc:1,c:["'.implode('","', $tmp3['2']).'"]}';
		}
		elseif(0 == $k2 )
		{
			$info[] = '{n:"'.$china_city2['2'][$k2].'",py:"'.$n_py.'",i:0,pv:["'.$china_p['1'].'","'.substr($c_py, 0,1).'"],pc:1,c:["'.implode('","', $tmp3['2']).'"]}';
		}
		else
		{
			$city2_all = '';
			foreach( $tmp3[2] AS $k_1 => $v_1)
			{
				if($v_1 != '长恒') $city2_all[] = $v_1;
			}
			$info[] = '{n:"'.$china_city2['2'][$k2].'",py:"'.$n_py.'",i:0,pv:["'.$china_p['1'].'","'.substr($c_py, 0,1).'"],pc:0,c:["'.implode('","', $city2_all).'"]}';
		}
	}
}
print_r($code);exit;
/*生成中国 城市 数组*/
//China City
$china_temp = implode(',', $info);
//file_put_contents('./chinacity.js', 'var J1616_weather_citys = ['.$china_temp.'];');
unset($china_temp);
//国外主要城市
preg_match_all("'<div\s+class=\"tmapin_citylist1\">(.*?)</div>'is", $city_content, $other_city);
foreach ($other_city['1'] AS $other_key => $other_value)
{
        preg_match("'<p>(.*?)</p>'is", $other_value, $other_p);
        preg_match_all("'<a\s+href=\"http://www.weather.com.cn/html/weather/(\d+)\.shtml\"[^>]*>(.*?)</a>'is", $other_value, $other_city2);
        //print_r($other_city2);exit;
        foreach ($other_city2['0'] as $other_city2_key => $other_city2_value)
        {
		if(!in_array($other_city2['2'][$other_city2_key], $other))
		{ 
                $info[] = '{n:"'.$other_city2['2'][$other_city2_key].'",py: "'.strtoupper($py->str2pyOne($other_city2['2'][$other_city2_key])).'",i:1,ct:"'.$other_p['1'].'"}';
                $code[] = $other_city2['1'][$other_city2_key].'|'.$other_city2['2'][$other_city2_key];
		}
        }
}
print_r($code);exit;
/*对城市列表整理*/
//地区 表 http://www.weather.com.cn/static/custom/select.html
$temp = implode(',', $info);
file_put_contents('./citys.js', 'var J1616_weather_citys = ['.$temp.'];');
/*exit;
foreach ($w_code as $wk => $wv) 
{
	$tmp3 = explode(',', $wv);
	//$tmp3 = explode('|', $wv);
	foreach ($tmp3 as $tk => $tw) 
	{
		$t = explode('|', $tw);
		//file_put_contents('code.txt', $t['0'].'|'.$t['1']."\n", FILE_APPEND);
	}
}
*/

foreach ( $code AS $c_key => $c_value)
{
	//$v = explode('|', $c_value);
	$c .= $c_value."\n";
}
file_put_contents('code.txt', $c);
echo "suss";
exit;
//print_r($code);exit;
//echo implode(',', $info);
//echo count($info);exit;
