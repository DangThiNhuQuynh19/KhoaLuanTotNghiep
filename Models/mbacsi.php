<?php
require_once('ketnoi.php');
include_once('Assets/config.php');
 class mBacSi{
        public function dsbacsi(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi 
                        join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                        join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                        left join taikhoan on nguoidung.email = taikhoan.tentk
                        left join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                        order by bacsi.mabacsi asc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function dsbacsi1(){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi 
                        join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                        join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                        join taikhoan on nguoidung.email = taikhoan.tentk
                        join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                        where taikhoan.matrangthai = 1 order by bacsi.mabacsi asc";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function chitietbacsi($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi 
                        join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                        join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                        join xaphuong on nguoidung.maxaphuong = xaphuong.maxaphuong
                        join tinhthanhpho on xaphuong.matinhthanhpho = tinhthanhpho.matinhthanhpho
                        left join taikhoan on nguoidung.email = taikhoan.tentk
                        left join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                        where bacsi.mabacsi='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        
        public function bacsitheoten($name){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM bacsi
                JOIN chuyenkhoa ON bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa
                JOIN nguoidung ON bacsi.mabacsi = nguoidung.manguoidung
                left join taikhoan on nguoidung.email = taikhoan.tentk
                left join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                WHERE nguoidung.hoten LIKE '%$name%'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function bacsitheokhoa($id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi join chuyenkhoa on bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                join nguoidung on bacsi.mabacsi = nguoidung.manguoidung
                left join taikhoan on nguoidung.email = taikhoan.tentk
                left join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                where bacsi.machuyenkhoa='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function bacsitheotenandkhoa($name,$id){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "SELECT * FROM bacsi 
                JOIN chuyenkhoa ON bacsi.machuyenkhoa = chuyenkhoa.machuyenkhoa 
                JOIN nguoidung ON bacsi.mabacsi = nguoidung.manguoidung
                left join taikhoan on nguoidung.email = taikhoan.tentk
                left join trangthai on taikhoan.matrangthai = trangthai.matrangthai
                WHERE nguoidung.hoten LIKE '%$name%' AND bacsi.machuyenkhoa='$id'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        
        public function xembacsitheotentk($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if($con){
                $str = "select * from bacsi b  join nguoidung d on b.mabacsi=d.manguoidung 
                join xaphuong x on d.maxaphuong = x.maxaphuong
                join tinhthanhpho t on x.matinhthanhpho = t.matinhthanhpho
                join chuyenkhoa c on b.machuyenkhoa = c.machuyenkhoa
                where email = '$tentk'";
                $tbl = $con->query($str);
                $p->dongketnoi($con);
                return $tbl;
            }else{
                return false; 
            }
        }
        public function updateBacSi($mabacsi, $data) {
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
            if (!$con) return false;
        
            // Lấy dữ liệu cũ
            $sqlOld = "SELECT b.*, n.* 
                       FROM bacsi b 
                       JOIN nguoidung n ON b.mabacsi = n.manguoidung
                       WHERE b.mabacsi=?";
            $stmt = $con->prepare($sqlOld);
            $stmt->bind_param("s", $mabacsi);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $p->dongketnoi($con);
                return false;
            }
            $old = $result->fetch_assoc();
        
            // Dữ liệu mới, giữ dữ liệu cũ nếu không có
            $hoten        = $data['hoten'] ?? $old['hoten'];
            $ngaysinh     = $data['ngaysinh'] ?? $old['ngaysinh'];
            $gioitinh     = $data['gioitinh'] ?? $old['gioitinh'];
            $cccd         = $data['cccd'] ?? $old['cccd'];
            $cccd_matruoc = $data['cccd_matruoc'] ?? $old['cccd_matruoc'];
            $cccd_matsau  = $data['cccd_matsau'] ?? $old['cccd_matsau'];
            $dantoc       = $data['dantoc'] ?? $old['dantoc'];
            $sdt          = isset($data['sdt']) ? encryptData($data['sdt']) : $old['sdt'];
        
            // ❌ Không cho phép update email đăng nhập
            $email        = $old['email'];
        
            $emailcanhan  = $data['emailcanhan'] ?? $old['emailcanhan'];
            $sonha        = $data['sonha'] ?? $old['sonha'];
            $maxaphuong   = $data['tenxaphuong'] ?? $old['maxaphuong'];
            $motabs       = $data['motabs'] ?? $old['motabs'];
            $gioithieubs  = $data['gioithieubs'] ?? $old['gioithieubs'];
            $ngaybatdau   = $data['ngaybatdau'] ?? $old['ngaybatdau'];
            $ngayketthuc  = $data['ngayketthuc'] ?? $old['ngayketthuc'];
            $imgbs        = $data['imgbs'] ?? $old['imgbs'];
            $giakham      = $data['giakham'] ?? $old['giakham'];
            $machuyenkhoa = $data['machuyenkhoa'] ?? $old['machuyenkhoa'];
            $capbac       = $data['capbac'] ?? $old['capbac'];
        
            // Update bảng bacsi
            $sqlBacSi = "UPDATE bacsi SET
                            gioithieubs=?, motabs=?, ngaybatdau=?, ngayketthuc=?,
                            imgbs=?, giakham=?, machuyenkhoa=?, capbac=?
                         WHERE mabacsi=?";
            $stmt1 = $con->prepare($sqlBacSi);
            $stmt1->bind_param(
                "sssssiiss",
                $gioithieubs,
                $motabs,
                $ngaybatdau,
                $ngayketthuc,
                $imgbs,
                $giakham,
                $machuyenkhoa,
                $capbac,
                $mabacsi
            );
            $ok1 = $stmt1->execute();
        
            // Update bảng nguoidung (❌ bỏ email ra khỏi phần update)
            $sqlNguoiDung = "UPDATE nguoidung SET
                                hoten=?, ngaysinh=?, gioitinh=?, cccd=?, cccd_matruoc=?, cccd_matsau=?,
                                dantoc=?, sdt=?, emailcanhan=?, sonha=?, maxaphuong=?
                             WHERE manguoidung=?";
            $stmt2 = $con->prepare($sqlNguoiDung);
            $stmt2->bind_param(
                "ssssssssssss",
                $hoten,
                $ngaysinh,
                $gioitinh,
                $cccd,
                $cccd_matruoc,
                $cccd_matsau,
                $dantoc,
                $sdt,
                $emailcanhan,
                $sonha,
                $maxaphuong,
                $mabacsi
            );
            $ok2 = $stmt2->execute();
        
            $p->dongketnoi($con);
            return $ok1 && $ok2;
        }
        
        public function xemlichlambacsi($tentk){
            $p = new clsKetNoi();
            $con = $p->moketnoi();
            $con->set_charset('utf8');
        
            if(!$con) return false;
        
            $str = "
                SELECT 
                    llv.*, 
                    c.tenca,
                    c.giobatdau,
                    c.gioketthuc,
                    p.tentoa,
                    p.tang,
                    p.sophong
                FROM nguoidung d
                JOIN bacsi b ON b.mabacsi = d.manguoidung
                JOIN lichlamviec llv ON llv.manguoidung = b.mabacsi   /* ✅ đúng */
                JOIN calamviec c ON c.macalamviec = llv.macalamviec
                LEFT JOIN phong p ON p.maphong = llv.maphong
                WHERE d.email = '$tentk'
                ORDER BY llv.ngaylam ASC
            ";
        
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }
        public function luuNguoiDungVaBacSi($data, $files) {
            $db = new clsKetNoi();
            $con = $db->moketnoi();
            if (!$con) return false;
        
            $con->begin_transaction();
        
            try {
                /* --- Thư mục upload --- */
                $uploadDir = __DIR__ . '/../Assets/img';
                $uploadDirCCCD = $uploadDir . '/cccd';
        
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                if (!is_dir($uploadDirCCCD)) mkdir($uploadDirCCCD, 0777, true);
        
                $safeName = function($name){
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    return time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
                };
        
                $upload = function($file, $dir) use ($safeName){
                    if (!isset($file) || $file['error'] !== 0) return null;
                    $newName = $safeName($file['name']);
                    move_uploaded_file($file['tmp_name'], $dir . '/' . $newName);
                    // chỉ lưu tên file
                    return $newName;
                };
        
                /* --- Upload từng file --- */
                $imgbs       = $upload($files['imgbs'] ?? null, $uploadDir);        // ảnh bác sĩ
                $cccd_before = $upload($files['cccd_matruoc'] ?? null, $uploadDirCCCD); // CCCD trước
                $cccd_after  = $upload($files['cccd_matsau'] ?? null, $uploadDirCCCD);  // CCCD sau
        
                /* --- Chuẩn bị dữ liệu --- */
                $manguoidung   = $data['manguoidung'] ?? null;
                $hoten         = $data['hoten'] ?? null;
                $ngaysinh      = $data['ngaysinh'] ?? null;
                $gioitinh      = $data['gioitinh'] ?? null;
                $cccd          = isset($data['cccd']) ? encryptData($data['cccd']) : null;
                $dantoc        = $data['dantoc'] ?? null;
                $sdt           = isset($data['sdt']) ? encryptData($data['sdt']) : null;
                $emailcanhan   = isset($data['emailcanhan']) ? encryptData($data['emailcanhan']) : null;
                $sonha         = $data['sonha'] ?? null;
                $maxaphuong    = $data['xaphuong'] ?? null;
                $email         = !empty($data['email']) ? $data['email'] : null; // FK nullable
        
                /* --- INSERT bảng nguoidung --- */
                $stmt = $con->prepare("
                    INSERT INTO nguoidung
                    (manguoidung, hoten, ngaysinh, gioitinh, cccd, cccd_matruoc, cccd_matsau, dantoc, sdt, emailcanhan, sonha, maxaphuong, email)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
                ");
        
                $stmt->bind_param(
                    "sssssssssssss",
                    $manguoidung,
                    $hoten,
                    $ngaysinh,
                    $gioitinh,
                    $cccd,
                    $cccd_before,
                    $cccd_after,
                    $dantoc,
                    $sdt,
                    $emailcanhan,
                    $sonha,
                    $maxaphuong,
                    $email
                );
        
                if (!$stmt->execute()) throw new Exception("Lỗi lưu bảng nguoidung: ".$stmt->error);
                $stmt->close();
        
                /* --- INSERT bảng bacsi --- */
                $motabs       = $data['motabs'] ?? null;
                $gioithieubs  = $data['gioithieubs'] ?? null;
                $ngaybatdau   = $data['ngaybatdau'] ?? null;
                $ngayketthuc  = $data['ngayketthuc'] ?? null;
                $giakham      = isset($data['giakham']) ? intval($data['giakham']) : 0;
                $machuyenkhoa = $data['machuyenkhoa'] ?? null;
                $capbac       = $data['capbac'] ?? null;
        
                $stmt2 = $con->prepare("
                    INSERT INTO bacsi
                    (mabacsi, motabs, gioithieubs, ngaybatdau, ngayketthuc, imgbs, giakham, machuyenkhoa, capbac)
                    VALUES (?,?,?,?,?,?,?,?,?)
                ");
        
                $stmt2->bind_param(
                    "ssssssiss",
                    $manguoidung,
                    $motabs,
                    $gioithieubs,
                    $ngaybatdau,
                    $ngayketthuc,
                    $imgbs,
                    $giakham,
                    $machuyenkhoa,
                    $capbac
                );
        
                if (!$stmt2->execute()) throw new Exception("Lỗi lưu bảng bacsi: ".$stmt2->error);
                $stmt2->close();
        
                $con->commit();
                $db->dongketnoi($con);
                return true;
        
            } catch (Exception $e) {
                $con->rollback();
                error_log("[ERROR] " . $e->getMessage());
                return false;
            }
        }
        
        
        
    }
?>