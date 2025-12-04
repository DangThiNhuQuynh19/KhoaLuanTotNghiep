# USE CASE: TẠO ĐơN THUỐC

## 1. Thông tin chung

### 1.1. Tên Use Case
**Tạo đơn thuốc** (Create Prescription)

### 1.2. Mã Use Case
UC-DT-001

### 1.3. Tác nhân (Actors)
- **Bác sĩ** (Doctor) - Người thực hiện chính
- **Chuyên gia y tế** (Medical Expert) - Người thực hiện chính
- **Hệ thống** (System) - Hỗ trợ lưu trữ và xử lý dữ liệu

### 1.4. Mô tả ngắn
Use case này cho phép bác sĩ hoặc chuyên gia y tế tạo đơn thuốc điện tử cho bệnh nhân sau khi khám bệnh. Đơn thuốc bao gồm danh sách các loại thuốc cần dùng, liều dùng, thời gian uống và số ngày uống.

### 1.5. Mức độ ưu tiên
**CAO** - Đây là chức năng cốt lõi của hệ thống quản lý bệnh án điện tử

---

## 2. Điều kiện tiên quyết (Preconditions)

1. Bác sĩ/Chuyên gia đã đăng nhập vào hệ thống
2. Bệnh nhân đã có hồ sơ bệnh án trong hệ thống (đã tạo `hosobenhan`)
3. Bác sĩ đang xem chi tiết hồ sơ bệnh án của bệnh nhân cần kê đơn thuốc
4. Hệ thống đã có danh sách thuốc trong cơ sở dữ liệu (bảng `thuoc`)
5. Bác sĩ có quyền truy cập và chỉnh sửa hồ sơ bệnh án

---

## 3. Điều kiện sau (Postconditions)

### 3.1. Khi thành công
1. Một bản ghi mới được tạo trong bảng `donthuoc` với ngày tạo đơn thuốc
2. Các bản ghi chi tiết thuốc được tạo trong bảng `chitietdonthuoc`
3. Đơn thuốc được liên kết với hồ sơ bệnh án qua bảng `chitiethoso`
4. Bệnh nhân có thể xem đơn thuốc của mình trong phần chi tiết hồ sơ bệnh án
5. Thông báo thành công hiển thị cho bác sĩ

### 3.2. Khi thất bại
1. Không có bản ghi mới được tạo trong cơ sở dữ liệu
2. Hiển thị thông báo lỗi phù hợp cho bác sĩ
3. Dữ liệu đã nhập được giữ lại để bác sĩ có thể điều chỉnh

---

## 4. Luồng sự kiện chính (Main Flow)

### Bước 1: Truy cập chức năng tạo đơn thuốc
1. Bác sĩ mở trang chi tiết hồ sơ bệnh án của bệnh nhân (URL: `Views/bacsi/pages/chitiethoso/index.php?mahoso={mahoso}`)
2. Hệ thống hiển thị thông tin bệnh nhân và các tab cập nhật
3. Bác sĩ chọn tab "Thêm đơn thuốc" (update-prescription)

### Bước 2: Nhập thông tin thuốc
1. Hệ thống hiển thị form nhập thông tin thuốc với các trường:
   - **Tên thuốc** (dropdown): Danh sách thuốc từ cơ sở dữ liệu
   - **Số lượng** (number): Số lượng thuốc cần kê
   - **Liều dùng** (text): Ví dụ "3 lần/ngày", "2 viên/lần"
   - **Số ngày uống** (text): Ví dụ "7 ngày", "14 ngày"
   - **Thời gian uống** (text): Ví dụ "Sau ăn", "Trước ăn", "Trước khi ngủ"

2. Bác sĩ chọn thuốc từ dropdown list
3. Bác sĩ nhập số lượng, liều dùng, số ngày uống và thời gian uống
4. Bác sĩ nhấn nút "Thêm vào đơn thuốc"

### Bước 3: Quản lý danh sách thuốc trong đơn
1. Hệ thống thêm thuốc vào bảng danh sách thuốc tạm thời (JavaScript array `medications[]`)
2. Hệ thống hiển thị bảng danh sách thuốc đã thêm với các cột:
   - STT
   - Tên thuốc
   - Số lượng
   - Liều dùng
   - Số ngày uống
   - Thời gian uống
   - Thao tác (Xóa)

