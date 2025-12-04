# Hướng Dẫn Sử Dụng Sơ Đồ Class UML

## Tổng Quan Sơ Đồ

Sơ đồ class UML được tạo thành công cho hệ thống quản lý bệnh viện "Hạnh Phúc". Sơ đồ mô tả đầy đủ các thành phần chính của hệ thống bao gồm:

### 1. **Các File Đã Tạo**

Trong thư mục `docs/`:
- **class-diagram.puml** - File nguồn PlantUML (có thể chỉnh sửa)
- **KhoaLuanTotNghiep_ClassDiagram.png** - Hình ảnh PNG (578KB, 3485x1876 pixels)
- **KhoaLuanTotNghiep_ClassDiagram.svg** - Hình ảnh SVG vector (128KB)
- **README.md** - Tài liệu chi tiết về sơ đồ

### 2. **Cấu Trúc Hệ Thống Được Mô Hình Hóa**

#### A. Lớp Kết Nối (Database Layer)
- **clsKetNoi**: Quản lý kết nối MySQL database

#### B. Lớp Model - Người Dùng
- **mNguoiDung** (Base class): Thông tin chung người dùng
  - **mBenhNhan**: Kế thừa từ mNguoiDung - Quản lý bệnh nhân
  - **mBacSi**: Kế thừa từ mNguoiDung - Quản lý bác sĩ
  - **mChuyenGia**: Kế thừa từ mNguoiDung - Quản lý chuyên gia tư vấn

#### C. Lớp Model - Khám Chữa Bệnh
- **mPhieuKhamBenh**: Quản lý phiếu khám và lịch hẹn
- **mHoSoBenhAnDienTu**: Quản lý hồ sơ bệnh án điện tử
- **mChiTietHoSo**: Chi tiết khám bệnh
- **mDonThuoc** & **mChiTietDonThuoc**: Quản lý đơn thuốc
- **mThuoc**: Thông tin thuốc

#### D. Lớp Model - Xét Nghiệm
- **mLichXetNghiem**: Lịch xét nghiệm
- **mLoaiXetNghiem**: Loại xét nghiệm
- **mKetQuaXetNghiem**: Kết quả xét nghiệm

#### E. Lớp Model - Quản Lý Lịch
- **mLichLamViec**: Lịch làm việc bác sĩ/chuyên gia
- **mCaLamViec**: Ca làm việc
- **mKhungGioKhamBenh**: Khung giờ khám
- **mLichHen**: Quản lý lịch hẹn

#### F. Lớp Model - Hỗ Trợ
- **mTaiKhoan**: Tài khoản đăng nhập
- **mChuyenKhoa** & **mLinhVuc**: Chuyên khoa và lĩnh vực
- **mTrangThai**: Trạng thái (phiếu khám, xét nghiệm, tài khoản)
- **mVaiTro**: Vai trò người dùng
- **mTinhThanhPho** & **mXaPhuong**: Địa chỉ hành chính
- **mPhong**: Phòng khám

#### G. Lớp Controller
- **cBenhNhan**, **cBacSi**, **cChuyenGia**: Xử lý nghiệp vụ người dùng
- **cTaiKhoan**: Xử lý đăng nhập/đăng ký
- **cPhieuKhamBenh**: Xử lý đặt lịch khám
- **cHoSoBenhAnDienTu**: Xử lý hồ sơ bệnh án
- **cLichHen**: Xử lý lịch hẹn

### 3. **Các Mối Quan Hệ Chính**

#### Kế Thừa (Inheritance)
```
mNguoiDung
    ↑
    ├── mBenhNhan
    ├── mBacSi
    └── mChuyenGia
```

#### Composition/Aggregation
- 1 Hồ sơ bệnh án → Nhiều chi tiết hồ sơ
- 1 Đơn thuốc → Nhiều chi tiết đơn thuốc
- 1 Bệnh nhân → Nhiều phiếu khám

#### Association
- Phiếu khám ↔ Bác sĩ/Chuyên gia
- Bác sĩ → Chuyên khoa
- Chuyên gia → Lĩnh vực
- Người dùng → Xã phường → Tỉnh thành phố

### 4. **Cách Xem Sơ Đồ**

#### Trên GitHub
Xem trực tiếp file PNG hoặc SVG trong thư mục `docs/`

#### Trên Máy Tính Local
1. Clone repository
2. Mở file `docs/KhoaLuanTotNghiep_ClassDiagram.png` hoặc `.svg`
3. Sử dụng trình duyệt web hoặc ứng dụng xem ảnh

#### Chỉnh Sửa Sơ Đồ
1. Mở file `docs/class-diagram.puml` bằng editor
2. Chỉnh sửa theo cú pháp PlantUML
3. Tạo lại sơ đồ:
```bash
# Cần có Java và Graphviz đã cài đặt
java -jar plantuml.jar -tpng docs/class-diagram.puml
java -jar plantuml.jar -tsvg docs/class-diagram.puml
```

### 5. **Ứng Dụng Thực Tế**

Sơ đồ này có thể được sử dụng cho:

1. **Tài liệu dự án**: Mô tả kiến trúc hệ thống
2. **Đào tạo**: Giúp thành viên mới hiểu hệ thống
3. **Phát triển**: Tham khảo khi thêm tính năng mới
4. **Bảo trì**: Hiểu rõ mối quan hệ giữa các module
5. **Thuyết trình**: Trình bày kiến trúc cho stakeholders

### 6. **Lưu Ý Kỹ Thuật**

- **Ngôn ngữ**: PHP
- **Database**: MySQL
- **Kiến trúc**: MVC (Model-View-Controller)
- **Công cụ vẽ**: PlantUML
- **Định dạng**: PNG (raster) và SVG (vector)

### 7. **Điểm Nổi Bật Của Sơ Đồ**

✅ **Hoàn chỉnh**: Bao gồm tất cả các class chính trong hệ thống
✅ **Chi tiết**: Hiển thị thuộc tính và phương thức quan trọng
✅ **Rõ ràng**: Các mối quan hệ được thể hiện rõ ràng
✅ **Có thể chỉnh sửa**: File nguồn PlantUML dễ dàng cập nhật
✅ **Đa định dạng**: Có cả PNG và SVG
✅ **Tài liệu đầy đủ**: Kèm theo README chi tiết

### 8. **Hỗ Trợ**

Nếu cần hỗ trợ hoặc có thắc mắc về sơ đồ:
1. Xem file `docs/README.md` để biết thêm chi tiết
2. Liên hệ với team phát triển
3. Tham khảo tài liệu PlantUML: https://plantuml.com/

---

**Ngày tạo**: 04/12/2025  
**Công cụ**: GitHub Copilot + PlantUML  
**Phiên bản**: 1.0
