<?php
include_once(__DIR__ . '/../../../../Controllers/cchuyengia.php');
include_once ("Assets/config.php");
$cChuyenGia = new cChuyenGia();

// Lấy email đăng nhập từ session
$tentk = $_SESSION["user"]["tentk"] ?? null;

if (!$tentk) {
    echo "Chưa đăng nhập!";
    exit;
}

// Lấy thông tin
$bs = $cChuyenGia->getChuyenGiaByTenTK($tentk);

if (!$bs || $bs === 0) {
    echo "Không tìm thấy thông tin chuyên gia!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin cá nhân</title>
<style>
   
    .card { 
        max-width: 700px; 
        margin: 50px auto; 
        border-radius: 10px; 
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
        background: #fff;
        overflow: hidden;
    }
    .card-header { 
        background-color: #3c1561; 
        color: white; 
        font-size: 20px; 
        font-weight: 600; 
        text-align: center;
        padding: 20px;
    }
    .card-header img {
        width:100px; 
        height:100px; 
        object-fit:cover; 
        border-radius:50%; 
        border:2px solid #fff; 
        margin-bottom:10px;
    }
    .card-body { padding: 20px; }
    .card-body p { margin-bottom: 10px; line-height: 1.5; }
    .btn {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-primary { 
        background: #007bff; 
        color: #fff; 
        border: none;
    }
    .btn-primary:hover { background: #0056b3; }
    .btn-warning { 
        background: #ffc107; 
        color: #000; 
        border: none;
    }
    .btn-warning:hover { background: #e0a800; }
    .text-center { text-align: center; }
    .mt-3 { margin-top: 1rem; }
    .me-2 { margin-right: 0.5rem; }
    .card-body img {
        width: 250px;
        margin: 10px 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
</style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <img src="Assets/img/<?= htmlspecialchars($bs['imgcg']) ?>" 
             alt="Ảnh bác sĩ">
        <div><?= htmlspecialchars($bs['hoten']) ?></div>
    </div>

    <div class="card-body">
        <p><strong>Mã chuyên gia:</strong> <?= htmlspecialchars($bs['machuyengia']) ?></p>
        <p><strong>Lĩnh vực:</strong> <?= htmlspecialchars($bs['tenlinhvuc']) ?></p>
        <p><strong>Mô tả chuyên gia:</strong> <?= htmlspecialchars($bs['motacg']) ?></p>
        <p><strong>Giới thiệu chuyên gia:</strong> <?= htmlspecialchars($bs['gioithieucg']) ?></p>
        <p><strong>Giá tư vấn:</strong> <?= htmlspecialchars($bs['giatuvan']) ?></p>
        <p><strong>Ngày bắt đầu làm việc:</strong> <?= date('d-m-Y', strtotime($bs['ngaybatdau'])) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars(decryptData($bs['email'])) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars(decryptData($bs['sdt'])) ?></p>
        <p><strong>Cấp bậc:</strong> <?= htmlspecialchars($bs['capbac']) ?></p>
        <p><strong>Ngày sinh:</strong> <?= date('d-m-Y', strtotime($bs['ngaysinh'])) ?></p>
        <p><strong>Giới tính:</strong> <?= htmlspecialchars($bs['gioitinh']) ?></p>
        <p><strong>Dân tộc:</strong> <?= htmlspecialchars($bs['dantoc']) ?></p>
        <p><strong>CCCD:</strong> <?= htmlspecialchars(decryptData($bs['cccd'])) ?></p>
        <img src="Assets/img/cccd/<?= htmlspecialchars($bs['cccd_matruoc']) ?>" 
             alt="Ảnh CCCD mặt trước" >
        <img src="Assets/img/cccd/<?= htmlspecialchars($bs['cccd_matsau']) ?>" 
             alt="Ảnh CCCD mặt sau" >
        <p><strong>Địa chỉ:</strong> 
            <?= htmlspecialchars($bs['sonha']) . ', ' 
            . htmlspecialchars($bs['tenxaphuong']) . ', ' 
            . htmlspecialchars($bs['tentinhthanhpho']) ?>
        </p>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-primary me-2">Quay lại trang chủ</a>
            <!-- <a href="?action=suathongtin&id=<?= htmlspecialchars($bs['machuyengia']) ?>" class="btn btn-warning">
                ✏ Sửa thông tin
            </a> -->
        </div>
    </div>
</div>
</body>
</html>
