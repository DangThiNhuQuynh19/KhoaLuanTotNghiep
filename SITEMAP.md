# Sơ Đồ Sitemap - Hệ Thống Quản Lý Bệnh Viện

## Tổng Quan Hệ Thống
Hệ thống quản lý bệnh viện với 7 vai trò người dùng khác nhau, mỗi vai trò có các trang riêng biệt.

---

## 1. BỆNH NHÂN (Patient) - Vai trò: Khách/Đã đăng nhập

### 1.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Về chúng tôi** (`vechungtoi`)
- **Liên hệ** (`lienhe`)
- **Blog** (`blog`)

### 1.2. Xác Thực
- **Đăng nhập** (`dangnhap`)
  - Login với Google (`logingoogle`)
- **Đăng ký** (`dangky`)
- **Đăng xuất** (`dangxuat`)

### 1.3. Quản Lý Hồ Sơ
- **Hồ sơ bệnh án điện tử** (`hosobenhandientu`)
  - Chi tiết hồ sơ bệnh án điện tử (`chitiethosobenhandientu`)
  - Tạo hồ sơ (`taohoso`)
  - Sửa hồ sơ (`suahoso`)
  - Xóa hồ sơ (`xoahoso`)

### 1.4. Đặt Lịch & Quản Lý Lịch Hẹn
- **Đặt lịch khám** (`datlichkham`)
- **Lịch hẹn** (`lichhen`)
- **Lịch khám** (`lichkham`)
- **Lịch khám chuyên gia** (`lichkhamchuyengia`)
- **Lịch xét nghiệm** (`lichxetnghiem`)

### 1.5. Tìm Kiếm Bác Sĩ & Chuyên Gia
- **Chuyên khoa** (`chuyenkhoa`)
  - Chi tiết chuyên khoa (`chitietchuyenkhoa`)
- **Bác sĩ** (`bacsi`)
  - Chi tiết bác sĩ (`chitietbacsi`)
- **Chuyên gia** (`chuyengia`)
  - Chi tiết chuyên gia (`chitietchuyengia`)

### 1.6. Thanh Toán
- **Thanh toán** (`thanhtoan`)
- **VNPay** (`vnpay`)
- **Kết quả thanh toán** (`paymentresult`)

### 1.7. Giao Tiếp
- **Tin nhắn** (`tinnhan`)

### 1.8. Cài Đặt
- **Cài đặt** (`caidat`)

---

## 2. BÁC SĨ (Doctor) - Vai trò: Đăng nhập = 2

### 2.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Đăng xuất** (`dangxuat`)

### 2.2. Quản Lý Bệnh Nhân
- **Bệnh nhân** (`benhnhan`)
  - Chi tiết bệnh nhân (`chitietbenhnhan`)

### 2.3. Quản Lý Hồ Sơ Bệnh Án
- **Hồ sơ** (`hoso`)
  - Chi tiết hồ sơ (`chitiethoso`)
  - Tạo hồ sơ (`taohoso`)
  - Cập nhật phiếu khám bệnh (`update_phieukhambenh`)

### 2.4. Quản Lý Lịch Hẹn
- **Lịch hẹn trực tiếp** (`lichhentructiep`)
- **Lịch hẹn trực tuyến** (`lichhentructuyen`)
- **Xem lịch làm việc** (`xemlichlamviec`)
- **Đặt lịch** (`datlich`)

### 2.5. Xét Nghiệm
- **Xét nghiệm** (`xetnghiem`)
- **Kết quả xét nghiệm** (`ketquaxetnghiem`)
- **Đặt lịch xét nghiệm** (`datlichxetnghiem`)

### 2.6. Đơn Thuốc
- **Tạo đơn thuốc** (`taodonthuoc`)

### 2.7. Giao Tiếp
- **Tin nhắn** (`tinnhan`)

---

## 3. CHUYÊN GIA (Expert) - Vai trò: Đăng nhập = 3

### 3.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Đăng xuất** (`dangxuat`)

### 3.2. Quản Lý Bệnh Nhân
- **Bệnh nhân** (`benhnhan`)
  - Chi tiết bệnh nhân (`chitietbenhnhan`)

### 3.3. Quản Lý Hồ Sơ
- **Hồ sơ** (`hoso`)
  - Chi tiết hồ sơ (`chitiethoso`)
  - Tạo hồ sơ (`taohoso`)
  - Cập nhật phiếu khám bệnh (`update_phieukhambenh`)

### 3.4. Quản Lý Lịch Hẹn
- **Lịch hẹn trực tiếp** (`lichhentructiep`)
- **Lịch hẹn trực tuyến** (`lichhentructuyen`)
- **Chi tiết lịch hẹn** (`chitietlichhen`)
- **Đặt lịch chuyên gia** (`datlichcg`)
- **Xem lịch làm việc** (`xemlichlamviec`)

### 3.5. Giao Tiếp
- **Tin nhắn** (`tinnhan`)

