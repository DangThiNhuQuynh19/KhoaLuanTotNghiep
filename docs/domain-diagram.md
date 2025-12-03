# Sơ Đồ Domain - Hệ Thống Quản Lý Bệnh Viện Hạnh Phúc

## Mô tả
Sơ đồ này mô tả các thực thể chính và mối quan hệ giữa chúng trong hệ thống quản lý bệnh viện Hạnh Phúc.

## Sơ Đồ Domain

```mermaid
erDiagram
    %% Core User Entities
    TAIKHOAN ||--o{ NGUOIDUNG : "đăng nhập"
    TAIKHOAN ||--|| VAITRO : "có vai trò"
    TAIKHOAN ||--|| TRANGTHAI : "có trạng thái"
    
    NGUOIDUNG ||--|| XAPHUONG : "thuộc"
    XAPHUONG ||--|| TINHTHANHPHO : "thuộc"
    
    %% Specialized Users
    NGUOIDUNG ||--o| BACSI : "là"
    NGUOIDUNG ||--o| BENHNHAN : "là"
    NGUOIDUNG ||--o| NHANVIEN : "là"
    NGUOIDUNG ||--o| CHUYENGIA : "là"
    
    BACSI ||--|| CHUYENKHOA : "thuộc"
    CHUYENKHOA ||--|| TRANGTHAI : "có trạng thái"
    
    CHUYENGIA ||--|| LINHVUC : "chuyên về"
    
    BENHNHAN ||--o| BENHNHAN : "được giám hộ bởi"
    BENHNHAN ||--|| TRANGTHAI : "có trạng thái"
    
    %% Medical Records
    BENHNHAN ||--o{ HOSOBENHAN : "có"
    HOSOBENHAN ||--o{ CHITIETHOSO : "bao gồm"
    CHITIETHOSO ||--|| NGUOIDUNG : "được khám bởi (bác sĩ/chuyên gia)"
    CHITIETHOSO ||--o| DONTHUOC : "có"
    
    DONTHUOC ||--o{ CHITIETDONTHUOC : "bao gồm"
    CHITIETDONTHUOC ||--|| THUOC : "chứa"
    
    %% Appointments
    BENHNHAN ||--o{ PHIEUKHAMBENH : "đặt khám"
    NGUOIDUNG ||--o{ PHIEUKHAMBENH : "khám (bác sĩ)"
    PHIEUKHAMBENH ||--|| KHUNGGIOKHAMBENH : "vào khung giờ"
    PHIEUKHAMBENH ||--|| TRANGTHAI : "có trạng thái"
    
    KHUNGGIOKHAMBENH ||--|| CALAMVIEC : "thuộc ca"
    
    %% Work Schedule
    NGUOIDUNG ||--o{ LICHLAMVIEC : "có lịch làm việc"
    LICHLAMVIEC ||--|| CALAMVIEC : "thuộc ca"
    LICHLAMVIEC ||--o| PHONG : "tại phòng"
    
    %% Lab Tests
    BENHNHAN ||--o{ LICHXETNGHIEM : "đặt xét nghiệm"
    LICHXETNGHIEM ||--|| LOAIXETNGHIEM : "loại"
    LICHXETNGHIEM ||--|| KHUNGGIOXETNGHIEM : "vào khung giờ"
    LICHXETNGHIEM ||--|| TRANGTHAI : "có trạng thái"
    LICHXETNGHIEM ||--|| HOSOBENHAN : "thuộc hồ sơ"
    LICHXETNGHIEM ||--o{ KETQUAXETNGHIEM : "có kết quả"
    
    LOAIXETNGHIEM ||--|| CHUYENKHOA : "thuộc"
    LOAIXETNGHIEM ||--|| DANHMUCXETNGHIEM : "thuộc danh mục"
    LOAIXETNGHIEM ||--|| TRANGTHAI : "có trạng thái"
    
    PHONG ||--o| DANHMUCXETNGHIEM : "dành cho"
    
    %% Payment
    LICHXETNGHIEM ||--o{ EMAIL_THANH_TOAN : "gửi email"
    LICHXETNGHIEM ||--o{ THONG_TIN_THANH_TOAN : "có thông tin thanh toán"

    %% Entity Details
    TAIKHOAN {
        varchar tentk PK
        varchar matkhau
        int mavaitro FK
        int matrangthai FK
        datetime ngaytao
        datetime ngaycapnhat
    }
    
    NGUOIDUNG {
        varchar manguoidung PK
        varchar hoten
        date ngaysinh
        enum gioitinh
        varchar cccd
        varchar cccd_matruoc
        varchar cccd_matsau
        varchar dantoc
        varchar sdt
        varchar emailcanhan
        varchar sonha
        int maxaphuong FK
        varchar email FK
    }
    
    BACSI {
        varchar mabacsi PK,FK
        text motabs
        text gioithieubs
        date ngaybatdau
        date ngayketthuc
        varchar imgbs
        int giakham
        int machuyenkhoa FK
        varchar capbac
    }
    
    BENHNHAN {
        varchar mabenhnhan PK,FK
        varchar nghenghiep
        text tiensubenhtatcuagiadinh
        text tiensubenhtatcuabenhnhan
        varchar manguoigiamho FK
        varchar moiquanhevoinguoithan
        int matrangthai FK
        varchar giaykhaisinh
    }
    
    CHUYENGIA {
        varchar machuyengia PK,FK
        int malinhvuc FK
        text gioithieucg
        text motacg
        date ngaybatdau
        date ngayketthuc
        varchar imgcg
        int giakham
    }
    
    PHIEUKHAMBENH {
        varchar maphieukhambenh PK
        date ngaykham
        int makhunggiokb FK
        varchar mabacsi FK
        varchar mabenhnhan FK
        int matrangthai FK
    }
    
    HOSOBENHAN {
        int mahoso PK
        varchar mabenhnhan FK
        timestamp ngaytao
        timestamp ngaycapnhat
    }
    
    CHITIETHOSO {
        int machitiethoso PK
        int mahoso FK
        varchar chandoan
        text ketluan
        varchar yeucauxetnghiem
        int madonthuoc FK
        varchar mabacsi FK
        timestamp ngaykham
    }
    
    LICHXETNGHIEM {
        int malichxetnghiem PK
        varchar mabenhnhan FK
        int maloaixetnghiem FK
        date ngayhen
        int makhunggio FK
        int matrangthai FK
        int mahoso FK
        varchar qr
    }
    
    DONTHUOC {
        int madonthuoc PK
        varchar ghichu
    }
    
    CHITIETDONTHUOC {
        int machitietdonthuoc PK
        int madonthuoc FK
        int mathuoc FK
        int soluong
        varchar lieudung
        varchar cachdung
    }
    
    CHUYENKHOA {
        int machuyenkhoa PK
        varchar tenchuyenkhoa
        text mota
        text dichvu
        int matrangthai FK
        varchar hinhanh
    }
    
    LOAIXETNGHIEM {
        int maloaixetnghiem PK
        varchar tenxetnghiem
        text mota
        int giatien
        int machuyenkhoa FK
        int madanhmuc FK
        int matrangthai FK
    }
    
    TRANGTHAI {
        int matrangthai PK
        varchar tentrangthai
    }
    
    VAITRO {
        int mavaitro PK
        varchar tenvaitro
    }
```

