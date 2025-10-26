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
            $ngay_bat_dau = date('Y-m-d');
            
            // Tính ngày kết thúc chính xác 6 tháng sau
            $ngay_ket_thuc = date('Y-m-d', strtotime('+6 months', strtotime($ngay_bat_dau))); 
            
            $success_count = 0;
            
            if (empty($manv_list)) {
                $p->dongketnoi($con);
                return 0;
            }
    
            // Bắt đầu Transaction để tăng hiệu suất (chỉ hoạt động nếu MySQLi hỗ trợ)
            // Nếu clsKetNoi sử dụng MySQLi, đây là cú pháp:
            $con->begin_transaction(); 
    
            $insert_values = [];
            $placeholders = [];
            $total_days = 0;
    
            foreach ($manv_list as $manv) {
                $current_date = $ngay_bat_dau;
                
                // Lặp từ ngày bắt đầu cho đến ngày trước ngày kết thúc 6 tháng
                while (strtotime($current_date) < strtotime($ngay_ket_thuc)) { 
                    
                    // Thu thập dữ liệu để insert nhiều bản ghi trong một câu lệnh
                    $insert_values[] = $manv;
                    $insert_values[] = $current_date; 
                    $insert_values[] = $macalam;
                    $insert_values[] = $hinhthuc;
                    
                    $placeholders[] = "(?, ?, ?, ?)";
    
                    // Chuyển sang ngày tiếp theo
                    $current_date = date('Y-m-d', strtotime('+1 day', strtotime($current_date)));
                    $total_days++;
                }
            }
            
            // Nếu không có ngày nào cần insert, thoát
            if (empty($placeholders)) {
                 $con->rollback();
                 $p->dongketnoi($con);
                 return 0;
            }
    
            // Tạo câu lệnh SQL hoàn chỉnh để chèn tất cả dữ liệu cùng lúc
            $query = "INSERT INTO lichlamviec 
                      (manguoidung, ngaylam, macalamviec, hinhthuclamviec) 
                      VALUES " . implode(', ', $placeholders);
            
            // Sử dụng prepare statement để tránh SQL Injection
            if ($stmt = $con->prepare($query)) {
                // Xác định kiểu dữ liệu cho tất cả tham số (ssisssis...)
                // Với 4 tham số (s, s, i, s) * số lần lặp, đây là một chuỗi rất dài.
                // Để đơn giản, ta chỉ cần gắn các giá trị (bound parameters)
                
                // Binding các tham số (ví dụ sử dụng call_user_func_array cho MySQLi prepare)
                $types = str_repeat('sisi', count($manv_list) * (int)($total_days / count($manv_list)));
                
                $bind_params[] = &$types;
                for ($i = 0; $i < count($insert_values); $i++) {
                    $bind_params[] = &$insert_values[$i];
                }
                
                // Vì bind_param phức tạp với số lượng lớn, ta sẽ dùng cách đơn giản hơn
                // (Hoặc giả định sử dụng PDO/MySQLi object thay vì chỉ MySQLi procedural)
                // TẠM THỜI: Chèn từng nhân viên một nếu prepare binding quá phức tạp
                
                // --- Chèn từng nhân viên (Dễ code, chậm hơn) ---
                $success_count = 0;
                $insert_query = "INSERT INTO lichlamviec (manguoidung, ngaylam, macalamviec, hinhthuclamviec) VALUES (?, ?, ?, ?)";
                
                if ($stmt_single = $con->prepare($insert_query)) {
                    foreach ($manv_list as $manv) {
                        $current_date = $ngay_bat_dau;
                        while (strtotime($current_date) < strtotime($ngay_ket_thuc)) {
                             $stmt_single->bind_param("ssis", $manv, $current_date, $macalam, $hinhthuc);
                             if ($stmt_single->execute()) {
                                 $success_count++;
                             }
                             $current_date = date('Y-m-d', strtotime('+1 day', strtotime($current_date)));
                        }
                    }
                    $stmt_single->close();
                } else {
                    // Lỗi chuẩn bị câu lệnh
                    $con->rollback();
                    $p->dongketnoi($con);
                    return -1;
                }
                // ------------------------------------------------
                
                // Kết thúc Transaction
                $con->commit();
                $p->dongketnoi($con);
                return $success_count;
    
            } else {
                // Lỗi prepare statement
                $con->rollback();
                $p->dongketnoi($con);
                return -1;
            }
        }
    }
 ?>