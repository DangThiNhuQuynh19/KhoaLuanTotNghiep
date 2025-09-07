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
            <td><?= $row['id'] ?></td>
            <td><?= date("d/m/Y", strtotime($row['ngayhen'])) ?></td>
            <td><?= $row['giohen'] ?></td>
            <td><?= $row['tenbenhnhan'] ?></td>
            <td><?= $row['tenbacsi'] ?></td>
            <td>
              <?php
                switch ($row['trangthai']) {
                    case 0: echo '<span class="badge bg-warning">Chờ xác nhận</span>'; break;
                    case 1: echo '<span class="badge bg-success">Đã xác nhận</span>'; break;
                    case 2: echo '<span class="badge bg-danger">Đã hủy</span>'; break;
                }
              ?>
            </td>
            <td>
              <a href="sualichhen.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil-square"></i> Sửa
              </a>
              <a href="xoalichhen.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch hẹn này?')">
                <i class="bi bi-trash"></i> Xóa
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