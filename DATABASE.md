# Tài liệu mô tả cơ sở dữ liệu - Hệ thống Quản lý Bệnh viện Hạnh Phúc

## Danh sách các bảng trong cơ sở dữ liệu

| STT | Tên bảng | Mô tả |
|-----|----------|-------|
| 1 | bacsi | Lưu trữ thông tin chi tiết về các bác sĩ làm việc tại bệnh viện |
| 2 | benhnhan | Quản lý thông tin cá nhân và y tế của bệnh nhân |
| 3 | calamviec | Định nghĩa các ca làm việc trong ngày (sáng, chiều, tối) |
| 4 | chitietdonthuoc | Lưu trữ chi tiết các loại thuốc trong đơn thuốc |
| 5 | chitiethoso | Ghi nhận chi tiết nội dung khám bệnh và chẩn đoán |
| 6 | chuyenkhoa | Danh mục các chuyên khoa y tế |
| 7 | danhmucxetnghiem | Phân loại các danh mục xét nghiệm |
| 8 | donthuoc | Quản lý các đơn thuốc được kê cho bệnh nhân |
| 9 | email_thanh_toan | Lưu trữ thông tin email xác nhận thanh toán |
| 10 | hosobenhan | Hồ sơ bệnh án tổng hợp của bệnh nhân |
| 11 | ketquaxetnghiem | Lưu trữ kết quả các xét nghiệm y tế |
| 12 | khunggioxetnghiem | Định nghĩa các khung giờ thực hiện xét nghiệm |
| 13 | khunggiokhambenh | Định nghĩa các khung giờ khám bệnh |
| 14 | lichlamviec | Quản lý lịch làm việc của bác sĩ và nhân viên |
| 15 | lichxetnghiem | Quản lý lịch hẹn xét nghiệm của bệnh nhân |
| 16 | linhvuc | Danh mục các lĩnh vực chuyên môn |
| 17 | loaixetnghiem | Phân loại các loại xét nghiệm cụ thể |
| 18 | nguoidung | Lưu trữ thông tin người dùng hệ thống |
| 19 | nguoigiamho | Thông tin người giám hộ cho bệnh nhân (trẻ em, người già) |
| 20 | nhanvien | Quản lý thông tin nhân viên y tế |
| 21 | phieukhambenh | Phiếu khám bệnh cho mỗi lần khám |
| 22 | phong | Quản lý các phòng khám và phòng xét nghiệm |
| 23 | taikhoan | Quản lý tài khoản đăng nhập hệ thống |
| 24 | thong_tin_thanh_toan | Lưu trữ thông tin thanh toán chi tiết |
| 25 | thuoc | Danh mục thuốc trong kho bệnh viện |
| 26 | tinhthanhpho | Danh sách tỉnh thành phố |
| 27 | tinnhan | Hệ thống tin nhắn giữa người dùng |
| 28 | trangthai | Danh mục các trạng thái (đã duyệt, chờ duyệt, hủy...) |
| 29 | vaitro | Phân quyền vai trò người dùng (admin, bác sĩ, bệnh nhân) |
| 30 | xaphuong | Danh sách xã phường thuộc các tỉnh thành |

## Mô tả chi tiết các bảng

### 1. Bảng `bacsi`
**Mô tả**: Lưu trữ thông tin chi tiết về các bác sĩ làm việc tại bệnh viện, bao gồm thông tin cá nhân, chuyên môn, học vị, kinh nghiệm làm việc và giá khám.

**Các trường chính**:
- `mabacsi`: Mã định danh bác sĩ
- `motabs`: Mô tả ngắn gọn về bác sĩ
- `gioithieubs`: Giới thiệu chi tiết về bác sĩ, kinh nghiệm và thành tích
- `ngaybatdau`: Ngày bắt đầu làm việc
- `ngayketthuc`: Ngày kết thúc (nếu đã nghỉ)
- `imgbs`: Ảnh đại diện của bác sĩ
- `giakham`: Giá khám bệnh
- `machuyenkhoa`: Chuyên khoa của bác sĩ
- `capbac`: Cấp bậc học vị (Bác sĩ, Thạc sĩ, Tiến sĩ, Giáo sư...)

