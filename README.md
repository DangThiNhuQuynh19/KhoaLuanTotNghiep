# Hệ Thống Quản Lý Bệnh Án Điện Tử

## Giới thiệu

Đây là hệ thống quản lý bệnh án điện tử được phát triển bằng PHP, hỗ trợ quản lý hồ sơ bệnh nhân, lịch khám, đơn thuốc và xét nghiệm.

## Công nghệ sử dụng

- **Backend**: PHP
- **Database**: MySQL/MariaDB
- **Frontend**: HTML, CSS, JavaScript
- **Framework/Library**: 
  - Laravel (app/)
  - Ratchet WebSocket
  - PHPMailer
  - Endroid QR Code
  - Google API Client

## Cấu trúc dự án

```
.
├── Controllers/          # Các controller xử lý logic nghiệp vụ
├── Models/              # Các model tương tác với database
├── Views/               # Giao diện người dùng
│   ├── admin/          # Giao diện quản trị
│   ├── bacsi/          # Giao diện bác sĩ
│   ├── benhnhan/       # Giao diện bệnh nhân
│   ├── chuyengia/      # Giao diện chuyên gia
│   ├── nhanvientieptan/    # Giao diện nhân viên tiếp tân
│   └── nhanvienxetnghiem/  # Giao diện nhân viên xét nghiệm
├── Assets/              # Tài nguyên tĩnh (CSS, JS, images)
├── Ajax/                # Xử lý các request Ajax
├── app/                 # Laravel application
├── vendor/              # Dependencies (Composer)
├── config.php          # File cấu hình
├── index.php           # Entry point
└── hanhphuc.sql        # Database schema

```

## Tài liệu Use Case và Sequence Diagram

Dự án bao gồm các tài liệu use case và sequence diagram chi tiết cho các chức năng chính:

### 📋 [Use Case: Tạo Đơn Thuốc](USE_CASE_TAO_DON_THUOC.md)
Tài liệu mô tả chi tiết quy trình tạo đơn thuốc điện tử cho bệnh nhân, bao gồm:
- Luồng sự kiện chính và phụ
- Điều kiện tiên quyết và điều kiện sau
- Quy tắc nghiệp vụ
- Cấu trúc database liên quan
- Test cases
- Các cải tiến đề xuất

### 🔄 [Sequence Diagram: Đặt Lịch Xét Nghiệm](SEQUENCE_DIAGRAM_DAT_LICH_XET_NGHIEM.md)
Sơ đồ sequence UML mô tả quy trình đặt lịch xét nghiệm cho bệnh nhân, bao gồm:
- Sơ đồ sequence đầy đủ với ~60 bước tương tác
- Mô tả chi tiết từng giai đoạn (khởi tạo, nhập liệu, xử lý, xác nhận)
- Tích hợp QR Code generation
- Cấu trúc database và quan hệ giữa các bảng
- Quy tắc nghiệp vụ và xử lý ngoại lệ
- Code PlantUML để render sơ đồ

## Chức năng chính

### 🏥 Quản lý bệnh nhân
- Đăng ký tài khoản bệnh nhân
- Xem hồ sơ bệnh án điện tử
- Xem lịch sử khám bệnh
- Xem đơn thuốc và kết quả xét nghiệm

### 👨‍⚕️ Quản lý bác sĩ/chuyên gia
- Xem lịch làm việc
- Khám bệnh và cập nhật hồ sơ
- **Tạo đơn thuốc điện tử**
- Chỉ định xét nghiệm
- Chuẩn đoán và kết luận

### 💊 Quản lý đơn thuốc
- Tạo đơn thuốc với nhiều loại thuốc
- Ghi rõ liều dùng, số ngày uống, thời gian uống
- Liên kết với hồ sơ bệnh án
- Bệnh nhân xem đơn thuốc trực tuyến

### 🔬 Quản lý xét nghiệm
- Đặt lịch xét nghiệm
- Nhập kết quả xét nghiệm
- Xem kết quả xét nghiệm

### 📅 Quản lý lịch hẹn
- Đặt lịch khám
- Xác nhận/hủy lịch hẹn
- Nhắc nhở lịch hẹn qua email

### 💬 Tính năng khác
- Chat real-time (WebSocket)
- Thanh toán trực tuyến
- Thông báo qua email
- Tạo QR code

## Cài đặt

### Yêu cầu hệ thống
- PHP >= 7.4
- MySQL/MariaDB
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
cd app && composer install && cd ..
```

3. Import database:
```bash
mysql -u your_username -p < hanhphuc.sql
```

4. Cấu hình database trong `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'hanhphuc');
```

5. Khởi động server:
```bash
php -S localhost:8000
```

6. Truy cập: `http://localhost:8000`

## Phân quyền người dùng

| Vai trò | Quyền hạn |
|---------|-----------|
| **Admin** | Quản trị toàn bộ hệ thống |
| **Bác sĩ** | Khám bệnh, tạo đơn thuốc, chỉ định xét nghiệm |
| **Chuyên gia** | Tương tự bác sĩ, có thể tư vấn chuyên sâu |
| **Nhân viên tiếp tân** | Quản lý lịch hẹn, đăng ký khám |
| **Nhân viên xét nghiệm** | Nhập kết quả xét nghiệm |
| **Bệnh nhân** | Xem hồ sơ, đặt lịch, xem đơn thuốc |

## Bảo mật

- Mã hóa mật khẩu
- Session management
- Phân quyền truy cập theo vai trò

### Vấn đề bảo mật cần khắc phục

⚠️ **Quan trọng**: Hệ thống hiện tại có một số vấn đề bảo mật cần được khắc phục trước khi triển khai production:

1. **SQL Injection**: Code hiện tại sử dụng string concatenation để tạo SQL queries thay vì prepared statements. Cần refactor để sử dụng PDO prepared statements hoặc mysqli prepared statements.
   
   Ví dụ code hiện tại (KHÔNG AN TOÀN):
   ```php
   $str = "select * from thuoc where mathuoc='$mathuoc'";
   ```
   
   Nên sửa thành:
   ```php
   $stmt = $con->prepare("select * from thuoc where mathuoc=?");
   $stmt->bind_param("i", $mathuoc);
   ```

2. **XSS Prevention**: Cần validate và escape tất cả user input trước khi hiển thị.

3. **CSRF Protection**: Cần implement CSRF tokens cho các form quan trọng.

Xem file `SECURITY.md` (sẽ được tạo) để biết chi tiết về các vấn đề bảo mật và kế hoạch khắc phục.

## Đóng góp

Mọi đóng góp đều được hoan nghênh. Vui lòng:
1. Fork repository
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. Push lên branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## Liên hệ

- **Repository**: [DangThiNhuQuynh19/KhoaLuanTotNghiep](https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep)

## License

Dự án này được phát triển cho mục đích học tập và nghiên cứu.

---

**Lưu ý**: Hệ thống này được phát triển cho mục đích khóa luận tốt nghiệp. Trước khi triển khai vào môi trường production, cần thực hiện thêm các biện pháp bảo mật và tối ưu hóa.
