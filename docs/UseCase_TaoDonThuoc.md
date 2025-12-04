# ĐẶC TẢ USE CASE: TẠO ĐơN THUỐC

## 1. THÔNG TIN CHUNG

### 1.1. Mã Use Case
**UC-DT-001**

### 1.2. Tên Use Case
**Tạo Đơn Thuốc** (Create Prescription)

### 1.3. Mô tả ngắn
Use case này mô tả quy trình tạo đơn thuốc cho bệnh nhân sau khi bác sĩ hoặc chuyên gia khám bệnh và chẩn đoán. Đơn thuốc bao gồm thông tin về các loại thuốc được kê, liều dùng, thời gian uống và số ngày uống.

### 1.4. Mức độ ưu tiên
**Cao** - Đây là chức năng cốt lõi trong quy trình khám bệnh và điều trị.

### 1.5. Phạm vi
Hệ thống Quản lý Bệnh viện Hạnh Phúc

---

## 2. CÁC ACTOR (TÁC NHÂN)

### 2.1. Actor chính
- **Bác sĩ**: Người thực hiện khám bệnh và kê đơn thuốc cho bệnh nhân
- **Chuyên gia**: Người có chuyên môn cao, thực hiện tư vấn và kê đơn thuốc chuyên sâu

### 2.2. Actor phụ
- **Hệ thống Database**: Lưu trữ thông tin đơn thuốc, chi tiết đơn thuốc và thông tin thuốc

---

## 3. ĐIỀU KIỆN TIÊN QUYẾT (PRECONDITIONS)

### 3.1. Điều kiện bắt buộc
1. Bác sĩ/Chuyên gia đã đăng nhập vào hệ thống thành công
2. Bệnh nhân đã có hồ sơ bệnh án điện tử trong hệ thống
3. Đã có phiếu khám bệnh được tạo cho lần khám hiện tại
4. Bác sĩ/Chuyên gia có quyền truy cập chức năng "Tạo đơn thuốc"
5. Danh sách thuốc đã được cập nhật trong hệ thống

### 3.2. Điều kiện tùy chọn
1. Bệnh nhân đã có lịch sử các đơn thuốc trước đó (để tham khảo)
2. Có thông tin về tiền sử dị ứng thuốc của bệnh nhân

---

## 4. ĐIỀU KIỆN HẬU QUYẾT (POSTCONDITIONS)

### 4.1. Khi thành công
1. Đơn thuốc mới được tạo và lưu vào database với mã đơn thuốc duy nhất
2. Chi tiết các loại thuốc, liều dùng, cách dùng được lưu vào bảng `chitietdonthuoc`
3. Đơn thuốc được liên kết với hồ sơ bệnh án của bệnh nhân
4. Ngày tạo đơn thuốc được ghi nhận tự động
5. Thông báo xác nhận tạo đơn thuốc thành công hiển thị cho bác sĩ
6. Bệnh nhân có thể xem đơn thuốc trong hồ sơ bệnh án điện tử của mình

### 4.2. Khi thất bại
1. Hệ thống không tạo đơn thuốc mới
2. Không có dữ liệu nào được ghi vào database
3. Thông báo lỗi được hiển thị cho bác sĩ
4. Dữ liệu đã nhập vào form vẫn được giữ lại để bác sĩ có thể sửa và thử lại

---

## 5. LUỒNG SỰ KIỆN CHÍNH (MAIN FLOW)

### Bước 1: Khởi tạo tạo đơn thuốc
1. Bác sĩ/Chuyên gia đăng nhập vào hệ thống
2. Bác sĩ/Chuyên gia truy cập vào trang quản lý bệnh nhân
3. Bác sĩ/Chuyên gia chọn bệnh nhân cần kê đơn thuốc
4. Hệ thống hiển thị thông tin bệnh nhân:
   - Mã bệnh nhân
   - Họ tên
   - Ngày sinh
   - Giới tính
   - Số điện thoại
   - Tiền sử dị ứng (nếu có)

