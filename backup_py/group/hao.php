<?php
$apiurls = file(dirname(__FILE__).'/api.txt');
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
$date = new DateTime();
$newDate = $date->format('Y-m-d');
$nodeName = array('website','siteurl','city','title','image','startTime','endTime','value','price','rebate','bought','item_url');
$doc = new DomDocument;
//$doc->validateOnParse = true;
//$doc->Load('./api.xml');
$all = array();
foreach($apiurls AS $url)
{
    $empty = true;
    //$url = 'http://www.kutuan.com/tuangou/api/hao123';
    $groups = '';
    $doc->Load(trim($url));
    $params = $doc->getElementsByTagName('url');
    foreach ($params as $k => $param) 
    {
        //echo $param->childNodes->length;exit;
        //$param->nodeValue;
        $start_datas = '';
        $start_dates = $param->getElementsByTagName('*');
        $city = $param->getElementsByTagName('city');
        $city = trim($city->item(0)->nodeValue);
        $startTime = $param->getElementsByTagName('startTime');
        $startTime = trim($startTime->item(0)->nodeValue);
        //echo $startTime."\n";
        //$startDate = date('Y-m-d', $startTime);
        //echo $start_dates->length;exit;
        //echo $city."\n";
        if(empty($groups[$city]) || (!empty($groups[$city]) && $groups[$city]['startTime'] < $startTime))
        {
            foreach ($start_dates as $item) 
            {
                /*
                   [website] => 美团 
                   [siteurl] => http://www.meituan.com/beijing
                   [city] => 北京 
                   [title] => 仅售5元，价值90元的乐淘网网上鞋城年庆优惠券（满150元就可使用+全场通用+专柜正品+免费配送+7天无理由退换
                   货），全国可用！ 
                   [image] => http://p1.meituan.com/275.168/deal/201008/ltzhutu_0818000927.jpg
                   [startTime] => 1282060800
                   [endTime] => 1282147199
                   [value] => 90//原价
                   [price] => 5//价格
                   [rebate] => 0.5//折扣
                   [bought] => 22136//订购人数 
                   [item_url] => http://www.meituan.com/beijing/deal/bjltw.html
                 */
                if(in_array($item->nodeName, $nodeName))
                {
                    $groups[$city][$item->nodeName] = trim($item->nodeValue);//array($item->nodeName => $item->nodeValue);
                }
            }
            $groups[$city]['item_url'] = $param->getElementsByTagName('loc')->item(0)->nodeValue;
        }
        if(empty($groups[$city])) {$empty = false;break;}
        else
        {
            //downImage($image);
        }
        //print_r($start_dates->item());exit;
        //echo $start_date = $start_dates->item(0)->nodeValue;
    }
    //print_r($groups);exit;
    if($empty) $all = arrayMerge($all, $groups);
}
if(!empty($all))
{
    $json = "var strTgData='".json_encode($all)."';";
    file_put_contents( dirname(__FILE__)."/data.js", $json, LOCK_EX );
}
//print_r(array_merge ($groups, $tmp));
function arrayMerge($array1, $array2)
{
    foreach($array2 AS $name => $info)
    {
        if(!empty($array1[$name]))
        {
            array_push($array1[$name], $info);
        }
        else
        {
            $array1[$name][] = $info;
        }
    }
    return $array1;
}
//print_r($groups);exit;
