<?php
// API endpoint để kiểm tra và hủy lịch hẹn hết hạn
header('Content-Type: application/json; charset=utf-8');
include_once("Assets/config.php");
include_once('xu-ly-thanh-toan.php');

$xu_ly_thanh_toan = new XuLyThanhToan();
$danh_sach_huy = $xu_ly_thanh_toan->kiem_tra_lich_hen_het_han();

echo json_encode([
    'trang_thai' => 'thanh_cong',
    'so_luong_huy' => count($danh_sach_huy),
    'danh_sach_ma_lich_hen' => $danh_sach_huy,
    'thoi_gian_kiem_tra' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
?>