### Bước 2: Truy cập form tạo đơn thuốc
1. Bác sĩ/Chuyên gia chọn chức năng "Tạo đơn thuốc" hoặc "Hoàn tất khám"
2. Hệ thống hiển thị form tạo đơn thuốc với các trường thông tin:
   - Thông tin bệnh nhân (tự động điền)
   - Thông tin bác sĩ kê đơn (tự động điền)
   - Trường tìm kiếm thuốc
   - Danh sách thuốc đã chọn (ban đầu rỗng)
   - Nút "Thêm thuốc"
   - Nút "Lưu đơn thuốc"
   - Nút "Hủy"

### Bước 3: Thêm thuốc vào đơn
1. Bác sĩ/Chuyên gia tìm kiếm thuốc bằng cách:
   - Nhập tên thuốc vào ô tìm kiếm, hoặc
   - Chọn từ danh sách thuốc có sẵn
2. Hệ thống hiển thị danh sách thuốc phù hợp với:
   - Mã thuốc
   - Tên thuốc
   - Dạng bào chế
   - Hàm lượng
   - Đơn vị tính
3. Bác sĩ/Chuyên gia chọn thuốc cần kê
4. Hệ thống hiển thị form nhập thông tin chi tiết cho thuốc:
   - Liều dùng (VD: 1 viên, 2 viên, 5ml...)
   - Thời gian uống (VD: Sáng, Trưa, Tối, Trước ăn, Sau ăn...)
   - Số ngày uống (VD: 3 ngày, 5 ngày, 7 ngày...)
5. Bác sĩ/Chuyên gia nhập thông tin chi tiết
6. Bác sĩ/Chuyên gia nhấn "Thêm thuốc"
7. Hệ thống thêm thuốc vào danh sách thuốc đã chọn
8. Hệ thống hiển thị thuốc vừa thêm trong danh sách với đầy đủ thông tin
9. **Bác sĩ/Chuyên gia lặp lại bước 3 để thêm các thuốc khác (nếu cần)**

### Bước 4: Xem lại và chỉnh sửa danh sách thuốc
1. Bác sĩ/Chuyên gia xem lại danh sách thuốc đã thêm
2. Nếu cần chỉnh sửa:
   - Bác sĩ/Chuyên gia chọn nút "Sửa" trên thuốc cần điều chỉnh
   - Hệ thống hiển thị form chỉnh sửa với thông tin hiện tại
   - Bác sĩ/Chuyên gia cập nhật thông tin
   - Bác sĩ/Chuyên gia xác nhận thay đổi
3. Nếu cần xóa thuốc:
   - Bác sĩ/Chuyên gia chọn nút "Xóa" trên thuốc cần gỡ
   - Hệ thống yêu cầu xác nhận xóa
   - Bác sĩ/Chuyên gia xác nhận
   - Hệ thống xóa thuốc khỏi danh sách

### Bước 5: Lưu đơn thuốc
1. Sau khi hoàn tất thêm tất cả thuốc cần thiết, Bác sĩ/Chuyên gia nhấn "Lưu đơn thuốc"
2. Hệ thống kiểm tra tính hợp lệ:
   - Đơn thuốc phải có ít nhất 1 loại thuốc
   - Tất cả thông tin bắt buộc đã được điền đầy đủ
   - Liều dùng, thời gian uống, số ngày uống phải là giá trị hợp lệ
3. Nếu dữ liệu hợp lệ:
   - Hệ thống tạo bản ghi mới trong bảng `donthuoc` với:
     - `madonthuoc`: Tự động tăng (auto-increment)
     - `ngaytaodonthuoc`: Ngày hiện tại (CURDATE())
   - Hệ thống lấy `madonthuoc` vừa tạo
   - Với mỗi thuốc trong danh sách, hệ thống tạo bản ghi trong bảng `chitietdonthuoc`:
     - `machitietdonthuoc`: Tự động tăng
     - `madonthuoc`: Mã đơn thuốc vừa tạo
     - `mathuoc`: Mã thuốc được chọn
     - `lieudung`: Liều dùng đã nhập
     - `thoigianuong`: Thời gian uống đã nhập
     - `songayuong`: Số ngày uống đã nhập
   - Hệ thống liên kết đơn thuốc với hồ sơ bệnh án trong bảng `chitiethoso`
   - Hệ thống commit transaction