3. Bác sĩ có thể:
   - Thêm nhiều loại thuốc khác nhau (lặp lại Bước 2)
   - Xóa thuốc khỏi danh sách nếu cần
   - Xem lại toàn bộ danh sách thuốc đã chọn

### Bước 4: Lưu đơn thuốc
1. Sau khi hoàn tất danh sách thuốc, bác sĩ nhấn nút "Cập nhật hồ sơ" (btnupdate)
2. Hệ thống gửi dữ liệu POST với:
   - `medications[]`: Mảng chứa thông tin các thuốc
   - `mahoso`: Mã hồ sơ bệnh án

### Bước 5: Xử lý và lưu trữ
1. **Controller** (`Views/bacsi/pages/chitiethoso/index.php`) nhận request:
   ```php
   if (isset($_POST['btnupdate'])) {
       if (isset($_POST['medications']) && is_array($_POST['medications'])) {
   ```

2. **Tạo đơn thuốc mới**:
   - Gọi `$cdonthuoc->create_donthuoc()`
   - Model (`mdonthuoc.php`) thực hiện:
     ```sql
     INSERT INTO donthuoc(ngaytaodonthuoc) VALUES(CURDATE())
     ```
   - Lấy `madonthuoc` vừa tạo

3. **Tạo chi tiết đơn thuốc**:
   - Với mỗi thuốc trong `medications[]`:
     - Gọi `$cchitietdongthuoc->create_chitietdonthuoc()`
     - Model (`mchitietdonthuoc.php`) thực hiện:
       ```sql
       INSERT INTO chitietdonthuoc(madonthuoc, mathuoc, lieudung, thoigianuong, songayuong)
       VALUES ('$madonthuoc', '$mathuoc', '$lieudung', '$thoigianuong', '$songayuong')
       ```

4. **Liên kết với hồ sơ bệnh án**:
   - Hệ thống tạo bản ghi trong `chitiethoso` liên kết `madonthuoc` với `mahoso`

### Bước 6: Xác nhận và hiển thị
1. Hệ thống hiển thị thông báo thành công
2. Trang chi tiết hồ sơ được làm mới
3. Đơn thuốc mới xuất hiện trong danh sách đơn thuốc của hồ sơ bệnh án

---

## 5. Luồng sự kiện phụ (Alternative Flows)

### 5.1. Luồng A1: Không chọn thuốc nào
**Điều kiện**: Bác sĩ nhấn "Cập nhật hồ sơ" mà chưa thêm thuốc nào

**Xử lý**:
1. Hệ thống kiểm tra `medications[]` rỗng hoặc không tồn tại
2. Hệ thống bỏ qua việc tạo đơn thuốc
3. Hồ sơ được cập nhật với các thông tin khác (nếu có)
4. Không có đơn thuốc mới được tạo

### 5.2. Luồng A2: Lỗi kết nối cơ sở dữ liệu
**Điều kiện**: Không thể kết nối đến database khi tạo đơn thuốc

**Xử lý**:
1. Model trả về `false`
2. Controller nhận kết quả lỗi
3. Đặt `$madonthuoc = null`
4. Hiển thị thông báo lỗi: "Không thể tạo đơn thuốc, vui lòng thử lại"
5. Dữ liệu không được lưu vào database

### 5.3. Luồng A3: Thuốc không tồn tại trong hệ thống
**Điều kiện**: `mathuoc` không tồn tại trong bảng `thuoc`

**Xử lý**:
1. Database constraint hoặc foreign key violation
2. Hệ thống bỏ qua thuốc đó hoặc hiển thị lỗi
3. Thông báo: "Thuốc không hợp lệ, vui lòng chọn lại"

### 5.4. Luồng A4: Thêm thuốc trùng lặp
**Điều kiện**: Bác sĩ thêm cùng một loại thuốc nhiều lần

**Xử lý**:
1. Hệ thống cho phép thêm (không kiểm tra trùng lặp ở client-side)
2. Cả hai bản ghi được lưu vào database
3. Bác sĩ có thể xóa bản ghi thừa trước khi lưu

### 5.5. Luồng A5: Xóa thuốc khỏi danh sách tạm
**Điều kiện**: Bác sĩ muốn xóa một thuốc đã thêm trước khi lưu

**Xử lý**:
1. Bác sĩ nhấn nút "Xóa" tại dòng thuốc cần xóa
2. JavaScript xóa phần tử khỏi mảng `medications[]`
3. Bảng danh sách được cập nhật lại
4. Nếu không còn thuốc nào, ẩn bảng danh sách

