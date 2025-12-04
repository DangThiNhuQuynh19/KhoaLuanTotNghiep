# Tài liệu Test Cases - Chức năng Đăng ký Tài khoản

## 📋 Tổng quan

Bộ test cases này được thiết kế để kiểm thử toàn diện chức năng đăng ký tài khoản bệnh nhân trong hệ thống quản lý bệnh viện.

## 🎯 Mục tiêu kiểm thử

- Đảm bảo quy trình đăng ký hoạt động chính xác
- Kiểm tra validation dữ liệu đầu vào
- Xác minh bảo mật mật khẩu
- Kiểm tra xử lý lỗi và trường hợp ngoại lệ
- Đảm bảo tính toàn vẹn dữ liệu trong database

## 📁 Cấu trúc File

```
tests/
├── RegistrationTest.php    # File chứa các test cases
├── README.md               # Hướng dẫn cơ bản
├── run_tests.sh           # Script chạy test trên Linux/Mac
└── DOCUMENTATION.md       # Tài liệu chi tiết (file này)
```

## 🧪 Danh sách Test Cases

### Test 1: Đăng ký thành công với thông tin hợp lệ
**Mục đích:** Kiểm tra quy trình đăng ký cơ bản hoạt động đúng

**Dữ liệu đầu vào:**
- Mã bệnh nhân: BN_TEST_[random]
- Email: test_[timestamp]@example.com (mã hóa)
- Họ tên: Nguyễn Văn Test
- Ngày sinh: 1990-01-01
- SĐT: 0123456789 (mã hóa)
- CCCD: 123456789012 (mã hóa)
- Giới tính: Nam
- Nghề nghiệp: Kỹ sư

**Kết quả mong đợi:**
- Trả về `true`
- Dữ liệu được lưu vào 3 bảng: `taikhoan`, `nguoidung`, `benhnhan`
- Mật khẩu được hash bằng MD5
- Vai trò = 1 (bệnh nhân)
- Trạng thái = 1 (active)

**Độ ưu tiên:** Cao

---

### Test 2: Kiểm tra email đã tồn tại
**Mục đích:** Đảm bảo hệ thống phát hiện và ngăn chặn email trùng lặp

**Dữ liệu đầu vào:**
- Đăng ký lần 1: Thông tin hợp lệ với email unique
- Đăng ký lần 2: Cùng email nhưng thông tin khác

**Kết quả mong đợi:**
- Lần 1: Đăng ký thành công (true)
- Lần 2: Trả về `"email_ton_tai"`
- Không tạo thêm tài khoản mới

**Độ ưu tiên:** Cao

---

### Test 3: Kiểm tra họ tên trống
**Mục đích:** Xác minh validation cho trường bắt buộc

**Dữ liệu đầu vào:**
- Họ tên: "" (chuỗi rỗng)
- Các trường khác: hợp lệ

**Kết quả mong đợi:**
- Không trả về `true`
- Có thể trả về lỗi hoặc throw exception
- Không tạo dữ liệu trong database

**Độ ưu tiên:** Trung bình

---

### Test 4: Kiểm tra mã hóa mật khẩu
**Mục đích:** Xác nhận mật khẩu được bảo mật đúng cách

**Dữ liệu đầu vào:**
- Mật khẩu: "testpassword123"

**Kết quả mong đợi:**
- Mật khẩu được hash bằng MD5
- Hash: b3e508d6e62e50b49eefa3c464d79e00
- Mật khẩu gốc không được lưu trực tiếp

**Độ ưu tiên:** Cao (Bảo mật)

**⚠️ QUAN TRỌNG:** MD5 không còn được khuyến nghị cho mục đích bảo mật vì đã bị phá vỡ về mặt mã hóa. Test này chỉ xác nhận implementation hiện tại. **NÊN nâng cấp** lên `password_hash()` với bcrypt hoặc argon2 để tăng cường bảo mật.

---

### Test 5: Kiểm tra tuổi hợp lệ (>= 18 tuổi)
**Mục đích:** Xác minh quy định độ tuổi đăng ký

**Dữ liệu đầu vào:**
- Ngày sinh: Tính toán để người dùng đúng 18 tuổi
- Các trường khác: hợp lệ

