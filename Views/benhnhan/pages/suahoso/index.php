<?php
include_once("Assets/config.php");
include_once("Controllers/cbenhnhan.php");
include_once("Controllers/ctaikhoan.php");
include_once("Controllers/ctinhthanhpho.php");
include_once("Controllers/cxaphuong.php");

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập.</p>";
    exit;
}

$id = isset($_GET['mabenhnhan']) ? $_GET['mabenhnhan'] : null;
if (!$id) {
    echo "<p>Không có mã bệnh nhân để sửa.</p>";
    exit;
}

// 🟡 Lấy thông tin bệnh nhân + thông tin địa phương đầy đủ
$pBenhNhan = new cBenhNhan();
$benhnhan = $pBenhNhan->getbenhnhanbyid($id);
if (!$benhnhan) {
    echo "<p>Không tìm thấy hồ sơ bệnh nhân với mã: $id</p>";
    exit;
}

$message = "";

// 🔸 Xử lý khi nhấn Lưu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten      = $_POST['hoten'] ?? '';
    $ngaysinh   = $_POST['ngaysinh'] ?? '';
    $gioitinh   = $_POST['gioitinh'] ?? '';
    $sdt        = $_POST['sdt'] ?? '';
    $diachi     = $_POST['diachi'] ?? '';

    $conn = new mysqli("localhost", "root", "", "hanhphuc");
    $conn->set_charset('utf8');
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $conn->begin_transaction();
    try {
        // 1️⃣ Cập nhật bảng nguoidung
        $sql1 = "UPDATE nguoidung 
                 SET hoten=?, ngaysinh=?, gioitinh=?, sdt=?, sonha=? 
                 WHERE manguoidung=?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sssssi", $hoten, $ngaysinh, $gioitinh, $sdt, $diachi, $id);
        $stmt1->execute();

        // 2️⃣ Có thể cập nhật thêm bảng benhnhan nếu cần
        $sql2 = "UPDATE benhnhan SET nghenghiep = nghenghiep WHERE mabenhnhan = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();

        $conn->commit();
        echo "<script>
                alert('Cập nhật hồ sơ thành công!');
                window.location.href = '?action=caidat';
              </script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Có lỗi xảy ra khi cập nhật hồ sơ: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa hồ sơ bệnh nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .container {
            max-width: 650px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #5e2d91;
            margin-bottom: 25px;
            text-align: center;
        }
        .btn-primary {
            background-color: #5e2d91;
            border: none;
        }
        .btn-primary:hover {
            background-color: #4b2173;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Sửa hồ sơ bệnh nhân</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="hoten" class="form-label">Họ tên</label>
            <input type="text" class="form-control" name="hoten" id="hoten"
                   value="<?= htmlspecialchars($benhnhan['hoten'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="ngaysinh" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" name="ngaysinh" id="ngaysinh"
                   value="<?= htmlspecialchars($benhnhan['ngaysinh'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới tính</label>
            <select class="form-select" name="gioitinh" required>
                <option value="Nam" <?= ($benhnhan['gioitinh'] ?? '') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                <option value="Nữ" <?= ($benhnhan['gioitinh'] ?? '') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="sdt" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" name="sdt" id="sdt"
                   value="<?= htmlspecialchars(decryptData($benhnhan['sdt'] ?? '')) ?>" required>
        </div>

        <div class="mb-3">
            <label for="diachi" class="form-label">Địa chỉ (Số nhà, đường)</label>
            <input type="text" class="form-control" name="diachi" id="diachi"
                   value="<?= htmlspecialchars($benhnhan['sonha'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tỉnh / Thành phố</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($benhnhan['tentinhthanhpho'] ?? '') ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Xã / Phường</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($benhnhan['tenxaphuong'] ?? '') ?>" readonly>
        </div>

        <div class="d-flex justify-content-between">
            <a href="?action=caidat" class="btn btn-secondary">← Quay lại</a>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</div>

</body>
</html>