---

## 6. Luồng ngoại lệ (Exception Flows)

### 6.1. Ngoại lệ E1: Session hết hạn
**Xử lý**:
1. Hệ thống phát hiện session không hợp lệ
2. Redirect đến trang đăng nhập
3. Hiển thị thông báo: "Phiên làm việc đã hết hạn, vui lòng đăng nhập lại"

### 6.2. Ngoại lệ E2: Không có quyền truy cập hồ sơ
**Xử lý**:
1. Hệ thống kiểm tra quyền của bác sĩ
2. Nếu không có quyền, hiển thị trang lỗi 403
3. Thông báo: "Bạn không có quyền chỉnh sửa hồ sơ này"

### 6.3. Ngoại lệ E3: Dữ liệu không hợp lệ
**Xử lý**:
1. Validate dữ liệu input (số lượng phải là số dương, các trường text không được rỗng)
2. Hiển thị thông báo lỗi cụ thể
3. Yêu cầu bác sĩ sửa lại thông tin

### 6.4. Ngoại lệ E4: Transaction rollback
**Xử lý**:
1. Nếu tạo `donthuoc` thành công nhưng tạo `chitietdonthuoc` thất bại
2. Cần rollback để đảm bảo tính toàn vẹn dữ liệu
3. (Lưu ý: Code hiện tại chưa implement transaction)

---

## 7. Dữ liệu đầu vào (Inputs)

### 7.1. Thông tin hồ sơ
- **mahoso** (int): Mã định danh hồ sơ bệnh án
- Truyền qua URL parameter: `?mahoso={mahoso}`

### 7.2. Thông tin thuốc (cho mỗi loại thuốc)
| Tên trường | Kiểu dữ liệu | Bắt buộc | Mô tả | Ví dụ |
|------------|--------------|----------|-------|-------|
| mathuoc | int | Có | Mã thuốc từ dropdown | 11 |
| lieudung | varchar(200) | Có | Liều dùng thuốc | "3 lần/ngày, mỗi lần 2 viên" |
| thoigianuong | varchar(200) | Có | Thời gian uống | "Sau ăn 30 phút" |
| songayuong | int | Có | Số ngày cần uống | 7 |
| soluong | int | Không | Số lượng thuốc | 21 |

### 7.3. Format dữ liệu POST
```php
$_POST = [
    'btnupdate' => 'submit',
    'mahoso' => '1',
    'medications' => [
        [
            'mathuoc' => '11',
            'lieudung' => '3 lần/ngày',
            'thoigianuong' => 'Sau ăn',
            'songayuong' => '7'
        ],
        [
            'mathuoc' => '12',
            'lieudung' => '2 lần/ngày',
            'thoigianuong' => 'Trước ăn',
            'songayuong' => '14'
        ]
    ]
]
```

---

## 8. Dữ liệu đầu ra (Outputs)

### 8.1. Khi thành công
1. **Thông báo**: "Cập nhật hồ sơ thành công"
2. **Dữ liệu database**:
   - Bản ghi mới trong `donthuoc`:
     ```sql
     madonthuoc: (auto increment)
     ngaytaodonthuoc: CURDATE()
     ```
   - Các bản ghi mới trong `chitietdonthuoc`:
     ```sql
     machitietdonthuoc: (auto increment)
     madonthuoc: [ID từ bước trên]
     mathuoc: [từ input]
     lieudung: [từ input]
     thoigianuong: [từ input]
     songayuong: [từ input]
     ```
   - Bản ghi liên kết trong `chitiethoso`

3. **Hiển thị**: Đơn thuốc xuất hiện trong danh sách đơn thuốc của bệnh nhân

### 8.2. Khi thất bại
1. **Thông báo lỗi**: Message cụ thể tùy loại lỗi
2. **Trạng thái HTTP**: 200 (với message lỗi) hoặc 500 (server error)

---

## 9. Quy tắc nghiệp vụ (Business Rules)

### BR-1: Thời gian tạo đơn thuốc
- Ngày tạo đơn thuốc luôn là ngày hiện tại của hệ thống (CURDATE())
- Không cho phép back-date hoặc future-date

### BR-2: Thông tin thuốc bắt buộc
- Mọi thuốc trong đơn phải có đầy đủ: tên thuốc, liều dùng, thời gian uống, số ngày uống
- Số ngày uống phải là số nguyên dương

