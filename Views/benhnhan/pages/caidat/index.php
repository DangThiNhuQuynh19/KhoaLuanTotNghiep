<?php
session_start();
include_once('Controllers/cBenhNhan.php');
include_once("Assets/config.php");
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập.</p>";
    exit;
}

$email = $_SESSION['user']['tentk'];
$pBenhNhan = new cBenhNhan();
$taikhoan = $pBenhNhan ->getbenhnhanbytk($email);
$benhnhans = $pBenhNhan->getAllBenhNhanByTK($taikhoan['mabenhnhan']);

$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

$filteredBenhnhans = [];
if ($currentTab === 'active') {
    // Filter for active patients (assuming status field exists)
    $filteredBenhnhans = array_filter($benhnhans, function($bn) {
        return isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Đang hoạt động';
    });
} elseif ($currentTab === 'cancelled') {
    // Filter for cancelled patients
    $filteredBenhnhans = array_filter($benhnhans, function($bn) {
        return isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Đã hủy';
    });
} else {
    // Show all patients
    $filteredBenhnhans = $benhnhans;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Hồ sơ bệnh nhân</title>
    <style>
        :root {
            --custom-purple: rgb(85, 45, 125);
            --custom-purple-dark: rgb(70, 35, 110);
            --input-border: #ced4da;
            --input-focus: var(--custom-purple);
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
            margin-bottom: 30px;
        }

        /* Added tab navigation styles */
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
            text-decoration: none;
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
        td{
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
            transition: background-color 0.3s ease;
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
        .btn-primary{
            background-color: var(--custom-purple);
            border-color: var(--custom-purple);
            border-radius: 50px;
            font-weight: 500;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--custom-purple-dark);
            border-color: var(--custom-purple-dark);
        }
        .them {
            width: 85%;
            display: flex;
            justify-content: flex-end;
            margin: 10px auto;
        }

        /* Added status badge styles */
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

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h2>Hồ sơ bệnh nhân</h2>
<div class="tab-navigation">
    <a href="?action=caidat&tab=all" class="tab-button <?= $currentTab === 'all' ? 'active' : '' ?>">
        Tất cả <span class="tab-count"><?= count($benhnhans) ?></span>
    </a>
    <a href="?action=caidat&tab=active" class="tab-button <?= $currentTab === 'active' ? 'active' : '' ?>">
        Đang sử dụng <span class="tab-count"><?= count(array_filter($benhnhans, function($bn) { return isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Đang hoạt động'; })) ?></span>
    </a>
    <a href="?action=caidat&tab=cancelled" class="tab-button <?= $currentTab === 'cancelled' ? 'active' : '' ?>">
        Đã hủy <span class="tab-count"><?= count(array_filter($benhnhans, function($bn) { return isset($bn['tentrangthai']) && $bn['tentrangthai'] === 'Đã hủy'; })) ?></span>
    </a>
</div>

<div class="them">
    <a href="?action=taohoso" class="btn btn-primary" style ="font-size: 14px;"> + Tạo hồ sơ</a>
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
                    <td><?= htmlspecialchars($bn['gioitinh'])?></td>
                    <td><?= htmlspecialchars(decryptData($bn['sdt'])); ?></td>
                    <td><?= htmlspecialchars($bn['sonha']) . ', ' . htmlspecialchars($bn['tenxaphuong']) . ', ' . htmlspecialchars($bn['tentinhthanhpho']);?></td>
                    <td>
                        <?php 
                        $status = isset($bn['tentrangthai']) ? $bn['tentrangthai'] : 'active';
                        if ($status === 'Đang hoạt động') {
                            echo '<span class="status-badge status-active">Đang sử dụng</span>';
                        } elseif ($status === 'Đã hủy') {
                            echo '<span class="status-badge status-cancelled">Đã hủy</span>';
                        } else {
                            echo '<span class="status-badge status-active">Đang sử dụng</span>';
                        }
                        ?>
                    </td>
                    <?php if($bn['tentrangthai']== 'Đang hoạt động'): ?>
                        <td class="action-buttons">
                            <a href="?action=suahoso&mabenhnhan=<?= $bn['mabenhnhan'] ?>" class="btn-edit"> <i class="fa-solid fa-pen-to-square"></i></a>

                            <form action="?action=xoahoso&mabenhnhan=<?= $bn['mabenhnhan'] ?>" method="post" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn hủy hồ sơ này không?');">
                                <input type="hidden" name="mabenhnhan" value="<?= $bn['mabenhnhan'] ?>">
                                <button type="submit" class="btn-delete"> <i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
<?php else : ?>
    <p>
        <?php 
        if ($currentTab === 'active') {
            echo "Không tìm thấy hồ sơ bệnh nhân đang sử dụng nào.";
        } elseif ($currentTab === 'cancelled') {
            echo "Không tìm thấy hồ sơ bệnh nhân đã hủy nào.";
        } else {
            echo "Không tìm thấy hồ sơ bệnh nhân nào.";
        }
        ?>
    </p>
<?php endif; ?>

</body>
</html>
