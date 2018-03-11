<?php
include_once "oauth-init.php";

$token = $oauth_storage->retrieveAccessToken("NokiaHealth");

$ns->refreshAccessToken($token);
$result = json_decode($ns->request("measure?action=getmeas&limit=5&meastype=1"), true);
echo "<pre>";
print_r($result);
?>
