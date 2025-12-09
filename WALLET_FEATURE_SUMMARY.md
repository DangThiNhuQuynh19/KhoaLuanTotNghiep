# Tổng quan tính năng Ví Hạnh Phúc

## Mô tả chức năng
Chức năng "Ví Hạnh Phúc" cho phép bệnh nhân xem số dư ví điện tử của mình trong hệ thống bệnh viện.

## Luồng sử dụng

### 1. Truy cập tính năng
- Bệnh nhân đăng nhập vào hệ thống
- Click vào icon/tên người dùng ở góc phải header
- Trong menu dropdown, chọn "Ví Hạnh Phúc" (có icon ví)

### 2. Thông tin hiển thị
Trang ví hiển thị các thông tin sau:

#### Header ví (Nền gradient tím đẹp mắt)
- Tiêu đề: "Ví Hạnh Phúc" với icon ví
- **Số dư hiện tại**: Hiển thị rõ ràng với định dạng tiền tệ VND
  - Font size lớn, dễ đọc
  - Định dạng: 1.000.000 đ (có dấu phân cách hàng nghìn)

#### Thông tin chi tiết
1. **Tài khoản**: Hiển thị email/username của người dùng
2. **Trạng thái**: "Hoạt động" (Active status)
3. **Phương thức thanh toán**: "Ví điện tử"

#### Ghi chú
- Thông báo: "Số dư ví được sử dụng để thanh toán các dịch vụ khám chữa bệnh tại Bệnh viện Hạnh Phúc"

#### Nút điều khiển
- Nút "Quay lại": Về trang chủ

## Thiết kế giao diện

### Màu sắc
- **Background tổng thể**: Gradient tím (từ #667eea đến #764ba2)
- **Card ví**: Nền trắng với bo góc 20px
- **Header card**: Gradient tím
- **Text chính**: Màu trắng trên header, màu đen trên body
- **Accent color**: Tím (#667eea) cho icons

### Layout
- Container tối đa 600px chiều rộng
- Căn giữa màn hình
- Responsive cho mobile
- Shadow đẹp (0 20px 60px rgba(0,0,0,0.3))

### Icons sử dụng
- 💼 Wallet icon (fas fa-wallet)
- 👤 User icon (fas fa-user-circle)
- 🛡️ Shield icon (fas fa-shield-alt)
- 💳 Credit card icon (fas fa-credit-card)
- ⬅️ Back arrow (fas fa-arrow-left)
- ℹ️ Info icon (fas fa-info-circle)

## Tính năng kỹ thuật

### Bảo mật
- ✅ Kiểm tra session người dùng trước khi hiển thị
- ✅ Redirect về trang đăng nhập nếu chưa login
- ✅ Sử dụng htmlspecialchars() để escape output
- ✅ Prepared statements cho SQL queries
- ✅ Charset utf8mb4 cho full Unicode support

### Performance
- ✅ Sử dụng kết nối database có sẵn (không tạo mới)
- ✅ Query đơn giản, nhanh
- ✅ Minimal JavaScript

### Code Quality
- ✅ Absolute include paths
- ✅ Consistent session checking
- ✅ Modern JavaScript (addEventListener thay vì onclick)
- ✅ Clean, maintainable code

## Database Schema

```sql
-- Bảng: taikhoan
ALTER TABLE `taikhoan` 
ADD COLUMN `vitien` DECIMAL(15,2) NOT NULL DEFAULT 0.00 
COMMENT 'Số dư ví của người dùng';
```

### Kiểu dữ liệu
- **DECIMAL(15,2)**: 
  - 15 digits tổng cộng
  - 2 chữ số thập phân
  - Cho phép số tiền lên đến 9,999,999,999,999.99 VND
  - Mặc định: 0.00

## Ví dụ sử dụng

### Cập nhật số dư ví (SQL)
```sql
-- Cập nhật số dư cho tài khoản
UPDATE taikhoan 
SET vitien = 1000000.00 
WHERE tentk = 'patient@example.com';

-- Nạp thêm tiền vào ví
UPDATE taikhoan 
SET vitien = vitien + 500000.00 
WHERE tentk = 'patient@example.com';

-- Trừ tiền khi thanh toán (đã có trong code)
UPDATE taikhoan 
SET vitien = vitien - 150000.00 
WHERE tentk = 'patient@example.com';
```

## Tích hợp với hệ thống hiện tại

### Thanh toán
Tính năng đã tích hợp với:
- `/Views/benhnhan/pages/thanhtoan/index.php`: Thanh toán bằng ví
- Phương thức `update_vitien_id()` trong model để trừ tiền

### Navigation
- Menu dropdown của bệnh nhân trong header
- URL: `?action=vi`
- Auto-routing qua index.php

## Tương lai có thể mở rộng

1. **Lịch sử giao dịch**
   - Xem các lần nạp/rút tiền
   - Lịch sử thanh toán
   - Export báo cáo

2. **Nạp tiền**
   - Tích hợp VNPay
   - Tích hợp MoMo
   - Chuyển khoản ngân hàng

3. **Thông báo**
   - Thông báo khi số dư thấp
   - Thông báo giao dịch thành công
   - Email xác nhận

4. **Ưu đãi**
   - Tích điểm
   - Giảm giá cho ví
   - Cashback

## Lưu ý quan trọng

⚠️ **Trước khi sử dụng:**
1. Phải chạy migration SQL để thêm cột `vitien`
2. Set số dư ban đầu cho các tài khoản cũ (nếu cần)
3. Test kỹ chức năng trên môi trường dev trước

✅ **Đã kiểm tra:**
- Syntax PHP không lỗi
- Code review đã pass
- Best practices đã áp dụng
- Security đã được xem xét

📝 **Documentation:**
- Có file WALLET_FEATURE.md chi tiết
- Code có comment rõ ràng
- Migration file có sẵn