### BR-3: Quyền tạo đơn thuốc
- Chỉ Bác sĩ và Chuyên gia có quyền tạo đơn thuốc
- Bác sĩ chỉ được tạo đơn thuốc cho bệnh nhân trong chuyên khoa của mình

### BR-4: Trạng thái hồ sơ
- Chỉ tạo đơn thuốc cho hồ sơ đang hoạt động (không bị đóng)
- Hồ sơ phải đã có ít nhất một lần khám trước khi kê đơn

### BR-5: Thuốc hợp lệ
- Thuốc phải tồn tại trong danh mục thuốc của hệ thống (bảng `thuoc`)
- Thuốc không bị ngừng cung cấp hoặc cấm sử dụng

### BR-6: Số lượng thuốc trong đơn
- Một đơn thuốc có thể chứa từ 1 đến nhiều loại thuốc
- Không giới hạn số lượng loại thuốc trong một đơn

### BR-7: Chỉnh sửa đơn thuốc
- Sau khi đơn thuốc được tạo, không thể chỉnh sửa trực tiếp
- Nếu cần thay đổi, phải tạo đơn thuốc mới

---

## 10. Yêu cầu phi chức năng (Non-functional Requirements)

### 10.1. Hiệu năng
- Thời gian tạo đơn thuốc < 2 giây
- Hỗ trợ tạo đơn thuốc cho nhiều bệnh nhân đồng thời

### 10.2. Bảo mật
- Mã hóa kết nối SSL/TLS
- Validate và sanitize mọi input để tránh SQL Injection
- Kiểm tra quyền truy cập trước mỗi thao tác

### 10.3. Tính sẵn sàng
- Hệ thống phải hoạt động 24/7
- Downtime cho maintenance < 0.1%

### 10.4. Khả năng sử dụng
- Giao diện trực quan, dễ sử dụng
- Hỗ trợ tiếng Việt Unicode
- Responsive design cho các thiết bị khác nhau

### 10.5. Tính toàn vẹn dữ liệu
- Đảm bảo atomicity khi tạo đơn thuốc (tất cả hoặc không có gì được lưu)
- Backup dữ liệu định kỳ

---

## 11. Sơ đồ luồng hoạt động (Activity Diagram)

```
[Bắt đầu]
    ↓
[Bác sĩ đăng nhập vào hệ thống]
    ↓
[Mở chi tiết hồ sơ bệnh án]
    ↓
[Chọn tab "Thêm đơn thuốc"]
    ↓
[Hiển thị form nhập thông tin thuốc]
    ↓
┌─────────────────────────────────────┐
│  VÒNG LẶP: Thêm thuốc vào đơn       │
│                                      │
│  [Chọn thuốc từ dropdown]           │
│          ↓                           │
│  [Nhập liều dùng, thời gian, số ngày]│
│          ↓                           │
│  [Nhấn "Thêm vào đơn thuốc"]        │
│          ↓                           │
│  [Thuốc được thêm vào danh sách]    │
│          ↓                           │
│  <Còn thuốc cần thêm?>              │
│     Có → [Quay lại đầu vòng lặp]   │
│     Không → [Thoát vòng lặp]        │
└─────────────────────────────────────┘
    ↓
[Hiển thị bảng danh sách thuốc]
    ↓
[Bác sĩ xem lại và xác nhận]
    ↓
[Nhấn "Cập nhật hồ sơ"]
    ↓
<Có thuốc trong danh sách?>
    │
    ├─ Có → [Tạo bản ghi donthuoc]
    │           ↓
    │       [Lấy madonthuoc mới]
    │           ↓
    │       ┌──────────────────────────┐
    │       │ VÒNG LẶP: Với mỗi thuốc │
    │       │                          │
    │       │ [Tạo chitietdonthuoc]   │
    │       │         ↓                │
    │       │ [Lưu vào database]      │
    │       └──────────────────────────┘
    │           ↓
    │       [Liên kết với chitiethoso]
    │           ↓
    │       [Hiển thị thông báo thành công]
    │
    └─ Không → [Bỏ qua tạo đơn thuốc]
                ↓
            [Cập nhật hồ sơ với thông tin khác]
    ↓
[Làm mới trang chi tiết hồ sơ]
    ↓
[Kết thúc]
```

---

