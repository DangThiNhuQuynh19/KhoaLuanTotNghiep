<?php
include_once('Controllers/cbacsi.php');
include_once('Controllers/cchuyenkhoa.php');
include_once('Controllers/ctinhthanhpho.php');
include_once('Controllers/cxaphuong.php');
include_once('Assets/config.php');

$cChuyenKhoa = new cChuyenKhoa();
$cthanhpho   = new cTinhThanhPho();
$cxaphuong   = new cXaPhuong();
$cBacSi      = new cBacSi();

$dsKhoa    = $cChuyenKhoa->getAllChuyenKhoa();
$tinh_list = $cthanhpho->get_tinhthanhpho();
$xa_list   = $cxaphuong->get_xaphuong();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $mabs = $cBacSi->generateDoctorCode();
} else {
    $mabs = $_POST['mabs'] ?? $cBacSi->generateDoctorCode();
}

$old = $_POST ?? [];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $res = $cBacSi->luuBacSi($_POST, $_FILES);

    if ($res == 1) {
        echo "<script>alert('Lưu bác sĩ thành công');window.location='?action=nhanvien&tab=bacsi';</script>";
        exit();
    } elseif ($res == -1) {
        $msg = '<div class="alert alert-warning text-center">Vui lòng nhập đầy đủ thông tin</div>';
    } else {
        $msg = '<div class="alert alert-danger text-center">Lưu bác sĩ thất bại</div>';
    }
}
?>
    <style>
        :root {
            --primary-color: #3498db;
            --primary-dark: #5a2e91;
            --primary-light: #ecf0f1;
            --text-primary: #2d3436;
            --text-secondary: #636e72;
            --border-color: #dfe6e9;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --spacing-unit: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            color: var(--text-primary);
        }


        /* Replaced Bootstrap with pure CSS Grid */
        .form-box {
            width: 100%;
            background: #ffffff;
            padding: calc(var(--spacing-unit) * 4);
            border-radius: 12px;
            border-top: 4px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============ TYPOGRAPHY ============ */
        .form-box h2 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: calc(var(--spacing-unit) * 3);
            text-align: center;
            letter-spacing: -0.5px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-top: calc(var(--spacing-unit) * 3);
            margin-bottom: calc(var(--spacing-unit) * 2);
            display: flex;
            align-items: center;
            gap: calc(var(--spacing-unit) * 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 2px;
        }

        label {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 14px;
            margin-bottom: calc(var(--spacing-unit) * 0.75);
            display: block;
        }

        /* ============ FORM CONTROLS ============ */
        .form-control,
        .form-select {
            width: 100%;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 1.5);
            font-size: 14px;
            line-height: 1.6;
            transition: all 0.3s ease;
            background-color: #ffffff;
            color: var(--text-primary);
            font-family: inherit;
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .form-control:hover,
        .form-select:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
            background-color: #ffffff;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        /* ============ FILE INPUT & PREVIEW ============ */
        .form-control[type="file"] {
            padding: calc(var(--spacing-unit) * 0.75);
            cursor: pointer;
        }

        .form-control[type="file"]::file-selector-button {
            padding: calc(var(--spacing-unit) * 0.75) calc(var(--spacing-unit) * 1.5);
            background-color: var(--primary-light);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: calc(var(--spacing-unit) * 1);
        }

        .form-control[type="file"]::file-selector-button:hover {
            background-color: var(--primary-color);
            color: white;
        }

        #preview {
            width: 120px;
            height: 140px;
            border-radius: 8px;
            margin-top: calc(var(--spacing-unit) * 1.5);
            display: none;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        }

        /* CSS Grid layout replacing Bootstrap grid */
        .row {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: calc(var(--spacing-unit) * 2.5);
            margin-bottom: var(--spacing-unit);
        }

        .col-md-2 { grid-column: span 2; }
        .col-md-3 { grid-column: span 3; }
        .col-md-4 { grid-column: span 4; }
        .col-md-6 { grid-column: span 6; }
        .col-md-12 { grid-column: span 12; }

        /* ============ DIVIDERS ============ */
        hr {
            margin: calc(var(--spacing-unit) * 4) 0;
            border: none;
            border-top: 1px solid var(--border-color);
            opacity: 0.6;
        }

        /* ============ BUTTONS ============ */
        .button-group {
            display: flex;
            gap: calc(var(--spacing-unit) * 2);
            justify-content: flex-end;
            margin-top: calc(var(--spacing-unit) * 5);
            padding-top: calc(var(--spacing-unit) * 3);
            border-top: 1px solid var(--border-color);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            padding: calc(var(--spacing-unit) * 1.25) calc(var(--spacing-unit) * 2.5);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 120px;
            text-align: center;
        }

        .btn-success {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .btn-success:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(52, 152, 219, 0.35);
        }

        .btn-success:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background-color: var(--border-color);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #d0d7de;
            border-color: #c5cdd2;
            transform: translateY(-2px);
        }

        /* ============ ALERTS ============ */
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: calc(var(--spacing-unit) * 2.5);
            padding: calc(var(--spacing-unit) * 1.5) calc(var(--spacing-unit) * 2);
            font-weight: 500;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }

        .text-center {
            text-align: center;
        }

        .alert-warning {
            background-color: #fff8e1;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }

        .alert-danger {
            background-color: #fadbd8;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        /* Responsive Grid adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: calc(var(--spacing-unit) * 2);
            }

            .form-box {
                padding: calc(var(--spacing-unit) * 2.5);
                border-radius: 10px;
            }

            .form-box h2 {
                font-size: 24px;
                margin-bottom: calc(var(--spacing-unit) * 2.5);
            }

            .section-title {
                font-size: 14px;
                margin-top: calc(var(--spacing-unit) * 2.5);
                margin-bottom: calc(var(--spacing-unit) * 1.5);
            }

            .row {
                grid-template-columns: repeat(6, 1fr);
                gap: calc(var(--spacing-unit) * 1.5);
            }

            .col-md-2 { grid-column: span 3; }
            .col-md-3 { grid-column: span 3; }
            .col-md-4 { grid-column: span 6; }
            .col-md-6 { grid-column: span 6; }
            .col-md-12 { grid-column: span 6; }

            .button-group {
                flex-direction: column;
                gap: calc(var(--spacing-unit) * 1.5);
                justify-content: stretch;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .main-container {
                padding: calc(var(--spacing-unit) * 1.5);
            }

            .form-box {
                padding: calc(var(--spacing-unit) * 2);
                border-radius: 8px;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            }

            .form-box h2 {
                font-size: 22px;
                margin-bottom: calc(var(--spacing-unit) * 2);
            }

            .section-title {
                font-size: 13px;
                margin-top: calc(var(--spacing-unit) * 2);
                margin-bottom: calc(var(--spacing-unit) * 1.25);
            }

            label {
                font-size: 13px;
            }

            .form-control,
            .form-select {
                font-size: 13px;
                padding: calc(var(--spacing-unit) * 0.75) calc(var(--spacing-unit) * 1.25);
            }

            .btn {
                font-size: 13px;
                padding: calc(var(--spacing-unit) * 1) calc(var(--spacing-unit) * 2);
            }

            hr {
                margin: calc(var(--spacing-unit) * 3) 0;
            }

            .button-group {
                margin-top: calc(var(--spacing-unit) * 4);
                padding-top: calc(var(--spacing-unit) * 2);
            }

            .row {
                grid-template-columns: 1fr;
                gap: calc(var(--spacing-unit) * 1.5);
            }

            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-6,
            .col-md-12 {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-box">
            <h2>Thêm Bác sĩ mới</h2>

            <?= $msg ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="mabs" value="<?= $mabs ?>">
                <input type="hidden" name="email" value="">

                <!-- ======================== 1. THÔNG TIN CÁ NHÂN ======================== -->
                <div class="section-title">Thông tin cá nhân</div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Họ tên *</label>
                        <input type="text" name="hoten" required class="form-control"
                               placeholder="Nhập họ và tên" value="<?= htmlspecialchars($old['hoten'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngaysinh" class="form-control"
                               value="<?= htmlspecialchars($old['ngaysinh'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label>Giới tính</label>
                        <select name="gioitinh" class="form-select">
                            <?php
                            foreach (['Nam','Nữ','Khác'] as $gt) {
                                $sel = (!empty($old['gioitinh']) && $old['gioitinh'] == $gt) ? 'selected' : '';
                                echo "<option value='$gt' $sel>$gt</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>CCCD *</label>
                        <input type="text" name="cccd" maxlength="12" required class="form-control"
                               placeholder="Nhập 12 số"
                               oninput="this.value=this.value.replace(/\D/g,'')"
                               value="<?= htmlspecialchars($old['cccd'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label>Dân tộc</label>
                        <select name="dantoc" id="dantoc" class="form-select">
                            <option value="">--Chọn dân tộc--</option>
                            <option value="Kinh">Kinh</option>
                            <option value="Tày">Tày</option>
                            <option value="Thái">Thái</option>
                            <option value="Hoa">Hoa</option>
                            <option value="Khơ-me">Khơ-Me</option>
                            <option value="Mường">Mường</option>
                            <option value="Nùng">Nùng</option>
                            <option value="HMông">HMông</option>
                            <option value="Dao">Dao</option>
                            <option value="Gia-rai">Gia-rai</option>
                            <option value="Ngái">Ngái</option>
                            <option value="Ê-đê">Ê-đê</option>
                            <option value="Ba-na">Ba-na</option>
                            <option value="Xơ-Đăng">Xơ-Đăng</option>
                            <option value="Sán chay">Sán chay</option>
                            <option value="Cơ-ho">Cơ-ho</option>
                            <option value="Chăm">Chăm</option>
                            <option value="Sán Dìu">Sán Dìu</option>
                            <option value="Hrê">Hrê</option>
                            <option value="Mnông">Mnông</option>
                            <option value="Ra-glai">Ra-glai</option>
                            <option value="Xtiêng">Xtiêng</option>
                            <option value="Bru-Vân Kiều">Bru-Vân Kiều</option>
                            <option value="Thổ">Giáy</option>
                            <option value="Cơ-tu">Cơ-tu</option>
                            <option value="Gié">Triêng</option>
                            <option value="Mạ">Mạ</option>
                            <option value="Khơ-mú">Khơ-mú</option>
                            <option value="Co">Co</option>
                            <option value="Tà-ôi">Tà-ôi</option>
                            <option value="Chơ-ro">Chơ-ro</option>
                            <option value="Kháng">Kháng</option>
                            <option value="Xinh-mun">Xinh-mun</option>
                            <option value="Hà Nhì">Hà Nhì</option>
                            <option value="Chu ru">Chu ru</option>
                            <option value="Lào">Lào</option>
                            <option value="La Chí">La Chí</option>
                            <option value="La Ha">La Ha</option>
                            <option value="Phù Lá">Phù Lá</option>
                            <option value="La Hủ">La Hủ</option>
                            <option value="Lự">Lự</option>
                            <option value="Lô Lô">Lô Lô</option>
                            <option value="Chứt">Chứt</option>
                            <option value="Mảng">Mảng</option>
                            <option value="Pà Thẻn">Pà Thẻn</option>
                            <option value="Co Lao">Co Lao</option>
                            <option value="Cống">Cống</option>
                            <option value="Bố Y">Bố Y</option>
                            <option value="Si La">Si La</option>
                            <option value="Pu Péo">Pu Péo</option>
                            <option value="Brâu">Brâu</option>
                            <option value="Ơ Đu">Ơ Đu</option>
                            <option value="Rơ măm">Rơ măm</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>SĐT</label>
                        <input type="text" name="sdt" class="form-control" placeholder="Nhập số điện thoại"
                               value="<?= htmlspecialchars($old['sdt'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Email cá nhân</label>
                        <input type="email" name="emailcanhan" class="form-control" placeholder="example@email.com"
                               value="<?= htmlspecialchars($old['emailcanhan'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Số nhà / Địa chỉ</label>
                        <input type="text" name="sonha" class="form-control" placeholder="Nhập số nhà, đường phố"
                               value="<?= htmlspecialchars($old['sonha'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Tỉnh / Thành phố *</label>
                        <select name="tinhthanhpho" id="tinhthanhpho" required class="form-select">
                            <option value="">-- Chọn Tỉnh/TP --</option>
                            <?php foreach ($tinh_list as $t) {
                                $sel = (!empty($old['tinhthanhpho']) && $old['tinhthanhpho'] == $t['matinhthanhpho']) ? 'selected' : '';
                                echo "<option value='{$t['matinhthanhpho']}' $sel>{$t['tentinhthanhpho']}</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Xã / Phường *</label>
                        <select name="xaphuong" id="xaphuong" required class="form-select">
                            <option value="">-- Chọn Xã/Phường --</option>
                        </select>
                    </div>
                </div>

                <hr>

                <!-- ======================== 2. THÔNG TIN HÀNH NGHỀ ======================== -->
                <div class="section-title">Thông tin hành nghề</div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Cấp bậc</label>
                        <input type="text" name="capbac" class="form-control"
                               value="<?= htmlspecialchars($old['capbac'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Chuyên khoa *</label>
                        <select name="machuyenkhoa" required class="form-select">
                            <option value="">-- Chọn chuyên khoa --</option>
                            <?php foreach ($dsKhoa as $k) {
                                $sel = (!empty($old['machuyenkhoa']) && $old['machuyenkhoa'] == $k['machuyenkhoa']) ? 'selected' : '';
                                echo "<option value='{$k['machuyenkhoa']}' $sel>{$k['tenchuyenkhoa']}</option>";
                            } ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Giá khám *</label>
                        <input type="text" name="giakham" required class="form-control"
                               oninput="this.value=this.value.replace(/\D/g,'')"
                               value="<?= htmlspecialchars($old['giakham'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="ngaybatdau" class="form-control"
                               value="<?= htmlspecialchars($old['ngaybatdau'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="ngayketthuc" class="form-control"
                               value="<?= htmlspecialchars($old['ngayketthuc'] ?? '') ?>">
                    </div>
                </div>

                <hr>

                <!-- ======================== 3. HÌNH ẢNH & GIỚI THIỆU ======================== -->
                <div class="section-title">Hình ảnh & Giới thiệu</div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Ảnh bác sĩ</label>
                        <input type="file" name="imgbs" class="form-control" accept="image/*" onchange="previewImg(event)">
                        <img id="preview">
                    </div>

                    <div class="col-md-4">
                        <label>CCCD mặt trước</label>
                        <input type="file" name="cccd_matruoc" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-4">
                        <label>CCCD mặt sau</label>
                        <input type="file" name="cccd_matsau" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-6">
                        <label>Mô tả ngắn</label>
                        <textarea name="motabs" class="form-control"><?= htmlspecialchars($old['motabs'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label>Giới thiệu chi tiết</label>
                        <textarea name="gioithieubs" class="form-control"><?= htmlspecialchars($old['gioithieubs'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- ======================== BUTTONS ======================== -->
                <div class="button-group">
                    <a href="?action=nhanvien&tab=bacsi" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-success">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImg(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('tinhthanhpho').addEventListener('change', function() {
            const matinhthanhpho = this.value;
            if (matinhthanhpho) {
                fetch('?action=load_xa&tinh=' + matinhthanhpho)
                    .then(response => response.json())
                    .then(data => {
                        const xaphuong = document.getElementById('xaphuong');
                        xaphuong.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                        data.forEach(xa => {
                            xaphuong.innerHTML += `<option value="${xa.maxaphuong}">${xa.texaphuong}</option>`;
                        });
                    });
            }
        });
    </script>
</body>
</html>

