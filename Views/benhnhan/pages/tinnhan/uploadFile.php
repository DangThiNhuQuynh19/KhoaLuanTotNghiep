<?php
header('Content-Type: application/json; charset=utf-8');

// ✅ Kiểm tra file được upload
if (! isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'error' => 'Không nhận được file hoặc có lỗi upload',
        'code' => $_FILES['file']['error'] ?? 'UNKNOWN'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES['file'];
$receiver = $_POST['receiver'] ?? 'unknown';
$sender = $_POST['sender'] ??  'unknown';

// ✅ Kiểm tra extension trước
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($extension !== 'pdf') {
    echo json_encode([
        'success' => false,
        'error' => 'Chỉ chấp nhận file PDF (extension: .' . $extension . ')'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ✅ Kiểm tra MIME type
$allowedMimes = ['application/pdf', 'application/x-pdf'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedMimes)) {
    echo json_encode([
        'success' => false,
        'error' => 'File không phải PDF (MIME: ' . $mimeType . ')'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ✅ Kiểm tra kích thước (10MB)
if ($file['size'] > 10 * 1024 * 1024) {
    echo json_encode([
        'success' => false,
        'error' => 'File không được vượt quá 10MB'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ✅ Tạo thư mục upload nếu chưa có
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    if (! mkdir($uploadDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'error' => 'Không thể tạo thư mục uploads'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ✅ Tạo tên file unique (giữ nguyên extension . pdf)
$originalName = pathinfo($file['name'], PATHINFO_FILENAME);
$safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalName);
$safeName = substr($safeName, 0, 50); // Giới hạn độ dài
$filename = time() . '_' . uniqid() . '_' .($safeName ?:'file') . '.pdf';
$filepath = $uploadDir . $filename;

// ✅ Di chuyển file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // ✅ Verify file đã được lưu đúng
    if (! file_exists($filepath)) {
        echo json_encode([
            'success' => false,
            'error' => 'File đã upload nhưng không tìm thấy'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ✅ URL download qua download.php
    $url = 'Views/benhnhan/pages/tinnhan/download.php?file=' . urlencode($filename);
    
    echo json_encode([
        'success' => true,
        'filename' => $file['name'],
        'savedFilename' => $filename,
        'url' => $url,
        'size' => $file['size'],
        'mimeType' => $mimeType
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Không thể lưu file.  Kiểm tra quyền thư mục uploads',
        'uploadDir' => $uploadDir,
        'permissions' => is_writable($uploadDir) ?  'writable' : 'not writable'
    ], JSON_UNESCAPED_UNICODE);
}
?>