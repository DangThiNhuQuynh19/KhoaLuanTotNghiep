# Hệ Thống Thông Báo Đẩy Kết Quả Xét Nghiệm

## Tổng Quan

Hệ thống thông báo đẩy (push notification) được thiết kế để thông báo real-time cho bác sĩ khi kết quả xét nghiệm đã được nhân viên xét nghiệm cập nhật vào hệ thống.

## Kiến Trúc

### 1. Database Layer
**File:** `/database/migrations/create_thongbao_table.sql`

Bảng `thongbao` lưu trữ tất cả thông báo:
- `mathongbao`: ID thông báo (auto increment)
- `manguoidung`: ID người dùng nhận thông báo
- `tieude`: Tiêu đề thông báo
- `noidung`: Nội dung thông báo
- `loaithongbao`: Loại thông báo (mặc định: 'ketquaxetnghiem')
- `malichxetnghiem`: ID lịch xét nghiệm liên quan
- `daxem`: Trạng thái đã xem (0/1)
- `ngaytao`: Thời gian tạo thông báo

### 2. Model Layer
**File:** `/Models/mthongbao.php`

Class `mThongBao` xử lý các thao tác database:
- `insert_thongbao()`: Tạo thông báo mới
- `select_thongbao_by_manguoidung()`: Lấy danh sách thông báo
- `count_thongbao_chuaxem()`: Đếm thông báo chưa xem
- `update_thongbao_daxem()`: Đánh dấu đã xem
- `update_all_thongbao_daxem()`: Đánh dấu tất cả đã xem
- `get_bacsi_from_lichxetnghiem()`: Lấy thông tin bác sĩ từ lịch xét nghiệm
- `delete_thongbao()`: Xóa thông báo

### 3. Controller Layer
**File:** `/Controllers/cthongbao.php`

Class `cThongBao` xử lý business logic:
- `create_thongbao()`: Tạo thông báo
- `get_thongbao_by_manguoidung()`: Lấy danh sách thông báo
- `count_thongbao_chuaxem()`: Đếm thông báo chưa xem
- `mark_thongbao_as_read()`: Đánh dấu đã xem
- `mark_all_thongbao_as_read()`: Đánh dấu tất cả đã xem
- `delete_thongbao()`: Xóa thông báo
- `send_test_result_notification()`: Gửi thông báo kết quả xét nghiệm
- `send_websocket_notification()`: Gửi thông báo qua WebSocket

### 4. WebSocket Server
**File:** `/Chat.php`

WebSocket server được mở rộng để xử lý thông báo:
- Command `register`: Đăng ký user với WebSocket server
- Command `notification`: Xử lý và phát thông báo đến client
- Quản lý kết nối user: `$userConnections[]`

### 5. API Endpoint
**File:** `/Ajax/thongbao.php`

REST API endpoint để quản lý thông báo:
- `GET /Ajax/thongbao.php?action=get_all`: Lấy tất cả thông báo
- `GET /Ajax/thongbao.php?action=count_unread`: Đếm thông báo chưa xem
- `GET /Ajax/thongbao.php?action=mark_read&mathongbao=X`: Đánh dấu đã xem
- `GET /Ajax/thongbao.php?action=mark_all_read`: Đánh dấu tất cả đã xem
- `GET /Ajax/thongbao.php?action=delete&mathongbao=X`: Xóa thông báo

### 6. Client-Side JavaScript
**File:** `/Assets/js/notification-handler.js`

Class `NotificationHandler` xử lý thông báo phía client:
- Kết nối WebSocket tự động
- Tự động reconnect khi mất kết nối
- Hiển thị browser notification
- Hiển thị in-page notification toast
- Cập nhật notification badge
- Phát âm thanh thông báo
- Xử lý click để xem chi tiết

## Luồng Hoạt Động

### 1. Khi Nhân Viên Xét Nghiệm Cập Nhật Kết Quả:

```php
// File: /Views/nhanvienxetnghiem/pages/chinhsua/index.php

// 1. Lưu kết quả xét nghiệm vào database
// 2. Cập nhật trạng thái lịch xét nghiệm thành "Đã có kết quả" (status = 12)
// 3. Gửi thông báo
$cThongBao = new cThongBao();
$cThongBao->send_test_result_notification($malich);
```

### 2. Xử Lý Gửi Thông Báo:

```php
// Controllers/cthongbao.php

public function send_test_result_notification($malichxetnghiem) {
    // 1. Lấy thông tin bác sĩ đã tạo phiếu xét nghiệm
    // 2. Tạo nội dung thông báo
    // 3. Lưu thông báo vào database
    // 4. Gửi thông báo real-time qua WebSocket
}
```

