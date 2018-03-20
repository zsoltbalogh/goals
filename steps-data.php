<?php
include "oauth-init.php";

$token = $oauth_storage->retrieveAccessToken("NokiaHealth");

$ns->refreshAccessToken($token);
$start = "2018-03-19";
$end = date("Y-m-d");
$result = json_decode($ns->request("v2/measure?action=getactivity&startdateymd=$start&enddateymd=$end"), true);

$steps = 0;
foreach ($result['body']['activities'] as $r) {
  $steps += $r['steps'];
}
$data['steps_count'] = $steps;
?>