### 2. Bảng `benhnhan`
**Mô tả**: Quản lý thông tin cá nhân và y tế của bệnh nhân, bao gồm thông tin liên hệ, địa chỉ, lịch sử bệnh án.

**Chức năng**:
- Lưu trữ hồ sơ bệnh nhân
- Quản lý thông tin cá nhân
- Theo dõi lịch sử khám bệnh

### 3. Bảng `calamviec`
**Mô tả**: Định nghĩa các ca làm việc trong ngày của bệnh viện (ca sáng, ca chiều, ca tối), phục vụ cho việc sắp xếp lịch làm việc và lịch khám bệnh.

**Chức năng**:
- Phân chia thời gian làm việc
- Hỗ trợ quản lý lịch làm việc
- Tối ưu hóa việc sắp xếp nhân sự

### 4. Bảng `chitietdonthuoc`
**Mô tả**: Lưu trữ chi tiết các loại thuốc trong từng đơn thuốc, bao gồm số lượng, liều lượng, cách dùng và ghi chú của bác sĩ.

**Chức năng**:
- Quản lý chi tiết đơn thuốc
- Theo dõi liều lượng và cách dùng
- Hỗ trợ nhà thuốc phát thuốc đúng

### 5. Bảng `chitiethoso`
**Mô tả**: Ghi nhận chi tiết nội dung khám bệnh, chẩn đoán của bác sĩ, triệu chứng, kết quả khám và phương án điều trị cho từng lần khám.

**Chức năng**:
- Lưu trữ thông tin khám bệnh chi tiết
- Ghi nhận chẩn đoán của bác sĩ
- Theo dõi tiến trình điều trị

### 6. Bảng `chuyenkhoa`
**Mô tả**: Danh mục các chuyên khoa y tế trong bệnh viện như Nội khoa, Ngoại khoa, Nhi khoa, Sản khoa, Tim mạch, v.v.

**Chức năng**:
- Phân loại chuyên môn bác sĩ
- Hỗ trợ bệnh nhân tìm đúng chuyên khoa
- Quản lý tổ chức khoa phòng

### 7. Bảng `danhmucxetnghiem`
**Mô tả**: Phân loại các danh mục xét nghiệm lớn như xét nghiệm máu, nước tiểu, X-quang, siêu âm, CT, MRI.

**Chức năng**:
- Tổ chức phân loại xét nghiệm
- Quản lý các loại xét nghiệm
- Hỗ trợ tìm kiếm và đặt lịch

### 8. Bảng `donthuoc`
**Mô tả**: Quản lý các đơn thuốc được bác sĩ kê cho bệnh nhân sau khi khám bệnh.

**Chức năng**:
- Lưu trữ đơn thuốc
- Liên kết với phiếu khám bệnh
- Quản lý việc cấp thuốc

### 9. Bảng `email_thanh_toan`
**Mô tả**: Lưu trữ thông tin về các email xác nhận thanh toán được gửi cho bệnh nhân sau khi họ thanh toán dịch vụ.

**Chức năng**:
- Ghi nhận việc gửi email xác nhận
- Theo dõi lịch sử giao dịch
- Hỗ trợ chăm sóc khách hàng

### 10. Bảng `hosobenhan`
**Mô tả**: Hồ sơ bệnh án tổng hợp của bệnh nhân, tổng hợp tất cả các lần khám bệnh, xét nghiệm và điều trị.

**Chức năng**:
- Quản lý hồ sơ bệnh án điện tử
- Theo dõi lịch sử y tế
- Hỗ trợ bác sĩ trong chẩn đoán

### 11. Bảng `ketquaxetnghiem`
**Mô tả**: Lưu trữ kết quả các xét nghiệm y tế của bệnh nhân, bao gồm kết quả, chỉ số và nhận xét của bác sĩ.

**Chức năng**:
- Lưu trữ kết quả xét nghiệm
- Cung cấp thông tin cho chẩn đoán
- Theo dõi diễn biến sức khỏe

