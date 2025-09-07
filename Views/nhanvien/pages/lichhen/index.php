<?php
include_once(__DIR__ . '../../../../../Controllers/clichkham.php');
$c = new cLichKham();
$result = $c->getlichhennhanvien();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách lịch hẹn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h3 class="mb-3"><i class="bi bi-calendar-event"></i> Danh sách lịch hẹn</h3>
  <table class="table table-bordered table-hover">
    <thead class="table-primary">
      <tr>
        <th>#</th>
        <th>Ngày hẹn</th>
        <th>Giờ hẹn</th>
        <th>Bệnh nhân</th>
        <th>Bác sĩ</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['maphieukhambenh'] ?></td>
            <td><?= date("d/m/Y", strtotime($row['ngaykham'])) ?></td>
            <td><?= $row['giobatdau'] ?></td>
            <td><?= $row['ten_benhnhan'] ?></td>
            <td><?= $row['ten_bacsi'] ?></td>
            <td>
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

            <td>
              <a href="?action=sualichhen&id=<?= $row['maphieukhambenh'] ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil-square"></i> Sửa
              </a>
              <a href="huylichhen.php?id=<?= $row['maphieukhambenh'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch hẹn này?')">
                <i class="bi bi-trash"></i> Hủy
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center">Chưa có lịch hẹn</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>