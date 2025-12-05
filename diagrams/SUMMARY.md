# Tóm Tắt: Sơ Đồ Activity Cập Nhật Kết Quả Xét Nghiệm

## 📋 Thông Tin Chung

**Chức năng:** Cập nhật kết quả xét nghiệm của Nhân Viên Xét Nghiệm  
**Người thực hiện:** Nhân Viên Xét Nghiệm (Lab Staff)  
**Hệ thống:** Quản lý Bệnh viện Hạnh Phúc

## 🎯 Mục Đích

Sơ đồ mô tả quy trình hoàn chỉnh từ khi nhân viên xét nghiệm đăng nhập, chọn lịch xét nghiệm, nhập kết quả, cho đến khi hệ thống lưu trữ và cập nhật trạng thái.

## 📊 Quy Trình Tóm Tắt

```
1. Đăng nhập → 2. Xem danh sách lịch → 3. Chọn lịch cần cập nhật 
→ 4. Nhập kết quả xét nghiệm → 5. Validate dữ liệu 
→ 6. Lưu vào database → 7. Cập nhật trạng thái → 8. Hoàn thành
```

## 🔑 Các Bước Chi Tiết

### Bước 1: Xác thực và Truy cập
- Kiểm tra session đăng nhập
- Hiển thị danh sách lịch xét nghiệm với bộ lọc

### Bước 2: Chọn Lịch Xét Nghiệm
- Lọc theo ngày và trạng thái "Đang thực hiện" (status 11)
- Click icon "✏️" để mở form cập nhật

### Bước 3: Nhập Kết Quả
**Thông tin nhập vào:**
- Tên chỉ số xét nghiệm (VD: Glucose, Hemoglobin)
- Giá trị kết quả
- Đơn vị đo (VD: mmol/L, g/dL)
- Khoảng tham chiếu
- Giờ lấy mẫu
- Nhận xét tổng quát

**Tính năng:**
- Có thể thêm nhiều chỉ số bằng nút "➕ Thêm chỉ số"
- Có thể xóa chỉ số bằng nút "🗑️"

### Bước 4: Xử Lý Dữ Liệu
**Validation:**
- Kiểm tra ID lịch xét nghiệm
- Kiểm tra tính đầy đủ của thông tin
- Validate định dạng dữ liệu

**Xử lý Database:**
```sql
-- Xóa kết quả cũ (nếu có)
DELETE FROM ketquaxetnghiem WHERE malichxetnghiem = $malich

-- Thêm kết quả mới
INSERT INTO ketquaxetnghiem VALUES (...)

-- Cập nhật trạng thái
UPDATE lichxetnghiem SET matrangthai = 12 WHERE malichxetnghiem = $malich
```

### Bước 5: Hoàn Thành
- Hiển thị thông báo "✅ Cập nhật kết quả xét nghiệm thành công!"
- Chuyển về trang chủ
- Trạng thái lịch chuyển thành "Đã có kết quả"

## 📁 Files Liên Quan

### Diagram Files
- `activity_capnhat_ketqua_xetnghiem.puml` - Mã nguồn PlantUML
- `activity_capnhat_ketqua_xetnghiem.png` - Hình ảnh PNG (184KB)
- `activity_capnhat_ketqua_xetnghiem.svg` - Hình ảnh SVG vector (46KB)

### Code Implementation
- **View:** `/Views/nhanvienxetnghiem/pages/chinhsua/index.php`
- **Controller:** `/Controllers/cketquaxetnghiem.php`
- **Model:** `/Models/mketquaxetnghiem.php`
- **List View:** `/Views/nhanvienxetnghiem/pages/trangchu/index.php`

## 🔄 Trạng Thái Lịch Xét Nghiệm

| Mã | Tên Trạng Thái | Mô Tả |
|----|----------------|-------|
| 10 | Chờ thanh toán | Chờ bệnh nhân thanh toán |
| 11 | Đang thực hiện | Có thể cập nhật kết quả |
| 12 | Đã có kết quả | Đã hoàn thành, có thể xem |

## 💾 Cấu Trúc Database

### Bảng: ketquaxetnghiem
Lưu trữ các chỉ số xét nghiệm chi tiết:
- maketquaxetnghiem (PK)
- malichxetnghiem (FK)
- tenchisoxetnghiem
- giatriketqua
- donviketqua
- khoangthamchieu
- ngaygiotraketqua
- giolaymau
- nhanxet

### Bảng: lichxetnghiem
Lưu trữ thông tin lịch hẹn xét nghiệm:
- malichxetnghiem (PK)
- mabenhnhan (FK)
- maloaixetnghiem (FK)
- ngayhen
- makhunggio (FK)
- matrangthai (FK)
- mahoso (FK)

## ✅ Điểm Đặc Biệt

1. **Xóa và Tạo Mới:** Mỗi lần cập nhật, hệ thống xóa toàn bộ kết quả cũ và tạo mới để đảm bảo tính nhất quán.

2. **Múi Giờ:** Hệ thống sử dụng múi giờ `Asia/Ho_Chi_Minh`.

3. **Validation Client-side:** JavaScript kiểm tra dữ liệu trước khi submit.

4. **Swimlane:** Sơ đồ sử dụng swimlane để phân biệt rõ các hành động của Nhân Viên và Hệ Thống.

5. **Responsive:** Form nhập liệu responsive, hoạt động tốt trên mobile.

## 🛠️ Cách Sử Dụng Sơ Đồ

### Xem Sơ Đồ
```bash
# Mở file PNG hoặc SVG
open diagrams/activity_capnhat_ketqua_xetnghiem.png
```

### Chỉnh Sửa và Tái Tạo
```bash
# Cài đặt PlantUML (nếu chưa có)
wget https://github.com/plantuml/plantuml/releases/download/v1.2024.3/plantuml-1.2024.3.jar

# Chỉnh sửa file .puml, sau đó tạo lại diagram
java -jar plantuml-1.2024.3.jar -tpng diagrams/activity_capnhat_ketqua_xetnghiem.puml
java -jar plantuml-1.2024.3.jar -tsvg diagrams/activity_capnhat_ketqua_xetnghiem.puml
```

## 📝 Ghi Chú

- Sơ đồ được tạo bằng PlantUML (ngôn ngữ mô tả UML dạng văn bản)
- Hỗ trợ xuất ra nhiều định dạng: PNG, SVG, PDF, LaTeX
- Dễ dàng version control với Git
- Có thể tích hợp vào documentation tự động

## 🔗 Tham Khảo

- [PlantUML Documentation](https://plantuml.com/)
- [PlantUML Activity Diagram](https://plantuml.com/activity-diagram-beta)
- Repository: `DangThiNhuQuynh19/KhoaLuanTotNghiep`

---

**Ngày tạo:** 2025-12-05  
**Phiên bản:** 1.0
