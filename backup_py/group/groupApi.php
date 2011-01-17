<?php
$info = array(
    'loc' => 'http://www.meituan.com/shanghai/deal/shltw.html', 
    'website' => '美团', 
    'siteurl' => 'http://www.meituan.com/', 
    'city' => '北京', 
    'title' => '原价188元的“玛雅蛋糕”仅售49元！', 
	'image' => 'http://maya.xxx.com/xxx.gif', 
    'startTime' => '1275926400', 
    'endTime' => '1291910399', 
    'value' => '188.00', 
	'price' => '49.00', 
	'rebate' => '2.6', 
	'bought' => '100');

$doc = new DOMDocument('1.0', 'utf-8');
// we want a nice output
$doc->formatOutput = true;
$urlset = $doc->createElement('urlset');
$urlset = $doc->appendChild($urlset);

$url = $doc->createElement('url');
$url = $urlset->appendChild($url);

$loc = $doc->createElement('loc');
$loc = $url->appendChild($loc);

//商品url
$text = $doc->createTextNode($info['loc']);
$text = $loc->appendChild($text);
//child data
$data = $doc->createElement('data');
$data = $url->appendChild($data);
//display
$display = $doc->createElement('display');
$display = $data->appendChild($display);

// 站点名称
$website = $doc->createElement('website');
$website = $display->appendChild($website);
$text = $doc->createTextNode($info['website']);
$text = $website->appendChild($text);


//站点名称 
$siteurl = $doc->createElement('siteurl');
$siteurl = $display->appendChild($siteurl);
$text = $doc->createTextNode($info['siteurl']);
$text = $siteurl->appendChild($text);

//城市名称
$city = $doc->createElement('city');
$city = $display->appendChild($city);
$text = $doc->createTextNode($inf['city']);
$text = $city->appendChild($text);

//商品标题 
$title = $doc->createElement('title');
$title = $display->appendChild($title);
$text = $doc->createTextNode($info['title']);
$text = $title->appendChild($text);

//商品图片url
$image = $doc->createElement('image');
$image = $display->appendChild($image);
$text = $doc->createTextNode($info['image']);
$text = $image->appendChild($text);

//商品开始时间  
$startTime = $doc->createElement('startTime');
$startTime = $display->appendChild($startTime);
$text = $doc->createTextNode($info['startTime']);
$text = $startTime->appendChild($text);

//商品结束时间  
$endTime = $doc->createElement('endTime');
$endTime = $display->appendChild($endTime);
$text = $doc->createTextNode($info['endTime']);
$text = $endTime->appendChild($text);


//商品原价
$value = $doc->createElement('value');
$value = $display->appendChild($value);
$text = $doc->createTextNode($info['value']);
$text = $value->appendChild($text);

//商品现价 
$price = $doc->createElement('price');
$price = $display->appendChild($price);
$text = $doc->createTextNode($info['price']);
$text = $price->appendChild($text);

//商品折扣 
$rebate = $doc->createElement('rebate');
$rebate = $display -> appendChild($rebate);
$text = $doc->createTextNode($info['rebate']);
$text = $rebate->appendChild($text);

//已购买人数 
$bought = $doc->createElement('bought');
$bought = $display->appendChild($bought);
$text = $doc->createTextNode($info['bought']);
$text = $bought->appendChild($text);

echo "Saving all the document:\n";
echo $doc->saveXML() . "\n";
echo "Saving only the title part:\n";
//echo $doc->saveXML($title);

/*
<?xml version="1.0" encoding="utf-8" ?>
<urlset>
　<url> 
　　 <loc>http://www.meituan.com/shanghai/deal/shltw.html</loc>
　　 <!-- 商品URLurl 256 bytes ［必填］-->
　　 <data>
　　 　<display> 
　　 　　 <website>美团</website>
　　 　　 <!-- 站点名称 50 bytes ［必填］--> 
　　 　　 <siteurl>http://www.meituan.com/</siteurl>
　　 　　 <!-- 站点名称 256 bytes ［必填］--> 
　　 　　 <city>北京</city>
　　 　　 <!-- 城市名称（城市名称不需要附带省、市、区、县等字，如果是全国范围请指明：全国） 16 bytes ［必填］ -->
　　 　   <title>原价188元的“玛雅蛋糕”仅售49元！</title>>
　　 　　 <!-- 商品标题 512 bytes［必填］ --> 
　　 　　 <image>http://maya.xxx.com/xxx.gif</image>
　　 　　 <!-- 商品图片url 256 bytes ［必填］ --> 
　　 　　 <startTime>1275926400</startTime>
　　 　　 <!-- 商品开始时间 10 bytes ［必填］--> 
　　 　　 <endTime>1291910399</endTime>
　　 　　 <!-- 商品结束时间 10 bytes ［必填］--> 
　　 　　 <value>188.00</value>
　　 　　 <!-- 商品原价 12 bytes ［必填］--> 
　　 　　 <price>49.00</price>
　　 　　 <!-- 商品现价 12 bytes ［必填］--> 
　　 　　 <rebate>2.6</rebate>
　　 　　 <!-- 商品折扣 6 bytes ［必填］--> 
　　 　　 <bought>100</bought>
　　 　　 <!-- 已购买人数 10 bytes ［必填］-->
　　 　</display>
　　 </data>
　</url>
</urlset>
*/
