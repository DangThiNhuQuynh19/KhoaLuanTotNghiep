<?php
session_start();
// Giả sử đã có session đăng nhập nhân viên
if (!isset($_SESSION['vaitro']) || $_SESSION['vaitro'] != 'nhanvien') {
    header("Location: dangnhap.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang nhân viên - Bệnh viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Segoe UI", Arial, sans-serif;
        }
        .navbar {
            background-color: #4c1a7b;
        }
        .navbar-brand, .nav-link, .navbar-text {
            color: #fff !important;
        }
        .navbar .dropdown-menu {
            right: 0;
            left: auto;
        }
        .sidebar {
            background-color: #4c1a7b;
            min-height: 100vh;
            padding-top: 20px;
            position: sticky;
            top: 0;
        }
        .sidebar a {
            color: #fff;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            margin: 6px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 15px;
            transition: 0.3s;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #fff;
            color: #4c1a7b;
            font-weight: 600;
        }
        .content {
            padding: 25px;
        }
        .card {
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
        }
        .card-header {
            background-color: #4c1a7b;
            color: #fff;
            border-radius: 14px 14px 0 0;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">🏥 Bệnh viện</a>
    <div class="ms-auto dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
        <img src="https://i.pravatar.cc/40?img=12" alt="avatar" class="rounded-circle me-2">
        <span>Nhân viên</span>
      </a>
      <ul class="dropdown-menu shadow">
        <li><a class="dropdown-item" href="dangxuat.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
      </ul>
    </div>
  </div>
</nav>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
