<?php
include_once "oauth-init.php";

if (!empty($_GET['go']) && $_GET['go'] === 'go') {
    header('Location: '.$ns->getAuthorizationUri());
    die();
} elseif (!empty($_GET['code'])) {
    $state = isset($_GET['state']) ? $_GET['state'] : null;
    $ns->requestAccessToken($_GET['code'], $state);

    $token = $oauth_storage->retrieveAccessToken("NokiaHealth");
    $ns->refreshAccessToken($token);
    $result = json_decode($ns->request("v2/measure?action=getactivity&startdateymd=2018-02-04&enddateymd=2018-02-10"), true);

    print_r($result);
}
?>
