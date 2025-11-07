<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "hanhphuc");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// ✅ Lấy macalam từ GET
$macalam = isset($_GET["macalam"]) ? intval($_GET["macalam"]) : 0;

if ($macalam <= 0) {
    echo json_encode([]);
    exit;
}

$ngay_bat_dau = date('Y-m-d');  // <= ngày hiện tại
$ngay_ket_thuc = date('Y-m-d', strtotime('+6 months', strtotime($ngay_bat_dau)));


$sql = "
SELECT 
    p.maphong, 
    p.tentoa, 
    p.tang, 
    p.sophong, 
    p.loaiphong
FROM phong p
WHERE NOT EXISTS (
    SELECT *
    FROM lichlamviec llv
    WHERE llv.maphong = p.maphong
      AND llv.macalamviec = $macalam
      AND llv.ngaylam BETWEEN '$ngay_bat_dau' AND '$ngay_ket_thuc'
)
ORDER BY p.tentoa, p.tang, p.sophong;

";

$result = $conn->query($sql);

$ds = [];

while ($row = $result->fetch_assoc()) {
    $ds[] = $row;
}

echo json_encode($ds);
