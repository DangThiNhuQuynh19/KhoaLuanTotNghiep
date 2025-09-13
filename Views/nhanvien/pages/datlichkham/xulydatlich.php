<?php
// date_default_timezone_set('Asia/Ho_Chi_Minh');

// include_once __DIR__ . "/../../../../Controllers/cLichKham.php";
// include_once __DIR__ . "/../../../../Controllers/cPhieuKhamBenh.php";

// $cPhieuKhamBenh = new cPhieuKhamBenh();
// $cLichKham = new cLichKham();

// // Lấy dữ liệu POST
// $makhunggiokb = $_POST['makhunggiokb'] ?? null;
// $manguoidung = $_POST['manguoidung'] ?? null;
// $ngaylam = $_POST['ngaylam'] ?? null;
// $mabenhnhan = $_POST['mabenhnhan'] ?? null;

// if (!$makhunggiokb || !$manguoidung || !$ngaylam || !$mabenhnhan) {
//     die("Thiếu dữ liệu bắt buộc!");
// }

// // Lấy thông tin người khám để phân biệt bác sĩ hay chuyên gia
// $nguoi = $cLichKham->getThongTinNguoi($manguoidung); // giả sử trả về ['vaitro'=>0 hoặc 1, 'hoten'=>...]
// $vaitro = $nguoi['vaitro'] ?? 0; // 0 = bác sĩ, 1 = chuyên gia

// // Kiểm tra trùng lịch cho bệnh nhân
// $trung = $cPhieuKhamBenh->kiemTraTrungLich($mabenhnhan, $ngaylam, $makhunggiokb);

// if ($trung) {
//     echo "<p style='color:red;'>Bệnh nhân đã có lịch khám trùng khung giờ này!</p>";
//     echo '<p><a href="javascript:history.back()">Quay lại</a></p>';
//     exit;
// }

// // Thêm lịch khám
// if ($vaitro == 0) {
//     // Bác sĩ
//     $success = $cLichKham->datLichChoBacSi($mabenhnhan, $manguoidung, $makhunggiokb, $ngaylam);
// } else {
//     // Chuyên gia
//     $success = $cLichKham->datLichChoChuyenGia($mabenhnhan, $manguoidung, $makhunggiokb, $ngaylam);
// }

// if ($success) {
//     echo "<p style='color:green;'>Đặt lịch thành công!</p>";
//     echo '<p><a href="javascript:history.back()">Quay lại</a></p>';
// } else {
//     echo "<p style='color:red;'>Đặt lịch thất bại. Vui lòng thử lại!</p>";
//     echo '<p><a href="javascript:history.back()">Quay lại</a></p>';
// }
?>