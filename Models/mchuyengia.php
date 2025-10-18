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
    }
?>