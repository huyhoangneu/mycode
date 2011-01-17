<?php
include_once('config.php');
include_once('function1.php');
define('CHARSET', 'utf-8');
set_time_limit(0);
$path = '/data/grab/data/xingzuo/';
foreach ($star_array as $key => $value) 
{
	$day_info = get_day($key);
	$info = template($day_info, $key);
	file_put_contents($path.$key.'.htm', $info);
}
function template($info, $key)
{
	$style = array('1' => '13px', '2' => '30px', '3' => '46px','4'=> '64px', '5' => '80px');
	$html = '
		<div id=xz-c class=a-c>
			<div class=xc-s>
			<a class=xc-pic href=http://yuncheng.1616.net/'.$key.'.htm><img src=http://i.9533.com/style/images/sx'.$key.'.gif></a>
			<div class=xc-ss><a>白羊座</a><em class=xc-arrow></em><div class=xc-ss-list><a>白羊座</a><a>金牛座</a><a>双子座</a><a>巨蟹座</a><a>狮子座</a><a>处女座</a><a>天秤座</a><a>天蝎座</a><a>射手座</a><a>魔羯座</a><a>水瓶座</a><a>双鱼座</a></div></div>
		</div>
		<ul class=xc-star>';
	$html .= '
	<li><span>综合：</span><em><b style="width:'.$style[$info['star']['0']['star']].'"></b></em></li><li><span>爱情：</span><em><b style="width:'.$style[$info['star']['1']['star']].'"></b></em></li><li><span>工作：</span><em><b style="width:'.$style[$info['star']['2']['star']].'"></b></em></li><li><span>理财：</span><em><b style="width:'.$style[$info['star']['3']['star']].'"></b></em></li>
	</ul>';
	$html .= '
	<ul class=xc-zs><li style=width:54%>健康指数：<em>'.$info['content']['4']['content'].'</em></li><li>商谈指数：<em>'.$info['content']['5']['content'].'</em></li><li style=width:54%>幸运颜色：<em>'.$info['content']['6']['content'].'</em></li><li>幸运数字：<em>'.$info['content']['7']['content'].'</em></li></ul>
	<p class=xc-yc><a href=http://yuncheng.1616.net/'.$key.'.htm >&nbsp;&nbsp;&nbsp;&nbsp;'.sub_str($info['comment'], 80).'</a></p><a class=xc-more href=http://yuncheng.1616.net/'.$key.'.htm >更多»</a>
	</div>';
	return $html;
}
function sub_str($string, $length = 0, $append = true)
{

	if(strlen($string) <= $length) {
		return $string;
	}

	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

	$strcut = '';

	if(strtolower(CHARSET) == 'utf-8') {
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	if ($append && $string != $strcut)
	{
		$strcut .= '...';
	}

	return $strcut;

}
?>
