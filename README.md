# Hệ Thống Quản Lý Bệnh Viện Hạnh Phúc

## Giới Thiệu

Hệ thống quản lý bệnh viện toàn diện với các chức năng:
- Quản lý bệnh nhân và hồ sơ bệnh án điện tử
- Đặt lịch khám bệnh trực tuyến
- Quản lý bác sĩ, chuyên gia và lịch làm việc
- Quản lý xét nghiệm và kết quả
- Kê đơn thuốc điện tử
- Chat trực tuyến với bác sĩ

## Công Nghệ

- **Backend**: PHP
- **Database**: MySQL/MariaDB
- **Frontend**: HTML, CSS, JavaScript
- **Realtime**: WebSocket (Ratchet)

## Cấu Trúc Thư Mục

```
.
├── Ajax/              # AJAX endpoints
├── Assets/            # CSS, JS, Images
├── Controllers/       # Controllers
├── Models/            # Database models
├── Views/             # View templates
├── app/              # Laravel components
├── docs/             # Tài liệu dự án
│   ├── domain-diagram.md    # Sơ đồ ER chi tiết
│   ├── domain-overview.md   # Tổng quan domain
│   └── README.md            # Hướng dẫn tài liệu
├── vendor/           # Dependencies
└── uploads/          # User uploads
```

## Tài Liệu

### 📊 Sơ Đồ Domain

Xem tài liệu chi tiết về kiến trúc và mô hình dữ liệu tại:

- **[Sơ Đồ Domain Chi Tiết](./docs/domain-diagram.md)** - ER Diagram với tất cả entities và relationships
- **[Tổng Quan Domain](./docs/domain-overview.md)** - Sơ đồ tổng quan và luồng hoạt động
- **[Hướng Dẫn Tài Liệu](./docs/README.md)** - Cách đọc và sử dụng tài liệu

## Cài Đặt

### Yêu Cầu

- PHP >= 7.4
- MySQL >= 5.7
- Composer
- Web server (Apache/Nginx)

### Các Bước

1. Clone repository:
```bash
git clone https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep.git
cd KhoaLuanTotNghiep
```

2. Cài đặt dependencies:
```bash
composer install
```

3. Import database:
```bash
mysql -u username -p database_name < hanhphuc.sql
```

4. Cấu hình kết nối database trong `config.php`

5. Chạy ứng dụng trên web server

## Chức Năng Chính

### Dành Cho Bệnh Nhân
- Đăng ký tài khoản và quản lý hồ sơ
- Đặt lịch khám bệnh với bác sĩ
- Xem hồ sơ bệnh án điện tử
- Đặt lịch xét nghiệm
- Xem kết quả xét nghiệm
- Chat trực tuyến với bác sĩ
- Quản lý hồ sơ người thân (giám hộ)

### Dành Cho Bác Sĩ/Chuyên Gia
- Quản lý lịch làm việc
- Xem danh sách bệnh nhân
- Khám bệnh và ghi hồ sơ
- Kê đơn thuốc
- Yêu cầu xét nghiệm
- Chat với bệnh nhân

### Dành Cho Quản Trị Viên
- Quản lý người dùng
- Quản lý chuyên khoa
- Quản lý thuốc và xét nghiệm
- Xem báo cáo và thống kê

## Database Schema

Hệ thống sử dụng 31 bảng chính:

**Core Tables:**
- `nguoidung`, `taikhoan`, `vaitro`, `trangthai`

**Medical Staff:**
- `bacsi`, `chuyengia`, `nhanvien`

**Patient Management:**
- `benhnhan`, `hosobenhan`, `chitiethoso`

**Appointments:**
- `phieukhambenh`, `lichlamviec`, `khunggiokhambenh`

**Lab Tests:**
- `lichxetnghiem`, `loaixetnghiem`, `ketquaxetnghiem`

**Prescriptions:**
- `donthuoc`, `chitietdonthuoc`, `thuoc`

Xem chi tiết trong [Sơ Đồ Domain](./docs/domain-diagram.md).

## API Endpoints

### Authentication
- `POST /login` - Đăng nhập
- `POST /logout` - Đăng xuất
- `POST /register` - Đăng ký

### Appointments
- `GET /api/doctors` - Danh sách bác sĩ
- `POST /api/appointments` - Đặt lịch khám
- `GET /api/appointments/{id}` - Chi tiết lịch khám

### Medical Records
- `GET /api/medical-records` - Hồ sơ bệnh án
- `POST /api/prescriptions` - Tạo đơn thuốc
- `GET /api/lab-results` - Kết quả xét nghiệm

## Đóng Góp

Để đóng góp vào dự án:

1. Fork repository
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

## Quy Tắc Nghiệp Vụ Quan Trọng

1. **Giám hộ**: Một người giám hộ chỉ được quản lý tối đa 4 hồ sơ
2. **Đặt lịch**: Chỉ đặt được khi bác sĩ có lịch làm việc
3. **Xét nghiệm**: Phải có chỉ định từ bác sĩ
4. **Đơn thuốc**: Chỉ kê sau khi có chẩn đoán

## License

Dự án Khóa Luận Tốt Nghiệp - Bệnh Viện Hạnh Phúc

## Liên Hệ

- Email: dangthinhunghquynh19@gmail.com
- Repository: https://github.com/DangThiNhuQuynh19/KhoaLuanTotNghiep

---

© 2024 Hệ Thống Quản Lý Bệnh Viện Hạnh Phúc
