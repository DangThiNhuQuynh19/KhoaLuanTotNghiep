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

        // ✅ Kiểm tra dữ liệu đầu vào
        if (empty($macalam) || empty($hinhthuc) || empty($manv_list)) {
            return [
                "success" => false,
                "message" => "Thiếu dữ liệu đầu vào."
            ];
        }
    
        // ✅ Đảm bảo maphong tồn tại trong từng phần tử
        foreach ($manv_list as &$item) {
    
            if (!isset($item["maphong"])) {
                $item["maphong"] = null;
            }
    
            // ✅ Online → maphong = null
            if ($hinhthuc === "Online") {
                $item["maphong"] = null;
            }
        }
    
        // ✅ Gọi Model đúng cấu trúc kiểu hàm get_calam()
        $p = new mCaLam();
        $count = $p->phanCaTheoThoiHan($macalam, $hinhthuc, $manv_list);
    
        // ✅ Xử lý kết quả trả về
        if ($count === -1) {
            return [
                "success" => false,
                "message" => "Lỗi CSDL."
            ];
        }
    
        if ($count === 0) {
            return [
                "success" => false,
                "message" => "Không có bản ghi nào được tạo."
            ];
        }
    
        // ✅ Thành công
        return [
            "success" => true,
            "message" => "Tạo thành công $count lịch làm việc trong 6 tháng."
        ];
    }
    

}
?>