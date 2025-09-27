<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán phí tư vấn</title>
    <style>
        /* Reset cơ bản */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f8f6fc;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .payment-info {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .payment-info h2 {
            text-align: center;
            color: #6f42c1;
            margin-bottom: 25px;
            font-size: 1.8rem;
            position: relative;
        }

        .payment-info h2::after {
            content: "";
            width: 60px;
            height: 3px;
            background: #6f42c1;
            display: block;
            margin: 10px auto 0;
            border-radius: 2px;
        }

        /* Bố cục ngang */
        .content {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .info-section {
            flex: 1;
            min-width: 300px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 1rem;
        }

        .info-item label {
            font-weight: 600;
            color: #444;
        }

        .info-item span {
            color: #555;
        }

        .total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #c2185b;
        }

        /* QR Section */
        .qr-section {
            flex: 1;
            min-width: 300px;
            text-align: center;
        }

        .qr-section h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #6f42c1;
        }

        .qr-code img {
            width: 220px;
            height: 220px;
            margin: 10px 0 20px;
            border: 5px solid #f1f1f1;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        /* Button */
        .button {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 15px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(45deg, #6f42c1, #9c27b0);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 6px 15px rgba(156, 39, 176, 0.3);
        }

        .button:hover {
            background: linear-gradient(45deg, #5a32a3, #7b1fa2);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<?php
    session_start();
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/cchuyengia.php');
    include_once('Controllers/cBenhNhan.php');
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

    // Xác nhận thanh toán
    if (isset($_POST['btnxacnhan'])) {
        if (isset($_SESSION['mabacsi'])) {
            $result = $pPhieuKham->insertphieukham(
                $_SESSION['maphieukhambenh'],
                $_SESSION['ngaykham'],
                $_SESSION['makhunggiokb'],
                $_SESSION['mabacsi'],
                $_SESSION['mabenhnhan'],
                $_SESSION['matrangthai']
            );
        } elseif (isset($_SESSION['machuyengia'])) {
            $result = $pPhieuKham->insertphieukham(
                $_SESSION['maphieukhambenh'],
                $_SESSION['ngaykham'],
                $_SESSION['makhunggiokb'],
                $_SESSION['machuyengia'],
                $_SESSION['mabenhnhan'],
                $_SESSION['matrangthai']
            );
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
        <h2>Thông tin thanh toán</h2>
        <div class="info-section">
            <div class="info-item">
                <label>Mã bệnh nhân:</label>
                <span><?php echo $_SESSION['mabenhnhan']; ?></span>
            </div>
            <div class="info-item">
                <label>Họ và tên:</label>
                <span><?php echo $benhnhan['hoten']; ?></span>
            </div>
            <div class="info-item">
                <label>Bác sĩ:</label>
                <span><?php echo $tblBacSi['hoten']; ?></span>
            </div>
            <div class="info-item">
                <label>Ngày khám:</label>
                <span><?php echo $_SESSION['ngaykham']; ?></span>
            </div>
            <div class="info-item">
                <label>Giờ khám:</label>
                <span><?php echo $giokham; ?></span>
            </div>
            <div class="info-item">
                <label>Thông tin:</label>
                <span><?php echo $thongtin; ?></span>
            </div>
            <div class="info-item">
                <label>Tổng tiền:</label>
                <span class="total"><?php echo number_format($_SESSION['tongtien'], 0, ',', '.') . ' VND'; ?></span>
            </div>
        </div>

        <form action="" method="post">
            <div class="qr-section">
                <h2>Quét mã QR để thanh toán</h2>
                <div class="qr-code">
                    <img src="https://img.vietqr.io/image/VCB-9355706358-compact.png?amount=<?php echo $_SESSION['tongtien']; ?>&addInfo=<?php echo $_SESSION['maphieukhambenh']; ?>&accountName=benhvienhanhphuc" alt="QR Code thanh toán">
                </div>
                <p>Hoặc chuyển khoản đến:</p>
                <p><b>Ngân hàng:</b> VCB</p>
                <p><b>Số TK:</b> 9355706358</p>
                <p><b>Chủ TK:</b> BỆNH VIỆN HẠNH PHÚC</p>
                <button type="submit" class="button" name="btnxacnhan">Xác nhận đã thanh toán</button>
            </div>
        </form>
    </div>
</div>

<script>
window.addEventListener("beforeunload", function () {
    navigator.sendBeacon("Views/benhnhan/pages/thanhtoan/xoasession.php");
});
</script>
</body>
</html>
