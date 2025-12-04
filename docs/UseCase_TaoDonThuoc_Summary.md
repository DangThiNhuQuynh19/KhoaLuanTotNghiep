# TÓM TẮT ĐẶC TẢ USE CASE: TẠO ĐƠN THUỐC

## Thông tin cơ bản

| Thuộc tính | Giá trị |
|------------|---------|
| **Mã Use Case** | UC-DT-001 |
| **Tên Use Case** | Tạo Đơn Thuốc (Create Prescription) |
| **Mức độ ưu tiên** | Cao |
| **Trạng thái** | Đã hoàn thành đặc tả |
| **Ngày tạo** | 04/12/2024 |
| **Phiên bản** | 1.0 |

## Mô tả ngắn

Use case mô tả quy trình tạo đơn thuốc cho bệnh nhân sau khi bác sĩ hoặc chuyên gia khám bệnh và chẩn đoán. Đơn thuốc bao gồm thông tin về các loại thuốc được kê, liều dùng, thời gian uống và số ngày uống.

## Các Actor

### Actor Chính
- **Bác sĩ**: Người thực hiện khám bệnh và kê đơn thuốc
- **Chuyên gia**: Người có chuyên môn cao, tư vấn và kê đơn chuyên sâu

### Actor Phụ  
- **Hệ thống Database**: Lưu trữ và quản lý dữ liệu đơn thuốc

## Các bước chính trong luồng sự kiện

1. **Khởi tạo**: Bác sĩ đăng nhập và truy cập chức năng tạo đơn thuốc
2. **Truy cập form**: Hệ thống hiển thị form với thông tin bệnh nhân
3. **Thêm thuốc**: Bác sĩ tìm kiếm và thêm thuốc vào đơn với chi tiết:
   - Liều dùng
   - Thời gian uống
   - Số ngày uống
4. **Xem lại**: Bác sĩ kiểm tra và chỉnh sửa danh sách thuốc
5. **Lưu đơn**: Hệ thống kiểm tra và lưu đơn thuốc vào database
6. **Xác nhận**: Hiển thị thông báo thành công với mã đơn thuốc

## Các luồng thay thế quan trọng

| Mã | Tên luồng | Mô tả |
|----|-----------|-------|
| AF-1 | Hủy tạo đơn thuốc | Bác sĩ hủy bỏ quá trình tạo đơn |
| AF-2 | Không có thuốc | Cảnh báo khi lưu đơn rỗng |
| AF-3 | Thông tin không hợp lệ | Kiểm tra và thông báo lỗi nhập liệu |
| AF-4 | Lỗi database | Xử lý lỗi kết nối và rollback |
| AF-5 | Xem lịch sử đơn thuốc | Tham khảo đơn thuốc cũ |
| AF-6 | Cảnh báo dị ứng | Cảnh báo khi thuốc gây dị ứng |

## Điều kiện tiên quyết

✓ Bác sĩ/Chuyên gia đã đăng nhập  
✓ Bệnh nhân có hồ sơ bệnh án  
✓ Đã có phiếu khám bệnh  
✓ Có quyền truy cập chức năng  
✓ Danh sách thuốc đã cập nhật

## Điều kiện hậu quyết (Khi thành công)

✓ Đơn thuốc mới được tạo với mã duy nhất  
✓ Chi tiết thuốc được lưu vào database  
✓ Đơn thuốc liên kết với hồ sơ bệnh án  
✓ Ngày tạo được ghi nhận tự động  
✓ Thông báo xác nhận hiển thị  
✓ Bệnh nhân có thể xem đơn thuốc

## Yêu cầu kỹ thuật

### Hiệu năng
- Thời gian tìm kiếm thuốc: < 2 giây
- Thời gian lưu đơn thuốc: < 3 giây
- Hỗ trợ 50+ bác sĩ tạo đơn đồng thời

### Bảo mật
- Chỉ người đã đăng nhập mới truy cập được
- Ghi nhận đầy đủ audit trail
- Mã hóa dữ liệu qua HTTPS
- Không cho phép xóa đơn thuốc đã tạo

