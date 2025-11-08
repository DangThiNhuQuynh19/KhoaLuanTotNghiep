<?php
include_once("Models/mbacsi.php");

class cBacSi{
    public function getAllBacSi(){
        $p = new mBacSi();
        $tbl = $p->dsbacsi();
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
    public function getAllBacSi1(){
        $p = new mBacSi();
        $tbl = $p->dsbacsi1();
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
    public function getBacSiById($id){
        $p = new mBacSi();
        $tbl = $p->chitietbacsi($id);
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
    public function getBacSiByKhoa($id){
        $p = new mBacSi();
        $tbl = $p->bacsitheokhoa($id);
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
    public function getBacSiByName($id){
        $p = new mBacSi();
        $tbl = $p->bacsitheoten($id);
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
    public function getBacSiByTenAndKhoa($name,$id){
        $p = new mBacSi();
        $tbl = $p->bacsitheotenandkhoa($name,$id);
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

    public function getBacSiByTenTK($tentk){
        $p = new mBacSi();
        $tbl = $p->xembacsitheotentk($tentk);
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
    public function updateBacSi($mabacsi, $data) {
        $p = new mBacSi();
        return $p->updateBacSi($mabacsi, $data);
    }
    public function getLichLamViecBacSi($tentk){
        $p = new mBacSi();
        $tbl = $p->xemlichlambacsi($tentk);
    
        if(!$tbl) return -1;
        if($tbl->num_rows > 0) return $tbl->fetch_all(MYSQLI_ASSOC);
        return 0;
    }
    
    
}
?>