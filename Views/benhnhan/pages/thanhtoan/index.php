<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán viện phí</title>
    <link rel="stylesheet" href="Views/benhnhan/pages/thanhtoan/style.css">
</head>
<?php
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/cchuyengia.php');
    include_once('Controllers/cBenhNhan.php');
    include_once('Controllers/clichkham.php');
    include_once('Controllers/cphieukhambenh.php'); 
    include_once('Controllers/clichlamviec.php'); 
    $pPhieuKham = new cPhieuKhambenh();
    $pBacSi = new cBacSi();
    $pChuyenGia = new cChuyenGia();
    if(isset($_SESSION['mabacsi'])){
       $tblBacSi = $pBacSi->getBacSiById($_SESSION['mabacsi'])->fetch_assoc();
    }
    elseif(isset($_SESSION['machuyengia'])){
        $tblBacSi = $pChuyenGia->getChuyenGiaById($_SESSION['machuyengia'])->fetch_assoc();

    }
    $pLichKham = new cLichKham();
    $pBenhNhan = new cBenhNhan();
    $tblLich = $pLichKham->getlich($_SESSION['makhunggiokb'])->fetch_assoc();
    $benhnhan = $pBenhNhan->getbenhnhanbyid($_SESSION['mabenhnhan']);
    echo $_SESSION['maphieukhambenh'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'], $_SESSION['mabenhnhan'], $_SESSION['matrangthai'];
    if(isset($_POST['btnxacnhan'])){
        if(isset($_SESSION['mabacsi'])){
            $result = $pPhieuKham->insertphieukham($_SESSION['maphieukhambenh'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'], $_SESSION['mabacsi'], $_SESSION['mabenhnhan'], $_SESSION['matrangthai']);
        }
        elseif(isset($_SESSION['machuyengia'])){
            $result = $pPhieuKham->insertphieukham($_SESSION['maphieukhambenh'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'], $_SESSION['machuyengia'], $_SESSION['mabenhnhan'], $_SESSION['matrangthai']);
        }
        if ($result) {
            echo '<script>alert("Đặt lịch khám thành công!!!"); location.href="?action=lichhen";</script>';
        } else {
            echo '<div class="text-danger text-center">Đặt lịch khám thất bại. Vui lòng thử lại.</div>';
        }
    }
?>

 <div class="container">
    
    <div class="payment-info">
        <div class="info-section">
            <div class="info-item">
                <label>Mã bệnh nhân:</label>
                <span><?php echo $_SESSION['mabenhnhan'];?></span>
            </div>
            <div class="info-item">
                <label>Họ và tên:</label>
                <span><?php echo $benhnhan['hoten'];?></span>
            </div>
            <div class="info-item">
                <label>Bác sĩ:</label>
                <span><?php echo $tblBacSi['hoten'];?></span>
            </div>
            <div class="info-item">
                <label>Ngày khám</label>
                <span><?php echo $_SESSION['ngaykham'];?></span>
            </div>
            <div class="info-item">
                <label>Giờ khám</label>
                <span><?php echo $tblLich['giobatdau']."-".$tblLich['gioketthuc'];?></span>
            </div>
            <div class="info-item">
                <label>Tổng tiền:</label>
                <span class="total"><?php echo number_format($_SESSION['tongtien'], 0, ',', '.') . ' VND';?></span>
            </div>
        </div>
        <form action="" method="post">
            <div class="qr-section">
                <h2>Quét mã QR để thanh toán</h2>
                <div class="qr-code">
                    <!-- Thay thế src bằng link ảnh QR thực tế -->
                    <img src="https://img.vietqr.io/image/VCB-9355706358-compact.png?amount=<?php echo $_SESSION['tongtien'];?>
                    &addInfo=<?php echo $_SESSION['maphieukhambenh'];?>
                    &accountName=benhvienhanhphuc" alt="QR Code thanh toán" style="width: 100%;">
                </div>
                <p>Hoặc chuyển khoản đến:</p>
                <p>Ngân hàng: VCB</p>
                <p>STK: 9355706358</p>
                <p>Chủ TK: BỆNH VIỆN HẠNH PHÚC</p>
                <button type="submit" class="button" name ="btnxacnhan">Xác nhận đã thanh toán</button>
            </div>
        </form>
    </div>
</div>
<script>
window.addEventListener("beforeunload", function () {
    // Gọi PHP để xóa session thanh toán khi rời khỏi trang
    navigator.sendBeacon("Views/benhnhan/pages/thanhtoan/xoasession.php");
});
</script>
