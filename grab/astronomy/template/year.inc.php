<?php
$summary = $info['summary'];
echo "<html><body><div id=\"cc\"><p style=\"text-align: center;\"><b>$summary</b></p>";
foreach ($info['info'] as $value) {
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
?>
<p class=gray><?php echo $info['date'];?></p>
</div></body></html>