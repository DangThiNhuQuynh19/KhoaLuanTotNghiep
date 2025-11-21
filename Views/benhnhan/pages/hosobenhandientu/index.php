<?php
include("Controllers/chosobenhandientu.php");

$p = new cHoSoBenhAnDienTu();

// Kiểm tra đăng nhập
if (!isset($_SESSION['user']['tentk']) || empty($_SESSION['user']['tentk'])) {
    echo "<div class='alert alert-warning'>Bạn chưa đăng nhập hoặc thiếu thông tin tài khoản.</div>";
    exit;
}

// Lấy hồ sơ bệnh án
$hoso = $p->getAllHSBADTOfTK1($_SESSION['user']['tentk']);

if (!$hoso || $hoso === 0) {
    echo "<div class='alert alert-info text-center'>Bạn chưa có hồ sơ bệnh án nào.</div>";
    exit;
}

// Tổ chức dữ liệu bệnh nhân + hồ sơ
$patients = [];
while ($row = $hoso->fetch_assoc()) {
    $mabenhnhan = $row['mabenhnhan'] ?? '';
    if (!$mabenhnhan) continue;

    if (!isset($patients[$mabenhnhan])) {
        $patients[$mabenhnhan] = [
            'hotenbenhnhan' => $row['hotenbenhnhan'] ?? '',
            'gioitinh' => $row['gioitinh'] ?? '',
            'ngaysinh' => $row['ngaysinh'] ?? '',
            'nghenghiep' => $row['nghenghiep'] ?? '',
            'dantoc' => $row['dantoc'] ?? '',
            'email' => isset($row['emailbenhnhan']) ? decryptData($row['emailbenhnhan']) : '',
            'quanhe' => $row['moiquanhevoinguoithan'] ?? '',
            'sdtbenhnhan' => isset($row['sdt']) ? decryptData($row['sdt']) : '',
            'tinh/thanhpho' => $row['tentinhthanhpho'] ?? '',
            'xa/phuong' => $row['tenxaphuong'] ?? '',
            'sonha' => $row['sonha'] ?? '',
            'hoso' => []
        ];
    }

    // Xác định là bác sĩ hay chuyên gia
    $isExpert = !empty($row['hotenchuyengia']); // Nếu hotenchuyengia tồn tại → là chuyên gia

    $patients[$mabenhnhan]['hoso'][] = [
        'ngaytao' => $row['ngaykham'] ?? '',
        'chandoan' => $row['chandoan'] ?? '',
        'machitiethoso' => $row['machitiethoso'] ?? '',
        // Lấy avatar
        'img' => $isExpert ? ($row['imgcg'] ?? 'default.png') : ($row['imgbs'] ?? 'default.png'),
        // Lấy tên
        'hoten' => $isExpert ? ($row['hotenchuyengia'] ?? 'Chưa cập nhật') : ($row['hotenbacsi'] ?? 'Chưa cập nhật'),
        // Lấy chuyên ngành / lĩnh vực
        'chuyennganh' => $isExpert ? ($row['tenlinhvuc'] ?? '') : ($row['tenchuyenkhoa'] ?? '')
    ];
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hồ sơ bệnh án</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.container { padding-top: 90px; }
.text-primary { color: #3c1561 !important; }
h4.mb-4 { color: #3c1561; font-weight: bold; font-size: 36px; text-align: center; margin-bottom: 50px; }
.card { border-radius: 12px; border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: all 0.3s ease; }
.card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(0,0,0,0.15); }
.card-body { padding: 30px; }
.doctor-info { display: flex; align-items: center; gap: 30px; margin-top: 20px; }
.doctor-info img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #3c1561; }
.doctor-info h6 { font-size: 18px; color: #333; font-weight: bold; }
.btn-outline-primary { border-color: #3c1561; color: #3c1561; transition: all 0.3s ease; }
.btn-outline-primary:hover { background-color: #3c1561; color: white; }
@media (max-width: 576px) {
    .doctor-info { flex-direction: column; align-items: flex-start; }
    .doctor-info img { margin-bottom: 10px; }
    h4.mb-4 { font-size: 28px; }
    .card-body { padding: 20px; }
}
</style>
</head>
<body>
<div class="container">
<h4 class="mb-4">📋 Danh sách hồ sơ bệnh án</h4>

<?php foreach ($patients as $patient): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="text-center text-primary mb-3">🧑‍⚕️ Thông tin bệnh nhân</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Họ tên:</strong> <?= htmlspecialchars($patient['hotenbenhnhan']) ?></p>
                    <p><strong>Giới tính:</strong> <?= htmlspecialchars($patient['gioitinh']) ?></p>
                    <p><strong>Ngày sinh:</strong> <?= htmlspecialchars($patient['ngaysinh']) ?></p>
                    <p><strong>Nghề nghiệp:</strong> <?= htmlspecialchars($patient['nghenghiep']) ?></p>
                    <p><strong>Dân tộc:</strong> <?= htmlspecialchars($patient['dantoc']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></p>
                    <p><strong>SĐT:</strong> <?= htmlspecialchars($patient['sdtbenhnhan']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($patient['sonha'] . ', ' . $patient['xa/phuong'] . ', ' . $patient['tinh/thanhpho']) ?></p>
                    <p><strong>Quan hệ:</strong> <?= htmlspecialchars($patient['quanhe']) ?></p>
                </div>
            </div>

            <hr>
            <p class="text-center text-white bg-primary py-2 px-3 rounded">Danh sách hồ sơ bệnh án</p>

            <?php if (!empty($patient['hoso'])): ?>
    <?php foreach ($patient['hoso'] as $row): ?>
        <div class="doctor-info">
            <!-- Lấy ảnh đúng theo bác sĩ hoặc chuyên gia -->
            <img src="Assets/img/<?= htmlspecialchars($row['img']) ?>" alt="ảnh bác sĩ/chuyên gia">
            <div>
                <h6>
                    <?= isset($row['capbac']) && !$row['hoten'] ? htmlspecialchars($row['capbac']) . ' - ' : '' ?>
                    <?= strtoupper(htmlspecialchars($row['hoten'])) ?>
                </h6>
                <p>
                    <strong>Chuyên khoa/Lĩnh vực:<?= !empty($row['tenchuyennganh']) ? 'Chuyên khoa/Lĩnh vực:' : '' ?></strong>
                    <?= htmlspecialchars($row['chuyennganh'] ?? '') ?>
                </p>
                <p><strong>Ngày lập hồ sơ:</strong> <?= htmlspecialchars($row['ngaytao']) ?></p>
                <p><strong>Chẩn đoán:</strong> <?= htmlspecialchars($row['chandoan']) ?></p>
                <a href="?action=chitiethosobenhandientu&id=<?= htmlspecialchars($row['machitiethoso']) ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-center">Chưa có hồ sơ bệnh án nào.</p>
<?php endif; ?>

        </div>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>
