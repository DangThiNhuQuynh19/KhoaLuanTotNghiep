-- Create notification table for push notifications
CREATE TABLE IF NOT EXISTS `thongbao` (
  `mathongbao` INT(11) NOT NULL AUTO_INCREMENT,
  `manguoidung` VARCHAR(100) NOT NULL,
  `tieude` VARCHAR(255) NOT NULL,
  `noidung` TEXT NOT NULL,
  `loaithongbao` VARCHAR(50) NOT NULL DEFAULT 'ketquaxetnghiem',
  `malichxetnghiem` INT(11) DEFAULT NULL,
  `daxem` TINYINT(1) NOT NULL DEFAULT 0,
  `ngaytao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mathongbao`),
  KEY `idx_manguoidung` (`manguoidung`),
  KEY `idx_malichxetnghiem` (`malichxetnghiem`),
  KEY `idx_daxem` (`daxem`),
  FOREIGN KEY (`manguoidung`) REFERENCES `nguoidung`(`manguoidung`) ON DELETE CASCADE,
  FOREIGN KEY (`malichxetnghiem`) REFERENCES `lichxetnghiem`(`malichxetnghiem`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
