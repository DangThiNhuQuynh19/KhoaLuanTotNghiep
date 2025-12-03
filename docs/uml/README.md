# Use Case: Xem Chi Tiết Bác Sĩ (View Doctor Details)

## Mô tả
Use case này mô tả quá trình người dùng xem đầy đủ thông tin chi tiết của một bác sĩ, bao gồm chuyên khoa, kinh nghiệm, lịch khám, thông tin cá nhân.

## Thông tin Use Case

### Actor chính
- Khách vãng lai (Visitor)
- Bệnh nhân (Patient)

### Actor phụ
Không có

### Tiền điều kiện (Pre-condition)
Người dùng truy cập hoặc đăng nhập vào website tại màn hình Danh sách bác sĩ

### Hậu điều kiện (Post-condition)
Thông tin chi tiết về bác sĩ được hiển thị cho người dùng xem

## Luồng sự kiện

### Luồng sự kiện chính (Main Flow)

| Bước | Actor | System |
|------|-------|--------|
| 1 | Chọn "Xem chi tiết" tại 1 bác sĩ | |
| 2 | | Hiển thị thông tin chi tiết của bác sĩ bao gồm:<br>- Ảnh<br>- Họ và tên<br>- Chức danh, học vị<br>- Chuyên khoa<br>- Thông tin mô tả về kinh nghiệm (chế độ xem thu gọn)<br>- Lịch khám bệnh |
| 4 | Chọn "Xem thêm" | |
| 5 | | Hiển thị đầy đủ thông tin mô tả về bác sĩ |

### Luồng sự kiện thay thế (Alternative Flow)
- **2.1**: Thông báo "Không tìm thấy thông tin bác sĩ"

### Luồng sự kiện ngoại lệ (Exception Flow)
- **Bước 1**: Người dùng chuyển sang trang khác
- **Bước 2**: Use case kết thúc

## Sơ đồ UML Sequence

![Xem Chi Tiết Bác Sĩ - Sequence Diagram](Xem%20Chi%20Tiet%20Bac%20Si%20-%20Sequence%20Diagram.png)

### Thành phần trong sơ đồ

1. **Actor**: Khách vãng lai/Bệnh nhân
2. **Trang danh sách bác sĩ**: Hiển thị danh sách bác sĩ với nút "Xem chi tiết"
3. **Controller (cBacSi)**: Xử lý logic nghiệp vụ
4. **Model (mBacSi)**: Tương tác với cơ sở dữ liệu
5. **Database**: Lưu trữ thông tin bác sĩ
6. **Trang chi tiết bác sĩ**: Hiển thị thông tin chi tiết

### Mô tả luồng trong sơ đồ

#### Main Flow:
1. Người dùng chọn "Xem chi tiết" tại một bác sĩ
2. Controller gọi `getBacSiById(id)` để lấy thông tin
3. Model thực hiện truy vấn SQL JOIN để lấy đầy đủ thông tin
4. Database trả về kết quả
5. Trang chi tiết hiển thị thông tin (ảnh, tên, chuyên khoa, mô tả thu gọn)
6. Người dùng chọn "Xem thêm"
7. Trang hiển thị đầy đủ thông tin mô tả

#### Alternative Flow:
- Nếu không tìm thấy bác sĩ, hiển thị thông báo lỗi

#### Exception Flow:
- Người dùng có thể thoát ra bất cứ lúc nào

## Triển khai

### Files liên quan
- **Controller**: `/Controllers/cbacsi.php`
- **Model**: `/Models/mbacsi.php`
- **View (Danh sách)**: `/Views/benhnhan/pages/bacsi/index.php`
- **View (Chi tiết)**: `/Views/benhnhan/pages/chitietbacsi/index.php`

### Thay đổi đã thực hiện
1. Cho phép khách vãng lai xem chi tiết bác sĩ (không yêu cầu đăng nhập)
2. Yêu cầu đăng nhập khi đặt lịch khám
3. Hiển thị thông tin chi tiết với chế độ "Xem thêm/Thu gọn"

## Notes
- Tính năng này cho phép cả khách vãng lai và bệnh nhân xem thông tin bác sĩ
- Chỉ yêu cầu đăng nhập khi thực hiện đặt lịch khám
- Thông tin hiển thị bao gồm đầy đủ các thông tin chuyên môn của bác sĩ
