# Sơ Đồ Lớp UML - Hệ Thống Quản Lý Bệnh Viện

## Tổng Quan

Đây là sơ đồ lớp UML (Unified Modeling Language) đầy đủ mô tả kiến trúc hệ thống quản lý bệnh viện. Sơ đồ này bao gồm **tất cả 32+ lớp Model** với **đầy đủ các phương thức** của từng lớp.

## Các File Trong Thư Mục

- **class-diagram.puml**: File nguồn PlantUML chứa định nghĩa sơ đồ lớp (đã cập nhật đầy đủ)
- **KhoaLuanTotNghiep_ClassDiagram.png**: Hình ảnh sơ đồ lớp định dạng PNG (832KB, độ phân giải cao)
- **KhoaLuanTotNghiep_ClassDiagram.svg**: Hình ảnh sơ đồ lớp định dạng SVG (184KB, vector)

## Phiên Bản Cập Nhật

**Phiên bản 2.0** - Đã bổ sung:
- ✅ Tất cả 32 lớp Model trong hệ thống
- ✅ Đầy đủ tất cả các phương thức (public/private) của mỗi lớp
- ✅ Các lớp bổ sung: mLichKham, mKhungGioXetNghiem, mChiTietBacSi, mChiTietChuyenKhoa, mNhanVien, mNguoiKham, mEmailThanhToan, ChatUserModel
- ✅ Tất cả phương thức đầy đủ cho mBenhNhan (19 phương thức), mPhieuKhamBenh (22 phương thức), mLichXetNghiem (11 phương thức), v.v.

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

### 2. Lớp Model (Tầng Dữ Liệu) - 32 Lớp Đầy Đủ

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
- **Phương thức**: select_thuoc()

#### Các lớp quản lý xét nghiệm

##### mLichXetNghiem
- **Mô tả**: Lớp quản lý lịch xét nghiệm với 11 phương thức đầy đủ
- **Quan hệ**: Thuộc về một hồ sơ bệnh án
- **Phương thức chính**:
  - select_lichxetnghiem_mabacsi(), select_lichxetnghiem_mabenhnhan()
  - timkiem_lichxetnghiem(), insert_lichxetnghiem()
  - lichxetnghiemtheotentk(), selectall_lichxetnghiem()
  - chitietlichxetnghiem()

##### mLoaiXetNghiem
- **Mô tả**: Lớp quản lý các loại xét nghiệm
- **Thuộc tính**: tenloaixetnghiem, dongia, mota
- **Phương thức**: select_danhmucxetnghiem(), select_loaixetnghiem(), select_loaixetnghiem_maloaixetnghiem()

##### mKetQuaXetNghiem
- **Mô tả**: Lớp lưu trữ kết quả xét nghiệm
- **Thuộc tính**: ketqua, ghichu, ngaytraketqua

##### mKhungGioXetNghiem
- **Mô tả**: Quản lý khung giờ xét nghiệm
- **Phương thức**: select_khunggioxetnghiem(), select_khunggioxetnghiem_makhunggio()

#### Các lớp hỗ trợ

##### mTaiKhoan
- **Mô tả**: Lớp quản lý tài khoản đăng nhập
- **Chức năng**:
  - Đăng ký, đăng nhập
  - Quản lý vai trò và trạng thái tài khoản

##### mChuyenKhoa & mLinhVuc
- **Mô tả**: Quản lý chuyên khoa của bác sĩ và lĩnh vực của chuyên gia
- **mLinhVuc phương thức**: dslinhvuc(), select_linhvuc_notmabenhnhan(), select_linhvuc_machuyengia()

##### mLichKham (Mới)
- **Mô tả**: Quản lý lịch khám chi tiết với 10 phương thức
- **Phương thức chính**: 
  - lichkhamcg(), lichkhambs(), xemlich()
  - getLichBacSiTheoNgay(), getLichChuyenGiaTheoNgay()
  - getTatCaLichKhamTheoNgay(), getLichTrongTheoNguoi()

##### mLichLamViec
- **Mô tả**: Quản lý lịch làm việc của bác sĩ/chuyên gia
- **Quan hệ**: Liên kết với ca làm việc và phòng khám
- **Phương thức**: updatelichlamviecday(), updatelichlamviectrong(), laymalichlamviec(), lichlamviec()

