<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// --- Kiểm tra đăng nhập ---
if (!isset($_SESSION['user']['tentk'])) {
    echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập']);
    exit;
}

// --- Kiểm tra file POST ---
if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'Không có file']);
    exit;
}

$file = $_FILES['file'];

// Kiểm tra upload error
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File vượt quá upload_max_filesize của server',
        UPLOAD_ERR_FORM_SIZE => 'File vượt quá giới hạn form',
        UPLOAD_ERR_PARTIAL => 'File bị upload 1 phần',
        UPLOAD_ERR_NO_FILE => 'Không có file được gửi',
        UPLOAD_ERR_NO_TMP_DIR => 'Không có thư mục tạm',
        UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file lên đĩa',
        UPLOAD_ERR_EXTENSION => 'Bị chặn bởi extension'
    ];
    $msg = $errors[$file['error']] ?? 'Lỗi upload không xác định';
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// Kiểm tra size (ví dụ 10MB)
$maxBytes = 10 * 1024 * 1024;
if ($file['size'] > $maxBytes) {
    echo json_encode(['success' => false, 'error' => 'File quá lớn (max 10MB)']);
    exit;
}

// Kiểm tra MIME thật bằng finfo (an toàn hơn dùng $file['type'])
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if ($mime !== 'application/pdf') {
    echo json_encode(['success' => false, 'error' => 'Chỉ chấp nhận PDF (MIME invalid: ' . $mime . ')']);
    exit;
}

// Calculate project root relative to this file
$projectRoot = realpath(__DIR__ . '/../../../../');
if ($projectRoot === false) {
    echo json_encode(['success' => false, 'error' => 'Không thể xác định đường dẫn gốc của dự án']);
    exit;
}
$uploadDirFS = $projectRoot . '/uploads/';

if (!is_dir($uploadDirFS)) {
    if (!mkdir($uploadDirFS, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'Không thể tạo thư mục uploads trên server']);
        exit;
    }
}
if (!is_writable($uploadDirFS)) {
    echo json_encode(['success' => false, 'error' => 'Thư mục uploads không có quyền ghi']);
    exit;
}


$origName = basename($file['name']);
$safeName = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $origName);
$targetPath = $uploadDirFS . $safeName;


if (!is_uploaded_file($file['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'File upload không hợp lệ (not uploaded file)']);
    exit;
}

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    // Build public URL based on current server scheme and hostname
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    // Use SERVER_NAME for security (more reliable than HTTP_HOST which can be manipulated)
    $serverName = $_SERVER['SERVER_NAME'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
    // Handle non-standard ports
    $port = $_SERVER['SERVER_PORT'] ?? '80';
    $portSuffix = '';
    if (($scheme === 'https' && $port != '443') || ($scheme === 'http' && $port != '80')) {
        $portSuffix = ':' . $port;
    }
    $publicBaseUrl = $scheme . '://' . $serverName . $portSuffix . '/uploads/';
    $publicUrl = rtrim($publicBaseUrl, '/') . '/' . rawurlencode($safeName);

    echo json_encode([
        'success'  => true,
        'filename' => $origName,
        'saved_name' => $safeName,
        'url'      => $publicUrl
    ]);
    exit;
} else {
    $err = error_get_last();
    echo json_encode(['success' => false, 'error' => 'move_uploaded_file thất bại: ' . ($err['message'] ?? 'Không rõ lỗi')]);
    exit;
}
