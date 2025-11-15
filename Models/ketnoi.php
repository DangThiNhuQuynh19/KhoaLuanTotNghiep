<?php
class clsketnoi{
    public function moKetNoi(){
        $con = mysqli_connect("160.30.136.31", "root", "kltntrangquynh2025@", "hanhphuc");
        mysqli_set_charset($con,'utf8');
        return $con;
    }
    public function dongKetNoi($con){
        mysqli_close($con);
    }
}
?>