## 12. Cấu trúc cơ sở dữ liệu liên quan

### 12.1. Bảng `donthuoc`
```sql
CREATE TABLE `donthuoc` (
  `madonthuoc` int(11) NOT NULL AUTO_INCREMENT,
  `ngaytaodonthuoc` date NOT NULL,
  PRIMARY KEY (`madonthuoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 12.2. Bảng `chitietdonthuoc`
```sql
CREATE TABLE `chitietdonthuoc` (
  `machitietdonthuoc` int(11) NOT NULL AUTO_INCREMENT,
  `madonthuoc` int(11) DEFAULT NULL,
  `mathuoc` int(11) NOT NULL,
  `lieudung` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `thoigianuong` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `songayuong` int(11) NOT NULL,
  PRIMARY KEY (`machitietdonthuoc`),
  KEY `madonthuoc` (`madonthuoc`),
  KEY `mathuoc` (`mathuoc`),
  CONSTRAINT `fk_chitietdonthuoc_donthuoc` FOREIGN KEY (`madonthuoc`) REFERENCES `donthuoc` (`madonthuoc`),
  CONSTRAINT `fk_chitietdonthuoc_thuoc` FOREIGN KEY (`mathuoc`) REFERENCES `thuoc` (`mathuoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 12.3. Bảng `thuoc`
```sql
CREATE TABLE `thuoc` (
  `mathuoc` int(11) NOT NULL AUTO_INCREMENT,
  `tenthuoc` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `hoatchat` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `dangbaoche` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `donvi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `mota` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `ghichu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  PRIMARY KEY (`mathuoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### 12.4. Mối quan hệ
```
hosobenhan (1) ←→ (N) chitiethoso
chitiethoso (N) ←→ (1) donthuoc
donthuoc (1) ←→ (N) chitietdonthuoc
chitietdonthuoc (N) ←→ (1) thuoc
```

---

## 13. Các file code liên quan

### 13.1. Controllers
- **cdonthuoc.php**: Controller xử lý logic đơn thuốc
  - `create_donthuoc()`: Tạo đơn thuốc mới
  - `get_donthuoc_new()`: Lấy đơn thuốc mới nhất
  - `get_donthuoc_mahoso($mahoso)`: Lấy đơn thuốc theo hồ sơ

- **cchitietdonthuoc.php**: Controller xử lý chi tiết đơn thuốc
  - `create_chitietdonthuoc($madonthuoc, $mathuoc, $lieudung, $thoigianuong, $songayuong)`: Tạo chi tiết đơn thuốc
  - `get_chitietdonthuoc_madonthuoc($madonthuoc)`: Lấy chi tiết đơn thuốc

### 13.2. Models
- **mdonthuoc.php**: Model tương tác với bảng `donthuoc`
  - `insert_donthuoc()`: INSERT vào database
  - `select_donthuoc_new()`: SELECT đơn thuốc mới nhất
  - `select_donthuoc_mahoso($mahoso)`: SELECT đơn thuốc theo hồ sơ

- **mchitietdonthuoc.php**: Model tương tác với bảng `chitietdonthuoc`
  - `insert_chitietdonthuoc()`: INSERT chi tiết thuốc
  - `select_chitietdonthuoc_madonthuoc($madonthuoc)`: SELECT chi tiết thuốc

### 13.3. Views
- **Views/bacsi/pages/chitiethoso/index.php**: Giao diện chính tạo đơn thuốc
  - Form nhập thông tin thuốc
  - Bảng hiển thị danh sách thuốc
  - JavaScript xử lý thêm/xóa thuốc
  - Xử lý submit form

- **Views/bacsi/pages/taohoso/index.php**: Modal tạo đơn thuốc khi tạo hồ sơ mới

- **Views/benhnhan/pages/chitiethosobenhandientu/index.php**: Hiển thị đơn thuốc cho bệnh nhân xem

---

## 14. Test Cases

### TC-1: Tạo đơn thuốc thành công với 1 loại thuốc
**Điều kiện**: Bác sĩ đã đăng nhập, có hồ sơ hợp lệ
**Bước thực hiện**:
1. Mở chi tiết hồ sơ
2. Chọn tab "Thêm đơn thuốc"
3. Chọn thuốc "Paracetamol"
4. Nhập liều dùng: "3 lần/ngày"
5. Nhập số ngày: "7"
6. Nhập thời gian: "Sau ăn"
7. Nhấn "Thêm vào đơn thuốc"
8. Nhấn "Cập nhật hồ sơ"

