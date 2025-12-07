<?php
// Lấy tên file từ URL
$filename = $_GET['file'] ?? '';

// Đường dẫn đến thư mục uploads
$uploadDir = __DIR__ . '/uploads/';
$filepath = $uploadDir . basename($filename);

// Kiểm tra file có tồn tại không
if (! file_exists($filepath)) {
    http_response_code(404);
    die('File không tồn tại');
}

// Kiểm tra extension
$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    http_response_code(403);
    die('Chỉ cho phép download file PDF');
}

// ✅ Set header đúng cho PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($filename) . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: public, max-age=3600');
header('Pragma: public');

// Xóa output buffer nếu có
if (ob_get_level()) {
    ob_clean();
}

// Đọc và xuất file
readfile($filepath);
exit;
?>