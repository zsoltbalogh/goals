<?php
include_once "init.php";
include_once "vendor/autoload.php";
include_once "oauth_storage.php";
include_once "NokiaHealth.php";

use OAuth\OAuth2\Service\NokiaHealth;
use OAuth\Common\Consumer\Credentials;

$user = "zsolt.balogh";

$credentials = new Credentials(
    "0fba995ab12b9857ad150b0d06ec09dd6cd00bbd2d90e37c5c0c2028ce73aa7e",
    "955a7581465541f6820d670db893b3f3854437f1988f1fe9133fb435b005e086",
    'http://dash.bzz.hu/nokia-auth.php'
);

$serviceFactory = new \OAuth\ServiceFactory();
$uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
$oauth_storage = new MixedStorage($conn, 1);

$ns = $serviceFactory->createService("NokiaHealth", $credentials, $oauth_storage, array('user.info', 'user.metrics', 'user.activity'));

?>
