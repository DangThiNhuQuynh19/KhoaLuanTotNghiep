<?php
include_once("Models/memailthanhtoan.php");

class cEmail{
    public function insert_emailyeucauthanhtoan($ma_lich_hen, $email_benh_nhan, $thoi_gian_gui, $thoi_gian_het_han){
        $p = new mEmail();
        $tbl = $p->insert_emailyeucauthanhtoan($ma_lich_hen, $email_benh_nhan, $thoi_gian_gui, $thoi_gian_het_han);
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
}

?>