**Kết quả mong đợi**: Đơn thuốc được tạo thành công, hiển thị trong danh sách

### TC-2: Tạo đơn thuốc với nhiều loại thuốc
**Điều kiện**: Bác sĩ đã đăng nhập, có hồ sơ hợp lệ
**Bước thực hiện**:
1. Thêm thuốc 1: Paracetamol - 3 lần/ngày - 7 ngày
2. Thêm thuốc 2: Amoxicillin - 2 lần/ngày - 5 ngày
3. Thêm thuốc 3: Vitamin C - 1 lần/ngày - 30 ngày
4. Nhấn "Cập nhật hồ sơ"

**Kết quả mong đợi**: Đơn thuốc chứa 3 loại thuốc được tạo thành công

### TC-3: Xóa thuốc khỏi danh sách trước khi lưu
**Điều kiện**: Đã thêm ít nhất 2 loại thuốc
**Bước thực hiện**:
1. Nhấn nút "Xóa" tại thuốc thứ 2
2. Kiểm tra danh sách
3. Nhấn "Cập nhật hồ sơ"

**Kết quả mong đợi**: Chỉ thuốc còn lại được lưu vào database

### TC-4: Không thêm thuốc nào và submit
**Điều kiện**: Bác sĩ đã đăng nhập, có hồ sơ hợp lệ
**Bước thực hiện**:
1. Mở chi tiết hồ sơ
2. Chọn tab "Thêm đơn thuốc"
3. Không thêm thuốc nào
4. Nhấn "Cập nhật hồ sơ"

**Kết quả mong đợi**: Không có đơn thuốc mới được tạo, hồ sơ được cập nhật bình thường

### TC-5: Nhập dữ liệu không hợp lệ
**Điều kiện**: Bác sĩ đã đăng nhập, có hồ sơ hợp lệ
**Bước thực hiện**:
1. Chọn thuốc
2. Bỏ trống trường "Liều dùng"
3. Nhấn "Thêm vào đơn thuốc"

**Kết quả mong đợi**: Hiển thị thông báo lỗi, yêu cầu nhập đầy đủ thông tin

---

## 15. Cải tiến đề xuất (Future Enhancements)

### 15.1. Validation nâng cao
- Kiểm tra tương tác thuốc (drug interaction)
- Cảnh báo khi kê thuốc bệnh nhân đã dị ứng
- Kiểm tra liều dùng theo độ tuổi và cân nặng

### 15.2. Quản lý đơn thuốc
- Cho phép chỉnh sửa đơn thuốc đã tạo
- Lịch sử các lần chỉnh sửa
- Tính năng copy đơn thuốc cũ

### 15.3. In và chia sẻ
- Xuất đơn thuốc ra PDF
- Gửi đơn thuốc qua email cho bệnh nhân
- In QR code để quét tại nhà thuốc

### 15.4. Thống kê và báo cáo
- Thống kê thuốc được kê nhiều nhất
- Báo cáo chi phí thuốc
- Phân tích xu hướng kê đơn

### 15.5. Tích hợp
- Tích hợp với hệ thống nhà thuốc
- Kết nối với bảo hiểm y tế
- Kiểm tra tồn kho thuốc thời gian thực

---

## 16. Phụ lục

### 16.1. Thuật ngữ (Glossary)
- **Đơn thuốc**: Prescription, chỉ định các loại thuốc bệnh nhân cần sử dụng
- **Liều dùng**: Dosage, số lượng thuốc cần dùng mỗi lần
- **Thời gian uống**: Time of administration, thời điểm trong ngày cần uống thuốc
- **Số ngày uống**: Duration, thời gian cần sử dụng thuốc
- **Hồ sơ bệnh án**: Medical record, hồ sơ lưu trữ thông tin bệnh lý của bệnh nhân

### 16.2. Tham chiếu
- Database schema: `hanhphuc.sql`
- Config file: `config.php`
- Main routing: `index.php`

### 16.3. Lịch sử thay đổi
| Phiên bản | Ngày | Người thực hiện | Nội dung thay đổi |
|-----------|------|-----------------|-------------------|
| 1.0 | 2025-12-04 | System | Tạo tài liệu use case ban đầu |

---

**Kết thúc tài liệu Use Case - Tạo Đơn Thuốc**