### 3. WebSocket Phát Thông Báo:

```php
// Chat.php

// WebSocket server nhận message với command 'notification'
// Tìm user trong danh sách kết nối
// Gửi thông báo đến user nếu đang online
```

### 4. Client Nhận và Hiển Thị:

```javascript
// Assets/js/notification-handler.js

// 1. WebSocket client nhận message
// 2. Hiển thị browser notification (nếu được phép)
// 3. Hiển thị toast notification trên trang
// 4. Cập nhật badge số lượng thông báo chưa đọc
// 5. Phát âm thanh thông báo
```

## Cài Đặt và Sử Dụng

### 1. Tạo Bảng Database:

```bash
mysql -u kltn -p hanhphuc < /database/migrations/create_thongbao_table.sql
```

### 2. Khởi Động WebSocket Server:

```bash
cd /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep
php server.php
```

### 3. Thêm JavaScript vào Trang Bác Sĩ:

```html
<!-- Trong header của trang bác sĩ -->
<body data-username="<?php echo $_SESSION['user']; ?>">

<!-- Trước thẻ </body> -->
<script src="/Assets/js/notification-handler.js"></script>
```

### 4. Thêm Badge Thông Báo (Optional):

```html
<!-- Trong menu/header -->
<div class="notification-icon">
    <i class="bell-icon"></i>
    <span class="notification-badge" style="display: none;">0</span>
</div>
```

## Tính Năng

### ✅ Đã Thực Hiện:
1. Lưu trữ thông báo trong database
2. Gửi thông báo real-time qua WebSocket
3. Hiển thị browser notification
4. Hiển thị in-page notification toast
5. Đếm và hiển thị số thông báo chưa đọc
6. Đánh dấu thông báo đã đọc
7. Tự động reconnect khi mất kết nối WebSocket
8. API endpoint để quản lý thông báo

### 🔄 Có Thể Mở Rộng:
1. Thông báo email (sử dụng PHPMailer có sẵn)
2. Thông báo SMS
3. Push notification cho mobile app (Firebase FCM)
4. Lọc và tìm kiếm thông báo
5. Cài đặt tùy chọn nhận thông báo
6. Thống kê thông báo

## Cấu Trúc Thông Báo

### Payload Thông Báo:
```json
{
    "command": "notification",
    "type": "ketquaxetnghiem",
    "receiver": "BS001",
    "title": "Kết quả xét nghiệm đã có",
    "content": "Kết quả xét nghiệm cho lịch #123 đã được cập nhật. Vui lòng kiểm tra.",
    "malichxetnghiem": 123,
    "mathongbao": 456,
    "timestamp": "2025-12-08 17:30:00"
}
```

## Bảo Mật

1. **Authentication**: Kiểm tra session trước khi gửi/nhận thông báo
2. **Authorization**: Chỉ gửi thông báo cho bác sĩ liên quan
3. **Input Validation**: Escape tất cả input trước khi lưu database
4. **SQL Injection Prevention**: Sử dụng prepared statements hoặc escape
5. **XSS Prevention**: Escape output khi hiển thị thông báo

## Testing

### Test Gửi Thông Báo:
1. Đăng nhập với tài khoản nhân viên xét nghiệm
2. Cập nhật kết quả xét nghiệm
3. Đăng nhập với tài khoản bác sĩ tương ứng
4. Kiểm tra thông báo hiển thị

### Test WebSocket:
```javascript
// Trong browser console
const ws = new WebSocket('ws://localhost:8080');
ws.onopen = () => {
    ws.send(JSON.stringify({
        command: 'register',
        username: 'BS001'
    }));
};
```

## Troubleshooting

### WebSocket không kết nối được:
- Kiểm tra `server.php` đang chạy
- Kiểm tra port 8080 không bị block
- Kiểm tra firewall settings

### Thông báo không hiển thị:
- Kiểm tra browser console có lỗi không
- Kiểm tra quyền notification của browser
- Kiểm tra user đã đăng ký với WebSocket chưa

### Database errors:
- Kiểm tra bảng `thongbao` đã được tạo
- Kiểm tra foreign key constraints
- Kiểm tra database credentials trong `ketnoi.php`

## Liên Hệ và Hỗ Trợ

Nếu có vấn đề hoặc câu hỏi, vui lòng tạo issue trong repository hoặc liên hệ team phát triển.
