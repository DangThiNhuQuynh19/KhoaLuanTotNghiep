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
        public function phieukhambenhcuataikhoan($tentk, $status) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if ($con) {
                $sql = "SELECT pk.maphieukhambenh, pk.ngaykham, kg.giobatdau, tt.tentrangthai, kg.gioketthuc, nd_bs.hoten AS hotenbacsi,
                nd_bn.hoten, ck.tenchuyenkhoa
                FROM phieukhambenh pk
                JOIN trangthai tt ON tt.matrangthai = pk.matrangthai
                JOIN khunggiokhambenh kg ON kg.makhunggiokb = pk.makhunggiokb
                JOIN bacsi bs ON pk.mabacsi = bs.mabacsi
                JOIN benhnhan bn ON pk.mabenhnhan = bn.mabenhnhan
                JOIN nguoidung nd_bs ON nd_bs.manguoidung = bs.mabacsi
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = bn.mabenhnhan
                JOIN chuyenkhoa ck ON bs.machuyenkhoa = ck.machuyenkhoa
                WHERE nd_bn.email  = '$tentk'";
                if (!empty($status)) {
                    $sql .= " AND tt.tentrangthai = '$status'";
                }
        
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
                $str = "update phieukhambenh set matrangthai='8' WHERE maphieukhambenh = '$maphieukb'";
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
        
        public function select_lichkham_mabacsi($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT bs.hoten, pk.trangthai, pk.*, ca.*, bn.*
                FROM phieukhambenh AS pk
                JOIN calamviec AS ca ON ca.macalamviec = pk.macalamviec
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
                WHERE bs.mabacsi = '$mabacsi' ";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function update_trangthai_phieukhambenh(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if ($con) {
                $sql = "UPDATE phieukhambenh pk
                        JOIN calamviec ca ON pk.macalamviec = ca.macalamviec
                        SET pk.trangthai = 'đã khám'
                        WHERE (
                            CURDATE() > pk.ngaykham OR
                            (CURDATE() = pk.ngaykham AND CURTIME() > ca.gioketthuc)
                        )
                        AND pk.trangthai != 'đã khám'";
                        
                $result = $con->query($sql);
                $p->dongketnoi($con);
                return $result;
            } else {
                return false;
            }
        }

        public function select_phieukham_homnay($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd.hoten, tt.tentrangthai, pk.*, kg.*, bn.*, ck.tenchuyenkhoa
                FROM phieukhambenh AS pk
                JOIN trangthai AS tt ON tt.matrangthai = pk.matrangthai
                JOIN khunggiokhambenh AS kg ON kg.makhunggiokb = pk.makhunggiokb
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN nguoidung AS nd ON nd.manguoidung = bs.mabacsi
                JOIN chuyenkhoa ck ON ck.machuyenkhoa=bs.machuyenkhoa
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
                WHERE bs.mabacsi = '$mabacsi' AND  pk.ngaykham = CURDATE()";
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
                $str = "SELECT nd_bs.hoten, tt.tentrangthai, pk.*, kg.*, bn.*
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

        public function timkiem_phieukham($tukhoa,$trangthai,$ngay,$mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT bs.hoten, pk.trangthai, pk.*, ca.*, bn.*
                FROM phieukhambenh AS pk
                JOIN calamviec AS ca ON ca.macalamviec = pk.macalamviec
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
                WHERE bs.mabacsi = '$mabacsi'";
                if (!empty($tukhoa)) {
                    $str .= " AND (bn.hotenbenhnhan LIKE '%$tukhoa%' OR pk.maphieukb LIKE '%$tukhoa%')";
                }
                if (!empty($trangthai)) {
                    $str .= " AND pk.trangthai = '$trangthai'";
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
                $str = "SELECT nd_bs.hoten, tt.tentrangthai, pk.*, kg.*, bn.*, c.*, bs.*
                FROM phieukhambenh AS pk
                JOIN khunggiokhambenh AS kg ON kg.makhunggiokb = pk.makhunggiokb
                JOIN trangthai AS tt ON tt.matrangthai = pk.matrangthai
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN nguoidung AS nd_bs ON nd_bs.manguoidung = bs.mabacsi
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
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
    }
?>