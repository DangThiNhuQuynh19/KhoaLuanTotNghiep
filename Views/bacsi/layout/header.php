<?php
    include_once('Controllers/cbacsi.php');
    $cbacsi= new cbacsi();
    if(isset($_SESSION["dangnhap"]) && isset($_SESSION["user"])){
        $bacsi = $cbacsi->getBacSiByTenTK($_SESSION["user"]["tentk"]);
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HanhPhuc Hospital</title>
    <link rel="stylesheet" href="Views/bacsi/assets/css/css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>               
<body>
<header class="main-header">
    <div class="logo">
        <a href="?action=trangchu">
            <img src="Assets/img/logo.png" alt="Hanh Phuc Hospital Logo" style="width:130px;">
        </a>
    </div>

    <nav class="main-nav">
        <ul>
            <li><a href="?action=trangchu"><i class="fas fa-home"></i> Trang chủ</a></li>
            <li><a href="?action=benhnhan"><i class="fas fa-user-injured"></i> Bệnh nhân</a></li>
            <li><a href="?action=lichhentructuyen"><i class="fas fa-laptop"></i> Lịch hẹn trực tuyến</a></li>
            <li><a href="?action=lichhentructiep"><i class="fas fa-clipboard-list"></i> Lịch hẹn trực tiếp</a></li>
            <li><a href="?action=datlich"><i class="fas fa-calendar-check"></i> Đặt lịch khám </a></li>
        </ul>
    </nav>

    <div class="user-menu">
        <div class="user-info">
            <span><?php echo $bacsi["hoten"] ?? 'Bác sĩ'; ?></span>
            <img src="Assets/img/<?php echo $bacsi["imgbs"]; ?>" class="user-avatar">
        </div>

        <div class="dropdown-menu">
            <a href="?action=hoso"><i class="fas fa-user"></i> Hồ sơ</a>
            <a href="?action=xemlichlamviec"><i class="fas fa-calendar"></i> Xem lịch làm việc</a>
            <a href="?action=dangxuat"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdown = document.querySelector(".dropdown-menu");
    const userInfo = document.querySelector(".user-info");

    userInfo.addEventListener("click", function(e) {
        e.stopPropagation();
        dropdown.classList.toggle("show");
    });

    document.addEventListener("click", function() {
        dropdown.classList.remove("show");
    });
});

</script>
