<?php
 include_once('ketnoi.php');
 class mEmail{
        public function insert_emailyeucauthanhtoan($ma_lich_hen, $email_benh_nhan, $thoi_gian_gui, $thoi_gian_het_han){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $sql = "INSERT INTO email_thanh_toan 
                (ma_lich_hen, email_benh_nhan, thoi_gian_gui, thoi_gian_het_han, trang_thai) 
                VALUES ('$ma_lich_hen', '$email_benh_nhan', '$thoi_gian_gui', '$thoi_gian_het_han', 'Đã gửi')";
                $tbl = $con->query($sql);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
    }
?>