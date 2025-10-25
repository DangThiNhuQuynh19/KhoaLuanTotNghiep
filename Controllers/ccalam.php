<?php
include_once("Models/mcalam.php");
class cCaLam{
    public function get_calam(){
        $p = new mCaLam();
        $tbl = $p->select_CaLam();
        if (!$tbl) {
            return -1; 
        } else {
            $list=array();
            if ($tbl->num_rows > 0) {
                while($row = $tbl->fetch_assoc()){
                    $list[]=$row;
                }  
                return $list;                 
            } else {
                return 0; 
            }
        }
    }
}
?>