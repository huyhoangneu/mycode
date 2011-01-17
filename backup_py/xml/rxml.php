<?php
$reader = new XMLReader();
$reader->open('http://www.meituan.com/api/v1/beijing/deals');
while ($reader->read()) 
{
    switch ($reader->nodeType)
    {
        case (XMLREADER::ELEMENT):
            if ($reader->name == "deal_url")
            {
                $reader->read();
                $code = trim($reader->value);
                echo "$code\n";
                break;
            }
            if ($reader->name == "small_image_url")
            {
                 $reader->read();
                 $wname = trim( $reader->value );
                 echo "$wname\n";
                 break;
            }
            if ($reader->name == "Camp")
            {
                 $camp = trim($reader->getAttribute("ID"));
                 echo "$camp\n";
                 break;
            }
     }
}
exit;
$reader = new XMLReader();
$reader->open('http://www.meituan.com/api/v1/beijing/deals');
$assoc = xml2assoc($reader);
while ($reader->read())
{
    if($reader->name == 'deal_url')
    {
        echo $reader->name."\n";
        if($reader->hasValue)
        {
            echo $reader->value."\n";
        }
    }
    /*
    echo $reader->name;
    if ($reader->hasValue)
    {
        echo ": " . $reader->value;
    }
    echo "\n";
    */
}
//$all = $xml->getAttribute('*');
$reader->close();
print_r($assoc);
function xml2assoc($xml) {
    $tree = null;
    while($xml->read())
        switch ($xml->nodeType) {
            case XMLReader::END_ELEMENT: return $tree;
            case XMLReader::ELEMENT:
            $node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : xml2assoc($xml));
            if($xml->hasAttributes)
                while($xml->moveToNextAttribute())
                    $node['attributes'][$xml->name] = $xml->value;
                    $tree[] = $node;
                    break;
                    case XMLReader::TEXT:
                    case XMLReader::CDATA:
                    $tree .= $xml->value;
        }
        return $tree;
}

