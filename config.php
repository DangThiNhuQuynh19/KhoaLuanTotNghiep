<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('562721365946-fnu3fn3uultptrqneeg01p890c5ajnqf.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://hanhphuc.site/?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");
