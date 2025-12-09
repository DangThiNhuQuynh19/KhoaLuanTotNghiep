/**
 * Notification Handler for Test Result Push Notifications
 * Kết nối WebSocket và xử lý thông báo real-time
 */

<script> 
    class NotificationHandler {
        constructor(username) {
            this.username = username;
            this.ws = null;
            this.reconnectInterval = 5000;
            this.reconnectAttempts = 0;
            this.maxReconnectAttempts = 10;
            this.notificationCallback = null;
            
            this.connect();
        }
        
        connect() {
            try {
                // Kết nối đến WebSocket server
                this.ws = new WebSocket('ws://localhost:8080');
                
                this.ws.onopen = () => {
                    console.log('✅ WebSocket connected');
                    this.reconnectAttempts = 0;
                    
                    // Đăng ký user với server
                    this.ws.send(JSON.stringify({
                        command: 'register',
                        username: this.username
                    }));
                    
                    // Lấy thông báo chưa đọc khi kết nối
                    this.loadUnreadNotifications();
                };
                
                this.ws.onmessage = (event) => {
                    try {
                        const data = JSON.parse(event.data);
                        console.log('📨 Received:', data);
                        
                        // Xử lý thông báo
                        if (data.command === 'notification') {
                            this.handleNotification(data);
                        }
                    } catch (error) {
                        console.error('Error parsing message:', error);
                    }
                };
                
                this.ws.onerror = (error) => {
                    console.error('WebSocket error:', error);
                };
                
                this.ws.onclose = () => {
                    console.log('❌ WebSocket disconnected');
                    this.attemptReconnect();
                };
                
            } catch (error) {
                console.error('Failed to connect:', error);
                this.attemptReconnect();
            }
        }
        
        attemptReconnect() {
            if (this.reconnectAttempts < this.maxReconnectAttempts) {
                this.reconnectAttempts++;
                console.log(`🔄 Reconnecting... Attempt ${this.reconnectAttempts}`);
                setTimeout(() => this.connect(), this.reconnectInterval);
            } else {
                console.error('Max reconnect attempts reached');
            }
        }
        
        handleNotification(data) {
            // Hiển thị thông báo trình duyệt
            this.showBrowserNotification(data.title, data.content, data);
            
            // Hiển thị thông báo trên trang
            this.showPageNotification(data);
            
            // Cập nhật badge số lượng thông báo
            this.updateNotificationBadge();
            
            // Gọi callback nếu có
            if (this.notificationCallback) {
                this.notificationCallback(data);
            }
            
            // Phát âm thanh thông báo
            this.playNotificationSound();
        }
        
        showBrowserNotification(title, body, data) {
            // Kiểm tra quyền thông báo trình duyệt
            if (!("Notification" in window)) {
                console.log("Browser doesn't support notifications");
                return;
            }
            
            if (Notification.permission === "granted") {
                const notification = new Notification(title, {
                    body: body,
                    icon: '/Assets/images/logo.png',
                    badge: '/Assets/images/badge.png',
                    tag: 'test-result-notification',
                    requireInteraction: true
                });
                
                notification.onclick = () => {
                    window.focus();
                    // Chuyển đến trang chi tiết kết quả xét nghiệm
                    if (data.malichxetnghiem) {
                        window.location.href = `/Views/bacsi/pages/ketquaxetnghiem/index.php?id=${data.malichxetnghiem}`;
                    }
                    notification.close();
                };
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        this.showBrowserNotification(title, body, data);
                    }
                });
            }
        }
        
        showPageNotification(data) {
            // Tạo phần tử thông báo trên trang
            const notificationHTML = `
                <div class="notification-toast" data-mathongbao="${data.mathongbao}" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    max-width: 350px;
                    z-index: 9999;
                    animation: slideInRight 0.3s ease-out;
                ">
                    <div style="display: flex; align-items: start; gap: 12px;">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: #4CAF50;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                        ">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #333;">
                                ${data.title}
                            </h4>
                            <p style="margin: 0; font-size: 14px; color: #666; line-height: 1.4;">
                                ${data.content}
                            </p>
                            <div style="margin-top: 12px; display: flex; gap: 8px;">
                                <button class="view-result-btn" data-malichxetnghiem="${data.malichxetnghiem}" style="
                                    background: #2196F3;
                                    color: white;
                                    border: none;
                                    padding: 6px 12px;
                                    border-radius: 4px;
                                    cursor: pointer;
                                    font-size: 13px;
                                ">Xem kết quả</button>
                                <button class="close-notification-btn" style="
                                    background: #f0f0f0;
                                    color: #666;
                                    border: none;
                                    padding: 6px 12px;
                                    border-radius: 4px;
                                    cursor: pointer;
                                    font-size: 13px;
                                ">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Thêm CSS animation nếu chưa có
            if (!document.querySelector('#notification-animations')) {
                const style = document.createElement('style');
                style.id = 'notification-animations';
                style.textContent = `
                    @keyframes slideInRight {
                        from {
                            transform: translateX(400px);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    @keyframes slideOutRight {
                        from {
                            transform: translateX(0);
                            opacity: 1;
                        }
                        to {
                            transform: translateX(400px);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
            
            // Thêm thông báo vào trang
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = notificationHTML;
            const notificationElement = tempDiv.firstElementChild;
            document.body.appendChild(notificationElement);
            
            // Xử lý nút "Xem kết quả"
            notificationElement.querySelector('.view-result-btn').addEventListener('click', () => {
                const malichxetnghiem = data.malichxetnghiem;
                window.location.href = `/Views/bacsi/pages/ketquaxetnghiem/index.php?id=${malichxetnghiem}`;
                this.markAsRead(data.mathongbao);
            });
            
            // Xử lý nút "Đóng"
            notificationElement.querySelector('.close-notification-btn').addEventListener('click', () => {
                this.closeNotification(notificationElement, data.mathongbao);
            });
            
            // Tự động đóng sau 10 giây
            setTimeout(() => {
                if (document.body.contains(notificationElement)) {
                    this.closeNotification(notificationElement, data.mathongbao);
                }
            }, 10000);
        }
        
        closeNotification(element, mathongbao) {
            element.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                element.remove();
            }, 300);
            this.markAsRead(mathongbao);
        }
        
        markAsRead(mathongbao) {
            if (!mathongbao) return;
            
            fetch(`/Ajax/thongbao.php?action=mark_read&mathongbao=${mathongbao}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.updateNotificationBadge();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
        }
        
        loadUnreadNotifications() {
            fetch('/Ajax/thongbao.php?action=count_unread')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.updateNotificationBadge(data.count);
                    }
                })
                .catch(error => console.error('Error loading unread count:', error));
        }
        
        updateNotificationBadge(count = null) {
            if (count === null) {
                // Lấy số lượng từ server
                this.loadUnreadNotifications();
                return;
            }
            
            // Cập nhật badge
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
        
        playNotificationSound() {
            // Phát âm thanh thông báo (nếu có file âm thanh)
            try {
                const audio = new Audio('/Assets/sounds/notification.mp3');
                audio.volume = 0.5;
                audio.play().catch(e => console.log('Could not play sound:', e));
            } catch (e) {
                // Bỏ qua nếu không có file âm thanh
            }
        }
        
        setNotificationCallback(callback) {
            this.notificationCallback = callback;
        }
        
        disconnect() {
            if (this.ws) {
                this.ws.close();
            }
        }
    }
// Khởi tạo notification handler khi trang load
    document.addEventListener('DOMContentLoaded', () => {
        // Lấy username từ session (cần truyền từ PHP)
        const username = document.body.getAttribute('data-username');
        if (username) {
            window.notificationHandler = new NotificationHandler(username);
        }
    });
</script>