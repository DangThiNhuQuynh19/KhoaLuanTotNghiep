<?php
 include_once('ketnoi.php');
 class mPhieuKhamBenh{
        public function insertphieukham($maphieukb,$ngaykham,$makhunggiokb,$mabacsi,$mabenhnhan,$matrangthai){
            $p = new clsketnoi();
            $truyvan = "INSERT INTO phieukhambenh(maphieukhambenh,ngaykham,makhunggiokb,mabacsi,mabenhnhan,matrangthai) VALUES ('$maphieukb','$ngaykham','$makhunggiokb','$mabacsi','$mabenhnhan','$matrangthai')";
            $con = $p->moketnoi();
            $kq = mysqli_query($con, $truyvan);
            $p->dongketnoi($con);
            return $kq;
        }
        public function phieukhambenhcuabn($idbn){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT *
                    FROM phieukhambenh pk 
                    JOIN bacsi bs ON pk.mabacsi = bs.mabacsi 
                    JOIN calamviec cv ON pk.macalamviec = cv.macalamviec
                    WHERE pk.mabenhnhan = '$idbn'";
        
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function kiemTraTrungLich($mabenhnhan, $ngaykham, $makhunggiokhambenh) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM phieukhambenh 
                    WHERE mabenhnhan = '$mabenhnhan' 
                      AND ngaykham = '$ngaykham' 
                      AND makhunggiokb = '$makhunggiokhambenh'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }   
        public function phieukhambenhcuataikhoan($tentk, $status = null, $ngay = null) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if ($con) {
                // 🔒 Escape dữ liệu đầu vào để chống SQL Injection
                $email = mysqli_real_escape_string($con, trim($tentk));
                $status = $status !== null ? mysqli_real_escape_string($con, trim($status)) : null;
                $ngay = $ngay !== null ? mysqli_real_escape_string($con, trim($ngay)) : null;
        
                // 📌 Điều kiện WHERE chung cho cả 2 loại phiếu
                $whereCondition = "
                    (nd_bn.email = '$email' 
                    OR bn.manguoigiamho IN (SELECT manguoidung FROM nguoidung WHERE email = '$email'))
                ";
        
                // 👨‍⚕️ Truy vấn cho BÁC SĨ
                $sql1 = "
                    SELECT 
                        pk.*, 
                        nd_bn.hoten, 
                        nd_nd.hoten AS hotenbacsi, 
                        nd_bn.email, 
                        'Bac si' AS loai, 
                        tt.tentrangthai, 
                        ck.tenchuyenkhoa AS tenchuyenkhoa,
                        kg.*, 
                        clv.tenca AS tenca, 
                        llv.malichlamviec, 
                        llv.hinhthuclamviec, 
                        CASE 
                            WHEN llv.maphong IS NULL THEN 'Online'
                            ELSE CONCAT(
                                'Tòa ', COALESCE(p.tentoa,''), 
                                ' / Tầng ', COALESCE(p.tang,''), 
                                ' / Số phòng ', COALESCE(p.sophong,'')
                            )
                        END AS tenphongdaydu
                    FROM phieukhambenh pk
                    JOIN trangthai tt ON tt.matrangthai = pk.matrangthai
                    JOIN khunggiokhambenh kg ON kg.makhunggiokb = pk.makhunggiokb
                    JOIN calamviec clv ON clv.macalamviec = kg.macalamviec
                    LEFT JOIN lichlamviec llv 
                        ON llv.manguoidung = pk.mabacsi 
                        AND llv.ngaylam = pk.ngaykham
                        AND llv.macalamviec = kg.macalamviec
                    JOIN benhnhan bn ON bn.mabenhnhan = pk.mabenhnhan
                    JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan
                    JOIN bacsi bs ON bs.mabacsi = pk.mabacsi
                    JOIN chuyenkhoa ck ON ck.machuyenkhoa = bs.machuyenkhoa
                    JOIN nguoidung nd_nd ON nd_nd.manguoidung = bs.mabacsi
                    LEFT JOIN phong p ON p.maphong = llv.maphong
                    WHERE $whereCondition
                ";
        
                // 🧠 Truy vấn cho CHUYÊN GIA
                $sql2 = "
                    SELECT 
                        pk.*, 
                        nd_bn.hoten, 
                        nd_nd.hoten AS hotenbacsi, 
                        nd_bn.email, 
                        'Chuyen gia' AS loai, 
                        tt.tentrangthai, 
                        lv.tenlinhvuc AS tenchuyenkhoa,
                        kg.*, 
                        clv.tenca AS tenca, 
                        llv.malichlamviec, 
                        llv.hinhthuclamviec,
                        CASE 
                            WHEN llv.maphong IS NULL THEN 'Online'
                            ELSE CONCAT(
                                'Tòa ', COALESCE(p.tentoa,''), 
                                ' / Tầng ', COALESCE(p.tang,''), 
                                ' / Số phòng ', COALESCE(p.sophong,'')
                            )
                        END AS tenphongdaydu
                    FROM phieukhambenh pk
                    JOIN trangthai tt ON tt.matrangthai = pk.matrangthai
                    JOIN khunggiokhambenh kg ON kg.makhunggiokb = pk.makhunggiokb
                    JOIN calamviec clv ON clv.macalamviec = kg.macalamviec
                    LEFT JOIN lichlamviec llv 
                        ON llv.manguoidung = pk.mabacsi 
                        AND llv.ngaylam = pk.ngaykham
                        AND llv.macalamviec = kg.macalamviec
                    JOIN benhnhan bn ON bn.mabenhnhan = pk.mabenhnhan
                    JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan
                    JOIN chuyengia cg ON cg.machuyengia = pk.mabacsi
                    JOIN linhvuc lv ON lv.malinhvuc = cg.malinhvuc
                    JOIN nguoidung nd_nd ON nd_nd.manguoidung = cg.machuyengia
                    LEFT JOIN phong p ON p.maphong = llv.maphong
                    WHERE $whereCondition
                ";
        
                // 📝 Thêm điều kiện lọc nếu có
                if (!empty($status)) {
                    $sql1 .= " AND LOWER(TRIM(tt.tentrangthai)) = LOWER(TRIM('$status'))";
                    $sql2 .= " AND LOWER(TRIM(tt.tentrangthai)) = LOWER(TRIM('$status'))";
                }
                if (!empty($ngay)) {
                    $sql1 .= " AND pk.ngaykham = '$ngay'";
                    $sql2 .= " AND pk.ngaykham = '$ngay'";
                }
        
                // 🔗 Gộp 2 truy vấn lại bằng UNION ALL
                $sql = "
                    ($sql1)
                    UNION ALL
                    ($sql2)
                    ORDER BY ngaykham DESC, giobatdau ASC
                ";
        
                // ⏳ Thực thi và trả kết quả
                $tbl = $con->query($sql);
                $p->dongketnoi($con);
        
                return $tbl;
            } else {
                return false;
            }
        }
        
        
        
        public function huyPhieuKhamBenh($maphieukb) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "update phieukhambenh set matrangthai='7' WHERE maphieukhambenh = '$maphieukb'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }   
        public function updatePhieuKhamBenh($maphieukb) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "update phieukhambenh set trangthai='đã khám' WHERE maphieukb = '$maphieukb'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }   
        public function phieukhamtheoidpk($maphieukb) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * FROM phieukhambenh WHERE maphieukhambenh = '$maphieukb' ";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        } 
        
        public function select_lichkhamonl_mabacsi($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.mabacsi = '$mabacsi' AND llv.hinhthuclamviec = 'online';";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_lichkhamonl_machuyengia($machuyengia){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN chuyengia AS bs ON pk.mabacsi = bs.machuyengia 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.machuyengia 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.machuyengia = '$machuyengia' AND llv.hinhthuclamviec = 'online';";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_lichkhamoff_mabacsi($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.mabacsi = '$mabacsi' AND llv.hinhthuclamviec = 'offline';";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_lichkhamoff_machuyengia($machuyengia){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN chuyengia AS bs ON pk.mabacsi = bs.machuyengia 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.machuyengia 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.machuyengia = '$machuyengia' AND llv.hinhthuclamviec = 'offline';";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        // public function update_trangthai_phieukhambenh(){
        //     $p = new clsKetNoi();
        //     $con = $p->moketnoi();
        //     $con->set_charset('utf8');
        //     if ($con) {
        //         $sql = "UPDATE phieukhambenh pk
        //                 JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb
        //                 JOIN trangthai tt on tt.matrangthai = pk.matrangthai
        //                 SET tt.tentrangthai = 'Đã khám'
        //                 WHERE (
        //                     CURDATE() > pk.ngaykham OR
        //                     (CURDATE() = pk.ngaykham AND CURTIME() > kg.gioketthuc)
        //                 )
        //                 AND tt.tentrangthai != 'Đã khám'";
                        
        //         $result = $con->query($sql);
        //         $p->dongketnoi($con);
        //         return $result;
        //     } else {
        //         return false;
        //     }
        // }

        public function select_phieukhamonl_homnay($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec, ck.* FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                JOIN chuyenkhoa AS ck ON bs.machuyenkhoa = ck.machuyenkhoa
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.mabacsi = '$mabacsi' AND llv.hinhthuclamviec = 'online' AND  pk.ngaykham = CURDATE()";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_phieukhamoff_homnay($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.mabacsi = '$mabacsi' AND llv.hinhthuclamviec = 'offline' AND  pk.ngaykham = CURDATE()";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_phieukham_homqua($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*
                FROM phieukhambenh AS pk
                JOIN trangthai AS tt ON tt.matrangthai = pk.matrangthai
                JOIN khunggiokhambenh AS kg ON kg.makhunggiokb = pk.makhunggiokb
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN nguoidung AS nd_bs ON nd_bs.manguoidung = bs.mabacsi
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
                JOIN nguoidung AS nd_bn ON nd_bn.manguoidung = bn.mabenhnhan
                WHERE bs.mabacsi = '$mabacsi' AND  pk.ngaykham = CURDATE()-1";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function timkiem_phieukhamonl($tukhoa,$trangthai,$ngay,$mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.mabacsi = '$mabacsi' AND llv.hinhthuclamviec = 'online'";
                if (!empty($tukhoa)) {
                    $str .= " AND (nd_bn.hoten LIKE '%$tukhoa%' OR pk.maphieukhambenh LIKE '%$tukhoa%')";
                }
                if (!empty($trangthai)) {
                    $str .= " AND tt.tentrangthai = '$trangthai'";
                }
                if (!empty($ngay)) {
                    $ngay_mysql = date("Y-m-d", strtotime($ngay));
                    $str .= " AND pk.ngaykham = '$ngay_mysql'";
                }
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function timkiem_phieukhamoff($tukhoa,$trangthai,$ngay,$mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.mabacsi = '$mabacsi' AND llv.hinhthuclamviec = 'offline'";
                if (!empty($tukhoa)) {
                    $str .= " AND (nd_bn.hoten LIKE '%$tukhoa%' OR pk.maphieukhambenh LIKE '%$tukhoa%')";
                }
                if (!empty($trangthai)) {
                    $str .= " AND tt.tentrangthai = '$trangthai'";
                }
                if (!empty($ngay)) {
                    $ngay_mysql = date("Y-m-d", strtotime($ngay));
                    $str .= " AND pk.ngaykham = '$ngay_mysql'";
                }
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function timkiem_phieukhamonlcg($tukhoa,$trangthai,$ngay,$mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, cg.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN chuyengia AS cg ON pk.mabacsi = cg.machuyengia 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = cg.machuyengia 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE cg.machuyengia = '$mabacsi' AND llv.hinhthuclamviec = 'online'";
                if (!empty($tukhoa)) {
                    $str .= " AND (nd_bn.hoten LIKE '%$tukhoa%' OR pk.maphieukhambenh LIKE '%$tukhoa%')";
                }
                if (!empty($trangthai)) {
                    $str .= " AND tt.tentrangthai = '$trangthai'";
                }
                if (!empty($ngay)) {
                    $ngay_mysql = date("Y-m-d", strtotime($ngay));
                    $str .= " AND pk.ngaykham = '$ngay_mysql'";
                }
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function timkiem_phieukhamoffcg($tukhoa,$trangthai,$ngay,$mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten AS tenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, nd_bn.*, bs.*, 
                llv.ngaylam, llv.hinhthuclamviec FROM phieukhambenh AS pk 
                JOIN khunggiokhambenh kg ON pk.makhunggiokb = kg.makhunggiokb 
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai 
                JOIN chuyengia AS bs ON pk.mabacsi = bs.machuyengia 
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.machuyengia 
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan 
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan 
                JOIN lichlamviec llv ON llv.manguoidung = pk.mabacsi 
                AND llv.ngaylam = pk.ngaykham AND llv.macalamviec = kg.macalamviec 
                JOIN calamviec clv ON clv.macalamviec = llv.macalamviec 
                WHERE bs.machuyengia = '$mabacsi' AND llv.hinhthuclamviec = 'offline'";
                if (!empty($tukhoa)) {
                    $str .= " AND (nd_bn.hoten LIKE '%$tukhoa%' OR pk.maphieukhambenh LIKE '%$tukhoa%')";
                }
                if (!empty($trangthai)) {
                    $str .= " AND tt.tentrangthai = '$trangthai'";
                }
                if (!empty($ngay)) {
                    $ngay_mysql = date("Y-m-d", strtotime($ngay));
                    $str .= " AND pk.ngaykham = '$ngay_mysql'";
                }
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_phieukham_trongtuan($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT COUNT(*) AS solichhentrongtuan
                FROM phieukhambenh
                WHERE WEEK(ngaykham, 1) = WEEK(CURDATE(), 1)
                AND YEAR(ngaykham) = YEAR(CURDATE())
                AND mabacsi = '$mabacsi';";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_lickham_sapden($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten as hotenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, c.*, bs.*, nd_bn.*
                FROM phieukhambenh AS pk
                JOIN khunggiokhambenh AS kg ON kg.makhunggiokb = pk.makhunggiokb
                JOIN trangthai AS tt ON tt.matrangthai = pk.matrangthai
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN nguoidung AS nd_bs ON nd_bs.manguoidung = bs.mabacsi
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
                JOIN nguoidung AS nd_bn ON nd_bn.manguoidung = bn.mabenhnhan
                JOIN chuyenkhoa AS c ON c.machuyenkhoa = bs.machuyenkhoa
                WHERE bs.mabacsi = '$mabacsi'
                AND pk.ngaykham >= CURDATE() 
                AND kg.giobatdau >= CURTIME()
                ORDER BY pk.ngaykham ASC, kg.giobatdau ASC
                LIMIT 1;";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_phieukhambenh(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from phieukhambenh";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
    }
?>