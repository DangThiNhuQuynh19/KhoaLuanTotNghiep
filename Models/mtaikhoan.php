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
            $str = "SELECT tk_bs.tentk, nd_bs.hoten, bs.imgbs
                    From phieukhambenh pkb
                    join bacsi bs on pkb.mabacsi = bs.mabacsi
                    join nguoidung nd_bs on nd_bs.manguoidung = bs.mabacsi
                    join taikhoan tk_bs on tk_bs.tentk = nd_bs.email
                    join benhnhan b on pkb.mabenhnhan = b.mabenhnhan
                    join nguoidung nd_bn on nd_bn.manguoidung = b.mabenhnhan
                    join taikhoan tk_bn on tk_bn.tentk = nd_bn.email
                    where tk_bn.tentk = '$tentk' group by tk_bs.tentk";
    
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
            $str = "SELECT * from taikhoan tk join nguoidung nd on nd.email = tk.tentk join benhnhan b on b.mabenhnhan = nd.manguoidung
                    join phieukhambenh pkb on b.mabenhnhan = pkb.mabenhnhan
                    where mavaitro=1 and pkb.mabacsi='$id' group by nd.manguoidung";
    
            $tbl = $con->query($str);
            $p->dongketnoi($con);
            return $tbl;
        }else{
            return false; 
        }
    }
}
?>