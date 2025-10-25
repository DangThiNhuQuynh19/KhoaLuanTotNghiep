<?php
 include_once('ketnoi.php');
 include_once('Assets/config.php');
 class mHoSoBenhAnDienTu{
        public function gethosotheotentk($tentk){
            $tentk= decryptData($tentk);
            echo $tentk;
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM hosobenhan hs 
                join benhnhan bn on hs.mabenhnhan=bn.mabenhnhan 
                join nguoidung nd on nd.manguoidung = bn.mabenhnhan
                join xaphuong xp on xp.maxaphuong = nd.maxaphuong
                join tinhthanhpho tp on tp.matinhthanhpho = xp.matinhthanhpho
                join chitiethoso ct on hs.mahoso = ct.mahoso 
                join bacsi bs on ct.mabacsi = bs.mabacsi 
                join chuyenkhoa ck on bs.machuyenkhoa = ck.machuyenkhoa 
                where nd.email= '$tentk'
                order by hs.mahoso";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getchitiethosotheotentk($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM hosobenhan hs
                  join chitiethoso ct on hs.mahoso = ct.mahoso  
                join benhnhan bn on hs.mabenhnhan=bn.mabenhnhan 
                join bacsi bs on ct.mabacsi = bs.mabacsi 
                join chuyenkhoa ck on bs.machuyenkhoa = ck.machuyenkhoa  
                where ct.machitiethoso = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getchitiethosotheohoso($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM hosobenhan hs 
                join chitiethoso ct on hs.mahoso = ct.mahoso 
                where ct.mahoso = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getdonthuoctheohoso($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM  donthuoc dt 
                join chitiethoso ct on ct.madonthuoc = dt.madonthuoc
                join chitietdonthuoc ctdt on dt.madonthuoc = ctdt.madonthuoc
                join thuoc t on t.mathuoc = ctdt.mathuoc
                where ct.machitiethoso = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function getxetnghiemtheohoso($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM  lichxetnghiem xn 
                join chitiethoso ct on ct.mahoso = xn.mahoso
                join loaixetnghiem lxn on xn.maloaixetnghiem = lxn.maloaixetnghiem
                join khunggioxetnghiem kg on xn.makhunggio=kg.makhunggioxetnghiem
                join ketquaxetnghiem kq on xn.malichxetnghiem = kq.malichxetnghiem
                where ct.machitiethoso = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_hsba_mabenhnhan($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan hs 
                join benhnhan bn on hs.mabenhnhan=bn.mabenhnhan 
                join nguoidung nd_bn on nd_bn.manguoidung = bn.mabenhnhan
                join chitiethoso ct on hs.mahoso = ct.mahoso 
                join bacsi bs on ct.mabacsi = bs.mabacsi 
                join nguoidung nd_bs on nd_bs.manguoidung = bs.mabacsi
                join chuyenkhoa ck on bs.machuyenkhoa = ck.machuyenkhoa 
                where hs.mabenhnhan= '$mabenhnhan'
                group by ct.mahoso";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_hsba_mabenhnhan1($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan hs 
                join benhnhan bn on hs.mabenhnhan=bn.mabenhnhan 
                join nguoidung nd_bn on nd_bn.manguoidung = bn.mabenhnhan
                join chitiethoso ct on hs.mahoso = ct.mahoso 
                join chuyengia bs on ct.mabacsi = bs.machuyengia
                join nguoidung nd_bs on nd_bs.manguoidung = bs.machuyengia
                join linhvuc ck on bs.malinhvuc = ck.malinhvuc
                where hs.mabenhnhan= '$mabenhnhan'
                group by ct.mahoso";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_hsba(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function insert_hosobenhandientu_mabenhnhan($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "insert into hosobenhan(mabenhnhan,ngaytao,ngaycapnhat) 
                values('$mabenhnhan',CURDATE(),CURDATE());";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_hsba_new($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM hosobenhan
                WHERE mabenhnhan = '$mabenhnhan'
                ORDER BY mahoso DESC
                LIMIT 1;";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_benhnhan_mahoso($mahoso){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from benhnhan bn 
                join nguoidung nd on nd.manguoidung = bn.mabenhnhan
                join hosobenhan hs on hs.mabenhnhan=bn.mabenhnhan
                join xaphuong x on x.maxaphuong = nd.maxaphuong
                join tinhthanhpho t on t.matinhthanhpho = x.matinhthanhpho
                where hs.mahoso = '$mahoso'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_hoso_mahoso($mahoso){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan hs
                join chitiethoso ct on hs.mahoso=ct.mahoso
                join bacsi bs on bs.mabacsi=ct.mabacsi
                join nguoidung nd on nd.manguoidung = bs.mabacsi
                where ct.mahoso = '$mahoso'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_hoso_mahoso1($mahoso){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan hs
                join chitiethoso ct on hs.mahoso=ct.mahoso
                join chuyengia bs on bs.machuyengia=ct.mabacsi
                join nguoidung nd on nd.manguoidung = bs.machuyengia
                where ct.mahoso = '$mahoso'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_hoso_machuyenkhoa($mabenhnhan,$machuyenkhoa){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan hs
                join chitiethoso ct on hs.mahoso=ct.mahoso
                join bacsi bs on bs.mabacsi=ct.mabacsi
                join chuyenkhoa ck on ck.machuyenkhoa =bs.machuyenkhoa
                join benhnhan bn on bn.mabenhnhan = hs.mabenhnhan
                where hs.mabenhnhan = '$mabenhnhan' and ck.machuyenkhoa = '$machuyenkhoa'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function select_hoso_malinhvuc($mabenhnhan,$machuyenkhoa){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from hosobenhan hs
                join chitiethoso ct on hs.mahoso=ct.mahoso
                join chuyengia bs on bs.machuyengia=ct.mabacsi
                join linhvuc ck on ck.malinhvuc =bs.malinhvuc
                join benhnhan bn on bn.mabenhnhan = hs.mabenhnhan
                where hs.mabenhnhan = '$mabenhnhan' and ck.malinhvuc = '$machuyenkhoa'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
    }
?>