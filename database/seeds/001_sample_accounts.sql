-- Seed: Sample accounts for testing messaging feature
-- Version: 1.0.0
-- Date: 2024-01-01
--
-- IMPORTANT: This seed creates test accounts for manual testing.
-- Password for all test accounts: Test@123 (hashed using PHP password_hash)
-- Modify mavaitro values according to your role configuration:
--   mavaitro = 1: Bệnh nhân (Patient)
--   mavaitro = 2: Bác sĩ (Doctor)
--   mavaitro = 3: Chuyên gia (Expert)
--
-- Note: These are sample INSERT statements. You may need to adjust 
-- column names and values based on your actual taikhoan table structure.

-- Sample Doctor Account
-- Username: bacsi_test
-- Password: Test@123
INSERT INTO `taikhoan` (`tentk`, `matkhau`, `hoten`, `email`, `mavaitro`, `matrangthai`) 
VALUES (
  'bacsi_test', 
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Test@123
  'Bác Sĩ Test',
  'bacsi.test@example.com',
  2, -- Doctor role
  1  -- Active status
) ON DUPLICATE KEY UPDATE `hoten` = VALUES(`hoten`);

-- Sample Expert Account
-- Username: chuyengia_test
-- Password: Test@123
INSERT INTO `taikhoan` (`tentk`, `matkhau`, `hoten`, `email`, `mavaitro`, `matrangthai`) 
VALUES (
  'chuyengia_test', 
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Test@123
  'Chuyên Gia Test',
  'chuyengia.test@example.com',
  3, -- Expert role
  1  -- Active status
) ON DUPLICATE KEY UPDATE `hoten` = VALUES(`hoten`);

-- Sample Patient Account
-- Username: benhnhan_test
-- Password: Test@123
INSERT INTO `taikhoan` (`tentk`, `matkhau`, `hoten`, `email`, `mavaitro`, `matrangthai`) 
VALUES (
  'benhnhan_test', 
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Test@123
  'Bệnh Nhân Test',
  'benhnhan.test@example.com',
  1, -- Patient role
  1  -- Active status
) ON DUPLICATE KEY UPDATE `hoten` = VALUES(`hoten`);

-- Sample messages for testing
INSERT INTO `tinnhan` (`tentk_gui`, `tentk_nhan`, `noidung`, `thoigiangui`) VALUES
('benhnhan_test', 'bacsi_test', 'Xin chào bác sĩ, tôi cần tư vấn.', NOW() - INTERVAL 5 MINUTE),
('bacsi_test', 'benhnhan_test', 'Xin chào, tôi có thể giúp gì cho bạn?', NOW() - INTERVAL 4 MINUTE),
('benhnhan_test', 'bacsi_test', 'Tôi bị đau đầu liên tục mấy ngày nay.', NOW() - INTERVAL 3 MINUTE),
('bacsi_test', 'benhnhan_test', 'Bạn có thể mô tả cơn đau chi tiết hơn được không?', NOW() - INTERVAL 2 MINUTE);

-- Verification query
-- SELECT tentk, hoten, mavaitro FROM taikhoan WHERE tentk IN ('bacsi_test', 'chuyengia_test', 'benhnhan_test');
-- SELECT * FROM tinnhan WHERE tentk_gui IN ('bacsi_test', 'benhnhan_test') OR tentk_nhan IN ('bacsi_test', 'benhnhan_test');
