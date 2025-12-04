# BẢNG TESTCASE - CHỨC NĂNG ĐĂNG KÝ TÀI KHOẢN

## Thông tin chung
- **Chức năng**: Đăng ký tài khoản bệnh nhân
- **Mô-đun**: Quản lý tài khoản
- **File code**: Views/benhnhan/pages/dangky/index.php
- **Ngày tạo**: 04/12/2025

---

## BẢNG TESTCASE

| ID | Mô tả | Tiền điều kiện | Các bước thực hiện | Kết quả mong đợi | Trạng thái |
|---|---|---|---|---|---|
| TC_DK_001 | Đăng ký tài khoản thành công với đầy đủ thông tin hợp lệ (người từ 18 tuổi trở lên) | - Người dùng chưa có tài khoản<br>- Truy cập vào trang đăng ký<br>- Tuổi >= 18 | 1. Nhập họ tên: "Nguyễn Văn A"<br>2. Chọn giới tính: "Nam"<br>3. Nhập ngày sinh: "01/01/2000" (24 tuổi)<br>4. Nhập email: "nguyenvana@gmail.com"<br>5. Nhập số điện thoại: "0988365345"<br>6. Nhập số CCCD: "123456789012"<br>7. Upload ảnh CCCD mặt trước (tùy chọn)<br>8. Upload ảnh CCCD mặt sau (tùy chọn)<br>9. Chọn nghề nghiệp: "Nhân viên văn phòng"<br>10. Nhập tiền sử bệnh bản thân: "Không"<br>11. Nhập tiền sử bệnh gia đình: "Không"<br>12. Chọn Tỉnh/Thành phố<br>13. Chọn Xã/Phường<br>14. Nhập số nhà: "123 Đường Lê Lợi"<br>15. Nhập mật khẩu: "123456"<br>16. Nhập lại mật khẩu: "123456"<br>17. Click nút "Đăng ký tài khoản" | - Hiển thị thông báo "Đăng ký tài khoản thành công!"<br>- Chuyển hướng về trang đăng nhập<br>- Tài khoản được lưu vào database<br>- Mã bệnh nhân được tạo tự động (BN_xxxxxxxx) | Chưa chạy |
| TC_DK_002 | Đăng ký với email đã tồn tại | - Đã có tài khoản với email "test@gmail.com" trong hệ thống<br>- Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin hợp lệ<br>2. Nhập email: "test@gmail.com" (email đã tồn tại)<br>3. Hoàn thành form và click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Email đã tồn tại."<br>- Form không được submit<br>- Người dùng vẫn ở trang đăng ký | Chưa chạy |
| TC_DK_003 | Đăng ký với email không hợp lệ | - Truy cập vào trang đăng ký<br>- Tuổi >= 18 | 1. Nhập đầy đủ thông tin<br>2. Nhập email không đúng định dạng: "emailkhonghople"<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Email không hợp lệ."<br>- Form không được submit | Chưa chạy |
| TC_DK_004 | Đăng ký với số điện thoại không hợp lệ (ít hơn 10 số) | - Truy cập vào trang đăng ký<br>- Tuổi >= 18 | 1. Nhập đầy đủ thông tin<br>2. Nhập số điện thoại: "098836534" (9 số)<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Số điện thoại không hợp lệ (10 số)."<br>- Form không được submit | Chưa chạy |
| TC_DK_005 | Đăng ký với số điện thoại không hợp lệ (nhiều hơn 10 số) | - Truy cập vào trang đăng ký<br>- Tuổi >= 18 | 1. Nhập đầy đủ thông tin<br>2. Nhập số điện thoại: "09883653456" (11 số)<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Số điện thoại không hợp lệ (10 số)."<br>- Form không được submit | Chưa chạy |
| TC_DK_006 | Đăng ký với số CCCD không hợp lệ (ít hơn 9 số) | - Truy cập vào trang đăng ký<br>- Tuổi >= 18 | 1. Nhập đầy đủ thông tin<br>2. Nhập số CCCD: "12345678" (8 số)<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Số CCCD không hợp lệ (9-12 số)."<br>- Form không được submit | Chưa chạy |
| TC_DK_007 | Đăng ký với số CCCD không hợp lệ (nhiều hơn 12 số) | - Truy cập vào trang đăng ký<br>- Tuổi >= 18 | 1. Nhập đầy đủ thông tin<br>2. Nhập số CCCD: "1234567890123" (13 số)<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Số CCCD không hợp lệ (9-12 số)."<br>- Form không được submit | Chưa chạy |
| TC_DK_008 | Đăng ký với họ tên chứa ký tự đặc biệt | - Truy cập vào trang đăng ký | 1. Nhập họ tên: "Nguyễn Văn A123@"<br>2. Nhập đầy đủ thông tin khác hợp lệ<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Họ tên chỉ được chứa chữ cái và khoảng trắng."<br>- Form không được submit | Chưa chạy |
| TC_DK_009 | Đăng ký với họ tên chứa số | - Truy cập vào trang đăng ký | 1. Nhập họ tên: "Nguyễn Văn A123"<br>2. Nhập đầy đủ thông tin khác hợp lệ<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Họ tên chỉ được chứa chữ cái và khoảng trắng."<br>- Form không được submit | Chưa chạy |
| TC_DK_010 | Đăng ký với mật khẩu và nhập lại mật khẩu không khớp | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin hợp lệ<br>2. Nhập mật khẩu: "123456"<br>3. Nhập lại mật khẩu: "654321"<br>4. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Mật khẩu nhập lại không khớp."<br>- Form không được submit | Chưa chạy |
| TC_DK_011 | Đăng ký với mật khẩu ít hơn 6 ký tự | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin hợp lệ<br>2. Nhập mật khẩu: "12345" (5 ký tự)<br>3. Nhập lại mật khẩu: "12345"<br>4. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Mật khẩu phải từ 6 ký tự trở lên."<br>- Form không được submit | Chưa chạy |
| TC_DK_012 | Đăng ký với tuổi dưới 18 | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin<br>2. Nhập ngày sinh: "01/01/2010" (14 tuổi)<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo lỗi: "Yêu cầu người đăng ký tài khoản phải từ 18 tuổi."<br>- Form không được submit | Chưa chạy |
| TC_DK_013 | Đăng ký với trường bắt buộc bỏ trống (Họ và tên) | - Truy cập vào trang đăng ký | 1. Bỏ trống trường "Họ và tên"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please fill out this field"<br>- Form không được submit | Chưa chạy |
| TC_DK_014 | Đăng ký với trường bắt buộc bỏ trống (Giới tính) | - Truy cập vào trang đăng ký | 1. Không chọn "Giới tính"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please select an item in the list"<br>- Form không được submit | Chưa chạy |
| TC_DK_015 | Đăng ký với trường bắt buộc bỏ trống (Ngày sinh) | - Truy cập vào trang đăng ký | 1. Bỏ trống trường "Ngày sinh"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please fill out this field"<br>- Form không được submit | Chưa chạy |
| TC_DK_016 | Đăng ký với trường bắt buộc bỏ trống (Tỉnh/Thành phố) | - Truy cập vào trang đăng ký | 1. Không chọn "Tỉnh/Thành phố"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please select an item in the list"<br>- Form không được submit | Chưa chạy |
| TC_DK_017 | Đăng ký với trường bắt buộc bỏ trống (Xã/Phường) | - Truy cập vào trang đăng ký | 1. Không chọn "Xã/Phường"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please select an item in the list"<br>- Form không được submit | Chưa chạy |
| TC_DK_018 | Đăng ký với trường bắt buộc bỏ trống (Số nhà) | - Truy cập vào trang đăng ký | 1. Bỏ trống trường "Số nhà, tên đường"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please fill out this field"<br>- Form không được submit | Chưa chạy |
| TC_DK_019 | Đăng ký với trường bắt buộc bỏ trống (Mật khẩu) | - Truy cập vào trang đăng ký | 1. Bỏ trống trường "Mật khẩu"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please fill out this field"<br>- Form không được submit | Chưa chạy |
| TC_DK_020 | Đăng ký với trường bắt buộc bỏ trống (Nhập lại mật khẩu) | - Truy cập vào trang đăng ký | 1. Bỏ trống trường "Nhập lại mật khẩu"<br>2. Nhập đầy đủ các trường khác<br>3. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please fill out this field"<br>- Form không được submit | Chưa chạy |
| TC_DK_021 | Kiểm tra chức năng chọn nghề nghiệp "Khác" | - Truy cập vào trang đăng ký | 1. Chọn nghề nghiệp: "Khác"<br>2. Quan sát form | - Hiển thị thêm trường "Nghề nghiệp khác"<br>- Trường "Nghề nghiệp khác" là bắt buộc (có dấu *) | Chưa chạy |
| TC_DK_022 | Đăng ký với nghề nghiệp "Khác" nhưng không nhập chi tiết | - Truy cập vào trang đăng ký | 1. Chọn nghề nghiệp: "Khác"<br>2. Bỏ trống trường "Nghề nghiệp khác"<br>3. Nhập đầy đủ các trường khác<br>4. Click "Đăng ký tài khoản" | - Hiển thị thông báo trình duyệt: "Please fill out this field"<br>- Form không được submit | Chưa chạy |
| TC_DK_023 | Đăng ký với nghề nghiệp "Khác" và nhập chi tiết | - Truy cập vào trang đăng ký | 1. Chọn nghề nghiệp: "Khác"<br>2. Nhập nghề nghiệp khác: "Freelancer"<br>3. Nhập đầy đủ thông tin hợp lệ khác<br>4. Click "Đăng ký tài khoản" | - Đăng ký thành công<br>- Nghề nghiệp được lưu là "Freelancer" | Chưa chạy |
| TC_DK_024 | Kiểm tra hiển thị tuổi khi chọn ngày sinh | - Truy cập vào trang đăng ký | 1. Chọn ngày sinh: "01/01/2000"<br>2. Quan sát giao diện | - Hiển thị thông tin tuổi: "Tuổi: 24" (hoặc tuổi tương ứng)<br>- Icon bánh sinh nhật xuất hiện | Chưa chạy |
| TC_DK_025 | Kiểm tra chức năng load Xã/Phường theo Tỉnh/Thành phố | - Truy cập vào trang đăng ký | 1. Chọn Tỉnh/Thành phố: "Hồ Chí Minh"<br>2. Quan sát dropdown Xã/Phường | - Dropdown Xã/Phường được load danh sách xã/phường thuộc TP. Hồ Chí Minh<br>- Danh sách hiển thị đúng dữ liệu | Chưa chạy |
| TC_DK_026 | Kiểm tra preview ảnh CCCD mặt trước | - Truy cập vào trang đăng ký | 1. Click vào "Chọn ảnh CCCD mặt trước"<br>2. Chọn file ảnh hợp lệ (jpg, png, jpeg)<br>3. Quan sát giao diện | - Hiển thị preview ảnh đã chọn<br>- Ảnh hiển thị rõ ràng | Chưa chạy |
| TC_DK_027 | Kiểm tra preview ảnh CCCD mặt sau | - Truy cập vào trang đăng ký | 1. Click vào "Chọn ảnh CCCD mặt sau"<br>2. Chọn file ảnh hợp lệ (jpg, png, jpeg)<br>3. Quan sát giao diện | - Hiển thị preview ảnh đã chọn<br>- Ảnh hiển thị rõ ràng | Chưa chạy |
| TC_DK_028 | Upload file ảnh CCCD không đúng định dạng | - Truy cập vào trang đăng ký | 1. Click vào "Chọn ảnh CCCD mặt trước"<br>2. Chọn file không phải ảnh (pdf, docx, txt,...)<br>3. Quan sát thông báo | - Hiển thị popup lỗi: "Vui lòng chọn đúng định dạng ảnh (jpg, png, jpeg)"<br>- File không được upload<br>- Preview không hiển thị | Chưa chạy |
| TC_DK_029 | Kiểm tra mã hóa dữ liệu nhạy cảm | - Đăng ký thành công một tài khoản<br>- Có quyền truy cập database | 1. Đăng ký tài khoản với email, SĐT, CCCD<br>2. Kiểm tra dữ liệu trong database | - Email, SĐT, CCCD được mã hóa trong database<br>- Dữ liệu không hiển thị dạng plain text | Chưa chạy |
| TC_DK_030 | Kiểm tra tự động tạo mã bệnh nhân | - Đăng ký thành công một tài khoản<br>- Có quyền truy cập database | 1. Đăng ký tài khoản thành công<br>2. Kiểm tra mã bệnh nhân trong database | - Mã bệnh nhân được tạo tự động<br>- Format: BN_xxxxxxxx (8 chữ số ngẫu nhiên)<br>- Mã là duy nhất | Chưa chạy |
| TC_DK_031 | Kiểm tra giới hạn tuổi tối đa | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin<br>2. Nhập ngày sinh: "01/01/1900" (124 tuổi)<br>3. Click "Đăng ký tài khoản" | - Hệ thống xử lý bình thường hoặc hiển thị cảnh báo tuổi quá cao<br>- Cần xác nhận business rule về tuổi tối đa | Chưa chạy |
| TC_DK_032 | Kiểm tra chức năng điền lại thông tin sau khi lỗi | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin<br>2. Nhập email đã tồn tại<br>3. Submit form<br>4. Quan sát các trường đã nhập | - Hiển thị thông báo lỗi<br>- Các trường đã nhập trước đó được giữ nguyên<br>- Người dùng không phải nhập lại từ đầu | Chưa chạy |
| TC_DK_033 | Kiểm tra validation ngày sinh trong tương lai | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin<br>2. Chọn ngày sinh là ngày trong tương lai<br>3. Quan sát kết quả | - HTML5 validation ngăn chặn chọn ngày tương lai (max="<?php echo date('Y-m-d'); ?>")<br>- Không thể chọn ngày lớn hơn hôm nay | Chưa chạy |
| TC_DK_034 | Kiểm tra validation ngày sinh quá 120 năm | - Truy cập vào trang đăng ký | 1. Thử chọn ngày sinh cách đây hơn 120 năm | - HTML5 validation ngăn chặn (min="<?php echo date('Y-m-d', strtotime('-120 years')); ?>")<br>- Không thể chọn ngày nhỏ hơn 120 năm trước | Chưa chạy |
| TC_DK_035 | Kiểm tra chuyển hướng sau khi đăng ký thành công | - Truy cập vào trang đăng ký | 1. Nhập đầy đủ thông tin hợp lệ<br>2. Click "Đăng ký tài khoản"<br>3. Click "OK" trên popup thành công | - Hiển thị popup thành công<br>- Sau khi click OK, chuyển hướng đến trang đăng nhập (?action=dangnhap) | Chưa chạy |
| TC_DK_036 | Kiểm tra responsive trên mobile | - Truy cập vào trang đăng ký trên thiết bị mobile hoặc resize browser | 1. Mở trang đăng ký trên mobile (hoặc resize browser về kích thước mobile)<br>2. Quan sát giao diện | - Form hiển thị responsive<br>- Các trường input không bị che khuất<br>- Có thể scroll và nhập liệu bình thường | Chưa chạy |
| TC_DK_037 | Kiểm tra các trường không bắt buộc có thể bỏ trống | - Truy cập vào trang đăng ký | 1. Chỉ nhập các trường bắt buộc (có dấu *)<br>2. Bỏ trống: Nghề nghiệp, Tiền sử bệnh, CCCD mặt trước/sau<br>3. Click "Đăng ký tài khoản" | - Đăng ký thành công<br>- Các trường không bắt buộc được lưu rỗng hoặc NULL | Chưa chạy |
| TC_DK_038 | Kiểm tra XSS injection trong trường họ tên | - Truy cập vào trang đăng ký | 1. Nhập họ tên: "<script>alert('XSS')</script>"<br>2. Nhập đầy đủ thông tin khác<br>3. Click "Đăng ký tài khoản" | - Validation chặn do họ tên chỉ chấp nhận chữ cái<br>- Hiển thị lỗi: "Họ tên chỉ được chứa chữ cái và khoảng trắng."<br>- Script không được thực thi | Chưa chạy |
| TC_DK_039 | Kiểm tra SQL injection trong trường email | - Truy cập vào trang đăng ký | 1. Nhập email: "test' OR '1'='1"<br>2. Nhập đầy đủ thông tin khác<br>3. Click "Đăng ký tài khoản" | - Validation email chặn định dạng không hợp lệ<br>- Hiển thị lỗi: "Email không hợp lệ."<br>- Query không bị injection | Chưa chạy |
| TC_DK_040 | Kiểm tra đăng ký với họ tên có dấu tiếng Việt | - Truy cập vào trang đăng ký | 1. Nhập họ tên: "Nguyễn Văn Á Ế Ồ Ừ"<br>2. Nhập đầy đủ thông tin hợp lệ khác<br>3. Click "Đăng ký tài khoản" | - Đăng ký thành công<br>- Họ tên tiếng Việt được lưu và hiển thị chính xác | Chưa chạy |

