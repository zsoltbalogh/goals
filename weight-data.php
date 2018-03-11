<?php
//{"status":0,"body":{"updatetime":1515003660,"more":1,"measuregrps":[{"grpid":996764374,"attrib":0,"date":1514957099,"category":1,"measures":[{"value":88716,"type":1,"unit":-3}]},{"grpid":995796767,"attrib":0,"date":1514868785,"category":1,"measures":[{"value":88665,"type":1,"unit":-3}]},{"grpid":995056915,"attrib":0,"date":1514790646,"category":1,"measures":[{"value":89530,"type":1,"unit":-3}]}],"timezone":"Europe\/Budapest"}}

include_once "oauth-init.php";

$token = $oauth_storage->retrieveAccessToken("NokiaHealth");

$ns->refreshAccessToken($token);
$result = json_decode($ns->request("measure?action=getmeas&limit=5&meastype=1"), true);

$count = 0;
$weight = 0;
foreach ($result['body']['measuregrps'] as $measure) {
  if ($count && ($measure['date'] < (time() - 3600*24*5))) break;
  $count++;
  $weight += $measure['measures'][0]['value']/1000;
}
$data['weight_now'] = round($weight/$count, 1);
$data['weight_goal'] = 81.0;
