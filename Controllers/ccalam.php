<?php
include_once("Models/mcalam.php");
class cCaLam{
    public function get_calam(){
        $p = new mCaLam();
        $tbl = $p->select_CaLam();
        if (!$tbl) {
            return -1; 
        } else {
            $list=array();
            if ($tbl->num_rows > 0) {
                while($row = $tbl->fetch_assoc()){
                    $list[]=$row;
                }  
                return $list;                 
            } else {
                return 0; 
            }
        }
    }

    public function phanCaNhanVien($macalam, $hinhthuc, $manv_list) {
        if (empty($macalam) || empty($hinhthuc) || empty($manv_list)) {
            return false;
        }
        
        // Gọi hàm phân ca theo thời hạn 6 tháng
        $count = $this->mPhanCa->phanCaTheoThoiHan($macalam, $hinhthuc, $manv_list);

        if ($count > 0) {
            return true;
        } else {
            // Trả về false nếu lỗi CSDL (count = -1) hoặc không chèn được (count = 0)
            return false;
        }
    }


}
?>