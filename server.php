<?php
require_once('vendor/autoload.php');
require_once('env.php');
require_once('Chat.php'); 

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

// Get port from environment or use default
$port = (int)config('websocket.port', 8080);

// Khởi động server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    $port // Cổng WebSocket từ config
);

echo "WebSocket server running on port {$port}\n";
echo "Press Ctrl+C to stop\n";
$server->run();