### 12. Bảng `khunggioxetnghiem`
**Mô tả**: Định nghĩa các khung giờ có thể thực hiện xét nghiệm trong ngày, phục vụ cho việc đặt lịch hẹn xét nghiệm.

**Chức năng**:
- Quản lý lịch trình xét nghiệm
- Tối ưu hóa sử dụng phòng xét nghiệm
- Hỗ trợ đặt lịch hẹn

### 13. Bảng `khunggiokhambenh`
**Mô tả**: Định nghĩa các khung giờ khám bệnh của bác sĩ trong từng ca làm việc, giúp bệnh nhân đặt lịch khám.

**Chức năng**:
- Quản lý lịch khám của bác sĩ
- Hỗ trợ đặt lịch hẹn khám
- Tránh trùng lịch

### 14. Bảng `lichlamviec`
**Mô tả**: Quản lý lịch làm việc của bác sĩ và nhân viên y tế tại bệnh viện, bao gồm ca làm việc, phòng làm việc và thời gian.

**Chức năng**:
- Sắp xếp lịch làm việc
- Quản lý nhân sự
- Tối ưu hóa nguồn lực

### 15. Bảng `lichxetnghiem`
**Mô tả**: Quản lý lịch hẹn xét nghiệm của bệnh nhân, bao gồm thông tin loại xét nghiệm, thời gian, địa điểm và trạng thái.

**Chức năng**:
- Đặt lịch xét nghiệm
- Theo dõi trạng thái xét nghiệm
- Quản lý thanh toán xét nghiệm

### 16. Bảng `linhvuc`
**Mô tả**: Danh mục các lĩnh vực chuyên môn hẹp hơn trong từng chuyên khoa (ví dụ: Tim mạch can thiệp, Gan mật tụy...).

**Chức năng**:
- Phân loại chuyên môn chi tiết
- Hỗ trợ tìm kiếm chuyên gia
- Quản lý đào tạo

### 17. Bảng `loaixetnghiem`
**Mô tả**: Phân loại các loại xét nghiệm cụ thể thuộc từng danh mục (ví dụ: Xét nghiệm công thức máu, xét nghiệm đường huyết...).

**Chức năng**:
- Danh mục xét nghiệm chi tiết
- Quản lý giá xét nghiệm
- Hỗ trợ kê đơn xét nghiệm

### 18. Bảng `nguoidung`
**Mô tả**: Lưu trữ thông tin về tất cả người dùng hệ thống bao gồm bác sĩ, nhân viên, và có thể cả bệnh nhân nếu có tài khoản.

**Chức năng**:
- Quản lý thông tin người dùng
- Liên kết với tài khoản
- Quản lý hồ sơ cá nhân

### 19. Bảng `nguoigiamho`
**Mô tả**: Lưu trữ thông tin về người giám hộ cho các bệnh nhân là trẻ em, người già hoặc người không tự chủ.

**Chức năng**:
- Quản lý thông tin người giám hộ
- Hỗ trợ liên hệ khẩn cấp
- Quản lý quyền đại diện

### 20. Bảng `nhanvien`
**Mô tả**: Quản lý thông tin chi tiết về nhân viên y tế không phải bác sĩ như điều dưỡng, kỹ thuật viên, hành chính.

**Chức năng**:
- Quản lý nhân sự
- Phân công công việc
- Theo dõi lịch làm việc

### 21. Bảng `phieukhambenh`
**Mô tả**: Lưu trữ thông tin về mỗi lần khám bệnh của bệnh nhân, bao gồm thông tin đặt lịch, bác sĩ khám, triệu chứng và chẩn đoán.

**Chức năng**:
- Quản lý lịch khám bệnh
- Theo dõi quá trình khám
- Liên kết với hồ sơ bệnh án

### 22. Bảng `phong`
**Mô tả**: Quản lý thông tin về các phòng trong bệnh viện như phòng khám, phòng xét nghiệm, phòng điều trị.

**Chức năng**:
- Quản lý cơ sở vật chất
- Sắp xếp phòng khám
- Tối ưu hóa sử dụng phòng

