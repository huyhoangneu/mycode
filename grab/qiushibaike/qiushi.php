<?php
$u = 'http://www.qiushibaike.com/groups/2/latest/page/';
for($i=1; $i <=10; $i++)
{
	$blocks = '';
	$contents = getHtmlContents($u.$i);
	preg_match_all("'<div\s+class=\"datetime\">(.*?)</div>'is", $contents, $block);
	$blocks = $block[1];
	foreach ($blocks AS $value)
	{
		preg_match("'<a\s+[^>]*>#(\d+)</a>'is", $value, $id);
		//$url = 'http://www.qiushibaike.com/articles/'.$id[1].'.htm'."\n";
		$urls[] = $id[1]."\n";
		//file_put_contents('./urls_id.txt', $url, FILE_APPEND);
	}
}
print_r($urls);
exit;
echo "aa";
function getHtmlContents($url)
{
	$agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/20090729 Firefox/3.5.2';
	$data = '';
	if( !isset($ch) && $url)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_USERAGENT, $agent );
	}
	$data = curl_exec ( $ch );
	return $data;
}
/*
	table => `embarrass_content`
	`id` int(11) NOT NULL AUTO_INCREMENT,

		'tags' => '',//'标签',
		'content' =>'',// '内容',
		'state' =>'1',// '状态 0为隐藏 1为审核通过 2没有审核',
		'comment_number' => '',// '评论数',
		'create_datetime' => time(),// '发布时间',
		'member_user' => ''//'发布用户',


		table => `embarrass_comment` 
		`content_id` int(11) NOT NULL COMMENT 'embarrass_content 表ID',

			`content` => '',// '内容',
			`state` => '1',// '状态 0为隐藏 1为显示',
			`create_datetime` => time() ,//'发布时间',
			`member_user` => '' //'发布用户',
*/