**Kết quả mong đợi:**
- Đăng ký thành công (true)
- Tuổi được tính chính xác từ ngày sinh

**Độ ưu tiên:** Cao

**Business Rule:** Hệ thống chỉ cho phép người >= 18 tuổi đăng ký tài khoản riêng

---

### Test 6: Kiểm tra lưu thông tin bệnh nhân đầy đủ
**Mục đích:** Đảm bảo tất cả thông tin bệnh nhân được lưu chính xác

**Dữ liệu đầu vào:**
- Đầy đủ thông tin:
  - Họ tên: Trần Thị Test
  - Giới tính: Nữ
  - Nghề nghiệp: Giáo viên
  - Tiền sử bệnh gia đình: Tiểu đường
  - Tiền sử bệnh bản thân: Hen suyễn
  - Địa chỉ: 456 Đường ABC, XP002, TP002

**Kết quả mong đợi:**
- Đăng ký thành công (true)
- Tất cả thông tin được lưu vào đúng bảng
- Mối quan hệ với người thân = "bản thân"

**Độ ưu tiên:** Cao

---

### Test 7: Kiểm tra CCCD images là optional
**Mục đích:** Xác nhận ảnh CCCD không bắt buộc

**Dữ liệu đầu vào:**
- CCCD số: 111222333444 (mã hóa)
- CCCD ảnh mặt trước: "" (rỗng)
- CCCD ảnh mặt sau: "" (rỗng)

**Kết quả mong đợi:**
- Đăng ký thành công (true)
- Hệ thống chấp nhận NULL hoặc chuỗi rỗng cho ảnh CCCD

**Độ ưu tiên:** Trung bình

---

## 🚀 Cách chạy test

### Phương pháp 1: Chạy trực tiếp
```bash
cd /path/to/KhoaLuanTotNghiep
php tests/RegistrationTest.php
```

### Phương pháp 2: Sử dụng script
```bash
cd /path/to/KhoaLuanTotNghiep
./tests/run_tests.sh
```

### Phương pháp 3: Chạy từ PHP code
```php
<?php
require_once 'tests/RegistrationTest.php';
$test = new RegistrationTest();
$success = $test->runAllTests();
?>
```

## ⚙️ Yêu cầu hệ thống

### Phần mềm:
- PHP >= 8.0
- MySQL/MariaDB
- Extension: mysqli, openssl

### Database:
- Database name: `hanhphuc`
- Các bảng cần thiết:
  - `taikhoan` (tentk, matkhau, mavaitro, matrangthai)
  - `nguoidung` (manguoidung, hoten, ngaysinh, gioitinh, cccd, sdt, email, ...)
  - `benhnhan` (mabenhnhan, nghenghiep, tiensubenhtatcuagiadinh, tiensubenhtatcuabenhnhan, ...)

### Cấu hình:
- File `Models/ketnoi.php` cấu hình đúng connection string
- File `Assets/config.php` định nghĩa encryption key

## 📊 Định dạng Output

### Test đang chạy:
```
================================================================================
Test 1: Đăng ký thành công với thông tin hợp lệ
================================================================================
✓ PASSED: Đăng ký thành công
```

### Báo cáo tổng kết:
```
╔══════════════════════════════════════════════════════════════════════════════╗
║                    TỔNG KẾT KẾT QUẢ KIỂM THỬ                     ║
╚══════════════════════════════════════════════════════════════════════════════╝
Test 1: Đăng ký thành công với thông tin hợp lệ            [PASSED]
Test 2: Kiểm tra email đã tồn tại                              [PASSED]
...

--------------------------------------------------------------------------------
Tổng số test: 7
✓ Passed: 7
✗ Failed: 0
⚠ Warning: 0
```

## 🔍 Phân tích kết quả

### Status codes:
- **PASSED (✓)**: Test thành công hoàn toàn
- **FAILED (✗)**: Test thất bại, có lỗi hoặc không đúng kết quả mong đợi
- **WARNING (⚠)**: Test pass nhưng có điểm cần lưu ý

### Exit codes:
- `0`: Tất cả test pass
- `1`: Có ít nhất 1 test failed

