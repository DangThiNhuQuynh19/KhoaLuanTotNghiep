<?php
include_once('Controllers/cnhanvien.php');

$cnhanvien = new cnhanvien();
$nhanvien = null;
if (isset($_SESSION["dangnhap"]) && isset($_SESSION["user"])) {
    $nhanvien = $cnhanvien->getNhanVienByTenTK($_SESSION["user"]["tentk"]);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản lý Bệnh viện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #483a73;
            --primary-hover: #5a4b9b;
            --light-bg: #f7f8fa;
        }

        /* ===== HEADER ===== */
        .navbar {
            background: var(--primary-color);
            padding: 10px 20px;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== LOGO ===== */
        .navbar-brand img {
            height: 40px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
            background-color: #fff;
            padding: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .navbar-toggler {
            border: none;
            color: #fff;
            font-size: 1.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        /* NAV LINKS */
        .navbar-nav {
            gap: 10px;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 400;
            border-radius: 8px;
            padding: 8px 14px;
            transition: background-color 0.25s ease;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background-color: #fff;
            color: var(--primary-color) !important;
        }

        /* USER MENU */
        .avatar {
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            width: 38px;
            height: 38px;
            object-fit: cover;
        }

        .dropdown-menu {
            right: 0;
            left: auto;
            border: none;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
            border-radius: 10px;
            overflow: hidden;
            animation: fadeIn 0.25s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-5px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
        }

        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .navbar-collapse {
                background-color: var(--primary-hover);
                border-radius: 12px;
                padding: 12px;
                margin-top: 8px;
            }

            .navbar-nav .nav-link {
                display: block;
                text-align: center;
                margin: 6px 0;
            }

            .dropdown {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
          <img src="Assets/img/logo.png" alt="Logo"> Quản lý Bệnh viện
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav mx-auto">
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
          </ul>

          <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
              <img src="<?php echo 'Assets/img/'.($nhanvien["imgnv"] ?? 'default.png'); ?>" alt="Avatar" class="avatar rounded-circle me-2">
              <span><?php echo $nhanvien ? htmlspecialchars($nhanvien["hoten"]) : 'Quản lý'; ?></span>
            </a>
            <ul class="dropdown-menu shadow">
              <li><a class="dropdown-item" href="?action=hoso"><i class="bi bi-person-circle"></i> Hồ sơ cá nhân</a></li>
              <li><a class="dropdown-item" href="?action=dangxuat"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
