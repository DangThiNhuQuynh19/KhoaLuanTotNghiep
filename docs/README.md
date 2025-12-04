# Sơ Đồ Lớp UML - Hệ Thống Quản Lý Bệnh Viện

## Tổng Quan

Đây là sơ đồ lớp UML (Unified Modeling Language) mô tả kiến trúc hệ thống quản lý bệnh viện. Sơ đồ này được tạo ra để giúp hiểu rõ cấu trúc, mối quan hệ giữa các lớp và luồng dữ liệu trong hệ thống.

## Các File Trong Thư Mục

- **class-diagram.puml**: File nguồn PlantUML chứa định nghĩa sơ đồ lớp
- **KhoaLuanTotNghiep_ClassDiagram.png**: Hình ảnh sơ đồ lớp định dạng PNG (độ phân giải cao)
- **KhoaLuanTotNghiep_ClassDiagram.svg**: Hình ảnh sơ đồ lớp định dạng SVG (vector, có thể phóng to không mất chất lượng)

## Xem Sơ Đồ

### Xem trực tiếp
Bạn có thể xem sơ đồ bằng cách mở file `KhoaLuanTotNghiep_ClassDiagram.png` hoặc `KhoaLuanTotNghiep_ClassDiagram.svg` trong trình duyệt hoặc ứng dụng xem ảnh.

### Xem và chỉnh sửa file PlantUML
File `class-diagram.puml` có thể được chỉnh sửa bằng bất kỳ trình soạn thảo văn bản nào. Để tạo lại sơ đồ sau khi chỉnh sửa:

```bash
# Cài đặt Java (nếu chưa có)
sudo apt-get install default-jre

# Cài đặt Graphviz
sudo apt-get install graphviz

# Tải PlantUML
wget https://github.com/plantuml/plantuml/releases/latest/download/plantuml.jar

# Tạo sơ đồ PNG
java -jar plantuml.jar -tpng class-diagram.puml

# Tạo sơ đồ SVG
java -jar plantuml.jar -tsvg class-diagram.puml
```

## Cấu Trúc Hệ Thống

### 1. Lớp Kết Nối Database

#### clsKetNoi
- **Mô tả**: Lớp quản lý kết nối đến cơ sở dữ liệu MySQL
- **Phương thức**:
  - `moKetNoi()`: Mở kết nối đến database
  - `dongKetNoi(con)`: Đóng kết nối database

### 2. Lớp Model (Tầng Dữ Liệu)

#### Các lớp người dùng

##### mNguoiDung (Lớp cơ sở)
- **Mô tả**: Lớp đại diện cho thông tin cơ bản của người dùng trong hệ thống
- **Thuộc tính**: manguoidung, hoten, ngaysinh, gioitinh, cccd, sdt, email, địa chỉ, v.v.

##### mBenhNhan
- **Mô tả**: Lớp quản lý thông tin và nghiệp vụ liên quan đến bệnh nhân
- **Kế thừa**: mNguoiDung
- **Chức năng chính**:
  - Quản lý hồ sơ bệnh nhân
  - Đăng ký, cập nhật, xóa thông tin bệnh nhân
  - Tìm kiếm bệnh nhân theo nhiều tiêu chí
  - Quản lý ví tiền của bệnh nhân

##### mBacSi
- **Mô tả**: Lớp quản lý thông tin bác sĩ
- **Kế thừa**: mNguoiDung
- **Thuộc tính đặc biệt**: 
  - machuyenkhoa (chuyên khoa)
  - giakham (giá khám)
  - capbac (cấp bậc)
  - imgbs (ảnh bác sĩ)
- **Chức năng chính**:
  - Quản lý danh sách bác sĩ
  - Tìm kiếm bác sĩ theo tên, chuyên khoa
  - Cập nhật thông tin bác sĩ
  - Quản lý lịch làm việc

##### mChuyenGia
- **Mô tả**: Lớp quản lý thông tin chuyên gia tư vấn
- **Kế thừa**: mNguoiDung
- **Thuộc tính đặc biệt**:
  - malinhvuc (lĩnh vực chuyên môn)
  - giatuvan (giá tư vấn)
- **Chức năng**: Tương tự mBacSi nhưng cho chuyên gia

#### Các lớp quản lý khám chữa bệnh

##### mPhieuKhamBenh
- **Mô tả**: Lớp quản lý phiếu khám bệnh và lịch hẹn
- **Chức năng chính**:
  - Tạo, hủy, cập nhật phiếu khám
  - Kiểm tra trùng lịch
  - Tìm kiếm phiếu khám theo nhiều tiêu chí
  - Quản lý lịch khám online/offline

##### mHoSoBenhAnDienTu
- **Mô tả**: Lớp quản lý hồ sơ bệnh án điện tử
- **Chức năng chính**:
  - Lưu trữ và truy xuất hồ sơ bệnh án
  - Quản lý chi tiết hồ sơ khám bệnh
  - Lấy thông tin đơn thuốc và xét nghiệm

##### mChiTietHoSo
- **Mô tả**: Lớp lưu trữ chi tiết của một lần khám bệnh
- **Thuộc tính**: chandoan, trieuchungbandau, huongdieutri, ketluan

##### mDonThuoc & mChiTietDonThuoc
- **Mô tả**: Quản lý đơn thuốc và chi tiết thuốc được kê
- **Quan hệ**: Một đơn thuốc có nhiều chi tiết đơn thuốc

##### mThuoc
- **Mô tả**: Lớp quản lý thông tin thuốc
- **Thuộc tính**: tenthuoc, donvitinh, dongia, mota

