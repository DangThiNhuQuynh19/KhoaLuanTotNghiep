<?php
include_once(__DIR__ . '/../../../Controllers/clichkham.php');
include_once(__DIR__ . '/../../../Controllers/cNguoiKham.php'); 
include_once(__DIR__ . '/../../../Controllers/ckhunggio.php'); 
 

$cLichKham = new cLichKham();
$cNguoiKham = new cNguoiKham();
$cKhungGio = new cKhungGio();


// // Lấy ID phiếu khám từ URL
// $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// $lichhen = $cLichKham->getLichHenById($id);

// // Lấy danh sách người khám (bác sĩ + chuyên gia)
// $dsNguoiKham = $cNguoiKham->getAllNguoiKham();
// $dsKhungGio = $cKhungGio->getAllKhungGio();
// $dsTrangThai = $cTrangThai->getAllTrangThai();

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $ngaykham = $_POST['ngaykham'];
//     $makhunggiokb = $_POST['makhunggiokb'];
//     $manguoikham = $_POST['manguoikham'];
//     $matrangthai = $_POST['matrangthai'];

//     $update = $cLichKham->updateLichHen($id, $ngaykham, $makhunggiokb, $manguoikham, $matrangthai);

//     if ($update) {
//         echo "<script>alert('Cập nhật lịch hẹn thành công!'); window.location='index.php';</script>";
//     } else {
//         echo "<script>alert('Cập nhật thất bại!');</script>";
//     }
// }

?>