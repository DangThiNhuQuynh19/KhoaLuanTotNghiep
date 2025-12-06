<?php
/**
 * WebSocket Configuration
 * Automatically determines WebSocket URL based on environment
 */

// Allow configuration via environment variables (optional)
$wsHost = getenv('WEBSOCKET_HOST') ?: null;
$wsPort = getenv('WEBSOCKET_PORT') ?: 8080;
$wsPath = getenv('WEBSOCKET_PATH') ?: ''; // e.g., '/ws' if using reverse proxy

// Detect if we're on production or local
$isProduction = (
    isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'hanhphuc.site') !== false || 
     strpos($_SERVER['HTTP_HOST'], 'www.hanhphuc.site') !== false)
);

// Detect if connection is secure (HTTPS)
$isSecure = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// Configure WebSocket URL
if ($isProduction) {
    // Production environment
    $wsProtocol = $isSecure ? 'wss' : 'ws';
    
    // Use environment variable if set, otherwise use current host
    if (!$wsHost) {
        $wsHost = $_SERVER['HTTP_HOST'];
    }
    
    // Build WebSocket URL
    // If using reverse proxy (wsPath set), don't include port
    if ($wsPath) {
        define('WEBSOCKET_URL', "{$wsProtocol}://{$wsHost}{$wsPath}");
    } else {
        define('WEBSOCKET_URL', "{$wsProtocol}://{$wsHost}:{$wsPort}");
    }
} else {
    // Local development environment
    define('WEBSOCKET_URL', 'ws://localhost:8080');
}

/**
 * Function to get WebSocket URL for JavaScript
 * Returns the WebSocket URL that should be used in client-side code
 */
function getWebSocketUrl() {
    return WEBSOCKET_URL;
}
?>
