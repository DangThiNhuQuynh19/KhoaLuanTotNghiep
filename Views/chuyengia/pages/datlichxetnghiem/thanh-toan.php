<?php
include_once("Assets/config.php");
include_once('xu-ly-thanh-toan.php');

$xu_ly_thanh_toan = new XuLyThanhToan();
$thong_bao = '';
$loai_thong_bao = '';
$thong_tin_lich_hen = null;

// Kiểm tra tham số từ URL
if (isset($_GET['ma']) && isset($_GET['lich'])) {
    $ma_bao_mat = $_GET['ma'];
    $ma_lich_hen = $_GET['lich'];
    
    // Lấy thông tin lịch hẹn
    $sql = "SELECT lh.*, bn.hoten, bn.email, lxn.tenloaixetnghiem, kg.giobatdau 
            FROM lichxetnghiem lh 
            JOIN benhnhan bn ON lh.mabenhnhan = bn.mabenhnhan 
            JOIN loaixetnghiem lxn ON lh.maloaixetnghiem = lxn.maloaixetnghiem 
            JOIN khunggioxetnghiem kg ON lh.makhunggio = kg.makhunggioxetnghiem 
            WHERE lh.malichhen = ? AND lh.trangthai = 'Chờ thanh toán'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ma_lich_hen);
    $stmt->execute();
    $ket_qua = $stmt->get_result();
    
    if ($ket_qua->num_rows > 0) {
        $thong_tin_lich_hen = $ket_qua->fetch_assoc();
        
        // Kiểm tra thời hạn thanh toán
        $sql_kiem_tra_han = "SELECT thoi_gian_het_han FROM email_thanh_toan WHERE ma_lich_hen = ?";
        $stmt_han = $conn->prepare($sql_kiem_tra_han);
        $stmt_han->bind_param("i", $ma_lich_hen);
        $stmt_han->execute();
        $ket_qua_han = $stmt_han->get_result();
        
        if ($ket_qua_han->num_rows > 0) {
            $thong_tin_han = $ket_qua_han->fetch_assoc();
            $thoi_gian_het_han = strtotime($thong_tin_han['thoi_gian_het_han']);
            $thoi_gian_hien_tai = time();
            
            if ($thoi_gian_hien_tai > $thoi_gian_het_han) {
                $thong_bao = 'Lịch hẹn đã hết hạn thanh toán. Vui lòng đặt lịch mới.';
                $loai_thong_bao = 'loi';
                $thong_tin_lich_hen = null;
            }
        }
    } else {
        $thong_bao = 'Không tìm thấy lịch hẹn hoặc lịch hẹn đã được xử lý.';
        $loai_thong_bao = 'loi';
    }
}

