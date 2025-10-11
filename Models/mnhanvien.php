<?php
require_once('ketnoi.php');
 class mNhanVien{
        public function xemnhanvientheotentk($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from nhanvien b  join nguoidung d on b.manhanvien=d.manguoidung join xaphuong as p on p.maxaphuong = d.maxaphuong join tinhthanhpho as t on t.matinhthanhpho = p.matinhthanhpho where email = '$tentk'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getAllNhanVien() {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM nhanvien 
                        JOIN nguoidung ON nhanvien.manhanvien = nguoidung.manguoidung
                        JOIN xaphuong ON xaphuong.maxaphuong = nguoidung.maxaphuong
                        JOIN tinhthanhpho ON tinhthanhpho.matinhthanhpho = xaphuong.matinhthanhpho
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                        ORDER BY nhanvien.manhanvien ASC";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        
        public function getNhanVienByName($name) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM nhanvien 
                        JOIN nguoidung ON nhanvien.manhanvien = nguoidung.manguoidung
                        JOIN xaphuong ON xaphuong.maxaphuong = nguoidung.maxaphuong
                        JOIN tinhthanhpho ON tinhthanhpho.matinhthanhpho = xaphuong.matinhthanhpho
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                        WHERE nguoidung.hoten LIKE '%$name%'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chitietnhanvien($id) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM nhanvien 
                        JOIN nguoidung ON nhanvien.manhanvien = nguoidung.manguoidung
                        JOIN xaphuong ON xaphuong.maxaphuong = nguoidung.maxaphuong
                        JOIN tinhthanhpho ON tinhthanhpho.matinhthanhpho = xaphuong.matinhthanhpho
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                        WHERE nhanvien.manhanvien='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        
    }
?>