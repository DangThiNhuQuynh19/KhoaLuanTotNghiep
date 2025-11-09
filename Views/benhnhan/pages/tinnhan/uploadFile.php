<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// --- Kiểm tra đăng nhập ---
if(!isset($_SESSION['user']['tentk'])){
    echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập']);
    exit;
}

// --- Kiểm tra dữ liệu ---
if(!isset($_FILES['file'])){
    echo json_encode(['success'=>false,'error'=>'Không có file']);
    exit;
}

$file = $_FILES['file'];

// Chỉ chấp nhận PDF
if($file['type'] !== 'application/pdf'){
    echo json_encode(['success'=>false,'error'=>'Chỉ chấp nhận PDF']);
    exit;
}

// Thư mục uploads
$uploadDir = 'C:/xampp/htdocs/KLTN/uploads/';
if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Tên file an toàn
$safeName = time().'_'.preg_replace('/[^a-zA-Z0-9_\.-]/','_',$file['name']);
$path = $uploadDir.$safeName;

// Upload file
if(move_uploaded_file($file['tmp_name'],$path)){
    $url = "http://localhost/KLTN/uploads/".$safeName;
    echo json_encode(['success'=>true,'filename'=>$file['name'],'url'=>$url]);
    exit;
}else{
    $err = error_get_last();
    echo json_encode(['success'=>false,'error'=>'Upload thất bại: '.($err['message'] ?? 'Không rõ lỗi')]);
    exit;
}
