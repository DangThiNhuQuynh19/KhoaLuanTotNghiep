# UML Diagrams

Thư mục này chứa các sơ đồ UML cho hệ thống.

## Danh sách sơ đồ

### 1. Sequence Diagram - Đặt Lịch Khám (`sequence_datlichkham.puml`)

Sơ đồ tuần tự mô tả luồng chức năng đặt lịch khám bệnh trong hệ thống.

#### Mô tả luồng:

1. **Bước 1: Xem thông tin lịch khám**
   - Bệnh nhân chọn Bác sĩ/Chuyên gia, ngày khám, và khung giờ
   - Hệ thống lấy thông tin bác sĩ/chuyên gia từ database
   - Hệ thống lấy thông tin lịch khám (giờ khám, địa điểm)

2. **Bước 2: Lấy danh sách hồ sơ bệnh nhân**
   - Hệ thống lấy thông tin tài khoản bệnh nhân
   - Hệ thống lấy danh sách tất cả hồ sơ bệnh nhân (bao gồm người được giám hộ)

3. **Bước 3: Đặt lịch khám**
   - Bệnh nhân chọn hồ sơ và nhấn "Đặt lịch khám"
   - Hệ thống kiểm tra lịch trùng
   - Nếu không trùng, chuyển đến trang thanh toán

4. **Bước 4: Thanh toán và xác nhận**
   - Hiển thị thông tin thanh toán và mã QR
   - Bệnh nhân xác nhận thanh toán
   - Hệ thống lưu phiếu khám bệnh vào database
   - Thông báo kết quả (thành công/thất bại)

#### Thành phần:
- **Actor**: Bệnh nhân
- **Views**: DatLichKham, ThanhToan
- **Controllers**: cBacSi, cChuyenGia, cLichKham, cBenhNhan, cPhieuKhamBenh
- **Models**: mBacSi, mChuyenGia, mLichKham, mBenhNhan, mPhieuKhamBenh
- **Database**: MySQL

## Cách sử dụng

### Xem sơ đồ online

1. Truy cập [PlantUML Online Server](https://www.plantuml.com/plantuml/uml/)
2. Copy nội dung file `.puml` và paste vào ô nhập liệu
3. Sơ đồ sẽ được render tự động

### Sử dụng với Visual Studio Code

1. Cài đặt extension "PlantUML" bởi jebbs (ID: jebbs.plantuml)
2. Mở file `.puml`
3. Nhấn `Alt + D` để xem preview
4. Nhấn chuột phải > "Export Current Diagram" để xuất ảnh

### Sử dụng với command line

```bash
# Cài đặt PlantUML (cần Java)
# macOS
brew install plantuml

# Ubuntu/Debian
sudo apt-get install plantuml

# Tạo file PNG
plantuml sequence_datlichkham.puml

# Tạo file SVG
plantuml -tsvg sequence_datlichkham.puml
```

### Sử dụng với IntelliJ IDEA / PhpStorm

1. Cài đặt plugin "PlantUML integration"
2. Mở file `.puml`
3. Panel preview sẽ hiển thị sơ đồ tự động

## Định dạng file

Các file sơ đồ được viết bằng cú pháp PlantUML:
- Định dạng: `.puml`
- Encoding: UTF-8

## Tham khảo

- [PlantUML Documentation](https://plantuml.com/)
- [PlantUML Sequence Diagram](https://plantuml.com/sequence-diagram)
