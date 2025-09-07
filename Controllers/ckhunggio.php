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
    }
?>
