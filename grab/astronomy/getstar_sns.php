<?php
define('ROOT_PATH', dirname(__FILE__));
include_once(ROOT_PATH.'/config.php');
include_once(ROOT_PATH.'/function1.php');
set_time_limit(0);
$stait = array(
        '0' => array(
            '时间' => '3月21-4月19',
            '吉祥物' => '凤凰',
            '吉祥金属' => '铁',
            '吉祥宝石' => '红宝石',
            '喜欢颜色' => '红色',
            '吉祥数字' => '9、18、27、36',
            '吉祥日' => '星期二'
            ),
        '1' =>array(
           '时间' => '4月20-5月20',
           '吉祥物' => '圣甲虫像',
           '吉祥金属' => '铜',
           '吉祥宝石' => '粉红色',
           '喜欢颜色' => '红色',
           '吉祥数字' => '6、15、24、33',
           '吉祥日' => '星期五'
            ),
         '2' =>array(
           '时间' => '5月21-6月21',
           '吉祥物' => '三角形',
           '吉祥金属' => '白金',
           '吉祥宝石' => '玛瑙',
           '喜欢颜色' => '条纹、多色、灰色',
           '吉祥数字' => '5、14、23、32',
           '吉祥日' => '星期三'
            ),
        '3' =>array(
           '时间' => '6月22-7月22',
           '吉祥物' => '银质新月',
           '吉祥金属' => '银',
           '吉祥宝石' => '晶体和珍珠',
           '喜欢颜色' => '白色和珠光色',
           '吉祥数字' => '2、11、20、29',
           '吉祥日' => '星期四'
            ),
        '4' =>array(
           '时间' => '7月23-8月22',
           '吉祥物' => '太阳或金质纪念章',
           '吉祥金属' => '金',
           '吉祥宝石' => '钻石',
           '喜欢颜色' => '艳黄、浅黄、褐色',
           '吉祥数字' => '1、10、19、28',
           '吉祥日' => '星期日'
            ),
        '5' =>array(
                '时间' => '8月23-9月22',
                '吉祥物' => '乌鸦',
                '吉祥金属' => '水银',
                '吉祥宝石' => '碧玉',
                '喜欢颜色' => '灰色',
                '吉祥数字' => '5、14、23、32',
                '吉祥日' => '星期五'
            ),
    '6' =>array(
           '时间' => '9月23-10月23',
           '吉祥物' => '心形物',
           '吉祥金属' => '铜',
           '吉祥宝石' => '纯绿宝石',
           '喜欢颜色' => '蓝、菘蓝',
           '吉祥数字' => '6、15、24、33',
           '吉祥日' => '星期五'
            ),
    '7' =>array(
           '时间' => '10月24-11月22',
           '吉祥物' => '龙',
           '吉祥金属' => '铁',
           '吉祥宝石' => '红宝石',
           '喜欢颜色' => '红色',
           '吉祥数字' => '9、18、27、36',
           '吉祥日' => '星期二'
            ),
    '8' =>array(
           '时间' => '11月23-12月21',
           '吉祥物' => '星形物',
           '吉祥金属' => '青铜',
           '吉祥宝石' => '绿松石',
           '喜欢颜色' => '蓝色和紫色',
           '吉祥数字' => '3、12、21、30',
           '吉祥日' => '星期四'
            ),
    '9' =>array(
           '时间' => '12月22-1月19',
           '吉祥物' => '植物',
           '吉祥金属' => '铅',
           '吉祥宝石' => '缟玛瑙',
           '喜欢颜色' => '黑色或海蓝色',
           '吉祥数字' => '8、16、26、35',
           '吉祥日' => '星期六'
            ),
    '10' =>array(
           '时间' => '1月20-2月18',
           '吉祥物' => '竖琴螺',
           '吉祥金属' => '铂或镭',
           '吉祥宝石' => '紫晶',
           '喜欢颜色' => '蓝黑色',
           '吉祥数字' => '4、13、22、31',
           '吉祥日' => '星期六'
            ),
    '11' =>array(
           '时间' => '2月19-3月20',
           '吉祥物' => '马头鱼尾怪兽',
           '吉祥金属' => '铂或合金',
           '吉祥宝石' => '海蓝宝石',
           '喜欢颜色' => '青绿和水色',
           '吉祥数字' => '7、16、 25、34',
           '吉祥日' => '星期四'
            )
);
//配置目录
if(!is_dir(ROOT))	mkdir(ROOT);

