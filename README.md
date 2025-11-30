# Hệ thống Đặt Lịch Khám Bệnh - Hạnh Phúc

Hệ thống quản lý đặt lịch khám bệnh với tính năng tin nhắn thời gian thực giữa bác sĩ và bệnh nhân.

## Tính năng chính

- **Đặt lịch khám bệnh**: Bệnh nhân có thể đặt lịch hẹn với bác sĩ
- **Tin nhắn thời gian thực**: Giao tiếp giữa bác sĩ và bệnh nhân
- **Quản lý hồ sơ**: Theo dõi hồ sơ bệnh nhân điện tử
- **Thanh toán trực tuyến**: Tích hợp VNPay
- **Đa vai trò**: Hỗ trợ bác sĩ, chuyên gia, bệnh nhân, nhân viên tiếp tân

## Yêu cầu hệ thống

- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Composer
- PHP Extensions: mysqli, json, openssl, mbstring

## Cài đặt

### 1. Clone repository

```bash
git clone https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep.git
cd KhoaLuanTotNghiep
```

### 2. Cài đặt dependencies

```bash
composer install
```

### 3. Cấu hình môi trường

```bash
cp .env.example.dist .env
# Chỉnh sửa .env với thông tin database của bạn
```

### 4. Import database

```bash
mysql -u your_user -p your_database < hanhphuc.sql
```

### 5. Chạy WebSocket server (tùy chọn)

```bash
php server.php
```

## Tính năng Tin nhắn (Tinnhan)

Hệ thống hỗ trợ hai phương thức giao tiếp:

### WebSocket (Thời gian thực)
- Tin nhắn được gửi và nhận ngay lập tức
- Yêu cầu WebSocket server chạy liên tục
- Phù hợp cho VPS/Dedicated server

### AJAX Polling (Fallback)
- Tự động kích hoạt khi WebSocket không khả dụng
- Hoạt động trên mọi shared hosting
- Độ trễ có thể điều chỉnh qua `POLLING_INTERVAL`

## Triển khai Production

Xem hướng dẫn chi tiết trong [DEPLOYMENT.md](DEPLOYMENT.md)

### Cấu hình quan trọng

```ini
# .env file
APP_DEBUG=false
WEBSOCKET_ENABLED=true  # hoặc false để dùng AJAX polling
WEBSOCKET_HOST=your-domain.com
WEBSOCKET_PORT=8080
```

## Kiểm tra

### Chạy test script

```bash
php tests/test_messaging.php
```

### Test thủ công

1. Mở 2 browser khác nhau
2. Browser 1: Đăng nhập với tài khoản bác sĩ
3. Browser 2: Đăng nhập với tài khoản bệnh nhân
4. Bệnh nhân vào trang Tin nhắn và chọn bác sĩ
5. Gửi tin nhắn qua lại và kiểm tra hiển thị

## Cấu trúc thư mục

```
├── Ajax/              # AJAX handlers
├── Assets/            # CSS, JS, images
├── Controllers/       # PHP controllers
├── Models/            # Database models
├── Views/             # View templates
│   ├── admin/        # Admin views
│   ├── bacsi/        # Doctor views
│   ├── benhnhan/     # Patient views
│   ├── chuyengia/    # Expert views
│   └── ...
├── database/          # Migrations and seeds
├── tests/             # Test files
├── uploads/           # User uploads
├── vendor/            # Composer dependencies
├── Chat.php           # WebSocket chat server
├── server.php         # WebSocket server entry
├── env.php            # Environment configuration
└── index.php          # Application entry point
```

## License

This project is developed as a graduation thesis project.
