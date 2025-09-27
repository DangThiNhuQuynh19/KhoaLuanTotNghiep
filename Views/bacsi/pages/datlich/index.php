<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();

include_once('Controllers/cbacsi.php');
include_once('Controllers/clichkham.php');

if (!isset($_SESSION["dangnhap"]) || !isset($_SESSION["user"])) {
    echo "<p>Bạn chưa đăng nhập!</p>";
    exit;
}

$cbacsi = new cBacSi();
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION["user"]["tentk"] ?? '');
if (!$bacsi) {
    echo "<p>Không tìm thấy bác sĩ.</p>";
    exit;
}

$manguoi = $bacsi['mabacsi'] ?? null;
if (!$manguoi) {
    echo "<p>Mã bác sĩ không hợp lệ.</p>";
    exit;
}

$tuNgay = date('Y-m-d');

$lich = new cLichKham();
$tbl = $lich->getLichTrongCuaNguoi1($tuNgay, $manguoi);

$lichOnline = [];
$lichOffline = [];

if ($tbl && $tbl !== -1 && $tbl !== 0) {
    while ($row = $tbl->fetch_assoc()) {
        if (strtolower($row['hinhthuclamviec']) === 'online') {
            $lichOnline[] = $row;
        } else {
            $lichOffline[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Trống Bác Sĩ</title>

</head>
<body>
<div class="lich-trong-wrapper">
    <h2>Lịch Trống Bác Sĩ</h2>

    <h4>Khám Online</h4>
    <?php if (!empty($lichOnline)): ?>
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Ca Làm Việc</th>
                    <th>Giờ Bắt Đầu</th>
                    <th>Giờ Kết Thúc</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lichOnline as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ngaylam']) ?></td>
                        <td><?= htmlspecialchars($row['tenca'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['giobatdau']) ?></td>
                        <td><?= htmlspecialchars($row['gioketthuc']) ?></td>
                        <td>
                            <button class="btn-datlich" onclick="alert('Đặt lịch ngày <?= $row['ngaylam'] ?>, ca <?= $row['tenca'] ?? '-' ?>')">Đặt lịch</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không có lịch online trống.</p>
    <?php endif; ?>

    <h4>Khám Offline</h4>
    <?php if (!empty($lichOffline)): ?>
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Ca Làm Việc</th>
                    <th>Giờ Bắt Đầu</th>
                    <th>Giờ Kết Thúc</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lichOffline as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ngaylam']) ?></td>
                        <td><?= htmlspecialchars($row['tenca'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['giobatdau']) ?></td>
                        <td><?= htmlspecialchars($row['gioketthuc']) ?></td>
                        <td>
                            <button class="btn-datlich" onclick="alert('Đặt lịch ngày <?= $row['ngaylam'] ?>, ca <?= $row['tenca'] ?? '-' ?>')">Đặt lịch</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không có lịch offline trống.</p>
    <?php endif; ?>
</div>

<style>
/* CSS chỉ áp dụng trong div. Tránh ảnh hưởng header khác */
.lich-trong-wrapper {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f5ff;
    padding: 30px 20px;
}
.lich-trong-wrapper h2 {
    text-align: center;
    color: #6c3483;
    margin-bottom: 40px;
}
.lich-trong-wrapper h4 {
    color: #4b0082;
    margin-top: 30px;
    margin-bottom: 15px;
}
.lich-trong-wrapper table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 4px 10px rgba(108, 52, 131, 0.15);
    border-radius: 8px;
    overflow: hidden;
}
.lich-trong-wrapper th,
.lich-trong-wrapper td {
    padding: 12px 10px;
    text-align: center;
    border-bottom: 1px solid #eee;
}
.lich-trong-wrapper th {
    background-color: #6c3483;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
}
.lich-trong-wrapper tr:nth-child(even) {
    background-color: #fdf5ff;
}
.lich-trong-wrapper tr:hover {
    background-color: #f3e5f5;
}
.lich-trong-wrapper .btn-datlich {
    padding: 6px 14px;
    background-color: #6c3483;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.3s;
}
.lich-trong-wrapper .btn-datlich:hover {
    background-color: #4b0082;
}
@media (max-width: 768px) {
    .lich-trong-wrapper th,
    .lich-trong-wrapper td { font-size: 12px; padding: 8px; }
    .lich-trong-wrapper .btn-datlich { font-size: 11px; padding: 5px 10px; }
}
</style>
</body> 
</html>