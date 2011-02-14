<?php
for($i=0; $i <=255; $i++)
{
$hash = md5($i);
$file =  substr($hash, 0, 2).'/'. substr($hash, 2, 2).'/'. substr($hash, 4, 2).'/'.substr($hash, 6, 2);
$dir = dirname($file);
createDirs('/home/zouzhihai/golang/dir/'. $dir);
}
function createDirs($dir, $mode = 0777) {
    if (empty($dir)) return false;
    if (!is_dir($dir)) {
        createDirs(dirname($dir), $mode);
        $parent_dir = dirname($dir);
        if (is_writable($parent_dir)) {
            return mkdir($dir, $mode);
        } else {
            die(sprintf("Directory %s's parent directory %s unable write, create directory failed!", $dir, $parent_dir));
            return false;
        }
    }
    return true;
}
