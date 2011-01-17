<html><body><div id="cc">
<?php
foreach ($info['info'] as $key => $value) {
	if ($key == 6 || $key == 7) {
		$title = $value['title'];
		$date = $value['date'];
		$content = $value['content'];
		echo <<<EOF
<div class="year"><ul><li><div class="startl2">$title : $date</div><li>$content</li></ul></div>
EOF;
}else {
	$title = $value['title'];
	$star = $value['star'];
	$content = $value['content'];
	$a = '';
	for ($i = 0; $i < $star; $i++){
		$a .= "<div></div>";
	}
	echo <<<EOF
<div class="year"><ul><li><div class="startl2">$title :</div><div class="star2">$a</div></li><li>$content</li></ul></div>
EOF;
}
}
?>
<p class=gray><?php echo $info['date'];?></p>
</div></body></html>