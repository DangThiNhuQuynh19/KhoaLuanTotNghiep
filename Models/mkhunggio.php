<?php
include_once('ketnoi.php');
class mKhungGio{
    public function allkhunggio(){
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if($con){
            $str = "SELECT kgkb.giobatdau, kgkb.gioketthuc FROM khunggiokhambenh kgkb 
            JOIN calamviec clv on kgkb.macalamviec = clv.macalamviec;";
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }
}