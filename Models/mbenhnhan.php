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
                        where mabenhnhan = '$manguoigiamho' OR manguoigiamho = '$manguoigiamho'
                        and b.matrangthai=1";
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

        // Hàm giữ dữ liệu cũ nếu giá trị mới rỗng
    private function keepOld($newValue, $oldValue) {
        return (isset($newValue) && $newValue !== '' && $newValue !== null) ? $newValue : $oldValue;
    }

    // Hàm cập nhật bệnh nhân
    public function capnhatbenhnhan(
        $mabenhnhan, $hoten, $ngaysinh, $gioitinh, $cccd, $dantoc, $sdt, $emailcanhan, $sonha, $maxaphuong,
        $nghenghiep, $tiensubenhtatcuagiadinh, $tiensubenhtatcuabenhnhan,
        $giaykhaisinh = null, $cccd_truoc = null, $cccd_sau = null, $quanhe = null,
        $manguoigiamho = null, $matrangthai = null
    ) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');

        if (!$con) return false;

        $con->begin_transaction();
        try {
            // Lấy dữ liệu cũ từ bảng nguoidung (mã bệnh nhân là string)
            $stmtOld = $con->prepare("SELECT * FROM nguoidung WHERE manguoidung=?");
            if (!$stmtOld) throw new Exception("Prepare lỗi SELECT nguoidung");
            $stmtOld->bind_param("s", $mabenhnhan);
            $stmtOld->execute();
            $oldData = $stmtOld->get_result()->fetch_assoc();
            $stmtOld->close();
            if (!$oldData) throw new Exception("Không tìm thấy người dùng");

            // Lấy dữ liệu cũ từ bảng benhnhan
            $stmtOldBn = $con->prepare("SELECT * FROM benhnhan WHERE mabenhnhan=?");
            if (!$stmtOldBn) throw new Exception("Prepare lỗi SELECT benhnhan");
            $stmtOldBn->bind_param("s", $mabenhnhan);
            $stmtOldBn->execute();
            $oldBnData = $stmtOldBn->get_result()->fetch_assoc();
            $stmtOldBn->close();
            if (!$oldBnData) throw new Exception("Không tìm thấy bệnh nhân");

            // Giữ dữ liệu cũ nếu rỗng
            $hoten = $this->keepOld($hoten, $oldData['hoten']);
            $ngaysinh = $this->keepOld($ngaysinh, $oldData['ngaysinh']);
            $gioitinh = $this->keepOld($gioitinh, $oldData['gioitinh']);
            $cccd = $this->keepOld($cccd, $oldData['cccd']);
            $dantoc = $this->keepOld($dantoc, $oldData['dantoc']);
            $sdt = $this->keepOld($sdt, $oldData['sdt']);
            $emailcanhan = $this->keepOld($emailcanhan, $oldData['emailcanhan']);
            $sonha = $this->keepOld($sonha, $oldData['sonha']);
            $maxaphuong = $this->keepOld($maxaphuong, $oldData['maxaphuong']);
            $cccd_truoc = $this->keepOld($cccd_truoc, $oldData['cccd_matruoc']);
            $cccd_sau = $this->keepOld($cccd_sau, $oldData['cccd_matsau']);

            $nghenghiep = $this->keepOld($nghenghiep, $oldBnData['nghenghiep']);
            $tiensubenhtatcuagiadinh = $this->keepOld($tiensubenhtatcuagiadinh, $oldBnData['tiensubenhtatcuagiadinh']);
            $tiensubenhtatcuabenhnhan = $this->keepOld($tiensubenhtatcuabenhnhan, $oldBnData['tiensubenhtatcuabenhnhan']);
            $giaykhaisinh = $this->keepOld($giaykhaisinh, $oldBnData['giaykhaisinh']);
            $quanhe = $this->keepOld($quanhe, $oldBnData['moiquanhevoinguoithan']);
            $manguoigiamho = $this->keepOld($manguoigiamho, $oldBnData['manguoigiamho']);
            $matrangthai = $this->keepOld($matrangthai, $oldBnData['matrangthai']);

            // Cập nhật bảng nguoidung
            $sql1 = "UPDATE nguoidung 
                     SET hoten=?, ngaysinh=?, gioitinh=?, cccd=?, dantoc=?, sdt=?, emailcanhan=?, sonha=?, maxaphuong=?, cccd_matruoc=?, cccd_matsau=?
                     WHERE manguoidung=?";
            $stmt1 = $con->prepare($sql1);
            if (!$stmt1) throw new Exception("Prepare lỗi UPDATE nguoidung");
            // tất cả là string, mabenhnhan là string -> "s"
            $stmt1->bind_param(
                "ssssssssssss",
                $hoten, $ngaysinh, $gioitinh, $cccd, $dantoc, $sdt, $emailcanhan, $sonha, $maxaphuong, $cccd_truoc, $cccd_sau, $mabenhnhan
            );
            if (!$stmt1->execute()) {
                $stmt1->close();
                throw new Exception("Execute lỗi UPDATE nguoidung: " . $stmt1->error);
            }
            $stmt1->close();

            // Cập nhật bảng benhnhan
            $sql2 = "UPDATE benhnhan 
            SET nghenghiep=?, 
                tiensubenhtatcuagiadinh=?, 
                tiensubenhtatcuabenhnhan=?, 
                giaykhaisinh=?, 
                moiquanhevoinguoithan=?, 
                manguoigiamho=?, 
                matrangthai=? 
            WHERE mabenhnhan=?";
            $stmt2 = $con->prepare($sql2);
            if (!$stmt2) throw new Exception("Prepare lỗi UPDATE benhnhan");
            $stmt2->bind_param(
                "ssssssss",
                $nghenghiep, $tiensubenhtatcuagiadinh, $tiensubenhtatcuabenhnhan, $giaykhaisinh, $quanhe, $manguoigiamho, $matrangthai, $mabenhnhan
            );
            if (!$stmt2->execute()) {
                $stmt2->close();
                throw new Exception("Execute lỗi UPDATE benhnhan: " . $stmt2->error);
            }
            $stmt2->close();

            $con->commit();
            $p->dongketnoi($con);
            return true;

        } catch (Exception $e) {
            $con->rollback();
            $p->dongketnoi($con);
            // tùy bạn có log $e->getMessage() không
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

        public function insertbenhnhan($mabenhnhan, $email, $hoten, $ngaysinh, $sdt, $dantoc, $cccd, $cccd_truoc_name, $birth_cert_name, $cccd_sau_name, $gioitinh, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $sonha, $xa, $tinh, $manguoithan, $quanhe) {
            $today = new DateTime();
            $dob = new DateTime($ngaysinh);
            $age = $today->diff($dob)->y;
        
            if (!($age < 18 || $age > 60)) {
                return "Chỉ được tạo hồ sơ cho trẻ em dưới 18 tuổi hoặc người già trên 60 tuổi.";
            }
        
            // Kiểm tra số lượng hồ sơ
            $stmtCheck = $this->conn->prepare("SELECT COUNT(*) as total FROM benhnhan WHERE manguoigiamho = ? and matrangthai=1");
            $stmtCheck->bind_param("s", $manguoithan);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result()->fetch_assoc();
        
            if ($result['total'] >= 4) {
                return "Một người giám hộ chỉ được tạo tối đa 4 hồ sơ.";
            }
        
            // Thêm vào bảng nguoidung
            $stmtInsertND = $this->conn->prepare("INSERT INTO nguoidung (manguoidung, hoten, ngaysinh, gioitinh, cccd, cccd_matruoc, cccd_matsau, dantoc, sdt, sonha, maxaphuong, emailcanhan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsertND->bind_param("ssssssssssss", $mabenhnhan, $hoten, $ngaysinh, $gioitinh, $cccd, $cccd_truoc_name, $cccd_sau_name, $dantoc, $sdt, $sonha, $xa, $email);
        
            if ($stmtInsertND->execute()) {
                // Thêm vào bảng benhnhan
                $stmtInsertBN = $this->conn->prepare("INSERT INTO benhnhan(mabenhnhan, nghenghiep, tiensubenhtatcuagiadinh, tiensubenhtatcuabenhnhan, manguoigiamho, moiquanhevoinguoithan,matrangthai, giaykhaisinh) VALUES (?, ?, ?, ?, ?, ?,1, ?)");
                $stmtInsertBN->bind_param("sssssss", $mabenhnhan, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $manguoithan, $quanhe, $birth_cert_name);
        
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
            $truyvan = "update benhnhan set matrangthai= where mabenhnhan='$id'";
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