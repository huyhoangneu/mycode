<?php
echo file_get_contents('http://www.hfbus.cn');exit; 
$headers = get_headers('http://www.hfbus.cn/');
print_r($headers);exit;

