# Hệ thống Quản lý Bệnh viện Hạnh Phúc

## Giới thiệu
Hệ thống quản lý bệnh viện Hạnh Phúc là một ứng dụng web được phát triển để quản lý toàn bộ hoạt động của bệnh viện, bao gồm:

- Quản lý thông tin bệnh nhân và hồ sơ bệnh án
- Quản lý lịch khám bệnh và đặt lịch hẹn
- Quản lý bác sĩ và nhân viên y tế
- Quản lý xét nghiệm và kết quả xét nghiệm
- Quản lý kho thuốc và đơn thuốc
- Hệ thống thanh toán và hóa đơn
- Hệ thống tin nhắn và thông báo

## Công nghệ sử dụng

### Backend
- PHP
- MySQL/MariaDB

### Frontend
- HTML, CSS, JavaScript
- Laravel Framework (trong thư mục `app`)

### Khác
- WebSocket (Chat realtime - `Chat.php`, `server.php`)
- Composer (Quản lý dependencies PHP)

## Cấu trúc dự án

```
KhoaLuanTotNghiep/
├── Ajax/              # Xử lý AJAX requests
├── Assets/            # Tài nguyên tĩnh (CSS, JS, Images)
├── Controllers/       # Controllers xử lý logic
├── Models/            # Models tương tác với database
├── Views/             # Views hiển thị giao diện
├── app/               # Laravel application
├── uploads/           # Thư mục lưu file upload
├── vendor/            # Dependencies từ Composer
├── Chat.php           # WebSocket chat server
├── server.php         # WebSocket server
├── index.php          # Entry point
├── config.php         # Cấu hình hệ thống
├── hanhphuc.sql       # Database schema
└── DATABASE.md        # Tài liệu mô tả database
```

## Tài liệu

### Tài liệu cơ sở dữ liệu
Xem file [DATABASE.md](./DATABASE.md) để tìm hiểu chi tiết về:
- Danh sách các bảng trong cơ sở dữ liệu
- Mô tả chi tiết từng bảng
- Mối quan hệ giữa các bảng
- Cấu trúc dữ liệu

## Cài đặt

### Yêu cầu hệ thống
- PHP >= 8.0
- MySQL/MariaDB >= 5.7
- Composer
- Web server (Apache/Nginx)

### Các bước cài đặt

1. Clone repository:
```bash
git clone https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep.git
cd KhoaLuanTotNghiep
```

2. Cài đặt dependencies:
```bash
composer install
```

3. Import database:
```bash
mysql -u username -p database_name < hanhphuc.sql
```

4. Cấu hình kết nối database trong `config.php`

5. Khởi động web server và truy cập ứng dụng

## Chức năng chính

### Dành cho Bệnh nhân
- Đăng ký tài khoản và quản lý hồ sơ cá nhân
- Đặt lịch khám bệnh với bác sĩ
- Đặt lịch xét nghiệm
- Xem kết quả xét nghiệm và hồ sơ bệnh án
- Thanh toán trực tuyến
- Nhận thông báo và tin nhắn

### Dành cho Bác sĩ
- Quản lý lịch làm việc
- Xem danh sách bệnh nhân và lịch hẹn
- Khám bệnh và ghi nhận thông tin vào hồ sơ
- Kê đơn thuốc
- Xem và nhận xét kết quả xét nghiệm
- Trao đổi tin nhắn với bệnh nhân

### Dành cho Quản trị viên
- Quản lý người dùng và phân quyền
- Quản lý danh mục (chuyên khoa, thuốc, xét nghiệm...)
- Quản lý phòng ban
- Thống kê và báo cáo
- Cấu hình hệ thống

## Bảo mật
- Mã hóa mật khẩu
- Phân quyền người dùng
- Bảo mật thông tin y tế
- Xác thực và phân quyền truy cập

## Đóng góp
Mọi đóng góp đều được chào đón. Vui lòng tạo pull request hoặc báo cáo lỗi qua Issues.

## Giấy phép
Dự án này được phát triển cho mục đích học tập và nghiên cứu.

## Liên hệ
- Repository: https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep
- Tác giả: Đặng Thị Như Quỳnh

---
© 2025 Hệ thống Quản lý Bệnh viện Hạnh Phúc
