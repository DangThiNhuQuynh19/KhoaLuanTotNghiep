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
    
}
?>