<?php
include_once("Controllers/cnhanvien.php");
include_once("Assets/config.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Không có nhân viên.</p>";
    exit;
}

$id = $_GET['id'];
$cNhanVien = new cNhanVien();
$tbl = $cNhanVien->getNhanVienById($id);

if ($tbl === -1) {
    echo "<p>Lỗi kết nối CSDL.</p>";
    exit;
} elseif ($tbl === 0) {
    echo "<p>Nhân viên không tồn tại.</p>";
    exit;
}

$nv = $tbl->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết nhân viên</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
  --primary: #3c1561;
  --secondary: #f5f5f7;
  --card-bg: #fff;
  --text-dark: #333;
  --border-color: #e0e0e0;
  --radius: 14px;
  --shadow: 0 4px 12px rgba(0,0,0,0.08);
}
body { background-color: var(--secondary); font-family: "Segoe UI", Tahoma, sans-serif; color: var(--text-dark); }
.card { border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border-color); padding: 20px; background-color: var(--card-bg); max-width: 900px; margin: 40px auto; }
.card img { width: 150px; height: 150px; object-fit: cover; border-radius: var(--radius); border: 1px solid var(--border-color); }
.card h3 { color: var(--primary); margin-bottom: 15px; }
.table th { width: 160px; }
</style>
</head>
<body>

<div class="card">
    <div class="d-flex align-items-center mb-4">
        <img src="Assets/img/<?= htmlspecialchars($nv['imgnv'] ?? 'default.png') ?>" alt="Avatar">
        <div class="ms-4">
            <h3><?= htmlspecialchars($nv['hoten']) ?></h3>
            <p><i class="bi bi-person-badge"></i> <?= htmlspecialchars($nv['chucvu']) ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Thông tin cá nhân</h5>
            <table class="table table-borderless">
                <tr><th>Ngày sinh:</th><td><?= htmlspecialchars($nv['ngaysinh']) ?></td></tr>
                <tr><th>Giới tính:</th><td><?= htmlspecialchars($nv['gioitinh']) ?></td></tr>
                <tr><th>Dân tộc:</th><td><?= htmlspecialchars($nv['dantoc']) ?></td></tr>
                <tr><th>CCCD:</th><td><?= htmlspecialchars(decryptData($nv['cccd'])) ?></td></tr>
                <tr><th>Địa chỉ:</th><td><?= htmlspecialchars($nv['sonha']) ?>, <?= htmlspecialchars($nv['tenxaphuong']) ?>, <?= htmlspecialchars($nv['tentinhthanhpho']) ?></td></tr>
                <tr><th>Email cá nhân:</th><td><?= htmlspecialchars($nv['emailcanhan']) ?></td></tr>
                <tr><th>Số điện thoại:</th><td><?= htmlspecialchars(decryptData($nv['sdt'])) ?></td></tr>
                <tr><th>Email TK:</th><td><?= htmlspecialchars(decryptData($nv['email'])) ?></td></tr>
            </table>
        </div>

        <div class="col-md-6">
            <h5>Thông tin công việc</h5>
            <table class="table table-borderless">
                <tr><th>Ngày bắt đầu:</th><td><?= htmlspecialchars($nv['ngaybatdau']) ?></td></tr>
                <tr><th>Ngày kết thúc:</th><td><?= htmlspecialchars($nv['ngayketthuc']) ?></td></tr>
                <tr><th>Chức vụ:</th><td><?= htmlspecialchars($nv['chucvu']) ?></td></tr>
                <tr><th>Trạng thái tài khoản:</th><td><?= htmlspecialchars($nv['tentrangthai']) ?></td></tr>
            </table>
        </div>
    </div>

    <a href="?action=nhanvien&tab=nhanvien" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

</body>
</html>
