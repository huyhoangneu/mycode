<?php
$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml ->startDocument('1.0" encoding="UTF-8');
$xml->startElement('urlset');
$xml->writeAttribute("version","1.0");

$xml->startElement('url');

$xml->startElement('loc');
$xml->text('http://www.meituan.com/shanghai/deal/shltw.html');
$xml->endElement();//end loc

$xml->startElement('data');
$xml->startElement('display');

$xml->startElement('website');
$xml->text('美团');
$xml->endElement();//end website

$xml->endElement();//end display
$xml->endElement();//end data

$xml->endElement();//end url

$xml->endElement();//end urlset
$xml->endDocument();
echo $xml->flush();