### Khả năng sử dụng
- Giao diện thân thiện, dễ sử dụng
- Autocomplete khi tìm kiếm
- Hiển thị rõ trường bắt buộc
- Cảnh báo lỗi rõ ràng
- Hỗ trợ phím tắt

## Quy tắc nghiệp vụ chính

### BR-1: Quy tắc quyền hạn
- Chỉ Bác sĩ và Chuyên gia mới tạo được đơn thuốc
- Mỗi đơn thuốc phải gắn với bác sĩ kê đơn
- Bác sĩ chỉ kê đơn cho bệnh nhân trong ca khám của mình

### BR-2: Quy tắc nội dung đơn thuốc
- Đơn thuốc phải có ít nhất 1 loại thuốc
- Mỗi thuốc phải có đầy đủ: liều dùng, thời gian uống, số ngày uống
- Số ngày uống phải > 0
- Không được kê trùng thuốc

### BR-3: Quy tắc ngày tạo đơn
- Ngày tạo tự động là ngày hiện tại
- Không cho phép ngày quá khứ/tương lai

### BR-4: Quy tắc toàn vẹn dữ liệu
- Sử dụng transaction
- Rollback nếu có lỗi

### BR-5: Quy tắc lịch sử
- Đơn thuốc được lưu vĩnh viễn
- Không cho phép xóa
- Sửa đổi bằng cách tạo đơn mới

## Cấu trúc Database

### Bảng: donthuoc
```sql
madonthuoc (INT, PK, AUTO_INCREMENT)
ngaytaodonthuoc (DATE)
```

### Bảng: chitietdonthuoc
```sql
machitietdonthuoc (INT, PK, AUTO_INCREMENT)
madonthuoc (INT, FK)
mathuoc (INT, FK)
lieudung (VARCHAR(200))
thoigianuong (VARCHAR(200))
songayuong (INT)
```

## Files liên quan

### Controllers
- `Controllers/cdonthuoc.php`
- `Controllers/cchitietdonthuoc.php`
- `Controllers/xulyhoantatkham.php`

### Models
- `Models/mdonthuoc.php`
- `Models/mchitietdonthuoc.php`

### Views
- `Views/bacsi/pages/taodonthuoc/index.php`
- `Views/chuyengia/pages/taohoso/index.php`
- `Views/bacsi/pages/chitiethoso/index.php`

## Use Cases liên quan

- **UC-KB-001**: Khám bệnh
- **UC-HS-001**: Quản lý hồ sơ bệnh án
- **UC-BN-001**: Xem thông tin bệnh nhân
- **UC-XN-001**: Đặt lịch xét nghiệm

## Tài liệu chi tiết

Để xem đặc tả đầy đủ, vui lòng tham khảo:
- **[UseCase_TaoDonThuoc.md](UseCase_TaoDonThuoc.md)** - Đặc tả chi tiết
- **[UseCaseDiagram_TaoDonThuoc.md](UseCaseDiagram_TaoDonThuoc.md)** - Sơ đồ use case

## Lưu ý triển khai

⚠️ **Quan trọng**:
1. Luôn sử dụng transaction khi lưu đơn thuốc
2. Kiểm tra kỹ quyền hạn trước khi cho phép tạo đơn
3. Validate đầy đủ dữ liệu phía server
4. Ghi log đầy đủ cho mục đích audit
5. Xử lý cẩn thận trường hợp lỗi database
6. Kiểm tra dị ứng thuốc trước khi lưu

## Gợi ý mở rộng tương lai

💡 **Tính năng có thể thêm**:
- In đơn thuốc với QR code
- Gửi đơn thuốc qua email/SMS
- Kiểm tra tương tác thuốc
- Đề xuất thuốc dựa trên AI
- Tích hợp với hệ thống nhà thuốc
- Thống kê đơn thuốc theo bác sĩ/khoa
- Cảnh báo thuốc sắp hết hạn

---

**Người lập:** Copilot Agent  
**Ngày lập:** 04/12/2024  
**Phiên bản:** 1.0  
**Trạng thái:** ✅ Đã hoàn thành
