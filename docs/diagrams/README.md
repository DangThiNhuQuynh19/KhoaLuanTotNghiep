# Sơ đồ Sequence - Hệ thống Bệnh viện Hạnh Phúc

Tài liệu này mô tả các sơ đồ sequence UML cho các chức năng chính trong hệ thống.

## Danh sách sơ đồ

| Chức năng | File PlantUML | File PNG |
|-----------|---------------|----------|
| Đăng ký | [sequence_dangky.puml](./sequence_dangky.puml) | [sequence_dangky.png](./sequence_dangky.png) |
| Đăng nhập | [sequence_dangnhap.puml](./sequence_dangnhap.puml) | [sequence_dangnhap.png](./sequence_dangnhap.png) |

---

# 1. Sơ đồ Sequence - Chức năng Đăng ký tài khoản

## Mô tả

Sơ đồ sequence này mô tả luồng hoạt động của chức năng đăng ký tài khoản trong hệ thống Bệnh viện Hạnh Phúc.

## Các thành phần tham gia

| Thành phần | Mô tả |
|------------|-------|
| **Người dùng** | Actor - người dùng muốn đăng ký tài khoản |
| **View** | dangky/index.php - Giao diện form đăng ký |
| **Controller** | ctaikhoan.php - Xử lý logic đăng ký |
| **Model** | mtaikhoan.php - Tương tác với database |
| **Database** | MySQL - Lưu trữ dữ liệu |

## Luồng hoạt động chính

### 1. Bắt đầu đăng ký
- Người dùng truy cập trang đăng ký (`index.php?action=dangky`)
- View hiển thị form đăng ký với các trường thông tin

### 2. Nhập thông tin đăng ký
- Người dùng nhập các thông tin:
  - Thông tin cá nhân: Họ tên, ngày sinh, giới tính
  - Thông tin liên hệ: Email, số điện thoại
  - Giấy tờ tùy thân: Số CCCD, ảnh CCCD mặt trước/sau
  - Thông tin nghề nghiệp
  - Tiền sử bệnh: Của bản thân và gia đình
  - Địa chỉ: Tỉnh/thành phố, xã/phường, số nhà
  - Mật khẩu

### 3. Validation
**Client-side (JavaScript):**
- Kiểm tra định dạng email
- Kiểm tra số điện thoại (10 số)
- Kiểm tra số CCCD (9-12 số)
- Hiển thị tuổi

**Server-side (PHP):**
- Validate email hợp lệ
- SĐT 10 số
- CCCD 9-12 số
- Họ tên chỉ chứa chữ cái và khoảng trắng
- Mật khẩu >= 6 ký tự và khớp với nhập lại
- Tuổi >= 18

### 4. Xử lý đăng ký (nếu validation thành công)
1. **View** mã hóa dữ liệu nhạy cảm (email, SĐT, CCCD)
2. **View** upload ảnh CCCD (nếu có)
3. **View** tạo mã bệnh nhân (BN_XXXXXXXX)
4. **View** gọi **Controller** `dangkytk()`
5. **Controller** gọi **Model** `dangkytk()`
6. **Model** kiểm tra email đã tồn tại trong database

### 5. Kết quả
**Trường hợp email đã tồn tại:**
- Model trả về "email_ton_tai"
- View hiển thị thông báo lỗi

**Trường hợp email chưa tồn tại:**
1. Hash mật khẩu (MD5 - *Lưu ý: nên sử dụng password_hash() với bcrypt để bảo mật tốt hơn*)
2. INSERT vào bảng `taikhoan`
3. INSERT vào bảng `nguoidung`
4. INSERT vào bảng `benhnhan`
5. Trả về true
6. View hiển thị thông báo thành công
7. Chuyển hướng đến trang đăng nhập

## Sơ đồ Sequence (PlantUML)

File: [sequence_dangky.puml](./sequence_dangky.puml)

## Hướng dẫn render sơ đồ

### Sử dụng PlantUML Online
1. Truy cập https://www.plantuml.com/plantuml/uml/
2. Copy nội dung file `sequence_dangky.puml`
3. Paste vào editor và nhấn Submit

### Sử dụng VS Code
1. Cài extension "PlantUML"
2. Mở file `sequence_dangky.puml`
3. Nhấn `Alt + D` để preview

### Sử dụng Command Line
```bash
# Cài đặt PlantUML
# Ubuntu/Debian
sudo apt-get install plantuml

# Render sơ đồ
plantuml sequence_dangky.puml
```

## Các bảng Database liên quan

