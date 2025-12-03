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
<title>Hệ thống Quản lý Bệnh viện</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #483a73;
        --hover-color: #5d4c9a;
        --light-gray: #f4f5f7;
    }

    body {
        overflow-x: hidden !important; /* ✅ tránh scroll ngang */
        background: #fafafa;
    }

    /* ✅ HEADER ĐỨNG YÊN */
    .navbar {
        position: sticky !important;
        top: 0;
        width: 100%;
        z-index: 9999;
        background: var(--primary-color);
        padding: 10px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* LOGO */
    .navbar-brand img {
        height: 42px;
        border-radius: 6px;
        background: #fff;
        padding: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .nav-link {
        color: #fff !important;
        padding: 8px 14px;
        border-radius: 8px;
        transition: 0.25s;
    }

    .nav-link:hover, .nav-link.active {
        background: #fff;
        color: var(--primary-color) !important;
    }
/* Giữ header không thay đổi chiều cao */
.navbar {
    display: flex;
    align-items: center;
    min-height: 70px;           /* ✅ chiều cao cố định */
    height: 70px;
}

/* Giữ menu khi sidebar mở không đẩy item */
.navbar-collapse {
    align-items: center !important;
}

/* Khi collapse mở (.show) -> KHÔNG được tự thêm padding/margin */
.navbar-collapse.show {
    display: flex !important;
    align-items: center !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Ngăn khoảng cách bị thay đổi khi menu mobile mở */
@media (max-width: 992px) {

    .navbar {
        height: auto; /* header cao theo nội dung nhưng các phần tử không nhảy */
    }

    .navbar-collapse {
        background: var(--primary-color);
        border-radius: 12px;
        padding: 15px !important;
        margin-top: 10px;
    }

    /* Fix đẩy item */
    .navbar-nav {
        width: 100%;
        gap: 6px;
    }

    .navbar-nav .nav-link {
        text-align: center;
        width: 100%;
        padding: 10px 0;
    }
}

    .user-dropdown {
    position: relative;
    cursor: pointer;
}

/* Avatar */
.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

/* Menu nhỏ gọn */
.dropdown-menu-custom {
    position: absolute;
    top: 110%;
    right: 0;

    width: 170px;                /* ✅ nhỏ gọn hơn */
    background: #fff;
    padding: 6px 0;
    border-radius: 10px;

    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    border: 1px solid #ececec;

    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    transition: 0.2s ease;

    z-index: 9999;
}

/* Hiện menu */
.user-dropdown.show .dropdown-menu-custom {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Mũi tên nhỏ */
.dropdown-menu-custom::before {
    content: "";
    position: absolute;
    top: -6px;
    right: 18px;
    width: 10px;
    height: 10px;
    background: #fff;
    border-left: 1px solid #ececec;
    border-top: 1px solid #ececec;
    transform: rotate(45deg);
}

/* Item nhỏ gọn */
.dropdown-item-custom {
    display: flex;
    align-items: center;
    gap: 8px;

    padding: 8px 14px;
    font-size: 0.9rem;
    font-weight: 500;

    color: #444;
    white-space: nowrap;

    border-radius: 6px;
    text-decoration: none !important;  /* ✅ bỏ gạch chân */
    transition: 0.15s;
}

/* Hover sạch sẽ */
.dropdown-item-custom:hover {
    background: #f2f3f5;
    color: var(--primary-color);
}

/* Icon nhỏ gọn */
.dropdown-item-custom i {
    font-size: 1rem;
    opacity: 0.8;
}

.dropdown-divider {
    margin: 6px 12px;
    border-top: 1px solid #e5e5e5;
}
</style>
</head>

<body>

<!-- ✅ HEADER -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="Assets/img/logo.png">
            <span class="ms-2 fw-semibold text-white">Quản lý Bệnh viện</span>
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav mx-auto">
                <li><a class="nav-link <?=($action=='nhanvien'?'active':'')?>" href="?action=nhanvien"><i class="bi bi-people"></i> Nhân sự</a></li>
                <li><a class="nav-link <?=($action=='lichlamviec'?'active':'')?>" href="?action=lichlamviec"><i class="bi bi-calendar-week"></i> Lịch làm việc</a></li>
            </ul>

            <div class="user-dropdown ms-3">
              <div class="d-flex align-items-center user-trigger">
                  <img src="<?='Assets/img/'.($nhanvien['imgnv']??'default.png')?>" class="avatar me-2">
                  <span class="text-white"><?=htmlspecialchars($nhanvien['hoten'] ?? 'Quản lý')?></span>
              </div>

              <div class="dropdown-menu-custom">

                  <a class="dropdown-item-custom" href="?action=hoso">
                      <i class="bi bi-person-circle"></i> Hồ sơ cá nhân
                  </a>

                  <a class="dropdown-item-custom" href="?action=lichlamviec">
                      <i class="bi bi-calendar3"></i> Lịch làm việc
                  </a>

                  <hr class="dropdown-divider">

                  <a class="dropdown-item-custom" href="?action=dangxuat">
                      <i class="bi bi-box-arrow-right"></i> Đăng xuất
                  </a>

              </div>
          </div>


        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".user-dropdown");
    const trigger = document.querySelector(".user-trigger");

    trigger.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.classList.toggle("show");
    });

    document.addEventListener("click", () => {
        menu.classList.remove("show");
    });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
