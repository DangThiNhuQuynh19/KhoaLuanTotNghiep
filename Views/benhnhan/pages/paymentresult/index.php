<?php
$vnp_HashSecret = "08QNEGVZXSFGER8MD7Y0MAGK24DAT6KW";

// Nhận giá trị từ URL trả về của VNPAY
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? null;
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? null;
$vnp_Amount = $_GET['vnp_Amount'] ?? 0;
$vnp_OrderInfo = $_GET['vnp_OrderInfo'] ?? '';
$vnp_PayDate = $_GET['vnp_PayDate'] ?? '';

// Kiểm tra mã bảo mật
$inputData = array();
foreach ($_GET as $key => $value) {
    if (strpos($key, 'vnp_') === 0) {
        $inputData[$key] = $value;
    }
}
unset($inputData['vnp_SecureHash']);
ksort($inputData);

$hashdata = "";
$i = 0;

foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

$secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

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

// Biến trạng thái
$isSuccess = false;
$errorMessage = '';

// Kiểm tra SecureHash và ResponseCode
if ($secureHash === $vnp_SecureHash) {
    if ($vnp_ResponseCode == '00') {
        // Kiểm tra nếu có các giá trị trong session
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
            $isSuccess = true;
        } else {
            $errorMessage = 'Đặt lịch khám thất bại. Vui lòng thử lại.';
        }
    } else {
        $errorMessage = 'Thanh toán không thành công. Mã lỗi: ' . $vnp_ResponseCode;
    }
} else {
    $errorMessage = 'Lỗi bảo mật! Không thể xác minh giao dịch.';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isSuccess ? 'Thanh toán thành công' : 'Thanh toán thất bại'; ?></title>
    <style>
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 40px 30px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-wrapper {
            margin-bottom: 24px;
        }

        .icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 40px;
            animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .icon.success {
            background: #d4f8e8;
            color: #10b981;
        }

        .icon.error {
            background: #fde2e4;
            color: #ef4444;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #111;
        }

        .success h1 {
            color: #10b981;
        }

        .error h1 {
            color: #ef4444;
        }

        p {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .details {
            background: #f9f9f9;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            text-align: left;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 14px;
            border-bottom: 1px solid #e5e5e5;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #111;
            font-weight: 600;
        }

        .success .detail-value {
            color: #10b981;
        }

        .error .detail-value {
            color: #ef4444;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        button {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: #e5e5e5;
            color: #111;
        }

        .btn-secondary:hover {
            background: #d4d4d4;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .success .button-group .btn-primary {
            background: #10b981;
        }

        .error .button-group .btn-primary {
            background: #3b82f6;
        }

        .countdown {
            font-size: 14px;
            color: #999;
            margin-top: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-error {
            background: #fee2e2;
            color: #7f1d1d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card <?php echo $isSuccess ? 'success' : 'error'; ?>">
            <?php if ($isSuccess): ?>
                <div class="status-badge badge-success">Giao dịch thành công</div>
                
                <div class="icon-wrapper">
                    <div class="icon success">✓</div>
                </div>

                <h1>Thanh toán thành công!</h1>
                <p>Lịch khám của bạn đã được xác nhận. Chúng tôi sẽ gửi thông tin chi tiết tới email của bạn.</p>

                <div class="details">
                    <div class="detail-item">
                        <span class="detail-label">Dịch vụ:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($vnp_OrderInfo); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Số tiền:</span>
                        <span class="detail-value"><?php echo number_format($vnp_Amount / 100, 0, ',', '.'); ?> VND</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Ngày thanh toán:</span>
                        <span class="detail-value"><?php echo substr($vnp_PayDate, 0, 4) . '-' . substr($vnp_PayDate, 4, 2) . '-' . substr($vnp_PayDate, 6, 2); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Mã giao dịch:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($_GET['vnp_TxnRef'] ?? 'N/A'); ?></span>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn-primary" onclick="window.location.href='?action=lichhen'">Xem lịch khám</button>
                </div>

                <div class="countdown">
                    Bạn sẽ được chuyển hướng trong <span id="countdown">5</span> giây...
                </div>

            <?php else: ?>
                <div class="status-badge badge-error">Giao dịch thất bại</div>
                
                <div class="icon-wrapper">
                    <div class="icon error">✕</div>
                </div>

                <h1>Thanh toán thất bại</h1>
                <p><?php echo htmlspecialchars($errorMessage); ?></p>

                <div class="details">
                    <div class="detail-item">
                        <span class="detail-label">Dịch vụ:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($vnp_OrderInfo); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Số tiền:</span>
                        <span class="detail-value"><?php echo number_format($vnp_Amount / 100, 0, ',', '.'); ?> VND</span>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn-primary" onclick="window.location.href='?action=thanhtoan'">Thử lại</button>
                    <button class="btn-secondary" onclick="window.location.href='?action=lichhen'">Quay lại</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if ($isSuccess): ?>
        // Countdown for redirect
        let countdown = 5;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownEl.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '?action=lichhen';
            }
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>

