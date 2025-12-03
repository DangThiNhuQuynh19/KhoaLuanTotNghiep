-- Migration: Add nguoitao column to hosobenhan table
-- This column stores the ID of the creator (mabacsi or machuyengia)
-- Date: 2025-12-03

ALTER TABLE `hosobenhan` 
ADD COLUMN `nguoitao` VARCHAR(100) NULL AFTER `mabenhnhan`,
ADD INDEX `idx_nguoitao` (`nguoitao`);

-- Update existing records to set nguoitao based on chitiethoso records
-- This will populate nguoitao for existing records where possible
UPDATE hosobenhan hs
LEFT JOIN (
    SELECT ct.mahoso, ct.mabacsi
    FROM chitiethoso ct
    WHERE ct.machitiethoso IN (
        SELECT MIN(machitiethoso) 
        FROM chitiethoso 
        GROUP BY mahoso
    )
) first_record ON hs.mahoso = first_record.mahoso
SET hs.nguoitao = first_record.mabacsi
WHERE hs.nguoitao IS NULL AND first_record.mabacsi IS NOT NULL;
