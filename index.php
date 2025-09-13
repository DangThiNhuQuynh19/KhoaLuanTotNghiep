<?php
ob_start();
session_start();

// Xác định action (page)
$page = isset($_GET["action"]) ? $_GET["action"] : 'trangchu';
$cate = isset($_GET["cate"]) ? $_GET["cate"] : null;

if (isset($_SESSION['dangnhap']) && ($_SESSION['dangnhap'] == 3 || $_SESSION['dangnhap'] == 2)) {
    // Bác sĩ
    if (file_exists("Views/bacsi/pages/$page/index.php")) {
        require("Views/bacsi/layout/header.php");
        include("Views/bacsi/pages/$page/index.php");
    } else {
        include("Views/bacsi/pages/404/index.php");
    }

} elseif (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 4) {
    // Nhân viên
    if (file_exists("Views/nhanvien/pages/$page/index.php")) {
        require("Views/nhanvien/layout/header.php");
        include("Views/nhanvien/pages/$page/index.php");
    } else {
        include("Views/nhanvien/pages/404/index.php");
    }

} elseif (isset($_SESSION['name']) && isset($_SESSION['email'])) {
    // Bệnh nhân đã đăng nhập
    if (file_exists("Views/benhnhan/pages/$page/index.php")) {
        require("Views/benhnhan/layout/header.php");
        include("Views/benhnhan/pages/$page/index.php");
    } else {
        include("Views/benhnhan/pages/404/index.php");
    }

} else {
    // Khách / bệnh nhân chưa đăng nhập
    if (file_exists("Views/benhnhan/pages/$page/index.php")) {
        require("Views/benhnhan/layout/header.php");
        include("Views/benhnhan/pages/$page/index.php");
    } else {
        include("Views/benhnhan/pages/404/index.php");
    }
}
?>
