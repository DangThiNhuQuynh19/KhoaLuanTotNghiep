# HƯỚNG DẪN NHANH: TRIỂN KHAI CHỨC NĂNG TẠO ĐƠN THUỐC

> **Quick Reference Guide for Developers**  
> Tài liệu này cung cấp hướng dẫn nhanh để triển khai và bảo trì chức năng tạo đơn thuốc

## 🎯 Mục đích

Hướng dẫn này giúp developer:
- Hiểu nhanh luồng xử lý tạo đơn thuốc
- Biết cách gọi các API/Controller
- Nắm được cấu trúc database
- Tránh các lỗi phổ biến

---

## 📋 Checklist Triển Khai

### Phase 1: Chuẩn bị
- [ ] Kiểm tra database schema (bảng `donthuoc`, `chitietdonthuoc`)
- [ ] Kiểm tra quyền của user (role: bác sĩ/chuyên gia)
- [ ] Đảm bảo có danh sách thuốc trong hệ thống
- [ ] Cài đặt dependencies cần thiết

### Phase 2: Backend Implementation
- [ ] Tạo/cập nhật Model: `mdonthuoc.php`, `mchitietdonthuoc.php`
- [ ] Tạo/cập nhật Controller: `cdonthuoc.php`, `cchitietdonthuoc.php`
- [ ] Implement validation cho dữ liệu đầu vào
- [ ] Implement transaction để đảm bảo tính toàn vẹn
- [ ] Thêm error handling và logging

### Phase 3: Frontend Implementation
- [ ] Tạo form nhập đơn thuốc
- [ ] Implement autocomplete cho tìm kiếm thuốc
- [ ] Thêm validation phía client
- [ ] Hiển thị danh sách thuốc đã chọn
- [ ] Thêm chức năng sửa/xóa thuốc khỏi danh sách

### Phase 4: Testing
- [ ] Test tạo đơn thuốc thành công
- [ ] Test các trường hợp validation
- [ ] Test rollback khi có lỗi
- [ ] Test cảnh báo dị ứng (nếu có)
- [ ] Test hiệu năng với nhiều thuốc

### Phase 5: Deployment
- [ ] Review code
- [ ] Test trên staging environment
- [ ] Chuẩn bị rollback plan
- [ ] Deploy lên production
- [ ] Monitor logs sau deploy

---

## 🔧 Code Examples

### 1. Tạo đơn thuốc mới (Controller)

```php
// File: Controllers/cdonthuoc.php
public function create_donthuoc(){
    $p = new mDonThuoc();
    $tbl = $p->insert_donthuoc();
    if(!$tbl){
        return -1;  // Lỗi
    }else{
        return $tbl;  // Thành công
    }
}
```

### 2. Lấy đơn thuốc vừa tạo

```php
// File: Controllers/cdonthuoc.php
public function get_donthuoc_new(){
    $p = new mDonThuoc();
    $tbl = $p->select_donthuoc_new();
    $list = array();
    if (!$tbl) {
        return -1;
    } else {
        if ($tbl->num_rows > 0) {
            while($r = $tbl->fetch_assoc()){
                $list[] = $r;
            }
            return $list;
        } else {
            return 0;
        }
    }
}
```

### 3. Thêm chi tiết thuốc vào đơn

```php
// File: Controllers/cchitietdonthuoc.php

/**
 * Tạo chi tiết đơn thuốc - thêm một loại thuốc vào đơn thuốc
 * 
 * @param int $madonthuoc Mã đơn thuốc cần thêm thuốc vào
 * @param int $mathuoc Mã thuốc cần thêm
 * @param string $lieudung Liều dùng của thuốc (VD: "1 viên", "2 viên", "5ml")
 * @param string $thoigianuong Thời gian uống thuốc (VD: "Sáng, Trưa, Tối", "Sau ăn")
 * @param int $songayuong Số ngày uống thuốc (phải > 0)
 * @return int|bool Trả về true nếu thành công, -1 nếu thất bại
 */
public function create_chitietdonthuoc($madonthuoc, $mathuoc, $lieudung, $thoigianuong, $songayuong) {
    $p = new mChiTietDonThuoc();
    $tbl = $p->insert_chitietdonthuoc($madonthuoc, $mathuoc, $lieudung, $thoigianuong, $songayuong);
    if (!$tbl) {
        return -1;
    } else {
        return $tbl;
    }
}
```

