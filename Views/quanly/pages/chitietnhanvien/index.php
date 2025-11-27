<?php
include_once("Controllers/cnhanvien.php");
include_once("Assets/config.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p>Không có nhân viên.</p>";
    exit;
}

$id = $_GET['id'];
$cNhanVien = new cNhanVien();
$tbl = $cNhanVien->getNhanVienById($id);

if ($tbl === -1) {
    echo "<p>Lỗi kết nối CSDL.</p>";
    exit;
} elseif ($tbl === 0) {
    echo "<p>Nhân viên không tồn tại.</p>";
    exit;
}

$nv = $tbl->fetch_assoc();
?>

<style>
    /* Card chính với shadow hiện đại */
    .card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 30px;
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    /* Header section với avatar */
    .header-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #ecf0f1;
    }

    .avatar-box {
        flex-shrink: 0;
    }

    .avatar-box img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
    }

    .info-box h3 {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .info-box p {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #7f8c8d;
    }

    .info-box i {
        color: #3b82f6;
        font-size: 16px;
    }

    /* Layout 2 cột cho thông tin */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 20px;
    }

    .info-section h5 {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table tr {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #ecf0f1;
    }

    .info-table tr:last-child {
        border-bottom: none;
    }

    .info-table th {
        flex: 0 0 40%;
        font-weight: 600;
        color: #3b82f6;
        text-align: left;
        font-size: 14px;
    }

    .info-table td {
        flex: 1;
        color: #2c3e50;
        font-size: 14px;
        word-break: break-word;
    }

    /* Button styling */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-secondary {
        background: #ecf0f1;
        color: #2c3e50;
    }

    .btn-secondary:hover {
        background: #bdc3c7;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn i {
        font-size: 16px;
    }

    /* Responsive cho mobile */
    @media (max-width: 768px) {
        .main-container {
            padding: 15px;
        }

        .card {
            padding: 20px;
        }

        .header-section {
            flex-direction: column;
            text-align: center;
        }

        .avatar-box img {
            width: 80px;
            height: 80px;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .info-table th {
            flex: 0 0 50%;
        }

        .info-table td {
            flex: 1;
        }
    }
</style>

<div class="main-container">
    <!-- Nút quay lại -->
    <a href="?action=nhanvien&tab=nhanvien" class="btn btn-secondary" style="margin-top: 20px;">
            <i class="bi bi-arrow-left"></i> Quay lại
    </a>
    <div class="card">
        <!-- Header với avatar -->
        <div class="header-section">
            <div class="avatar-box">
                <img src="Assets/img/<?= htmlspecialchars($nv['imgnv'] ?? 'default.png') ?>" alt="Avatar <?= htmlspecialchars($nv['hoten']) ?>">
            </div>
            <div class="info-box">
                <h3><?= htmlspecialchars($nv['hoten']) ?></h3>
                <p><i class="bi bi-person-badge"></i> <?= htmlspecialchars($nv['chucvu']) ?></p>
            </div>
        </div>

        <!-- Thông tin chi tiết -->
        <div class="info-grid">
            <!-- Thông tin cá nhân -->
            <div class="info-section">
                <h5>Thông tin cá nhân</h5>
                <table class="info-table">
                    <tr>
                        <th>Ngày sinh:</th>
                        <td><?= htmlspecialchars($nv['ngaysinh']) ?></td>
                    </tr>
                    <tr>
                        <th>Giới tính:</th>
                        <td><?= htmlspecialchars($nv['gioitinh']) ?></td>
                    </tr>
                    <tr>
                        <th>Dân tộc:</th>
                        <td><?= htmlspecialchars($nv['dantoc']) ?></td>
                    </tr>
                    <tr>
                        <th>CCCD:</th>
                        <td><?= htmlspecialchars(decryptData($nv['cccd'])) ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td><?= htmlspecialchars($nv['sonha']) ?>, <?= htmlspecialchars($nv['tenxaphuong']) ?>, <?= htmlspecialchars($nv['tentinhthanhpho']) ?></td>
                    </tr>
                    <tr>
                        <th>Email cá nhân:</th>
                        <td><?= htmlspecialchars($nv['emailcanhan']) ?></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại:</th>
                        <td><?= htmlspecialchars(decryptData($nv['sdt'])) ?></td>
                    </tr>
                    <tr>
                        <th>Email TK:</th>
                        <td><?= htmlspecialchars(decryptData($nv['email'])) ?></td>
                    </tr>
                </table>
            </div>

            <!-- Thông tin công việc -->
            <div class="info-section">
                <h5>Thông tin công việc</h5>
                <table class="info-table">
                    <tr>
                        <th>Ngày bắt đầu:</th>
                        <td><?= htmlspecialchars($nv['ngaybatdau']) ?></td>
                    </tr>
                    <tr>
                        <th>Ngày kết thúc:</th>
                        <td><?= htmlspecialchars($nv['ngayketthuc']) ?></td>
                    </tr>
                    <tr>
                        <th>Chức vụ:</th>
                        <td><?= htmlspecialchars($nv['chucvu']) ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái tài khoản:</th>
                        <td><?= htmlspecialchars($nv['tentrangthai']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>

