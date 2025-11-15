<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('464279125155-dshf51l06kgncpfe3vqn6k3v9uvel5jq.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://hanhphuc.site/?action=logingoogle');
$client->addScope("email");
$client->addScope("profile");