<?php
    include_once("Models/mnguoikham.php");
    class cNguoiKham{
        public function getAllnguoikham() {
            $p = new mNguoikham();
            $tbl = $p->allnguoikham();
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
