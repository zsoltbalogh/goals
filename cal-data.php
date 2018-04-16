<?php
include "oauth-init.php";

$token = $oauth_storage->retrieveAccessToken("NokiaHealth");

$ns->refreshAccessToken($token);
$start = date("Y-m-d", strtotime("this Monday"));
$end = date("Y-m-d");
$result = json_decode($ns->request("v2/measure?action=getactivity&startdateymd=$start&enddateymd=$end"), true);

$cals = 0;
foreach ($result['body']['activities'] as $r) {
  $cals += $r['calories'];
}
$data['cal_count'] = $cals;
?>
