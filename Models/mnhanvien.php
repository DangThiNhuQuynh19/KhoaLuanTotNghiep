<?php
require_once('ketnoi.php');
 class mNhanVien{
    
        
        public function xemnhanvientheotentk($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from nhanvien b  join nguoidung d on b.manhanvien=d.manguoidung join xaphuong as p on p.maxaphuong = d.maxaphuong join tinhthanhpho as t on t.matinhthanhpho = p.matinhthanhpho where email = '$tentk'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
    }
?>