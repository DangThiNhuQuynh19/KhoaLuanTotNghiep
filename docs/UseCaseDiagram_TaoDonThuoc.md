# SƠ ĐỒ USE CASE: TẠO ĐƠN THUỐC

## 1. Sơ đồ tổng quan

```
┌─────────────────────────────────────────────────────────────────────┐
│                   HỆ THỐNG QUẢN LÝ BỆNH VIỆN                       │
│                                                                     │
│                                                                     │
│        ┌──────────────┐                                            │
│        │   Bác sĩ     │                                            │
│        └──────┬───────┘                                            │
│               │                                                     │
│               │                                                     │
│               │          ┌─────────────────────┐                   │
│               ├─────────>│   Tạo đơn thuốc    │                   │
│               │          └──────────┬──────────┘                   │
│               │                     │                               │
│               │                     │ <<include>>                   │
│               │                     ▼                               │
│               │          ┌─────────────────────┐                   │
│               │          │   Tìm kiếm thuốc   │                   │
│               │          └─────────────────────┘                   │
│               │                                                     │
│               │                     │ <<include>>                   │
│               │                     ▼                               │
│               │          ┌─────────────────────┐                   │
│               │          │ Thêm chi tiết thuốc│                   │
│               │          └─────────────────────┘                   │
│               │                                                     │
│               │                     │ <<extend>>                    │
│        ┌──────┴───────┐            ▼                               │
│        │  Chuyên gia  │ ┌─────────────────────┐                   │
│        └──────────────┘ │ Xem lịch sử đơn    │                   │
│                         │      thuốc          │                   │
│                         └─────────────────────┘                   │
│                                                                     │
│                                  │ <<extend>>                       │
│                                  ▼                                  │
│                         ┌─────────────────────┐                   │
│                         │  Cảnh báo dị ứng   │                   │
│                         └─────────────────────┘                   │
│                                                                     │
│                                                                     │
│                         ┌─────────────────────┐                   │
│                         │   Database System   │                   │
│                         │   (Actor phụ)       │                   │
│                         └─────────────────────┘                   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## 2. Mô tả các thành phần

### 2.1. Actors (Tác nhân)

#### Actor chính:
1. **Bác sĩ** (Doctor)
   - Vai trò: Người khám bệnh và kê đơn thuốc cho bệnh nhân
   - Quyền hạn: Tạo, xem đơn thuốc cho bệnh nhân trong ca khám của mình

2. **Chuyên gia** (Specialist)
   - Vai trò: Người có chuyên môn cao, tư vấn và kê đơn thuốc chuyên sâu
   - Quyền hạn: Tương tự Bác sĩ, nhưng có thể kê đơn phức tạp hơn

#### Actor phụ:
3. **Database System**
   - Vai trò: Lưu trữ và quản lý dữ liệu đơn thuốc
   - Tương tác: Tự động khi hệ thống lưu/đọc dữ liệu

### 2.2. Use Cases (Ca sử dụng)

#### UC chính:
1. **Tạo đơn thuốc** (Create Prescription)
   - Mã: UC-DT-001
   - Mô tả: Quy trình chính để tạo đơn thuốc mới cho bệnh nhân
   - Kích hoạt: Bác sĩ/Chuyên gia chọn chức năng "Tạo đơn thuốc"

#### UC phụ (include):
2. **Tìm kiếm thuốc** (Search Medication)
   - Mô tả: Tìm thuốc trong danh sách thuốc của hệ thống
   - Quan hệ: <<include>> - Luôn được thực hiện khi tạo đơn thuốc

3. **Thêm chi tiết thuốc** (Add Medication Details)
   - Mô tả: Nhập liều dùng, thời gian uống, số ngày uống cho từng loại thuốc
   - Quan hệ: <<include>> - Bắt buộc cho mỗi thuốc được thêm vào đơn

#### UC mở rộng (extend):
4. **Xem lịch sử đơn thuốc** (View Prescription History)
   - Mô tả: Xem các đơn thuốc trước đó của bệnh nhân
   - Quan hệ: <<extend>> - Tùy chọn, khi bác sĩ cần tham khảo

5. **Cảnh báo dị ứng** (Allergy Warning)
   - Mô tả: Hiển thị cảnh báo nếu bệnh nhân có dị ứng với thuốc được chọn
   - Quan hệ: <<extend>> - Chỉ xảy ra khi có dị ứng

## 3. Quan hệ giữa các Use Case

### 3.1. Quan hệ Include (<<include>>)
Quan hệ bắt buộc - Use case này luôn cần use case khác

```
Tạo đơn thuốc ──<<include>>──> Tìm kiếm thuốc
Tạo đơn thuốc ──<<include>>──> Thêm chi tiết thuốc
```

**Giải thích:**
- Khi tạo đơn thuốc, bắt buộc phải tìm kiếm thuốc
- Sau khi tìm được thuốc, bắt buộc phải nhập chi tiết (liều dùng, cách dùng...)

### 3.2. Quan hệ Extend (<<extend>>)
Quan hệ mở rộng - Use case này có thể xảy ra hoặc không

```
Xem lịch sử đơn thuốc ──<<extend>>──> Tạo đơn thuốc
Cảnh báo dị ứng ──<<extend>>──> Tạo đơn thuốc
```

**Giải thích:**
- Bác sĩ có thể xem lịch sử đơn thuốc cũ (tùy chọn)
- Cảnh báo dị ứng chỉ xuất hiện khi có thuốc gây dị ứng

## 4. Luồng tương tác chính

```
┌──────────┐                                        ┌──────────────┐
│  Bác sĩ  │                                        │  Hệ thống    │
└────┬─────┘                                        └──────┬───────┘
     │                                                     │
     │  1. Chọn "Tạo đơn thuốc"                          │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │  2. Hiển thị form tạo đơn                         │
     │  <────────────────────────────────────────────────│
     │                                                     │
     │  3. Tìm kiếm thuốc (nhập tên thuốc)               │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │  4. Hiển thị danh sách thuốc                      │
     │  <────────────────────────────────────────────────│
     │                                                     │
     │  5. Chọn thuốc                                     │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │  6. Hiển thị form nhập chi tiết                   │
     │  <────────────────────────────────────────────────│
     │                                                     │
     │  7. Nhập liều dùng, thời gian, số ngày            │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │  8. Thêm thuốc vào danh sách                      │
     │  <────────────────────────────────────────────────│
     │                                                     │
     │  9. Lặp lại bước 3-8 (nếu cần thêm thuốc)        │
     │  ╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎╎ │
     │                                                     │
     │  10. Nhấn "Lưu đơn thuốc"                         │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │                          11. Kiểm tra hợp lệ       │
     │                          ──────────────────>       │
     │                                   │                 │
     │                          12. Lưu vào DB            │
     │                          ──────────────────>       │
     │                                   │                 │
     │  13. Hiển thị thông báo thành công               │
     │  <────────────────────────────────────────────────│
     │                                                     │
