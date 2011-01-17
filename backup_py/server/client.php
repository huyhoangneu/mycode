<?php
$fp = fsockopen("localhost", 9999, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    $out = "211.100.36.235\n";
    fwrite($fp, $out);
    echo fgets($fp);
    fclose($fp);
}