### 23. Bảng `taikhoan`
**Mô tả**: Quản lý tài khoản đăng nhập vào hệ thống, bao gồm tên đăng nhập, mật khẩu, vai trò và trạng thái.

**Chức năng**:
- Xác thực người dùng
- Phân quyền truy cập
- Bảo mật hệ thống

### 24. Bảng `thong_tin_thanh_toan`
**Mô tả**: Lưu trữ thông tin chi tiết về các giao dịch thanh toán của bệnh nhân cho dịch vụ khám bệnh, xét nghiệm.

**Chức năng**:
- Quản lý thanh toán
- Theo dõi công nợ
- Xuất hóa đơn

### 25. Bảng `thuoc`
**Mô tả**: Danh mục tất cả các loại thuốc có trong kho của bệnh viện, bao gồm tên thuốc, thành phần, giá bán và số lượng tồn kho.

**Chức năng**:
- Quản lý kho thuốc
- Theo dõi tồn kho
- Quản lý giá thuốc

### 26. Bảng `tinhthanhpho`
**Mô tả**: Danh sách các tỉnh thành phố trong hệ thống, phục vụ cho việc quản lý địa chỉ của bệnh nhân và nhân viên.

**Chức năng**:
- Quản lý địa chỉ hành chính
- Hỗ trợ lọc theo khu vực
- Thống kê theo vùng miền

### 27. Bảng `tinnhan`
**Mô tả**: Hệ thống tin nhắn nội bộ giữa các người dùng trong hệ thống (bác sĩ, bệnh nhân, nhân viên).

**Chức năng**:
- Giao tiếp nội bộ
- Tư vấn trực tuyến
- Thông báo hệ thống

### 28. Bảng `trangthai`
**Mô tả**: Danh mục các trạng thái được sử dụng trong hệ thống như "Chờ xác nhận", "Đã duyệt", "Đã hủy", "Hoàn thành".

**Chức năng**:
- Quản lý trạng thái
- Theo dõi tiến trình
- Báo cáo thống kê

### 29. Bảng `vaitro`
**Mô tả**: Phân quyền vai trò người dùng trong hệ thống như Admin, Bác sĩ, Bệnh nhân, Nhân viên, Lễ tân.

**Chức năng**:
- Phân quyền truy cập
- Quản lý chức năng
- Bảo mật dữ liệu

### 30. Bảng `xaphuong`
**Mô tả**: Danh sách các xã phường thuộc các quận huyện và tỉnh thành, cung cấp thông tin địa chỉ chi tiết cho người dùng.

**Chức năng**:
- Quản lý địa chỉ chi tiết
- Hỗ trợ tìm kiếm
- Thống kê theo địa phương

## Mối quan hệ giữa các bảng

### Quan hệ chính:
1. **nguoidung** ↔ **bacsi**: Một người dùng có thể là bác sĩ
2. **benhnhan** → **hosobenhan**: Một bệnh nhân có nhiều hồ sơ bệnh án
3. **hosobenhan** → **chitiethoso**: Một hồ sơ có nhiều chi tiết khám bệnh
4. **phieukhambenh** → **bacsi**: Một phiếu khám do một bác sĩ thực hiện
5. **phieukhambenh** → **benhnhan**: Một phiếu khám cho một bệnh nhân
6. **donthuoc** → **chitietdonthuoc**: Một đơn thuốc có nhiều loại thuốc
7. **lichxetnghiem** → **ketquaxetnghiem**: Một lịch xét nghiệm có kết quả tương ứng
8. **lichlamviec** → **nguoidung**: Lịch làm việc của nhân viên/bác sĩ
9. **taikhoan** → **nguoidung**: Một tài khoản tương ứng một người dùng
10. **xaphuong** → **tinhthanhpho**: Xã phường thuộc tỉnh thành phố

## Ghi chú
- Cơ sở dữ liệu sử dụng MySQL/MariaDB
- Mã hóa: UTF-8 (utf8mb4_vietnamese_ci)
- Engine: InnoDB
- Có sử dụng khóa ngoại để đảm bảo tính toàn vẹn dữ liệu
