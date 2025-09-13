<?php
include_once(__DIR__ . '/../../../../Controllers/cLichHen.php');



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
<title>Quản lý lịch hẹn</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .btn-purple { background-color: #6f42c1; color: #fff; border: none; }
    .btn-purple:hover { background-color: #5a32a3; color: #fff; }
</style>
</head>
<body>
<div class="container py-4">

<h1 style="text-align:center; color:#3c1561; position: relative;">
    <a href="index.php" style="position:absolute; left:20px; top:0; text-decoration:none; color:#3c1561; display:flex; align-items:center; font-size:16px;">
        <i class="bi bi-house-door-fill me-1"></i> Trang chủ
    </a>
    <h3 class="mb-3" style="text-align:center;"><i class="bi bi-calendar-event"></i> Danh sách lịch hẹn</h3>
</h1>

<form method="get" action="index.php" class="row g-3 mb-4">
    <input type="hidden" name="action" value="lichhen">

    <div class="col-md-3">
        <label class="form-label">Chọn ngày</label>
        <input type="date" name="ngay" class="form-control" value="<?= htmlspecialchars($ngay ?? date('Y-m-d')) ?>">
    </div>

    <div class="col-md-3">
        <label class="form-label">Người khám</label>
        <select name="loaikham" class="form-select">
            <option value="">-- Tất cả --</option>
            <option value="bacsi" <?= ($loaikham === 'bacsi') ? 'selected' : '' ?>>Bác sĩ</option>
            <option value="chuyengia" <?= ($loaikham === 'chuyengia') ? 'selected' : '' ?>>Chuyên gia</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Hình thức</label>
        <select name="hinhthuclamviec" class="form-select">
            <option value="">-- Tất cả --</option>
            <option value="online" <?= ($hinhthuclamviec === 'online') ? 'selected' : '' ?>>Online</option>
            <option value="offline" <?= ($hinhthuclamviec === 'offline') ? 'selected' : '' ?>>Offline</option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Tên bệnh nhân</label>
        <input type="text" name="tenbenhnhan" class="form-control" value="<?= htmlspecialchars($tenbenhnhan ?? '') ?>">
    </div>

    <div class="col-md-12 d-flex justify-content-end mt-2">
    <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Lọc</button>
    <a href="index.php?action=lichhen" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Bỏ lọc</a>
</div>

</form>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
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
            <?php if (is_array($result) && count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td class="text-center"><?= $row['maphieukhambenh'] ?></td>
                        <td><?= date("d/m/Y", strtotime($row['ngaykham'])) ?></td>
                        <td><?= $row['giobatdau'] ?></td>
                        <td><?= $row['ten_benhnhan'] ?></td>
                        <td>
                            <?= $row['ten_nguoi_kham'] ?>
                            <?php if ($row['loaikham'] === 'bacsi'): ?>
                                <span class="badge bg-info">Bác sĩ</span>
                            <?php elseif ($row['loaikham'] === 'chuyengia'): ?>
                                <span class="badge bg-secondary">Chuyên gia</span>
                            <?php else: ?>
                                <span class="badge bg-dark">Khác</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                          <?php if ($row['hinhthuclamviec'] === 'online'): ?>
                              <span class="badge bg-success">Online</span>
                          <?php elseif ($row['hinhthuclamviec'] === 'offline'): ?>
                              <span class="badge bg-warning">Offline</span>
                          <?php else: ?>
                              <span class="badge bg-secondary">Khác</span>
                          <?php endif; ?>
                      </td>

                        <td class="text-center">
                            <?php
                                if ($row['tentrangthai'] == 'Chưa khám') {
                                    echo '<span class="badge bg-warning">Chưa khám</span>';
                                } elseif ($row['tentrangthai'] == 'Đã khám') {
                                    echo '<span class="badge bg-success">Đã khám</span>';
                                } elseif ($row['tentrangthai'] == 'Đã hủy') {
                                    echo '<span class="badge bg-danger">Đã hủy</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">'.$row['tentrangthai'].'</span>';
                                }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['tentrangthai'] != 'Đã khám'): ?>
                                <a href="index.php?action=sualichhen&id=<?= $row['maphieukhambenh'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i> Sửa</a>
                                <a href="huylichhen.php?id=<?= $row['maphieukhambenh'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?')"><i class="bi bi-trash"></i> Hủy</a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center">Chưa có lịch hẹn</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4">
    <a href="index.php" class="btn btn-purple"><i class="bi bi-arrow-left"></i> Quay về</a>
</div>

</div>
</body>
</html>
