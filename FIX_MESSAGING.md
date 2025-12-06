# Sửa lỗi chức năng nhắn tin của bác sĩ/chuyên gia với bệnh nhân

## Vấn đề
Chức năng nhắn tin hoạt động tốt trên môi trường local nhưng không hoạt động trên host (hanhphuc.site).

## Nguyên nhân
WebSocket URL được hard-code là `ws://localhost:8080` trong tất cả các file nhắn tin, khiến cho kết nối không thể thiết lập được từ môi trường production.

## Giải pháp đã triển khai

### 1. File cấu hình động `Assets/websocket-config.php`
- Tự động phát hiện môi trường (local hoặc production)
- Tự động sử dụng `wss://` cho kết nối HTTPS
- Hỗ trợ cấu hình qua biến môi trường (tùy chọn)
- Hỗ trợ reverse proxy (Nginx/Apache)

### 2. Các file đã được cập nhật
- ✅ `/Chta.php` - Trang chat độc lập
- ✅ `/Views/bacsi/pages/tinnhan/index.php` - Giao diện nhắn tin của bác sĩ
- ✅ `/Views/chuyengia/pages/tinnhan/index.php` - Giao diện nhắn tin của chuyên gia
- ✅ `/Views/benhnhan/pages/tinnhan/index.php` - Giao diện nhắn tin của bệnh nhân

### 3. Tài liệu triển khai
- 📖 `WEBSOCKET_DEPLOYMENT.md` - Hướng dẫn đầy đủ về triển khai WebSocket server trên production
- 📄 `.env.websocket.example` - File mẫu cấu hình biến môi trường

## Cách hoạt động

### Trên Local Development
```php
// Tự động sử dụng:
ws://localhost:8080
```

### Trên Production (HTTP)
```php
// Tự động sử dụng:
ws://hanhphuc.site:8080
```

### Trên Production (HTTPS)
```php
// Tự động sử dụng:
wss://hanhphuc.site:8080
```

## Bước triển khai trên Production

### 1. Cập nhật code
```bash
git pull origin main  # hoặc branch của bạn
```

### 2. Đảm bảo WebSocket server đang chạy
```bash
# Kiểm tra
ps aux | grep server.php

# Nếu chưa chạy, khởi động
php server.php &
```

### 3. Mở firewall cho port 8080
```bash
sudo ufw allow 8080/tcp
```

### 4. (Khuyến nghị) Cấu hình reverse proxy cho HTTPS

**Với Nginx:**
```nginx
location /ws {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
}
```

**Với Apache:**
```apache
ProxyPass /ws ws://localhost:8080
ProxyPassReverse /ws ws://localhost:8080
```

Sau đó cập nhật biến môi trường:
```bash
export WEBSOCKET_PATH=/ws
```

### 5. Test kết nối
Mở trình duyệt, vào trang nhắn tin và kiểm tra console (F12):
- Nếu thành công: "✅ WebSocket connected"
- Nếu lỗi: Xem logs và khắc phục theo `WEBSOCKET_DEPLOYMENT.md`

## Tính năng mới

### Cấu hình qua biến môi trường (tùy chọn)
```bash
# Tùy chỉnh host
export WEBSOCKET_HOST=ws.hanhphuc.site

# Tùy chỉnh port
export WEBSOCKET_PORT=9090

# Sử dụng reverse proxy
export WEBSOCKET_PATH=/ws
```

## Tài liệu tham khảo
- Xem `WEBSOCKET_DEPLOYMENT.md` để biết chi tiết đầy đủ về:
  - Cấu hình systemd để tự động khởi động
  - Cấu hình SSL/TLS
  - Xử lý sự cố
  - Bảo mật và monitoring

## Hỗ trợ
Nếu gặp vấn đề:
1. Kiểm tra WebSocket server có đang chạy không
2. Kiểm tra firewall có mở port 8080 không
3. Xem logs: `tail -f websocket.log`
4. Tham khảo phần troubleshooting trong `WEBSOCKET_DEPLOYMENT.md`
