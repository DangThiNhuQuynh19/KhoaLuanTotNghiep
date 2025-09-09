<?php
include_once('Controllers/clichxetnghiem.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập.</p>";
    exit;
}

$tentk = $_SESSION['user']['tentk'];
$lich = new cLichXetNghiem();
$result = $lich->getlichxetnghiemtheotentk($tentk);

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
        return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Hoàn thành';
    });
} else {
    $filerlich = $data;
}

if (!empty($filter)) {
    $filerlich = array_filter($filerlich, function($lichxetnghiem) use ($filter) {
        return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === $filter;
    });
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
    </style>
</head>
<body>
    <h2>Lịch Xét Nghiệm</h2>

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
           Hoàn thành <span class="tab-count"><?= count(array_filter($data, function($lichxetnghiem) { return isset($lichxetnghiem['tentrangthai']) && $lichxetnghiem['tentrangthai'] === 'Hoàn thành'; })) ?></span>
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
                <!-- Modal hiện ảnh lớn -->
            <div id="qrModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.8); justify-content:center; align-items:center;">
                <img id="qrModalImg" src="/placeholder.svg" style="max-width:90%; max-height:90%;">
            </div>
            <?php if($filerlich):?>
                <?php foreach ($filerlich as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ngayhen']) ?></td>
                        <td><?= htmlspecialchars($row['giobatdau']) . ' - ' . htmlspecialchars($row['gioketthuc']) ?></td>
                        <td><?= htmlspecialchars($row['tenloaixetnghiem']) ?></td>
                        <td><?= htmlspecialchars($row['tenchuyenkhoa']) ?></td>
                        <td><?= htmlspecialchars($row['tentrangthai']) ?></td>
                        <td>
                            <img src="Assets/img/<?= htmlspecialchars($row['qr']) ?>" alt="QR Code" width="100"
                                style="cursor: pointer;"
                                onclick="showLargeQR(this.src)">
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else:?>
                    <tr>
                        <td colspan="7">Chưa có lịch</td>
                    </tr>
            <?php endif?>
        </tbody>
    </table>
<?php endif; ?>

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
    document.getElementById("qrModal").addEventListener("click", function () {
        this.style.display = "none";
    });
</script>
