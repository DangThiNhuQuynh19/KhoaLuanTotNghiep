<?php
    include_once("Models/mkhunggio.php");
    class cKhungGio{
        public function getAllKhungGio() {
            $p = new mKhungGio();
            $tbl = $p->allkhunggio();
            $list=array();
            if (!$tbl) {
                return -1;
            } else {
                if ($tbl->num_rows > 0) {
                    while($r=$tbl->fetch_assoc()){
                        $list[]=$r;
                    }
                    return $list;
                } else {
                    return 0;
                }
            }
        }
        public function getKhunggio($id) {
            $p = new mKhungGio();
            $tbl = $p->selectgio($id);
            if (!$tbl) {
                return -1;
            }
            if ($tbl->num_rows > 0) {
                return $tbl->fetch_assoc(); 
            }
            return 0;
        }
        
    }
?>
