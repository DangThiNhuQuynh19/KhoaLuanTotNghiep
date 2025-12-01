# Sơ đồ Sequence - Mermaid Code

Dưới đây là code Mermaid cho các sơ đồ sequence. Bạn có thể copy và paste vào bất kỳ công cụ nào hỗ trợ Mermaid như:
- GitHub (trong file `.md`)
- GitLab
- Notion
- Mermaid Live Editor: https://mermaid.live/

---

## 1. Sơ đồ Sequence - Đăng ký tài khoản

```mermaid
sequenceDiagram
    title Sơ đồ Sequence - Chức năng Đăng ký

    actor User as Người dùng
    participant View as View<br/>(dangky/index.php)
    participant Controller as Controller<br/>(ctaikhoan.php)
    participant Model as Model<br/>(mtaikhoan.php)
    participant DB as Database<br/>(MySQL)

    rect rgb(240, 248, 255)
        Note over User, DB: Bắt đầu đăng ký
        User->>View: Truy cập trang đăng ký<br/>(index.php?action=dangky)
        View-->>User: Hiển thị form đăng ký
    end

    rect rgb(255, 250, 240)
        Note over User, DB: Nhập thông tin đăng ký
        User->>View: Nhập thông tin cá nhân<br/>(email, họ tên, ngày sinh, giới tính,<br/>CCCD, SĐT, nghề nghiệp, địa chỉ, mật khẩu)
        View->>View: Validate dữ liệu client-side<br/>(JavaScript validation)
        
        alt Dữ liệu không hợp lệ
            View-->>User: Hiển thị thông báo lỗi validation
        else Dữ liệu hợp lệ
            User->>View: Submit form (POST request)
        end
    end

    rect rgb(240, 255, 240)
        Note over User, DB: Xử lý đăng ký
        View->>View: Validate dữ liệu server-side<br/>(kiểm tra email, SĐT, CCCD, tuổi >= 18)
        
        alt Dữ liệu không hợp lệ
            View-->>User: Hiển thị thông báo lỗi (SweetAlert)
        else Dữ liệu hợp lệ
            View->>View: Mã hóa dữ liệu nhạy cảm<br/>(encryptData: email, SĐT, CCCD)
            View->>View: Upload ảnh CCCD (nếu có)
            View->>View: Tạo mã bệnh nhân (BN_XXXXXXXX)
            
            View->>Controller: dangkytk(mabenhnhan, email, hoten,<br/>ngaysinh, sdt, cccd, ...)
            Controller->>Model: dangkytk(...)
            
            Model->>DB: SELECT * FROM taikhoan WHERE tentk = ?
            DB-->>Model: Kết quả kiểm tra email
            
            alt Email đã tồn tại
                Model-->>Controller: return "email_ton_tai"
                Controller-->>View: return "email_ton_tai"
                View-->>User: Hiển thị thông báo<br/>"Email đã tồn tại" (SweetAlert)
            else Email chưa tồn tại
                Model->>Model: Hash mật khẩu (MD5)
                Model->>DB: INSERT INTO taikhoan<br/>(tentk, matkhau, mavaitro, matrangthai)
                DB-->>Model: OK (tài khoản đã tạo)
                
                Model->>DB: INSERT INTO nguoidung<br/>(manguoidung, hoten, ngaysinh, ...)
                DB-->>Model: OK (người dùng đã tạo)
                
                Model->>DB: INSERT INTO benhnhan<br/>(mabenhnhan, nghenghiep, ...)
                DB-->>Model: OK (bệnh nhân đã tạo)
                
                Model-->>Controller: return true
                Controller-->>View: return true
                View-->>User: Hiển thị thông báo<br/>"Đăng ký thành công!" (SweetAlert)
                User->>View: Click "OK"
                View-->>User: Chuyển hướng đến trang đăng nhập
            end
        end
    end
```

---

## 2. Sơ đồ Sequence - Đăng nhập

