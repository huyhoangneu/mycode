#!/data/opt/php/bin/php
<?PHP
define("COOKIE_FILE", tempnam('tmp','cookie') );
define('YT', date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-1, date('Y'))));
include_once '/data/grab/db/yahoo.inc.php';
function checklogin($username , $pwd)
{
	$username = trim($username);
	$pwd = trim($pwd);
	$ch = curl_init( );
	curl_setopt( $ch, CURLOPT_URL, "http://tongji.cn.yahoo.com/" );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE );
	ob_start( );
	curl_exec( $ch );
	$contents = ob_get_contents( );
	ob_end_clean( );
	curl_close( $ch );
	$pattern = "/name=[\"|\\']*.challenge.[\"|\\']*\\s+value=[\"|\\']+(.*)[\"|\\']+>/";
	if ( !preg_match_all( $pattern, $contents, $result, PREG_PATTERN_ORDER ) )
	{
		return 0;
	}
	$challenge = trim( $result[1][0] );

	
	$ch = curl_init( );
	curl_setopt( $ch,  CURLOPT_VERBOSE, 1); 
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt( $ch, CURLOPT_SSLVERSION, 3);
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true);
    curl_setopt( $ch, CURLOPT_REFERER, "http://tongji.cn.yahoo.com/");
	//curl_setopt( $ch, CURLOPT_COOKIESESSION, true);
    /*
    curl_setopt( $ch, CURLOPT_SSLCERTTYPE, "DER");
    curl_setopt( $ch, CURLOPT_SSLKEYTYPE, "DER");*/
//    curl_setopt( $ch, CURLOPT_CAPATH, "./tmp");
    
  //  curl_setopt( $ch, CURLOPT_CAINFO, "/etc/ssl/cert/Equifax_Secure_CA.pem");
	
	curl_setopt( $ch, CURLOPT_URL, 'https://edit.bjs.yahoo.com/config/login');
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_HEADER, 1);
    $post_data = '.intl=cn&.src='. urlencode('https://member.cn.yahoo.com/cnreg/reginfo.html') .'&.done='. urlencode('http://tongji.cn.yahoo.com/mystat.html') .'&.challenge='.$challenge.'&login='.urlencode($username).'&passwd='.urlencode($pwd);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
//	ob_start();
	curl_exec( $ch );
//	$contents = ob_get_contents();
//	ob_end_clean( );
	curl_close( $ch );
//    echo $contents;
//exit;
	$ch = curl_init( );
	curl_setopt( $ch,  CURLOPT_VERBOSE, 1); 
	curl_setopt( $ch, CURLOPT_URL, 'http://tongji.cn.yahoo.com/mystat.html');
	curl_setopt( $ch, CURLOPT_HEADER, 1);
	//curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
    curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIE_FILE );
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	ob_start();
	curl_exec( $ch );
	$contents = ob_get_contents();
	ob_end_clean( );
	curl_close( $ch );
    



	$user_info = array();
	preg_match_all("'<li onMouseOver=\"hlit\(this\)\" onMouseOut=\"delit\(this\)\" class=\"(rgt)?\">(.*?)<\/li>'si",$contents, $tmp);
	
	//if(empty($tmp)) return $user_info;
	foreach ($tmp['2'] as $id => $content) 
	{
		preg_match("'<div class=\"idt\">(.*?)<\/div>'",$content,$title);
		$user_info[$id][] = $title['1'];
		preg_match_all("'<tr style=\"cursor:pointer;\">(.*?)<\/tr>'si",$content,$tmp2);
		preg_match_all("'<td>(.*?)<\/td>'si", $tmp2['1']['3'], $number);
		$user_info[$id][] = $number['1']['2'];
		
	}
/*
	$ch = curl_init( );
	curl_setopt( $ch,  CURLOPT_VERBOSE, 1); 
	curl_setopt( $ch, CURLOPT_URL, 'http://tongji.cn.yahoo.com/index.html');
	curl_setopt( $ch, CURLOPT_HEADER, 1);
	//curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIE_FILE);
    curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIE_FILE);
	curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10');
	ob_start();
	curl_exec( $ch );
	$contents = ob_get_contents();
	ob_end_clean( );
	curl_close( $ch );
    echo $contents;
	*/
	return $user_info;
}

//$username = 'hot123net@yahoo.cn';
//$pwd = 'hot!@#ABC';

//$username = 'seacoastboy@yahoo.cn';
//$pwd = 'zouzhihai';
//$username = 'zougood2002';
//$pwd = 'zouzhihai';
//print_r( checklogin($username , $pwd));exit;

$userinfos = $mysql->get_all("SELECT * FROM `union`.`member` WHERE `groupid` = '5' AND `yahooname` !='' AND `website_url` != '' AND `yahoopwd` != '' AND `flag` = '1' ");
//print_r($userinfos);exit;
if(!empty($userinfos))
{
	foreach ($userinfos as $k => $userinfo) 
	{
		$yahoo = checklogin( $userinfo['yahooname'], $userinfo['yahoopwd'] );
		if (!empty($yahoo)) 
		{
			foreach ($yahoo as $k => $v) 
			{
				if($v['0'] == $userinfo['stats_url'])
				{
					echo $sql = "replace into `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userinfo['userid']."' , '".$v['1']."', '".YT."')";
					//replace into `statistics` (`userid`, `yahooip`, `time`) VALUES ('5', '10', '2009-01-01')
					//replace into `paylog`(`userid`, `data`, `total`) VALUE ('4' , '2009-03','20')
					//30 1 * * * /apps/bin/cleanup.sh
				//	$mysql->query($sql);
				}
				else
				{

					echo $sql = "replace into `union`.`statistics` (`userid` ,`yahooip` ,`time`) VALUES ('".$userinfo['userid']."' , '-1', '".YT."')";
	         		}
				$mysql->query($sql);
			}
		}
	}
}
?>

