<?php
include_once('ketnoi.php');
class mNguoiKham{
    public function allnguoikham(){
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if($con){
            $str = "SELECT bs.mabacsi AS id, nd.hoten, 'Bác sĩ' AS vaitro, bs.machuyenkhoa FROM bacsi bs 
            JOIN nguoidung nd ON bs.mabacsi = nd.manguoidung 
            JOIN chuyenkhoa ck on bs.machuyenkhoa = ck.machuyenkhoa 
            UNION 
            SELECT cg.machuyengia AS id, nd.hoten, 'Chuyên gia' AS vaitro, cg.malinhvuc AS chuyenkhoa 
            FROM chuyengia cg JOIN nguoidung nd ON cg.machuyengia = nd.manguoidung 
            JOIN linhvuc lv on cg.malinhvuc = lv.malinhvuc;";
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }
}