# Tài Liệu Sơ Đồ Domain - Bệnh Viện Hạnh Phúc

## Giới Thiệu

Thư mục này chứa các sơ đồ domain (domain diagrams) mô tả kiến trúc và mô hình dữ liệu của Hệ Thống Quản Lý Bệnh Viện Hạnh Phúc.

## Danh Sách Tài Liệu

### 1. [Domain Diagram - Sơ Đồ Chi Tiết](./domain-diagram.md)
Sơ đồ ER (Entity-Relationship) chi tiết mô tả:
- Tất cả các thực thể (entities) trong hệ thống
- Các thuộc tính (attributes) của từng thực thể
- Mối quan hệ (relationships) giữa các thực thể
- Quy tắc nghiệp vụ (business rules)
- Luồng hoạt động chính

### 2. [Domain Overview - Tổng Quan Domain](./domain-overview.md)
Sơ đồ tổng quan cấp cao bao gồm:
- Sơ đồ domain tổng quan
- Các luồng hoạt động (sequence diagrams)
- Sơ đồ context
- Các thành phần chính của hệ thống
- Thuật ngữ domain (ubiquitous language)

## Cách Xem Sơ Đồ

### Trên GitHub
GitHub tự động render các sơ đồ Mermaid. Chỉ cần mở file `.md` trực tiếp trên GitHub.

### Trên VS Code
1. Cài đặt extension: **Markdown Preview Mermaid Support**
2. Mở file `.md`
3. Nhấn `Ctrl+Shift+V` (Windows/Linux) hoặc `Cmd+Shift+V` (Mac) để xem preview

### Trên Mermaid Live Editor
1. Truy cập: https://mermaid.live/
2. Copy nội dung trong block `\`\`\`mermaid ... \`\`\``
3. Paste vào editor để xem và chỉnh sửa

### Export Sơ Đồ
Bạn có thể export sơ đồ sang các định dạng khác:
- **PNG/SVG**: Sử dụng Mermaid Live Editor hoặc Mermaid CLI
- **PDF**: Export từ Markdown preview hoặc sử dụng pandoc

## Cấu Trúc Hệ Thống

### Các Module Chính

```
Hệ Thống Bệnh Viện Hạnh Phúc
│
├── Quản Lý Người Dùng
│   ├── Tài khoản & Phân quyền
│   ├── Bệnh nhân
│   ├── Bác sĩ
│   ├── Chuyên gia
│   └── Nhân viên
│
├── Quản Lý Hồ Sơ Y Tế
│   ├── Hồ sơ bệnh án
│   ├── Chi tiết khám bệnh
│   └── Đơn thuốc
│
├── Quản Lý Lịch Hẹn
│   ├── Đặt lịch khám
│   ├── Lịch làm việc
│   └── Khung giờ
│
├── Quản Lý Xét Nghiệm
│   ├── Đặt lịch xét nghiệm
│   ├── Loại xét nghiệm
│   └── Kết quả xét nghiệm
│
└── Quản Lý Chuyên Khoa
    ├── Các chuyên khoa
    └── Dịch vụ khám
```

## Các Thực Thể Chính

| Thực Thể | Mô Tả | File Liên Quan |
|----------|-------|----------------|
| NGUOIDUNG | Thông tin chung người dùng | Models/mnguoidung.php |
| BACSI | Bác sĩ | Models/mbacsi.php |
| BENHNHAN | Bệnh nhân | Models/mbenhnhan.php |
| HOSOBENHAN | Hồ sơ bệnh án | Models/mhosobenhandientu.php |
| PHIEUKHAMBENH | Phiếu khám bệnh | Models/mphieukhambenh.php |
| LICHXETNGHIEM | Lịch xét nghiệm | Models/mlichxetnghiem.php |
| CHUYENKHOA | Chuyên khoa | Models/mchuyenkhoa.php |

## Các Mối Quan Hệ Quan Trọng

### 1. Người Dùng
- Một NGUOIDUNG có thể là BACSI, BENHNHAN, CHUYENGIA hoặc NHANVIEN
- NGUOIDUNG liên kết với TAIKHOAN qua email

### 2. Bệnh Nhân
- Một BENHNHAN có thể có nhiều HOSOBENHAN
- BENHNHAN có thể có người giám hộ (BENHNHAN khác)
- Giới hạn: Một người giám hộ tối đa 4 hồ sơ

### 3. Khám Bệnh
- PHIEUKHAMBENH kết nối BENHNHAN với BACSI/CHUYENGIA
- Phải có KHUNGGIOKHAMBENH và LICHLAMVIEC phù hợp

### 4. Hồ Sơ Y Tế
- HOSOBENHAN → CHITIETHOSO → DONTHUOC → THUOC
- CHITIETHOSO lưu chẩn đoán và kết luận của bác sĩ

### 5. Xét Nghiệm
- LICHXETNGHIEM → LOAIXETNGHIEM → KETQUAXETNGHIEM
- Liên kết với HOSOBENHAN

## Quy Tắc Nghiệp Vụ

1. **Giám hộ**: Chỉ tạo hồ sơ cho trẻ < 18 tuổi hoặc người > 60 tuổi, tối đa 4 hồ sơ/người giám hộ
2. **Đặt lịch**: Chỉ đặt được khi bác sĩ có lịch làm việc
3. **Trạng thái**: Tất cả các thực thể quan trọng đều có trạng thái
4. **Phân quyền**: Mỗi tài khoản có vai trò xác định
5. **Hồ sơ**: Tự động tạo khi bệnh nhân đăng ký lần đầu
6. **Đơn thuốc**: Chỉ kê sau khi có chẩn đoán

## Công Nghệ Sử Dụng

- **Database**: MySQL/MariaDB
- **Backend**: PHP
- **Diagram**: Mermaid
- **Documentation**: Markdown

## Cập Nhật

Tài liệu này được tạo vào: **03/12/2024**

Để cập nhật sơ đồ:
1. Chỉnh sửa file `.md` tương ứng
2. Cập nhật cú pháp Mermaid
3. Kiểm tra preview
4. Commit và push

## Liên Hệ

Nếu có thắc mắc về sơ đồ domain, vui lòng liên hệ nhóm phát triển.

## License

Tài liệu này là tài sản của dự án Khóa Luận Tốt Nghiệp - Bệnh Viện Hạnh Phúc.