### 4. Insert vào Database (Model)

```php
// File: Models/mdonthuoc.php
public function insert_donthuoc(){
    $p = new clsKetNoi();
    $con = $p->moketnoi();
    $con->set_charset('utf8');
    if($con){
        $str = "INSERT INTO donthuoc(ngaytaodonthuoc) VALUES(CURDATE())";
        $tbl = $con->query($str);
        $p->dongketnoi($con);
        return $tbl;
    }else{
        return false; 
    }
}
```

### 5. Xử lý với Transaction (Recommended)

```php
// File: Controllers/xulyhoantatkham.php
try {
    // Bắt đầu transaction
    if ($conn) {
        $conn->begin_transaction();
    }
    
    // 1. Tạo đơn thuốc
    if ($cdonthuoc->create_donthuoc()) {
        $donthuoc_new = $cdonthuoc->get_donthuoc_new();
        $madonthuoc = $donthuoc_new[0]['madonthuoc'];
        
        // 2. Thêm từng thuốc vào đơn
        foreach ($medications as $thuoc) {
            $mathuoc = $thuoc['mathuoc'];
            $lieudung = $thuoc['lieudung'];
            $thoigianuong = $thuoc['thoigianuong'];
            $songayuong = $thuoc['songayuong'];
            
            $cchitietdonthuoc->create_chitietdonthuoc(
                $madonthuoc, $mathuoc, $lieudung, 
                $thoigianuong, $songayuong
            );
        }
        
        // 3. Commit nếu thành công
        if ($conn) {
            $conn->commit();
        }
        echo "Thành công!";
    }
} catch (Exception $e) {
    // Rollback nếu có lỗi
    if ($conn) {
        $conn->rollback();
    }
    echo "Lỗi: " . $e->getMessage();
}
```

---

## 🗄️ Database Schema Reference

### Bảng: donthuoc
```sql
CREATE TABLE `donthuoc` (
  `madonthuoc` INT(11) NOT NULL AUTO_INCREMENT,
  `ngaytaodonthuoc` DATE NOT NULL,
  PRIMARY KEY (`madonthuoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Bảng: chitietdonthuoc
```sql
CREATE TABLE `chitietdonthuoc` (
  `machitietdonthuoc` INT(11) NOT NULL AUTO_INCREMENT,
  `madonthuoc` INT(11) DEFAULT NULL,
  `mathuoc` INT(11) NOT NULL,
  `lieudung` VARCHAR(200) NOT NULL,
  `thoigianuong` VARCHAR(200) NOT NULL,
  `songayuong` INT(11) NOT NULL,
  PRIMARY KEY (`machitietdonthuoc`),
  KEY `fk_madonthuoc` (`madonthuoc`),
  KEY `fk_mathuoc` (`mathuoc`),
  CONSTRAINT `fk_madonthuoc` FOREIGN KEY (`madonthuoc`) 
    REFERENCES `donthuoc` (`madonthuoc`),
  CONSTRAINT `fk_mathuoc` FOREIGN KEY (`mathuoc`) 
    REFERENCES `thuoc` (`mathuoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Indexes Quan Trọng
```sql
-- Index cho tìm kiếm nhanh đơn thuốc theo bệnh nhân
CREATE INDEX idx_benhnhan ON chitiethoso(mabenhnhan);

-- Index cho tìm kiếm nhanh chi tiết đơn thuốc
CREATE INDEX idx_donthuoc ON chitietdonthuoc(madonthuoc);

-- Index cho tìm kiếm thuốc
CREATE INDEX idx_tenthuoc ON thuoc(tenthuoc);
```

