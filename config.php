<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('562721365946-9jcabau5kh6s66b2v6i9u885j990gj9k.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://hanhphuc.site/?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");
