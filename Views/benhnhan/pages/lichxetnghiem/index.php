<?php
include_once('Controllers/clichxetnghiem.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập.</p>";
    exit;
}

$tentk = $_SESSION['user']['tentk'];

// Lấy ngày filter từ GET
$ngay = isset($_GET['ngay']) ? $_GET['ngay'] : null;

$lich = new cLichXetNghiem();
$result = $lich->getlichxetnghiemtheotentk($tentk, $ngay);

$data = [];
if ($result && $result !== -1 && $result !== 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$filerlich = [];
if ($currentTab === 'wait') {
    $filerlich = array_filter($data, function($lichxetnghiem) {
        return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Chờ thanh toán';
    });
} elseif ($currentTab === 'active') {
    $filerlich = array_filter($data, function($lichxetnghiem) {
        return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Đang thực hiện';
    });
} elseif ($currentTab === 'complete') {
    $filerlich = array_filter($data, function($lichxetnghiem) {
        return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Đã có kết quả';
    });
} else {
    $filerlich = $data;
}

// Bộ lọc theo trạng thái thêm
if (!empty($filter)) {
    $filerlich = array_filter($filerlich, function($lichxetnghiem) use ($filter) {
        return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === $filter;
    });
}

// Bộ lọc theo ngày
if (!empty($ngay)) {
    $filerlich = array_filter($filerlich, function($lichxetnghiem) use ($ngay) {
        return isset($lichxetnghiem['ngayhen']) && $lichxetnghiem['ngayhen'] === $ngay;
    });
}

// --- Pagination settings: 10 items / page ---
$perPage = 10;
$totalRows = count($filerlich);
$totalPages = ($totalRows > 0) ? intval(ceil($totalRows / $perPage)) : 1;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

// Reindex filtered array and slice for current page
$filerlich_values = array_values($filerlich);
$pagedData = array_slice($filerlich_values, $offset, $perPage);

// Helper to build base query preserving action, tab, filter, ngay
function build_base_query($overrides = []) {
    $params = array_merge([
        'action' => 'lichxetnghiem',
        'tab' => isset($_GET['tab']) ? $_GET['tab'] : null,
        'filter' => isset($_GET['filter']) ? $_GET['filter'] : null,
        'ngay' => isset($_GET['ngay']) ? $_GET['ngay'] : null,
    ], $overrides);
    // remove nulls
    foreach ($params as $k => $v) {
        if ($v === null || $v === '') unset($params[$k]);
    }
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Xét Nghiệm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding-bottom: 20px; /* footer spacing requested */
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
            vertical-align: middle;
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
        /* CSS bộ lọc ngày */
        .date-filter-form {
            width: 90%;
            max-width: 700px;
            margin: 0 auto 20px auto;
            background-color: #f8f0fc;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(108, 52, 131, 0.2);
            font-size: 14px;
        }

        .date-filter-form label {
            font-weight: 500;
            color: #4b2a7b;
        }

        .date-filter-form .date-input {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .date-filter-form .date-input:focus {
            border-color: #552d7d;
            box-shadow: 0 0 6px rgba(85, 45, 125, 0.4);
        }

        .date-filter-form .btn {
            padding: 6px 14px;
            font-size: 13px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .date-filter-form .btn-primary {
            background-color: #552d7d;
            border-color: #552d7d;
        }

        .date-filter-form .btn-primary:hover {
            background-color: #6c3483;
            border-color: #6c3483;
        }

        .date-filter-form .btn-secondary {
            background-color: #aaa0b8;
            border-color: #aaa0b8;
        }

        .date-filter-form .btn-secondary:hover {
            background-color: #9187a5;
            border-color: #9187a5;
        }

        /* pagination styling */
        .pagination { margin-top: 18px; }
        .app-footer { height: 20px; min-height: 20px; }

        /* QR modal */
        #qrModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }
        #qrModalImg { max-width: 90%; max-height: 90%; }

        /* Responsive */
        @media (max-width: 768px) {
            table { width: 95%; font-size: 11px; }
            th, td { padding: 8px 6px; }
            .tab-button { padding: 8px 12px; font-size: 12px; }
            .date-filter-form { padding: 10px; }
        }

    </style>
</head>
<body>
    <h2>Lịch Xét Nghiệm</h2>

  <!-- Form chọn ngày -->
    <form method="get" class="date-filter-form d-flex justify-content-center align-items-center gap-2 mb-4 flex-wrap">
        <input type="hidden" name="action" value="lichxetnghiem">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($currentTab) ?>">

        <label for="ngay" class="me-2 mb-0">Chọn ngày:</label>
        <input type="date" id="ngay" name="ngay" value="<?= htmlspecialchars($ngay) ?>" class="date-input">

        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>

        <?php if($ngay): ?>
            <a href="?action=lichxetnghiem&tab=<?= $currentTab ?>" class="btn btn-secondary btn-sm">Bỏ lọc</a>
        <?php endif; ?>
    </form>

    <?php if ($result === -1): ?>
        <p class="message text-danger">Lỗi kết nối cơ sở dữ liệu.</p>
    <?php elseif ($result === 0 || empty($data)): ?>
        <p class="message">Bạn chưa có lịch xét nghiệm nào.</p>
    <?php else: ?>
    <div class="tab-navigation">
        <!-- Fix tab navigation with correct active classes and count functions -->
        <a href="?action=lichxetnghiem&tab=all" class="tab-button <?= $currentTab === 'all' ? 'active' : '' ?>">
            Tất cả <span class="tab-count"><?= count($data) ?></span>
        </a>
        <a href="?action=lichxetnghiem&tab=wait" class="tab-button <?= $currentTab === 'wait' ? 'active' : '' ?>">
            Chờ thanh toán <span class="tab-count"><?= count(array_filter($data, function($lichxetnghiem) { return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Chờ thanh toán'; })) ?></span>
        </a>
        <a href="?action=lichxetnghiem&tab=active" class="tab-button <?= $currentTab === 'active' ? 'active' : '' ?>">
            Đang thực hiện <span class="tab-count"><?= count(array_filter($data, function($lichxetnghiem) { return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Đang thực hiện'; })) ?></span>
        </a>
        <a href="?action=lichxetnghiem&tab=complete" class="tab-button <?= $currentTab === 'complete' ? 'active' : '' ?>">
           Hoàn thành <span class="tab-count"><?= count(array_filter($data, function($lichxetnghiem) { return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Đã có kết quả'; })) ?></span>
        </a>
    </div>

    <table class="custom-table">
        <thead>
            <tr>
                <th>Ngày Xét Nghiệm</th>
                <th>Thời Gian</th>
                <th>Loại Xét Nghiệm</th>
                <th>Chuyên Khoa</th>
                <th>Trạng Thái</th>
                <th>Mã QR</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pagedData)): ?>
                <?php foreach ($pagedData as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ngayhen']) ?></td>
                        <td><?= htmlspecialchars($row['giobatdau']) . ' - ' . htmlspecialchars($row['gioketthuc']) ?></td>
                        <td><?= htmlspecialchars($row['tenloaixetnghiem']) ?></td>
                        <td><?= htmlspecialchars($row['tenchuyenkhoa']) ?></td>
                        <td><?= htmlspecialchars($row['tentrangthai']) ?></td>
                        <td>
                            <?php if (!empty($row['qr'])): ?>
                                <img src="Assets/img/<?= htmlspecialchars($row['qr']) ?>" alt="QR Code" width="100"
                                     style="cursor: pointer;"
                                     onclick="showLargeQR(this.src)">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Chưa có lịch</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <!-- Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= ($page > 1) ? build_base_query(['page' => $page - 1]) : '#' ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php
            // Determine range of pages to show (simple window)
            $start = 1;
            $end = $totalPages;
            if ($totalPages > 7) {
                $start = max(1, $page - 3);
                $end = min($totalPages, $page + 3);
                if ($start === 1) $end = 7;
                if ($end === $totalPages) $start = max(1, $totalPages - 6);
            }
            for ($p = $start; $p <= $end; $p++): ?>
                <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= build_base_query(['page' => $p]) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= ($page < $totalPages) ? build_base_query(['page' => $page + 1]) : '#' ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

<?php endif; ?>

<!-- QR Modal (moved outside table) -->
<div id="qrModal">
    <img id="qrModalImg" src="/placeholder.svg" alt="QR lớn">
</div>

<!-- Footer spacer -->
<footer class="app-footer"></footer>

</body>
</html>

<script>
    // Bắt sự kiện click ảnh nhỏ
    function showLargeQR(src) {
        const modal = document.getElementById("qrModal");
        const modalImg = document.getElementById("qrModalImg");
        modalImg.src = src;
        modal.style.display = "flex";
    }

    // Đóng modal khi click ra ngoài ảnh
    document.getElementById("qrModal").addEventListener("click", function (e) {
        // nếu click vào chính ảnh thì không đóng, click vào nền thì đóng
        const img = document.getElementById("qrModalImg");
        if (e.target === img) return;
        this.style.display = "none";
    });
</script>