<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once('../../../../Models/ChatUserModel.php'); // Model chat

if (!isset($_SESSION['user']['tentk'])) {
    echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập']); exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['success'=>false,'error'=>'Không có file']); exit;
}

$file = $_FILES['file'];
if ($file['type'] !== 'application/pdf') {
    echo json_encode(['success'=>false,'error'=>'Chỉ chấp nhận PDF']); exit;
}

// Thư mục uploads
$uploadDir = 'C:/xampp/htdocs/KLTN/uploads/';
if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Tên file an toàn
$safeName = time().'_'.preg_replace('/[^a-zA-Z0-9_\.-]/','_',$file['name']);
$path = $uploadDir.$safeName;

// Upload file
if(move_uploaded_file($file['tmp_name'], $path)){
    $url = "http://localhost/KLTN/uploads/".$safeName;

    // Lưu file vào database dưới dạng tin nhắn [FILE]
    if(isset($_POST['receiver'])){ // gửi từ client kèm tentk người nhận
        $chat = new ChatUserModel();
        $chat->setSender($_SESSION['user']['tentk']);
        $chat->setReceiver($_POST['receiver']);
        $chat->setMessage("[FILE] $url");
        $chat->saveMessage();
    }

    echo json_encode(['success'=>true,'filename'=>$file['name'],'url'=>$url]);
} else {
    $err = error_get_last();
    echo json_encode(['success'=>false,'error'=>'Upload thất bại: '.($err['message'] ?? 'Không rõ lỗi')]);
}
