# Sơ Đồ Domain Tổng Quan - Bệnh Viện Hạnh Phúc

## Sơ Đồ Tổng Quan (High-Level Domain View)

```mermaid
graph TB
    subgraph "Quản Lý Người Dùng"
        TK[Tài Khoản<br/>TAIKHOAN]
        ND[Người Dùng<br/>NGUOIDUNG]
        VT[Vai Trò<br/>VAITRO]
    end

    subgraph "Người Dùng Chuyên Môn"
        BS[Bác Sĩ<br/>BACSI]
        CG[Chuyên Gia<br/>CHUYENGIA]
        BN[Bệnh Nhân<br/>BENHNHAN]
        NV[Nhân Viên<br/>NHANVIEN]
    end

    subgraph "Chuyên Khoa & Lĩnh Vực"
        CK[Chuyên Khoa<br/>CHUYENKHOA]
        LV[Lĩnh Vực<br/>LINHVUC]
    end

    subgraph "Hồ Sơ Y Tế"
        HS[Hồ Sơ Bệnh Án<br/>HOSOBENHAN]
        CT[Chi Tiết Hồ Sơ<br/>CHITIETHOSO]
        DT[Đơn Thuốc<br/>DONTHUOC]
        CTDT[Chi Tiết Đơn Thuốc<br/>CHITIETDONTHUOC]
        T[Thuốc<br/>THUOC]
    end

    subgraph "Đặt Lịch Khám"
        PKB[Phiếu Khám Bệnh<br/>PHIEUKHAMBENH]
        KG[Khung Giờ<br/>KHUNGGIOKHAMBENH]
        CA[Ca Làm Việc<br/>CALAMVIEC]
        LLV[Lịch Làm Việc<br/>LICHLAMVIEC]
    end

    subgraph "Xét Nghiệm"
        LXN[Lịch Xét Nghiệm<br/>LICHXETNGHIEM]
        LXN_TYPE[Loại Xét Nghiệm<br/>LOAIXETNGHIEM]
        KQ[Kết Quả<br/>KETQUAXETNGHIEM]
        DM[Danh Mục<br/>DANHMUCXETNGHIEM]
    end

    subgraph "Địa Chỉ"
        TP[Tỉnh/TP<br/>TINHTHANHPHO]
        XP[Xã/Phường<br/>XAPHUONG]
    end

    %% Relationships
    TK -->|đăng nhập| ND
    TK -->|có vai trò| VT
    ND -->|thuộc| XP
    XP -->|thuộc| TP
    
    ND -->|là| BS
    ND -->|là| CG
    ND -->|là| BN
    ND -->|là| NV
    
    BS -->|thuộc| CK
    CG -->|chuyên về| LV
    
    BN -->|có| HS
    HS -->|bao gồm| CT
    CT -->|có| DT
    DT -->|bao gồm| CTDT
    CTDT -->|chứa| T
    
    BN -->|đặt khám| PKB
    BS -->|khám| PKB
    CG -->|khám| PKB
    PKB -->|vào khung giờ| KG
    KG -->|thuộc ca| CA
    
    BS -->|có lịch| LLV
    CG -->|có lịch| LLV
    LLV -->|thuộc ca| CA
    
    BN -->|đặt| LXN
    LXN -->|loại| LXN_TYPE
    LXN -->|có kết quả| KQ
    LXN_TYPE -->|thuộc| DM
    LXN_TYPE -->|thuộc| CK

    style TK fill:#e1f5ff
    style ND fill:#e1f5ff
    style BS fill:#fff4e1
    style CG fill:#fff4e1
    style BN fill:#ffe1e1
    style HS fill:#e1ffe1
    style PKB fill:#f0e1ff
    style LXN fill:#ffe1f0
```

## Sơ Đồ Luồng Hoạt Động

### Luồng 1: Bệnh Nhân Đặt Lịch Khám

```mermaid
sequenceDiagram
    participant BN as Bệnh Nhân
    participant PKB as Phiếu Khám Bệnh
    participant LLV as Lịch Làm Việc
    participant BS as Bác Sĩ/Chuyên Gia
    participant HS as Hồ Sơ Bệnh Án
    
    BN->>LLV: 1. Xem lịch làm việc
    LLV-->>BN: 2. Danh sách khung giờ khả dụng
    BN->>PKB: 3. Đặt lịch khám
    PKB-->>BS: 4. Thông báo lịch khám mới
    BS->>HS: 5. Khám và ghi hồ sơ
    HS-->>BN: 6. Kết quả khám
```

### Luồng 2: Khám Bệnh và Kê Đơn Thuốc

```mermaid
sequenceDiagram
    participant BN as Bệnh Nhân
    participant BS as Bác Sĩ
    participant CT as Chi Tiết Hồ Sơ
    participant DT as Đơn Thuốc
    participant LXN as Lịch Xét Nghiệm
    
    BN->>BS: 1. Đến khám
    BS->>CT: 2. Ghi chẩn đoán
    
    alt Cần xét nghiệm
        BS->>LXN: 3a. Yêu cầu xét nghiệm
        LXN-->>BN: 4a. Lịch xét nghiệm
    end
    
    alt Cần thuốc
        BS->>DT: 3b. Kê đơn thuốc
        DT-->>BN: 4b. Đơn thuốc
    end
    
    CT-->>BN: 5. Kết luận
```

