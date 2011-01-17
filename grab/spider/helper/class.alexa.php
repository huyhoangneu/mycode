<?php
define ('ALEXA_ERROR_NODATA', 0);

 
/** 
 * @author [YS.PRO] 
 * @copyright Copyright &copy; 2009, [YS.PRO] 
 * http://ys-pro.com 
 * @version 0.2 
 */  
class Alexa {  
  
    const CURL_TIMEOUT = 20;  
    const ALEXA_SITE_INFO_URL = 'http://www.alexa.com/siteinfo/';  
  
    private $domain = NULL;  
  
    public function __construct($domain = NULL) 
	{  
        if (!is_null($domain)) 
		{
			$parn =array('http://'=>'','https://'=>'','mms://'=>'');
			$this->domain = strtr($domain,$parn);
            //$this->domain = $domain;  
        } 
		else 
		{  
            throw new Exception('You must pass domain name to constructor!');  
        }  
    }  
  
    public function setDomain($domain) 
	{  
        $this->domain = $domain;  
    }  
  
    public function getAlexaRank() 
	{  
        $response = $this->get(self::ALEXA_SITE_INFO_URL . $this->domain);  
        // parse string with alexa ranking info  
        $regexp = '#<div class="data .+?">(.*?)</div>#si';  
        preg_match($regexp, $response, $matches);  
        if (!isset($matches[1])) 
		{  
            return FALSE;  
        }  
        preg_match('#[\d,]+#s', $matches[1], $m);  
        if (!isset($m[0])) 
		{  
            return FALSE;  
        }  
        $rank = $m[0];  
        // delete commas  
        $rank = str_replace(',', '', $rank);  
        return (int) $rank;  
    }  
  
    protected function get($url) 
	{  
        $hCurl = curl_init($url);  
        curl_setopt($hCurl, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);  
        curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, TRUE);  
        return curl_exec($hCurl);  
    }  
}
?>