# Test Cases - Kiểm thử chức năng đăng ký

## Mô tả
File này chứa các test case để kiểm thử chức năng đăng ký tài khoản trong hệ thống.

## Các test case được triển khai

### 1. Test đăng ký thành công
- **Mục đích**: Kiểm tra đăng ký với thông tin hợp lệ
- **Dữ liệu test**: Thông tin đầy đủ và hợp lệ
- **Kết quả mong đợi**: Trả về `true`

### 2. Test email đã tồn tại
- **Mục đích**: Kiểm tra hệ thống phát hiện email trùng lặp
- **Dữ liệu test**: Đăng ký 2 lần với cùng email
- **Kết quả mong đợi**: Lần 2 trả về `"email_ton_tai"`

### 3. Test họ tên trống
- **Mục đích**: Kiểm tra validation cho trường họ tên bắt buộc
- **Dữ liệu test**: Họ tên để trống
- **Kết quả mong đợi**: Xử lý lỗi hoặc exception

### 4. Test mã hóa mật khẩu
- **Mục đích**: Xác nhận mật khẩu được mã hóa MD5
- **Dữ liệu test**: Mật khẩu mẫu
- **Kết quả mong đợi**: Mật khẩu được hash bằng MD5

### 5. Test tuổi hợp lệ
- **Mục đích**: Kiểm tra người dùng >= 18 tuổi được đăng ký
- **Dữ liệu test**: Ngày sinh của người 18 tuổi
- **Kết quả mong đợi**: Đăng ký thành công

### 6. Test lưu thông tin bệnh nhân
- **Mục đích**: Kiểm tra tất cả thông tin bệnh nhân được lưu đầy đủ
- **Dữ liệu test**: Thông tin đầy đủ bao gồm tiền sử bệnh, nghề nghiệp...
- **Kết quả mong đợi**: Đăng ký thành công với đầy đủ thông tin

### 7. Test CCCD images optional
- **Mục đích**: Kiểm tra ảnh CCCD không bắt buộc
- **Dữ liệu test**: Đăng ký không có ảnh CCCD
- **Kết quả mong đợi**: Đăng ký thành công

## Cách chạy test

### Chạy trực tiếp bằng PHP CLI:
```bash
php tests/RegistrationTest.php
```

### Yêu cầu:
- PHP >= 8.0
- Database MySQL đã được cấu hình đúng trong `config.php`
- Các bảng cần thiết: `taikhoan`, `nguoidung`, `benhnhan`
- Extension PHP cần thiết: mysqli

## Kết quả test

Test sẽ hiển thị:
- ✓ PASSED: Test thành công
- ✗ FAILED: Test thất bại
- ⚠ WARNING: Cảnh báo (vẫn pass nhưng cần lưu ý)

## Báo cáo test
Sau khi chạy, test sẽ hiển thị:
- Tổng số test cases
- Số test passed
- Số test failed
- Số warning
- Chi tiết từng test case

## Lưu ý
- Test sử dụng dữ liệu mẫu với prefix "BN_TEST_" và email có timestamp để tránh trùng lặp
- Test có thể tạo dữ liệu trong database thực, nên chạy trên môi trường test/development
- Database cần có kết nối đúng theo cấu hình trong `config.php`
