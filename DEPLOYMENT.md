# Deployment Guide - Hệ thống Tin nhắn (Tinnhan)

Hướng dẫn triển khai hệ thống tin nhắn cho ứng dụng trên môi trường hosting (shared host / VPS).

## Mục lục

1. [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
2. [Cài đặt cơ bản](#cài-đặt-cơ-bản)
3. [Cấu hình cơ sở dữ liệu](#cấu-hình-cơ-sở-dữ-liệu)
4. [Cấu hình WebSocket](#cấu-hình-websocket)
5. [Sử dụng AJAX Polling (Fallback)](#sử-dụng-ajax-polling-fallback)
6. [Cấu hình Upload](#cấu-hình-upload)
7. [Supervisor cho WebSocket Server](#supervisor-cho-websocket-server)
8. [Kiểm tra và xác minh](#kiểm-tra-và-xác-minh)
9. [Xử lý sự cố](#xử-lý-sự-cố)

---

## Yêu cầu hệ thống

### Yêu cầu tối thiểu
- PHP 7.4 trở lên (khuyến nghị PHP 8.0+)
- MySQL 5.7 trở lên hoặc MariaDB 10.3+
- Composer (để cài đặt dependencies)
- Extension PHP: `mysqli`, `json`, `openssl`, `mbstring`

### Yêu cầu cho WebSocket (tùy chọn)
- VPS hoặc dedicated server (shared hosting thường không hỗ trợ)
- Quyền chạy process PHP liên tục (daemon)
- Supervisor hoặc systemd để quản lý process

### Nếu không có WebSocket
- AJAX polling sẽ được sử dụng tự động làm fallback
- Hoạt động trên mọi shared hosting

---

## Cài đặt cơ bản

### 1. Clone repository và cài đặt dependencies

```bash
# Clone repository
git clone https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep.git
cd KhoaLuanTotNghiep

# Cài đặt PHP dependencies
composer install --no-dev --optimize-autoloader
```

### 2. Tạo file cấu hình

```bash
# Copy file cấu hình mẫu
cp .env.example.dist .env

# Chỉnh sửa file .env với thông tin của bạn
nano .env
```

### 3. Cấu hình file .env

```ini
# Application Settings
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=hanhphuc
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# WebSocket Configuration (xem phần dưới)
WEBSOCKET_ENABLED=true
WEBSOCKET_HOST=your-domain.com
WEBSOCKET_PORT=8080
WEBSOCKET_PROTOCOL=wss

# Upload Configuration
UPLOAD_DIR=/var/www/html/your-app/uploads/
UPLOAD_URL=/uploads/

# Encryption Keys (QUAN TRỌNG: Thay đổi trong production!)
ENCRYPTION_KEY=your-secure-random-key-here
ENCRYPTION_IV_SEED=your-secure-iv-seed-here
```

---

## Cấu hình cơ sở dữ liệu

### 1. Import database schema

```bash
# Import file SQL
mysql -u your_db_user -p your_database < hanhphuc.sql
```

### 2. Kiểm tra bảng tinnhan

Bảng `tinnhan` cần có cấu trúc sau:

```sql
CREATE TABLE IF NOT EXISTS `tinnhan` (
  `matinnhan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tentk_gui` varchar(100) NOT NULL,
  `tentk_nhan` varchar(100) NOT NULL,
  `noidung` text NOT NULL,
  `thoigiangui` datetime NOT NULL,
  PRIMARY KEY (`matinnhan`),
  INDEX `idx_sender_receiver` (`tentk_gui`, `tentk_nhan`),
  INDEX `idx_time` (`thoigiangui`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Tạo tài khoản test (tùy chọn)

```sql
-- Tạo tài khoản bác sĩ test
INSERT INTO taikhoan (tentk, matkhau, hoten, email, mavaitro, matrangthai) 
VALUES ('bacsi_test', 'hashed_password', 'Bác sĩ Test', 'bacsi@test.com', 2, 1);

-- Tạo tài khoản bệnh nhân test
INSERT INTO taikhoan (tentk, matkhau, hoten, email, mavaitro, matrangthai) 
VALUES ('benhnhan_test', 'hashed_password', 'Bệnh nhân Test', 'benhnhan@test.com', 1, 1);
```

---

## Cấu hình WebSocket

### Option A: VPS với WebSocket Server

#### 1. Cấu hình .env

```ini
WEBSOCKET_ENABLED=true
WEBSOCKET_HOST=your-domain.com
WEBSOCKET_PORT=8080
WEBSOCKET_PROTOCOL=ws
```

#### 2. Khởi chạy WebSocket server

```bash
# Chạy thủ công (để test)
php server.php

# Server sẽ hiển thị:
# WebSocket server running on port 8080
```

#### 3. Sử dụng Supervisor (khuyến nghị)

Tạo file `/etc/supervisor/conf.d/websocket.conf`:

```ini
[program:websocket]
command=php /var/www/html/your-app/server.php
directory=/var/www/html/your-app
user=www-data
autostart=true
autorestart=true
startsecs=3
startretries=3
stderr_logfile=/var/log/websocket.err.log
stdout_logfile=/var/log/websocket.out.log
```

Sau đó:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start websocket
```

#### 4. Cấu hình Nginx proxy (cho SSL/WSS)

Thêm vào file Nginx config:

```nginx
# WebSocket proxy
location /ws/ {
    proxy_pass http://127.0.0.1:8080/;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_read_timeout 86400;
}
```

Và cập nhật .env:

```ini
WEBSOCKET_HOST=your-domain.com
WEBSOCKET_PORT=443
WEBSOCKET_PROTOCOL=wss
```

### Option B: Sử dụng systemd

Tạo file `/etc/systemd/system/websocket.service`:

```ini
[Unit]
Description=WebSocket Chat Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/html/your-app
ExecStart=/usr/bin/php server.php
Restart=on-failure
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Sau đó:

```bash
sudo systemctl daemon-reload
sudo systemctl enable websocket
sudo systemctl start websocket
```

---

## Sử dụng AJAX Polling (Fallback)

Nếu hosting không hỗ trợ WebSocket hoặc bạn muốn sử dụng phương pháp đơn giản hơn:

### 1. Cấu hình .env

```ini
WEBSOCKET_ENABLED=false
POLLING_INTERVAL=3000
```

### 2. Ưu điểm của AJAX Polling

- Hoạt động trên mọi shared hosting
- Không cần cấu hình server đặc biệt
- Dễ dàng triển khai

### 3. Nhược điểm

- Độ trễ cao hơn (phụ thuộc vào POLLING_INTERVAL)
- Tăng tải server do request liên tục

### 4. Điều chỉnh POLLING_INTERVAL

```ini
# Polling nhanh (real-time hơn, tải server cao hơn)
POLLING_INTERVAL=2000

# Polling cân bằng (khuyến nghị)
POLLING_INTERVAL=3000

# Polling chậm (giảm tải server)
POLLING_INTERVAL=5000
```

---

## Cấu hình Upload

### 1. Tạo thư mục uploads

```bash
mkdir -p /var/www/html/your-app/uploads
chmod 755 /var/www/html/your-app/uploads
chown www-data:www-data /var/www/html/your-app/uploads
```

### 2. Cấu hình .env

```ini
UPLOAD_DIR=/var/www/html/your-app/uploads/
UPLOAD_URL=/uploads/
```

### 3. Cấu hình Nginx/Apache

**Nginx:**
```nginx
location /uploads/ {
    alias /var/www/html/your-app/uploads/;
    expires 30d;
    add_header Cache-Control "public, immutable";
}
```

**Apache (.htaccess):**
```apache
<Directory "/var/www/html/your-app/uploads">
    Options -Indexes
    AllowOverride None
    Require all granted
</Directory>
```

---

## Kiểm tra và xác minh

### 1. Kiểm tra cấu hình

```bash
# Kiểm tra PHP syntax
php -l env.php
php -l Chat.php
php -l server.php

# Kiểm tra database connection
php -r "
require_once 'env.php';
require_once 'Models/ketnoi.php';
\$db = new clsketnoi();
\$con = \$db->moKetNoi();
echo \$con ? 'Database OK' : 'Database FAILED';
"
```

### 2. Kiểm tra WebSocket

```bash
# Chạy WebSocket server
php server.php &

# Test connection (cần cài wscat)
npm install -g wscat
wscat -c ws://localhost:8080
```

### 3. Kiểm tra AJAX polling

Mở trình duyệt và gọi:

```
https://your-domain.com/Ajax/chat_api.php?action=get_config
```

Kết quả mong đợi:
```json
{
  "success": true,
  "websocket": {
    "enabled": false,
    "url": "ws://localhost:8080"
  },
  "polling": {
    "interval": 3000
  }
}
```

### 4. Test thủ công messaging

1. Đăng nhập với tài khoản bác sĩ ở browser 1
2. Đăng nhập với tài khoản bệnh nhân ở browser 2 (hoặc incognito)
3. Bệnh nhân chọn bác sĩ để chat
4. Gửi tin nhắn từ cả hai phía
5. Kiểm tra tin nhắn hiển thị đúng ở cả hai bên

---

## Xử lý sự cố

### WebSocket không kết nối được

1. Kiểm tra server đang chạy:
   ```bash
   ps aux | grep server.php
   ```

2. Kiểm tra port đang mở:
   ```bash
   netstat -tlnp | grep 8080
   ```

3. Kiểm tra firewall:
   ```bash
   sudo ufw allow 8080
   ```

4. Kiểm tra log:
   ```bash
   tail -f /var/log/websocket.err.log
   ```

### AJAX polling không hoạt động

1. Kiểm tra session:
   - Đảm bảo đã đăng nhập
   - Session không bị expire

2. Kiểm tra API response:
   ```bash
   curl "https://your-domain.com/Ajax/chat_api.php?action=get_config"
   ```

3. Kiểm tra PHP error log:
   ```bash
   tail -f /var/log/php_errors.log
   ```

### Tin nhắn không lưu vào database

1. Kiểm tra quyền database user
2. Kiểm tra bảng `tinnhan` tồn tại
3. Kiểm tra kết nối database trong .env

### Upload file thất bại

1. Kiểm tra quyền thư mục uploads:
   ```bash
   ls -la uploads/
   ```

2. Kiểm tra PHP upload limits:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

---

## Bảo mật Production

### 1. Cấu hình quan trọng

```ini
APP_DEBUG=false
```

### 2. Thay đổi encryption keys

```ini
ENCRYPTION_KEY=generate-a-strong-random-key
ENCRYPTION_IV_SEED=generate-another-random-string
```

### 3. Sử dụng HTTPS

```ini
WEBSOCKET_PROTOCOL=wss
APP_URL=https://your-domain.com
```

### 4. Giới hạn CORS (nếu cần)

Thêm vào file PHP nếu cần:
```php
header('Access-Control-Allow-Origin: https://your-domain.com');
```

---

## Liên hệ hỗ trợ

Nếu gặp vấn đề trong quá trình triển khai, vui lòng tạo issue trên GitHub repository.
