<?php
// config.php - lưu khóa mã hóa và phương thức mã hóa
// Load environment configuration
require_once(__DIR__ . '/../env.php');

// Get encryption settings from environment
$encKey = config('encryption.key', 'mySuperSecretKey123!');
$encIvSeed = config('encryption.iv_seed', 'myInitVector');

define('ENCRYPTION_KEY', hash('sha256', $encKey, true));
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_IV', substr(hash('sha256', $encIvSeed), 0, 16)); // 16 bytes IV

// Hàm mã hóa dữ liệu
function encryptData($data) {
    return base64_encode(openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

function decryptData($encrypted) {
    return openssl_decrypt(base64_decode($encrypted), ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}
?>