### Luồng 3: Xét Nghiệm

```mermaid
sequenceDiagram
    participant BN as Bệnh Nhân
    participant LXN as Lịch Xét Nghiệm
    participant TT as Thanh Toán
    participant KQ as Kết Quả
    participant HS as Hồ Sơ
    
    BN->>LXN: 1. Đặt lịch xét nghiệm
    LXN->>TT: 2. Tạo thông tin thanh toán
    BN->>TT: 3. Thanh toán
    TT-->>LXN: 4. Xác nhận
    BN->>KQ: 5. Đến làm xét nghiệm
    KQ-->>BN: 6. Nhận kết quả
    KQ->>HS: 7. Lưu vào hồ sơ
```

## Sơ Đồ Domain Context

```mermaid
C4Context
    title Sơ Đồ Context - Hệ Thống Bệnh Viện Hạnh Phúc

    Person(benhnhan, "Bệnh Nhân", "Người đến khám và điều trị")
    Person(bacsi, "Bác Sĩ", "Khám và điều trị bệnh")
    Person(chuyengia, "Chuyên Gia", "Tư vấn chuyên môn")
    Person(nhanvien, "Nhân Viên", "Quản lý hành chính")
    
    System(hethong, "Hệ Thống Quản Lý<br/>Bệnh Viện", "Quản lý toàn bộ<br/>hoạt động bệnh viện")
    
    System_Ext(email, "Email System", "Gửi thông báo email")
    System_Ext(payment, "Payment Gateway", "Xử lý thanh toán")
    
    Rel(benhnhan, hethong, "Đặt lịch, xem hồ sơ")
    Rel(bacsi, hethong, "Khám bệnh, kê đơn")
    Rel(chuyengia, hethong, "Tư vấn, khám")
    Rel(nhanvien, hethong, "Quản lý dữ liệu")
    
    Rel(hethong, email, "Gửi email thông báo")
    Rel(hethong, payment, "Xử lý thanh toán")
```

## Các Thành Phần Chính

### 1. **Quản Lý Người Dùng** (User Management)
   - Xác thực và phân quyền
   - Quản lý thông tin cá nhân
   - Quản lý địa chỉ

### 2. **Quản Lý Hồ Sơ Y Tế** (Medical Records)
   - Hồ sơ bệnh án điện tử
   - Lịch sử khám bệnh
   - Đơn thuốc và thuốc

### 3. **Quản Lý Lịch Hẹn** (Appointment Management)
   - Đặt lịch khám bệnh
   - Lịch làm việc bác sĩ
   - Khung giờ và ca làm việc

### 4. **Quản Lý Xét Nghiệm** (Lab Management)
   - Đặt lịch xét nghiệm
   - Các loại xét nghiệm
   - Kết quả xét nghiệm

### 5. **Quản Lý Chuyên Khoa** (Department Management)
   - Các chuyên khoa
   - Bác sĩ theo chuyên khoa
   - Dịch vụ của từng khoa

## Các Ràng Buộc Domain

1. **Giám Hộ**: Một người giám hộ có thể quản lý tối đa 4 hồ sơ bệnh nhân (trẻ em < 18 tuổi hoặc người già > 60 tuổi)

2. **Lịch Làm Việc**: Bác sĩ/Chuyên gia chỉ có thể được đặt lịch khám trong khung giờ họ làm việc

3. **Trạng Thái**: Tất cả các thực thể quan trọng đều có trạng thái (đang hoạt động, đã khóa, đã hủy, v.v.)

4. **Phân Quyền**: Mỗi tài khoản có một vai trò xác định quyền truy cập

5. **Hồ Sơ Bệnh Án**: Mỗi bệnh nhân có một hoặc nhiều hồ sơ, mỗi hồ sơ có nhiều chi tiết khám

6. **Đơn Thuốc**: Đơn thuốc chỉ được tạo sau khi có chẩn đoán trong chi tiết hồ sơ

## Thuật Ngữ Domain (Ubiquitous Language)

| Tiếng Việt | Tiếng Anh | Định Nghĩa |
|------------|-----------|------------|
| Bệnh nhân | Patient | Người đến khám và điều trị tại bệnh viện |
| Bác sĩ | Doctor | Người có chuyên môn y khoa, khám và điều trị bệnh |
| Chuyên gia | Expert/Specialist | Người có chuyên môn sâu trong lĩnh vực cụ thể |
| Hồ sơ bệnh án | Medical Record | Tập hợp thông tin y tế của bệnh nhân |
| Phiếu khám bệnh | Medical Examination Form | Đơn đặt lịch khám với bác sĩ |
| Đơn thuốc | Prescription | Danh sách thuốc được kê bởi bác sĩ |
| Xét nghiệm | Lab Test | Các kiểm tra y tế để chẩn đoán |
| Chuyên khoa | Department | Phân loại theo lĩnh vực y tế (Nhi, Tim mạch, v.v.) |
| Người giám hộ | Guardian | Người chịu trách nhiệm cho bệnh nhân nhỏ tuổi/cao tuổi |
| Khung giờ khám | Time Slot | Khoảng thời gian cụ thể để khám bệnh |

