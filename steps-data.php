<?php
include_once "oauth-init.php";

$token = $oauth_storage->retrieveAccessToken("NokiaHealth");
$ns->refreshAccessToken($token);
$result = json_decode($ns->request("v2/measure?action=getactivity&startdateymd=2018-02-04&enddateymd=2018-02-10"), true);
echo "<pre>";
print_r($result);
?>
