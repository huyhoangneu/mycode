<?php
$che_type = array('58.com', 'ganji.com', 'sohu.com','huoche.com', 'huochepiao.com');
$che_type_more = array('58.com' => '58.com', 'ganji.com' => '赶集网', 'sohu.com' =>'搜狐网', 'huoche.com' => '火车网', 'huochepiao.com' => '火车票网');
$base_url = 'http://shenghuo.google.cn/shenghuo/search?a_n0=%E7%81%AB%E8%BD%A6%E7%A5%A8&a_n1=%E5%A7%8B%E5%8F%91%E7%AB%99&a_y1=1&a_o1=0&a_n2=%E6%9D%A5%E6%BA%90&a_y2=1&a_o2=0&a_v2=%E4%B8%AA%E4%BA%BA&a_n3=%E5%9F%8E%E5%B8%82&a_y3=1&a_o3=0&a_v3=%E5%8C%97%E4%BA%AC&view=Table';
$alls = '';
for($i =1; ;$i++)
{
	$stop = 0;
	if($i == 20)
	{
			foreach($che_type AS $key => $value)
			{
					if(isset($alls[$value]))
					{
							$count = count($alls[$value]);
							echo $count."\n";
							if($count == 10)
							{
									$stop +=1;
							}
					}
			}
	}
	//echo $stop."\n";
	if($stop == 4 || $stop == 3) break;
	$url = $base_url.'&start='.($i*20).'&num=20';
	$contents = getHtmlContents($url);
	preg_match_all("'<tr\s+id=\"sprr\d+\"[^>]*>(.*?)</tr>'is", $contents, $block1);
	foreach($block1['1'] AS $k1 => $v1)
	{
		preg_match_all("'<td[^>]*>(.*?)</td>'is", $v1, $block_td);
		$v = $block_td['1']['0'];
		//print_r($block_td);exit;
		preg_match_all("'<a.*?href=\"(.*?)\"[^>]*>(.*?)</a>'is", $v, $one);
		$url = $one['1']['0'];
		$info = $one['2']['0'];
		$f = $one['2']['1'];
		if(in_array($f, $che_type))
		{
			if(count($alls[$f]) < 10)
			{
				$alls[$f][] = array('url' => $url, 'info' => $info, 'type' => $block_td['1']['3'], 'time' => $block_td['1']['4']);
			}
		}
	}
}
foreach($che_type AS $k_sort => $v_sort)
{
		if(in_array($v_sort, array_keys($alls)))
		{
			$all[$v_sort] = $alls[$v_sort];
		}
}
//print_r($tmp_sort);exit;
$div = '<div class="t3"><div class="t3L"><div class="t3R"> <strong>火车票转让信息</strong><ul>';
$html = '';
$j = 0;
foreach($all AS $k1 => $v1)
{
	$count = count($v1);
	if($count == 10)
	{
			if($j == 0)
			{
			$div .= '<li id="tab'.$j.'" class="hv"><span><a href="javascript:void(0);" onclick="return false;">'.$che_type_more[$k1].'</a></span></li>';
			}
			else
			{
			$div .= '<li id="tab'.$j.'"><span><a href="javascript:void(0);" onclick="return false;">'.$che_type_more[$k1].'</a></span></li>';
			}
			//echo $j."\n";
			$html .= createTbl($v1, $j);
			$j +=1;
	}
}
$div .= '</ul></div></div></div>';
file_put_contents('/data/grab/data/huochepiao/piao.htm', $div.$html);
exit;
//echo $div.$html;exit;
//print_r($all);exit;
function createTbl($arr, $flag)
{
		//echo $flag."\n";
		$html = '';
		if($flag == 0)
		{
				      $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab'.$flag.'_body">';
		}
		else
		{
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab'.$flag.'_body" class="hide">';             
		} 
		$html .= '<tr><th align="left">火车票信息</th><th width="12%" aling="center">发车时间</th><th align="center" width="11%">席别</th><th align="center" width="11%">车次</th><th align="center">更新时间</th></tr>';
		foreach($arr AS $k => $v)
		{
				/*
				if($flag == 0)
				{
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab'.$flag.'_body">';
				}
				else
				{
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab'.$flag.'_body" class="hide">';
				}
				 */
				$tmp = explode(':', $v['info'], 2);
				print_r($tmp);
				$infos = explode(' ', $v['info']);
				$time = explode(':', $infos['4']);
				$che = preg_match("'(t|d|z|n|a|l|y|[1-9])\d{1,3}([,//](t|d|z|n|a|l|y|[1-9])\d{1,3})*'", $infos['1']) ? $infos['1'] : '--';
				//$html .= '<tr><td><a href="'.$v['url'].'">[转让]'.trim(strtr($tmp['0'], array('发车日期' => ''))).'</a></td><td >'.$tmp['1'].'</td><td>'.$v['type'].'</td><td>'.$che.'</td><td>'.$v['time'].'</td></tr>';
				$html .= '<tr><td><a href="'.$v['url'].'">[转让]'.trim(strtr($tmp['0'], array('发车日期' => ''))).'</a></td><td width="12%" align="center">'.$tmp['1'].'</td><td width="8%" align="center">'.$v['type'].'</td><td width="11%" align="center">'.$che.'</td><td align="center">'.$v['time'].'</td></tr>';
				//echo $html;exit;
		}
		$html .= '</table>';
		return $html;

}
function getHtmlContents($url)
{
	$data = '';
	if( !isset($ch) && $url)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0' );
	}
	$data = curl_exec ( $ch );
	return $data;
}
