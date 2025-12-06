# Hướng dẫn triển khai WebSocket Server cho chức năng nhắn tin

## Tổng quan
Hệ thống nhắn tin sử dụng WebSocket để giao tiếp thời gian thực giữa bác sĩ/chuyên gia và bệnh nhân. Tài liệu này mô tả cách triển khai WebSocket server trên môi trường production (hanhphuc.site).

## Yêu cầu

1. **PHP >= 7.4** với các extension:
   - php-sockets
   - php-mbstring
   - php-json

2. **Composer** để cài đặt dependencies

3. **Quyền truy cập SSH** vào server production

4. **Cổng 8080** phải được mở và có thể truy cập từ bên ngoài

## Cài đặt Dependencies

```bash
cd /path/to/your/project
composer install
```

## Cấu hình Firewall

Mở cổng 8080 cho WebSocket:

```bash
# Với UFW (Ubuntu/Debian)
sudo ufw allow 8080/tcp

# Với firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --reload
```

## Chạy WebSocket Server

### 1. Chạy thử nghiệm (Foreground)

```bash
cd /path/to/your/project
php server.php
```

### 2. Chạy nền (Background) với nohup

```bash
cd /path/to/your/project
nohup php server.php > websocket.log 2>&1 &
echo $! > websocket.pid
```

### 3. Chạy với systemd (Khuyến nghị cho production)

Tạo file service: `/etc/systemd/system/websocket-chat.service`

```ini
[Unit]
Description=WebSocket Chat Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php /path/to/your/project/server.php
Restart=always
RestartSec=10
StandardOutput=append:/var/log/websocket-chat.log
StandardError=append:/var/log/websocket-chat-error.log

[Install]
WantedBy=multi-user.target
```

Khởi động service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable websocket-chat
sudo systemctl start websocket-chat
sudo systemctl status websocket-chat
```

Kiểm tra logs:

```bash
sudo journalctl -u websocket-chat -f
# hoặc
tail -f /var/log/websocket-chat.log
```

## Cấu hình cho HTTPS/WSS

Nếu website chạy qua HTTPS (khuyến nghị), bạn cần cấu hình reverse proxy để WebSocket cũng chạy qua WSS.

### Với Nginx

Thêm vào file cấu hình Nginx của bạn (`/etc/nginx/sites-available/hanhphuc.site`):

```nginx
# WebSocket proxy
location /ws {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_read_timeout 86400;
}
```

Sau đó reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

Nếu dùng cấu hình này, bạn cần cập nhật `Assets/websocket-config.php`:

```php
// Thay đổi từ:
define('WEBSOCKET_URL', "{$wsProtocol}://{$wsHost}:{$wsPort}");

// Thành:
define('WEBSOCKET_URL', "{$wsProtocol}://{$wsHost}/ws");
```

### Với Apache

Enable các module cần thiết:

```bash
sudo a2enmod proxy proxy_http proxy_wstunnel
sudo systemctl restart apache2
```

Thêm vào VirtualHost configuration:

```apache
<VirtualHost *:443>
    ServerName hanhphuc.site
    
    # WebSocket proxy
    ProxyPass /ws ws://localhost:8080
    ProxyPassReverse /ws ws://localhost:8080
    
    # Các cấu hình khác...
</VirtualHost>
```

## Kiểm tra kết nối

### 1. Kiểm tra WebSocket Server đang chạy

```bash
netstat -tuln | grep 8080
# hoặc
ss -tuln | grep 8080
```

### 2. Test kết nối từ browser console

```javascript
// Thử kết nối WebSocket
const ws = new WebSocket('wss://hanhphuc.site:8080');
ws.onopen = () => console.log('Connected!');
ws.onerror = (err) => console.error('Error:', err);
ws.onclose = () => console.log('Disconnected');
```

### 3. Test với websocat (nếu đã cài)

```bash
websocat ws://localhost:8080
```

## Xử lý sự cố

### Lỗi: "Connection refused"

- Kiểm tra WebSocket server có đang chạy không: `ps aux | grep server.php`
- Kiểm tra firewall: `sudo ufw status` hoặc `sudo firewall-cmd --list-all`
- Kiểm tra port 8080 có được lắng nghe không: `netstat -tuln | grep 8080`

### Lỗi: "Mixed Content" trên HTTPS

- Đảm bảo sử dụng `wss://` thay vì `ws://` khi site chạy HTTPS
- Cấu hình reverse proxy như mô tả ở trên

### Lỗi: "WebSocket closed" liên tục

- Kiểm tra logs: `tail -f /var/log/websocket-chat.log`
- Kiểm tra kết nối database trong `Models/ChatUserModel.php`
- Restart WebSocket server: `sudo systemctl restart websocket-chat`

### Server bị crash

- Kiểm tra logs để tìm lỗi
- Đảm bảo PHP memory_limit đủ lớn (ít nhất 128M)
- Systemd sẽ tự động restart nếu cấu hình đúng

## Monitoring

### Kiểm tra status

```bash
sudo systemctl status websocket-chat
```

### Xem logs realtime

```bash
sudo journalctl -u websocket-chat -f
```

### Restart service

```bash
sudo systemctl restart websocket-chat
```

### Stop service

```bash
sudo systemctl stop websocket-chat
```

## Lưu ý bảo mật

1. **Không expose trực tiếp port 8080 ra internet** nếu có thể, sử dụng reverse proxy
2. **Cấu hình SSL/TLS** cho WebSocket (WSS)
3. **Giới hạn kết nối** để tránh DDoS
4. **Validate dữ liệu** từ client trước khi xử lý
5. **Cập nhật dependencies** thường xuyên: `composer update`

## Tham khảo

- [Ratchet Documentation](http://socketo.me/)
- [WebSocket Protocol](https://tools.ietf.org/html/rfc6455)
- [Nginx WebSocket Proxy](https://www.nginx.com/blog/websocket-nginx/)
