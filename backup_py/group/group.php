<?php
/*
城市数组
$city = array('beijing' => '北京');
生成文件 名称规则
xml_meittuan_beijing.xml
xml_美团_北京.xml
http://www.meituan.com/api/v1/城市拼音(beijing)/deals
*/
//http://search.hao123.com/tgdata.js
$city = array('美团' => array('北京' => array('url' => 'http://xxx.meituan.com/xxx', 'rules' => array())));
date_default_timezone_set("Asia/Shanghai");
$date = new DateTime('2010-08-19T00:00:00+08:00');
echo $date->format('Y-m-d H:i:s');

$groups = '';
//echo file_get_contents('http://www.meituan.com/api/v1/beijing/deals');
$doc = new DomDocument;
//$doc->validateOnParse = true;
//$doc->Load('./group.xml');
$doc->Load('http://open.client.lashou.com/list/goods/cityid/2419');
//$doc->Load('http://www.meituan.com/api/v1/beijing/deals');
//$params = $doc->getElementsByTagName('deal');
$params = $doc->getElementsByTagName('goods');
foreach ($params as $k => $param) 
{
    //$param->nodeValue;
    $start_datas = '';
    $start_dates = $param->getElementsByTagName('*');
    //$start_dates = $param -> getElementsByTagName('vendor_id');
    //echo $start_dates->length;exit;
    foreach ($start_dates as $item) 
    {
        $groups[$k][$item->nodeName] = $item->nodeValue;//array($item->nodeName => $item->nodeValue);
        echo $item->nodeName.' '.$item->nodeValue . "\n";
    }
    //exit;
    //print_r($start_dates->item());exit;
    //echo $start_date = $start_dates->item(0)->nodeValue;
}
print_r($groups);exit;