## Giải Thích Các Thực Thể Chính

### 1. Quản Lý Người Dùng
- **TAIKHOAN**: Tài khoản đăng nhập của người dùng
- **NGUOIDUNG**: Thông tin chung của tất cả người dùng trong hệ thống
- **VAITRO**: Phân quyền (Bệnh nhân, Bác sĩ, Nhân viên, Quản trị viên)
- **TRANGTHAI**: Trạng thái hoạt động/khóa của tài khoản

### 2. Người Dùng Chuyên Biệt
- **BACSI**: Bác sĩ với thông tin chuyên khoa, giá khám, kinh nghiệm
- **BENHNHAN**: Bệnh nhân với tiền sử bệnh, người giám hộ (cho trẻ em/người cao tuổi)
- **CHUYENGIA**: Chuyên gia tư vấn trong các lĩnh vực đặc biệt
- **NHANVIEN**: Nhân viên hành chính

### 3. Hồ Sơ Bệnh Án
- **HOSOBENHAN**: Hồ sơ bệnh án tổng hợp của bệnh nhân
- **CHITIETHOSO**: Chi tiết từng lần khám (chẩn đoán, kết luận, bác sĩ khám)
- **DONTHUOC**: Đơn thuốc kê cho bệnh nhân
- **CHITIETDONTHUOC**: Chi tiết các loại thuốc trong đơn

