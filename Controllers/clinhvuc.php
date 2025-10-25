<?php
include_once("Models/mlinhvuc.php");

class cLinhVuc{
    public function getAllLinhVuc(){
        $p = new mLinhVuc();
        $tbl = $p->dslinhvuc();
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl;
            }else{
                return 0;
            }
        }
    }
    public function get_linhvuc_notmabenhnhan($mabenhnhan){
        $p = new mLinhVuc();
        $tbl = $p->select_linhvuc_notmabenhnhan($mabenhnhan);
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl;
            }else{
                return 0;
            }
        }
    }
    public function get_linhvuc_machuyengia($mabacsi){
        $p = new mLinhVuc();
        $tbl = $p->select_linhvuc_machuyengia($mabacsi);
        if(!$tbl){
            return -1;
        }else{
            if($tbl->num_rows > 0){
                return $tbl->fetch_assoc();
            }else{
                return 0;
            }
        }
    }
}
?>