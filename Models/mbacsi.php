<?php
require_once('ketnoi.php');
 class mBacSi{
        public function dsbacsi(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi 
                        join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                        join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on bacsi.matrangthai = trangthai.matrangthai
                        order by bacsi.mabacsi asc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function dsbacsi1(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi 
                        join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                        join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on bacsi.matrangthai = trangthai.matrangthai
                        where trangthai.matrangthai = 1 order by bacsi.mabacsi asc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chitietbacsi($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi 
                        join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                        join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                        join xaphuong on nguoidung.maxaphuong = xaphuong.maxaphuong
                        join tinhthanhpho on xaphuong.matinhthanhpho = tinhthanhpho.matinhthanhpho
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on bacsi.matrangthai = trangthai.matrangthai
                        where bacsi.mabacsi='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        
        public function bacsitheoten($name){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM bacsi
                JOIN chuyenkhoa ON bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa
                JOIN nguoidung ON bacsi.mabacsi = nguoidung.manguoidung
                join taikhoan on nguoidung.email = taikhoan.tentk
                join trangthai on bacsi.matrangthai = trangthai.matrangthai
                WHERE nguoidung.hoten LIKE '%$name%'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function bacsitheokhoa($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                join taikhoan on nguoidung.email = taikhoan.tentk
                join trangthai on bacsi.matrangthai = trangthai.matrangthai
                where bacsi.machuyenkhoa='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function bacsitheotenandkhoa($name,$id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM bacsi 
                JOIN chuyenkhoa ON bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                JOIN nguoidung ON bacsi.mabacsi = nguoidung.manguoidung
                join taikhoan on nguoidung.email = taikhoan.tentk
                join trangthai on bacsi.matrangthai = trangthai.matrangthai
                WHERE nguoidung.hoten LIKE '%$name%' AND bacsi.machuyenkhoa='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        
        public function xembacsitheotentk($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi b  join nguoidung d on b.mabacsi=d.manguoidung 
                join xaphuong x on d.maxaphuong = x.maxaphuong
                join tinhthanhpho t on x.matinhthanhpho = t.matinhthanhpho
                join chuyenkhoa c on b.machuyenkhoa = c.machuyenkhoa
                where email = '$tentk'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function updateBacSi($mabacsi, $data) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if (!$con) return false;
    
            // Lấy dữ liệu cũ
            $sqlOld = "SELECT b.*, n.* 
                       FROM bacsi b 
                       JOIN nguoidung n ON b.mabacsi = n.manguoidung
                       WHERE b.mabacsi=?";
            $stmt = $con->prepare($sqlOld);
            $stmt->bind_param("s", $mabacsi);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $p->dongketnoi($con);
                return false;
            }
            $old = $result->fetch_assoc();
    
            // Dữ liệu mới, giữ dữ liệu cũ nếu không có
            $hoten        = $data['hoten'] ?? $old['hoten'];
            $ngaysinh     = $data['ngaysinh'] ?? $old['ngaysinh'];
            $gioitinh     = $data['gioitinh'] ?? $old['gioitinh'];
            $cccd         = $data['cccd'] ?? $old['cccd'];
            $cccd_matruoc = $data['cccd_matruoc'] ?? $old['cccd_matruoc'];
            $cccd_matsau  = $data['cccd_matsau'] ?? $old['cccd_matsau'];
            $dantoc       = $data['dantoc'] ?? $old['dantoc'];
            $sdt          = isset($data['sdt']) ? encryptData($data['sdt']) : $old['sdt'];
            $email        = isset($data['email']) ? encryptData($data['email']) : $old['email'];
            $emailcanhan  = $data['emailcanhan'] ?? $old['emailcanhan'];
            $sonha        = $data['sonha'] ?? $old['sonha'];
            $maxaphuong   = $data['maxaphuong'] ?? $old['maxaphuong'];
    
            $motabs       = $data['motabs'] ?? $old['motabs'];
            $gioithieubs  = $data['gioithieubs'] ?? $old['gioithieubs'];
            $ngaybatdau   = $data['ngaybatdau'] ?? $old['ngaybatdau'];
            $ngayketthuc  = $data['ngayketthuc'] ?? $old['ngayketthuc'];
            $imgbs        = $data['imgbs'] ?? $old['imgbs'];
            $giakham      = $data['giakham'] ?? $old['giakham'];
            $machuyenkhoa = $data['machuyenkhoa'] ?? $old['machuyenkhoa'];
            $capbac       = $data['capbac'] ?? $old['capbac'];
            $matrangthai  = $data['trangthai'] ?? $old['matrangthai'];
    
            // Update bảng bacsi
            $sqlBacSi = "UPDATE bacsi SET
                            gioithieubs=?, motabs=?, ngaybatdau=?, ngayketthuc=?,
                            imgbs=?, giakham=?, machuyenkhoa=?, capbac=?, matrangthai=?
                         WHERE mabacsi=?";
            $stmt1 = $con->prepare($sqlBacSi);
            $stmt1->bind_param("sssssiisss", $gioithieubs, $motabs, $ngaybatdau, $ngayketthuc, $imgbs, $giakham, $machuyenkhoa, $capbac, $matrangthai, $mabacsi);
            $ok1 = $stmt1->execute();
    
            // Update bảng nguoidung
            $sqlNguoiDung = "UPDATE nguoidung SET
                                hoten=?, ngaysinh=?, gioitinh=?, cccd=?, cccd_matruoc=?, cccd_matsau=?,
                                dantoc=?, sdt=?, emailcanhan=?, sonha=?, maxaphuong=?, email=?
                             WHERE manguoidung=?";
            $stmt2 = $con->prepare($sqlNguoiDung);
            $stmt2->bind_param("sssssssssssss", $hoten, $ngaysinh, $gioitinh, $cccd, $cccd_matruoc, $cccd_matsau, $dantoc, $sdt, $emailcanhan, $sonha, $maxaphuong, $email, $mabacsi);
            $ok2 = $stmt2->execute();
    
            $p->dongketnoi($con);
            return $ok1 && $ok2;
        }
        
    }
?>