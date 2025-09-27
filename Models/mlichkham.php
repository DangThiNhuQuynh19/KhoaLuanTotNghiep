<?php
 include_once('ketnoi.php');
class mLichKham {
    public function lichkhamcg($ngay, $id, $gioHienTai = null) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if ($con) {
            $str = "";
            // Kiểm tra nếu ngày là hôm nay và có giờ hiện tại
            if ($ngay == date('Y-m-d') && $gioHienTai !== null) {
                // Lọc ca làm việc có giờ bắt đầu >= giờ hiện tại
                $str = "SELECT calamviec.tenca, lichlamviec.*, nguoidung.*, chuyengia.*, 
                        phieukhambenh.*, khunggiokhambenh.*
                        FROM khunggiokhambenh
                        JOIN calamviec ON khunggiokhambenh.macalamviec = calamviec.macalamviec
                        JOIN lichlamviec ON lichlamviec.macalamviec = calamviec.macalamviec
                        JOIN nguoidung ON lichlamviec.manguoidung = nguoidung.manguoidung
                        JOIN chuyengia ON nguoidung.manguoidung = chuyengia.machuyengia
                        LEFT JOIN phieukhambenh 
                            ON phieukhambenh.makhunggiokb = khunggiokhambenh.makhunggiokb
                            AND phieukhambenh.ngaykham = lichlamviec.ngaylam
                            AND phieukhambenh.mabacsi = lichlamviec.manguoidung
                            AND phieukhambenh.matrangthai = 6 
                            WHERE  ngaylam = '$ngay' 
                                AND chuyengia.machuyengia = '$id'
                                AND khunggiokhambenh.giobatdau >= '$gioHienTai'
                                AND phieukhambenh.maphieukhambenh IS NULL";
            } else {
                // Ngày lớn hơn hôm nay, hiển thị tất cả ca
                $str = "SELECT calamviec.tenca, lichlamviec.*, nguoidung.*, chuyengia.*, 
                            phieukhambenh.*, khunggiokhambenh.*
                        FROM khunggiokhambenh
                        JOIN calamviec ON khunggiokhambenh.macalamviec = calamviec.macalamviec
                        JOIN lichlamviec ON lichlamviec.macalamviec = calamviec.macalamviec
                        JOIN nguoidung ON lichlamviec.manguoidung = nguoidung.manguoidung
                        JOIN chuyengia ON nguoidung.manguoidung = chuyengia.machuyengia
                        LEFT JOIN phieukhambenh 
                            ON phieukhambenh.makhunggiokb = khunggiokhambenh.makhunggiokb
                            AND phieukhambenh.ngaykham = lichlamviec.ngaylam
                            AND phieukhambenh.mabacsi = lichlamviec.manguoidung
                            AND phieukhambenh.matrangthai = 6 
                        WHERE  ngaylam = '$ngay' 
                            AND chuyengia.machuyengia = '$id'
                            AND phieukhambenh.maphieukhambenh IS NULL";
            }
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        } else {
            return false;
        }
    }
    public function lichkhambs($ngay, $id, $gioHienTai = null) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if ($con) {
            $str = "";
            // Kiểm tra nếu ngày là hôm nay và có giờ hiện tại
            if ($ngay == date('Y-m-d') && $gioHienTai !== null) {
                // Lọc ca làm việc có giờ bắt đầu >= giờ hiện tại
                $str = "SELECT calamviec.tenca, lichlamviec.*, nguoidung.*, bacsi.*, 
                        phieukhambenh.*, khunggiokhambenh.*
                        FROM khunggiokhambenh
                        JOIN calamviec ON khunggiokhambenh.macalamviec = calamviec.macalamviec
                        JOIN lichlamviec ON lichlamviec.macalamviec = calamviec.macalamviec
                        JOIN nguoidung ON lichlamviec.manguoidung = nguoidung.manguoidung
                        JOIN bacsi ON nguoidung.manguoidung = bacsi.mabacsi
                        LEFT JOIN phieukhambenh 
                            ON phieukhambenh.makhunggiokb = khunggiokhambenh.makhunggiokb
                            AND phieukhambenh.ngaykham = lichlamviec.ngaylam
                            AND phieukhambenh.mabacsi = lichlamviec.manguoidung
                            AND phieukhambenh.matrangthai = 6 
                            WHERE  ngaylam = '$ngay' 
                                AND bacsi.mabacsi = '$id'
                                AND khunggiokhambenh.giobatdau >= '$gioHienTai'
                                AND phieukhambenh.maphieukhambenh IS NULL";
            } else {
                // Ngày lớn hơn hôm nay, hiển thị tất cả ca
                $str = "SELECT calamviec.tenca, lichlamviec.*, nguoidung.*, bacsi.*, 
                            phieukhambenh.*, khunggiokhambenh.*
                        FROM khunggiokhambenh
                        JOIN calamviec ON khunggiokhambenh.macalamviec = calamviec.macalamviec
                        JOIN lichlamviec ON lichlamviec.macalamviec = calamviec.macalamviec
                        JOIN nguoidung ON lichlamviec.manguoidung = nguoidung.manguoidung
                        JOIN bacsi ON nguoidung.manguoidung = bacsi.mabacsi
                        LEFT JOIN phieukhambenh 
                            ON phieukhambenh.makhunggiokb = khunggiokhambenh.makhunggiokb
                            AND phieukhambenh.ngaykham = lichlamviec.ngaylam
                            AND phieukhambenh.mabacsi = lichlamviec.manguoidung
                            AND phieukhambenh.matrangthai = 6 
                        WHERE  ngaylam = '$ngay' 
                            AND bacsi.mabacsi = '$id'
                            AND phieukhambenh.maphieukhambenh IS NULL";
            }
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        } else {
            return false;
        }
    }
    public function xemlich($idca, $ngay, $idbs){
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if($con){
            $str = "SELECT 
                        CONCAT(k.giobatdau, ' - ', k.gioketthuc) AS giokham,
                        CASE 
                            WHEN llv.hinhthuclamviec = 'Offline' 
                                THEN CONCAT('Tòa: ', p.tentoa, ', Tầng: ', p.tang, ', Phòng: ', p.sophong)
                            ELSE 'Khám trực tuyến (Online)'
                        END AS thongtin
                    FROM lichlamviec llv
                    JOIN calamviec cv 
                        ON llv.macalamviec = cv.macalamviec
                    JOIN khunggiokhambenh k 
                        ON k.macalamviec = cv.macalamviec
                    LEFT JOIN phong p 
                        ON p.malichlamviec = llv.malichlamviec
                    WHERE k.makhunggiokb = '$idca'
                    AND llv.ngaylam = '$ngay'
                    AND llv.manguoidung = '$idbs'
                    LIMIT 1;";
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }
    
   
    public function kiemtragiohen($bs, $bn) {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');

        if ($con) {
            $sql = "SELECT pkb.ngaykham, clv.giobatdau, clv.gioketthuc, bs.tentk, bn.tentk
                    FROM phieukhambenh pkb
                    JOIN calamviec clv ON pkb.macalamviec = clv.macalamviec
                    JOIN bacsi bs ON pkb.mabacsi = bs.mabacsi
                    JOIN benhnhan bn ON pkb.mabenhnhan = bn.mabenhnhan
                    WHERE bs.tentk = '$bs' AND bn.tentk = '$bn'";
            $result = $con->query($sql);
            $p->dongketnoi($con);
            return $result;
        } else {
            return false;
        }
    }
    public function lichhen() {
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');

        if ($con) {
            $sql = "SELECT pk.maphieukhambenh, pk.ngaykham, kgkb.giobatdau, nd_bn.hoten AS ten_benhnhan, 
                    nd_bs.hoten AS ten_bacsi, tt.tentrangthai FROM phieukhambenh AS pk 
                    JOIN khunggiokhambenh AS kgkb ON pk.makhunggiokb = kgkb.makhunggiokb 
                    JOIN bacsi AS bs ON pk.mabacsi = bs.mabacsi 
                    JOIN benhnhan AS bn ON pk.mabenhnhan = bn.mabenhnhan 
                    
                    JOIN nguoidung AS nd_bs ON bs.mabacsi = nd_bs.manguoidung 
                    JOIN nguoidung AS nd_bn ON bn.mabenhnhan = nd_bn.manguoidung 
                    JOIN trangthai AS tt ON pk.matrangthai = tt.matrangthai 
                    WHERE pk.ngaykham >= CURRENT_DATE ORDER BY pk.ngaykham ASC, kgkb.giobatdau ASC;";
            $result = $con->query($sql);
            $p->dongketnoi($con);
            return $result;
        } else {
            return false;
        }
    }
            // Lấy lịch bác sĩ theo ngày
            public function getLichBacSiTheoNgay($ngay, $mabacsi){
                $p = new clsKetNoi();
                $con = $p->moketnoi();
                $con->set_charset('utf8');
            
                if ($con) {
                    $sql = "SELECT ll.*, cv.tenca, kg.giobatdau, kg.gioketthuc, nguoidung.hoten AS hoten 
                            FROM lichlamviec ll
                            JOIN calamviec cv ON cv.macalamviec = ll.macalamviec 
                            JOIN bacsi b ON b.mabacsi = ll.manguoidung 
                            JOIN nguoidung ON nguoidung.manguoidung = ll.manguoidung 
                            JOIN khunggiokhambenh kg ON kg.macalamviec = ll.macalamviec 
                            WHERE ll.manguoidung = '$mabacsi' AND DATE(ll.ngaylam) = '$ngay' 
                            ORDER BY kg.giobatdau ASC";
                    $tbl = $con->query($sql);
                    $p->dongketnoi($con);
                    return $tbl;
                } else {
                    return false;
                }
            }
            

        // Lấy lịch chuyên gia theo ngày
        public function getLichChuyenGiaTheoNgay($ngay, $machuyengia){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if ($con) {
                $sql = "SELECT ll.*, cv.tenca, kg.giobatdau, kg.gioketthuc, nguoidung.hoten AS hoten 
                        FROM lichlamviec ll
                        JOIN calamviec cv ON cv.macalamviec = ll.macalamviec 
                        JOIN chuyengia cg ON cg.machuyengia = ll.manguoidung 
                        JOIN nguoidung ON nguoidung.manguoidung = ll.manguoidung 
                        JOIN khunggiokhambenh kg ON kg.macalamviec = ll.macalamviec 
                        WHERE ll.manguoidung = '$machuyengia' AND DATE(ll.ngaylam) = '$ngay' 
                        ORDER BY kg.giobatdau ASC";
                $tbl = $con->query($sql);
                $p->dongketnoi($con);
                return $tbl;
            } else {
                return false;
            }
        }
        public function getTatCaLichKhamTheoNgay($ngay) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if ($con) {
                $sql = "
                    SELECT llv.*, c.*, kg.*, nd.*, bs.*, cg.*, ck.*, lv.*
                    FROM khunggiokhambenh kg
                    JOIN calamviec c ON kg.macalamviec = c.macalamviec
                    JOIN lichlamviec llv ON llv.macalamviec = c.macalamviec
                    JOIN nguoidung nd ON nd.manguoidung = llv.manguoidung
                    LEFT JOIN bacsi bs ON bs.mabacsi = llv.manguoidung
                    LEFT JOIN chuyengia cg ON cg.machuyengia = llv.manguoidung
                    LEFT JOIN chuyenkhoa ck ON ck.machuyenkhoa = bs.machuyenkhoa
                    LEFT JOIN linhvuc lv ON lv.malinhvuc = cg.malinhvuc
                    LEFT JOIN phieukhambenh pkb 
                        ON pkb.makhunggiokb = kg.makhunggiokb
                        AND pkb.ngaykham = llv.ngaylam
                        AND pkb.mabacsi = llv.manguoidung
                        AND pkb.matrangthai = 6
                    WHERE llv.ngaylam = '$ngay'
                        AND pkb.maphieukhambenh IS NULL
                ";
        
                $tbl = $con->query($sql);
                $p->dongketnoi($con);
                return $tbl;
            } else {
                return false;
            }
        }
        public function getLichTrongTheoNguoi($tuNgay, $manguoi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if($con){
                $sql = "SELECT llv.*, c.*, kg.*, nd.*, bs.*, cg.*
                        FROM khunggiokhambenh kg
                        JOIN calamviec c ON kg.macalamviec = c.macalamviec
                        JOIN lichlamviec llv ON llv.macalamviec = c.macalamviec
                        JOIN nguoidung nd ON nd.manguoidung = llv.manguoidung
                        LEFT JOIN bacsi bs ON bs.mabacsi = llv.manguoidung
                        LEFT JOIN chuyengia cg ON cg.machuyengia = llv.manguoidung
                        LEFT JOIN phieukhambenh pkb 
                            ON pkb.makhunggiokb = kg.makhunggiokb
                            AND pkb.mabacsi = llv.manguoidung
                            AND pkb.ngaykham >= ?
                            AND pkb.matrangthai = 6
                        WHERE llv.manguoidung = ?
                          AND llv.ngaylam >= ?
                          AND pkb.maphieukhambenh IS NULL
                        ORDER BY llv.ngaylam, kg.giobatdau";
                
                $stmt = $con->prepare($sql);
                $stmt->bind_param("sis", $tuNgay, $manguoi, $tuNgay);
                $stmt->execute();
                $result = $stmt->get_result();
                $p->dongketnoi($con);
                return $result;
            }else{
                return false;
            }
        }
        function getThongTinNguoi($manguoidung) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            // Kiểm tra bác sĩ
            $sql = "SELECT mabacsi AS id, hoten, 0 AS vaitro FROM bacsi WHERE mabacsi='$manguoidung' LIMIT 1";
            $result = $con->query($sql);
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        
            // Kiểm tra chuyên gia
            $sql = "SELECT machuyengia AS id, hoten, 1 AS vaitro FROM chuyengia WHERE machuyengia='$manguoidung' LIMIT 1";
            $result = $con->query($sql);
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        
            return null; // Không tìm thấy
        }
}

?>