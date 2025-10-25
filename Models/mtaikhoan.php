<?php
include_once("ketnoi.php");


class mtaikhoan{
    private $conn;

    public function __construct() {
        $p = new clsketnoi();
        $this->conn = $p->moketnoi();
    }

    // Đăng ký tài khoản
    public function dangkytk ($mabenhnhan, $email, $hoten, $ngaysinh, $sdt, $cccd, $cccd_truoc_name, $cccd_sau_name, $gioitinh, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $sonha, $xa, $tinh, $matkhau) {
        // Kiểm tra email đã tồn tại
        $dantoc='kinh';
        $stmtCheck = $this->conn->prepare("SELECT * FROM taikhoan WHERE tentk = ?");
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        
        if ($result->num_rows > 0) {
            return "email_ton_tai"; // Nếu email đã tồn tại
        }
    
        // Băm mật khẩu (tốt hơn nên dùng password_hash)
        $hashedPassword = md5($matkhau);
        $cccd_truoc_name = !empty($cccd_truoc_name) ? $cccd_truoc_name : null;
        $cccd_sau_name   = !empty($cccd_sau_name)   ? $cccd_sau_name   : null;
        // Thêm vào bảng taikhoan
        $stmtInsertTK = $this->conn->prepare("INSERT INTO taikhoan (tentk, matkhau, mavaitro, matrangthai) VALUES (?, ?, 1,1)");
        $stmtInsertTK->bind_param("ss", $email, $hashedPassword);
        
        if ($stmtInsertTK->execute()) {
            // Tính tuổi bệnh nhân
            $today = new DateTime();
            $dob = new DateTime($ngaysinh);
            $age = $today->diff($dob)->y;
    
            // Thêm vào bảng nguoidung
            $stmtInsertND = $this->conn->prepare("INSERT INTO nguoidung (manguoidung, hoten, ngaysinh, gioitinh, cccd, cccd_matruoc, cccd_matsau, dantoc, sdt, sonha, maxaphuong, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsertND->bind_param("ssssssssssss", $mabenhnhan, $hoten, $ngaysinh, $gioitinh, $cccd, $cccd_truoc_name, $cccd_sau_name,$dantoc, $sdt, $sonha, $xa, $email);
            
            if($stmtInsertND->execute()){
                // Thêm bệnh nhân
                $stmtInsertBN = $this->conn->prepare("INSERT INTO benhnhan(mabenhnhan, nghenghiep, tiensubenhtatcuagiadinh, tiensubenhtatcuabenhnhan) VALUES (?, ?, ?, ?)");
                $stmtInsertBN->bind_param("ssss", $mabenhnhan, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan);
                if(!$stmtInsertBN->execute()){
                    var_dump($stmtInsertBN->error); exit;
                }
                return true;
            } else {
                return "Lỗi khi thêm thông tin người dùng.";
            }
        } else {
            return "Lỗi khi tạo tài khoản.";
        }
    }
    public function select_01_taikhoan($tentk, $matkhau) {
        $truyvan = "SELECT * FROM taikhoan WHERE tentk = ? and matkhau= ?";
        $stmt = $this->conn->prepare($truyvan);
        $stmt->bind_param("ss", $tentk, $matkhau);
        $stmt->execute();
        return $stmt->get_result(); 
    }
    public function taikhoanbacsi($tentk){
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if($con){
            $str = "
            (
                -- 🧍 BÁC SĨ đã khám cho chính người giám hộ
                SELECT DISTINCT 
                    bs_nd.email AS tentk,
                    bs_nd.hoten AS hoten,
                    bs.imgbs AS img,
                    'bacsi' AS vaitro
                FROM phieukhambenh pkb
                JOIN bacsi bs ON pkb.mabacsi = bs.mabacsi
                JOIN nguoidung bs_nd ON bs_nd.manguoidung = bs.mabacsi
                JOIN benhnhan b ON pkb.mabenhnhan = b.mabenhnhan
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = b.mabenhnhan
                JOIN taikhoan tk_bn ON tk_bn.tentk = nd_bn.email
                WHERE tk_bn.tentk = '$tentk'
               
            )
            UNION
            (
                -- 👶 BÁC SĨ đã khám cho bệnh nhân được người giám hộ này giám hộ
                SELECT DISTINCT 
                    bs_nd.email AS tentk,
                    bs_nd.hoten AS hoten,
                    bs.imgbs AS img,
                    'bacsi' AS vaitro
                FROM phieukhambenh pkb
                JOIN bacsi bs ON pkb.mabacsi = bs.mabacsi
                JOIN nguoidung bs_nd ON bs_nd.manguoidung = bs.mabacsi
                JOIN benhnhan bn_con ON pkb.mabenhnhan = bn_con.mabenhnhan
                JOIN nguoidung nd_giamho ON nd_giamho.manguoidung = bn_con.manguoigiamho
                JOIN taikhoan tk_giamho ON tk_giamho.tentk = nd_giamho.email
                WHERE tk_giamho.tentk = '$tentk'
                AND bn_con.manguoigiamho = nd_giamho.manguoidung
               
            )
            UNION
            (
                -- 🧍 CHUYÊN GIA đã khám cho chính người giám hộ
                SELECT DISTINCT 
                    cg_nd.email AS tentk,
                    cg_nd.hoten AS hoten,
                    cg.imgcg AS img,
                    'chuyengia' AS vaitro
                FROM phieukhambenh pkb
                JOIN chuyengia cg ON pkb.mabacsi = cg.machuyengia
                JOIN nguoidung cg_nd ON cg_nd.manguoidung = cg.machuyengia
                JOIN benhnhan b ON pkb.mabenhnhan = b.mabenhnhan
                JOIN nguoidung nd_bn ON nd_bn.manguoidung = b.mabenhnhan
                JOIN taikhoan tk_bn ON tk_bn.tentk = nd_bn.email
                WHERE tk_bn.tentk = '$tentk'
              
            )
            UNION
            (
                -- 👶 CHUYÊN GIA đã khám cho bệnh nhân được người giám hộ này giám hộ
                SELECT DISTINCT 
                    cg_nd.email AS tentk,
                    cg_nd.hoten AS hoten,
                    cg.imgcg AS img,
                    'chuyengia' AS vaitro
                FROM phieukhambenh pkb
                JOIN chuyengia cg ON pkb.mabacsi = cg.machuyengia
                JOIN nguoidung cg_nd ON cg_nd.manguoidung = cg.machuyengia
                JOIN benhnhan bn_con ON pkb.mabenhnhan = bn_con.mabenhnhan
                JOIN nguoidung nd_giamho ON nd_giamho.manguoidung = bn_con.manguoigiamho
                JOIN taikhoan tk_giamho ON tk_giamho.tentk = nd_giamho.email
                WHERE tk_giamho.tentk = '$tentk'
                AND bn_con.manguoigiamho = nd_giamho.manguoidung
             
            )
            ";

            $tbl = $con->query($str);
        
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }
    public function taikhoanbenhnhan($id){
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if($con){
            $str = "SELECT DISTINCT 
                    tk_giamho.tentk,
                    nd_giamho.hoten
                FROM phieukhambenh pkb
                JOIN benhnhan b 
                    ON pkb.mabenhnhan = b.mabenhnhan 
                    OR pkb.mabenhnhan = b.manguoigiamho
                LEFT JOIN nguoidung nd_giamho 
                    ON nd_giamho.manguoidung = 
                        CASE 
                            WHEN pkb.mabenhnhan = b.manguoigiamho THEN b.manguoigiamho
                            ELSE b.manguoigiamho
                        END
                JOIN taikhoan tk_giamho ON tk_giamho.tentk = nd_giamho.email
                WHERE pkb.mabacsi = '$id'
                AND tk_giamho.mavaitro = 1;
                ";
    
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }

    public function select_taikhoan(){
        $p = new clsKetNoi();
        $con = $p->moketnoi();
        $con->set_charset('utf8');
        if($con){
            $str = "select * from taikhoan join nguoidung on taikhoan.tentk=nguoidung.email";
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }


}
?>