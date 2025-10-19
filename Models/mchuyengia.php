<?php
require_once('ketnoi.php');
 class mChuyenGia{
        public function dschuyengia1(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from chuyengia 
                        join linhvuc on chuyengia.malinhvuc = linhvuc.malinhvuc 
                        join nguoidung on chuyengia.machuyengia = nguoidung.manguoidung
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on chuyengia.matrangthai = trangthai.matrangthai
                        order by chuyengia.machuyengia desc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function dschuyengia(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from chuyengia 
                        join linhvuc on chuyengia.malinhvuc = linhvuc.malinhvuc 
                        join nguoidung on chuyengia.machuyengia = nguoidung.manguoidung
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on chuyengia.matrangthai = trangthai.matrangthai
                        where taikhoan.matrangthai=1 order by chuyengia.machuyengia desc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chuyengiatheolinhvuc($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from chuyengia join linhvuc on chuyengia.malinhvuc = linhvuc.malinhvuc 
                join nguoidung on chuyengia.machuyengia = nguoidung.manguoidung
                join taikhoan on nguoidung.email = taikhoan.tentk
                join trangthai on chuyengia.matrangthai = trangthai.matrangthai
                where chuyengia.malinhvuc='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chuyengiatheotenandlinhvuc($name,$id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM chuyengia 
                JOIN linhvuc ON chuyengia.malinhvuc = linhvuc.malinhvuc
                JOIN nguoidung ON chuyengia.machuyengia = nguoidung.manguoidung
                join taikhoan on nguoidung.email = taikhoan.tentk
                join trangthai on chuyengia.matrangthai = trangthai.matrangthai
                WHERE nguoidung.hoten LIKE '%$name%' AND chuyengia.malinhvuc='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chuyengiatheoten($name){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM chuyengia
                JOIN linhvuc ON chuyengia.malinhvuc = linhvuc.malinhvuc
                JOIN nguoidung ON chuyengia.machuyengia = nguoidung.manguoidung
                join taikhoan on nguoidung.email = taikhoan.tentk
                join trangthai on chuyengia.matrangthai = trangthai.matrangthai
                WHERE nguoidung.hoten LIKE '%$name%'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chitietchuyengia($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from chuyengia 
                        join linhvuc on chuyengia.malinhvuc = linhvuc.malinhvuc 
                        join nguoidung on chuyengia.machuyengia = nguoidung.manguoidung
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join xaphuong on nguoidung.maxaphuong = xaphuong.maxaphuong
                        join tinhthanhpho on xaphuong.matinhthanhpho = tinhthanhpho.matinhthanhpho
                        join trangthai on chuyengia.matrangthai = trangthai.matrangthai
                        where chuyengia.machuyengia='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function xemchuyengiatheotentk($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from chuyengia b  join nguoidung d on b.machuyengia=d.manguoidung where email = '$tentk'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function updateChuyenGia($machuyengia, $data) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if (!$con) return false;
        
            // Lấy dữ liệu cũ
            $sqlOld = "SELECT b.*, n.* 
                       FROM chuyengia b 
                       JOIN nguoidung n ON b.machuyengia = n.manguoidung
                       WHERE b.machuyengia=?";
            $stmt = $con->prepare($sqlOld);
            $stmt->bind_param("s", $machuyengia);
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
        
            // ❌ Không cho phép update email đăng nhập
            $email        = $old['email'];
        
            $emailcanhan  = $data['emailcanhan'] ?? $old['emailcanhan'];
            $sonha        = $data['sonha'] ?? $old['sonha'];
            $maxaphuong   = $data['tenxaphuong'] ?? $old['maxaphuong'];
            $motabs       = $data['motacg'] ?? $old['motacg'];
            $gioithieubs  = $data['gioithieucg'] ?? $old['gioithieucg'];
            $ngaybatdau   = $data['ngaybatdau'] ?? $old['ngaybatdau'];
            $ngayketthuc  = $data['ngayketthuc'] ?? $old['ngayketthuc'];
            $imgbs        = $data['imgcg'] ?? $old['imgcg'];
            $giakham      = $data['giatuvan'] ?? $old['giatuvan'];
            $machuyenkhoa = $data['malinhvuc'] ?? $old['malinhvuc'];
            $capbac       = $data['capbac'] ?? $old['capbac'];
        
            // Update bảng bacsi
            $sqlBacSi = "UPDATE chuyengia SET
                            gioithieucg=?, motacg=?, ngaybatdau=?, ngayketthuc=?,
                            imgcg=?, giatuvan=?, malinhvuc=?, capbac=?
                         WHERE machuyengia=?";
            $stmt1 = $con->prepare($sqlBacSi);
            $stmt1->bind_param(
                "sssssiiss",
                $gioithieubs,
                $motabs,
                $ngaybatdau,
                $ngayketthuc,
                $imgbs,
                $giakham,
                $machuyenkhoa,
                $capbac,
                $machuyengia
            );
            $ok1 = $stmt1->execute();
        
            // Update bảng nguoidung (❌ bỏ email ra khỏi phần update)
            $sqlNguoiDung = "UPDATE nguoidung SET
                                hoten=?, ngaysinh=?, gioitinh=?, cccd=?, cccd_matruoc=?, cccd_matsau=?,
                                dantoc=?, sdt=?, emailcanhan=?, sonha=?, maxaphuong=?
                             WHERE manguoidung=?";
            $stmt2 = $con->prepare($sqlNguoiDung);
            $stmt2->bind_param(
                "ssssssssssss",
                $hoten,
                $ngaysinh,
                $gioitinh,
                $cccd,
                $cccd_matruoc,
                $cccd_matsau,
                $dantoc,
                $sdt,
                $emailcanhan,
                $sonha,
                $maxaphuong,
                $machuyengia
            );
            $ok2 = $stmt2->execute();
        
            $p->dongketnoi($con);
            return $ok1 && $ok2;
        }
    }
?>