if(!is_dir(ROOT . 'sns'))	mkdir(ROOT . 'sns');//天
/*
if(!is_dir(ROOT . 'tomorrow'))	mkdir(ROOT . 'tomorrow');//明天
if(!is_dir(ROOT . 'week'))	mkdir(ROOT . 'week');//本周
if(!is_dir(ROOT . 'month'))	mkdir(ROOT . 'month');// 本月
if(!is_dir(ROOT . 'year'))	mkdir(ROOT . 'year');// 本年
if(!is_dir(ROOT . 'love'))	mkdir(ROOT . 'love');// 爱情
*/
/**/
// create today star files
#array -> all
$all = array();
foreach ($star_array as $key => $value) 
{
	//这里是 天的 星座 
	/*二次 改进*/
    $all[$key]['today'] = get_day($key);
}
// create tomorrow star files
foreach ($star_array as $key => $value) 
{
	$all[$key]['tomorrow'] = get_day($key . '_1');
}

// create week star files
foreach ($star_array as $key => $value) 
{
	$all[$key]['week'] = get_week($key);
}

// create month star files
foreach ($star_array as $key => $value) 
{
	$all[$key]['month'] = get_month($key);
}

// create year star files
foreach ($star_array as $key => $value) 
{
	$all[$key]['year'] = get_year($key);
}
/**/

