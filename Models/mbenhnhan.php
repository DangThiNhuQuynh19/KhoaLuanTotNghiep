<?php
 include_once('ketnoi.php');
 include_once('Assets/config.php');
 class mBenhNhan{
        private $conn;

        public function __construct() {
            $p = new clsketnoi();
            $this->conn = $p->moketnoi();
        }
        public function getBenhNhanByTenTK($email) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan as b join nguoidung as n on b.mabenhnhan=n.manguoidung join xaphuong as p on p.maxaphuong = n.maxaphuong join tinhthanhpho as t on t.matinhthanhpho = p.matinhthanhpho where email = '$email'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_benhnhan_manguoigiamho($manguoigiamho){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan as b 
                        join trangthai tt on tt.matrangthai =b.matrangthai
                        join nguoidung as n on b.mabenhnhan=n.manguoidung 
                        join xaphuong as p on p.maxaphuong = n.maxaphuong 
                        join tinhthanhpho as t on t.matinhthanhpho = p.matinhthanhpho 
                        where mabenhnhan = '$manguoigiamho' OR manguoigiamho = '$manguoigiamho'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getBenhNhanChinhByTenTK($tentk) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan where tentk = '$tentk' and quanhe = 'bản thân'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getBenhNhanByID($id) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan b
                        join nguoidung n on b.mabenhnhan=n.manguoidung 
                        join xaphuong as p on p.maxaphuong = n.maxaphuong join tinhthanhpho as t on t.matinhthanhpho = p.matinhthanhpho
                        where b.mabenhnhan = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getBenhNhan() {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan join nguoidung on nguoidung.manguoidung = benhnhan.mabenhnhan";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function capnhatbenhnhan($mabenhnhan, $hotenbenhnhan, $ngaysinh, $gioitinh, $nghenghiep, $cccdbenhnhan,
                                $dantoc, $email, $sdtbenhnhan, $tinh, $quan, $xa, $sonha, $quanhe,
                                $tiensubenhtatcuagiadinh, $tiensubenhtatcuabenhnhan, $nhommau) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if ($con) {
                // Sử dụng dấu backtick để bao quanh tên cột
                $str = "UPDATE benhnhan 
                        SET hotenbenhnhan='$hotenbenhnhan', ngaysinh='$ngaysinh', gioitinh='$gioitinh', nghenghiep='$nghenghiep', 
                            cccdbenhnhan='$cccdbenhnhan', dantoc='$dantoc', email='$email', sdtbenhnhan='$sdtbenhnhan',
                            `tinh/thanhpho`='$tinh', `quan/huyen`='$quan', `xa/phuong`='$xa', sonha='$sonha', quanhe='$quanhe',
                            tiensubenhtatcuagiadinh='$tiensubenhtatcuagiadinh', tiensubenhtatcuabenhnhan='$tiensubenhtatcuabenhnhan', 
                            nhommau='$nhommau' 
                        WHERE mabenhnhan='$mabenhnhan'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            } else {
                return false;
            }
        }

        public function select_benhnhan_mabacsi($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT DISTINCT b.mabenhnhan, nd.ngaysinh, nd.gioitinh, nd.hoten, nd.sdt, nd.cccd, nd.email
                FROM benhnhan AS b
                JOIN phieukhambenh AS p ON b.mabenhnhan = p.mabenhnhan
                JOIN nguoidung nd ON nd.manguoidung = b.mabenhnhan
                WHERE p.mabacsi = '$mabacsi'
                ORDER BY b.mabenhnhan DESC
                ";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_benhnhan_machuyengia($machuyengia){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT DISTINCT b.mabenhnhan, nd.ngaysinh, nd.gioitinh, nd.hoten, nd.sdt, nd.cccd, nd.email
                FROM benhnhan AS b
                JOIN phieukhambenh AS p ON b.mabenhnhan = p.mabenhnhan
                JOIN nguoidung nd ON nd.manguoidung = b.mabenhnhan
                WHERE p.mabacsi = '$machuyengia'
                ORDER BY b.mabenhnhan DESC";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_benhnhan_id($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan b 
                        join nguoidung nd on nd.manguoidung = b.mabenhnhan
                        join xaphuong x on x.maxaphuong = nd.maxaphuong
                        join tinhthanhpho t on t.matinhthanhpho = x.matinhthanhpho
                        where mabenhnhan = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_benhnhan_homnay($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT nd_bs.hoten as hotenbacsi, tt.tentrangthai, pk.*, kg.*, bn.*, ck.tenchuyenkhoa, nd_bn.*
                FROM phieukhambenh AS pk
                JOIN trangthai AS tt ON tt.matrangthai = pk.matrangthai
                JOIN khunggiokhambenh AS kg ON kg.makhunggiokb = pk.makhunggiokb
                JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi
                JOIN nguoidung AS nd_bs ON nd_bs.manguoidung = bs.mabacsi
                JOIN chuyenkhoa ck ON ck.machuyenkhoa=bs.machuyenkhoa
                JOIN benhnhan AS bn ON bn.mabenhnhan = pk.mabenhnhan
                JOIN nguoidung AS nd_bn ON nd_bn.manguoidung = bn.mabenhnhan
                WHERE bs.mabacsi = '$mabacsi' AND  pk.ngaykham = CURDATE() GROUP BY bn.mabenhnhan";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function insertbenhnhan($mabenhnhan, $email, $hoten, $ngaysinh, $sdt,$dantoc, $cccd, $cccd_truoc_name, $birth_cert_name, $cccd_sau_name, $gioitinh, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $sonha, $xa, $tinh, $manguoithan, $quanhe) {
            // --- 1. Tính tuổi bệnh nhân ---
            $today = new DateTime();
            $dob = new DateTime($ngaysinh);
            $age = $today->diff($dob)->y;
        
            if (!($age < 18 || $age > 60)) {
                return "Chỉ được tạo hồ sơ cho trẻ em dưới 18 tuổi hoặc người già trên 60 tuổi.";
            }
        
            // --- 2. Kiểm tra số lượng hồ sơ đã có của người giám hộ ---
            $stmtCheck = $this->conn->prepare("SELECT COUNT(*) as total FROM benhnhan WHERE manguoigiamho = ?");
            $stmtCheck->bind_param("s", $manguoithan);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result()->fetch_assoc();
        
            if ($result['total'] >= 4) {
                return "Một người giám hộ chỉ được tạo tối đa 4 hồ sơ.";
            }
        
            // --- 3. Thêm vào bảng nguoidung ---
            $stmtInsertND = $this->conn->prepare("INSERT INTO nguoidung (manguoidung, hoten, ngaysinh, gioitinh, cccd, cccd_matruoc, cccd_matsau, dantoc, sdt, sonha, maxaphuong, emailcanhan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsertND->bind_param("ssssssssssss", $mabenhnhan, $hoten, $ngaysinh, $gioitinh, $cccd, $cccd_truoc_name, $cccd_sau_name, $dantoc, $sdt, $sonha, $xa, $email);
        
            if ($stmtInsertND->execute()) {
                // --- 4. Thêm vào bảng benhnhan ---
                $stmtInsertBN = $this->conn->prepare("INSERT INTO benhnhan(mabenhnhan, nghenghiep, tiensubenhtatcuagiadinh, tiensubenhtatcuabenhnhan, manguoigiamho, moiquanhevoinguoithan) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsertBN->bind_param("ssssss", $mabenhnhan, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $manguoithan, $quanhe);
        
                if (!$stmtInsertBN->execute()) {
                    return "Lỗi khi thêm bệnh nhân: " . $stmtInsertBN->error;
                }
                return true;
            } else {
                return "Lỗi khi thêm người dùng: " . $stmtInsertND->error;
            }
        }
        public function deletebenhnhan($id) {
            $p = new clsketnoi();
            $con = $p->moketnoi();
            $truyvan = "update benhnhan set matrangthai=8 where mabenhnhan='$id'";
            $tbl = mysqli_query($con, $truyvan);
            $p->dongketnoi($con);
            return $tbl;
        }


        public function timkiem_benhnhan_tukhoa($tukhoa, $mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if ($con) {
                $sql = "SELECT * 
                        FROM benhnhan AS b  
                        JOIN nguoidung nd ON nd.manguoidung =b.mabenhnhan
                        JOIN phieukhambenh AS p ON b.mabenhnhan = p.mabenhnhan
                        WHERE p.mabacsi = '$mabacsi'";
        
                if (!empty($tukhoa)) {
                    $sql .= " AND (b.mabenhnhan = '$tukhoa' 
                                OR nd.hoten LIKE '%$tukhoa%') ";
                }
        
                $sql .= " GROUP BY b.mabenhnhan 
                          ORDER BY b.mabenhnhan DESC";
        
                $result = $con->query($sql);
                $p->dongketnoi($con);
                return $result;
            } else {
                return false;
            }
        }
    }
    
?>