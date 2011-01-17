<?php
$date = $info['date'];
$content = explode("\n", $info['content']);
foreach ($content as $a) {
	$b .= '<p>' . $a . '</p>'; 
}
$boy = $info['boy'];
$girl = $info['girl'];
echo <<<EOF
<html><body><div id=cc><p class=gray>$date</p>
<div class=love>$b<div class=warn><b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b><p><span style="border-right:1px dashed #c60;color:#c69">$girl</span><span>$boy</span></p></body></html>
EOF;
?>