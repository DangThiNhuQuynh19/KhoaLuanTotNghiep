<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('946684689164-3nqigms1tv54mcqr11mhfm819dpon7aa.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://hanhphuc.site/?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");