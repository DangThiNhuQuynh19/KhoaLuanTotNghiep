<?php
    include_once('Controllers/cphieukhambenh.php');
    include_once('Controllers/cchuyengia.php');
    include_once('Controllers/clinhvuc.php');
    date_default_timezone_set('Asia/Ho_Chi_Minh'); // đặt múi giờ Việt Nam
    $clinhvuc = new cLinhVuc();
    $cchuyengia = new cChuyenGia();
    $cphieukhambenh = new cPhieuKhamBenh();
    $chuyengia= $cchuyengia->getChuyenGiaByTenTK($_SESSION['user']['tentk']);
    $lichhenhomnay_list=$cphieukhambenh->get_lichkhamonl_homnay($chuyengia['machuyengia'], date('Y-m-d'));
    $tongbenhnhanhomnay= $cphieukhambenh->count_benhnhan($chuyengia['machuyengia']);
    $sophieukhamtrongtuan = $cphieukhambenh->get_sophieukham_trongtuan($chuyengia['machuyengia']);
    $lichkhamsapden = $cphieukhambenh->get_lichkham_sapden($chuyengia['machuyengia']);
?>
<div class="container">
        <div class="dashboard">
            <div class="notification-panel">
                <h3>Chào mừng trở lại, <?php echo "chuyên gia ".$chuyengia['hoten'] ?? 'Chuyên gia'; ?>!</h3>
                <p>Bạn có lịch hẹn sắp tới với bệnh nhân <?php echo(!empty($lichkhamsapden))? $lichkhamsapden[0]['hoten']:" "; ?></p>
                <div class="notification-details">
                    <div class="notification-detail">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo(!empty($lichkhamsapden))? $lichkhamsapden[0]['ngaykham']:" "; ?></span>
                    </div>
                    <div class="notification-detail">
                        <i class="fas fa-clock"></i>
                        <span><?php echo(!empty($lichkhamsapden))? $lichkhamsapden[0]['giobatdau'].'-'.$lichkhamsapden[0]['gioketthuc']:"";?></span>
                    </div>
                    <div class="notification-detail">
                        <i class="fas fa-comment-medical"></i>
                        <span>Vui lòng liên hệ qua chat</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <a href="?action=tinnhan&mabenhnhan=<?php echo(!empty($lichkhamsapden))? $lichkhamsapden[0]['mabenhnhan']:" "?>" class="btn-primary btn-small">Nhắn tin</a>
                </div>
            </div>
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <a href="?action=benhnhan"><i class="fas fa-user-injured"></i></a>
                    </div>
                    <div class="stat-info">
                        <h3>Bệnh nhân hôm nay</h3>
                        <p class="stat-number"><?php echo $tongbenhnhanhomnay['homnay'];?></p>
                        <p class="stat-change positive"><?php echo $tongbenhnhanhomnay['trangthai']." ".$tongbenhnhanhomnay['chenhlech'];?> so với hôm qua</p>
                    </div>
                </div>
                
                
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <a href="?action=lichhen"><i class="fas fa-calendar-check"></i></a>
                    </div>
                    <div class="stat-info">
                        <h3>Lịch hẹn</h3>
                        <p class="stat-number"><?php echo $sophieukhamtrongtuan['solichhentrongtuan']; ?></p>
                        <p class="stat-change">Trong tuần này</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <a href="?action=tinnhan"> <i class="fas fa-comment-medical"></i></a>
                    </div>
                    <div class="stat-info">
                        <h3>Tin nhắn mới</h3>
                        <p class="stat-number">5</p>
                        <p class="stat-change positive">+2 trong 24 giờ qua</p>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-content">
                <div class="appointments-section">
                    <h2>Lịch hẹn hôm nay</h2>
                    <div class="appointment-list">
                        <?php
                        if($lichhenhomnay_list){
                            foreach ($lichhenhomnay_list as $i) {
                                echo '<div class="appointment-item">';
                                echo '<div class="appointment-icon"><i class="fas fa-user"></i></div>';
                                echo '<div class="appointment-details">';
                                echo '<h4>' . $i['hoten'] . '</h4>';
                                echo '<p>' . $i['tenlinhvuc'] . ' - ' . $i['giobatdau'] . '</p>';
                                echo '</div>';
                                $giobatdau = new DateTime($i['giobatdau']);
                                $hientai = new DateTime(); 
                            if ($hientai > $giobatdau) {
                                echo '<a href="?action=tinnhan&mabenhnhan=' . $i['mabenhnhan'] . '" class="btn-primary btn-small">Nhắn tin</a>';
                            }
                            echo '</div>';
                            }
                        }else{
                            echo '<div class="appointment-item">';
                               echo 'Hôm nay bạn không có bệnh nhân';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
                
            
            </div>
        </div>
    </div>
    <?php require("Views/bacsi/layout/footer.php"); ?>