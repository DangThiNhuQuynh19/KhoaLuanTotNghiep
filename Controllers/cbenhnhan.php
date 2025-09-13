<?php
include_once("Models/mbenhnhan.php");
include_once("Assets/config.php");
class cBenhNhan{
    public function getbenhnhanbytk($email){
        $p = new mBenhNhan();
        $tbl = $p->getBenhNhanByTenTK($email);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $row = $tbl->fetch_assoc();  
                return $row;                 
            } else {
                return 0; 
            }
        }
    }
    public function getAllBenhNhanByTK($manguoigiamho) {
        $m = new mBenhNhan();
        $tbl = $m->select_benhnhan_manguoigiamho($manguoigiamho);
        $ds = [];
        if ($tbl && $tbl->num_rows > 0) {
            while ($row = $tbl->fetch_assoc()) {
                $ds[] = $row;
            }
        }
        return $ds;
    }
    public function getBenhNhanChinhByTK($tentk){
        $p = new mBenhNhan();
        $tbl = $p->getBenhNhanChinhByTenTK($tentk);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $row = $tbl->fetch_assoc();  
                return $row;                 
            } else {
                return 0; 
            }
        }
    }
    public function getbenhnhanbyid($id){
        $p = new mBenhNhan();
        $tbl = $p->getBenhNhanByID($id);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $row = $tbl->fetch_assoc();  
                return $row;                 
            } else {
                return 0; 
            }
        }
    }
    public function updateBenhNhan($mabenhnhan,$hotenbenhnhan,$ngaysinh,$gioitinh,$nghenghiep,$cccdbenhnhan,
                                    $dantoc,$email,$sdtbenhnhan,$tinh,$quan,$xa,$sonha,$quanhe,
                                    $tiensubenhtatcuagiadinh,$tiensubenhtatcuabenhnhan,$nhommau) {
        $p = new mBenhNhan();
        $kq = $p->capnhatbenhnhan($mabenhnhan,$hotenbenhnhan,$ngaysinh,$gioitinh,$nghenghiep,$cccdbenhnhan,
                                    $dantoc,$email,$sdtbenhnhan,$tinh,$quan,$xa,$sonha,$quanhe,
                                    $tiensubenhtatcuagiadinh,$tiensubenhtatcuabenhnhan,$nhommau);
        if ($kq) { 
            return true;
        } else {
            return false;
        }
    }


    public function get_benhnhan_mabacsi($mabacsi){
        $p = new mBenhNhan();
        $tbl = $p->select_benhnhan_mabacsi($mabacsi);
        $list = array();
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) { 
                while($r=$tbl->fetch_assoc()){
                    $r['sdt'] = decryptData($r['sdt']);
                    $r['cccd'] = decryptData($r['cccd']);
                    $r['email'] = decryptData($r['email']);
                    $list[] = $r;
                }
                return $list ;                 
            } else {
                return 0; 
            }
        }
    }

    public function get_benhnhan_id($id){
        $p = new mBenhNhan();
        $tbl = $p->select_benhnhan_id($id);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                return $tbl->fetch_assoc();                  
            } else {
                return 0; 
            }
        }
    }

    public function get_benhnhan_tukhoa($tukhoa, $mabacsi){
        $p = new mBenhNhan();
        $tbl = $p->timkiem_benhnhan_tukhoa($tukhoa, $mabacsi);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $ds = array();
                while ($row = $tbl->fetch_assoc()) {

                    $r['sdt'] = decryptData($r['sdt']);
                    $r['cccd'] = decryptData($r['cccd']);
                    $r['email'] = decryptData($r['email']);
                    $ds[] = $r;
                }
                return $ds;
            } else {
                return 0; 
            }
        }
    }
    public function insertbenhnhan($mabenhnhan, $email, $hoten, $ngaysinh, $sdt,$dantoc, $cccd, $cccd_truoc_name, $birth_cert_name, $cccd_sau_name, $gioitinh, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $sonha, $xa, $tinh, $manguoithan, $quanhe){
        $p = new mBenhNhan();
        $kq = $p -> insertbenhnhan($mabenhnhan, $email, $hoten, $ngaysinh, $sdt,$dantoc, $cccd, $cccd_truoc_name, $birth_cert_name, $cccd_sau_name, $gioitinh, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $sonha, $xa, $tinh, $manguoithan, $quanhe);
        if($kq){
            return $kq;
        } else {
            return false;
        }
    }
    public function deletebenhnhan($id){
        $p = new mBenhNhan();
        $kq = $p -> deletebenhnhan($id);
        if($kq){
            return $kq;
        }else{
            return false;
        }
    }

    public function get_benhnhan_homnay($mabacsi){
        $p = new mBenhNhan();
        $kq = $p -> select_benhnhan_homnay($mabacsi);
        $list = array();
        if (!$kq) {
            return -1; 
        } else {
            if ($kq->num_rows > 0) { 
                while($r=$kq->fetch_assoc()){
                    $r['sdt'] = decryptData($r['sdt']);
                    $r['cccd'] = decryptData($r['cccd']);
                    $r['email'] = decryptData($r['email']);
                    $list[] = $r;
                }
                return $list ;                 
            } else {
                return 0; 
            }
        }
    }

}
?>