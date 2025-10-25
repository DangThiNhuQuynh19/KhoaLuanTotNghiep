<?php
include_once("Models/mvaitro.php");
class cVaiTro{
    public function get_vaitro(){
        $p = new mVaiTro();
        $tbl = $p->select_vaitro();
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