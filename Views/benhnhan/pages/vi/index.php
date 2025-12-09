<?php
include_once('Controllers/ctaikhoan.php');

// Kiểm tra đăng nhập
if(!isset($_SESSION["dangnhap"]) || !isset($_SESSION["user"])){
    header("Location:index.php?action=dangnhap");
    exit();
}

$cTaiKhoan = new ctaiKhoan();
$tentk = $_SESSION["user"]["tentk"];
$soDuVi = $cTaiKhoan->getSoDuVi($tentk);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ví Hạnh Phúc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wallet-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            margin-top: 80px;
        }

        .wallet-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .wallet-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .wallet-header .wallet-icon {
            font-size: 32px;
        }

        .balance-display {
            background: rgba(255,255,255,0.2);
            padding: 30px;
            margin: 20px 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .balance-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .balance-amount {
            font-size: 48px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .balance-currency {
            font-size: 24px;
            margin-left: 5px;
            opacity: 0.9;
        }

        .wallet-info {
            padding: 30px;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .info-item i {
            font-size: 24px;
            color: #667eea;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }

        .info-item .info-text {
            flex: 1;
        }

        .info-item .info-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .info-item .info-value {
            font-size: 16px;
            color: #212529;
            font-weight: 500;
        }

        .action-buttons {
            padding: 0 30px 30px 30px;
            display: flex;
            gap: 15px;
        }

        .btn {
            flex: 1;
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        .wallet-note {
            padding: 20px 30px 30px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .wallet-container {
                margin-top: 100px;
            }

            .balance-amount {
                font-size: 36px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="wallet-container">
        <div class="wallet-header">
            <h1>
                <i class="fas fa-wallet wallet-icon"></i>
                Ví Hạnh Phúc
            </h1>
            
            <div class="balance-display">
                <div class="balance-label">Số dư hiện tại</div>
                <div class="balance-amount">
                    <?php echo number_format($soDuVi, 0, ',', '.'); ?>
                    <span class="balance-currency">đ</span>
                </div>
            </div>
        </div>

        <div class="wallet-info">
            <div class="info-item">
                <i class="fas fa-user-circle"></i>
                <div class="info-text">
                    <div class="info-label">Tài khoản</div>
                    <div class="info-value"><?php echo isset($_SESSION['user']['tentk']) ? $_SESSION['user']['tentk'] : 'N/A'; ?></div>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-shield-alt"></i>
                <div class="info-text">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">Hoạt động</div>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-credit-card"></i>
                <div class="info-text">
                    <div class="info-label">Phương thức thanh toán</div>
                    <div class="info-value">Ví điện tử</div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-back" onclick="window.location.href='?action=trangchu'">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </button>
        </div>

        <div class="wallet-note">
            <i class="fas fa-info-circle"></i>
            Số dư ví được sử dụng để thanh toán các dịch vụ khám chữa bệnh tại Bệnh viện Hạnh Phúc
        </div>
    </div>
</body>
</html>
