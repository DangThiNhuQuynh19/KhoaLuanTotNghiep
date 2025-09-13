<?php
include_once(__DIR__ . '/../../../../Controllers/cNhanVien.php');
include ("Assets/config.php");
$cNhanVien = new cNhanVien();

// Lấy email đăng nhập từ session
$tentk = $_SESSION["user"]["tentk"] ?? null;

if (!$tentk) {
    echo "Chưa đăng nhập!";
    exit;
}

// Lấy thông tin nhân viên
$nv = $cNhanVien->getNhanVienByTenTK($tentk);

if (!$nv || $nv === 0) {
    echo "Không tìm thấy thông tin nhân viên!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin cá nhân</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .card { max-width: 600px; margin: 50px auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .card-header { background-color: #3c1561; color: white; font-size: 20px; font-weight: 600; }
    .card-body p { margin-bottom: 10px; }
</style>
</head>
<body>
<div class="card">
<div class="card-header text-center">
    <img src="<?= !empty($nv['imgnv']) ? '/Assets/img/' . htmlspecialchars($nv['imgnv']) : '/Assets/img/kimlien.png' ?>" 
         alt="Ảnh nhân viên" 
         style="width:100px; height:100px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-bottom:10px;">
    <div><?= htmlspecialchars($nv['hoten']) ?></div>
</div>

    <div class="card-body">
        <p><strong>Mã nhân viên:</strong> <?= htmlspecialchars($nv['manhanvien']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars(decryptData($nv['email'])) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars(decryptData($nv['sdt'])) ?></p>
        <p><strong>Chức vụ:</strong> <?= htmlspecialchars($nv['chucvu']) ?></p>
        <p><strong>Ngày sinh:</strong> <?= date('d-m-Y', strtotime($nv['ngaysinh'])) ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($nv['diachi']) ?></p>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-primary me-2">Quay lại trang chủ</a>
            <a href="http://localhost/KLTN/index.php?action=suathongtin&id=<?= htmlspecialchars($nv['manhanvien']) ?>" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Sửa thông tin
            </a>
        </div>

    </div>
</div>

</body>
</html>
