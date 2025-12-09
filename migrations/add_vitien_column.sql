-- Migration: Add vitien (wallet balance) column to taikhoan table
-- Date: 2025-12-09

ALTER TABLE `taikhoan` 
ADD COLUMN `vitien` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Số dư ví của người dùng';
