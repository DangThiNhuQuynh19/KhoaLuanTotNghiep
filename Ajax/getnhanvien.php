<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

$macalam = $_POST['macalam'] ?? null;
$machucvu = $_POST['machucvu'] ?? null;

if (!$macalam || !$machucvu) {
    echo json_encode([]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "hanhphuc");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT nd.manguoidung AS manv, nd.hoten
FROM nguoidung nd
JOIN taikhoan tk ON tk.tentk = nd.email
WHERE tk.mavaitro = ?
  AND nd.manguoidung NOT IN (
      SELECT l.manguoidung 
      FROM lichlamviec l 
      WHERE l.macalamviec = ? 
        AND l.ngaylam BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
  )
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $machucvu, $macalam);
$stmt->execute();
$result = $stmt->get_result();

$nhanvien = [];
while ($row = $result->fetch_assoc()) {
    $nhanvien[] = $row;
}

echo json_encode($nhanvien);
