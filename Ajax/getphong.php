<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "hanhphuc");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Query lấy danh sách phòng
$sql = "
    SELECT 
        maphong, 
        tentoa, 
        tang, 
        sophong, 
        loaiphong
    FROM phong
    ORDER BY tentoa, tang, sophong
";

$result = $conn->query($sql);

$danhSachPhong = [];

while ($row = $result->fetch_assoc()) {
    $danhSachPhong[] = $row;
}

echo json_encode($danhSachPhong);