// Xử lý thanh toán
if (isset($_POST['btn_thanh_toan']) && $thong_tin_lich_hen) {
    $phuong_thuc = $_POST['phuong_thuc_thanh_toan'];
    $so_tien = 500000; // Giá cố định hoặc lấy từ database
    
    if ($xu_ly_thanh_toan->xu_ly_thanh_toan_thanh_cong($ma_lich_hen, $phuong_thuc, $so_tien)) {
        $thong_bao = 'Thanh toán thành công! Lịch hẹn của bạn đã được xác nhận.';
        $loai_thong_bao = 'thanh_cong';
        $thong_tin_lich_hen['trangthai'] = 'Đã thanh toán';
    } else {
        $thong_bao = 'Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.';
        $loai_thong_bao = 'loi';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Lịch Xét Nghiệm - Bệnh Viện Hạnh Phúc</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .thong-tin-lich { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2563eb; }
        .phuong-thuc-thanh-toan { margin: 20px 0; }
        .phuong-thuc-item { display: flex; align-items: center; padding: 15px; border: 2px solid #e2e8f0; border-radius: 8px; margin: 10px 0; cursor: pointer; transition: all 0.3s; }
        .phuong-thuc-item:hover { border-color: #2563eb; background: #f8fafc; }
        .phuong-thuc-item input[type="radio"] { margin-right: 15px; }
        .btn-thanh-toan { background: #16a34a; color: white; padding: 15px 30px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 20px; }
        .btn-thanh-toan:hover { background: #15803d; }
        .alert { padding: 15px; border-radius: 8px; margin: 20px 0; }
        .alert.thanh_cong { background: #dcfce7; border: 1px solid #16a34a; color: #15803d; }
        .alert.loi { background: #fef2f2; border: 1px solid #ef4444; color: #dc2626; }
        .dem-nguoc { background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .thoi-gian-con-lai { font-size: 24px; font-weight: bold; color: #f59e0b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Bệnh Viện Hạnh Phúc</h1>
            <p>Thanh Toán Lịch Xét Nghiệm</p>
        </div>
        
        <div class="content">
            <?php if (!empty($thong_bao)): ?>
            <div class="alert <?php echo $loai_thong_bao; ?>">
                <?php echo $thong_bao; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($thong_tin_lich_hen && $thong_tin_lich_hen['trangthai'] !== 'Đã thanh toán'): ?>
            
            <div class="dem-nguoc">
                <p><strong>⏰ Thời gian còn lại để thanh toán:</strong></p>
                <div class="thoi-gian-con-lai" id="dem-nguoc-timer">Đang tính toán...</div>
            </div>
            
            <div class="thong-tin-lich">
                <h3>📋 Thông Tin Lịch Hẹn</h3>
                <p><strong>Mã lịch hẹn:</strong> #<?php echo $thong_tin_lich_hen['malichhen']; ?></p>
                <p><strong>Bệnh nhân:</strong> <?php echo $thong_tin_lich_hen['hoten']; ?></p>
                <p><strong>Loại xét nghiệm:</strong> <?php echo $thong_tin_lich_hen['tenloaixetnghiem']; ?></p>
                <p><strong>Ngày hẹn:</strong> <?php echo date('d/m/Y', strtotime($thong_tin_lich_hen['ngayhen'])); ?></p>
                <p><strong>Giờ hẹn:</strong> <?php echo $thong_tin_lich_hen['giobatdau']; ?></p>
                <p><strong>Số tiền:</strong> <span style="color: #16a34a; font-weight: bold;">500.000 VNĐ</span></p>
            </div>
            
            <form method="post" id="form-thanh-toan">
                <h3>💳 Chọn Phương Thức Thanh Toán</h3>
                <div class="phuong-thuc-thanh-toan">
                    <label class="phuong-thuc-item">
                        <input type="radio" name="phuong_thuc_thanh_toan" value="Thẻ tín dụng" required>
                        <div>
                            <strong>💳 Thẻ tín dụng/Ghi nợ</strong><br>
                            <small>Visa, MasterCard, JCB</small>
                        </div>
                    </label>
                    
                    <label class="phuong-thuc-item">
                        <input type="radio" name="phuong_thuc_thanh_toan" value="Ví điện tử" required>
                        <div>
                            <strong>📱 Ví điện tử</strong><br>
                            <small>MoMo, ZaloPay, VNPay</small>
                        </div>
                    </label>
                    
                    <label class="phuong-thuc-item">
                        <input type="radio" name="phuong_thuc_thanh_toan" value="Chuyển khoản" required>
                        <div>
                            <strong>🏦 Chuyển khoản ngân hàng</strong><br>
                            <small>Internet Banking, Mobile Banking</small>
                        </div>
                    </label>
                </div>
                
                <button type="submit" name="btn_thanh_toan" class="btn-thanh-toan">
                    💰 THANH TOÁN 500.000 VNĐ
                </button>
            </form>
            
            <?php elseif ($thong_tin_lich_hen && $thong_tin_lich_hen['trangthai'] === 'Đã thanh toán'): ?>
            
            <div class="alert thanh_cong">
                <h3>✅ Thanh toán thành công!</h3>
                <p>Lịch hẹn của bạn đã được xác nhận. Vui lòng đến đúng giờ hẹn.</p>
            </div>
            
            <div class="thong-tin-lich">
                <h3>📋 Thông Tin Lịch Hẹn Đã Xác Nhận</h3>
                <p><strong>Mã lịch hẹn:</strong> #<?php echo $thong_tin_lich_hen['malichhen']; ?></p>
                <p><strong>Bệnh nhân:</strong> <?php echo $thong_tin_lich_hen['hoten']; ?></p>
                <p><strong>Loại xét nghiệm:</strong> <?php echo $thong_tin_lich_hen['tenloaixetnghiem']; ?></p>
                <p><strong>Ngày hẹn:</strong> <?php echo date('d/m/Y', strtotime($thong_tin_lich_hen['ngayhen'])); ?></p>
                <p><strong>Giờ hẹn:</strong> <?php echo $thong_tin_lich_hen['giobatdau']; ?></p>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Đếm ngược thời gian thanh toán
        <?php if ($thong_tin_lich_hen && $thong_tin_lich_hen['trangthai'] !== 'Đã thanh toán'): ?>
        <?php
        $sql_lay_han = "SELECT thoi_gian_het_han FROM email_thanh_toan WHERE ma_lich_hen = ?";
        $stmt_lay_han = $conn->prepare($sql_lay_han);
        $stmt_lay_han->bind_param("i", $ma_lich_hen);
        $stmt_lay_han->execute();
        $ket_qua_lay_han = $stmt_lay_han->get_result();
        $thong_tin_han = $ket_qua_lay_han->fetch_assoc();
        ?>
        
        const thoi_gian_het_han = new Date("<?php echo $thong_tin_han['thoi_gian_het_han']; ?>").getTime();
        
        const cap_nhat_dem_nguoc = setInterval(function() {
            const thoi_gian_hien_tai = new Date().getTime();
            const thoi_gian_con_lai = thoi_gian_het_han - thoi_gian_hien_tai;
            
            if (thoi_gian_con_lai > 0) {
                const phut = Math.floor((thoi_gian_con_lai % (1000 * 60 * 60)) / (1000 * 60));
                const giay = Math.floor((thoi_gian_con_lai % (1000 * 60)) / 1000);
                
                document.getElementById("dem-nguoc-timer").innerHTML = 
                    phut.toString().padStart(2, '0') + ":" + giay.toString().padStart(2, '0');
            } else {
                document.getElementById("dem-nguoc-timer").innerHTML = "HẾT HẠN";
                document.getElementById("form-thanh-toan").style.display = "none";
                clearInterval(cap_nhat_dem_nguoc);
                
                // Tự động reload trang sau 3 giây
                setTimeout(function() {
                    location.reload();
                }, 3000);
            }
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
