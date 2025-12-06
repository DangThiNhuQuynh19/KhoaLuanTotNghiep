<?php
include_once('Controllers/cnhanvien.php');

$cnhanvien = new cnhanvien();
$nhanvien = null;
if (isset($_SESSION["dangnhap"]) && isset($_SESSION["user"])) {
    $nhanvien = $cnhanvien->getNhanVienByTenTK($_SESSION["user"]["tentk"]);
}

$action = $_GET['action'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hanhphuc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding-top: 70px;
        }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999; /* để luôn nằm trên cùng */

            background-color: #3498db;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        
        .header-left {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 14px;
        }
        
        .header-right a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .container {
            display: flex;
            min-height: calc(100vh - 60px);
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            overflow-y: auto;
            height: 100%
        }
        
        .sidebar-title {
            padding: 15px 20px;
            font-size: 12px;
            font-weight: bold;
            color: #95a5a6;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            position: relative;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #bdc3c7;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            background-color: #34495e;
            color: white;
            padding-left: 30px;
        }
        
        .sidebar-menu a.active {
            background-color: #3498db;
            color: white;
        }
        
        .sidebar-menu li.has-submenu > a {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar-menu li.has-submenu > a::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 12px;
            transition: transform 0.3s;
        }
        
        .sidebar-menu li.has-submenu.active > a::after {
            transform: rotate(-180deg);
        }
        
        .submenu {
            display: none;
            background-color: #34495e;
            list-style: none;
        }
        
        .sidebar-menu li.has-submenu.active .submenu {
            display: block;
        }
        
        .submenu li a {
            padding: 10px 20px 10px 40px;
            font-size: 13px;
            color: #95a5a6;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .submenu li a:hover {
            background-color: #2c3e50;
            color: white;
            padding-left: 50px;
        }
        
        .submenu li a.active {
            background-color: #3498db;
            color: white;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: #f5f5f5;
        }

        .main-container {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 24px;
            margin-left: 240px;
            padding: 24px 20px;
            width: calc(100% - 230px);
        }
        
        .breadcrumb {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }
        
        .breadcrumb span {
            color: #7f8c8d;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-size: 24px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .page-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #229954;
        }
        
        .form-container {
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .form-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e74c3c;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .required {
            color: #e74c3c;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="tel"],
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-group select {
            cursor: pointer;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-icon {
            color: #3498db;
            cursor: help;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        .logo-circle {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden; /* cắt ảnh theo hình tròn */
            border: 2px solid #fff; /* đường viền cho ảnh nổi bật */
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* giữ tỉ lệ, zoom vừa khít */
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;       /* bo tròn avatar */
            object-fit: cover;        /* giữ tỉ lệ ảnh, không méo */
            border: 2px solid #fff3;  /* viền mờ nhẹ cho đẹp */
            margin-right: 8px;
        }


    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <div class="logo-circle">
                <img src="Assets/img/logo.png" alt="Logo">
            </div>
        </div>
        <div class="header-right">
            <span><i class="fas fa-phone"></i> Hỗ trợ 24/7: 1900 9477</span>
            <a href="mailto:web@pav.vn"><i class="fas fa-envelope"></i> hanhphuc@hospital.vn</a>
            <a href="#">
                <img src="<?='Assets/img/'.($nhanvien['imgnv']??'default.png')?>" class="avatar me-2">
                <span class="text-white"><?=htmlspecialchars($nhanvien['hoten'] ?? 'Quản lý')?></span>
            </a>
            <a href="?action=dangxuat"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>
    </div>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-title">Chức năng hệ thống</div>
            <ul class="sidebar-menu">
                <li><a href="?action=trangchu"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li><a href="?action=lichcanhan"><i class="fas fa-calendar"></i> Lịch cá nhân</a></li>
                <li><a href="?action=thongtin"> <i class="fas fa-person"></i>Thông tin cá nhân</a></li>
                <li><a href="?action=chuyengia"><i class="fas fa-people"></i> Danh sách chuyên gia</a></li>
                <li><a href="?action=bacsi"><i class="fas fas-people"></i> Danh sách bác sĩ</a></li>
                <li><a href="?action=lichhen"><i class="fas fa-calendar-event"></i> Lịch hẹn</a></li>
            </ul>
        </div>
        <script>
            document.querySelectorAll('.has-submenu > a').forEach(menu => {
                menu.addEventListener('click', function () {
                    const li = this.parentElement;

                    // Toggle class active
                    li.classList.toggle('active');
                });
            });
        </script>