### 4. Đặt Lịch Khám
- **PHIEUKHAMBENH**: Phiếu đặt lịch khám bệnh
- **KHUNGGIOKHAMBENH**: Khung giờ khám (7h-8h, 8h-9h, v.v.)
- **CALAMVIEC**: Ca làm việc (sáng, chiều, tối)
- **LICHLAMVIEC**: Lịch làm việc của bác sĩ/chuyên gia

### 5. Xét Nghiệm
- **LICHXETNGHIEM**: Lịch hẹn xét nghiệm của bệnh nhân
- **LOAIXETNGHIEM**: Các loại xét nghiệm (xét nghiệm máu, tim mạch, v.v.)
- **DANHMUCXETNGHIEM**: Danh mục phân loại xét nghiệm
- **KETQUAXETNGHIEM**: Kết quả xét nghiệm
- **KHUNGGIOXETNGHIEM**: Khung giờ xét nghiệm

### 6. Địa Chỉ
- **TINHTHANHPHO**: Tỉnh/Thành phố
- **XAPHUONG**: Xã/Phường/Quận

### 7. Khác
- **CHUYENKHOA**: Các chuyên khoa (Nhi, Tim mạch, Thần kinh, v.v.)
- **LINHVUC**: Lĩnh vực chuyên môn của chuyên gia
- **PHONG**: Phòng khám/phòng xét nghiệm
- **THUOC**: Thông tin thuốc

## Luồng Hoạt Động Chính

### 1. Đăng Ký Và Đăng Nhập
```
TAIKHOAN → NGUOIDUNG → [BACSI | BENHNHAN | NHANVIEN | CHUYENGIA]
```

### 2. Đặt Lịch Khám
```
BENHNHAN → PHIEUKHAMBENH → KHUNGGIOKHAMBENH → BACSI/CHUYENGIA
```

### 3. Khám Bệnh
```
BENHNHAN → HOSOBENHAN → CHITIETHOSO → DONTHUOC → THUOC
```

### 4. Xét Nghiệm
```
BENHNHAN → LICHXETNGHIEM → LOAIXETNGHIEM → KETQUAXETNGHIEM
```

### 5. Thanh Toán
```
LICHXETNGHIEM → EMAIL_THANH_TOAN/THONG_TIN_THANH_TOAN
```

## Các Mối Quan Hệ Đặc Biệt

1. **Người Giám Hộ**: Bệnh nhân có thể là người giám hộ cho bệnh nhân khác (trẻ em hoặc người cao tuổi)
2. **Bác Sĩ/Chuyên Gia**: Cả hai đều có thể khám bệnh nhân, được phân biệt bởi vai trò trong CHITIETHOSO
3. **Lịch Làm Việc**: Bác sĩ và chuyên gia có lịch làm việc riêng, liên kết với khung giờ khám
4. **Trạng Thái**: Nhiều thực thể sử dụng chung bảng TRANGTHAI để quản lý trạng thái

## Quy Tắc Nghiệp Vụ

1. Một người giám hộ chỉ được tạo tối đa 4 hồ sơ bệnh nhân
2. Chỉ được tạo hồ sơ cho trẻ em dưới 18 tuổi hoặc người già trên 60 tuổi khi có người giám hộ
3. Bác sĩ/Chuyên gia phải có lịch làm việc trước khi bệnh nhân có thể đặt lịch khám
4. Mỗi phiếu khám bệnh phải có khung giờ cụ thể và không trùng lặp
5. Hồ sơ bệnh án được tạo tự động khi bệnh nhân đăng ký lần đầu
6. Đơn thuốc chỉ được kê sau khi có chẩn đoán trong chi tiết hồ sơ
