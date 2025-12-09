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
        <div class="notification-icon" id="notificationIcon" style="position: relative; margin-right: 20px; cursor: pointer;">
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
        <div id="notificationPanel" class="notification-panel" style="
            display: none;
            position: absolute;
            top: 60px;
            right: 20px;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            z-index: 1000;
            overflow: hidden;
        ">
            <div style="padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; color: #333;">Thông báo</h3>
                <button id="markAllRead" style="
                    background: transparent;
                    border: none;
                    color: #2196F3;
                    cursor: pointer;
                    font-size: 14px;
                    padding: 4px 8px;
                ">Đánh dấu đã đọc</button>
            </div>
            <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                <div style="padding: 20px; text-align: center; color: #999;">
                    Đang tải thông báo...
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdown = document.querySelector(".dropdown-menu");
    const userInfo = document.querySelector(".user-info");
    const notificationIcon = document.getElementById("notificationIcon");
    const notificationPanel = document.getElementById("notificationPanel");
    const notificationList = document.getElementById("notificationList");
    const markAllReadBtn = document.getElementById("markAllRead");

    userInfo.addEventListener("click", function(e) {
        e.stopPropagation();
        dropdown.classList.toggle("show");
        // Close notification panel when opening user menu
        if (notificationPanel) {
            notificationPanel.style.display = "none";
        }
    });

    // Handle notification icon click
    if (notificationIcon && notificationPanel) {
        notificationIcon.addEventListener("click", function(e) {
            e.stopPropagation();
            // Toggle notification panel
            const isVisible = notificationPanel.style.display === "block";
            notificationPanel.style.display = isVisible ? "none" : "block";
            
            // Close user dropdown if open
            dropdown.classList.remove("show");
            
            // Load notifications if opening
            if (!isVisible) {
                loadNotifications();
            }
        });
    }

    // Handle mark all as read button
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener("click", function(e) {
            e.stopPropagation();
            markAllNotificationsAsRead();
        });
    }

    // Close panels when clicking outside
    document.addEventListener("click", function(e) {
        dropdown.classList.remove("show");
        if (notificationPanel && !notificationPanel.contains(e.target) && !notificationIcon.contains(e.target)) {
            notificationPanel.style.display = "none";
        }
    });

    // Function to load notifications
    function loadNotifications() {
        notificationList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Đang tải...</div>';
        
        fetch('/Ajax/thongbao.php?action=get_all')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    displayNotifications(data.data);
                } else {
                    notificationList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Không có thông báo mới</div>';
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = '<div style="padding: 20px; text-align: center; color: #f44336;">Lỗi khi tải thông báo</div>';
            });
    }

    // Function to display notifications
    function displayNotifications(notifications) {
        notificationList.innerHTML = '';
        
        notifications.forEach(notification => {
            const notifItem = document.createElement('div');
            notifItem.className = 'notification-item';
            notifItem.style.cssText = `
                padding: 16px;
                border-bottom: 1px solid #f0f0f0;
                cursor: pointer;
                transition: background 0.2s;
                background: ${notification.daxem == 0 ? '#f5f9ff' : 'white'};
            `;
            
            notifItem.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 4px;">
                    <h4 style="margin: 0; font-size: 15px; font-weight: 600; color: #333; flex: 1;">
                        ${notification.tieude}
                    </h4>
                    ${notification.daxem == 0 ? '<span style="width: 8px; height: 8px; background: #2196F3; border-radius: 50%; margin-left: 8px; flex-shrink: 0;"></span>' : ''}
                </div>
                <p style="margin: 8px 0; font-size: 14px; color: #666; line-height: 1.4;">
                    ${notification.noidung}
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: #999;">
                        ${formatDate(notification.ngaytao)}
                    </span>
                    ${notification.malichxetnghiem ? 
                        '<span style="font-size: 13px; color: #2196F3; font-weight: 500;">Xem chi tiết →</span>' 
                        : ''}
                </div>
            `;
            
            // Add click handler
            notifItem.addEventListener('click', () => {
                handleNotificationClick(notification);
            });
            
            // Add hover effect
            notifItem.addEventListener('mouseenter', () => {
                notifItem.style.background = '#f8f8f8';
            });
            notifItem.addEventListener('mouseleave', () => {
                notifItem.style.background = notification.daxem == 0 ? '#f5f9ff' : 'white';
            });
            
            notificationList.appendChild(notifItem);
        });
    }

    // Function to handle notification click
    function handleNotificationClick(notification) {
        // Mark as read
        if (notification.daxem == 0) {
            fetch(`/Ajax/thongbao.php?action=mark_read&mathongbao=${notification.mathongbao}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && window.notificationHandler) {
                        window.notificationHandler.updateNotificationBadge();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
        }
        
        // Navigate to detail if available
        if (notification.malichxetnghiem) {
            window.location.href = `?action=ketquaxetnghiem&id=${notification.malichxetnghiem}`;
        }
        
        // Close panel
        notificationPanel.style.display = "none";
    }

    // Function to mark all as read
    function markAllNotificationsAsRead() {
        fetch('/Ajax/thongbao.php?action=mark_all_read')
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
    }

    // Function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 1) return 'Vừa xong';
        if (minutes < 60) return `${minutes} phút trước`;
        if (hours < 24) return `${hours} giờ trước`;
        if (days < 7) return `${days} ngày trước`;
        
        return date.toLocaleDateString('vi-VN', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric' 
        });
    }
});

</script>