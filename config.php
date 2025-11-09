<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('464279125155-qqo3akm7mlq3lo8jllfvntviercjef78.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://hanhphuc.site/KhoaLuanTotNghiep?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");