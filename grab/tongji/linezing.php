#!/data/opt/php/bin/php
<?PHP
define("COOKIE_FILE", tempnam('tmp','cookie') );
define('YT', date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-1, date('Y'))));
include_once '/data/grab/db/linezing.inc.php';
//include_once 'yahoo.inc.php';

function checklogin($username , $pwd)
{
	$username = trim($username);
	$pwd = trim($pwd);		
	$ch = curl_init( );	
	//referer=&webname=index&username=seacoastboy&password=zouzhihai&submit=%E7%99%BB%E5%BD%95
	curl_setopt( $ch, CURLOPT_URL, 'http://www.linezing.com/login.php');
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_HEADER, 1);
    $post_data = 'referer=&webname=index&username='. urlencode($username) .'&password='.urlencode($pwd).'&submit='.urlencode('登录');
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	//ob_start();
	curl_exec( $ch );
	//$contents = ob_get_contents();
	//ob_end_clean( );
	curl_close( $ch );
    //echo $contents;
//exit;

	$ch = curl_init( );
	curl_setopt( $ch,  CURLOPT_VERBOSE, 1); 
	curl_setopt( $ch, CURLOPT_URL, 'http://www.linezing.com');
	curl_setopt( $ch, CURLOPT_HEADER, 1);
	//curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIE_FILE );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	//ob_start();
	curl_exec( $ch );
	//$contents = ob_get_contents();
	//ob_end_clean( );
	curl_close( $ch );
	//echo $contents;


	$ch = curl_init( );
	curl_setopt( $ch,  CURLOPT_VERBOSE, 1); 
	curl_setopt( $ch, CURLOPT_URL, 'http://tongji.linezing.com/mystat.html');
	curl_setopt( $ch, CURLOPT_HEADER, 1);
	//curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIE_FILE );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	ob_start();
	curl_exec( $ch );
	$contents = ob_get_contents();
	ob_end_clean( );
	curl_close( $ch );
	//echo $contents;
	
	//模拟 退出 动作
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_VERBOSE, 1);
	curl_setopt( $ch, CURLOPT_URL, 'http://www.linezing.com/logout.php');
	curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIE_FILE);
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	curl_exec( $ch );
	curl_close( $ch);
	
	//file_put_contents('a.html',$contents);

	


	$user_info = array();
	preg_match_all("'<li onMouseOver=\"hlit\(this\)\" onMouseOut=\"delit\(this\)\"[^>]*?>(.*?)<\/li>'si",$contents, $tmp);
	
	foreach ($tmp['1'] as $id => $content) 
	{
		preg_match("'<div class=\"idt\">(.*?)<\/div>'",$content,$title);
		$user_info[$id][] = $title['1'];
		preg_match_all("'<tr style=\"cursor:pointer;\">(.*?)<\/tr>'si",$content,$tmp2);
		preg_match_all("'<td>(.*?)<\/td>'si", $tmp2['1']['3'], $number);
		$user_info[$id][] = $number['1']['2'];
		
	}
//print_r($user_info);
	return $user_info;
}

//$username = 'hot123net@yahoo.cn';
//$pwd = 'hot!@#ABC';

//$username = 'seacoastboy@yahoo.cn';
//$pwd = 'zouzhihai';
$username = 'hot123net@yahoo.cn';
$pwd = 'hot!@#ABC';
//print_r( checklogin($username , $pwd));exit;
//只考虑  本月的 数据 是否 全
function checkLog($userid)
{
	global $mysql;
	$Dyd = date('Y-m');
	$day = date('j');
	if(3 <= $day)
	{
		for($i = 1 ; $i <= $day -2; $i++)
		{
			$m = str_pad($i,  2, "0", STR_PAD_LEFT);
			$Dymd = $Dyd.'-'.$m;
			$row = $mysql->get_one("SELECT * FROM  `union`.`statistics` WHERE `userid` = '".$userid."' AND `time` = '".$Dymd."'");
			if (empty($row)) 
			{//echo "INSERT INTO `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userid."' , '-1', '".$Dymd."')" ."\n";
				$mysql->query("INSERT INTO `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userid."' , '-1', '".$Dymd."')" );
			}
		}
	}

}
$pwd = 'hot!@#ABC';
$username = 'hot123net@yahoo.cn';
//$yahoo = checklogin($username, $pwd);
//exit;
//checkLog('11');

$userinfos = $mysql->get_all("SELECT * FROM `union`.`member` WHERE `groupid` = '5' AND `yahooname` !='' AND `website_url` != '' AND `yahoopwd` != '' AND `flag` = '1' ");
//print_r($userinfos);exit;
if(!empty($userinfos))
{
	foreach ($userinfos as $k => $userinfo) 
	{
		$yahoo = checklogin( $userinfo['yahooname'], $userinfo['yahoopwd'] );
		//$yahoo = array(array('火热导航', '1235'),array('123', '1235'));
		if (!empty($yahoo)) 
		{
			checkLog($userinfo['userid']);
			foreach ($yahoo as $k => $v) 
			{
				if($v['0'] == $userinfo['stats_url'])
				{
					echo $sql = "replace into `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userinfo['userid']."' , '".$v['1']."', '".YT."')";
					//replace into `statistics` (`userid`, `yahooip`, `time`) VALUES ('5', '10', '2009-01-01')
					//replace into `paylog`(`userid`, `data`, `total`) VALUE ('4' , '2009-03','20')
					//30 1 * * * /apps/bin/cleanup.sh
					$mysql->query($sql);
					break;
				}
				else
				{

					echo $sql = "replace into `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userinfo['userid']."' , '-1', '".YT."')";
	         		}
				$mysql->query($sql);
			}
		}
		else 
		{
			$sql = "replace into `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userinfo['userid']."' , '-1', '".YT."')";
			$mysql->query($sql);
		}
	}
}

?>


