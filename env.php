<?php
/**
 * Environment Configuration Loader
 * 
 * Loads configuration from .env file and provides helper functions
 * to access environment variables throughout the application.
 */

// Load .env file if it exists
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes from value if present
            if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                $value = $matches[1];
            }
            
            // Set environment variable if not already set
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

/**
 * Get environment variable with optional default value
 * 
 * @param string $key Environment variable name
 * @param mixed $default Default value if not found
 * @return mixed
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    
    // Convert string booleans
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
    }
    
    return $value;
}

/**
 * Get configuration value with nested key support
 * 
 * @param string $key Configuration key (e.g., 'database.host')
 * @param mixed $default Default value if not found
 * @return mixed
 */
function config($key, $default = null) {
    static $config = null;
    
    if ($config === null) {
        $config = [
            'app' => [
                'debug' => env('APP_DEBUG', false),
                'url' => env('APP_URL', 'http://localhost'),
            ],
            'database' => [
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', 3306),
                'database' => env('DB_DATABASE', 'hanhphuc'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ],
            'websocket' => [
                'enabled' => env('WEBSOCKET_ENABLED', true),
                'host' => env('WEBSOCKET_HOST', 'localhost'),
                'port' => env('WEBSOCKET_PORT', 8080),
                'protocol' => env('WEBSOCKET_PROTOCOL', 'ws'),
            ],
            'upload' => [
                'dir' => env('UPLOAD_DIR', __DIR__ . '/uploads/'),
                'url' => env('UPLOAD_URL', '/uploads/'),
            ],
            'encryption' => [
                'key' => env('ENCRYPTION_KEY', 'mySuperSecretKey123!'),
                'iv_seed' => env('ENCRYPTION_IV_SEED', 'myInitVector'),
            ],
            'pusher' => [
                'enabled' => env('PUSHER_ENABLED', false),
                'app_id' => env('PUSHER_APP_ID', ''),
                'key' => env('PUSHER_APP_KEY', ''),
                'secret' => env('PUSHER_APP_SECRET', ''),
                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
            ],
            'polling' => [
                'interval' => (int)env('POLLING_INTERVAL', 3000),
            ],
        ];
    }
    
    // Support nested keys like 'database.host'
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }
    
    return $value;
}

/**
 * Get WebSocket URL based on configuration
 * 
 * @return string
 */
function getWebSocketUrl() {
    $protocol = config('websocket.protocol', 'ws');
    $host = config('websocket.host', 'localhost');
    $port = config('websocket.port', 8080);
    
    return "{$protocol}://{$host}:{$port}";
}

/**
 * Check if WebSocket is enabled
 * 
 * @return bool
 */
function isWebSocketEnabled() {
    return config('websocket.enabled', true);
}

/**
 * Get the base URL for the application
 * 
 * @return string
 */
function getBaseUrl() {
    return rtrim(config('app.url', 'http://localhost'), '/');
}

/**
 * Get upload URL for files
 * 
 * @param string $filename Optional filename to append
 * @return string
 */
function getUploadUrl($filename = '') {
    $baseUrl = getBaseUrl();
    $uploadPath = config('upload.url', '/uploads/');
    return $baseUrl . $uploadPath . $filename;
}

/**
 * Get upload directory path
 * 
 * @return string
 */
function getUploadDir() {
    return config('upload.dir', __DIR__ . '/uploads/');
}
