# Sơ Đồ Sitemap - Hệ Thống Quản Lý Bệnh Viện

## Giới thiệu

Repository này chứa sơ đồ sitemap chi tiết cho hệ thống quản lý bệnh viện. Sitemap bao gồm tất cả các trang và chức năng của 7 vai trò người dùng khác nhau.

## Các file sitemap

### 1. 📄 SITEMAP.md
**File tài liệu chính** - Tài liệu chi tiết về cấu trúc website

- Format: Markdown
- Nội dung: 
  - Danh sách đầy đủ tất cả các trang theo vai trò
  - Mô tả chức năng từng trang
  - Cấu trúc URL
  - Thông tin kỹ thuật chi tiết
  - Công nghệ sử dụng

**Cách xem:** Mở file bằng bất kỳ trình đọc Markdown nào hoặc xem trên GitHub

### 2. 🎨 sitemap-diagram.svg
**Biểu đồ trực quan** - Sơ đồ sitemap dạng hình ảnh

- Format: SVG (Scalable Vector Graphics)
- Nội dung:
  - Biểu đồ màu sắc đầy đủ
  - Hiển thị cấu trúc phân cấp
  - Thống kê số trang theo vai trò
  - Chi tiết kỹ thuật hệ thống

**Cách xem:** 
- Mở trực tiếp file .svg bằng trình duyệt web
- Hoặc mở file `sitemap-viewer.html`
- Có thể zoom in/out vì là vector graphics

### 3. 🌲 sitemap-tree.txt
**Cấu trúc cây ASCII** - Sơ đồ dạng text đơn giản

- Format: Plain text with ASCII art
- Nội dung:
  - Cấu trúc phân cấp dạng cây
  - Dễ đọc trên terminal
  - Phù hợp để in ra giấy
  - Bao gồm thống kê và ghi chú kỹ thuật

**Cách xem:**
- Mở bằng bất kỳ text editor nào
- Xem trên terminal: `cat sitemap-tree.txt`

### 4. 🌐 sitemap-viewer.html
**Trình xem web** - Giao diện web để xem sitemap

- Format: HTML
- Nội dung:
  - Hiển thị SVG diagram
  - Links đến các file khác
  - Thông tin tóm tắt
  - Responsive design

**Cách xem:** Mở file bằng trình duyệt web

## Tổng quan hệ thống

### Thống kê
- **Tổng số trang:** 91 trang
- **Số vai trò:** 7 vai trò người dùng
- **URL Pattern:** `index.php?action={page_name}`

### Vai trò người dùng

| # | Vai trò | Số trang | Session | Mô tả |
|---|---------|----------|---------|-------|
| 1 | **Bệnh nhân** (Patient) | 31 | `name`, `email` | Khách hoặc đã đăng nhập |
| 2 | **Bác sĩ** (Doctor) | 16 | `dangnhap = 2` | Quản lý bệnh nhân, hồ sơ, lịch hẹn |
| 3 | **Chuyên gia** (Expert) | 13 | `dangnhap = 3` | Tư vấn chuyên môn |
| 4 | **NV Tiếp tân** (Reception) | 11 | `dangnhap = 4` | Quản lý lịch hẹn |
| 5 | **NV Xét nghiệm** (Lab) | 3 | `dangnhap = 5` | Quản lý kết quả xét nghiệm |
| 6 | **Admin** | 5 | `dangnhap = 6` | Quản trị hệ thống |
| 7 | **Quản lý** (Manager) | 12 | `dangnhap = 7` | Quản lý nhân sự, lịch làm việc |

## Công nghệ

### Backend
- **PHP** với kiến trúc MVC
- **MySQL** database (`hanhphuc.sql`)
- **Session-based authentication**

### Packages (Composer)
- `cboden/ratchet` ^0.4.4 - WebSocket server
- `endroid/qr-code` ^6.0 - QR Code generation
- `guzzlehttp/guzzle` 7.7 - HTTP client
- `google/apiclient` 2.15 - Google OAuth
- `phpmailer/phpmailer` ^6.10 - Email service

### Tính năng chính
- ✅ Real-time chat (WebSocket)
- ✅ Đặt lịch khám bệnh
- ✅ Hồ sơ bệnh án điện tử
- ✅ Thanh toán VNPay
- ✅ Đăng nhập Google OAuth
- ✅ Quản lý xét nghiệm
- ✅ Thông báo email tự động

## Cấu trúc thư mục

```
/
├── Controllers/     - Business logic
├── Models/          - Database layer
├── Views/           - UI templates
│   ├── admin/
│   ├── bacsi/
│   ├── benhnhan/
│   ├── chuyengia/
│   ├── nhanvientieptan/
│   ├── nhanvienxetnghiem/
│   └── quanly/
├── Assets/          - Static files
├── Ajax/            - AJAX endpoints
├── uploads/         - File uploads
├── app/             - Laravel app (parallel)
├── index.php        - Main entry point
└── server.php       - WebSocket server
```

## Hướng dẫn sử dụng

### Xem sitemap nhanh
```bash
# Xem cấu trúc cây trong terminal
cat sitemap-tree.txt

# Hoặc mở file HTML trong trình duyệt
open sitemap-viewer.html  # macOS
xdg-open sitemap-viewer.html  # Linux
start sitemap-viewer.html  # Windows
```

### Tìm kiếm trang cụ thể
```bash
# Tìm trong file markdown
grep -i "tên_trang" SITEMAP.md

# Tìm trong cấu trúc cây
grep -i "tên_trang" sitemap-tree.txt
```

## URL Examples

```
# Bệnh nhân
index.php?action=trangchu          # Trang chủ
index.php?action=bacsi             # Danh sách bác sĩ
index.php?action=datlichkham       # Đặt lịch khám

# Bác sĩ (sau khi đăng nhập)
index.php?action=trangchu          # Trang chủ bác sĩ
index.php?action=benhnhan          # Danh sách bệnh nhân
index.php?action=taohoso           # Tạo hồ sơ bệnh án

# Admin
index.php?action=quanlynhansu      # Quản lý nhân sự
index.php?action=phanquyen         # Phân quyền
```

## Session Management

```php
// Bệnh nhân
$_SESSION['name'] = "Tên bệnh nhân";
$_SESSION['email'] = "email@example.com";

// Các vai trò khác
$_SESSION['dangnhap'] = 2;  // Bác sĩ
$_SESSION['dangnhap'] = 3;  // Chuyên gia
$_SESSION['dangnhap'] = 4;  // NV Tiếp tân
$_SESSION['dangnhap'] = 5;  // NV Xét nghiệm
$_SESSION['dangnhap'] = 6;  // Admin
$_SESSION['dangnhap'] = 7;  // Quản lý
```

## Notes

- File `index.php` là entry point chính xử lý tất cả routing
- Mỗi vai trò có layout và sidebar riêng
- Admin có sidebar đặc biệt
- Bệnh nhân có thể truy cập một số trang không cần đăng nhập
- WebSocket server chạy độc lập trên `server.php`

## Cập nhật

Sitemap này được tạo dựa trên cấu trúc code hiện tại của dự án. Khi thêm trang mới, vui lòng cập nhật:
1. SITEMAP.md - Thêm trang vào section tương ứng
2. sitemap-tree.txt - Thêm vào cấu trúc cây
3. sitemap-diagram.svg - Cập nhật số lượng trang (nếu cần)

## Liên hệ

Dự án: Khóa Luận Tốt Nghiệp
Repository: DangThiNhuQuynh19/KhoaLuanTotNghiep

---

*Created: December 2024*
*Last updated: December 2024*
