<?php
include_once("Models/mchuyengia.php");

class cChuyenGia{
    public function getAllChuyenGia(){
        $p = new mChuyenGia();
        $tbl = $p->dschuyengia();
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
    public function getAllChuyenGia1(){
        $p = new mChuyenGia();
        $tbl = $p->dschuyengia1();
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
    public function getChuyenGiaByLinhVuc($id){
        $p = new mChuyenGia();
        $tbl = $p->chuyengiatheolinhvuc($id);
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
    public function getChuyenGiaByTenAndLinhVuc($name,$id){
        $p = new mChuyenGia();
        $tbl = $p->chuyengiatheotenandlinhvuc($name,$id);
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
    public function getChuyenGiaByName($id){
        $p = new mChuyenGia();
        $tbl = $p->chuyengiatheoten($id);
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
    public function getChuyenGiaById($id){
        $p = new mChuyenGia();
        $tbl = $p->chitietchuyengia($id);
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
    public function getChuyenGiaByTenTK($tentk){
        $p = new mChuyenGia();
        $tbl = $p->xemchuyengiatheotentk($tentk);
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

    public function updateChuyenGia($machuyengia, $data) {
        $p = new mChuyenGia();
        return $p->updateChuyenGia($machuyengia, $data);
    }
    
    public function getLichLamViecChuyengia($tentk){
        $p = new mChuyenGia();
        $tbl = $p->xemlichlamchuyengia($tentk);
    
        if(!$tbl) return -1;
        if($tbl->num_rows > 0) return $tbl->fetch_all(MYSQLI_ASSOC);
        return 0;
    }
}
?>