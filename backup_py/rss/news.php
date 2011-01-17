<?php
ini_set('default_socket_timeout','600');
include 'lastRSS0.9.1.php';
$rss = new lastRSS;
$rss->CDATA = 'content';
$news = '';
$encoding = $pubDate = 0;
$def = $rss->Get('http://sports.qq.com/isocce/rss_isocce.xml');
//$def = $rss->Get('http://home.focus.cn/common/xml/rss/news/hot.php');
if(!empty($def) && !empty($def['items']))
{
    $items = $def['items'];
    foreach($items AS $k => $item)
    {
        $item = array_map('trim', $item);
        if($item['title'] && $item['link'] && $item['description'])
        {
            $news[$k]= array(
                    'title'=> $encoding ? iconv('GBK', 'UTF-8', $item['title']) : $item['title'],
                    'link' => $item['link'],
                    //'description' => iconv('GBK', 'UTF-8', $item['description']),
                    'pubDate' => $pubDate ? date('Y-m-d H:i:s', strtotime($item['pubDate'])) : $item['pubDate']
                    );
        }
    }
}
//$contents = iconv('GBK', 'UTF-8', $contents);
$news = sortByMultiCols($news, array('pubDate' => SORT_DESC));
print_r($news);
//print_r(json_encode($news));


function sortByMultiCols($rowset, $args)
{
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir) 
    {
        foreach ($rowset as $offset => $row) 
        {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) { return $rowset; }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}
