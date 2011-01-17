<html><body><div id=cc><ul class="month">
<?php
foreach ($info['star'] as $value) {
	$title = $value['title'];
	$star = $value['star'];
	echo "<li><div class=startl2>$title: </div><div class=star2>";
	for ($i = 0; $i < $star; $i++) {
		echo "<div></div>";
	}
	echo "</div></li>";
}

foreach ($info['content'] as $value) {
	$title = $value['title'];
	$content = $value['content'];
	echo '<li>' . $title . ': ' . $content . '</li>'; 
}
?>
</ul><p><b></b><br>
<?php echo preg_replace("/\n/", '<br>', $info['comment'])?>
</p>
<p class=gray><?php echo $info['date'];?></p>
</div></body></html>