<?php
class XuLyThanhToan {
    
    public function kiem_tra_lich_hen_het_han() {
        global $conn;
        $thoi_gian_hien_tai = date('Y-m-d H:i:s');
        
        // Tìm các lịch hẹn chờ thanh toán quá 30 phút
        $sql = "SELECT lh.malichhen, lh.mabenhnhan, et.thoi_gian_het_han 
                FROM lichxetnghiem lh 
                JOIN email_thanh_toan et ON lh.malichhen = et.ma_lich_hen 
                WHERE lh.trangthai = 'Chờ thanh toán' 
                AND et.thoi_gian_het_han < ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $thoi_gian_hien_tai);
        $stmt->execute();
        $ket_qua = $stmt->get_result();
        
        $danh_sach_huy = [];
        while ($lich_hen = $ket_qua->fetch_assoc()) {
            if ($this->huy_lich_hen_tu_dong($lich_hen['malichhen'])) {
                $danh_sach_huy[] = $lich_hen['malichhen'];
            }
        }
        
        return $danh_sach_huy;
    }
    
    private function huy_lich_hen_tu_dong($ma_lich_hen) {
        global $conn;
        
        try {
            $conn->begin_transaction();
            
            // Cập nhật trạng thái lịch hẹn
            $sql_cap_nhat_lich = "UPDATE lichxetnghiem SET trangthai = 'Đã hủy - Quá hạn thanh toán' WHERE malichhen = ?";
            $stmt1 = $conn->prepare($sql_cap_nhat_lich);
            $stmt1->bind_param("i", $ma_lich_hen);
            $stmt1->execute();
            
            // Cập nhật trạng thái email
            $sql_cap_nhat_email = "UPDATE email_thanh_toan SET trang_thai = 'Hết hạn' WHERE ma_lich_hen = ?";
            $stmt2 = $conn->prepare($sql_cap_nhat_email);
            $stmt2->bind_param("i", $ma_lich_hen);
            $stmt2->execute();
            
            // Ghi log hủy lịch
            $sql_log = "INSERT INTO log_huy_lich (ma_lich_hen, ly_do, thoi_gian_huy) VALUES (?, 'Quá hạn thanh toán', NOW())";
            $stmt3 = $conn->prepare($sql_log);
            $stmt3->bind_param("i", $ma_lich_hen);
            $stmt3->execute();
            
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi hủy lịch hẹn: " . $e->getMessage());
            return false;
        }
    }
    
    public function xu_ly_thanh_toan_thanh_cong($ma_lich_hen, $phuong_thuc_thanh_toan, $so_tien) {
        global $conn;
        
        try {
            $conn->begin_transaction();
            
            // Cập nhật trạng thái lịch hẹn
            $sql_cap_nhat_lich = "UPDATE lichxetnghiem SET trangthai = 'Đã thanh toán' WHERE malichhen = ?";
            $stmt1 = $conn->prepare($sql_cap_nhat_lich);
            $stmt1->bind_param("i", $ma_lich_hen);
            $stmt1->execute();
            
            // Cập nhật trạng thái email
            $sql_cap_nhat_email = "UPDATE email_thanh_toan SET trang_thai = 'Đã thanh toán' WHERE ma_lich_hen = ?";
            $stmt2 = $conn->prepare($sql_cap_nhat_email);
            $stmt2->bind_param("i", $ma_lich_hen);
            $stmt2->execute();
            
            // Lưu thông tin thanh toán
            $sql_thanh_toan = "INSERT INTO thong_tin_thanh_toan (ma_lich_hen, phuong_thuc, so_tien, thoi_gian_thanh_toan) 
                              VALUES (?, ?, ?, NOW())";
            $stmt3 = $conn->prepare($sql_thanh_toan);
            $stmt3->bind_param("isd", $ma_lich_hen, $phuong_thuc_thanh_toan, $so_tien);
            $stmt3->execute();
            
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi xử lý thanh toán: " . $e->getMessage());
            return false;
        }
    }
}
?>
