# Hướng Dẫn Triển Khai Chat Trên VPS

## 1. Mở Port 8080 Trên VPS Firewall

### Nếu VPS dùng UFW (Ubuntu/Debian):
```bash
# Mở port 8080
sudo ufw allow 8080/tcp

# Kiểm tra trạng thái
sudo ufw status

# Reload firewall
sudo ufw reload
```

### Nếu VPS dùng Firewalld (CentOS/RHEL):
```bash
# Mở port 8080
sudo firewall-cmd --permanent --add-port=8080/tcp

# Reload firewall
sudo firewall-cmd --reload

# Kiểm tra
sudo firewall-cmd --list-ports
```

### Nếu VPS dùng iptables:
```bash
# Mở port 8080
sudo iptables -A INPUT -p tcp --dport 8080 -j ACCEPT

# Lưu cấu hình
sudo iptables-save > /etc/iptables.rules
```

---

## 2. Chạy WebSocket Server Với Supervisor

### Bước 1: Cài đặt Supervisor
```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

### Bước 2: Tạo file cấu hình cho WebSocket
```bash
sudo nano /etc/supervisor/conf.d/websocket-chat.conf
```

Nội dung file:
```ini
[program:websocket-chat]
command=php /var/www/html/hanhphuc.site/server.php
directory=/var/www/html/hanhphuc.site
autostart=true
autorestart=true
stderr_logfile=/var/log/websocket-chat.err.log
stdout_logfile=/var/log/websocket-chat.out.log
user=www-data
```

**Lưu ý:** Thay `/var/www/html/hanhphuc.site` bằng đường dẫn thực tế của project trên VPS.

### Bước 3: Khởi động Supervisor
```bash
# Reload cấu hình
sudo supervisorctl reread
sudo supervisorctl update

# Khởi động WebSocket server
sudo supervisorctl start websocket-chat

# Kiểm tra trạng thái
sudo supervisorctl status websocket-chat
```

### Các lệnh quản lý khác:
```bash
# Dừng server
sudo supervisorctl stop websocket-chat

# Khởi động lại
sudo supervisorctl restart websocket-chat

# Xem log lỗi
tail -f /var/log/websocket-chat.err.log

# Xem log output
tail -f /var/log/websocket-chat.out.log
```

---

## 3. Chạy WebSocket Server Với Systemd (Cách khác)

### Bước 1: Tạo file service
```bash
sudo nano /etc/systemd/system/websocket-chat.service
```

Nội dung file:
```ini
[Unit]
Description=WebSocket Chat Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html/hanhphuc.site
ExecStart=/usr/bin/php /var/www/html/hanhphuc.site/server.php
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

### Bước 2: Kích hoạt và khởi động
```bash
# Reload systemd
sudo systemctl daemon-reload

# Kích hoạt tự động chạy khi khởi động
sudo systemctl enable websocket-chat

# Khởi động
sudo systemctl start websocket-chat

# Kiểm tra trạng thái
sudo systemctl status websocket-chat
```

### Các lệnh quản lý khác:
```bash
# Dừng
sudo systemctl stop websocket-chat

# Khởi động lại
sudo systemctl restart websocket-chat

# Xem log
sudo journalctl -u websocket-chat -f
```

---

## 4. Bảo Vệ Thư Mục Uploads

File `.htaccess` đã được tạo trong thư mục `uploads/` với các cấu hình:
- **Chặn liệt kê file**: `Options -Indexes` - Ngăn không cho truy cập hanhphuc.site/uploads hiển thị danh sách file
- **Cho phép file hợp lệ**: Chỉ cho phép truy cập các file .jpg, .jpeg, .png, .gif, .pdf
- **Chặn file PHP**: Ngăn chặn thực thi file PHP trong thư mục uploads để bảo mật

### Kiểm tra Apache có bật mod_rewrite:
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# CentOS/RHEL
# Kiểm tra trong /etc/httpd/conf/httpd.conf
# Đảm bảo AllowOverride All cho thư mục web
```

---

## 5. Cấu Hình SSL Cho WebSocket (Tùy chọn)

Nếu muốn dùng `wss://` (WebSocket Secure), bạn cần:

### Cách 1: Dùng Nginx làm Reverse Proxy
Thêm vào file cấu hình Nginx của domain:
```nginx
location /ws {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_read_timeout 86400;
}
```

Sau đó đổi `WEBSOCKET_URL` trong `Assets/config.php`:
```php
define('WEBSOCKET_URL', 'wss://hanhphuc.site/ws');
```

### Cách 2: Dùng Apache làm Reverse Proxy
Bật modules cần thiết:
```bash
sudo a2enmod proxy proxy_wstunnel
sudo systemctl restart apache2
```

Thêm vào VirtualHost:
```apache
ProxyPass /ws ws://127.0.0.1:8080
ProxyPassReverse /ws ws://127.0.0.1:8080
```

---

## 6. Kiểm Tra Hoạt Động

### Test WebSocket từ trình duyệt:
Mở Console (F12) và chạy:
```javascript
var ws = new WebSocket('ws://hanhphuc.site:8080');
ws.onopen = function() { console.log('Connected!'); };
ws.onerror = function(e) { console.log('Error:', e); };
```

### Kiểm tra port đang lắng nghe:
```bash
netstat -tlnp | grep 8080
```

### Kiểm tra log WebSocket:
```bash
# Nếu dùng Supervisor
tail -f /var/log/websocket-chat.out.log

# Nếu dùng Systemd
sudo journalctl -u websocket-chat -f
```

---

## 7. Troubleshooting

### WebSocket không kết nối được:
1. Kiểm tra port 8080 đã mở: `sudo ufw status` hoặc `firewall-cmd --list-ports`
2. Kiểm tra WebSocket server đang chạy: `supervisorctl status` hoặc `systemctl status websocket-chat`
3. Kiểm tra log lỗi

### Không upload được file:
1. Kiểm tra quyền thư mục uploads: `chmod 755 uploads`
2. Kiểm tra owner: `chown www-data:www-data uploads`

### Chat không hiển thị tin nhắn:
1. Mở Console (F12) xem có lỗi JavaScript không
2. Kiểm tra WebSocket đã kết nối chưa
3. Kiểm tra database có bảng `tinnhan` không