---

## 4. NHÂN VIÊN TIẾP TÂN (Reception Staff) - Vai trò: Đăng nhập = 4

### 4.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Đăng xuất** (`dangxuat`)

### 4.2. Quản Lý Thông Tin Cá Nhân
- **Thông tin** (`thongtin`)
- **Sửa thông tin** (`suathongtin`)

### 4.3. Quản Lý Lịch Hẹn
- **Lịch hẹn** (`lichhen`)
  - Hủy lịch hẹn (`huylichhen`)
  - Sửa lịch hẹn (`sualichhen`)
- **Lịch cá nhân** (`lichcanhan`)
- **Đặt lịch khám** (`datlichkham`)

### 4.4. Quản Lý Nhân Sự
- **Nhân viên** (`nhanvien`)
- **Chuyên gia** (`chuyengia`)
  - Chi tiết chuyên gia (`chitietchuyengia`)
  - Chi tiết bác sĩ (`chitietbacsi`)

---

## 5. NHÂN VIÊN XÉT NGHIỆM (Lab Staff) - Vai trò: Đăng nhập = 5

### 5.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Đăng xuất** (`dangxuat`)

### 5.2. Quản Lý Xét Nghiệm
- **Xem chi tiết** (`xemchitiet`)
- **Chỉnh sửa** (`chinhsua`)

---

## 6. ADMIN (Administrator) - Vai trò: Đăng nhập = 6

### 6.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Đăng xuất** (`dangxuat`)

### 6.2. Quản Lý Hệ Thống
- **Quản lý nhân sự** (`quanlynhansu`)
- **Quản lý bài viết** (`quanlybaiviet`)
- **Phân quyền** (`phanquyen`)

---

## 7. QUẢN LÝ (Manager) - Vai trò: Đăng nhập = 7

### 7.1. Trang Chính
- **Trang chủ** (`trangchu`)
- **Đăng xuất** (`dangxuat`)

### 7.2. Quản Lý Nhân Viên
- **Nhân viên** (`nhanvien`)
  - Chi tiết nhân viên (`chitietnhanvien`)

### 7.3. Quản Lý Bác Sĩ
- **Thêm bác sĩ** (`thembacsi`)
- **Sửa bác sĩ** (`suabacsi`)
- **Chi tiết bác sĩ** (`chitietbacsi`)

### 7.4. Quản Lý Chuyên Gia
- **Thêm chuyên gia** (`themchuyengia`)
- **Sửa chuyên gia** (`suachuyengia`)
- **Chi tiết chuyên gia** (`chitietchuyengia`)

### 7.5. Quản Lý Lịch Làm Việc
- **Lịch làm việc** (`lichlamviec`)
- **Xếp lịch** (`xeplich`)

---

## Cấu Trúc URL
Hệ thống sử dụng query string để điều hướng:
- **URL Pattern**: `index.php?action={page_name}[&cate={category}]`
- **Ví dụ**: 
  - `index.php?action=trangchu` - Trang chủ
  - `index.php?action=bacsi` - Danh sách bác sĩ
  - `index.php?action=bacsi&cate=timach` - Bác sĩ chuyên khoa tim mạch
  - `index.php?action=datlichkham` - Đặt lịch khám

**Lưu ý**: Tham số `cate` (category) là tùy chọn và được sử dụng để lọc theo chuyên khoa hoặc phân loại cụ thể.

---

## Tính Năng Đặc Biệt

### WebSocket/Chat
- **Chat Server**: `server.php` (Ratchet WebSocket)
- **Chat Client**: `Chat.php`, `Chta.php`

### AJAX Endpoints
- `Ajax/getnhanvien.php` - Lấy thông tin nhân viên
- `Ajax/getphong.php` - Lấy thông tin phòng
- `Ajax/phancanhanvien.php` - Phân ca nhân viên
- `Ajax/getlichhen.php` - Lấy lịch hẹn

### Thanh Toán
- Tích hợp VNPay
- Email thanh toán tự động

### Xác Thực
- Đăng nhập thông thường
- Đăng nhập qua Google OAuth
- Phân quyền 7 cấp độ

---

## Công Nghệ Sử Dụng

### Backend
- PHP với kiến trúc MVC
- Composer packages:
  - Ratchet (WebSocket)
  - Endroid QR Code
  - Guzzle HTTP Client
  - Google API Client
  - PHPMailer

### Frontend  
- Laravel Mix (trong thư mục app/)
- Assets tĩnh trong Assets/

### Database
- MySQL (`hanhphuc.sql`)

---

## Ghi Chú
- Hệ thống có 2 phần: PHP thuần (thư mục gốc) và Laravel app (thư mục app/)
- Session dùng để quản lý đăng nhập với giá trị `$_SESSION['dangnhap']` từ 2-7
- Bệnh nhân dùng `$_SESSION['name']` và `$_SESSION['email']`
