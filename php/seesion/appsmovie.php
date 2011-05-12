<?php
if(count($argv) > 3)
{
    exit;
}
else
{
    $url = $argv[1];
    $pid = $argv[2];
//print_r($argv);exit;
}
getinfos($url, $pid);
function getinfos($url, $pid)
{
	$pageurl = "http://v.youku.com/v_vpofficiallist/page_%s_type_%s_showid_%s_id_%s.html?__rt=1&__ro=vpofficiallist";
	$contents = getHtmlContents($url);
	if(preg_match("'id_(.*?).html'is", $url, $start))
	{
		if(!empty($start[1]))
		{
			$movie[$start[1]] = '01集';
		}
		else
		{
			exit;
		}
	}
	else
	{
		exit();
	}
	//print_r($movie);
	//get pages count
	preg_match("'<ul\s+class=\"pages\">(.*?)</ul>'is", $contents, $block);
	if(!empty($block[1]))
	{
		$pagesinfo = $block[1];
		preg_match_all("'<li[^>]*>(.*?)</li>'is", $pagesinfo, $tmp);
		if(!empty($tmp[1]))
		{
			//print_r($tmp[1]);exit;
			$tmpblock = $tmp[1][1];
			if(!empty($tmpblock))
			{
				//get id showid type
				preg_match("'page_(\d+)_type_(\d+)_showid_(\d+)_id_(\d+)\.html'is", $tmpblock, $info);
				if(count($info) == 5)
				{
					$type = $info[2];
					$showid = $info[3];
					$id = $info[4];
					$count = count($tmp[1]);
					if($type && $showid && $id && $count)
					{
						//print_r($tmp[1]);exit;
						if($count == 1)
						{
						}
						elseif($count > 1)
						{
							for($i =1; $i <= $count; $i++)
							{
								//array_merge
								$movie = array_merge($movie, getUrlinfos(sprintf($pageurl, $i,$type, $showid, $id)));
								echo sprintf($pageurl, $i, $type, $showid, $id)."\n";
								//echo $i."\n";
							}
							foreach($movie AS $k => $v)
							{
								$v = str_replace("集", '', $v);
								echo "INSERT INTO `movie_info_data` (`mid`, `movie_url`, `episode`) VALUES ('".$pid."', '".$k."', '".intval($v)."');"."\n";
								//echo $k.' '.$v."\n";
							}
							//print_r($movie);exit;
						}
					}
				}
				else
				{
					echo "get id or showid or type fail!";
				}
			}
			
		}
	}
	else
	{
		echo 'down html fails';
	}
}
function getUrlinfos($url)
{
    if($url)
    {
        $contents = getHtmlContents($url);
        if(!empty($contents))
        {
            if(preg_match("'<ul\s+class=\"pack_number\"[^>]*>(.*?)</ul>'is", $contents, $block))
            {
                $block = $block[1];
                if(!empty($block))
                {
                    if(preg_match_all("'href=\"http://v.youku.com/v_show/id_(.*?).html\">(.*?)</a>'is", $block, $infos))
                    {
                        if($infos[1] && $infos[2])
                        {
                            foreach($infos[0] AS $k => $v)
                            {
                                $tmp[$infos[1][$k]] = $infos[2][$k];
                            }
                            return $tmp? $tmp : false;
                            print_r($tmp);exit;
                        }
                    }
                }
            }
        }
    }
    return false;
}

function getHtmlContents($url)
{
    $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/20090729 Firefox/3.5.2';
    $data = '';
    if( !isset($ch) && $url)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_USERAGENT, $agent );
    }
    $data = curl_exec ( $ch );
    return $data;
}