---

## ⚡ API Endpoints / Controller Methods

### Controller: cdonthuoc.php

| Method | Parameters | Return | Description |
|--------|-----------|--------|-------------|
| `create_donthuoc()` | None | `true/-1` | Tạo đơn thuốc mới |
| `get_donthuoc_new()` | None | `array/0/-1` | Lấy đơn thuốc mới nhất |
| `get_donthuoc_mahoso($mahoso)` | `$mahoso` | `array/0/-1` | Lấy đơn thuốc theo mã hồ sơ |
| `get_donthuoc_mabenhnhan($mabenhnhan)` | `$mabenhnhan` | `array/0/-1` | Lấy đơn thuốc theo mã bệnh nhân |

### Controller: cchitietdonthuoc.php

| Method | Parameters | Return | Description |
|--------|-----------|--------|-------------|
| `create_chitietdonthuoc()` | `$madonthuoc, $mathuoc, $lieudung, $thoigianuong, $songayuong` | `true/-1` | Thêm thuốc vào đơn |
| `get_chitietdonthuoc_madonthuoc($madonthuoc)` | `$madonthuoc` | `array/0/-1` | Lấy chi tiết đơn thuốc |

---

## ✅ Validation Rules

### Client-side Validation (JavaScript)
```javascript
function validatePrescription() {
    // 1. Kiểm tra có ít nhất 1 thuốc
    if (medications.length === 0) {
        alert("Vui lòng thêm ít nhất một loại thuốc");
        return false;
    }
    
    // 2. Kiểm tra từng thuốc
    for (let med of medications) {
        // Kiểm tra liều dùng
        if (!med.lieudung || med.lieudung.trim() === "") {
            alert("Vui lòng nhập liều dùng");
            return false;
        }
        
        // Kiểm tra thời gian uống
        if (!med.thoigianuong || med.thoigianuong.trim() === "") {
            alert("Vui lòng nhập thời gian uống");
            return false;
        }
        
        // Kiểm tra số ngày uống
        if (!med.songayuong || med.songayuong <= 0) {
            alert("Số ngày uống phải lớn hơn 0");
            return false;
        }
    }
    
    return true;
}
```

### Server-side Validation (PHP)
```php
function validateMedication($mathuoc, $lieudung, $thoigianuong, $songayuong) {
    $errors = [];
    
    // Kiểm tra mã thuốc
    if (empty($mathuoc) || !is_numeric($mathuoc)) {
        $errors[] = "Mã thuốc không hợp lệ";
    }
    
    // Kiểm tra liều dùng
    if (empty($lieudung) || trim($lieudung) === "") {
        $errors[] = "Liều dùng không được rỗng";
    }
    
    // Kiểm tra thời gian uống
    if (empty($thoigianuong) || trim($thoigianuong) === "") {
        $errors[] = "Thời gian uống không được rỗng";
    }
    
    // Kiểm tra số ngày uống
    if (!is_numeric($songayuong) || $songayuong <= 0) {
        $errors[] = "Số ngày uống phải là số nguyên dương";
    }
    
    return $errors;
}
```

---

## ⚠️ Common Errors & Solutions

### Error 1: Transaction không commit
**Triệu chứng**: Đơn thuốc không được lưu vào database  
**Nguyên nhân**: Quên gọi `commit()` hoặc có exception  
**Giải pháp**: 
```php
try {
    $conn->begin_transaction();
    // ... code ...
    $conn->commit();  // Đừng quên!
} catch (Exception $e) {
    $conn->rollback();
}
```

