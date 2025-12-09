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
            <span class="notification-badge" style="
                position: absolute;
                top: -8px;
                right: -8px;
                background: #ff4444;
                color: white;
                border-radius: 50%;
                padding: 2px 6px;
                font-size: 11px;
                font-weight: bold;
                display: none;
            ">0</span>
        </div>
        
        <!-- Notification Dropdown Panel -->
        <div class="notification-dropdown" style="
            position: absolute;
            top: 60px;
            right: 200px;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: none;
            z-index: 1000;
            overflow: hidden;
        ">
            <div class="notification-header" style="
                padding: 15px 20px;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8f9fa;
            ">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #333;">Thông báo</h3>
                <button class="mark-all-read-btn" style="
                    background: none;
                    border: none;
                    color: #2196F3;
                    font-size: 13px;
                    cursor: pointer;
                    padding: 5px 10px;
                ">Đánh dấu tất cả đã đọc</button>
            </div>
            <div class="notification-list" style="
                max-height: 400px;
                overflow-y: auto;
            ">
                <div class="notification-loading" style="
                    padding: 40px;
                    text-align: center;
                    color: #999;
                ">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i>
                    <p style="margin-top: 10px;">Đang tải thông báo...</p>
                </div>
            </div>
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

<style>
.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.2s;
}
.notification-item:hover {
    background: #f0f0f0 !important;
}
.notification-item.unread {
    background: #f8f9fa;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdown = document.querySelector(".dropdown-menu");
    const userInfo = document.querySelector(".user-info");
    const notificationIcon = document.querySelector(".notification-icon");
    const notificationDropdown = document.querySelector(".notification-dropdown");
    const notificationList = document.querySelector(".notification-list");
    const markAllReadBtn = document.querySelector(".mark-all-read-btn");

    // Toggle user dropdown
    userInfo.addEventListener("click", function(e) {
        e.stopPropagation();
        dropdown.classList.toggle("show");
        // Close notification dropdown if open
        if (notificationDropdown.style.display === "block") {
            notificationDropdown.style.display = "none";
        }
    });

    // Toggle notification dropdown
    notificationIcon.addEventListener("click", function(e) {
        e.stopPropagation();
        const isVisible = notificationDropdown.style.display === "block";
        notificationDropdown.style.display = isVisible ? "none" : "block";
        
        // Close user dropdown if open
        dropdown.classList.remove("show");
        
        // Load notifications when opening
        if (!isVisible) {
            loadNotifications();
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener("click", function() {
        dropdown.classList.remove("show");
        notificationDropdown.style.display = "none";
    });

    // Prevent closing when clicking inside notification dropdown
    notificationDropdown.addEventListener("click", function(e) {
        e.stopPropagation();
    });

    // Mark all as read button
    markAllReadBtn.addEventListener("click", function() {
        fetch('Ajax/thongbao.php?action=mark_all_read')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    if (window.notificationHandler) {
                        window.notificationHandler.updateNotificationBadge();
                    }
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
    });

    // Helper function to create loading HTML
    function getLoadingHTML() {
        return `
            <div class="notification-loading" style="padding: 40px; text-align: center; color: #999;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i>
                <p style="margin-top: 10px;">Đang tải thông báo...</p>
            </div>
        `;
    }

    // Function to load notifications
    function loadNotifications() {
        notificationList.innerHTML = getLoadingHTML();

        fetch('Ajax/thongbao.php?action=get_all')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.length === 0) {
                        notificationList.innerHTML = `
                            <div style="padding: 40px; text-align: center; color: #999;">
                                <i class="fas fa-bell-slash" style="font-size: 36px; opacity: 0.5;"></i>
                                <p style="margin-top: 15px;">Không có thông báo nào</p>
                            </div>
                        `;
                    } else {
                        notificationList.innerHTML = data.data.map(notification => `
                            <div class="notification-item ${notification.daxem == 0 ? 'unread' : ''}" data-mathongbao="${notification.mathongbao}">
                                <div style="display: flex; gap: 12px;">
                                    <div style="
                                        width: 8px;
                                        height: 8px;
                                        border-radius: 50%;
                                        background: ${notification.daxem == 0 ? '#2196F3' : 'transparent'};
                                        margin-top: 6px;
                                        flex-shrink: 0;
                                    "></div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: ${notification.daxem == 0 ? '600' : '500'}; color: #333;">
                                            ${notification.tieude}
                                        </h4>
                                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #666; line-height: 1.4;">
                                            ${notification.noidung}
                                        </p>
                                        <span style="font-size: 11px; color: #999;">
                                            ${formatTime(notification.ngaytao)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        // Add click handlers to notification items
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.addEventListener('click', function() {
                                const mathongbao = this.dataset.mathongbao;
                                markNotificationAsRead(mathongbao);
                            });
                        });
                    }
                } else {
                    notificationList.innerHTML = `
                        <div style="padding: 40px; text-align: center; color: #999;">
                            <p>Lỗi khi tải thông báo</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = `
                    <div style="padding: 40px; text-align: center; color: #999;">
                        <p>Lỗi kết nối</p>
                    </div>
                `;
            });
    }

    // Mark single notification as read
    function markNotificationAsRead(mathongbao) {
        fetch(`Ajax/thongbao.php?action=mark_read&mathongbao=${mathongbao}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    if (window.notificationHandler) {
                        window.notificationHandler.updateNotificationBadge();
                    }
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
    }

    // Format time helper
    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Vừa xong';
        if (diffMins < 60) return `${diffMins} phút trước`;
        if (diffHours < 24) return `${diffHours} giờ trước`;
        if (diffDays < 7) return `${diffDays} ngày trước`;
        
        return date.toLocaleDateString('vi-VN');
    }
});

</script>