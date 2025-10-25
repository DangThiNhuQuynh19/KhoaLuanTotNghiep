<?php
 include_once('ketnoi.php');
 class mLinhVuc{
        public function dslinhvuc(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from linhvuc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_linhvuc_notmabenhnhan($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str="select * from linhvuc 
                where malinhvuc NOT IN( SELECT ck.malinhvuc from hosobenhan as hs 
                join chitiethoso as ct on hs.mahoso=ct.mahoso 
                join chuyengia as bs on bs.machuyengia=ct.mabacsi 
                join linhvuc as ck on ck.malinhvuc=bs.malinhvuc 
                WHERE hs.mabenhnhan='$mabenhnhan');";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_linhvuc_machuyengia($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str="select * from linhvuc as ck
                join chuyengia as bs on bs.malinhvuc=ck.malinhvuc
                WHERE bs.machuyengia='$mabacsi';";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
    }
?>