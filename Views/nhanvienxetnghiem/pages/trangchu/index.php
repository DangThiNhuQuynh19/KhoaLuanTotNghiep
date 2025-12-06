<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once('Controllers/clichxetnghiem.php');
include_once('Controllers/cbenhnhan.php');

if (!isset($_SESSION["dangnhap"]) || !isset($_SESSION["user"])) {
    echo "<p>Bạn chưa đăng nhập!</p>";
    exit;
}


$cLichXN = new cLichXetNghiem();

// Ngày và trạng thái mặc định
$ngaychon = isset($_GET['ngaychon']) && $_GET['ngaychon'] != '' ? $_GET['ngaychon'] : date('Y-m-d');
$trangthai = isset($_GET['trangthai']) && in_array($_GET['trangthai'], [10,11,12]) ? $_GET['trangthai'] : 11;

$lichXNList = $cLichXN->get_alllichxetnghiem($ngaychon, $trangthai);

$statusMap = [
    10 => ['text'=>'Chờ thanh toán','class'=>'btn-pending'],
    11 => ['text'=>'Đang thực hiện','class'=>'btn-inprogress'],
    12 => ['text'=>'Đã có kết quả','class'=>'btn-done']
];
?>
    <style>
        /* Header */
h2 {
    color: #3498db;
    margin-bottom: 24px;
    letter-spacing: -0.5px;
}

/* Filter Form */
.filter-form {
    background: white;
    padding: 16px;
    border-radius: 8px;
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 12px;
    margin-bottom: 24px;
    box-shadow: 0 1px 6px rgba(52, 152, 219, 0.15);
    align-items: flex-end;
}

.filter-form label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #3498db;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-form input,
.filter-form select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 13px;
    font-family: inherit;
    transition: all 0.3s ease;
    background: white;
}

.filter-form input:focus,
.filter-form select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.filter-form button {
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-form button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.25);
}

/* Table Container */
.table-wrapper {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(52, 152, 219, 0.15);
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #3498db;
    color: white;
}

th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 13px;
}

tbody tr:hover {
    background-color: #f0f8ff;
}

/* Status Column */
.status-column {
    text-align: center;
}

.status-column button {
    border: none;
    padding: 4px 10px;
    border-radius: 16px;
    font-size: 11px;
    font-weight: 600;
    cursor: default;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    transition: all 0.2s ease;
}

/* Status Badges */
.status-pending { background: #d6eaff; color: #00529b; }
.status-inprogress { background: #cce5ff; color: #004085; }
.status-done { background: #a0d8f1; color: #03396c; }

/* Action Icons */
td:last-child {
    text-align: center;
}

.edit-icon,
.view-icon {
    display: inline-block;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 2px 6px;
    border-radius: 4px;
}

.edit-icon { color: #3498db; }
.edit-icon:hover { background: rgba(52, 152, 219, 0.1); transform: scale(1.2); }

.view-icon { color: #2980b9; }
.view-icon:hover { background: rgba(41, 128, 185, 0.1); transform: scale(1.2); }

/* Empty State */
tbody tr td[colspan] {
    text-align: center;
    color: #999;
    padding: 30px 12px;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 1024px) {
    .filter-form { grid-template-columns: 1fr 1fr; }
    .filter-form button { grid-column: 1 / -1; }
    th, td { padding: 10px; font-size: 12px; }
}

@media (max-width: 768px) {
    .filter-form { grid-template-columns: 1fr; padding: 12px; gap: 10px; }
    h2 { font-size: 22px; margin-bottom: 16px; }
    table { font-size: 11px; }
    th, td { padding: 8px; }
    .status-column button { padding: 3px 8px; font-size: 10px; }
    .edit-icon, .view-icon { font-size: 14px; padding: 1px 4px; }
}

@media (max-width: 480px) {
    body { padding: 10px; }
    h2 { font-size: 18px; margin-bottom: 12px; }
    .filter-form { padding: 10px; gap: 8px; }
    th, td { padding: 6px; font-size: 10px; }
    .status-column button { padding: 2px 6px; font-size: 9px; }
}

    </style>
</head>
<body>
<div class="main-container">
    <h2>Danh Sách Lịch Xét Nghiệm</h2>

    <form method="get" class="filter-form">
        <div>
            <label for="ngaychon">Chọn ngày:</label>
            <input type="date" name="ngaychon" id="ngaychon" value="<?= htmlspecialchars($ngaychon) ?>">
        </div>

        <div>
            <label for="trangthai">Trạng thái:</label>
            <select name="trangthai" id="trangthai">
                <?php foreach($statusMap as $key=>$val): ?>
                    <option value="<?= $key ?>" <?= $key == $trangthai ? 'selected':'' ?>><?= $val['text'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Tìm kiếm</button>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>STT</th>
                <th>Mã Bệnh Nhân</th>
                <th>Họ Tên</th>
                <th>SĐT</th>
                <th>Ngày Xét Nghiệm</th>
                <th>Tên Xét Nghiệm</th>
                <th>Thời Gian</th>
                <th class="status-column">Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
            </thead>
            <tbody>
            <?php if($lichXNList && $lichXNList!==-1 && $lichXNList!==0): ?>
                <?php $stt=1; ?>
                <?php foreach($lichXNList as $row): ?>
                    <?php 
                        $statusId = (int)$row['matrangthai'];
                        $statusText = $statusMap[$statusId]['text'] ?? $row['tentrangthai'];
                        $statusClass = $statusMap[$statusId]['class'] ?? '';
                    ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><?= htmlspecialchars($row['mabenhnhan']) ?></td>
                        <td><?= htmlspecialchars($row['hoten']) ?></td>
                        <td><?= htmlspecialchars(decryptData($row['sdt'])) ?></td>
                        <td><?= htmlspecialchars($row['ngayhen']) ?></td>
                        <td><?= htmlspecialchars($row['tenloaixetnghiem']) ?></td>
                        <td><?= htmlspecialchars($row['giobatdau']).' - '.htmlspecialchars($row['gioketthuc']) ?></td>
                        <td class="status-column">
                            <button class="<?= $statusClass ?>"><?= htmlspecialchars($statusText) ?></button>
                        </td>
                        <td>
                            <?php if($statusId === 11): ?>
                                <a href="?action=chinhsua&id=<?= $row['malichxetnghiem'] ?>" title="Chỉnh sửa kết quả">
                                    <span class="edit-icon">✏️</span>
                                </a>
                            <?php endif; ?>
                            &nbsp;
                            <a href="?action=xemchitiet&id=<?= $row['malichxetnghiem'] ?>" title="Xem chi tiết">
                                <span class="view-icon">👁️</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="9">Không có lịch xét nghiệm</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
