<?php
include_once("../Controllers/clanviec.php");
include_once("../Controllers/cnhanvien.php");

$clv = new cLanViec();
$dsCa = $clv->getAllCaLam(); // Hàm lấy danh sách ca làm việc

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phân ca</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="../public/js/phanca.js" defer></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-phan-ca {
            background-color: #4CAF50;
            color: white;
        }
        /* popup */
        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup {
            background: #fff;
            border-radius: 10px;
            width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            padding: 20px;
            position: relative;
        }
        .popup h3 { margin-bottom: 15px; }
        .close-btn {
            position: absolute;
            right: 10px; top: 10px;
            cursor: pointer;
            font-size: 18px;
            color: red;
        }
    </style>
</head>
<body>

<h2>Quản lý ca làm việc</h2>
<table>
    <thead>
        <tr>
            <th>Mã ca</th>
            <th>Tên ca</th>
            <th>Giờ vào</th>
            <th>Giờ ra</th>
            <th>Số lượng NV</th>
            <th>Tùy chọn</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dsCa as $ca): ?>
        <tr>
            <td><?= $ca['ma_ca'] ?></td>
            <td><?= $ca['ten_ca'] ?></td>
            <td><?= $ca['gio_vao'] ?></td>
            <td><?= $ca['gio_ra'] ?></td>
            <td><?= $ca['so_nv'] ?></td>
            <td><button class="btn btn-phan-ca" onclick="openPhanCaPopup('<?= $ca['ma_ca'] ?>')">Phân ca</button></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Popup phân ca -->
<div class="overlay" id="phanCaPopup">
    <div class="popup">
        <span class="close-btn" onclick="closePhanCaPopup()">&times;</span>
        <h3>Phân ca làm việc</h3>

        <form id="formPhanCa">
            <input type="hidden" id="maCa" name="maCa">

            <label>Chức vụ:</label>
            <select id="chucVu" name="chucVu" onchange="loadNhanVien()">
                <option value="">-- Chọn chức vụ --</option>
                <option value="Thu ngân">Thu ngân</option>
                <option value="Phục vụ">Phục vụ</option>
                <option value="Bếp">Bếp</option>
            </select>

            <label>Loại nhân viên:</label>
            <select id="loaiNV" name="loaiNV" onchange="loadNhanVien()">
                <option value="chua">Nhân viên chưa phân ca</option>
                <option value="co">Nhân viên đã có ca</option>
            </select>

            <div id="dsNhanVien" style="margin-top:10px;">
                <!-- Danh sách nhân viên sẽ hiển thị tại đây -->
            </div>

            <button type="button" class="btn btn-phan-ca" onclick="xacNhanPhanCa()">Xác nhận phân ca</button>
        </form>
    </div>
</div>

<!-- Popup xác nhận -->
<div class="overlay" id="xacNhanPopup">
    <div class="popup">
        <span class="close-btn" onclick="closeXacNhanPopup()">&times;</span>
        <h3>Xác nhận loại làm việc</h3>
        <p>Bạn muốn nhân viên làm Online hay Offline?</p>
        <button class="btn" style="background:#2196F3; color:#fff;" onclick="luuLich('online')">Làm Online</button>
        <button class="btn" style="background:#FF9800; color:#fff;" onclick="luuLich('offline')">Làm Offline</button>
    </div>
</div>

</body>
</html>
