<?php
include_once('ketnoi.php');

class mThongBao {
    // Tạo thông báo mới
    public function insert_thongbao($manguoidung, $tieude, $noidung, $loaithongbao = 'ketquaxetnghiem', $malichxetnghiem = null) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $manguoidung = $con->real_escape_string($manguoidung);
            $tieude = $con->real_escape_string($tieude);
            $noidung = $con->real_escape_string($noidung);
            $loaithongbao = $con->real_escape_string($loaithongbao);
            $malichxetnghiem_val = $malichxetnghiem ? intval($malichxetnghiem) : 'NULL';
            
            $str = "INSERT INTO thongbao (manguoidung, tieude, noidung, loaithongbao, malichxetnghiem, daxem, ngaytao) 
                    VALUES ('$manguoidung', '$tieude', '$noidung', '$loaithongbao', $malichxetnghiem_val, 0, NOW())";
            
            $result = $con->query($str);
            $insert_id = $con->insert_id;
            $p->dongketnoi($con);
            
            return $result ? $insert_id : false;
        }
        return false;
    }
    
    // Lấy danh sách thông báo của người dùng
    public function select_thongbao_by_manguoidung($manguoidung, $daxem = null) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $manguoidung = $con->real_escape_string($manguoidung);
            $str = "SELECT * FROM thongbao WHERE manguoidung = '$manguoidung'";
            
            if ($daxem !== null) {
                $daxem = intval($daxem);
                $str .= " AND daxem = $daxem";
            }
            
            $str .= " ORDER BY ngaytao DESC";
            
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }
        return false;
    }
    
    // Đếm số thông báo chưa xem
    public function count_thongbao_chuaxem($manguoidung) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $manguoidung = $con->real_escape_string($manguoidung);
            $str = "SELECT COUNT(*) as total FROM thongbao WHERE manguoidung = '$manguoidung' AND daxem = 0";
            
            $result = $con->query($str);
            $p->dongketnoi($con);
            
            if ($result && $row = $result->fetch_assoc()) {
                return $row['total'];
            }
        }
        return 0;
    }
    
    // Đánh dấu thông báo đã xem
    public function update_thongbao_daxem($mathongbao) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $mathongbao = intval($mathongbao);
            $str = "UPDATE thongbao SET daxem = 1 WHERE mathongbao = $mathongbao";
            
            $result = $con->query($str);
            $p->dongketnoi($con);
            return $result;
        }
        return false;
    }
    
    // Đánh dấu tất cả thông báo của người dùng đã xem
    public function update_all_thongbao_daxem($manguoidung) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $manguoidung = $con->real_escape_string($manguoidung);
            $str = "UPDATE thongbao SET daxem = 1 WHERE manguoidung = '$manguoidung' AND daxem = 0";
            
            $result = $con->query($str);
            $p->dongketnoi($con);
            return $result;
        }
        return false;
    }
    
    // Lấy thông tin bác sĩ từ lịch xét nghiệm (để gửi thông báo cho bác sĩ đã tạo phiếu)
    public function get_bacsi_from_lichxetnghiem($malichxetnghiem) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $malichxetnghiem = intval($malichxetnghiem);
            $str = "SELECT ct.mabacsi, nd.hoten, nd.email 
                    FROM lichxetnghiem l 
                    JOIN hosobenhan hs ON l.mahoso = hs.mahoso
                    JOIN chitiethoso ct ON ct.mahoso = hs.mahoso
                    JOIN nguoidung nd ON nd.manguoidung = ct.mabacsi
                    WHERE l.malichxetnghiem = $malichxetnghiem
                    LIMIT 1";
            
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }
        return false;
    }
    
    // Xóa thông báo
    public function delete_thongbao($mathongbao) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        
        if ($con) {
            $mathongbao = intval($mathongbao);
            $str = "DELETE FROM thongbao WHERE mathongbao = $mathongbao";
            
            $result = $con->query($str);
            $p->dongketnoi($con);
            return $result;
        }
        return false;
    }
}
?>