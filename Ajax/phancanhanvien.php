<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
include_once('../Controllers/cphanca.php'); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Lấy dữ liệu POST
$macalam = $_POST['macalam'] ?? '';
$hinhthuc = $_POST['hinhthuc'] ?? '';
$manv_list = json_decode($_POST['manv_list'] ?? '[]', true); 

if (empty($macalam) || empty($hinhthuc) || empty($manv_list)) {
    echo json_encode(['success' => false, 'message' => 'Missing required data.']);
    exit;
}

/* ✅ NẾU HÌNH THỨC = ONLINE → TỰ ĐỘNG SET PHÒNG = NULL */
if ($hinhthuc === "Online") {

    foreach ($manv_list as &$nv) {
        $nv["maphong"] = null;
    }
}

/* ✅ NẾU HÌNH THỨC = OFFLINE → KIỂM TRA TRÙNG PHÒNG */
else if ($hinhthuc === "Offline") {

    $phongUsed = [];

    foreach ($manv_list as $nv) {
        $phong = $nv['maphong'];

        if (isset($phongUsed[$phong])) {
            echo json_encode([
                'success' => false,
                'message' => "Phòng {$phong} đang bị trùng. Vui lòng chọn phòng khác."
            ]);
            exit;
        }

        $phongUsed[$phong] = true;
    }
}


/* ✅ Tiếp tục xử lý nếu không trùng phòng */
$cPhanCa = new cPhanCa(); 
$result = $cPhanCa->phanCaNhanVien($macalam, $hinhthuc, $manv_list);

if ($result) {
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
