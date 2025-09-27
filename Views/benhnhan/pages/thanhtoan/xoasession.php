<?php
session_start();

// Chỉ xóa session liên quan đến thanh toán, GIỮ LẠI session đăng nhập
$payment_sessions = [
    'maphieukhambenh',
    'ngaykham', 
    'makhunggiokb',
    'mabacsi',           // Thêm session bác sĩ
    'machuyengia',       // Thêm session chuyên gia  
    'mabenhnhan',        // Session bệnh nhân cho lịch khám này
    'tongtien',
    'matrangthai',
    'macalamviec'        // Thêm session ca làm việc nếu có
];

$cleared_sessions = [];

foreach ($payment_sessions as $session_key) {
    if (isset($_SESSION[$session_key])) {
        unset($_SESSION[$session_key]);
        $cleared_sessions[] = $session_key;
    }
}

http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Payment sessions cleared',
    'cleared' => $cleared_sessions,
    'remaining_sessions' => array_keys($_SESSION) // Để kiểm tra session nào còn lại
]);
?>