| Bảng | Mô tả |
|------|-------|
| `taikhoan` | Lưu thông tin đăng nhập (tentk, matkhau, mavaitro, matrangthai) |
| `nguoidung` | Lưu thông tin cá nhân (manguoidung, hoten, ngaysinh, gioitinh, cccd, sdt, email, ...) |
| `benhnhan` | Lưu thông tin y tế bệnh nhân (mabenhnhan, nghenghiep, tiensucuagiadinh, tiensucuabanthan, ...) |

## Tác giả
Hệ thống Bệnh viện Hạnh Phúc

---

# 2. Sơ đồ Sequence - Chức năng Đăng nhập

## Mô tả

Sơ đồ sequence này mô tả luồng hoạt động của chức năng đăng nhập trong hệ thống Bệnh viện Hạnh Phúc.

## Các thành phần tham gia

| Thành phần | Mô tả |
|------------|-------|
| **Người dùng** | Actor - người dùng muốn đăng nhập |
| **View** | dangnhap/index.php - Giao diện form đăng nhập |
| **Xử lý đăng nhập** | xulydangnhap.php - Xử lý request đăng nhập |
| **Controller** | ctaikhoan.php - Xử lý logic đăng nhập |
| **Model** | mtaikhoan.php - Tương tác với database |
| **Database** | MySQL - Lưu trữ dữ liệu |

## Luồng hoạt động chính

### 1. Bắt đầu đăng nhập
- Người dùng truy cập trang đăng nhập (`index.php?action=dangnhap`)
- View hiển thị form đăng nhập với các trường: email và mật khẩu

### 2. Nhập thông tin đăng nhập
- Người dùng nhập email và mật khẩu
- Click nút "Đăng nhập" để submit form

### 3. Xử lý đăng nhập
1. **xulydangnhap.php** nhận POST request
2. Mã hóa email bằng `encryptData()`
3. Hash mật khẩu bằng `MD5()`
4. Gọi **Controller** `dangnhap(tentk, password)`
5. **Controller** gọi **Model** `select_01_taikhoan(tentk, matkhau)`
6. **Model** truy vấn database kiểm tra tài khoản

### 4. Kết quả đăng nhập

**Trường hợp 1: Không tìm thấy tài khoản**
- Hiển thị thông báo "Email hoặc password không chính xác"
- Quay lại trang đăng nhập

**Trường hợp 2: Tài khoản bị vô hiệu hóa (matrangthai != 1)**
- Hiển thị thông báo "Tài khoản của bạn đã bị vô hiệu hóa"
- Quay lại trang đăng nhập

**Trường hợp 3: Đăng nhập thành công**
- Lưu thông tin vào SESSION:
  - `$_SESSION['dangnhap']` = mã vai trò
  - `$_SESSION['user']` = thông tin người dùng
- Chuyển hướng đến trang chủ (`index.php?action=trangchu`)

### 5. Phân quyền theo vai trò

| Mã vai trò | Vai trò | Giao diện |
|------------|---------|-----------|
| 1 | Bệnh nhân | Views/benhnhan |
| 2 | Bác sĩ | Views/bacsi |
| 3 | Chuyên gia | Views/chuyengia |
| 4 | Nhân viên tiếp tân | Views/nhanvientieptan |
| 5 | Nhân viên xét nghiệm | Views/nhanvienxetnghiem |
| 6 | Admin | Views/admin |
| 7 | Quản lý | Views/quanly |

### 6. Đăng nhập bằng Google (OAuth)
- Hỗ trợ đăng nhập bằng tài khoản Google
- Sử dụng Google API Client
- Callback handler trong `logingoogle/`

## Sơ đồ Sequence (PlantUML)

File: [sequence_dangnhap.puml](./sequence_dangnhap.puml)

## Các bảng Database liên quan

| Bảng | Mô tả |
|------|-------|
| `taikhoan` | Lưu thông tin đăng nhập (tentk, matkhau, mavaitro, matrangthai) |

---

## Hướng dẫn render sơ đồ

### Sử dụng PlantUML Online
1. Truy cập https://www.plantuml.com/plantuml/uml/
2. Copy nội dung file `.puml`
3. Paste vào editor và nhấn Submit

### Sử dụng VS Code
1. Cài extension "PlantUML"
2. Mở file `.puml`
3. Nhấn `Alt + D` để preview

### Sử dụng Command Line
```bash
# Cài đặt PlantUML
# Ubuntu/Debian
sudo apt-get install plantuml

# Render sơ đồ
plantuml sequence_dangky.puml
plantuml sequence_dangnhap.puml
```

## Tác giả
Hệ thống Bệnh viện Hạnh Phúc