```mermaid
sequenceDiagram
    title Sơ đồ Sequence - Chức năng Đăng nhập

    actor User as Người dùng
    participant View as View<br/>(dangnhap/index.php)
    participant XuLy as Xử lý đăng nhập<br/>(xulydangnhap.php)
    participant Controller as Controller<br/>(ctaikhoan.php)
    participant Model as Model<br/>(mtaikhoan.php)
    participant DB as Database<br/>(MySQL)

    rect rgb(240, 248, 255)
        Note over User, DB: Bắt đầu đăng nhập
        User->>View: Truy cập trang đăng nhập<br/>(index.php?action=dangnhap)
        View-->>User: Hiển thị form đăng nhập
    end

    rect rgb(255, 250, 240)
        Note over User, DB: Nhập thông tin đăng nhập
        User->>View: Nhập email và mật khẩu
        User->>View: Click "Đăng nhập" (POST request)
        
        View->>XuLy: Submit form với<br/>(tentk, password)
        XuLy->>XuLy: Mã hóa email<br/>(encryptData(tentk))
        XuLy->>XuLy: Hash mật khẩu<br/>(MD5(password))
        
        XuLy->>Controller: dangnhap(tentk, password)
        Controller->>Model: select_01_taikhoan(tentk, matkhau)
        
        Model->>DB: SELECT * FROM taikhoan<br/>WHERE tentk = ? AND matkhau = ?
        DB-->>Model: Kết quả truy vấn
        Model-->>Controller: return $result (mysqli_result)
    end

    rect rgb(240, 255, 240)
        Note over User, DB: Xử lý kết quả đăng nhập
        
        alt Không tìm thấy tài khoản (num_rows == 0)
            Controller-->>View: echo alert("Email hoặc password không chính xác")
            View-->>User: Hiển thị thông báo lỗi<br/>(JavaScript alert)
            View-->>User: Quay lại trang đăng nhập<br/>(window.history.back())
        else Tài khoản tồn tại (num_rows > 0)
            Controller->>Controller: fetch_assoc() - Lấy thông tin user
            
            alt Tài khoản bị vô hiệu hóa (matrangthai != 1)
                Controller-->>View: echo alert("Tài khoản đã bị vô hiệu hóa")
                View-->>User: Hiển thị thông báo<br/>(JavaScript alert)
                View-->>User: Quay lại trang đăng nhập
            else Tài khoản hoạt động (matrangthai == 1)
                Controller->>Controller: Lưu SESSION<br/>$_SESSION['dangnhap'] = mavaitro<br/>$_SESSION['user'] = row
                Controller-->>View: header("Location:index.php?action=trangchu")
                View-->>User: Chuyển hướng đến trang chủ
            end
        end
    end

    rect rgb(255, 240, 245)
        Note over User, DB: Phân quyền theo vai trò
        Note right of Controller: mavaitro = 1: Bệnh nhân → Views/benhnhan<br/>mavaitro = 2: Bác sĩ → Views/bacsi<br/>mavaitro = 3: Chuyên gia → Views/chuyengia<br/>mavaitro = 4: Nhân viên tiếp tân<br/>mavaitro = 5: Nhân viên xét nghiệm<br/>mavaitro = 6: Admin → Views/admin<br/>mavaitro = 7: Quản lý → Views/quanly
    end

    rect rgb(245, 245, 255)
        Note over User, DB: Đăng nhập bằng Google (OAuth)
        User->>View: Click "Đăng nhập bằng Google"
        View->>View: Redirect đến Google OAuth<br/>($client->createAuthUrl())
        View-->>User: Chuyển hướng đến Google
        Note right of View: Xử lý OAuth trong:<br/>- config.php (Google API Client)<br/>- logingoogle/ (callback handler)
    end
```

---

## 3. Sơ đồ Sequence - Xem danh sách và Tìm kiếm bác sĩ

