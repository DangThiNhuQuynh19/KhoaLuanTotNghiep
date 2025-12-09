# Sitemap - Sơ đồ cây phân cấp theo Role

## Cấu trúc hệ thống theo vai trò

```
HỆ THỐNG BỆNH VIỆN HẠNH PHÚC
│
├── 👤 BỆNH NHÂN (Benhnhan) - Role: Guest/Patient
│   ├── 🏠 Trang chủ (trangchu)
│   ├── 📝 Đăng ký (dangky)
│   ├── 🔐 Đăng nhập (dangnhap)
│   ├── 🔓 Đăng xuất (dangxuat)
│   ├── 👨‍⚕️ Bác sĩ (bacsi)
│   │   └── Chi tiết bác sĩ (chitietbacsi)
│   ├── 🎓 Chuyên gia (chuyengia)
│   │   └── Chi tiết chuyên gia (chitietchuyengia)
│   ├── 🏥 Chuyên khoa (chuyenkhoa)
│   │   └── Chi tiết chuyên khoa (chitietchuyenkhoa)
│   ├── 📅 Đặt lịch khám (datlichkham)
│   ├── 📋 Lịch hẹn (lichhen)
│   ├── 🧪 Lịch xét nghiệm (lichxetnghiem)
│   ├── 📁 Hồ sơ bệnh án điện tử (hosobenhandientu)
│   │   ├── Tạo hồ sơ (taohoso)
│   │   ├── Sửa hồ sơ (suahoso)
│   │   ├── Xóa hồ sơ (xoahoso)
│   │   └── Chi tiết hồ sơ (chitiethosobenhandientu)
│   ├── 💬 Tin nhắn (tinnhan)
│   ├── 💳 Thanh toán (thanhtoan)
│   │   ├── VNPay (vnpay)
│   │   └── Kết quả thanh toán (paymentresult)
│   ├── 💰 Ví (vi)
│   ├── 📰 Blog/Tin tức (blog, tintuc)
│   ├── ℹ️ Về chúng tôi (vechungtoi, gioithieu)
│   ├── 📞 Liên hệ (lienhe)
│   ├── ⚙️ Cài đặt (caidat)
│   └── 🔑 Đăng nhập Google (logingoogle)
│
├── 👨‍⚕️ BÁC SĨ (Bacsi) - Role: 2
│   ├── 🏠 Trang chủ (trangchu)
│   ├── 👥 Bệnh nhân (benhnhan)
│   │   ├── Chi tiết bệnh nhân (chitietbenhnhan)
│   │   └── Chi tiết hồ sơ (chitiethoso)
│   ├── 📅 Lịch hẹn trực tuyến (lichhentructuyen)
│   ├── 📅 Lịch hẹn trực tiếp (lichhentructiep)
│   ├── 🗓️ Đặt lịch khám (datlich)
│   ├── 🧪 Đặt lịch xét nghiệm (datlichxetnghiem)
│   ├── 🔬 Xét nghiệm (xetnghiem)
│   │   └── Kết quả xét nghiệm (ketquaxetnghiem)
│   ├── 📋 Tạo hồ sơ (taohoso)
│   ├── 💊 Tạo đơn thuốc (taodonthuoc)
│   ├── 📝 Cập nhật phiếu khám bệnh (update_phieukhambenh)
│   ├── 📆 Xem lịch làm việc (xemlichlamviec)
│   ├── 💬 Tin nhắn (tinnhan)
│   ├── 👤 Hồ sơ (hoso)
│   └── 🔓 Đăng xuất (dangxuat)
│
├── 🎓 CHUYÊN GIA (Chuyengia) - Role: 3
│   ├── 🏠 Trang chủ (trangchu)
│   ├── 👥 Bệnh nhân (benhnhan)
│   │   ├── Chi tiết bệnh nhân (chitietbenhnhan)
│   │   └── Chi tiết hồ sơ (chitiethoso)
│   ├── 📅 Lịch hẹn trực tuyến (lichhentructuyen)
│   ├── 📅 Lịch hẹn trực tiếp (lichhentructiep)
│   ├── 📅 Chi tiết lịch hẹn (chitietlichhen)
│   ├── 🗓️ Đặt lịch chuyên gia (datlichcg)
│   ├── 📋 Tạo hồ sơ (taohoso)
│   ├── 📝 Cập nhật phiếu khám bệnh (update_phieukhambenh)
│   ├── 📆 Xem lịch làm việc (xemlichlamviec)
│   ├── 💬 Tin nhắn (tinnhan)
│   ├── 👤 Hồ sơ (hoso)
│   └── 🔓 Đăng xuất (dangxuat)
│
├── 🧑‍💼 NHÂN VIÊN TIẾP TÂN (Nhanvientieptan) - Role: 4
│   ├── 🏠 Trang chủ (trangchu)
│   ├── 👨‍⚕️ Bác sĩ (bacsi)
│   │   └── Chi tiết bác sĩ (chitietbacsi)
│   ├── 🎓 Chuyên gia (chuyengia)
│   │   └── Chi tiết chuyên gia (chitietchuyengia)
│   ├── 📅 Đặt lịch khám (datlichkham)
│   ├── 📋 Lịch hẹn (lichhen)
│   │   ├── Sửa lịch hẹn (sualichhen)
│   │   ├── Hủy lịch hẹn (huylichhen)
│   │   └── Xử lý đặt lịch (xulydatlich)
│   ├── 📆 Lịch cá nhân (lichcanhan)
│   ├── 👥 Nhân viên (nhanvien)
│   ├── ℹ️ Thông tin (thongtin)
│   │   └── Sửa thông tin (suathongtin)
│   └── 🔓 Đăng xuất (dangxuat)
│
├── 🔬 NHÂN VIÊN XÉT NGHIỆM (Nhanvienxetnghiem) - Role: 5
│   ├── 🏠 Trang chủ (trangchu)
│   ├── 👁️ Xem chi tiết (xemchitiet)
│   ├── ✏️ Chỉnh sửa kết quả (chinhsua)
│   └── 🔓 Đăng xuất (dangxuat)
│
├── 👨‍💼 QUẢN LÝ (Quanly) - Role: 7
│   ├── 🏠 Trang chủ (trangchu)
│   ├── 👥 Nhân viên (nhanvien)
│   │   └── Chi tiết nhân viên (chitietnhanvien)
│   ├── 👨‍⚕️ Bác sĩ
│   │   ├── Thêm bác sĩ (thembacsi)
│   │   ├── Sửa bác sĩ (suabacsi)
│   │   └── Chi tiết bác sĩ (chitietbacsi)
│   ├── 🎓 Chuyên gia
│   │   ├── Thêm chuyên gia (themchuyengia)
│   │   ├── Sửa chuyên gia (suachuyengia)
│   │   └── Chi tiết chuyên gia (chitietchuyengia)
│   ├── 📆 Lịch làm việc (lichlamviec)
│   ├── 🗓️ Xếp lịch (xeplich)
│   └── 🔓 Đăng xuất (dangxuat)
│
└── 🔐 ADMIN (Admin) - Role: 6
    ├── 🏠 Trang chủ (trangchu)
    ├── 👥 Quản lý nhân sự (quanlynhansu)
    ├── 📝 Quản lý bài viết (quanlybaiviet)
    ├── 🔑 Phân quyền (phanquyen)
    └── 🔓 Đăng xuất (dangxuat)
```