4. Hệ thống hiển thị thông báo thành công với mã đơn thuốc
5. Hệ thống chuyển hướng về trang chi tiết hồ sơ bệnh án hoặc danh sách bệnh nhân

### Bước 6: Kết thúc
Use case kết thúc thành công.

---

## 6. LUỒNG SỰ KIỆN THAY THẾ (ALTERNATIVE FLOWS)

### 6.1. AF-1: Hủy tạo đơn thuốc
**Điều kiện kích hoạt**: Tại bất kỳ bước nào trong quá trình tạo đơn thuốc, bác sĩ quyết định không tiếp tục

**Luồng sự kiện**:
1. Bác sĩ/Chuyên gia nhấn nút "Hủy" hoặc nút "Quay lại"
2. Hệ thống hiển thị thông báo xác nhận: "Bạn có chắc chắn muốn hủy? Tất cả dữ liệu đã nhập sẽ bị mất."
3. Bác sĩ/Chuyên gia xác nhận hủy
4. Hệ thống hủy bỏ tất cả dữ liệu đã nhập
5. Hệ thống quay lại trang trước đó (trang chi tiết bệnh nhân hoặc danh sách bệnh nhân)
6. Use case kết thúc

### 6.2. AF-2: Không có thuốc trong danh sách khi lưu
**Điều kiện kích hoạt**: Bác sĩ nhấn "Lưu đơn thuốc" khi chưa thêm thuốc nào

**Luồng sự kiện**:
1. Hệ thống kiểm tra danh sách thuốc
2. Hệ thống phát hiện danh sách rỗng
3. Hệ thống hiển thị thông báo lỗi: "Vui lòng thêm ít nhất một loại thuốc vào đơn"
4. Hệ thống giữ nguyên form tạo đơn thuốc
5. Quay lại bước 3 của luồng chính

### 6.3. AF-3: Thông tin thuốc không hợp lệ
**Điều kiện kích hoạt**: Bác sĩ nhập thông tin không hợp lệ cho thuốc

**Luồng sự kiện**:
1. Bác sĩ/Chuyên gia nhập thông tin thuốc với dữ liệu không hợp lệ (VD: liều dùng rỗng, số ngày âm, v.v.)
2. Bác sĩ/Chuyên gia nhấn "Thêm thuốc"
3. Hệ thống kiểm tra tính hợp lệ của dữ liệu
4. Hệ thống phát hiện lỗi và hiển thị thông báo cụ thể:
   - "Vui lòng nhập liều dùng"
   - "Vui lòng nhập thời gian uống"
   - "Số ngày uống phải là số nguyên dương"
5. Hệ thống highlight các trường lỗi bằng màu đỏ
6. Bác sĩ/Chuyên gia sửa lại thông tin
7. Quay lại bước 3 của luồng chính

### 6.4. AF-4: Lỗi kết nối database khi lưu
**Điều kiện kích hoạt**: Mất kết nối database trong quá trình lưu đơn thuốc

**Luồng sự kiện**:
1. Bác sĩ/Chuyên gia nhấn "Lưu đơn thuốc"
2. Hệ thống bắt đầu quá trình lưu vào database
3. Kết nối database thất bại hoặc bị gián đoạn
4. Hệ thống rollback transaction (nếu đã bắt đầu)
5. Hệ thống hiển thị thông báo lỗi: "Không thể lưu đơn thuốc. Vui lòng kiểm tra kết nối và thử lại."
6. Hệ thống giữ nguyên dữ liệu đã nhập
7. Bác sĩ/Chuyên gia có thể thử lại hoặc hủy
8. Use case quay lại bước 5 của luồng chính hoặc chuyển sang AF-1

### 6.5. AF-5: Xem lại đơn thuốc trước đó của bệnh nhân
**Điều kiện kích hoạt**: Bác sĩ muốn tham khảo đơn thuốc cũ trước khi kê đơn mới

