# Sơ Đồ Activity: Cập Nhật Kết Quả Xét Nghiệm

## Mô tả
Sơ đồ hoạt động (Activity Diagram) mô tả quy trình cập nhật kết quả xét nghiệm của **Nhân Viên Xét Nghiệm** trong hệ thống quản lý bệnh viện.

## Tác nhân chính
- **Nhân Viên Xét Nghiệm** (Lab Staff): Người thực hiện cập nhật kết quả xét nghiệm
- **Hệ Thống**: Xử lý và lưu trữ thông tin

## Quy trình chính

### 1. Đăng nhập và truy cập
- Nhân viên xét nghiệm đăng nhập vào hệ thống
- Truy cập trang danh sách lịch xét nghiệm
- Lọc theo ngày và trạng thái "Đang thực hiện" (status 11)

### 2. Chọn lịch xét nghiệm
- Xem danh sách các lịch xét nghiệm cần cập nhật
- Chọn lịch cụ thể và nhấn nút "Chỉnh sửa" (✏️)

### 3. Nhập kết quả xét nghiệm
Hệ thống hiển thị form nhập liệu với các trường:
- **Cho mỗi chỉ số xét nghiệm:**
  - Tên chỉ số (VD: Glucose, Hemoglobin)
  - Giá trị kết quả
  - Đơn vị đo (VD: mmol/L, g/dL)
  - Khoảng tham chiếu (VD: 3.9 - 6.4)
- **Thông tin chung:**
  - Giờ lấy mẫu
  - Nhận xét tổng quát

Nhân viên có thể thêm nhiều chỉ số xét nghiệm bằng nút "➕ Thêm chỉ số"

### 4. Xử lý và lưu trữ
Khi nhấn "💾 Lưu Cập Nhật", hệ thống:
1. Kiểm tra và validate dữ liệu đầu vào
2. Xóa kết quả cũ (nếu có) của lịch xét nghiệm
3. Thêm từng chỉ số mới vào bảng `ketquaxetnghiem`
4. Cập nhật trạng thái lịch xét nghiệm thành 12 ("Đã có kết quả")
5. Hiển thị thông báo thành công và chuyển về trang chủ

### 5. Xem chi tiết kết quả
- Nhân viên có thể xem chi tiết kết quả đã cập nhật
- Nhấn nút "Xem chi tiết" (👁️) để kiểm tra thông tin

## Trạng thái lịch xét nghiệm
- **10**: Chờ thanh toán
- **11**: Đang thực hiện (có thể cập nhật kết quả)
- **12**: Đã có kết quả (đã hoàn thành)

## Cấu trúc Database

### Bảng: ketquaxetnghiem
```sql
CREATE TABLE ketquaxetnghiem (
    maketquaxetnghiem INT(11) PRIMARY KEY AUTO_INCREMENT,
    malichxetnghiem INT(11) NOT NULL,
    tenchisoxetnghiem VARCHAR(255),
    giatriketqua VARCHAR(100),
    donviketqua VARCHAR(50),
    khoangthamchieu VARCHAR(100),
    ngaygiotraketqua DATETIME,
    giolaymau TIME,
    nhanxet TEXT,
    FOREIGN KEY (malichxetnghiem) REFERENCES lichxetnghiem(malichxetnghiem)
);
```

### Bảng: lichxetnghiem
```sql
CREATE TABLE lichxetnghiem (
    malichxetnghiem INT(11) PRIMARY KEY AUTO_INCREMENT,
    mabenhnhan INT(11),
    maloaixetnghiem INT(11),
    ngayhen DATE,
    makhunggio INT(11),
    matrangthai INT(11),
    mahoso INT(11),
    qr VARCHAR(255)
);
```

## File trong thư mục

- **activity_capnhat_ketqua_xetnghiem.puml**: Mã nguồn PlantUML của sơ đồ
- **activity_capnhat_ketqua_xetnghiem.png**: Hình ảnh sơ đồ dạng PNG
- **activity_capnhat_ketqua_xetnghiem.svg**: Hình ảnh sơ đồ dạng SVG (vector)

## Cách sử dụng

### Xem sơ đồ
Mở file PNG hoặc SVG để xem sơ đồ hoàn chỉnh.

### Chỉnh sửa sơ đồ
1. Cài đặt PlantUML và Java
2. Chỉnh sửa file `.puml`
3. Tạo lại hình ảnh:
```bash
java -jar plantuml.jar -tpng activity_capnhat_ketqua_xetnghiem.puml
java -jar plantuml.jar -tsvg activity_capnhat_ketqua_xetnghiem.puml
```

## Tham khảo
- Code implementation: 
  - `/Views/nhanvienxetnghiem/pages/chinhsua/index.php` - Form cập nhật kết quả
  - `/Views/nhanvienxetnghiem/pages/trangchu/index.php` - Danh sách lịch xét nghiệm
  - `/Controllers/cketquaxetnghiem.php` - Controller xử lý logic
  - `/Models/mketquaxetnghiem.php` - Model truy vấn database
