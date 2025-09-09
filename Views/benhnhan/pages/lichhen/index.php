<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
include_once('Controllers/cphieukhambenh.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập hoặc thiếu thông tin tài khoản.</p>";
    exit;
}

$tentk = $_SESSION['user']['tentk'];
$pPhieu = new cPhieuKhamBenh();
$filter = $_GET['filter'] ?? null;
$currentDate = date('Y-m-d');

if ($filter === 'Đã hủy') {
    $phieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, $filter);
} elseif ($filter === 'Đã khám') {
    $phieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, $filter);
} elseif ($filter === 'Chưa khám') {
    $phieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, $filter);
} else {
    $phieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, $filter);
}

// Xử lý hủy lịch hẹn
if (isset($_GET['cancel_id'])) {
    $maphieukb = $_GET['cancel_id'];
    $phieu = $pPhieu->getPhieuKhamBenhOfIDPK($maphieukb);
    if ($phieu) {
        $ngaykham = $phieu['ngaykham'];
        if ($ngaykham == $currentDate) {
            echo "<script>alert('Không thể hủy lịch hẹn vì sắp tới giờ khám.');</script>";
        } else {
            $malichlamviec = $phieu['malichlamviec'];
            $result = $pPhieu->cancelPhieuKhamBenh($maphieukb);
            if ($result) {
                echo "<script>alert('Lịch hẹn đã được hủy thành công.'); window.location.href='?action=lichhen';</script>";
            } else {
                echo "<script>alert('Lỗi khi hủy lịch hẹn. Vui lòng thử lại sau.');</script>";
            }
        }
    } else {
        echo "<script>alert('Không tìm thấy phiếu khám với mã này.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch hẹn khám bệnh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Added Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Added missing status badge styles */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-completed {
            background-color: #d1edff;
            color: #0c5460;
            border: 1px solid #b8daff;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .muted-text {
            color: #6c757d;
            font-style: italic;
        }

        .btn-sm {
            margin: 0 2px;
        }

        /* Added responsive design */
        @media (max-width: 768px) {
            table {
                width: 95%;
                font-size: 11px;
            }
            
            th, td {
                padding: 8px 4px;
            }
            
            .tab-button {
                padding: 8px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<h2>Lịch Hẹn Khám Bệnh</h2>

<?php
$allCount = 0;
$chuakhamCount = 0;
$dakhamCount = 0;
$dahuyCount = 0;

// Get counts for each status
$allPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, null);
if ($allPhieus && $allPhieus !== -1 && $allPhieus !== 0) {
    $allCount = $allPhieus->num_rows;
}

$chuakhamPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, 'Chưa khám');
if ($chuakhamPhieus && $chuakhamPhieus !== -1 && $chuakhamPhieus !== 0) {
    $chuakhamCount = $chuakhamPhieus->num_rows;
}

$dakhamPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, 'Đã khám');
if ($dakhamPhieus && $dakhamPhieus !== -1 && $dakhamPhieus !== 0) {
    $dakhamCount = $dakhamPhieus->num_rows;
}

$dahuyPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, 'Đã hủy');
if ($dahuyPhieus && $dahuyPhieus !== -1 && $dahuyPhieus !== 0) {
    $dahuyCount = $dahuyPhieus->num_rows;
}
?>

<!-- Fixed tab counts to show correct numbers for each filter -->
<div class="tab-navigation">
    <a href="?action=lichhen" class="tab-button <?= (!$filter) ? 'active' : '' ?>">
        Tất cả
        <span class="tab-count"><?= $allCount ?></span>
    </a>
    <a href="?action=lichhen&filter=Chưa khám" class="tab-button <?= ($filter === 'Chưa khám') ? 'active' : '' ?>">
        Chưa khám <span class="tab-count"><?= $chuakhamCount ?></span>
    </a>
    <a href="?action=lichhen&filter=Đã khám" class="tab-button <?= ($filter === 'Đã khám') ? 'active' : '' ?>">
        Đã khám <span class="tab-count"><?= $dakhamCount ?></span>
    </a>
    <a href="?action=lichhen&filter=Đã hủy" class="tab-button <?= ($filter === 'Đã hủy') ? 'active' : '' ?>">
        Đã hủy <span class="tab-count"><?= $dahuyCount ?></span>
    </a>
</div>
<table>
        <thead>
        <tr>
            <th>Mã Lịch Hẹn</th>
            <th>Bệnh Nhân</th>
            <th>Ngày Khám</th>
            <th>Thời Gian</th>
            <th>Khoa</th>
            <th>Bác Sĩ</th>
            <th>Trạng Thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <?php if ($phieus === -1): ?>
            <tbody>
                <tr>
                    <td colspan="8">Lỗi kết nối.</td>
                </tr>
            </tbody>
        <?php elseif ($phieus === 0): ?>
            <tbody>
                <tr>
                    <td colspan="8">Không có lịch hẹn nào được tìm thấy.</td>
                </tr>
            </tbody>
        <?php else: ?>
            <tbody>
            <?php while ($row = $phieus->fetch_assoc()): ?>
                <?php
                $trangthai = $row['tentrangthai'] ?? '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['maphieukhambenh']) ?></td>
                    <td><?= htmlspecialchars($row['hoten']) ?></td>
                    <td><?= htmlspecialchars($row['ngaykham']) ?></td>
                    <td><?= htmlspecialchars($row['giobatdau']) . ' - ' . htmlspecialchars($row['gioketthuc']) ?></td>
                    <td><?= htmlspecialchars($row['tenchuyenkhoa']) ?></td>
                    <td><?= htmlspecialchars($row['hotenbacsi']) ?></td>
                    <!-- Added status badge column -->
                    <td>
                        <?php if ($trangthai === 'Đã hủy'): ?>
                            <span class="status-badge status-cancelled">Đã hủy</span>
                        <?php elseif ($trangthai === 'Đã khám'): ?>
                            <span class="status-badge status-completed">Đã khám</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">Chưa khám</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if ($trangthai === 'Đã hủy') {
                            echo '<span class="muted-text">-</span>';
                        } elseif ($trangthai === 'Đã khám') {
                            echo '<span class="muted-text">-</span>';
                        } else {
                            echo '<a href="?action=lichhen&cancel_id=' . $row['maphieukhambenh'] . '" class="btn btn-danger btn-sm"
                                    onclick="return confirm(\'Bạn có chắc chắn muốn hủy lịch hẹn này?\');"><i class="fa-solid fa-trash"></i></a>
                                <a href="?action=lichhen&update_id=' . $row['maphieukhambenh'] . '" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
<?php endif; ?>

</body>
</html>
