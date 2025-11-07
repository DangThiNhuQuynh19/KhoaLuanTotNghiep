<?php
require_once('ketnoi.php');
 class mCaLam{
        public function select_calam(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from calamviec";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function phanCaTheoThoiHan($macalam, $hinhthuc, $manv_list) {
        
            $p = new clsKetNoi();
            $con = $p->moketnoi();
        
            if (!$con) {
                error_log("Lỗi kết nối CSDL trong MPhanCa.");
                return -1;
            }
        
            $con->set_charset('utf8');
        
            $ngay_bat_dau = date("Y-m-d");
            $ngay_ket_thuc = date('Y-m-d', strtotime('+6 months', strtotime($ngay_bat_dau)));
        
            $success_count = 0;
        
            // ✅ Câu lệnh có maphong
            $sql = "INSERT INTO lichlamviec 
                    (manguoidung, ngaylam, macalamviec, hinhthuclamviec, maphong)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
        
            if (!$stmt) {
                error_log("Prepare failed");
                return -1;
            }
        
            foreach ($manv_list as $item) {
        
                $manv    = $item["manv"];
                $maphong = $item["maphong"];  // ✅ lấy maphong
        
                if ($maphong === "" || $hinhthuc === "Online") {
                    $maphong = null;          // ✅ online thì phòng = null
                }
        
                $curr_date = $ngay_bat_dau;
        
                while (strtotime($curr_date) < strtotime($ngay_ket_thuc)) {
        
                    // ✅ Thêm 1 biến int hoặc null
                    $stmt->bind_param("ssisi", $manv, $curr_date, $macalam, $hinhthuc, $maphong);
        
                    if ($stmt->execute()) {
                        $success_count++;
                    }
        
                    $curr_date = date('Y-m-d', strtotime('+1 day', strtotime($curr_date)));
                }
            }
        
            $stmt->close();
            $p->dongketnoi($con);
        
            return $success_count;
        }
        
    }
 ?>