```mermaid
sequenceDiagram
    title Sơ đồ Sequence - Xem danh sách và Tìm kiếm bác sĩ

    actor User as Người dùng
    participant View as View<br/>(bacsi/index.php)
    participant Controller as Controller<br/>(cbacsi.php)
    participant ControllerKhoa as Controller Khoa<br/>(cchuyenkhoa.php)
    participant Model as Model<br/>(mbacsi.php)
    participant ModelKhoa as Model Khoa<br/>(mchuyenkhoa.php)
    participant DB as Database<br/>(MySQL)

    rect rgb(240, 248, 255)
        Note over User, DB: Truy cập trang danh sách bác sĩ
        User->>View: Truy cập trang danh sách bác sĩ<br/>(index.php?action=bacsi)
        
        View->>ControllerKhoa: getAllChuyenKhoa()
        ControllerKhoa->>ModelKhoa: dschuyenkhoa()
        ModelKhoa->>DB: SELECT * FROM chuyenkhoa
        DB-->>ModelKhoa: Kết quả danh sách chuyên khoa
        ModelKhoa-->>ControllerKhoa: return $tbl
        ControllerKhoa-->>View: return $dsKhoa
    end

    rect rgb(255, 250, 240)
        Note over User, DB: Xử lý tìm kiếm/lọc
        
        alt Không có điều kiện lọc (Xem tất cả)
            View->>Controller: getAllBacSi1()
            Controller->>Model: dsbacsi1()
            Model->>DB: SELECT * FROM bacsi<br/>JOIN chuyenkhoa, nguoidung, taikhoan, trangthai<br/>WHERE matrangthai = 1<br/>ORDER BY mabacsi ASC
            DB-->>Model: Danh sách bác sĩ đang hoạt động
            Model-->>Controller: return $tbl
            Controller-->>View: return $ds
        else Tìm theo tên bác sĩ (?name=...)
            View->>Controller: getBacSiByName(name)
            Controller->>Model: bacsitheoten(name)
            Model->>DB: SELECT * FROM bacsi<br/>JOIN chuyenkhoa, nguoidung<br/>WHERE hoten LIKE '%name%'
            DB-->>Model: Danh sách bác sĩ phù hợp
            Model-->>Controller: return $tbl
            Controller-->>View: return $ds
        else Lọc theo chuyên khoa (?khoa=...)
            View->>Controller: getBacSiByKhoa(khoa)
            Controller->>Model: bacsitheokhoa(id)
            Model->>DB: SELECT * FROM bacsi<br/>JOIN chuyenkhoa, nguoidung<br/>WHERE machuyenkhoa = 'id'
            DB-->>Model: Danh sách bác sĩ theo khoa
            Model-->>Controller: return $tbl
            Controller-->>View: return $ds
        else Tìm theo tên VÀ lọc theo khoa
            View->>Controller: getBacSiByTenAndKhoa(name, khoa)
            Controller->>Model: bacsitheotenandkhoa(name, id)
            Model->>DB: SELECT * FROM bacsi<br/>WHERE hoten LIKE '%name%'<br/>AND machuyenkhoa = 'id'
            DB-->>Model: Danh sách bác sĩ phù hợp
            Model-->>Controller: return $tbl
            Controller-->>View: return $ds
        end
    end

    rect rgb(240, 255, 240)
        Note over User, DB: Hiển thị kết quả
        
        alt Lỗi kết nối ($ds == -1)
            View-->>User: Hiển thị thông báo<br/>"Lỗi kết nối dữ liệu"
        else Không tìm thấy ($ds == 0)
            View-->>User: Hiển thị thông báo<br/>"Không có bác sĩ nào"
        else Có kết quả
            View->>View: Lặp qua danh sách bác sĩ<br/>(while $row = $ds->fetch_assoc())
            View-->>User: Hiển thị danh sách bác sĩ<br/>(Card: ảnh, tên, cấp bậc, chuyên khoa,<br/>mô tả, nút "Xem chi tiết")
        end
    end

    rect rgb(255, 240, 245)
        Note over User, DB: Xem chi tiết bác sĩ
        User->>View: Click "Xem chi tiết"<br/>(index.php?action=chitietbacsi&id=...)
        Note right of User: Chuyển sang màn hình<br/>chi tiết bác sĩ
    end
```

---

## Hướng dẫn sử dụng

### Trên GitHub
Tạo file `.md` và paste code Mermaid vào trong block:
````markdown
```mermaid
// code ở đây
```
````

### Trên Mermaid Live Editor
1. Truy cập https://mermaid.live/
2. Paste code Mermaid vào panel bên trái
3. Xem kết quả realtime bên phải
4. Export thành PNG/SVG nếu cần

### Trên Notion
1. Gõ `/code` để tạo code block
2. Chọn ngôn ngữ "Mermaid"
3. Paste code vào

### Trên VS Code
1. Cài extension "Markdown Preview Mermaid Support"
2. Mở file `.md` có chứa code Mermaid
3. Nhấn `Ctrl+Shift+V` để preview
