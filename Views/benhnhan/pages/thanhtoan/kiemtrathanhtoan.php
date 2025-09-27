<?php
session_start();
header('Content-Type: application/json');

// Nhận dữ liệu JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['maphieukhambenh']) || !isset($input['tongtien'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$maphieukhambenh = $input['maphieukhambenh'];
$tongtien = $input['tongtien'];

try {
    // Kết nối database (điều chỉnh theo cấu hình của bạn)
    include_once('../../../../config/database.php'); // Điều chỉnh đường dẫn
    
    // Kiểm tra trong bảng giao dịch hoặc API ngân hàng
    // Đây là ví dụ - bạn cần tích hợp với API ngân hàng thực tế
    
    // Giả lập kiểm tra thanh toán qua API VCB hoặc database giao dịch
    $isPaid = checkPaymentFromBank($maphieukhambenh, $tongtien);
    
    if ($isPaid) {
        // Cập nhật trạng thái thanh toán trong database
        updatePaymentStatus($maphieukhambenh, 'paid');
        
        echo json_encode([
            'success' => true, 
            'paid' => true, 
            'message' => 'Thanh toán thành công'
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'paid' => false, 
            'message' => 'Chưa nhận được thanh toán'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

function checkPaymentFromBank($maphieukhambenh, $tongtien) {
    // Tích hợp với API ngân hàng để kiểm tra giao dịch
    // Ví dụ: kiểm tra giao dịch VCB qua API
    
    // Tạm thời return false - bạn cần tích hợp API thực tế
    // Có thể sử dụng:
    // - API VCB Digital Banking
    // - Webhook từ cổng thanh toán
    // - Kiểm tra database giao dịch nội bộ
    
    return false; // Thay bằng logic kiểm tra thực tế
}

function updatePaymentStatus($maphieukhambenh, $status) {
    // Cập nhật trạng thái thanh toán trong database
    // Ví dụ:
    // UPDATE phieukhambenh SET trangthai_thanhtoan = 'paid' WHERE maphieukhambenh = ?
}
?>
