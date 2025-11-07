<?php
 include_once('ketnoi.php');
 class mLichLamViec{
        public function updatelichlamviecday($malichlamviec){
            $p = new clsketnoi();
            $truyvan = "UPDATE lichlamviec SET ghichu ='đã đặt' where malichlamviec='$malichlamviec'";
            $con = $p->moketnoi();
            $kq = mysqli_query($con, $truyvan);
            $p->dongketnoi($con);
            return $kq;
        }
        public function updatelichlamviectrong($malichlamviec){
            $p = new clsketnoi();
            $truyvan = "UPDATE lichlamviec SET ghichu ='' where malichlamviec='$malichlamviec'";
            $con = $p->moketnoi();
            $kq = mysqli_query($con, $truyvan);
            $p->dongketnoi($con);
            return $kq;
        }
        public function laymalichlamviec($mabacsi,$ngaylam,$macalamviec){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select malichlamviec from lichlamviec where mabacsi='$mabacsi' and ngaylam='$ngaylam' and
                        macalamviec='$macalamviec'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function lichlamviec($ngay){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT 
                            llv.*, 
                            nd.*, 

                            bs.mabacsi,
                            bs.imgbs AS avatar_bacsi,

                            cg.machuyengia,
                            cg.imgcg AS avatar_chuyengia,
                    

                            clv.tenca,
                            kgkb.giobatdau,
                            kgkb.gioketthuc,

                            p.tentoa,
                            p.tang,
                            p.sophong

                        FROM lichlamviec llv

                        JOIN nguoidung nd 
                            ON llv.manguoidung = nd.manguoidung

                        LEFT JOIN bacsi bs 
                            ON nd.manguoidung = bs.mabacsi

                        LEFT JOIN chuyengia cg 
                            ON nd.manguoidung = cg.machuyengia

                        JOIN calamviec clv 
                            ON llv.macalamviec = clv.macalamviec

                        JOIN khunggiokhambenh kgkb 
                            ON kgkb.macalamviec = clv.macalamviec

                        LEFT JOIN phong p 
                            ON p.maphong = llv.maphong

                        WHERE llv.ngaylam = '$ngay'
                        ORDER BY nd.hoten ASC, kgkb.giobatdau ASC;

                    ";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
    }
?>