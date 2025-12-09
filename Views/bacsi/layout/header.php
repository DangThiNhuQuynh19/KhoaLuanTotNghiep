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
    <link rel="icon" type="image/x-icon" href="app/public/favicon.ico">
    <link rel="stylesheet" href="Views/bacsi/assets/css/css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>               
<body data-username="<?php echo $_SESSION['user']['tentk'] ?? ''; ?>">
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
        <!-- Notification Bell Icon -->
        <div class="notification-icon" style="position: relative; margin-right: 20px; cursor: pointer;">
            <i class="fas fa-bell" style="font-size: 20px; color: #333;"></i>
            <span class="notification-badge" style="position: absolute; top: -8px; right: -8px; background: #ff4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; display: none;">0</span>
        </div>
        
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

<!-- Include notification handler script -->
<script src="Assets/js/notification-handler.js"></script>

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
    
    // Xử lý click vào notification icon
    const notificationIcon = document.querySelector(".notification-icon");
    if (notificationIcon) {
        notificationIcon.addEventListener("click", function(e) {
            e.stopPropagation();
            showNotificationDropdown();
        });
    }
});

// Hiển thị dropdown thông báo
function showNotificationDropdown() {
    // Kiểm tra xem đã có dropdown chưa
    let existingDropdown = document.querySelector('.notification-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
        return; // Toggle off nếu đã mở
    }
    
    // Tạo dropdown
    const dropdown = document.createElement('div');
    dropdown.className = 'notification-dropdown';
    dropdown.style.cssText = `
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 10px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
    `;
    
    dropdown.innerHTML = '<div style="padding: 12px; text-align: center;">Đang tải...</div>';
    
    document.querySelector('.notification-icon').style.position = 'relative';
    document.querySelector('.notification-icon').appendChild(dropdown);
    
    // Tải danh sách thông báo
    fetch('Ajax/thongbao.php?action=get_all&daxem=0')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.length === 0) {
                    dropdown.innerHTML = `
                        <div style="padding: 20px; text-align: center; color: #999;">
                            <i class="fas fa-bell-slash" style="font-size: 32px; margin-bottom: 8px;"></i>
                            <p style="margin: 0;">Không có thông báo mới</p>
                        </div>
                    `;
                } else {
                    let html = '<div style="padding: 12px; border-bottom: 1px solid #eee; font-weight: 600;">Thông báo</div>';
                    data.data.forEach(notification => {
                        html += `
                            <div class="notification-item" data-mathongbao="${notification.mathongbao}" data-malichxetnghiem="${notification.malichxetnghiem}" style="
                                padding: 12px;
                                border-bottom: 1px solid #eee;
                                cursor: pointer;
                                background: ${notification.daxem == 0 ? '#f0f8ff' : 'white'};
                            " onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='${notification.daxem == 0 ? '#f0f8ff' : 'white'}'">
                                <div style="font-weight: 600; margin-bottom: 4px;">${notification.tieude}</div>
                                <div style="font-size: 13px; color: #666; margin-bottom: 4px;">${notification.noidung}</div>
                                <div style="font-size: 11px; color: #999;">${notification.ngaytao}</div>
                            </div>
                        `;
                    });
                    dropdown.innerHTML = html;
                    
                    // Thêm event listener cho mỗi notification item
                    dropdown.querySelectorAll('.notification-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const mathongbao = this.getAttribute('data-mathongbao');
                            const malichxetnghiem = this.getAttribute('data-malichxetnghiem');
                            
                            // Đánh dấu đã đọc
                            if (mathongbao) {
                                fetch(`Ajax/thongbao.php?action=mark_read&mathongbao=${mathongbao}`)
                                    .then(() => {
                                        if (window.notificationHandler) {
                                            window.notificationHandler.updateNotificationBadge();
                                        }
                                    });
                            }
                            
                            // Chuyển đến trang kết quả xét nghiệm
                            if (malichxetnghiem) {
                                window.location.href = `?action=ketquaxetnghiem&id=${malichxetnghiem}`;
                            }
                        });
                    });
                }
            } else {
                dropdown.innerHTML = `<div style="padding: 20px; text-align: center; color: red;">Lỗi: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            dropdown.innerHTML = '<div style="padding: 20px; text-align: center; color: red;">Lỗi tải thông báo</div>';
        });
    
    // Đóng dropdown khi click ra ngoài
    setTimeout(() => {
        document.addEventListener('click', function closeDropdown(e) {
            if (!dropdown.contains(e.target) && !e.target.closest('.notification-icon')) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        });
    }, 100);
}

</script>
