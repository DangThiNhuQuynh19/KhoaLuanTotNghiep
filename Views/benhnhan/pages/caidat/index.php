<?php
include_once(__DIR__ .'Controllers/cBenhNhan.php');
include_once(__DIR__ ."Assets/config.php");

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập.</p>";
    exit;
}

$email = $_SESSION['user']['tentk'];
$pBenhNhan = new cBenhNhan();
$taikhoan = $pBenhNhan->getbenhnhanbytk($email);
$benhnhans = $pBenhNhan->getAllBenhNhanByTK($taikhoan['mabenhnhan']);

$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

$filteredBenhnhans = [];
if ($currentTab === 'active') {
    $filteredBenhnhans = array_filter($benhnhans, function($bn) {
        return isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Đang hoạt động';
    });
} elseif ($currentTab === 'inactive') {
    $filteredBenhnhans = array_filter($benhnhans, function($bn) {
        return isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Ngưng hoạt động';
    });
} else {
    $filteredBenhnhans = $benhnhans;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8e3f8c6c44.js" crossorigin="anonymous"></script>
    <title>Hồ sơ bệnh nhân</title>
    <style>
        :root {
            --custom-purple: rgb(85, 45, 125);
            --custom-purple-dark: rgb(70, 35, 110);
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            margin: 0;
            padding-top: 100px;
        }

        h2 {
            text-align: center;
            color: #6c3483;
            margin-bottom: 20px;
        }

        /* Nút tạo hồ sơ đưa lên đầu */
        .them {
            width: 90%;
            max-width: 1100px;
            display: flex;
            justify-content: flex-end;
            margin: 0 auto 15px auto;
        }

        .btn-primary {
            background-color: var(--custom-purple);
            border-color: var(--custom-purple);
            border-radius: 50px;
            font-weight: 500;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--custom-purple-dark);
            border-color: var(--custom-purple-dark);
        }

        .tab-navigation {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: center;
            border-bottom: 2px solid #eee;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 12px 24px;
            margin: 0 5px;
            cursor: pointer;
            font-size: 13px;
            color: #666;
            text-decoration: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            color: var(--custom-purple);
        }

        .tab-button.active {
            color: var(--custom-purple);
            border-bottom-color: var(--custom-purple);
            font-weight: 600;
        }

        .tab-count {
            background-color: var(--custom-purple);
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 8px;
        }

        table {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(108, 52, 131, 0.2);
        }

        th, td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #3c1561;
            color: white;
            text-transform: uppercase;
            font-size: 13px;
        }

        td {
            font-size: 12px;
        }

        tr:nth-child(even) {
            background-color: #faf5ff;
        }

        tr:hover {
            background-color: #f3e5f5;
        }

        .action-buttons button {
            margin: 0 4px;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: #9b59b6;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
            margin: 0 4px;
        }

        .btn-edit:hover {
            background-color: #884ea0;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        p {
            text-align: center;
            font-size: 18px;
            color: #c0392b;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Tăng độ rộng cho cột Trạng thái và Hành động */
        th:nth-child(7),
        td:nth-child(7) {
            width: 110px;
        }

        th:nth-child(8),
        td:nth-child(8) {
            width: 170px;
        }
        th:nth-child(9),
        td:nth-child(9) {
            width: 130px;
        }

    </style>
</head>
<body>

<h2>Danh sách bệnh nhân</h2>

<div class="them">
    <a href="?action=taohoso" class="btn btn-primary">+ Tạo hồ sơ</a>
</div>

<div class="tab-navigation">
    <a href="?action=caidat&tab=all" class="tab-button <?= $currentTab === 'all' ? 'active' : '' ?>">
        Tất cả <span class="tab-count"><?= count($benhnhans) ?></span>
    </a>
    <a href="?action=caidat&tab=active" class="tab-button <?= $currentTab === 'active' ? 'active' : '' ?>">
        Đang hoạt động <span class="tab-count"><?= count(array_filter($benhnhans, fn($bn) => isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Đang hoạt động')) ?></span>
    </a>
    <a href="?action=caidat&tab=inactive" class="tab-button <?= $currentTab === 'inactive' ? 'active' : '' ?>">
        Ngưng hoạt động <span class="tab-count"><?= count(array_filter($benhnhans, fn($bn) => isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Ngưng hoạt động')) ?></span>
    </a>
</div>

<?php if (!empty($filteredBenhnhans)) : ?>
    <table>
        <thead>
            <tr>
                <th>Mã Bệnh Nhân</th>
                <th>Họ tên</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th>Quan hệ với người giám hộ</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filteredBenhnhans as $bn) : ?>
                <tr>
                    <td><?= htmlspecialchars($bn['mabenhnhan']) ?></td>
                    <td><?= htmlspecialchars($bn['hoten']) ?></td>
                    <td><?= htmlspecialchars($bn['ngaysinh']) ?></td>
                    <td><?= htmlspecialchars($bn['gioitinh']) ?></td>
                    <td><?= htmlspecialchars(decryptData($bn['sdt'])); ?></td>
                    <td><?= htmlspecialchars($bn['sonha']) . ', ' . htmlspecialchars($bn['tenxaphuong']) . ', ' . htmlspecialchars($bn['tentinhthanhpho']); ?></td>
                    <td><?= htmlspecialchars($bn['moiquanhevoinguoithan']) ?></td>
                    <td>
                        <?php 
                        $status = $bn['tentrangthai'] ?? 'Đang hoạt động';
                        if ($status === 'Đang hoạt động') {
                            echo '<span class="status-badge status-active">Đang hoạt động</span>';
                        } else {
                            echo '<span class="status-badge status-inactive">Ngưng hoạt động</span>';
                        }
                        ?>
                    </td>
                    <?php if($bn['tentrangthai']== 'Đang hoạt động'): ?>
                        <td class="action-buttons">
                            <a href="?action=suahoso&mabenhnhan=<?= $bn['mabenhnhan'] ?>" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="?action=xoahoso&mabenhnhan=<?= $bn['mabenhnhan'] ?>" method="post" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn ngưng hoạt động hồ sơ này không?');">
                                <input type="hidden" name="mabenhnhan" value="<?= $bn['mabenhnhan'] ?>">
                                <button type="submit" class="btn-delete"><i class="fa-solid fa-power-off"></i></button>
                            </form>
                        </td>
                    <?php else: ?>
                        <td></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <p>
        <?php 
        if ($currentTab === 'active') {
            echo "Không tìm thấy hồ sơ bệnh nhân đang hoạt động.";
        } elseif ($currentTab === 'inactive') {
            echo "Không tìm thấy hồ sơ bệnh nhân ngưng hoạt động.";
        } else {
            echo "Không tìm thấy hồ sơ bệnh nhân nào.";
        }
        ?>
    </p>
<?php endif; ?>

</body>
</html>