##### mCaLamViec
- **Mô tả**: Định nghĩa các ca làm việc
- **Thuộc tính**: tenca, giobatdau, gioketthuc
- **Phương thức**: select_calam(), phanCaTheoThoiHan()

##### mKhungGioKhamBenh
- **Mô tả**: Khung giờ khám trong mỗi ca
- **Phương thức**: allkhunggio(), selectgio()

##### mTrangThai
- **Mô tả**: Quản lý các trạng thái (phiếu khám, tài khoản, xét nghiệm)

##### mVaiTro
- **Mô tả**: Định nghĩa các vai trò trong hệ thống (admin, bác sĩ, bệnh nhân, chuyên gia)
- **Phương thức**: select_vaitro()

##### mTinhThanhPho & mXaPhuong
- **Mô tả**: Quản lý thông tin địa chỉ hành chính
- **mTinhThanhPho phương thức**: select_tinhthanhpho()
- **mXaPhuong phương thức**: select_xaphuong_mathanhpho(), select_xaphuong()

##### mPhong
- **Mô tả**: Quản lý thông tin phòng khám
- **Thuộc tính**: tentoa, tang, sophong

#### Các lớp bổ sung mới

##### mChiTietBacSi (Mới)
- **Mô tả**: Quản lý chi tiết thông tin bác sĩ
- **Phương thức**: chitietbacsi(), lichlamvieccuabacsi()

##### mChiTietChuyenKhoa (Mới)
- **Mô tả**: Quản lý chi tiết chuyên khoa
- **Phương thức**: chitietchuyenkhoa()

##### mNhanVien (Mới)
- **Mô tả**: Quản lý thông tin nhân viên
- **Phương thức**: xemnhanvientheotentk(), getAllNhanVien(), getNhanVienByName(), chitietnhanvien()

##### mNguoiKham (Mới)
- **Mô tả**: Quản lý người khám
- **Phương thức**: allnguoikham()

##### mEmailThanhToan (Mới)
- **Mô tả**: Quản lý email thanh toán
- **Phương thức**: insert_emailyeucauthanhtoan()

##### ChatUserModel (Mới)
- **Mô tả**: Quản lý tin nhắn chat giữa người dùng
- **Phương thức**: setSender(), setReceiver(), setMessage(), saveMessage(), getMessages(), getMessageTime()

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
- **Tổng số lớp Model**: 32+ lớp
- **Tổng số phương thức**: 200+ phương thức

## Cập Nhật

- **Ngày tạo**: 04/12/2025
- **Phiên bản**: 2.0 (Đã bổ sung đầy đủ tất cả các lớp và phương thức)
- **Cập nhật lần cuối**: 04/12/2025
- **Người tạo**: GitHub Copilot

**Thay đổi trong phiên bản 2.0:**
- ✅ Bổ sung 8 lớp mới: mLichKham, mKhungGioXetNghiem, mChiTietBacSi, mChiTietChuyenKhoa, mNhanVien, mNguoiKham, mEmailThanhToan, ChatUserModel
- ✅ Bổ sung đầy đủ tất cả phương thức cho mọi lớp:
  - mBenhNhan: 19 phương thức (thêm 12 phương thức)
  - mPhieuKhamBenh: 22 phương thức (thêm 14 phương thức)
  - mLichXetNghiem: 11 phương thức (hoàn toàn mới)
  - mHoSoBenhAnDienTu: 14 phương thức (thêm 2 phương thức)
  - Và tất cả các lớp khác với đầy đủ phương thức
- ✅ Cập nhật sơ đồ PNG (832KB) và SVG (184KB)

## Hướng Dẫn Sử Dụng Sơ Đồ

1. **Đọc hiểu các lớp**: Bắt đầu từ các lớp cơ bản như `mNguoiDung`, `clsKetNoi`
2. **Theo dõi mối quan hệ**: Xem các mũi tên để hiểu cách các lớp tương tác
3. **Nghiên cứu Controller**: Hiểu cách Controller điều phối giữa View và Model
4. **Ánh xạ với code**: Đối chiếu với code thực tế trong thư mục Models/ và Controllers/
5. **Xem chi tiết phương thức**: Tất cả phương thức public và private đã được liệt kê đầy đủ

## Liên Hệ

Nếu có thắc mắc hoặc cần hỗ trợ về sơ đồ này, vui lòng liên hệ với nhóm phát triển.
