<?php
$mabenhnhan = $_GET['id'] ?? '';
include_once('Controllers/cbenhnhan.php');
include_once('Controllers/chosobenhandientu.php');
include_once("Assets/config.php");
$chsba = new cHoSoBenhAnDienTu();
$cbenhnhan = new cBenhNhan();
$bn= $cbenhnhan->get_benhnhan_id($mabenhnhan);
$hsba_list= $chsba->get_hsba_mabenhnhan1($mabenhnhan);
?>    
<div class="container">
    <div class="content-header">
        <div class="back-button">
            <a href="?action=benhnhan" class="btn-icon"><i class="fas fa-arrow-left"></i></a>
            <h1>Hồ sơ bệnh nhân: <?php echo $bn['mabenhnhan']; ?></h1>
        </div>
    </div>
    
    <div class="patient-detail">
        <div class="patient-sidebar">
            <div class="card">
                <div class="card-body">
                    <div class="patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 class="patient-name"><?php echo $bn['hoten']; ?></h2>
                    <p class="patient-id">Mã BN: <?php echo $bn['mabenhnhan']; ?></p>
                    
                    <div class="patient-info">
                        <div class="info-row">
                            <span class="info-label">Ngày sinh:</span>
                            <span class="info-value"><?php echo $bn['ngaysinh']; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Giới tính:</span>
                            <span class="info-value"><?php echo $bn['gioitinh']; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Điện thoại:</span>
                            <span class="info-value"><?php echo decryptData($bn['sdt']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Địa chỉ:</span>
                            <span class="info-value"><?php echo $bn['sonha'].",".$bn['tenxaphuong'].",".$bn['tentinhthanhpho']; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tiền sử bệnh tật của bệnh nhân:</span>
                            <span class="info-value"><?php echo decryptData($bn['tiensubenhtatcuabenhnhan']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tiền sử bệnh tật của gia đình:</span>
                            <span class="info-value"><?php echo decryptData($bn['tiensubenhtatcuagiadinh']); ?></span>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
        <div class="patient-content">
            <form method="POST" style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
            <a href="?action=taohoso&mabenhnhan=<?php echo $mabenhnhan;?>" class="btn-primary"><i class="fas fa-plus"></i> Thêm hồ sơ</a>
            </form>
            <div class="tabs">
                <div class="tab-header">
                    <a href="?action=chitietbenhnhan&id=<?php echo $mabenhnhan; ?>&tab=medical-records" class="tab-link <?php echo $active_tab === 'medical-records' ? 'active' : ''; ?>">Hồ sơ bệnh án</a>
                </div>
                <div class="tab-content">
                  
                        <div class="card">
                            <div class="card-header">
                                <h2>Hồ sơ bệnh án</h2>
                                <p>Lịch sử tư vấn của bệnh nhân</p>  
                            </div>
                            <div class="card-body">
                                <div class="medical-records">
                                    <?php
                                    if($hsba_list){
                                        foreach ($hsba_list as $i) {
                                            echo '<div class="record-item">';
                                            echo '<div class="record-header">';
                                            echo '<h3>' . $i['tenlinhvuc'] . '</h3>';
                                            echo '<span class="record-date">' . $i['ngaytao'] . '</span>';
                                            echo '</div>';
                                            echo '<div class="record-details">';
                                            echo '<p><strong>Chuyên gia:</strong> ' . $i['hoten'] . '</p>';
                                            echo '<p><strong>Triệu chứng ban đầu:</strong> ' . $i['trieuchungbandau'] . '</p>';
                                            echo '<p><strong>Chẩn đoán:</strong> ' . $i['chandoan'] . '</p>';
                                            echo '<p><strong>Ghi chú:</strong> ' . $i['huongdieutri'] . '</p>';
                                            echo '</div>';
                                            echo '<div class="record-actions">';
                                            echo '<a href="?action=chitiethoso&mahoso='.$i['mahoso'] .'" class="btn-small">Xem chi tiết</a>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    }else{
                                        echo '</div>';
                                            echo '<div class="record-details">';
                                            echo '<p><strong>Chưa có hồ sơ nào gần đây</strong></p>';
                                            echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                   

            </div>
        </div>
    </div>
</div>
<?php require("Views/chuyengia/layout/footer.php"); ?>
