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
                        END AS thongtin, k.makhunggiokb
                    FROM lichlamviec llv
                    JOIN calamviec cv 
                        ON llv.macalamviec = cv.macalamviec
                    JOIN khunggiokhambenh k 
                        ON k.macalamviec = cv.macalamviec
                    LEFT JOIN phong p 
                        ON p.maphong = llv.maphong
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
            $sql = "SELECT 
                pkb.ngaykham, 
                clv.giobatdau, 
                clv.gioketthuc, 
                tk_bs.tentk AS tentk_bacsi,
                tk_bn.tentk AS tentk_benhnhan
            FROM phieukhambenh pkb
            -- JOIN khunggiokhambenh trước
            JOIN khunggiokhambenh kgb 
                ON pkb.makhunggiokb = kgb.makhunggiokb
            -- JOIN calamviec sau
            JOIN calamviec clv 
                ON kgb.macalamviec = clv.macalamviec
            -- Thông tin Bác sĩ
            JOIN bacsi bs 
                ON pkb.mabacsi = bs.mabacsi
            JOIN nguoidung nd_bs 
                ON nd_bs.manguoidung = bs.mabacsi
            JOIN taikhoan tk_bs 
                ON tk_bs.tentk = nd_bs.email
            -- Thông tin Bệnh nhân
            JOIN benhnhan bn 
                ON pkb.mabenhnhan = bn.mabenhnhan
            JOIN nguoidung nd_bn 
                ON nd_bn.manguoidung = bn.mabenhnhan
            JOIN taikhoan tk_bn 
                ON tk_bn.tentk = nd_bn.email
            WHERE tk_bs.tentk = '$bs' 
            AND tk_bn.tentk = '$bn';
            ";
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
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if (!$con) return false;
        
            $gioHienTai = date('H:i:s');
        
            $sql = "
                SELECT 
                    llv.*,
                    c.*,
                    kg.*,
                    nd.*,
                    bs.*,
                    cg.*,
                    kg.giobatdau AS kg_giobatdau,
                    kg.gioketthuc AS kg_gioketthuc,
                    CONCAT(
                    CASE 
                        WHEN p.tentoa IS NOT NULL AND p.tentoa <> '' 
                        THEN CONCAT('Tòa ', p.tentoa) 
                        ELSE '' 
                    END,
                    CASE 
                        WHEN p.tang IS NOT NULL AND p.tang <> '' 
                        THEN CONCAT(
                            CASE WHEN p.tentoa IS NOT NULL AND p.tentoa <> '' THEN ' - ' ELSE '' END,
                            'Tầng ', p.tang
                        )
                        ELSE '' 
                    END,
                    CASE 
                        WHEN p.sophong IS NOT NULL AND p.sophong <> '' 
                        THEN CONCAT(
                            CASE 
                                WHEN (p.tentoa IS NOT NULL AND p.tentoa <> '') OR (p.tang IS NOT NULL AND p.tang <> '') 
                                THEN ' - ' 
                                ELSE '' 
                            END,
                            'Phòng ', p.sophong
                        )
                        ELSE '' 
                    END
                ) AS thongtin_phong

                FROM khunggiokhambenh kg
                JOIN calamviec c ON kg.macalamviec = c.macalamviec
                JOIN lichlamviec llv ON llv.macalamviec = c.macalamviec
                JOIN nguoidung nd ON nd.manguoidung = llv.manguoidung
                LEFT JOIN bacsi bs ON bs.mabacsi = llv.manguoidung
                LEFT JOIN chuyengia cg ON cg.machuyengia = llv.manguoidung
                LEFT JOIN phong p ON llv.maphong = p.maphong
                LEFT JOIN phieukhambenh pkb
                    ON pkb.makhunggiokb = kg.makhunggiokb
                    AND pkb.ngaykham = ?
                    AND pkb.matrangthai = 6
                    AND pkb.mabacsi = llv.manguoidung
                WHERE 
                    llv.ngaylam = ?
                    AND pkb.maphieukhambenh IS NULL
                    AND (
                        llv.ngaylam > CURDATE() 
                        OR (llv.ngaylam = CURDATE() AND kg.giobatdau >= ?)
                    )
                ORDER BY llv.ngaylam, kg.giobatdau
            ";
        
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sss", $ngay, $ngay, $gioHienTai);
            $stmt->execute();
            $result = $stmt->get_result();
        
            $p->dongketnoi($con);
            return $result;
        }
        
        public function getLichTrongTheoNguoi($manguoi, $ngayChon) {
            date_default_timezone_set('Asia/Ho_Chi_Minh');
        
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if (!$con) return false;
        
            $gioHienTai = date('H:i:s');
        
            $sql = "
                SELECT 
                    llv.*,
                    c.*,
                    kg.*,
                    nd.*,
                    bs.*,
                    cg.*,
                    kg.giobatdau AS kg_giobatdau,
                    kg.gioketthuc AS kg_gioketthuc,
                    llv.hinhthuclamviec,
                    CONCAT(
                        CASE WHEN p.tentoa IS NOT NULL AND p.tentoa <> '' THEN CONCAT('Tòa ', p.tentoa) ELSE '' END,
                        CASE WHEN p.tang IS NOT NULL AND p.tang <> '' THEN CONCAT(
                            CASE WHEN p.tentoa IS NOT NULL AND p.tentoa <> '' THEN ' - ' ELSE '' END,
                            'Tầng ', p.tang
                        ) ELSE '' END,
                        CASE WHEN p.sophong IS NOT NULL AND p.sophong <> '' THEN CONCAT(
                            CASE WHEN (p.tentoa IS NOT NULL AND p.tentoa <> '') OR (p.tang IS NOT NULL AND p.tang <> '') THEN ' - ' ELSE '' END,
                            'Phòng ', p.sophong
                        ) ELSE '' END
                    ) AS thongtin_phong
                FROM khunggiokhambenh kg
                JOIN calamviec c ON kg.macalamviec = c.macalamviec
                JOIN lichlamviec llv ON llv.macalamviec = c.macalamviec
                JOIN nguoidung nd ON nd.manguoidung = llv.manguoidung
                LEFT JOIN bacsi bs ON bs.mabacsi = llv.manguoidung
                LEFT JOIN chuyengia cg ON cg.machuyengia = llv.manguoidung
                LEFT JOIN phong p ON llv.maphong = p.maphong
                LEFT JOIN phieukhambenh pkb
                    ON bs.mabacsi IS NOT NULL
                    AND pkb.makhunggiokb = kg.makhunggiokb
                    AND pkb.ngaykham = ?
                    AND pkb.matrangthai = 6
                    AND pkb.mabacsi = llv.manguoidung
                WHERE llv.manguoidung = ?
                AND llv.ngaylam = ?
                AND pkb.maphieukhambenh IS NULL
                AND (
                    ? <> ? OR kg.giobatdau >= ?
                )
                ORDER BY llv.ngaylam, kg.giobatdau
            ";
        
            $stmt = $con->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed: " . $con->error);
                return false;
            }
        
            // Nếu ngày chọn là hôm nay, lọc giờ đã qua
            $isToday = $ngayChon === date('Y-m-d');
        
            $stmt->bind_param(
                "ssssss",
                $ngayChon,   // pkb.ngaykham = ?
                $manguoi,    // llv.manguoidung = ?
                $ngayChon,   // llv.ngaylam = ?
                date('Y-m-d'), // so sánh với hôm nay
                date('Y-m-d'), // so sánh với hôm nay
                $gioHienTai  // kg.giobatdau >= ?
            );
        
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }
        
            $result = $stmt->get_result();
            $stmt->close();
            $p->dongketnoi($con);
        
            return $result;
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

        public function select_lichkham_mabacsi($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT 
                            ca.macalamviec, 
                            ca.tenca, 
                            l.*, 
                            kg.*, 
                            p.*, 
                            nd.* 
                        FROM lichlamviec l
                        JOIN calamviec ca ON ca.macalamviec = l.macalamviec
                        JOIN khunggiokhambenh kg ON kg.macalamviec = l.macalamviec
                        JOIN phong p ON p.maphong = l.maphong
                        JOIN nguoidung nd ON nd.manguoidung = l.manguoidung
                        WHERE l.manguoidung = '$mabacsi'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
}

?>