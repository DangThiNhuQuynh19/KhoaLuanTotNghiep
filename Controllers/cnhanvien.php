<?php
include_once("Models/mnhanvien.php");

class cNhanVien{
   
    public function getNhanVienByTenTK($tentk){
        $p = new mNhanVien();
        $tbl = $p->xemnhanvientheotentk($tentk);
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
    public function getdanhsachnhanvien(){
        $p = new mNhanVien();
        $tbl = $p->getAllNhanVien();
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
    
    public function getNhanVienByName($name){
        $p = new mNhanVien();
        $tbl = $p->getNhanVienByName($name);
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
    public function getNhanVienById($id){
        $p = new mNhanVien();
        $tbl = $p->chitietnhanvien($id);
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
}
?>