// create love star files
foreach ($star_array as $key => $value) 
{
    $all[$key]['love'] = get_love($key);
}
$trans = array("\r\n" => '<br/>', "\n" => '<br/>', "\r" => '<br/>');
#$order   = array("\r\n");
#$order   = array("\r\n", "\n", "\r");
#$replace = '<br/>';
foreach($all AS $k => $v)
{
    $filename = $key.'.js';
    $js = '';
    $js .= "var yuncheng={";
    foreach($stait[$k] as $k0 => $v0)
    {
        $js .= "'".$k0."':'".$v0."',";
    }
    $js .= "'今日运程':{";
    foreach($v['today']['star'] AS $k1 => $v1)
    {
        $tmp[] = "'".trim($v1['title'])."':'".$v1['star']."'";
    }
    foreach($v['today']['content'] AS $k2 => $v2)
    {
        $tmp[] = "'".trim($v2['title'])."':'".$v2['content']."'";
    }
    $tmp[] = "'描述':'".rtrim(strtr($v['today']['comment'], $trans))."'";
    #$tmp[] = "'描述':'".rtrim(str_replace($order, $replace, $v['today']['comment']))."'";
    /*
    '综合运势' : 2,
    '爱情运势' : 3,
    '幸运颜色' : '红色',
    '工作状况' : '',
    '理财投资' : '',
    '幸运数字' : '',
    '健康指数' : '',
    '商谈指数' : '',
    '速配星座' : '',
    '描述' : '**********************xxoo***************',
    */
    $time = explode(':', $v['today']['date']);
    $tmp[] = "'有效日期' : '".$time['1']."'";
    $js .= implode(',', $tmp);
    unset($tmp, $time);
    $js .= '},';
    /*
       '明日运程' : 
       {
       '综合运势' : 2,
       '爱情运势' : 3,
       '幸运颜色' : '红色',
       '工作状况' : '',
       '理财投资' : '',
       '幸运数字' : '',
       '健康指数' : '',
       '商谈指数' : '',
       '速配星座' : '',
       '描述' : '**********************xxoo***************',
       '有效日期' : '2010/02/06'
       },
    */
    $js .= "'明日运程' : {";
    foreach($v['tomorrow']['star'] AS $k3 => $v3)
    {
        $tmp[] = "'".trim($v3['title'])."':'".$v3['star']."'";
    }
    foreach($v['tomorrow']['content'] AS $k3 => $v3)
    {
        $tmp[] = "'".trim($v3['title'])."':'".$v3['content']."'";
    }
    #$tmp[] = "'描述':'".rtrim(str_replace($order, $replace,$v['tomorrow']['comment']))."'";
    $tmp[] = "'描述':'".rtrim(strtr($v['tomorrow']['comment'], $trans))."'";
    $time = explode(':', $v['tomorrow']['date']);
    $tmp[] = "'有效日期':'".$time['1']."'";
    $js .= implode(',', $tmp);
    unset($tmp, $time);
    $js .= '},';
    /*
       '本周运程' : 
       {
       '整体运势' : {'数字' : 2, '描述' : 'xxoo'},
       '健康运势' : {'数字' : 2, '描述' : 'xxoo'},
       '工作学业运' : {'数字' : 2, '描述' : 'xxoo'},
       '性欲指数' : {'数字' : 2, '描述' : 'xxoo'},
       '红心日' : {'数字' : 2, '描述' : 'xxoo'},
       '黑梅日' : {'数字' : 2, '描述' : 'xxoo'},
       '有效日期' : '2010/02/06 - 2010/02/12'
       },
    */
    $js .= "'本周运程' : {";
    foreach($v['week']['info'] AS $k4 => $v4)
    {
        if($k4 == 6 or $k4 == 7)
        {
        $tmp[] = "'".trim($v4['title'])."':{'数字':'".$v4['date']."','描述':'".rtrim($v4['content'])."'}";
        }
        else
        {
        $tmp[] = "'".trim($v4['title'])."':{'数字':".$v4['star'].",'描述':'".rtrim($v4['content'])."'}";
        }
    }
    $time = explode(':', $v['week']['date']);
    $tmp[] = "'有效日期':'".$time['1']."'";
    $js .= implode(',', $tmp);
    unset($tmp, $time);
    $js .= '},';
    /*
       '本月运程' : {
       '整体运势' : {'数字' : 2, '描述' : 'xxoo'},
       '爱情运势' : {'数字' : 2, '描述' : 'xxoo'},
       '投资理财运' : {'数字' : 2, '描述' : 'xxoo'},
       '解压方式' : 'xxoo',
       '开运小秘诀' : '还是xxoo',
       '有效日期' : '2010/02/06 - 2010/03/06'
       },
    */
    $js .= "'本月运程':{";
    foreach($v['month']['info'] AS $k5 => $v5)
    {
        if($k5 == 4 or $k5 == 5)
        {
            $tmp[] = "'".trim($v5['title'])."':'".$v5['content']."'";
        }
        else
        {
            $tmp[] = "'".trim($v5['title'])."':{'数字':".$v5['star'].",'描述':'".rtrim(strtr($v5['content'], $trans))."'}";
            #$tmp[] = "'".trim($v5['title'])."':{'数字':".$v5['star'].",'描述':'".rtrim(str_replace($order,
            #$replace,$v5['content']))."'}";
        }
    }
    $time = explode(':', $v['month']['date']);
    $tmp[] = "'有效日期':'".$time['1']."'";
    $js .= implode(',', $tmp);
    unset($tmp, $time);
    $js .= '},';
    /*
       '本年运程' : {
       '描述' : '2010年是白羊座起伏变化、动力十足、蠢蠢欲动的一年。',
       '整体运势' : {'数字' : 2, '描述' : 'xxoo'},
       '功课学业' : {'数字' : 2, '描述' : 'xxoo'},
       '工作职场' : {'数字' : 2, '描述' : 'xxoo'},
       '金钱理财' : {'数字' : 2, '描述' : 'xxoo'},
       '恋爱婚姻 ' : {'数字' : 2, '描述' : 'xxoo'},
       '有效日期' : '2010/02/06 - 2011/02/06'
       },
    */
    $js .= "'本年运程':{";
    $tmp[] = "'描述':'".$v['year']['summary']."'";
    foreach($v['year']['info'] AS $k6 => $v6)
    {
        $tmp[] = "'".trim($v6['title'])."':{'数字':".$v6['star'].",'描述':'".rtrim(strtr($v6['content'], $trans))."'}";
        #$tmp[] = "'".trim($v6['title'])."':{'数字':".$v6['star'].",'描述':'".rtrim(str_replace($order,$replace,$v6['content']))."'}";
    }
    $time = explode(':', $v['year']['date']);
    $tmp[] = "'有效日期':'".$time['1']."'";
    $js .= implode(',', $tmp);
    unset($tmp, $time);
    $js .= '},';
    /*
       '爱情运程' : {
       '描述' : '如果你是苹单身还没有被绑住的牡羊，那麽，谈到恋爱这方面，务必切记，千千万万不可以随便就纵容自己花心，见一个爱一个！因为牡羊的基本性格以及行为模式',
       '星座爱情' : {"w":,"man":},
       '有效日期' : '2010/02/06 - 2010/02/12'
       }
    */
    $js .= "'爱情运程':{";
    $js .= "'描述':'".strtr($v['love']['content'], array("\r\n" => '<br/>', "\r" => '<br/>', "\n" => '<br/>'))."',";
    #$js .= "'描述':'".str_replace($order, $replace,$v['love']['content'])."',";
    $js .= "'星座爱情':{'girl':'".trim($v['love']['girl'])."','boy':'".trim($v['love']['boy'])."'},";
    $time = explode(':', $v['love']['date']);
    $js .= "'有效日期':'".$time['1']."'}";
    unset($time); 
    $js .= '}';
    if(!empty($js))
    {
        file_put_contents(ROOT . 'sns/'.$k.'.js', $js);
    }
    //echo $js;exit;
}
?>
