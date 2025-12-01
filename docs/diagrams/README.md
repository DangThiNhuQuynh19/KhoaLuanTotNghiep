# Sơ đồ Sequence - Chức năng Đăng ký tài khoản

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
