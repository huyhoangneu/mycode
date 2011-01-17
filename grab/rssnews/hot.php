<?php
include_once '/data/grab/spider/db/config.inc.php';
$hotnews_path = '/data/grab/data/news/';
$dics = array(
        'gn' => '国内',
        'gj' => '国际',
        'cj' => '财经',
        'ty' => '体育',
        'yl' => '娱乐',
        'kj' => '科技',
        'sh' => '社会'
        );
$dicstype = array(
        '国内' => 'gn',
        '国际' => 'gj',
        '财经' => 'cj',
        '体育' => 'ty',
        '娱乐' => 'yl',
        '科技' => 'kj',
        '社会' => 'sh'
        );
$rs = $mysql->getAll("SELECT * FROM `hotnews2`");
foreach ($rs as $k => $v) 
{
    if( 0 <= $k && $k <= 13) 
    {   
        $textall[] = '["'.$v['title'].'", "'.$v['url'].'", "", ["'.$v['category'].'", "'.$dicstype[$v['category']].'"]]';
        $focus[] = $v; 
    }   
}
//$textall[] = '["'.$avalue['name'].'", "'.$avalue['url'].'", "'.date('H:i', $avalue['date']).'", ["'.$avalue['c'].'", "'.$avalue['cy'].'"]]';
print_r($textall);
