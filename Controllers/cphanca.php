<?php
include_once("../Models/mcalam.php");
class cPhanCa{
    public function phanCaNhanVien($macalam, $hinhthuc, $manv_list) {
        $mPhanCa = new mCaLam();
        if (empty($macalam) || empty($hinhthuc) || empty($manv_list)) {
            return false;
        }
        
        // Gọi hàm phân ca theo thời hạn 6 tháng
        $count = $mPhanCa->phanCaTheoThoiHan($macalam, $hinhthuc, $manv_list);

        if ($count > 0) {
            return true;
        } else {
            // Trả về false nếu lỗi CSDL (count = -1) hoặc không chèn được (count = 0)
            return false;
        }
    }


}
?>