# Debug Guide: Test Result Notifications

## Issue Fixed
The notification system wasn't working because the test result submission file was using **incorrect database credentials**.

### What Was Wrong
```php
// OLD CODE - Wrong credentials
$con = mysqli_connect("localhost", "root", "", "hanhphuc");
```

### What Was Fixed
```php
// NEW CODE - Correct credentials from config
include_once('Models/ketnoi.php');
$p = new clsKetNoi();
$con = $p->moKetNoi();
```

---

## How to Test Notifications

### Step 1: Ensure Database Table Exists
```bash
mysql -u kltn -pKltntrangquynh2025@ hanhphuc
```

```sql
-- Check if thongbao table exists
SHOW TABLES LIKE 'thongbao';

-- If not exists, create it:
SOURCE database/migrations/create_thongbao_table.sql;

-- Verify structure
DESCRIBE thongbao;
```

### Step 2: Start WebSocket Server
```bash
cd /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep
php server.php
```

You should see output like:
```
Server started on localhost:8080
```

Keep this terminal running!

### Step 3: Test the Flow

1. **Login as Lab Technician**
   - Go to test result submission page
   - Select a test that has status "Đang thực hiện" (In Progress)

2. **Submit Test Results**
   - Fill in the test results form
   - Click "Lưu Cập Nhật" (Save Update)

3. **Check Logs** (in another terminal)
   ```bash
   # Check PHP error log
   tail -f /var/log/php-fpm/error.log
   # or
   tail -f /var/log/apache2/error.log
   ```

   You should see one of these logs:
   - ✅ `Notification created successfully for doctor BS001, test #123`
   - ❌ `Failed to create notification in database for test #123`
   - ❌ `Could not find doctor information for test #123`

4. **Check Database**
   ```sql
   -- See if notification was saved
   SELECT * FROM thongbao ORDER BY ngaytao DESC LIMIT 5;
   
   -- Check specific test
   SELECT * FROM thongbao WHERE malichxetnghiem = 123;
   ```

5. **Login as Doctor** (who created the test order)
   - Open browser, login as the doctor
   - You should see:
     - Bell icon with badge (if notification exists)
     - Browser notification pop-up (if permitted)
     - Toast notification slide-in

### Step 4: Debugging Common Issues

#### Issue: "Could not find doctor information"
**Cause**: The test doesn't have an associated doctor

**Check**:
```sql
SELECT l.malichxetnghiem, l.mahoso, ct.mabacsi, nd.hoten
FROM lichxetnghiem l
JOIN hosobenhan hs ON l.mahoso = hs.mahoso
JOIN chitiethoso ct ON ct.mahoso = hs.mahoso
JOIN nguoidung nd ON nd.manguoidung = ct.mabacsi
WHERE l.malichxetnghiem = 123;
```

If this returns no rows, the test doesn't have a doctor assigned.

#### Issue: "Failed to create notification in database"
**Cause**: Database error (foreign key, permissions, etc.)

**Check**:
```sql
-- Check if user exists
SELECT * FROM nguoidung WHERE manguoidung = 'BS001';

-- Check if test exists
SELECT * FROM lichxetnghiem WHERE malichxetnghiem = 123;

-- Test manual insert
INSERT INTO thongbao (manguoidung, tieude, noidung, loaithongbao, malichxetnghiem, daxem)
VALUES ('BS001', 'Test', 'Test content', 'ketquaxetnghiem', 123, 0);
```

#### Issue: WebSocket Not Connecting
**Check**:
```bash
# Check if server is running
ps aux | grep server.php

# Check if port is open
netstat -tulpn | grep 8080

# Check browser console
# Open DevTools > Console
# Look for WebSocket connection messages
```

#### Issue: Notification in Database but Not Showing
**Cause**: WebSocket server not running or browser not connected

**Fix**:
1. Ensure WebSocket server is running (`php server.php`)
2. Check browser console for connection errors
3. Reload the doctor's page to reconnect

---

## Log Messages Reference

### Success Messages
```
✓ Notification created successfully for doctor BS001, test #123
```
Means:
- Database insert succeeded
- Notification saved with ID
- WebSocket message attempted

### Error Messages

```
✗ Failed to create notification in database for test #123
```
Means:
- Database insert failed
- Check database permissions
- Check foreign key constraints

```
✗ Could not find doctor information for test #123
```
Means:
- No doctor is associated with this test
- Check the test's hoso → chitiethoso → mabacsi link

```
✗ Failed to send notification for test result #123
```
Means:
- Overall notification process failed
- Check previous error logs for specific cause

```
✗ Error sending notification: [error message]
```
Means:
- PHP exception occurred
- Check the specific error message for details

---

## Manual Notification Test

You can manually test notification creation:

```php
// Test script: test_notification.php
<?php
include_once('Controllers/cthongbao.php');

$cThongBao = new cThongBao();
$result = $cThongBao->send_test_result_notification(123); // Replace with real test ID

if ($result) {
    echo "✅ Notification sent successfully\n";
} else {
    echo "❌ Notification failed\n";
}
?>
```

Run it:
```bash
php test_notification.php
```

---

## Checklist for Working Notifications

- [ ] Database table `thongbao` exists
- [ ] Database credentials are correct in `Models/ketnoi.php`
- [ ] WebSocket server is running on port 8080
- [ ] Test has an associated doctor (via hoso → chitiethoso)
- [ ] Doctor's browser is connected to WebSocket
- [ ] Browser has notification permission granted
- [ ] PHP error logs show success message

---

## Quick Test Command

```bash
# All-in-one test
cd /home/runner/work/KhoaLuanTotNghiep/KhoaLuanTotNghiep

# 1. Check database
mysql -u kltn -pKltntrangquynh2025@ hanhphuc -e "SELECT COUNT(*) as count FROM thongbao;"

# 2. Check WebSocket server
ps aux | grep server.php

# 3. Check recent notifications
mysql -u kltn -pKltntrangquynh2025@ hanhphuc -e "SELECT * FROM thongbao ORDER BY ngaytao DESC LIMIT 3;"
```

---

## Need More Help?

1. Check the main documentation: `NOTIFICATION_SYSTEM_README.md`
2. Review architecture: `ARCHITECTURE_DIAGRAM.md`
3. Follow deployment guide: `IMPLEMENTATION_GUIDE.md`
4. Check PHP error logs for specific errors
5. Test database connection manually
6. Verify WebSocket server is running
