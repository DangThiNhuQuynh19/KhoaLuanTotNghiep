<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('464279125155-1nghqp2efppp05jqhphjsasa2e2t7jsg.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://hanhphuc.site/?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");