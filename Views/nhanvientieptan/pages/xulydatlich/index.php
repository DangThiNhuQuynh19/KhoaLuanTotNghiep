<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once "Controllers/clichkham.php";
include_once "Controllers/cphieukhambenh.php";

$cPhieuKhamBenh = new cPhieuKhamBenh();
$cLichKham = new cLichKham();

// Lấy dữ liệu POST
$makhunggiokb = $_POST['makhunggiokb'] ?? null;
$manguoidung = $_POST['manguoidung'] ?? null;
$ngaylam = $_POST['ngaylam'] ?? null;
$mabenhnhan = $_POST['mabenhnhan'] ?? null;

if (!$makhunggiokb || !$manguoidung || !$ngaylam || !$mabenhnhan) {
    die("Thiếu dữ liệu bắt buộc!");
}

// Lấy thông tin người khám để phân biệt bác sĩ hay chuyên gia
$nguoi = $cLichKham->getThongTinNguoi($manguoidung);
$vaitro = $nguoi['vaitro'] ?? 0; // 0 = bác sĩ, 1 = chuyên gia

// Kiểm tra trùng lịch cho bệnh nhân
$trung = $cPhieuKhamBenh->kiemTraTrungLich($mabenhnhan, $ngaylam, $makhunggiokb);

if ($trung) {
    echo "<p style='color:red;'>Bệnh nhân đã có lịch khám trùng khung giờ này!</p>";
    echo '<p><a href="javascript:history.back()">Quay lại</a></p>';
    exit;
}

// Tạo mã phiếu khám bệnh
$maphieukb = 'PKB' . time() . rand(100, 999);

// Trạng thái phiếu khám: 6 = Chờ xác nhận
$TRANG_THAI_CHO_XAC_NHAN = 6;

// Thêm phiếu khám bệnh
// Lưu ý: Cột 'mabacsi' trong DB lưu cả mã bác sĩ và mã chuyên gia
// $manguoidung có thể là mabacsi (VD: '1', '36') hoặc machuyengia (VD: 'CG_77219778')
$success = $cPhieuKhamBenh->insertphieukham(
    $maphieukb,
    $ngaylam,
    $makhunggiokb,
    $manguoidung,  // ID của bác sĩ hoặc chuyên gia
    $mabenhnhan,
    $TRANG_THAI_CHO_XAC_NHAN
);

if ($success) {
    echo "<p style='color:green;'>Đặt lịch thành công!</p>";
    echo '<p><a href="javascript:history.back()">Quay lại</a></p>';
} else {
    echo "<p style='color:red;'>Đặt lịch thất bại. Vui lòng thử lại!</p>";
    echo '<p><a href="javascript:history.back()">Quay lại</a></p>';
}
?>