---

## Ghi chú

### Các trường bắt buộc (Required):
- Họ và tên (*)
- Giới tính (*)
- Ngày sinh (*)
- Email (*) - Bắt buộc với người >= 18 tuổi (dựa vào validation trong code)
- Số điện thoại (*) - Bắt buộc với người >= 18 tuổi (dựa vào validation trong code)
- Số CCCD (*) - Bắt buộc với người >= 18 tuổi (dựa vào validation trong code)
- Tỉnh/Thành phố (*)
- Xã/Phường (*)
- Số nhà, tên đường (*)
- Mật khẩu (*)
- Nhập lại mật khẩu (*)

**Lưu ý về tuổi**: 
- Trong code, validation email/sdt/cccd chỉ áp dụng khi tuổi >= 16 (xem dòng 49-54)
- Tuy nhiên, yêu cầu đăng ký tài khoản phải từ 18 tuổi trở lên (xem dòng 93-98)
- Do đó, các test case đều sử dụng tuổi >= 18 là điều kiện tiên quyết

### Các trường không bắt buộc (Optional):
- CCCD mặt trước
- CCCD mặt sau
- Nghề nghiệp
- Tiền sử bệnh của bản thân
- Tiền sử bệnh của gia đình

### Quy tắc validation:
1. **Email**: Phải đúng định dạng email (filter_var FILTER_VALIDATE_EMAIL)
2. **Số điện thoại**: Đúng 10 số (regex: /^[0-9]{10}$/)
3. **Số CCCD**: 9-12 số (regex: /^[0-9]{9,12}$/)
4. **Họ tên**: Chỉ chứa chữ cái và khoảng trắng (regex: /^[a-zA-ZÀ-ỹ\s]+$/u)
5. **Mật khẩu**: Tối thiểu 6 ký tự
6. **Mật khẩu nhập lại**: Phải khớp với mật khẩu
7. **Tuổi**: Phải từ 18 tuổi trở lên để đăng ký tài khoản
8. **Ngày sinh**: Không được là ngày tương lai, không được quá 120 năm trước

