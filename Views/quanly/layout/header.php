<?php
include_once('Controllers/cnhanvien.php');

$cnhanvien = new cnhanvien();
$nhanvien = null;
if (isset($_SESSION["dangnhap"]) && isset($_SESSION["user"])) {
    $nhanvien = $cnhanvien->getNhanVienByTenTK($_SESSION["user"]["tentk"]);
}

// Lấy action hiện tại để active menu
$action = isset($_GET['action']) ? $_GET['action'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bệnh viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5b2c83;
            --primary-hover: #6f38a0;
            --light-bg: #f7f8fa;
        }
        body {
            background-color: var(--light-bg);
            font-family: "Segoe UI", Arial, sans-serif;
        }
        .navbar {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
            box-shadow: 0 2px 10px rgba(0,0,0,0.12);
        }
        .navbar-brand {
            font-size: 1.25rem;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            padding: 10px 18px;
            margin: 0 3px;
            border-radius: 8px;
            transition: background-color 0.25s ease;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: #fff;
            color: var(--primary-color) !important;
        }
        .dropdown-menu {
            right: 0;
            left: auto;
            border: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        .dropdown-item:hover {
            background-color: var(--light-bg);
        }
        .avatar {
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-white" href="index.php">
      🏥 Quản lý Bệnh viện
    </a>

    <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <i class="bi bi-list" style="font-size: 1.5rem;"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent" style="margin-left: 150px;">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo $action == 'nhanvien' ? 'active' : ''; ?>" href="?action=nhanvien">
            <i class="bi bi-people"></i> Quản lý nhân sự
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $action == 'lichlamviec' ? 'active' : ''; ?>" href="?action=lichlamviec">
            <i class="bi bi-calendar-week"></i> Lịch làm việc
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $action == 'duyetyeucau' ? 'active' : ''; ?>" href="?action=duyetyeucau">
            <i class="bi bi-check2-circle"></i> Duyệt yêu cầu
          </a>
        </li>
      </ul>

      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
          <img src="<?php echo 'Assets/img/'.($nhanvien["imgnv"] ?? 'default.png'); ?>" alt="Avatar" class="avatar rounded-circle me-2" width="38" height="38">
          <span><?php echo $nhanvien ? htmlspecialchars($nhanvien["hoten"]) : 'Quản lý'; ?></span>
        </a>
        <ul class="dropdown-menu shadow">
          <li><a class="dropdown-item" href="?action=dangxuat"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
