<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI';}
body{background:#f8f6fc;color:#333;}
.container{max-width:1000px;margin:40px auto;padding:20px;}
.payment-info{background:#fff;border-radius:15px;padding:30px;box-shadow:0 8px 20px rgba(0,0,0,0.08);}
.payment-info h2{text-align:center;color:#6f42c1;margin-bottom:25px;font-size:1.8rem;}
.info-section .info-item{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px dashed #ddd;}
.total{color:#c2185b;font-weight:bold;font-size:1.3rem;}
.button{padding:12px 24px;border-radius:50px;color:white;font-weight:600;cursor:pointer;margin:10px;display:inline-block;border:none;background:linear-gradient(45deg,#6f42c1,#9c27b0);}
.button.secondary{background:linear-gradient(45deg,#dc3545,#c82333);}
#cancelModal{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;justify-content:center;align-items:center;z-index:9999;transition:all 0.3s;}
#cancelModal.show{display:flex;}
#cancelModal .box{background:#fff;padding:25px;border-radius:12px;text-align:center;width:380px;box-shadow:0 5px 15px rgba(0,0,0,0.3);}
#cancelModal h3{margin-bottom:15px;color:#c2185b;}
#cancelModal p{margin-bottom:20px;color:#555;}
.modal-btn{padding:10px 18px;margin:5px;border:none;font-weight:600;border-radius:8px;cursor:pointer;transition:0.2s;}
.modal-cancel{background:#e0e0e0;}
.modal-cancel:hover{background:#d5d5d5;}
.modal-exit{background:#c2185b;color:white;}
.modal-exit:hover{background:#a00037;}
#insufficientModal{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;justify-content:center;align-items:center;z-index:9999;transition:all 0.3s;}
#insufficientModal.show{display:flex;}
#insufficientModal .box{background:#fff;padding:25px;border-radius:12px;text-align:center;width:380px;box-shadow:0 5px 15px rgba(0,0,0,0.3);}
#insufficientModal h3{margin-bottom:15px;color:#c2185b;}
#insufficientModal p{margin-bottom:20px;color:#555;}
.modal-btn{padding:10px 18px;margin:5px;border:none;font-weight:600;border-radius:8px;cursor:pointer;transition:0.2s;}
.modal-exit{background:#6f42c1;color:white;}
.modal-exit:hover{background:#4b238f;}
.success-overlay {
    position: fixed;
    inset: 0;
 /* background: rgba(0,0,0,0.4); */
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.success-popup {
    background: #fff;
    padding: 25px 35px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    animation: fadeInScale 0.4s ease;
}

.check-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 15px;
    background: #28a745;
    color: white;
    border-radius: 50%;
    font-size: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes fadeInScale {
    0% { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
<div class="success-overlay" id="successOverlay">
    <div class="success-popup">
        <div class="check-icon">✔</div>
        <h3>Đặt lịch thành công!</h3>
    </div>
</div>
<?php
include_once('Controllers/cbacsi.php');
include_once('Controllers/cchuyengia.php');
include_once('Controllers/cbenhnhan.php');
include_once('Controllers/clichkham.php');
include_once('Controllers/cphieukhambenh.php');

$pPhieuKham  = new cPhieuKhambenh();
$pBacSi      = new cBacSi();
$pChuyenGia  = new cChuyenGia();
$pLichKham   = new cLichKham();
$pBenhNhan   = new cBenhNhan();

// Lấy thông tin bác sĩ hoặc chuyên gia
$tblBacSi = null;
$idbs = null;
if (isset($_SESSION['mabacsi'])) {
    $tblBacSi = $pBacSi->getBacSiById($_SESSION['mabacsi'])->fetch_assoc();
    $idbs = $_SESSION['mabacsi'];
} elseif (isset($_SESSION['machuyengia'])) {
    $tblBacSi = $pChuyenGia->getChuyenGiaById($_SESSION['machuyengia'])->fetch_assoc();
    $idbs = $_SESSION['machuyengia'];
}

// Lấy thông tin lịch khám
$tblLich = $pLichKham->getlich($_SESSION['makhunggiokb'], $_SESSION['ngaykham'], $idbs);

$giokham = "";
$thongtin = "";
if (is_array($tblLich) && count($tblLich) > 0) {
    $giokham  = $tblLich[0]['giokham'];
    $thongtin = $tblLich[0]['thongtin'];
}

// Thông tin bệnh nhân
$benhnhan = $pBenhNhan->getbenhnhanbyid($_SESSION['mabenhnhan']);
$chutaikhoan =$pBenhNhan->getBenhNhanChinhByTK($_SESSION['user']['tentk']);
// Nút VNPay
if (isset($_POST['btnvnpay'])) {
    echo "<script>sessionStorage.setItem('noUnload', '1'); window.location.href='?action=vnpay';</script>";
    exit();
}

// Nút Ví
if (isset($_POST['btnvi'])) {
    if ($chutaikhoan['vitien'] < $_SESSION['tongtien']) {
        $showInsufficient = true;
    } else {
        $showInsufficient = false;
        if (isset($_SESSION['mabacsi'])) {
            $result1 = $pPhieuKham->insertphieukham(
                $_SESSION['maphieukhambenh'],
                $_SESSION['ngaykham'],
                $_SESSION['makhunggiokb'],
                $_SESSION['mabacsi'],
                $_SESSION['mabenhnhan'],
                $_SESSION['matrangthai']
            );
            $result2 =$pBenhNhan->update_vitien_id($_SESSION['user']['tentk'], $_SESSION['tongtien']);
        } elseif (isset($_SESSION['machuyengia'])) {
            $result1 = $pPhieuKham->insertphieukham(
                $_SESSION['maphieukhambenh'],
                $_SESSION['ngaykham'],
                $_SESSION['makhunggiokb'],
                $_SESSION['machuyengia'],
                $_SESSION['mabenhnhan'],
                $_SESSION['matrangthai']
            );
            $result2 =$pBenhNhan->update_vitien_id($_SESSION['user']['tentk'], $_SESSION['tongtien']);
        }

        if ($result1 && $result2) {
            if ($result1 && $result2) {
                echo '<script>
                    window.onload = function() {
                        const overlay = document.getElementById("successOverlay");
                        overlay.style.display = "flex";
            
                        setTimeout(() => {
                            window.location.href = "?action=lichhen";
                        }, 2000);
                    }
                </script>';
                exit();
            }
        } else {
            echo '<div class="text-danger text-center">Đặt lịch khám thất bại. Vui lòng thử lại.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thanh toán phí tư vấn</title>
</head>
<body>
<div class="container">
    <div class="payment-info">
        <h2>Thông tin thanh toán</h2>

        <div class="info-section">
            <div class="info-item"><label>Mã bệnh nhân:</label> <span><?= $_SESSION['mabenhnhan']; ?></span></div>
            <div class="info-item"><label>Họ và tên:</label> <span><?= $benhnhan['hoten']; ?></span></div>
            <div class="info-item"><label>Bác sĩ:</label> <span><?= $tblBacSi['hoten']; ?></span></div>
            <div class="info-item"><label>Ngày khám:</label> <span><?= $_SESSION['ngaykham']; ?></span></div>
            <div class="info-item"><label>Giờ khám:</label> <span><?= $giokham; ?></span></div>
            <div class="info-item"><label>Thông tin:</label> <span><?= $thongtin; ?></span></div>
            <div class="info-item"><label>Tổng tiền:</label><span class="total"><?= number_format($_SESSION['tongtien'], 0, ',', '.'); ?> VND</span></div>
        </div>

        <form method="POST" id="paymentForm">
            <button class="button" name="btnvnpay" id="btnvnpay">VNPay</button>
            <button class="button" name="btnvi" id="btnvi">Ví Hạnh Phúc</button>
            <button type="button" class="button secondary" id="btnhuy">Hủy</button>
        </form>
    </div>
</div>

<!-- Popup xác nhận hủy -->
<div id="cancelModal">
    <div class="box">
        <h3>Xác nhận hủy thanh toán</h3>
        <p>Bạn có chắc muốn hủy thanh toán? Thao tác này sẽ xóa thông tin hiện tại.</p>
        <button class="modal-btn modal-cancel" id="stayBtn">Ở lại</button>
        <button class="modal-btn modal-exit" id="confirmCancelBtn">Xác nhận hủy</button>
    </div>
</div>

<!-- Modal thông báo số dư không đủ -->
<div id="insufficientModal" class="<?= $showInsufficient ? 'show' : '' ?>">
    <div class="box">
        <h3>Số dư ví không đủ</h3>
        <p>Số dư ví Hạnh Phúc không đủ thanh toán. Vui lòng chọn phương thức thanh toán khác.</p>
        <button class="modal-btn modal-exit" id="okBtn">OK</button>
    </div>
</div>
<script>
let allowLeave = false;

// VNPay/Ví
document.querySelector("#btnvnpay").addEventListener("click", function(){
    sessionStorage.setItem("noUnload", "1");
    allowLeave = true;
});
document.querySelector("#btnvi").addEventListener("click", function(){
    sessionStorage.setItem("noUnload", "1");
    allowLeave = true;
});

// Hiển thị modal xác nhận hủy
document.querySelector("#btnhuy").addEventListener("click", function(){
    document.querySelector("#cancelModal").classList.add("show");
});

// Nút ở lại → đóng modal
document.querySelector("#stayBtn").addEventListener("click", function(){
    document.querySelector("#cancelModal").classList.remove("show");
});

// Nút xác nhận hủy → fetch và redirect
document.querySelector("#confirmCancelBtn").addEventListener("click", function(){
    fetch('Views/benhnhan/pages/thanhtoan/xoasession.php')
        .then(() => {
            allowLeave = true;
            window.location.href = '?action=chitietbacsi&id=<?= isset($tblBacSi["mabacsi"]) ? $tblBacSi["mabacsi"] : $tblBacSi["machuyengia"]; ?>';
        });
});

document.getElementById("okBtn")?.addEventListener("click", function(){
    document.getElementById("insufficientModal").classList.remove("show");
});

// Cảnh báo rời trang
window.addEventListener("beforeunload", function (e) {
    if (allowLeave) return;
    if (sessionStorage.getItem("noUnload") === "1") return;
    e.preventDefault();
    e.returnValue = "";
});

// Bấm back của Chrome → xóa session
window.addEventListener("pageshow", function (e) {
    if (e.persisted) { 
        fetch("Views/benhnhan/pages/thanhtoan/xoasession.php");
    }
});
</script>

</body>
</html>

