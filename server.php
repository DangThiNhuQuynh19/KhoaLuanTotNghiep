<?php
require_once('vendor/autoload.php');
require_once('Chat.php'); 
require_once('Assets/config.php');

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

$port = WEBSOCKET_PORT;
$host = WEBSOCKET_HOST;

// Khởi động server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    $port // Cổng WebSocket
);

echo "WebSocket server running on ws://{$host}:{$port}\n";
$server->run();
