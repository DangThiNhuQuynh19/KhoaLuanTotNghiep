<?php
/**
 * WebSocket Configuration
 * Automatically determines WebSocket URL based on environment
 */

// Allow configuration via environment variables (optional)
$wsHost = getenv('WEBSOCKET_HOST');
$wsHost = ($wsHost === false) ? null : $wsHost; // Normalize false to null
$wsPort = getenv('WEBSOCKET_PORT');
$wsPort = ($wsPort === false) ? 8080 : (int)$wsPort; // Default to 8080, cast to int
$wsPath = getenv('WEBSOCKET_PATH');
$wsPath = ($wsPath === false) ? '' : $wsPath; // Normalize false to empty string

// Define allowed production hosts (whitelist for security)
$allowedProductionHosts = [
    'hanhphuc.site',
    'www.hanhphuc.site'
];

// Detect if we're on production or local
$currentHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
// Remove port from host if present
$currentHostWithoutPort = preg_replace('/:\d+$/', '', $currentHost);

$isProduction = in_array($currentHostWithoutPort, $allowedProductionHosts, true);

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
    
    // Use environment variable if set, otherwise use validated current host
    if (!$wsHost) {
        // Use the current host but ensure it's in our whitelist
        $wsHost = $currentHostWithoutPort;
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
