<?php
 include_once('ketnoi.php');
 class mLichXetNghiem{
        public function select_lichxetnghiem_mabacsi($mabacsi){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from lichxetnghiem as l
                JOIN trangthai tt on tt.matrangthai = l.matrangthai
                JOIN hosobenhan AS hs ON l.mahoso = hs.mahoso
                JOIN chitiethoso AS ct ON ct.mahoso=hs.mahoso
                join benhnhan as b on l.mabenhnhan=b.mabenhnhan
                JOIN nguoidung as nd_bn on nd_bn.manguoidung = b.mabenhnhan
                join loaixetnghiem as loai on l.maloaixetnghiem=loai.maloaixetnghiem 
                join chuyenkhoa as c on loai.machuyenkhoa=c.machuyenkhoa
                join khunggioxetnghiem as k on k.makhunggioxetnghiem = l.makhunggio
                where ct.mabacsi='$mabacsi' ORDER BY l.malichxetnghiem DESC ";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_lichxetnghiem_mabenhnhan($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from lichxetnghiem where mabenhnhan='$mabenhnhan' ORDER BY malichxetnghiem DESC";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function timkiem_lichxetnghiem($tukhoa, $machuyenkhoa, $trangthai,$mabacsi) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if ($con) {
                $sql = "SELECT * 
                        FROM lichxetnghiem AS l
                        JOIN trangthai tt on tt.matrangthai=l.matrangthai
                        JOIN hosobenhan AS hs ON l.mahoso = hs.mahoso
                        JOIN chitiethoso AS ct ON ct.mahoso=hs.mahoso
                        JOIN bacsi AS bs ON ct.mabacsi= bs.mabacsi
                        JOIN nguoidung as nd_bs on nd_bs.manguoidung=bs.mabacsi
                        JOIN benhnhan AS b ON l.mabenhnhan = b.mabenhnhan
                        JOIN nguoidung as nd_bn on nd_bn.manguoidung=b.mabenhnhan
                        JOIN loaixetnghiem AS loai ON l.maloaixetnghiem = loai.maloaixetnghiem
                        JOIN chuyenkhoa AS c ON loai.machuyenkhoa = c.machuyenkhoa
                        JOIN khunggioxetnghiem AS k ON k.makhunggioxetnghiem = l.makhunggio
                        WHERE ct.mabacsi = '$mabacsi'";
        
                if (!empty($tukhoa)) {
                    $sql .= " AND (b.mabenhnhan LIKE '%$tukhoa%' OR nd_bn.hoten LIKE '%$tukhoa%' OR l.malichxetnghiem LIKE '%$tukhoa%' )";
                }
        
                if (!empty($machuyenkhoa)) {
                    $sql .= " AND c.machuyenkhoa = '$machuyenkhoa'";
                }
        
                if (!empty($trangthai)) {
                    $sql .= " AND tt.tentrangthai = '$trangthai'";
                }
        
                $result = $con->query($sql);
                $p->dongketnoi($con);
                return $result;
            } else {
                return false;
            }
        }
        
        public function select_lichxetnghiem_malichxetnghiem($mabacsi, $malichxetnghiem){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * 
                FROM lichxetnghiem AS l
                JOIN trangthai AS tt ON tt.matrangthai = l.matrangthai
                JOIN hosobenhan AS hs ON l.mahoso = hs.mahoso
                JOIN chitiethoso AS ct ON ct.mahoso=hs.mahoso
                JOIN benhnhan AS b ON l.mabenhnhan = b.mabenhnhan
                JOIN loaixetnghiem AS loai ON l.maloaixetnghiem = loai.maloaixetnghiem
                JOIN chuyenkhoa AS c ON loai.machuyenkhoa = c.machuyenkhoa
                JOIN khunggioxetnghiem AS k ON k.makhunggioxetnghiem = l.makhunggio
                WHERE ct.mabacsi = '$mabacsi' and l.malichxetnghiem='$malichxetnghiem'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function lichxetnghiemtheotentk($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * 
                FROM lichxetnghiem AS l
                JOIN trangthai AS tt ON tt.matrangthai = l.matrangthai
                JOIN hosobenhan AS hs ON l.mahoso = hs.mahoso
                JOIN chitiethoso AS ct ON ct.mahoso=hs.mahoso
                JOIN benhnhan AS b ON l.mabenhnhan = b.mabenhnhan
                JOIN nguoidung nd ON nd.manguoidung = b.mabenhnhan
                JOIN loaixetnghiem AS loai ON l.maloaixetnghiem = loai.maloaixetnghiem
                JOIN chuyenkhoa AS c ON loai.machuyenkhoa = c.machuyenkhoa
                JOIN khunggioxetnghiem AS k ON k.makhunggioxetnghiem = l.makhunggio
                WHERE nd.email = '$tentk' ";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_lichxetnghiemchitiet_mabenhnhan($mabenhnhan){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * 
                FROM lichxetnghiem AS l
                JOIN trangthai tt on tt.matrangthai = l.matrangthai
                JOIN hosobenhan AS hs ON l.mahoso = hs.mahoso
                JOIN benhnhan AS b ON hs.mabenhnhan = b.mabenhnhan
                JOIN chitiethoso AS ct ON ct.mahoso=hs.mahoso
                JOIN loaixetnghiem AS loai ON l.maloaixetnghiem = loai.maloaixetnghiem
                JOIN chuyenkhoa AS c ON loai.machuyenkhoa = c.machuyenkhoa
                JOIN khunggioxetnghiem AS k ON k.makhunggioxetnghiem = l.makhunggio
                WHERE b.mabenhnhan='$mabenhnhan'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }

        public function select_lichxetnghiem() {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * from lichxetnghiem l JOIN trangthai tt on tt.matrangthai=l.matrangthai";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false;
            }
        }

        public function insert_lichxetnghiem($mabenhnhan,$maloaixetnghiem,$ngayhen,$makhunggio,$trangthailichxetnghiem,$mahoso,$img){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "INSERT INTO lichxetnghiem(mabenhnhan,maloaixetnghiem,ngayhen,makhunggio,matrangthai,mahoso,qr) 
                values('$mabenhnhan','$maloaixetnghiem','$ngayhen','$makhunggio','10','$mahoso','$img')";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false;
            }
        }

        public function select_lichxetnghiem_mahoso($mahoso) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * from lichxetnghiem l 
                JOIN trangthai tt on tt.matrangthai=l.matrangthai
                JOIN  loaixetnghiem loai on loai.maloaixetnghiem = l.maloaixetnghiem
                JOIN khunggioxetnghiem kg on kg.makhunggioxetnghiem = l.makhunggio
                WHERE l.mahoso='$mahoso'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false;
            }
        }
        public function selectall_lichxetnghiem($ngay = '', $matrangthai = '') {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if($con){
                $str = "SELECT l.*, tt.tentrangthai, nd.hoten, nd.sdt, loai.tenloaixetnghiem, kg.giobatdau, kg.gioketthuc
                        FROM lichxetnghiem l
                        JOIN trangthai tt ON tt.matrangthai = l.matrangthai
                        JOIN benhnhan b ON b.mabenhnhan = l.mabenhnhan
                        JOIN nguoidung nd ON nd.manguoidung = b.mabenhnhan
                        JOIN loaixetnghiem loai ON loai.maloaixetnghiem = l.maloaixetnghiem
                        JOIN khunggioxetnghiem kg ON kg.makhunggioxetnghiem = l.makhunggio
                        WHERE 1=1";
        
                if($ngay != ''){
                    $str .= " AND l.ngayhen = '".$con->real_escape_string($ngay)."'";
                }
        
                $allowedStatus = [10,11,12];
                if($matrangthai != '' && in_array($matrangthai, $allowedStatus)){
                    $str .= " AND l.matrangthai = ".intval($matrangthai);
                }
        
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            } else {
                return false;
            }
        }
        public function chitietlichxetnghiem($id) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT l.*, tt.tentrangthai, nd.hoten AS ten_benhnhan, nd.sdt AS sdt_benhnhan,
                            loai.tenloaixetnghiem, kg.giobatdau, kg.gioketthuc,
                            hs.*, ct.trieuchungbandau, ct.chandoan, ct.mabacsi,
                            bs.hoten AS ten_bacsi, bs.sdt AS sdt_bacsi, bacsi.capbac AS chucvu_bacsi,
                            kq.*, chuyenkhoa.tenchuyenkhoa
                        FROM lichxetnghiem l
                        JOIN trangthai tt ON tt.matrangthai = l.matrangthai
                        JOIN benhnhan b ON b.mabenhnhan = l.mabenhnhan
                        JOIN nguoidung nd ON nd.manguoidung = b.mabenhnhan
                        JOIN loaixetnghiem loai ON loai.maloaixetnghiem = l.maloaixetnghiem
                        JOIN khunggioxetnghiem kg ON kg.makhunggioxetnghiem = l.makhunggio
                        JOIN hosobenhan hs ON hs.mahoso = l.mahoso
                        JOIN chitiethoso ct ON ct.mahoso = hs.mahoso
                        LEFT JOIN nguoidung bs ON bs.manguoidung = ct.mabacsi
                        join bacsi on bacsi.mabacsi = ct.mabacsi
                        join chuyenkhoa on chuyenkhoa.machuyenkhoa = bacsi.machuyenkhoa
                        LEFT JOIN ketquaxetnghiem kq ON kq.malichxetnghiem = l.malichxetnghiem
                        WHERE l.malichxetnghiem = '$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false;
            }
        }
        
    }
?>