#### Các lớp quản lý xét nghiệm

##### mLichXetNghiem
- **Mô tả**: Lớp quản lý lịch xét nghiệm
- **Quan hệ**: Thuộc về một hồ sơ bệnh án

##### mLoaiXetNghiem
- **Mô tả**: Lớp quản lý các loại xét nghiệm
- **Thuộc tính**: tenloaixetnghiem, dongia, mota

##### mKetQuaXetNghiem
- **Mô tả**: Lớp lưu trữ kết quả xét nghiệm
- **Thuộc tính**: ketqua, ghichu, ngaytraketqua

#### Các lớp hỗ trợ

##### mTaiKhoan
- **Mô tả**: Lớp quản lý tài khoản đăng nhập
- **Chức năng**:
  - Đăng ký, đăng nhập
  - Quản lý vai trò và trạng thái tài khoản

##### mChuyenKhoa & mLinhVuc
- **Mô tả**: Quản lý chuyên khoa của bác sĩ và lĩnh vực của chuyên gia

##### mLichLamViec
- **Mô tả**: Quản lý lịch làm việc của bác sĩ/chuyên gia
- **Quan hệ**: Liên kết với ca làm việc và phòng khám

##### mCaLamViec
- **Mô tả**: Định nghĩa các ca làm việc
- **Thuộc tính**: tenca, giobatdau, gioketthuc

##### mKhungGioKhamBenh
- **Mô tả**: Khung giờ khám trong mỗi ca

##### mTrangThai
- **Mô tả**: Quản lý các trạng thái (phiếu khám, tài khoản, xét nghiệm)

##### mVaiTro
- **Mô tả**: Định nghĩa các vai trò trong hệ thống (admin, bác sĩ, bệnh nhân, chuyên gia)

##### mTinhThanhPho & mXaPhuong
- **Mô tả**: Quản lý thông tin địa chỉ hành chính

##### mPhong
- **Mô tả**: Quản lý thông tin phòng khám
- **Thuộc tính**: tentoa, tang, sophong

### 3. Lớp Controller (Tầng Điều Khiển)

Các lớp Controller xử lý logic nghiệp vụ và điều phối giữa View và Model:

- **cBenhNhan**: Xử lý nghiệp vụ liên quan đến bệnh nhân
- **cBacSi**: Xử lý nghiệp vụ liên quan đến bác sĩ
- **cChuyenGia**: Xử lý nghiệp vụ liên quan đến chuyên gia
- **cTaiKhoan**: Xử lý đăng nhập, đăng ký
- **cPhieuKhamBenh**: Xử lý đặt lịch, khám bệnh
- **cHoSoBenhAnDienTu**: Xử lý hồ sơ bệnh án
- **cLichHen**: Xử lý lịch hẹn

## Mối Quan Hệ Chính

### Quan hệ kế thừa
- `mBenhNhan`, `mBacSi`, `mChuyenGia` kế thừa từ `mNguoiDung`

### Quan hệ has-a (composition/aggregation)
- Mỗi `mBenhNhan` có nhiều `mPhieuKhamBenh`
- Mỗi `mHoSoBenhAnDienTu` chứa nhiều `mChiTietHoSo`
- Mỗi `mDonThuoc` có nhiều `mChiTietDonThuoc`
- Mỗi `mChiTietDonThuoc` sử dụng một `mThuoc`

### Quan hệ association
- `mPhieuKhamBenh` được gán cho `mBacSi` hoặc `mChuyenGia`
- `mBacSi` thuộc một `mChuyenKhoa`
- `mChuyenGia` thuộc một `mLinhVuc`
- `mNguoiDung` sống tại một `mXaPhuong`

### Quan hệ uses
- Tất cả Model classes sử dụng `clsKetNoi` để truy cập database
- Tất cả Controller classes sử dụng các Model tương ứng

## Luồng Hoạt Động Chính

### 1. Đăng ký/Đăng nhập
```
User → cTaiKhoan → mTaiKhoan → Database
                 → mNguoiDung → Database
```

### 2. Đặt lịch khám
```
Bệnh nhân → cPhieuKhamBenh → mPhieuKhamBenh → Database
                           → mBacSi/mChuyenGia → Database
                           → mKhungGioKhamBenh → Database
```

### 3. Khám bệnh và lưu hồ sơ
```
Bác sĩ → cHoSoBenhAnDienTu → mHoSoBenhAnDienTu → Database
                           → mChiTietHoSo → Database
                           → mDonThuoc → Database
```

## Ghi Chú Kỹ Thuật

- **Ngôn ngữ lập trình**: PHP
- **Cơ sở dữ liệu**: MySQL
- **Kiến trúc**: MVC (Model-View-Controller)
- **Mô hình hóa**: PlantUML

## Cập Nhật

- **Ngày tạo**: 04/12/2025
- **Phiên bản**: 1.0
- **Người tạo**: GitHub Copilot

## Hướng Dẫn Sử Dụng Sơ Đồ

1. **Đọc hiểu các lớp**: Bắt đầu từ các lớp cơ bản như `mNguoiDung`, `clsKetNoi`
2. **Theo dõi mối quan hệ**: Xem các mũi tên để hiểu cách các lớp tương tác
3. **Nghiên cứu Controller**: Hiểu cách Controller điều phối giữa View và Model
4. **Ánh xạ với code**: Đối chiếu với code thực tế trong thư mục Models/ và Controllers/

## Liên Hệ

Nếu có thắc mắc hoặc cần hỗ trợ về sơ đồ này, vui lòng liên hệ với nhóm phát triển.