## Chi tiết vai trò

### 1. Bệnh nhân (Patient)
- **Mô tả**: Người dùng có thể đăng ký, đặt lịch khám, xem thông tin bác sĩ/chuyên gia, quản lý hồ sơ bệnh án
- **Quyền truy cập**: Public (guest) hoặc đăng nhập
- **Trang chính**: Trang chủ, Đặt lịch khám, Hồ sơ bệnh án điện tử, Thanh toán

### 2. Bác sĩ (Doctor) - Role: 2
- **Mô tả**: Bác sĩ quản lý bệnh nhân, tạo đơn thuốc, đặt lịch xét nghiệm, xem kết quả xét nghiệm
- **Quyền truy cập**: Đăng nhập với vai trò bác sĩ
- **Trang chính**: Bệnh nhân, Lịch hẹn, Xét nghiệm, Đơn thuốc

### 3. Chuyên gia (Specialist) - Role: 3
- **Mô tả**: Chuyên gia y tế tư vấn cho bệnh nhân, quản lý lịch hẹn
- **Quyền truy cập**: Đăng nhập với vai trò chuyên gia
- **Trang chính**: Bệnh nhân, Lịch hẹn, Tư vấn

### 4. Nhân viên tiếp tân (Receptionist) - Role: 4
- **Mô tả**: Nhân viên tiếp nhận và xử lý đặt lịch khám cho bệnh nhân
- **Quyền truy cập**: Đăng nhập với vai trò nhân viên tiếp tân
- **Trang chính**: Đặt lịch khám, Lịch hẹn, Quản lý lịch

### 5. Nhân viên xét nghiệm (Lab Technician) - Role: 5
- **Mô tả**: Nhân viên xét nghiệm cập nhật kết quả xét nghiệm
- **Quyền truy cập**: Đăng nhập với vai trò nhân viên xét nghiệm
- **Trang chính**: Danh sách xét nghiệm, Cập nhật kết quả

### 6. Admin (Administrator) - Role: 6
- **Mô tả**: Quản trị viên hệ thống, quản lý nhân sự và phân quyền
- **Quyền truy cập**: Đăng nhập với vai trò admin
- **Trang chính**: Quản lý nhân sự, Phân quyền, Quản lý bài viết

### 7. Quản lý (Manager) - Role: 7
- **Mô tả**: Quản lý nhân viên, bác sĩ, chuyên gia và xếp lịch làm việc
- **Quyền truy cập**: Đăng nhập với vai trò quản lý
- **Trang chính**: Nhân viên, Xếp lịch, Lịch làm việc

## Cấu trúc thư mục

```
Views/
├── admin/           (Admin - Role 6)
├── bacsi/           (Bác sĩ - Role 2)
├── benhnhan/        (Bệnh nhân - Guest/Patient)
├── chuyengia/       (Chuyên gia - Role 3)
├── nhanvientieptan/ (Nhân viên tiếp tân - Role 4)
├── nhanvienxetnghiem/ (Nhân viên xét nghiệm - Role 5)
└── quanly/          (Quản lý - Role 7)
```

## Luồng chính

1. **Bệnh nhân đặt lịch** → Nhân viên tiếp tân xác nhận → Bác sĩ/Chuyên gia khám → Bác sĩ đặt lịch xét nghiệm (nếu cần) → Nhân viên xét nghiệm cập nhật kết quả → Bác sĩ xem kết quả và tạo đơn thuốc

2. **Quản lý nhân sự**: Admin/Quản lý thêm và quản lý tài khoản → Quản lý xếp lịch làm việc → Nhân viên/Bác sĩ làm việc theo lịch

## Ghi chú về thông báo (Notification)

- **Bác sĩ** nhận thông báo real-time khi nhân viên xét nghiệm cập nhật kết quả
- **Icon thông báo** trong header của bác sĩ cho phép xem và quản lý thông báo
- **WebSocket** được sử dụng cho thông báo real-time