```

## 5. Các trường hợp đặc biệt

### 5.1. Trường hợp có cảnh báo dị ứng

```
┌──────────┐                                        ┌──────────────┐
│  Bác sĩ  │                                        │  Hệ thống    │
└────┬─────┘                                        └──────┬───────┘
     │                                                     │
     │  Chọn thuốc                                        │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │                              Kiểm tra dị ứng       │
     │                              ──────────────>       │
     │                                   │                 │
     │  ⚠️ CẢNH BÁO: Bệnh nhân dị ứng                   │
     │  <────────────────────────────────────────────────│
     │                                                     │
     │  Xác nhận hoặc chọn thuốc khác                    │
     │────────────────────────────────────────────────>  │
     │                                                     │
```

### 5.2. Trường hợp xem lịch sử

```
┌──────────┐                                        ┌──────────────┐
│  Bác sĩ  │                                        │  Hệ thống    │
└────┬─────┘                                        └──────┬───────┘
     │                                                     │
     │  Nhấn "Xem lịch sử đơn thuốc"                     │
     │────────────────────────────────────────────────>  │
     │                                                     │
     │                              Lấy đơn thuốc cũ     │
     │                              ──────────────>       │
     │                                   │                 │
     │  Hiển thị danh sách đơn thuốc cũ                 │
     │  <────────────────────────────────────────────────│
     │                                                     │
     │  Sao chép hoặc đóng                               │
     │────────────────────────────────────────────────>  │
     │                                                     │
```

## 6. Ràng buộc và quy tắc

### 6.1. Ràng buộc thời gian
- Thời gian tìm kiếm thuốc: < 2 giây
- Thời gian lưu đơn thuốc: < 3 giây

### 6.2. Ràng buộc dữ liệu
- Mỗi đơn thuốc phải có ít nhất 1 loại thuốc
- Liều dùng không được rỗng
- Số ngày uống phải > 0
- Không được kê trùng thuốc trong cùng đơn

### 6.3. Ràng buộc bảo mật
- Chỉ Bác sĩ/Chuyên gia đã đăng nhập mới có quyền
- Mỗi đơn thuốc phải ghi nhận người tạo
- Không cho phép xóa đơn thuốc đã tạo

## 7. Tích hợp với các Use Case khác

```
┌──────────────────────┐
│   Khám bệnh          │
│   (UC-KB-001)        │
└─────────┬────────────┘
          │
          │ sau khi khám xong
          ▼
┌──────────────────────┐
│   Tạo đơn thuốc     │────────> Liên kết với hồ sơ bệnh án
│   (UC-DT-001)       │          (UC-HS-001)
└─────────┬────────────┘
          │
          │ nếu cần
          ▼
┌──────────────────────┐
│   Đặt lịch xét      │
│   nghiệm             │
│   (UC-XN-001)        │
└──────────────────────┘
```

## 8. Ghi chú

- Sơ đồ này tuân thủ chuẩn UML 2.0
- Sử dụng ký hiệu ASCII art để dễ đọc trên mọi nền tảng
- Các mối quan hệ <<include>> và <<extend>> được thể hiện rõ ràng
- Có thể chuyển đổi sang diagram công cụ như PlantUML, Draw.io nếu cần

---

**Lần cập nhật cuối:** 04/12/2024  
**Phiên bản:** 1.0