**Luồng sự kiện**:
1. Tại form tạo đơn thuốc, bác sĩ nhấn nút "Xem lịch sử đơn thuốc"
2. Hệ thống hiển thị danh sách các đơn thuốc trước đó của bệnh nhân:
   - Ngày tạo đơn
   - Bác sĩ kê đơn
   - Danh sách thuốc
   - Liều dùng và cách dùng
3. Bác sĩ/Chuyên gia xem thông tin
4. Bác sĩ/Chuyên gia có thể:
   - Sao chép toàn bộ đơn thuốc cũ (nếu muốn kê lại)
   - Sao chép một số thuốc từ đơn cũ
   - Đóng cửa sổ lịch sử
5. Quay lại bước 3 của luồng chính

### 6.6. AF-6: Cảnh báo dị ứng thuốc
**Điều kiện kích hoạt**: Bác sĩ chọn thuốc mà bệnh nhân có tiền sử dị ứng

**Luồng sự kiện**:
1. Bác sĩ/Chuyên gia chọn thuốc từ danh sách
2. Hệ thống kiểm tra tiền sử dị ứng của bệnh nhân
3. Hệ thống phát hiện bệnh nhân có dị ứng với thuốc đã chọn hoặc thành phần tương tự
4. Hệ thống hiển thị cảnh báo nổi bật:
   - "⚠️ CẢNH BÁO: Bệnh nhân có tiền sử dị ứng với [tên thuốc/thành phần]"
   - Hiển thị chi tiết về dị ứng
5. Bác sĩ/Chuyên gia xem xét thông tin
6. Bác sĩ/Chuyên gia quyết định:
   - Chọn thuốc khác (quay lại bước 3 của luồng chính)
   - Xác nhận vẫn kê thuốc này (với lý do y khoa hợp lý)
7. Nếu xác nhận kê thuốc, hệ thống ghi chú lại quyết định của bác sĩ
8. Tiếp tục luồng chính

---

## 7. YÊU CẦU ĐẶC BIỆT (SPECIAL REQUIREMENTS)

### 7.1. Yêu cầu về hiệu năng
- Thời gian tìm kiếm thuốc: < 2 giây
- Thời gian lưu đơn thuốc: < 3 giây
- Hệ thống hỗ trợ tối thiểu 50 bác sĩ tạo đơn thuốc đồng thời

### 7.2. Yêu cầu về bảo mật
- Chỉ bác sĩ/chuyên gia đã đăng nhập mới được phép tạo đơn thuốc
- Mỗi đơn thuốc phải được ghi nhận bác sĩ kê đơn (audit trail)
- Dữ liệu đơn thuốc phải được mã hóa khi truyền qua mạng (HTTPS)
- Không cho phép xóa đơn thuốc đã tạo, chỉ cho phép sửa đổi trong thời gian giới hạn

### 7.3. Yêu cầu về khả năng sử dụng (Usability)
- Giao diện thân thiện, dễ sử dụng
- Hỗ trợ autocomplete khi tìm kiếm thuốc
- Hiển thị rõ ràng các trường bắt buộc
- Cảnh báo rõ ràng khi có lỗi
- Hỗ trợ phím tắt để tăng tốc độ nhập liệu

### 7.4. Yêu cầu về tính khả dụng (Availability)
- Hệ thống phải hoạt động 24/7
- Thời gian downtime tối đa: 1% (khoảng 3.65 ngày/năm)

### 7.5. Yêu cầu về tương thích
- Hỗ trợ các trình duyệt: Chrome, Firefox, Edge (phiên bản mới nhất)
- Responsive design: Hoạt động tốt trên máy tính, tablet

---

## 8. CÁC QUY TẮC NGHIỆP VỤ (BUSINESS RULES)

### BR-1: Quy tắc về quyền hạn
- Chỉ Bác sĩ và Chuyên gia mới có quyền tạo đơn thuốc
- Mỗi đơn thuốc phải gắn với bác sĩ kê đơn cụ thể
- Bác sĩ chỉ được kê đơn cho bệnh nhân trong ca khám của mình

