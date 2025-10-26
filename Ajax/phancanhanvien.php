<?php
// phan_ca_nhan_vien.php
// Đường dẫn này giả định cphan_ca.php nằm trong thư mục Controllers
include_once('../Controllers/cphanca.php'); 
// 🚨 LƯU Ý: Không cần include clsKetNoi ở đây vì nó đã được include trong Model
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Lấy dữ liệu từ POST
$macalam = $_POST['macalam'] ?? '';
$hinhthuc = $_POST['hinhthuc'] ?? '';
// Chuyển chuỗi JSON mã nhân viên thành mảng PHP
$manv_list = json_decode($_POST['manv_list'] ?? '[]', true); 

if (empty($macalam) || empty($hinhthuc) || empty($manv_list)) {
    echo json_encode(['success' => false, 'message' => 'Missing required data.']);
    exit;
}

// 🚨 Khởi tạo Controller. 
// Trong cấu trúc của bạn, Controller và Model tự quản lý kết nối CSDL, 
// nên không cần truyền tham số nào vào constructor.
$cPhanCa = new cPhanCa(); 

// Gọi hàm xử lý
$result = $cPhanCa->phanCaNhanVien($macalam, $hinhthuc, $manv_list); 

// Trả về kết quả JSON
if ($result) {
    // Thông báo chi tiết hơn về số lượng nhân viên
    echo json_encode([
        'success' => true, 
        'message' => 'Phân ca thành công cho ' . count($manv_list) . ' nhân viên trong 6 tháng.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi CSDL hoặc không có nhân viên nào được phân ca.'
    ]);
}
?>