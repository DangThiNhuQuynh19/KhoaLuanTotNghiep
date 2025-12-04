# TÀI LIỆU DỰ ÁN - HỆ THỐNG QUẢN LÝ BỆNH VIỆN HẠNH PHÚC

## Giới thiệu

Thư mục này chứa tất cả các tài liệu kỹ thuật, đặc tả yêu cầu, và thiết kế của Hệ thống Quản lý Bệnh viện Hạnh Phúc.

## Cấu trúc thư mục

```
docs/
├── README.md                        # File này - hướng dẫn về tài liệu
├── UseCase_TaoDonThuoc.md          # Đặc tả use case: Tạo đơn thuốc
├── UseCaseDiagram_TaoDonThuoc.md   # Sơ đồ use case: Tạo đơn thuốc
└── (các tài liệu khác sẽ được bổ sung)
```

## Danh sách tài liệu

### 1. Đặc tả Use Case

#### UseCase_TaoDonThuoc.md
**Mã:** UC-DT-001  
**Tên:** Tạo Đơn Thuốc (Create Prescription)  
**Mô tả:** Đặc tả chi tiết quy trình tạo đơn thuốc cho bệnh nhân sau khi khám bệnh  
**Trạng thái:** ✅ Hoàn thành  
**Phiên bản:** 1.0  
**Ngày cập nhật:** 04/12/2024

### 2. Sơ đồ Use Case

#### UseCaseDiagram_TaoDonThuoc.md
**Mã:** UC-DT-001 (Diagram)  
**Tên:** Sơ đồ Use Case - Tạo Đơn Thuốc  
**Mô tả:** Sơ đồ minh họa các actor, use case và mối quan hệ trong quy trình tạo đơn thuốc  
**Trạng thái:** ✅ Hoàn thành  
**Phiên bản:** 1.0  
**Ngày cập nhật:** 04/12/2024

## Quy ước viết tài liệu

### 1. Định dạng file
- Tất cả tài liệu sử dụng định dạng Markdown (.md)
- Encoding: UTF-8
- Line ending: LF (Unix)

### 2. Cấu trúc đặc tả Use Case
Mỗi tài liệu đặc tả use case cần bao gồm các phần sau:

1. **Thông tin chung**
   - Mã use case
   - Tên use case
   - Mô tả ngắn
   - Mức độ ưu tiên
   - Phạm vi

2. **Các Actor (Tác nhân)**
   - Actor chính
   - Actor phụ

3. **Điều kiện tiên quyết (Preconditions)**

4. **Điều kiện hậu quyết (Postconditions)**

5. **Luồng sự kiện chính (Main Flow)**

6. **Luồng sự kiện thay thế (Alternative Flows)**

7. **Yêu cầu đặc biệt (Special Requirements)**

8. **Các quy tắc nghiệp vụ (Business Rules)**

9. **Tham chiếu**
   - Các bảng database liên quan
   - Các file code liên quan
   - Use case liên quan

10. **Phụ lục**
    - Lịch sử thay đổi
    - Thuật ngữ
    - Ghi chú bổ sung

### 3. Quy tắc đặt tên
- **Use Case:** `UseCase_<TenChucNang>.md`
- **Thiết kế:** `Design_<TenModule>.md`
- **API:** `API_<TenService>.md`

### 4. Mã hóa Use Case
- Tạo đơn thuốc: UC-DT-001
- Khám bệnh: UC-KB-xxx
- Hồ sơ bệnh án: UC-HS-xxx
- Bệnh nhân: UC-BN-xxx
- Lịch hẹn: UC-LH-xxx
- Xét nghiệm: UC-XN-xxx

## Cách đóng góp tài liệu

### Thêm tài liệu mới
1. Tạo file mới trong thư mục `docs/`
2. Tuân thủ quy ước đặt tên và cấu trúc
3. Cập nhật file README.md này (thêm vào danh sách tài liệu)
4. Commit với message rõ ràng

### Cập nhật tài liệu
1. Cập nhật nội dung file
2. Cập nhật phiên bản và ngày trong mục "Lịch sử thay đổi"
3. Cập nhật thông tin trong README.md (nếu cần)
4. Commit với message mô tả thay đổi

## Liên hệ

Nếu có câu hỏi hoặc cần hỗ trợ về tài liệu, vui lòng liên hệ:
- Team phát triển: [Thông tin liên hệ]
- Project Owner: [Thông tin liên hệ]

## Giấy phép

Tài liệu này là tài sản của dự án Khóa Luận Tốt Nghiệp - Hệ thống Quản lý Bệnh viện Hạnh Phúc.

---

**Lần cập nhật cuối:** 04/12/2024  
**Phiên bản:** 1.0