## 🛠️ Troubleshooting

### Lỗi: "No such file or directory"
**Nguyên nhân:** Không kết nối được database
**Giải pháp:** 
1. Kiểm tra MySQL service đang chạy
2. Xác nhận thông tin kết nối trong `Models/ketnoi.php`
3. Kiểm tra user có quyền truy cập database

### Lỗi: "Class not found"
**Nguyên nhân:** Không tìm thấy class Controller/Model
**Giải pháp:**
1. Kiểm tra đường dẫn trong `require_once`
2. Xác nhận file tồn tại
3. Chạy từ thư mục root của project

### Lỗi: "Call to undefined function encryptData()"
**Nguyên nhân:** Chưa load config.php
**Giải pháp:**
1. Đảm bảo `Assets/config.php` được require
2. Kiểm tra openssl extension được enable

## 📈 Coverage

| Chức năng | Test Coverage |
|-----------|---------------|
| Đăng ký cơ bản | ✓ |
| Validation email | ✓ |
| Validation họ tên | ✓ |
| Mã hóa mật khẩu | ✓ |
| Kiểm tra độ tuổi | ✓ |
| Lưu thông tin bệnh nhân | ✓ |
| CCCD optional | ✓ |
| Xử lý file upload | ✗ (Cần thêm) |
| Validation số điện thoại | ✗ (Cần thêm) |
| Validation CCCD | ✗ (Cần thêm) |

## 🔐 Bảo mật

### Điểm mạnh:
- ✓ Email, SĐT, CCCD được mã hóa trước khi lưu
- ✓ Mật khẩu được hash (MD5)
- ✓ Kiểm tra email trùng lặp

### Điểm cần cải thiện:
- ⚠ MD5 không còn an toàn, nên dùng `password_hash()` với bcrypt/argon2
- ⚠ Cần thêm rate limiting để chống brute force
- ⚠ Cần thêm CSRF token cho form đăng ký
- ⚠ Validation input có thể mạnh hơn (SQL injection, XSS)

## 📝 Best Practices

1. **Chạy test thường xuyên**: Sau mỗi thay đổi code liên quan đến đăng ký
2. **Test trên môi trường riêng**: Không chạy trên production database
3. **Backup trước khi test**: Test có thể tạo dữ liệu mẫu
4. **Review test results**: Không chỉ nhìn pass/fail, đọc chi tiết output
5. **Update test cases**: Khi có thay đổi business logic

## 🔄 Maintain & Extend

### Thêm test case mới:
```php
public function testYourNewTest() {
    $testName = "Test N: Mô tả test";
    echo "\n" . str_repeat("=", 80) . "\n";
    echo $testName . "\n";
    echo str_repeat("=", 80) . "\n";
    
    try {
        // Your test logic
        $result = $this->controller->dangkytk(...);
        
        if ($result === true) {
            echo "✓ PASSED: Mô tả\n";
            $this->testResults[] = ['test' => $testName, 'status' => 'PASSED'];
            return true;
        } else {
            echo "✗ FAILED: ...\n";
            $this->testResults[] = ['test' => $testName, 'status' => 'FAILED', 'message' => $result];
            return false;
        }
    } catch (Exception $e) {
        echo "✗ FAILED: Exception - " . $e->getMessage() . "\n";
        $this->testResults[] = ['test' => $testName, 'status' => 'FAILED', 'message' => $e->getMessage()];
        return false;
    }
}
```

Sau đó thêm vào `runAllTests()`:
```php
$this->testYourNewTest();
```

## 📞 Liên hệ & Hỗ trợ

Nếu gặp vấn đề với test cases, vui lòng:
1. Kiểm tra phần Troubleshooting
2. Xem lại requirements
3. Liên hệ team phát triển

## 📚 Tài liệu tham khảo

- PHP Testing Best Practices: https://phpunit.de/
- Database Testing: https://www.php.net/manual/en/book.mysqli.php
- Security Best Practices: https://cheatsheetseries.owasp.org/

---

**Version:** 1.0  
**Last Updated:** 2025-12-04  
**Author:** Copilot AI Assistant
