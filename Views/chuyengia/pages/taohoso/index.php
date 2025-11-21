<?php
include_once('Controllers/cbenhnhan.php');
include_once('Controllers/clinhvuc.php');
include_once('Controllers/chosobenhandientu.php');
include_once('Controllers/cchitiethoso.php');
include_once('Controllers/cchuyengia.php');
include_once("Assets/config.php");
$cchuyengia = new cChuyenGia();
$chosobenhandientu = new cHoSoBenhAnDienTu();
$cchitiethoso = new cChiTietHoSo();
$cbenhnhan = new cBenhnhan();
$clinhvuc = new cLinhVuc();
$mabenhnhan = $_GET['mabenhnhan'];
$benhnhan = $cbenhnhan->getbenhnhanbyid($_GET['mabenhnhan']);
$linhvuc = $clinhvuc->get_linhvuc_notmabenhnhan($mabenhnhan);
$chuyengia= $cchuyengia->getChuyenGiaByTenTK($_SESSION['user']['tentk']);
$linhvuc_chuyengia = $clinhvuc->get_linhvuc_machuyengia($chuyengia['machuyengia']);
$message = "";

if(isset($_POST['submit'])){
    // Tạo hồ sơ bệnh án mới
    if($chosobenhandientu->create_hosobenhan_mabenhnhan($mabenhnhan)){
        $hosonew = $chosobenhandientu->get_hsba_new($mabenhnhan);
        $mahoso = $hosonew[0]['mahoso']; // Lấy mã hồ sơ vừa tạo
        $madonthuoc=NULL;
       
       if( $cchitiethoso->create_chitiethoso($mahoso,$chuyengia['machuyengia'],$_POST['trieuchung'],$_POST['chuandoan'],$_POST['huongdieutri'],$madonthuoc,$_POST['note']) ){
            $message = "Hồ sơ bệnh án đã được tạo thành công!";
       }
       else{
            $message = "Hồ sơ bệnh án không được tạo thành công!";
       }  
    } else {
        $message = "Hồ sơ bệnh án không được tạo thành công!";
    }
}
?>
<!DOCTYPE html>
<style>
    .back-button {
        display: flex;
        align-items: center;
    }

    .back-button a {
        margin-right: 16px;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--dark);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--light-gray);
        border-radius: var(--border-radius);
        font-size: 14px;
        transition: var(--transition);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) inset;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
        outline: none;
    }

    .form-group select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23718096' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .form-col {
        flex: 1;
        padding: 0 10px;
        min-width: 200px;
    }

    /* Patient Info */
    .patient-info {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 16px;
    }

    .patient-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .patient-avatar i {
        font-size: 48px;
        color: var(--primary);
    }

    .patient-details {
        flex: 1;
        min-width: 300px;
    }

    .patient-name {
        font-size: 20px;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .patient-id {
        font-size: 14px;
        color: var(--gray);
        margin-bottom: 12px;
        display: inline-block;
        background-color: var(--light-gray);
        padding: 4px 12px;
        border-radius: 20px;
    }

    .patient-data {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .patient-data-item {
        margin-bottom: 8px;
    }

    .data-label {
        font-size: 12px;
        color: var(--gray);
        margin-bottom: 4px;
    }

    .data-value {
        font-weight: 500;
        color: var(--dark);
    }

    /* Specialty Selection */
    .specialty-selection {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .specialty-card {
        border: 1px solid var(--light-gray);
        border-radius: var(--border-radius);
        padding: 16px;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
    }

    .specialty-card:hover {
        border-color: var(--primary);
        background-color: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .specialty-card.selected {
        border-color: var(--primary);
        background-color: var(--primary-light);
        box-shadow: var(--shadow);
    }

    .specialty-card.selected::before {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        top: 12px;
        right: 12px;
        color: var(--primary);
        background-color: var(--white);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        box-shadow: var(--shadow);
    }

    .specialty-name {
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 8px;
    }

    .specialty-description {
        font-size: 13px;
        color: var(--gray);
    }

    /* Alert Messages */
    .alert {
        padding: 16px;
        border-radius: var(--border-radius);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
    }

    .alert i {
        margin-right: 12px;
        font-size: 20px;
    }

    .alert-success {
        background-color: var(--success-light);
        color: var(--success);
        border-left: 4px solid var(--success);
    }

    .alert-warning {
        background-color: var(--warning-light);
        color: var(--warning);
        border-left: 4px solid var(--warning);
    }

    .alert-info {
        background-color: var(--primary-light);
        color: var(--primary);
        border-left: 4px solid var(--primary);
    }

    .alert-danger {
        background-color: var(--danger-light);
        color: var(--danger);
        border-left: 4px solid var(--danger);
    }
    
    .tab-button {
        padding: 12px 24px;
        background-color: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        color: var(--gray);
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }
        
        .form-col {
            margin-bottom: 16px;
        }
        
        .patient-info {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .patient-avatar {
            margin-right: 0;
            margin-bottom: 16px;
        }
        
        .patient-data {
            grid-template-columns: 1fr;
        }
        
        .specialty-selection {
            grid-template-columns: 1fr;
        }
    }

    .modal {
        display: none; /* Ẩn ban đầu */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        overflow: auto;
        background-color: rgba(0,0,0,0.4); /* nền mờ */
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 800px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        position: relative;
        max-height: 80vh;
        overflow-y: auto;
    }

    .close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
    }

    /* Bảng danh sách thuốc */
    .medication-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .medication-table th,
    .medication-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid var(--light-gray);
    }

    .medication-table th {
        background-color: var(--primary-light);
        color: var(--primary-dark);
        font-weight: 600;
    }

    .medication-table tr:hover {
        background-color: var(--primary-light);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-small {
        padding: 6px 12px;
        font-size: 13px;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-danger {
        background-color: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background-color: #d32f2f;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
    }

    .btn-success {
        background-color: var(--success);
        color: white;
    }

    .btn-success:hover {
        background-color: #388e3c;
    }

    .medication-form {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid var(--light-gray);
    }

    .form-title {
        margin-bottom: 15px;
        font-weight: 600;
        color: var(--primary-dark);
        border-bottom: 1px solid var(--light-gray);
        padding-bottom: 10px;
    }
</style>
</head>
<body>
<main class="container">
    <div class="content-header">
        <div class="back-button">
            <a href="medical-records.php" class="btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Tạo Hồ Sơ Bệnh Án</h1>
        </div>
    </div>

    <!-- Thông báo thành công -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Thành công!</strong> <?php echo $message; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Chỉ hiển thị form khi không có thông báo -->
    <?php if (empty($message)): ?>

        <!-- Chưa chọn bệnh nhân -->
        <?php if (empty($mabenhnhan)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Lưu ý!</strong> Vui lòng chọn bệnh nhân trước khi tạo hồ sơ bệnh án.
                </div>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="?action=benhnhan" class="btn-primary">
                    <i class="fas fa-user-injured"></i> Chọn bệnh nhân
                </a>
            </div>

        <!-- Không tìm thấy bệnh nhân -->
        <?php elseif (!$mabenhnhan): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Lỗi!</strong> Không tìm thấy thông tin bệnh nhân với mã <strong><?php echo $mabenhnhan; ?></strong>.
                </div>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="?action=benhnhan" class="btn-primary">
                    <i class="fas fa-user-injured"></i> Chọn bệnh nhân khác
                </a>
            </div>

        <!-- Hồ sơ lĩnh vực đã tồn tại -->
        <?php elseif ($chosobenhandientu->get_hoso_malinhvuc($mabenhnhan,$linhvuc_chuyengia['malinhvuc'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Lỗi!</strong> Hồ sơ của lĩnh vực <?php echo $linhvuc_chuyengia['tenlinhvuc'];?> đã được tạo.
                </div>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="?action=chitietbenhnhan&id=<?php echo $mabenhnhan ?>" class="btn-primary">
                    <i class="fas fa-user-injured"></i> Chọn hồ sơ để xem
                </a>
            </div>

        <!-- Hiển thị form tạo hồ sơ -->
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h2>Thông Tin Bệnh Nhân</h2>
                </div>
                <div class="card-body">
                    <div class="patient-info">
                        <div class="patient-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="patient-details">
                            <h3 class="patient-name"><?php echo $benhnhan['hoten']; ?></h3>
                            <div class="patient-id"><?php echo $benhnhan['mabenhnhan']; ?></div>
                            <div class="patient-data">
                                <div class="patient-data-item">
                                    <div class="data-label">Ngày sinh</div>
                                    <div class="data-value"><?php echo $benhnhan['ngaysinh']; ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">Giới tính</div>
                                    <div class="data-value"><?php echo $benhnhan['gioitinh']; ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">Tiền sử bệnh tật của bệnh nhân</div>
                                    <div class="data-value"><?php echo decryptData($benhnhan['tiensubenhtatcuabenhnhan']); ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">Tiền sử bệnh tật của gia đình</div>
                                    <div class="data-value"><?php echo decryptData($benhnhan['tiensubenhtatcuagiadinh']); ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">Địa chỉ</div>
                                    <div class="data-value"><?php echo $benhnhan['sonha'].'-'.$benhnhan['xaphuong'].'-'.$benhnhan['tinhthanhpho']; ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">Số điện thoại</div>
                                    <div class="data-value"><?php echo decryptData($benhnhan['sdt']); ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">Email cá nhân</div>
                                    <div class="data-value"><?php echo decryptData($benhnhan['emailcanhan']); ?></div>
                                </div>
                                <div class="patient-data-item">
                                    <div class="data-label">CCCD</div>
                                    <div class="data-value"><?php echo decryptData($benhnhan['cccd']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Record Form -->
            <form action="" method="post" id="medicalRecordForm">
                <input type="hidden" name="patientId" value="<?php echo $benhnhan['mabenhnhan']; ?>">
                
                <div class="card">
                    <div class="card-header">
                        <h2>Thông Tin Tư Vấn</h2>
                    </div>
                    <div class="card-body">
                        <div class="tabs">
                            <div class="tab-header">
                                <button type="button" class="tab-button active" onclick="openTab(event, 'tab-complaint')">Lý do tư vấn & Lịch sử</button>
                            </div>
                        </div>
                        <div id="tab-complaint" class="tab-content active">
                            <div class="form-group">
                                <label for="chiefComplaint">Tình trạng bệnh nhân</label>
                                <textarea name="trieuchung" id="chiefComplaint" rows="3" required placeholder="Nhập tình trạng của bệnh nhân..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="chuandoan">Chẩn đoán</label>
                                <textarea name="chuandoan" id="chuandoan" rows="3" placeholder="Nhập chẩn đoán về bệnh của bệnh nhân..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="note">Kết luận</label>
                                <textarea name="note" id="note" rows="3" placeholder="Nhập kết luận..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="huongdieutri">Hướng điều trị</label>
                                <textarea name="huongdieutri" id="huongdieutri" rows="3" placeholder="Cho biết hướng điều trị..."></textarea>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                            <button type="button" class="btn-outline" onclick="window.location.href='?action=taohoso&mabenhnhan=<?php echo $mabenhnhan?>'">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <div>
                                <button type="submit" name="submit" class="btn-primary">
                                    <i class="fas fa-check"></i> Hoàn thành hồ sơ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>

    <?php endif; ?>
</main>
</body>
</html>