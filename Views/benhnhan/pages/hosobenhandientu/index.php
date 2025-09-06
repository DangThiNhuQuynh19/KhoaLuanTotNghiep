<?php
session_start();
include("Controllers/chosobenhandientu.php");
$p = new cHoSoBenhAnDienTu();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['tentk'])) {
    echo "<div class='alert alert-warning'>Bạn chưa đăng nhập hoặc thiếu thông tin tài khoản.</div>";
    exit;
}

$tentk = $_SESSION['user']['tentk'];
$tbl = $p->getAllHSBADTOfTK($tentk);
if ($tbl === -1) {
    echo "<div class='alert alert-danger'>Lỗi kết nối cơ sở dữ liệu!</div>";
    exit;
} elseif ($tbl === 0) {
    echo "
    <div class='container' style ='padding-top:50px;'>
        <div class='empty-state-container'>
            <div class='empty-state-card'>
                <div class='empty-state-icon'>
                    <svg width='80' height='80' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.89 22 5.99 22H18C19.1 22 20 21.1 20 20V8L14 2Z' stroke='#3c1561' stroke-width='2' fill='none'/>
                        <polyline points='14,2 14,8 20,8' stroke='#3c1561' stroke-width='2' fill='none'/>
                        <line x1='16' y1='13' x2='8' y2='13' stroke='#3c1561' stroke-width='2'/>
                        <line x1='16' y1='17' x2='8' y2='17' stroke='#3c1561' stroke-width='2'/>
                        <polyline points='10,9 9,9 8,9' stroke='#3c1561' stroke-width='2'/>
                    </svg>
                </div>
                <h5 class='empty-state-title'>Chưa có hồ sơ bệnh án nào</h5>
                <p class='empty-state-description'>
                    Hiện tại bạn chưa có hồ sơ bệnh án nào trong hệ thống. 
                    Hồ sơ sẽ được tạo sau khi bạn thực hiện khám bệnh.
                </p>
                <div class='empty-state-actions'>
                    <a href='?action=lienhe' class='btn btn-outline-primary'>Liên hệ hỗ trợ</a>
                </div>
            </div>
        </div>
    </div>
    <style>
        .empty-state-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 40px 20px;
        }
        .empty-state-card {
            background: white;
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(60, 21, 97, 0.1);
            border: 1px solid rgba(60, 21, 97, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .empty-state-icon {
            margin-bottom: 30px;
            opacity: 0.8;
        }
        .empty-state-title {
            color: #3c1561;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .empty-state-description {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        .empty-state-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .empty-state-actions .btn {
            padding: 12px 24px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        @media (max-width: 576px) {
            .empty-state-card {
                padding: 40px 20px;
            }
            .empty-state-title {
                font-size: 24px;
            }
            .empty-state-actions {
                flex-direction: column;
            }
            .empty-state-actions .btn {
                width: 100%;
            }
        }
    </style>
    ";
    exit;
} else {
    $patients = [];
    while ($row = $tbl->fetch_assoc()) {
        $mabenhnhan = $row['mabenhnhan'];
        if (!isset($patients[$mabenhnhan])) {
            $patients[$mabenhnhan] = [
                'hotenbenhnhan' => $row['hotenbenhnhan'],
                'gioitinh' => $row['gioitinh'],
                'ngaysinh' => $row['ngaysinh'],
                'nghenghiep' => $row['nghenghiep'],
                'dantoc' => $row['dantoc'],
                'email' => $row['email'],
                'quanhe' => $row['quanhe'],
                'sdtbenhnhan' => $row['sdtbenhnhan'],
                'tinh/thanhpho' => $row['tinh/thanhpho'],
                'quan/huyen' => $row['quan/huyen'],
                'xa/phuong' => $row['xa/phuong'],
                'sonha' => $row['sonha'],
                'hoso' => []
            ];
        }
        $patients[$mabenhnhan]['hoso'][] = $row;
    }
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
        .container { padding-top: 100px; }
        .bg-primary { background-color: #3c1561 !important; }
        .text-primary { color: #3c1561 !important; }
        h4.mb-4 {
            color: #3c1561;
            font-weight: bold;
            font-size: 36px;
            text-align: center;
            margin-top: 50px;
        }
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }
        .card-body { padding: 30px; }
        .card-title {
            font-size: 24px;
            font-weight: bold;
            color: #3c1561;
        }
        .doctor-info {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-top: 30px;
        }
        .doctor-info img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3c1561;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .doctor-info div {
            flex-grow: 1;
        }
        .doctor-info h6 {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        .doctor-info p { margin-bottom: 10px; }
        .btn-outline-primary {
            border-color: #3c1561;
            color: #3c1561;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background-color: #3c1561;
            color: white;
        }
        @media (max-width: 576px) {
            .doctor-info {
                flex-direction: column;
                align-items: flex-start;
            }
            .doctor-info img {
                margin-bottom: 15px;
            }
            .container h4 {
                font-size: 28px;
            }
            .card-body { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h4 class="mb-4">📋 Danh sách hồ sơ bệnh án</h4>

    <?php foreach ($patients as $mabenhnhan => $patient): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-center text-primary mb-3">🧑‍⚕️ Thông tin bệnh nhân</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> <?= htmlspecialchars($patient['hotenbenhnhan']) ?></p>
                        <p><strong>Giới tính:</strong> <?= htmlspecialchars($patient['gioitinh']) ?></p>
                        <p><strong>Ngày sinh:</strong> <?= htmlspecialchars($patient['ngaysinh']) ?></p>
                        <p><strong>Nghề nghiệp:</strong> <?= htmlspecialchars($patient['nghenghiep']) ?></p>
                       
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></p>
                        <p><strong>SĐT:</strong> <?= htmlspecialchars($patient['sdtbenhnhan']) ?></p>
                        <p><strong>Địa chỉ:</strong>
                            <?= htmlspecialchars($patient['sonha']). ', ' .
                                htmlspecialchars($patient['xa/phuong']). ', ' .
                                htmlspecialchars($patient['quan/huyen']). ', ' .
                                htmlspecialchars($patient['tinh/thanhpho']) ?>
                        </p>
                        <p><strong>Quan hệ:</strong> <?= htmlspecialchars($patient['quanhe']) ?></p>
                    </div>
                </div>
                <hr>
                <p class="text-center text-white bg-primary py-2 px-3 rounded">Danh sách hồ sơ bệnh án</p>

                <?php foreach ($patient['hoso'] as $row): ?>
                    <div class="doctor-info">
                        <img src="Assets/img/<?= htmlspecialchars($row['imgbs']) ?>" alt="ảnh bác sĩ">
                        <div>
                            <h6 class="mb-1"><?= htmlspecialchars($row['capbac']) ?> - <?= strtoupper(htmlspecialchars($row['hoten'])) ?></h6>
                            <p class="mb-1"><strong>Chuyên khoa:</strong> <?= strtoupper(htmlspecialchars($row['tenchuyenkhoa'])) ?></p>
                            <p class="mb-1"><strong>Ngày lập hồ sơ:</strong> <?= htmlspecialchars($row['ngaytao']) ?></p>
                            <p class="mb-1"><strong>Chẩn đoán:</strong> <?= htmlspecialchars($row['chandoan']) ?></p>
                            <a href="?action=chitiethosobenhandientu&id=<?= htmlspecialchars($row['machitiethoso']) ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>