### Error 2: Foreign key constraint fails
**Triệu chứng**: Lỗi khi insert vào `chitietdonthuoc`  
**Nguyên nhân**: `madonthuoc` hoặc `mathuoc` không tồn tại  
**Giải pháp**: Kiểm tra đơn thuốc đã được tạo chưa trước khi thêm chi tiết

### Error 3: Không lấy được đơn thuốc mới
**Triệu chứng**: `get_donthuoc_new()` trả về rỗng  
**Nguyên nhân**: Đơn thuốc chưa được commit  
**Giải pháp**: Đảm bảo commit transaction trước khi gọi

### Error 4: SQL Injection
**Triệu chứng**: Bảo mật kém, có thể bị tấn công  
**Nguyên nhân**: Không dùng prepared statements  
**Giải pháp**: 
```php
// ❌ Không an toàn - dễ bị SQL Injection
$str = "INSERT INTO donthuoc(ngaytaodonthuoc) VALUES('$ngay')";

// ✅ An toàn - sử dụng prepared statements
$stmt = $con->prepare("INSERT INTO chitietdonthuoc 
    (madonthuoc, mathuoc, lieudung, thoigianuong, songayuong) 
    VALUES(?, ?, ?, ?, ?)");
$stmt->bind_param("iissi", $madonthuoc, $mathuoc, $lieudung, $thoigianuong, $songayuong);
$stmt->execute();
```

---

## 🧪 Testing Guide

### Unit Test Example
```php
// Test tạo đơn thuốc
public function testCreatePrescription() {
    $controller = new cDonThuoc();
    $result = $controller->create_donthuoc();
    $this->assertNotEquals(-1, $result);
}

// Test validation
public function testValidation() {
    $errors = validateMedication(null, "", "", -1);
    $this->assertNotEmpty($errors);
}
```

### Manual Test Cases

| Test Case | Input | Expected Output |
|-----------|-------|-----------------|
| TC-1 | Tạo đơn với 1 thuốc hợp lệ | Thành công |
| TC-2 | Tạo đơn không có thuốc | Lỗi validation |
| TC-3 | Tạo đơn với số ngày âm | Lỗi validation |
| TC-4 | Tạo đơn với thuốc trùng | Lỗi/Cảnh báo |
| TC-5 | Tạo đơn với thuốc gây dị ứng | Hiển thị cảnh báo |

---

## 📊 Performance Tips

1. **Use Indexes**: Đảm bảo có index trên các cột tìm kiếm thường xuyên
2. **Batch Insert**: Nếu có nhiều thuốc, cân nhắc dùng batch insert
3. **Cache**: Cache danh sách thuốc để giảm query
4. **Pagination**: Phân trang khi hiển thị lịch sử đơn thuốc
5. **Connection Pool**: Sử dụng connection pooling cho database

---

## 🔐 Security Checklist

- [ ] Validate tất cả input từ client
- [ ] Sử dụng prepared statements
- [ ] Kiểm tra quyền truy cập (role-based)
- [ ] Log tất cả các thao tác (audit trail)
- [ ] Mã hóa dữ liệu nhạy cảm
- [ ] Sử dụng HTTPS
- [ ] Implement rate limiting
- [ ] Sanitize output để tránh XSS

---

## 📚 Tài liệu liên quan

- [UseCase_TaoDonThuoc.md](UseCase_TaoDonThuoc.md) - Đặc tả đầy đủ
- [UseCaseDiagram_TaoDonThuoc.md](UseCaseDiagram_TaoDonThuoc.md) - Sơ đồ use case
- [UseCase_TaoDonThuoc_Summary.md](UseCase_TaoDonThuoc_Summary.md) - Tóm tắt

---

## 💬 Support & Contact

Nếu gặp vấn đề khi triển khai:
1. Kiểm tra lại tài liệu đặc tả
2. Xem lại code examples
3. Check common errors section
4. Liên hệ team lead

---

**Version:** 1.0  
**Last Updated:** 04/12/2024  
**Maintainer:** Development Team
