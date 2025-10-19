<?php
// config.php - lưu khóa mã hóa và phương thức mã hóa

define('ENCRYPTION_KEY', hash('sha256', 'mySuperSecretKey123!', true)); // bạn nên thay đổi thành chuỗi bí mật của riêng bạn
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_IV', substr(hash('sha256', 'myInitVector'), 0, 16)); // 16 bytes IV

// Hàm mã hóa dữ liệu
function encryptData($data) {
    return base64_encode(openssl_encrypt($data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV));
}

function decryptData($encrypted) {
    return openssl_decrypt(base64_decode($encrypted), ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}
?>