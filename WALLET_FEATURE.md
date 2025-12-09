# Hướng dẫn thêm chức năng xem số dư ví bệnh nhân

## Mô tả
Chức năng này cho phép bệnh nhân xem số dư ví Hạnh Phúc của họ thông qua giao diện web.

## Các thay đổi đã thực hiện

### 1. Database Migration
File: `migrations/add_vitien_column.sql`

Thêm cột `vitien` (số dư ví) vào bảng `taikhoan`:
```sql
ALTER TABLE `taikhoan` 
ADD COLUMN `vitien` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Số dư ví của người dùng';
```

### 2. Model Layer
File: `Models/mtaikhoan.php`

Thêm phương thức `getSoDuVi()` để lấy số dư ví của người dùng dựa trên tên tài khoản.

### 3. Controller Layer
File: `Controllers/ctaikhoan.php`

Thêm phương thức `getSoDuVi()` để xử lý logic lấy số dư ví.

### 4. View Layer
File: `Views/benhnhan/pages/vi/index.php`

Tạo trang mới để hiển thị thông tin ví bệnh nhân với:
- Số dư hiện tại
- Thông tin tài khoản
- Trạng thái ví
- Phương thức thanh toán

### 5. Navigation
File: `Views/benhnhan/layout/header.php`

Thêm link "Ví Hạnh Phúc" vào menu dropdown của bệnh nhân.

## Cách áp dụng

### Bước 1: Chạy Migration
Truy cập vào phpMyAdmin hoặc MySQL command line và chạy:
```sql
ALTER TABLE `taikhoan` 
ADD COLUMN `vitien` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Số dư ví của người dùng';
```

Hoặc import file: `migrations/add_vitien_column.sql`

### Bước 2: Kiểm tra code đã được cập nhật
Đảm bảo các file sau đã được cập nhật:
- `Models/mtaikhoan.php`
- `Controllers/ctaikhoan.php`
- `Views/benhnhan/layout/header.php`
- `Views/benhnhan/pages/vi/index.php`

### Bước 3: Test chức năng
1. Đăng nhập với tài khoản bệnh nhân
2. Click vào dropdown menu bên cạnh tên người dùng
3. Chọn "Ví Hạnh Phúc"
4. Kiểm tra xem số dư có hiển thị đúng không

## Tính năng

- ✅ Hiển thị số dư ví hiện tại
- ✅ Hiển thị thông tin tài khoản
- ✅ Giao diện đẹp, responsive
- ✅ Tích hợp vào menu điều hướng

## Lưu ý

- Mặc định số dư ví của các tài khoản hiện tại sẽ là 0.00 VND
- Để cập nhật số dư cho tài khoản, có thể chạy SQL:
  ```sql
  UPDATE taikhoan SET vitien = [số tiền] WHERE tentk = '[tên tài khoản]';
  ```
- Chức năng nạp tiền vào ví có thể được phát triển thêm trong tương lai

## Screenshots

Trang Ví Hạnh Phúc hiển thị:
- Số dư hiện tại với định dạng VND
- Icon ví điện tử
- Thông tin tài khoản và trạng thái
- Nút quay lại trang chủ

## Hỗ trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. Database đã có cột `vitien` chưa
2. Session người dùng có đúng không
3. Các file PHP có được include đúng không
