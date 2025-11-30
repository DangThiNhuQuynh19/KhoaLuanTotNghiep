-- Migration: Create tinnhan (messaging) table
-- Version: 1.0.0
-- Date: 2024-01-01

-- Create tinnhan table if not exists
CREATE TABLE IF NOT EXISTS `tinnhan` (
  `matinnhan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tentk_gui` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'Sender username',
  `tentk_nhan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'Receiver username',
  `noidung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL COMMENT 'Message content',
  `thoigiangui` datetime NOT NULL COMMENT 'Sent timestamp',
  PRIMARY KEY (`matinnhan`),
  INDEX `idx_sender` (`tentk_gui`),
  INDEX `idx_receiver` (`tentk_nhan`),
  INDEX `idx_sender_receiver` (`tentk_gui`, `tentk_nhan`),
  INDEX `idx_time` (`thoigiangui`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci COMMENT='Chat messages between users';

-- Add read_at column for message read tracking (optional enhancement)
-- ALTER TABLE `tinnhan` ADD COLUMN `read_at` datetime DEFAULT NULL COMMENT 'Read timestamp' AFTER `thoigiangui`;
