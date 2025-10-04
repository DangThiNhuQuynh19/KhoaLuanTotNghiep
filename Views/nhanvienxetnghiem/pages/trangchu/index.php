<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();

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

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh Sách Lịch Xét Nghiệm</title>
<style>
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f5f7fa; margin:0; padding:0;}
.container { max-width:1200px; margin:40px auto; padding:20px; background:#fff; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#6c3483; margin-bottom:30px;}
.filter-form { display:flex; justify-content:flex-start; align-items:center; gap:10px; margin-bottom:20px;}
.filter-form input[type="date"], .filter-form select { padding:6px 12px; border-radius:6px; border:1px solid #ccc;}
.filter-form button { padding:6px 15px; border:none; border-radius:6px; background:#6c3483; color:#fff; cursor:pointer;}
.filter-form button:hover { background:#4b0082; }

table { width:100%; border-collapse: collapse;}
th, td { padding:12px 10px; border-bottom:1px solid #ddd; text-align:left;}
th { background:#6c3483; color:#fff;}
tr:hover { background: #f0e7ff;}
.edit-icon { color:#0dcaf0; cursor:pointer; font-size:18px; }
.edit-icon:hover { color:#0bb7d0; }
.view-icon { color:#28a745; cursor:pointer; font-size:16px; }
.view-icon:hover { color:#218838; }

/* Button trạng thái */
.btn-pending, .btn-inprogress, .btn-done {
    padding:4px 12px;
    font-size:14px;
    border-radius:6px;
    font-weight:bold;
    border:none;
    cursor: default;
}
.btn-pending { background:#ff9800; color:#fff; }
.btn-inprogress { background:#0d6efd; color:#fff; }
.btn-done { background:#6c757d; color:#fff; }

/* Cột trạng thái rộng để vừa chữ */
table td.status-column, table th.status-column {
    width:150px;   /* đủ rộng để chữ không xuống dòng */
    text-align:center;
}

@media(max-width:768px){ 
    .filter-form { flex-direction:column; align-items:flex-start;} 
    th, td { font-size:14px; } 
}
</style>
</head>
<body>
<div class="container">
<h2>Danh Sách Lịch Xét Nghiệm</h2>

<form method="get" class="filter-form">
    <label for="ngaychon">Chọn ngày:</label>
    <input type="date" name="ngaychon" id="ngaychon" value="<?= htmlspecialchars($ngaychon) ?>">

    <label for="trangthai">Trạng thái:</label>
    <select name="trangthai" id="trangthai">
        <?php foreach($statusMap as $key=>$val): ?>
            <option value="<?= $key ?>" <?= $key == $trangthai ? 'selected':'' ?>><?= $val['text'] ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Lọc</button>
</form>

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
                        <span class="edit-icon">&#9998;</span>
                    </a>
                <?php endif; ?>
                &nbsp;
                <a href="?action=xemchitiet&id=<?= $row['malichxetnghiem'] ?>" title="Xem chi tiết">
                    <span class="view-icon">&#128065;</span>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr><td colspan="9" style="text-align:center;">Không có lịch xét nghiệm</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>
