# Test Cases - Kiểm thử chức năng đăng ký

## 📋 Mô tả
Bộ test cases này kiểm thử toàn diện chức năng đăng ký tài khoản bệnh nhân trong hệ thống.

## 🎯 Tổng quan
- **Tổng số test cases**: 7
- **File test**: `RegistrationTest.php`
- **Chức năng kiểm thử**: Đăng ký tài khoản (`dangkytk`)

## 📝 Các test case được triển khai

### 1. ✓ Test đăng ký thành công
Kiểm tra đăng ký với thông tin hợp lệ, đảm bảo dữ liệu được lưu vào 3 bảng (taikhoan, nguoidung, benhnhan)

### 2. ✓ Test email đã tồn tại
Kiểm tra hệ thống phát hiện và ngăn chặn email trùng lặp

### 3. ✓ Test họ tên trống
Kiểm tra validation cho trường họ tên bắt buộc

### 4. ✓ Test mã hóa mật khẩu
Xác nhận mật khẩu được hash bằng MD5 (lưu ý: nên nâng cấp lên bcrypt)

### 5. ✓ Test tuổi hợp lệ (>= 18)
Kiểm tra quy định người dùng phải từ 18 tuổi trở lên

### 6. ✓ Test lưu thông tin bệnh nhân
Kiểm tra tất cả thông tin (tiền sử bệnh, nghề nghiệp...) được lưu đầy đủ

### 7. ✓ Test CCCD images optional
Kiểm tra ảnh CCCD không bắt buộc khi đăng ký

## 🚀 Cách chạy test

### Cách 1: Chạy trực tiếp
```bash
cd /path/to/KhoaLuanTotNghiep
php tests/RegistrationTest.php
```

### Cách 2: Sử dụng script
```bash
cd /path/to/KhoaLuanTotNghiep
./tests/run_tests.sh
```

## ⚙️ Yêu cầu
- **PHP**: >= 8.0
- **Database**: MySQL/MariaDB
- **Extensions**: mysqli, openssl
- **Tables**: taikhoan, nguoidung, benhnhan

## 📊 Kết quả test

### Status Codes:
- ✓ **PASSED**: Test thành công
- ✗ **FAILED**: Test thất bại
- ⚠ **WARNING**: Cảnh báo (pass nhưng cần lưu ý)

### Báo cáo:
Sau khi chạy, test hiển thị:
- Tổng số test cases
- Số test passed/failed/warning
- Chi tiết từng test case với lý do (nếu failed)
- Thời gian thực thi

## 📚 Tài liệu

Xem thêm chi tiết tại:
- **DOCUMENTATION.md**: Tài liệu chi tiết từng test case, troubleshooting, best practices
- **run_tests.sh**: Script tự động chạy test

## 🔍 Lưu ý quan trọng

### ⚠️ Môi trường test:
- **KHÔNG** chạy trên production database
- Test tạo dữ liệu mẫu với prefix "BN_TEST_"
- Email sử dụng timestamp để tránh trùng lặp

### 🔐 Bảo mật:
- Mật khẩu hiện dùng MD5 (cần nâng cấp lên bcrypt/argon2)
- Email, SĐT, CCCD được mã hóa AES-256-CBC
- Kiểm tra email trùng lặp trước khi tạo

### 🐛 Troubleshooting:
- **"No such file or directory"**: Kiểm tra MySQL service và connection
- **"Class not found"**: Chạy từ thư mục root của project
- **"Undefined function"**: Kiểm tra Assets/config.php được load

## 📞 Hỗ trợ
Gặp vấn đề? Tham khảo:
1. File DOCUMENTATION.md (chi tiết đầy đủ)
2. Kiểm tra requirements và troubleshooting
3. Liên hệ team phát triển
