<?php
// error_reporting(1);
ob_start();
session_start();

// Xác định action (page)
$page = isset($_GET["action"]) ? $_GET["action"] : 'trangchu';
$cate = isset($_GET["cate"]) ? $_GET["cate"] : null;

if (isset($_SESSION['dangnhap']) && ($_SESSION['dangnhap'] == 2)){
    // Bác sĩ
    if (file_exists("Views/bacsi/pages/$page/index.php")) {
        require("Views/bacsi/layout/header.php");
        include("Views/bacsi/pages/$page/index.php");
    } else {
        include("Views/bacsi/pages/404/index.php");
    }

}elseif (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 3) {
    // Chuyên gia
    if (file_exists("Views/chuyengia/pages/$page/index.php")) {
        require("Views/chuyengia/layout/header.php");
        include("Views/chuyengia/pages/$page/index.php");
    } else {
        include("Views/chuyengia/pages/404/index.php");
    }

} elseif (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 4) {
    // Nhân viên
    if (file_exists("Views/nhanvientieptan/pages/$page/index.php")) {
        require("Views/nhanvientieptan/layout/header.php");
        include("Views/nhanvientieptan/pages/$page/index.php");
    } else {
        include("Views/nhanvientieptan/pages/404/index.php");
    }

} elseif (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] == 5) {
    // Nhân viên
    if (file_exists("Views/nhanvienxetnghiem/pages/$page/index.php")) {
        require("Views/nhanvienxetnghiem/layout/header.php");
        include("Views/nhanvienxetnghiem/pages/$page/index.php");
    } else {
        include("Views/nhanvienxetnghiem/pages/404/index.php");
    }

}elseif (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] ==6) {
    // ADMIN
    if (file_exists("Views/admin/pages/$page/index.php")) {
        require("Views/admin/layout/header.php");
        require("Views/admin/layout/sidebar.php");
        include("Views/admin/pages/$page/index.php");
    } else {
        include("Views/admin/pages/404/index.php");
    }

}elseif (isset($_SESSION['dangnhap']) && $_SESSION['dangnhap'] ==7) {
    if (file_exists("Views/quanly/pages/$page/index.php")) {
        require("Views/quanly/layout/header.php");
        include("Views/quanly/pages/$page/index.php");
    } else {
        include("Views/quanly/pages/404/index.php");
    }
}
 elseif (isset($_SESSION['name']) && isset($_SESSION['email'])) {
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