### Bảo mật:
- Email, SĐT, CCCD được mã hóa bằng hàm `encryptData()` trước khi lưu database
- Upload file chỉ chấp nhận định dạng: jpg, jpeg, png
- File được đổi tên thành dạng unique: upload_xxxxx.ext

### Luồng xử lý:
1. Validate dữ liệu đầu vào
2. Upload file CCCD (nếu có)
3. Tạo mã bệnh nhân tự động (BN_xxxxxxxx)
4. Mã hóa dữ liệu nhạy cảm
5. Gọi controller `dangkytk()` để lưu vào database
6. Kiểm tra kết quả và hiển thị thông báo
7. Chuyển hướng đến trang đăng nhập nếu thành công

---

## Kết luận
Tổng số test case: **40**

### Phân loại theo chức năng:
- **Test case chức năng (Functional)**: TC_DK_001, TC_DK_021, TC_DK_023, TC_DK_024, TC_DK_025, TC_DK_026, TC_DK_027, TC_DK_030, TC_DK_032, TC_DK_035, TC_DK_036, TC_DK_037 (12 test cases)
- **Test case validation (Validation)**: TC_DK_002, TC_DK_003, TC_DK_004, TC_DK_005, TC_DK_006, TC_DK_007, TC_DK_008, TC_DK_009, TC_DK_010, TC_DK_011, TC_DK_012, TC_DK_013, TC_DK_014, TC_DK_015, TC_DK_016, TC_DK_017, TC_DK_018, TC_DK_019, TC_DK_020, TC_DK_022, TC_DK_028, TC_DK_031, TC_DK_033, TC_DK_034 (24 test cases)
- **Test case bảo mật (Security)**: TC_DK_029, TC_DK_038, TC_DK_039, TC_DK_040 (4 test cases)

Cần chạy tất cả các test case trước khi release tính năng đăng ký.
