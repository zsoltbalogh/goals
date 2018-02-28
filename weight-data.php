<?php
//{"status":0,"body":{"updatetime":1515003660,"more":1,"measuregrps":[{"grpid":996764374,"attrib":0,"date":1514957099,"category":1,"measures":[{"value":88716,"type":1,"unit":-3}]},{"grpid":995796767,"attrib":0,"date":1514868785,"category":1,"measures":[{"value":88665,"type":1,"unit":-3}]},{"grpid":995056915,"attrib":0,"date":1514790646,"category":1,"measures":[{"value":89530,"type":1,"unit":-3}]}],"timezone":"Europe\/Budapest"}}

include_once "NokiaHealth.php";



$weight_url = "http://api.health.nokia.com/measure?action=getmeas&userid=12889548&meastype=1&limit=5";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $weight_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);

$weight = 0;
for ($i = 0; $i < 5; $i++) {
    $p = strpos($content, "\"value\":");
    $weight_str = substr($content, $p + 8, 5); 
    $weight += $weight_str/1000;
    $content = substr($content, $p + 8);
}
$weight = round($weight/5, 1);
$weight_goal = 81;