### BR-2: Quy tắc về nội dung đơn thuốc
- Mỗi đơn thuốc phải có ít nhất 1 loại thuốc
- Mỗi loại thuốc trong đơn phải có đầy đủ:
  - Liều dùng
  - Thời gian uống
  - Số ngày uống (> 0)
- Không được kê trùng thuốc trong cùng một đơn

### BR-3: Quy tắc về ngày tạo đơn
- Ngày tạo đơn thuốc được ghi nhận tự động là ngày hiện tại
- Không cho phép tạo đơn thuốc với ngày trong quá khứ hoặc tương lai

### BR-4: Quy tắc về tính toàn vẹn dữ liệu
- Sử dụng transaction để đảm bảo tính toàn vẹn khi lưu đơn thuốc
- Nếu lưu bất kỳ phần nào thất bại, toàn bộ đơn thuốc phải được rollback

### BR-5: Quy tắc về lịch sử
- Mỗi đơn thuốc sau khi tạo phải được lưu vĩnh viễn
- Không cho phép xóa đơn thuốc
- Nếu cần sửa đổi, phải tạo đơn thuốc mới

---

## 9. THAM CHIẾU

### 9.1. Các bảng Database liên quan
- **donthuoc**: Lưu thông tin đơn thuốc
  - madonthuoc (INT, PK, AUTO_INCREMENT)
  - ngaytaodonthuoc (DATE)

- **chitietdonthuoc**: Lưu chi tiết các thuốc trong đơn
  - machitietdonthuoc (INT, PK, AUTO_INCREMENT)
  - madonthuoc (INT, FK)
  - mathuoc (INT, FK)
  - lieudung (VARCHAR(200))
  - thoigianuong (VARCHAR(200))
  - songayuong (INT)

- **thuoc**: Danh mục thuốc
  - mathuoc (INT, PK)
  - tenthuoc (VARCHAR)
  - dạng bào chế, hàm lượng...

- **chitiethoso**: Liên kết đơn thuốc với hồ sơ bệnh án
  - madonthuoc (INT, FK)
  - mahoso (INT, FK)
  - mabacsi (INT, FK)

### 9.2. Các file code liên quan
- **Controllers/cdonthuoc.php**: Controller xử lý logic đơn thuốc
- **Controllers/cchitietdonthuoc.php**: Controller xử lý chi tiết đơn thuốc
- **Models/mdonthuoc.php**: Model tương tác với bảng donthuoc
- **Models/mchitietdonthuoc.php**: Model tương tác với bảng chitietdonthuoc
- **Views/bacsi/pages/taodonthuoc/index.php**: Giao diện tạo đơn thuốc
- **Controllers/xulyhoantatkham.php**: Xử lý hoàn tất khám bệnh (bao gồm tạo đơn thuốc)

### 9.3. Use case liên quan
- UC-KB-001: Khám bệnh
- UC-HS-001: Quản lý hồ sơ bệnh án
- UC-BN-001: Xem thông tin bệnh nhân

---

## 10. PHỤ LỤC

### 10.1. Lịch sử thay đổi
| Phiên bản | Ngày | Người thay đổi | Mô tả thay đổi |
|-----------|------|----------------|----------------|
| 1.0 | 04/12/2024 | Copilot Agent | Tạo tài liệu đặc tả ban đầu |

### 10.2. Thuật ngữ
- **Đơn thuốc (Prescription)**: Tài liệu y tế do bác sĩ kê đơn, liệt kê các loại thuốc bệnh nhân cần sử dụng
- **Liều dùng (Dosage)**: Lượng thuốc cần dùng mỗi lần
- **Thời gian uống**: Thời điểm trong ngày cần uống thuốc (sáng/trưa/tối, trước/sau ăn)
- **Số ngày uống**: Tổng số ngày bệnh nhân cần sử dụng thuốc theo chỉ định

### 10.3. Ghi chú bổ sung
- Tài liệu này tuân thủ theo chuẩn đặc tả use case UML
- Có thể mở rộng thêm các tính năng như: in đơn thuốc, gửi đơn thuốc qua email, kiểm tra tương tác thuốc
