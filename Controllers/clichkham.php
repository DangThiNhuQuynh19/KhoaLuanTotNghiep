<?php
include_once(__DIR__ . '/../Models/mlichkham.php');

class cLichKham {
    public function getLichKhamOfBacSiByNgay($ngay, $id, $gioHienTai = null) {
        $p = new mLichKham();
        $tbl = $p->lichkhambs($ngay, $id, $gioHienTai);
    
        if (!$tbl) {
            return false;
        } else {
            return ($tbl->num_rows > 0) ? $tbl : false;
        }
    }
    public function getLichKhamOfChuyenGiaByNgay($ngay, $id, $gioHienTai = null) {
        $p = new mLichKham();
        $tbl = $p->lichkhamcg($ngay, $id, $gioHienTai);
    
        if (!$tbl) {
            return false;
        } else {
            return ($tbl->num_rows > 0) ? $tbl : false;
        }
    }
    public function getlich($idca, $ngay, $idbs) {
        $p = new mLichKham();
        $tbl = $p->xemlich($idca, $ngay, $idbs);
    
        if ($tbl === false) {
            return -1; // lỗi kết nối DB
        }
    
        if ($tbl->num_rows > 0) {
            $result = [];
            while ($row = $tbl->fetch_assoc()) {
                $result[] = [
                    'giokham'  => $row['giokham'],
                    'thongtin' => $row['thongtin']
                ];
            }
            return $result; // trả về mảng dữ liệu
        } else {
            return 0; // không tìm thấy lịch
        }
    }
    
    
    public function getThongTinNguoi($manguoidung){
        $p = new mLichKham();
        $nguoi = $p->getThongTinNguoi($manguoidung);
    
        if (!$nguoi) {
            return -1; 
        }
    
        return $nguoi; 
    }
    
    public function getLichBacSiTheoNgay($ngay, $mabacsi){
        $p = new mLichKham();
        $tbl = $p->getLichBacSiTheoNgay($ngay, $mabacsi);
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl;
            }else{
                return 0;
            }
        }
    }
    public function getAllLichKhamByNgay($ngay){
        $p = new mLichKham();
        $tbl = $p->getTatCaLichKhamTheoNgay($ngay);
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl;
            }else{
                return 0;
            }
        }
    }
    public function getLichTrongCuaNguoi($manguoi,$tuNgay){
        $p = new mLichKham();
        $tbl = $p->getLichTrongTheoNguoi($manguoi,$tuNgay);
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl;
            }else{
                return 0;
            }
        }
    }
    // public function getLichTrongCuaNguoi1( $manguoi, $tuNgay){
    //     $p = new mLichKham();
    //     $tbl = $p->getLichTrongTheoNguoi1( $manguoi, $tuNgay);
    //     if(!$tbl){
    //         return -1;
    //     }else{
    //         if($tbl->num_rows > 0){
    //             return $tbl;
    //         }else{
    //             return 0;
    //         }
    //     }
    // }
    public function getlichhen($bs, $bn) {
        $p = new mLichKham();
        $tbl = $p->kiemtragiohen($bs, $bn);
    
        if (!$tbl) return -1; // Không kết nối được CSDL
    
        $today = date('Y-m-d');
        $now = time();
    
        while ($row = $tbl->fetch_assoc()) {
            // Lấy đúng phần ngày
            $ngayKham = substr($row['ngaykham'], 0, 10);
    
            if ($ngayKham === $today) {
                // Kiểm tra giờ bắt đầu và kết thúc không rỗng
                if (!empty($row['giobatdau']) && !empty($row['gioketthuc'])) {
                    $batdau = strtotime($ngayKham . ' ' . $row['giobatdau']);
                    $ketthuc = strtotime($ngayKham . ' ' . $row['gioketthuc']);
    
                    if ($now >= $batdau && $now <= $ketthuc) {
                        return true; // Đúng thời điểm
                    }
                }
            }
        }
    
        return 0; // Có lịch nhưng không phải hôm nay hoặc chưa đến giờ
    }
    

    public function getlichhennhanvien(){
        $p = new mLichKham();
        $tbl = $p->lichhen();
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl;
            }else{
                return 0;
            }
        }
    }

    public function get_lickham_mabacsi($mabacsi){
        $p = new mLichKham();
        $tbl = $p->select_lichkham_mabacsi($mabacsi);
        $list =[];
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                while ($row = $tbl->fetch_assoc()) {
                    $list[] = $row;
                }
                return $list;
            }else{
                return 0;
            }
        }
    }

}
?>
