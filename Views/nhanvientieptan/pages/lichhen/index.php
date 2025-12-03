<?php
include_once('Controllers/clichhen.php');

$c = new cLichHen();

$ngay = $_GET['ngay'] ?? null;
$loaikham = $_GET['loaikham'] ?? null;
$hinhthuclamviec = $_GET['hinhthuclamviec'] ?? null;
$tenbenhnhan = $_GET['tenbenhnhan'] ?? null;

$result = $c->getAllLichHen($ngay, $loaikham, $hinhthuclamviec, $tenbenhnhan);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách lịch hẹn</title>
<style>
body {

    font-size: 13px; /* chữ nhỏ lại */
}


/* ===== FILTER FORM ===== */
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
    align-items: center; /* nút và input cùng hàng */
}
.filter-form .form-group {
    display: flex;
    flex-direction: column;
}
.filter-form label {
    font-weight: 500;
    margin-bottom: 4px;
    color: #3498db;
    font-size: 13px;
}
.filter-form input[type="text"],
.filter-form input[type="date"],
.filter-form select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 13px;
    outline: none;
}
.filter-form input:focus,
.filter-form select:focus { border-color: #3498db; }

/* Nút ở cùng hàng với input */
.filter-form .form-group.buttons {
    display: flex;
    flex-direction: row;
    gap: 8px;
    align-items: center;
    margin-top: 22px; /* căn nút xuống đúng hàng với input */
}


/* BUTTONS */
.btn {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: 0.2s;
    font-size: 13px;
}
.btn-primary { background: #3498db; color: #fff; }
.btn-primary:hover { background: #217dbb; }
.btn-secondary { background: #95a5a6; color: #fff; }
.btn-secondary:hover { background: #7f8c8d; }

/* TABLE */
.table-container { overflow-x: auto; }
.table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
    font-size: 13px;
}
.table th, .table td {
    padding: 8px 10px;
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #ddd;
}
.table th { background: #3498db; color: #fff; font-weight: 600; }
.table tr:hover { background: #f0f8ff; }

/* BADGES */
.badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    color: #fff;
}
.bg-warning { background: #f0ad4e; }
.bg-success { background: #5cb85c; }
.bg-danger { background: #d9534f; }
.bg-info { background: #3498db; }
.bg-secondary { background: #95a5a6; }
.bg-dark { background: #7f8c8d; }

/* SMALL ICON BUTTONS */
.action-btn {
    border: none;
    background: none;
    cursor: pointer;
    margin: 0 2px;
    font-size: 16px;
    transition: 0.2s;
}
.action-btn.edit { color: #3498db; }
.action-btn.edit:hover { color: #217dbb; }
.action-btn.delete { color: #d9534f; }
.action-btn.delete:hover { color: #c9302c; }

/* BACK BUTTON */
.back-btn {
    padding: 8px 16px;
    border-radius: 6px;
    background: #3498db;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    display: inline-block;
    margin-top: 15px;
    font-size: 13px;
    transition: 0.2s;
}
.back-btn:hover { background: #217dbb; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .filter-form { flex-direction: column; }
    .table th, .table td { font-size: 12px; padding: 6px 8px; }
}
</style>
</head>
<body>
<div class="main-container">
    <h3 style="color: #3498db; margin-bottom: 10px;">Danh sách lịch hẹn</h3>
    <form method="get" action="index.php" class="filter-form">
        <input type="hidden" name="action" value="lichhen">
        <div class="form-group">
            <label>Chọn ngày</label>
            <input type="date" name="ngay" value="<?= htmlspecialchars($ngay ?? date('Y-m-d')) ?>">
        </div>
        <div class="form-group">
            <label>Người khám</label>
            <select name="loaikham">
                <option value="">-- Tất cả --</option>
                <option value="bacsi" <?= ($loaikham==='bacsi')?'selected':'' ?>>Bác sĩ</option>
                <option value="chuyengia" <?= ($loaikham==='chuyengia')?'selected':'' ?>>Chuyên gia</option>
            </select>
        </div>
        <div class="form-group">
            <label>Hình thức</label>
            <select name="hinhthuclamviec">
                <option value="">-- Tất cả --</option>
                <option value="online" <?= ($hinhthuclamviec==='online')?'selected':'' ?>>Online</option>
                <option value="offline" <?= ($hinhthuclamviec==='offline')?'selected':'' ?>>Offline</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tên bệnh nhân</label>
            <input type="text" name="tenbenhnhan" value="<?= htmlspecialchars($tenbenhnhan ?? '') ?>">
        </div>
        <div class="form-group buttons">
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="index.php?action=lichhen" class="btn btn-secondary">Bỏ lọc</a>
        </div>
    </form>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ngày hẹn</th>
                    <th>Giờ hẹn</th>
                    <th>Bệnh nhân</th>
                    <th>Người khám</th>
                    <th>Hình thức</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if(is_array($result) && count($result)>0): ?>
                <?php foreach($result as $row): ?>
                <tr>
                    <td><?= $row['maphieukhambenh'] ?></td>
                    <td><?= date("d/m/Y", strtotime($row['ngaykham'])) ?></td>
                    <td><?= $row['giobatdau'] ?></td>
                    <td><?= htmlspecialchars($row['ten_benhnhan']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['ten_nguoi_kham']) ?>
                        <?php if($row['loaikham']==='bacsi'): ?><span class="badge bg-info">Bác sĩ</span>
                        <?php elseif($row['loaikham']==='chuyengia'): ?><span class="badge bg-secondary">Chuyên gia</span>
                        <?php else: ?><span class="badge bg-dark">Khác</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if($row['hinhthuclamviec']==='online'): ?><span class="badge bg-success">Online</span>
                        <?php elseif($row['hinhthuclamviec']==='offline'): ?><span class="badge bg-warning">Offline</span>
                        <?php else: ?><span class="badge bg-secondary">Khác</span><?php endif; ?>
                    </td>
                    <td>
                        <?php
                        if($row['tentrangthai']=='Chưa khám') echo '<span class="badge bg-warning">Chưa khám</span>';
                        elseif($row['tentrangthai']=='Đã khám') echo '<span class="badge bg-success">Đã khám</span>';
                        elseif($row['tentrangthai']=='Đã hủy') echo '<span class="badge bg-danger">Đã hủy</span>';
                        else echo '<span class="badge bg-secondary">'.htmlspecialchars($row['tentrangthai']).'</span>';
                        ?>
                    </td>
                    <td>
                        <?php if($row['tentrangthai']!='Đã khám'): ?>
                        <button class="action-btn edit" title="Sửa" onclick="location.href='index.php?action=sualichhen&id=<?= $row['maphieukhambenh'] ?>'">&#9998;</button>
                        <button class="action-btn delete" title="Hủy" onclick="if(confirm('Bạn có chắc muốn hủy lịch hẹn này?')) location.href='huylichhen.php?id=<?= $row['maphieukhambenh'] ?>'">&#10060;</button>
                        <?php else: ?>—
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Chưa có lịch hẹn</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="index.php" class="back-btn">&#x2190; Quay về</a>
</div>
</body>
</html>
