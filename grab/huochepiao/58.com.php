<?php
$alls = '';
$url = 'http://www.58.com/huochepiao/';
$contents = getHtmlContents($url);
preg_match("'<table\s+class=\"tblist\"[^>]*>(.*?)</table>'is", $contents, $block);
$blocks = $block['1'];
preg_match_all("'<tr>(.*?)</tr>'is", $blocks, $tr);
unset($tr['1']['0']);
$trs = $tr['1'];
foreach($trs AS $k => $v)
{
	preg_match_all("'<td[^>]*>(.*?)</td>'is", $v, $td);
	$tds = $td['1'];
	preg_match("'<a\s+target=\"_blank\"\s+href=\"(.*?)\">(.*?)</a>'is", $tds['0'], $info);
	//print_r($tds);
	$tmp = explode(' ', $info['2']);
	//print_r($tmp);exit;
	$alls[] = array('url' => $info['1'], 'info' => $info['2'], 'type' => $tds['1'], 'time' => $tds['3'], 'sendtime' => $tmp['0'], 'ci' => $tmp['2']);
	//print_r($alls);exit;
}
//print_r($alls);exit;
//print_r($tmp_sort);exit;
$div = '<div class="t3"><div class="t3L"><div class="t3R"> <strong>火车票转让信息</strong>';
$div .= '<span class="more"><a href="http://www.58.com/huochepiao/" target="_blank">更多&raquo;</a></span>';
$html = createTbl($alls);
$div .= '</div></div></div>';
file_put_contents('/data/grab/data/huochepiao/piao.htm', $div.$html);
exit;
//echo $div.$html;exit;
//print_r($all);exit;
function createTbl($arr, $flag = 0)
{
		//echo $flag."\n";
		$html = '';
		if($flag == 0)
		{
				      $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab0_body">';
		}
		else
		{
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab'.$flag.'_body" class="hide">';             
		} 
		$html .= '<tr><th align="left">火车票信息</th><th width="12%" aling="center">发车时间</th><th align="center" width="11%">席别</th><th align="center" width="11%">车次</th><th align="center">更新时间</th></tr>';
		foreach($arr AS $k => $v)
		{
				if($k < 10)
				{
					$html .= '<tr><td><a href="'.$v['url'].'">[转让]'.trim($v['info']).'</a></td><td width="12%" align="center">'.$v['sendtime'].'</td><td width="8%" align="center">'.$v['type'].'</td><td width="11%" align="center">'.$v['ci'].'</td><td align="center">'.$v['time'].'</td></tr>';
				}
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
