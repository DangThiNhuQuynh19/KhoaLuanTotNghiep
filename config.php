<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('562721365946-o3phmrsbmp9tvnp3gb03i7q0pr2o4cg4.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://hanhphuc.site/?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");