<?php
#!/data/opt/php/bin/php
include_once '/data/grab/db/config.inc.php';
$target_path = '/data/grab/data/history/';
$img_path = '/data/grab/data/history/';
for($m=1; $m<13; $m++)
for($d=1; $d<32; $d++)
{
	$md = date("md", mktime(0, 0, 0, $m, $d));
	$rs = $mysql->get_all("SELECT * FROM `today_in_history` AS a LEFT JOIN `tih_attachment` AS b ON a.sid = b.sid where a.date like '%" . $md . "' order by a.date");
	if(count($rs) > 0)
	{
		$names = array();
		$contents = array();
		foreach($rs as $k => $r)
		{
			if ($r['date'] < 0) {
				$r['date'] = '公元前' . abs($r['date']);
			}
			$r['name'] = strtr(trim($r['name']),array("\n"=>"","\r\n"=>""));
			$names[] = $r['date'];
			$names[] = $r['name'];
			$contents[$k]['date'] = $r['date'];
			$contents[$k]['name']     = $r['name'];
			if(!empty($r['filename'])) 
			{	
				list($width, $height) = getimagesize($img_path.$r['filename']);
				$contents[$k]['filename'] = array($r['filename'], $height, $width); 
			}
			else 
			{
				$contents[$k]['filename'] = array();
			}
			$contents[$k]['keywords'] = strtr($r['keywords'],array(","=>"&nbsp;"));
			$contents[$k]['content'] = strtr($r['content'],array("'"=>"\'",'"'=>'\"'));
			$contents[$k]['pic_discripion'] = $r['comment']; 
		}

		/*foreach ($names as $k => $v) 
		  {
		  $temp[$k] = "[".$v['date'].",".$v['name']."]";
		  }*/
		$js = "var names=[\"" . implode("\",\"", $names) . "\"];";
		$js = str_replace(array("\r","\n", "\t"), "", $js);
		file_put_contents( $target_path . $md . '.js', $js);
		$temp = '';$js = '';
		foreach ($contents as $k => $v) 
		{
			if(!empty($v['filename']))
			{
				$img_info  = "['";
				$img_info .= implode("','",$v['filename']);
				$img_info .= "']";
			}
			else 
			{
				$img_info = '[]';
			}
			$temp[$k] = "['".$v['date']."','".$v['name']."',".$img_info.",'".$v['keywords']."','".$v['content']."','".$v['pic_discripion']."']";
		}
		$js = "var contents=[" . implode(",", str_replace(' ', '&nbsp;', $temp)) . "];";
		$js = str_replace(array("\r\n"), "##", $js);
		$js = str_replace(array("\n"), "#", $js);
		file_put_contents( $target_path . $md . '_c.js', $js);
	}
}
//['19830109','曾侯乙墓编钟复制成功',['2294_3530422_1235026037.JPG','高','宽'],'曾侯乙墓编钟&nbsp;曾侯乙编钟','978年5月泛的兴趣。','曾侯乙编钟']
