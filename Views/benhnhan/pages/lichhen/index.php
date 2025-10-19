<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
include_once('Controllers/cphieukhambenh.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập hoặc thiếu thông tin tài khoản.</p>";
    exit;
}

$tentk = $_SESSION['user']['tentk'];
$pPhieu = new cPhieuKhamBenh();

// Nhận filter trạng thái + ngày
$filter = $_GET['filter'] ?? null;
$selectedDate = $_GET['ngay'] ?? null;  // ngày từ form
$currentDate = date('Y-m-d');

// Lấy danh sách phiếu theo filter + ngày
$phieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, $filter, $selectedDate);

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
                echo "<script>alert('Lịch hẹn đã được hủy thành công.'); window.location.href='?action=lichhen&filter=Đã hủy';</script>";
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

    /* Tab navigation */
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

    /* Bộ lọc ngày */

    .filter-box {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
        background-color: #f8f0fc;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(108, 52, 131, 0.2);
        font-size: 14px;
        max-width: 700px;
        width: 90%;
        padding-left: 170px;
    }

    .filter-box label {
        font-weight: 500;
        color: #4b2a7b;
        margin-bottom: 0;
    }

    .filter-box input[type="date"] {
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    }

    .filter-box input[type="date"]:focus {
        border-color: #552d7d;
        box-shadow: 0 0 6px rgba(85, 45, 125, 0.4);
    }

    .filter-box .btn {
        padding: 6px 14px;
        font-size: 13px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .filter-box .btn-primary {
        background-color: #552d7d;
        border-color: #552d7d;
        color: #fff;
    }

    .filter-box .btn-primary:hover {
        background-color: #6c3483;
        border-color: #6c3483;
    }

    .filter-box .btn-outline-secondary {
        border-color: #aaa0b8;
        color: #552d7d;
        background-color: #fff;
    }

    .filter-box .btn-outline-secondary:hover {
        background-color: #eee5f5;
        color: #552d7d;
    }

    /* Responsive: xếp dọc khi màn hình nhỏ */
    @media (max-width: 480px) {
        .filter-box {
            flex-direction: column;
            align-items: flex-start;
        }
        .filter-box label {
            margin-left: 5px;
        }
    }


    table {
        width: 95%;
        max-width: 1200px;
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
    td { font-size: 12px; }
    tr:nth-child(even) { background-color: #faf5ff; }
    tr:hover { background-color: #f3e5f5; }
    table th:nth-child(9),
        table td:nth-child(9) {
            width: 120px; 
        }
        table th:nth-child(10),
        table td:nth-child(10) {
            width: 100px;
        }
    /* Status badge */
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
    .muted-text { color: #6c757d; font-style: italic; }

    @media (max-width: 768px) {
        table { width: 95%; font-size: 11px; }
        th, td { padding: 8px 4px; }
        .tab-button { padding: 8px 12px; font-size: 12px; }
        .filter-box { flex-direction: column; align-items: stretch; }
    }
    </style>
</head>
<body>

<h2>Lịch Hẹn Khám Bệnh</h2>

<!-- Form lọc ngày -->
<div class="d-flex justify-content-center">
    <form method="get" class="filter-box">
        <input type="hidden" name="action" value="lichhen">
        <?php if (!empty($filter)): ?>
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <?php endif; ?>

        <label for="ngay">Chọn ngày:</label>
        <input type="date" id="ngay" name="ngay"
               value="<?= htmlspecialchars($selectedDate ?? '') ?>">

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-filter"></i> Lọc
        </button>

        <?php if (!empty($selectedDate)): ?>
            <a href="?action=lichhen<?= $filter ? '&filter=' . urlencode($filter) : '' ?>" 
               class="btn btn-outline-secondary btn-sm">
               <i class="fa-solid fa-xmark"></i> Xóa lọc
            </a>
        <?php endif; ?>
    </form>
</div>

<?php
// Đếm số lượng theo trạng thái
$allCount = $chuakhamCount = $dakhamCount = $dahuyCount = 0;
$allPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, null, $selectedDate);
if ($allPhieus && $allPhieus !== -1 && $allPhieus !== 0) $allCount = $allPhieus->num_rows;
$chuakhamPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, 'Chưa khám', $selectedDate);
if ($chuakhamPhieus && $chuakhamPhieus !== -1 && $chuakhamPhieus !== 0) $chuakhamCount = $chuakhamPhieus->num_rows;
$dakhamPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, 'Đã khám', $selectedDate);
if ($dakhamPhieus && $dakhamPhieus !== -1 && $dakhamPhieus !== 0) $dakhamCount = $dakhamPhieus->num_rows;
$dahuyPhieus = $pPhieu->getAllPhieuKhamBenhOfTK($tentk, 'Đã hủy', $selectedDate);
if ($dahuyPhieus && $dahuyPhieus !== -1 && $dahuyPhieus !== 0) $dahuyCount = $dahuyPhieus->num_rows;
?>

<!-- Tab filter -->
<div class="tab-navigation">
    <a href="?action=lichhen<?= $selectedDate ? '&ngay=' . urlencode($selectedDate) : '' ?>" 
       class="tab-button <?= (!$filter) ? 'active' : '' ?>">
        Tất cả <span class="tab-count"><?= $allCount ?></span>
    </a>
    <a href="?action=lichhen&filter=Chưa khám<?= $selectedDate ? '&ngay=' . urlencode($selectedDate) : '' ?>" 
       class="tab-button <?= ($filter === 'Chưa khám') ? 'active' : '' ?>">
        Chưa khám <span class="tab-count"><?= $chuakhamCount ?></span>
    </a>
    <a href="?action=lichhen&filter=Đã khám<?= $selectedDate ? '&ngay=' . urlencode($selectedDate) : '' ?>" 
       class="tab-button <?= ($filter === 'Đã khám') ? 'active' : '' ?>">
        Đã khám <span class="tab-count"><?= $dakhamCount ?></span>
    </a>
    <a href="?action=lichhen&filter=Đã hủy<?= $selectedDate ? '&ngay=' . urlencode($selectedDate) : '' ?>" 
       class="tab-button <?= ($filter === 'Đã hủy') ? 'active' : '' ?>">
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
        <th>Hình Thức</th> 
        <th>Phòng khám</th> 
        <th>Trạng Thái</th>
        <th>Hành động</th>
    </tr>
    </thead>
    <?php if ($phieus === -1): ?>
        <tbody><tr><td colspan="9">Lỗi kết nối.</td></tr></tbody>
    <?php elseif ($phieus === 0): ?>
        <tbody><tr><td colspan="9">Không có lịch hẹn nào được tìm thấy.</td></tr></tbody>
    <?php else: ?>
        <tbody>
        <?php while ($row = $phieus->fetch_assoc()): ?>
            <?php $trangthai = $row['tentrangthai'] ?? ''; ?>
            <tr>
                <td><?= htmlspecialchars($row['maphieukhambenh']) ?></td>
                <td><?= htmlspecialchars($row['hoten']) ?></td>
                <td><?= htmlspecialchars($row['ngaykham']) ?></td>
                <td><?= htmlspecialchars($row['giobatdau']) . ' - ' . htmlspecialchars($row['gioketthuc']) ?></td>
                <td><?= htmlspecialchars($row['tenchuyenkhoa']) ?></td>
                <td><?= htmlspecialchars($row['hotenbacsi'] ?? $row['hotennguoi_kham']) ?></td>
                <td><?= htmlspecialchars($row['hinhthuclamviec'] ?? '-') ?></td> 
                <td><?= htmlspecialchars($row['tenphongdaydu'] ?? '-') ?></td> 
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
                    <?php if ($trangthai === 'Đã hủy' || $trangthai === 'Đã khám'): ?>
                        <span class="muted-text">-</span>
                    <?php else: ?>
                        <a href="?action=lichhen&cancel_id=<?= $row['maphieukhambenh'] ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?><?= $selectedDate ? '&ngay=' . urlencode($selectedDate) : '' ?>" 
                           class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?');">
                           <i class="fa-solid fa-trash"></i>
                        </a>
                        <a href="?action=lichhen&update_id=<?= $row['maphieukhambenh'] ?><?= $filter ? '&filter=' . urlencode($filter) : '' ?><?= $selectedDate ? '&ngay=' . urlencode($selectedDate) : '' ?>" 
                           class="btn btn-warning btn-sm">
                           <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    <?php endif; ?>
</table>

</body>
</html>
