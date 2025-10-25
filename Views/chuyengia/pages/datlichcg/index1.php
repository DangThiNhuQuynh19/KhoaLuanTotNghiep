<?php
    require 'vendor/autoload.php';
    use Endroid\QrCode\Builder\Builder;
    use Endroid\QrCode\Writer\PngWriter;
    include_once("Assets/config.php");
    include_once('xu-ly-email.php');
    include_once('xu-ly-thanh-toan.php');

    include_once('Controllers/cbenhnhan.php');
    include_once('Controllers/cchuyengia.php');
    include_once('Controllers/chosobenhandientu.php');
    include_once('Controllers/clichkham.php');
    include_once('Controllers/cPhieuKhambenh.php');

    // Khởi tạo các đối tượng controller
    $cbenhnhan = new cBenhNhan();
    $chosobenhandientu = new cHoSoBenhAnDienTu();
    $cchuyengia = new cChuyenGia();
    $clichkham = new cLichKham();
    $cphieukhambenh = new cPhieuKhamBenh();

    $phieukham = $cphieukhambenh->get_phieukhambenh();
    $chuyengia = $cchuyengia->getChuyenGiaByTenTK($_SESSION['user']['tentk']);
    $danh_sach_benh_nhan = $cbenhnhan->get_benhnhan_machuyengia($chuyengia['machuyengia']);
    $lichchuyengia = $clichkham->get_lickham_mabacsi($chuyengia['machuyengia']);
   
    $thong_bao = '';
    $loai_thong_bao = 'thanh_cong';

    if(isset($_POST['btn_xac_nhan'])){
        $_SESSION['mabenhnhan']   = $_POST['ma_benh_nhan'];
        $_SESSION['makhunggiokb'] = $_POST['makhunggiokb'];
        if ($idbs) $_SESSION['mabacsi'] = $_POST['mabacsi'];
        elseif ($idcg) $_SESSION['machuyengia'] = $_POST['machuyengia'];
        $_SESSION['ngaykham']     = $_POST['ngaykham'];
        $_SESSION['tongtien']     = $_POST['giakham'];
        $_SESSION['matrangthai']  = '6';
      
        if(!empty($_POST['ma_benh_nhan']) && !empty($_POST['gio_hen']) && !empty($_POST['ngay_hen'])){
            $maphieukb = 'PKB' . time() . rand(100, 999);
            $result = $cphieukhambenh->insertphieukham(
                $maphieukb,
                $_POST['ngay_hen'],
                $_POST['gio_hen'],
                $chuyengia['machuyengia'],
                $_POST['ma_benh_nhan'],
                6
            );

            if($result){
                $xu_ly_email = new XuLyEmail();
                $ket_qua_gui_email = $xu_ly_email->gui_email_yeu_cau_thanh_toan(
                    'nguyentrang2642003@gmail.com',
                    $_POST['ten_benh_nhan'],
                    'Đặt lịch khám bệnh '.$_POST['hinh_thuc'],
                    $_POST['ngay_hen'],
                    $khung_gio[0]['giobatdau'],
                    $maphieukb
                );

                if ($ket_qua_gui_email) {
                    $_SESSION['popup_success'] = true;
                    $_SESSION['popup_title'] = 'Thành công!';
                    $_SESSION['popup_message'] = 'Đã đặt lịch xét nghiệm và gửi email yêu cầu thanh toán đến bệnh nhân.';
                    
                    // Redirect để tránh insert lần nữa khi F5
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit();
                    
                } else {
                    $_SESSION['thong_bao'] = '<strong>Cảnh báo!</strong> Đã đặt lịch thành công nhưng không thể gửi email. Vui lòng liên hệ bệnh nhân trực tiếp.';
                    $_SESSION['loai_thong_bao'] = 'canh_bao';
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit();
                    
                }
            }else {
                $_SESSION['thong_bao'] = '<strong>Thất bại!</strong> Đặt lịch không thành công. Vui lòng thử lại.';
                $_SESSION['loai_thong_bao'] = 'loi';
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
                
            }
        }else {
            $_SESSION['thong_bao'] = '<strong>Lỗi!</strong> Vui lòng điền đầy đủ thông tin.';
            $_SESSION['loai_thong_bao'] = 'loi';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        
    }

?>

<style>
    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        transition: opacity 0.5s ease-out;
    }
    
    .alert-thanh_cong {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-canh_bao {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .alert-loi {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .ca-kham {
        margin-bottom: 20px;
    }

    .khung-gio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }

    .khung-gio-btn {
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .khung-gio-btn:hover {
        border-color: #5e4b93;
        background: #8a7fc7;
        transform: translateY(-2px);
    }

    .khung-gio-btn.selected {
        border-color: #8a7fc7;
        background:#8a7fc7;
        color: white;
    }

    .khung-gio-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f5f5f5;
    }

    .khung-gio-btn .time {
        font-weight: bold;
        font-size: 14px;
    }

    .khung-gio-btn .status {
        font-size: 12px;
        margin-top: 5px;
        color: #666;
    }

    .khung-gio-btn.selected .status {
        color: white;
    }
</style>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>
    <main class="container">
        <div class="content-header">
            <div class="back-button">
                <a href="index.php" class="btn-icon">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>Đặt Lịch khám bệnh</h1>
            </div>
        </div>
        <!-- Thông báo -->
        <?php if (!empty($_SESSION['thong_bao'])): ?>
            <div class="alert alert-<?php echo $_SESSION['loai_thong_bao']; ?>" id="thong_bao_alert">
                <i class="fas fa-<?php echo $_SESSION['loai_thong_bao'] === 'thanh_cong' ? 'check-circle' : ($_SESSION['loai_thong_bao'] === 'canh_bao' ? 'exclamation-triangle' : 'exclamation-circle'); ?>"></i>
                <div>
                    <?php echo $_SESSION['thong_bao']; ?>
                </div>
            </div>
            <!-- Enhanced auto-hide notification script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        const alert = document.getElementById('thong_bao_alert');
                        if (alert) {
                            alert.style.transition = 'opacity 0.5s ease-out';
                            alert.style.opacity = '0';
                            setTimeout(function() {
                                alert.style.display = 'none';
                            }, 500);
                        }
                    }, 6000); // 6 seconds
                });
            </script>
            <?php 
                unset($_SESSION['thong_bao']); 
                unset($_SESSION['loai_thong_bao']); 
            ?>
        <?php endif; ?>
        <!-- Added popup success message handling -->
        <?php if (!empty($_SESSION['popup_success'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    hien_thi_popup_thanh_cong(
                        '<?php echo $_SESSION['popup_title']; ?>', 
                        '<?php echo $_SESSION['popup_message']; ?>'
                    );
                });
            </script>
            <?php 
                unset($_SESSION['popup_success']); 
                unset($_SESSION['popup_title']); 
                unset($_SESSION['popup_message']); 
            ?>
        <?php endif; ?>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Lưu ý:</strong> Sau khi đặt lịch, bệnh nhân sẽ nhận email thông báo. Vui lòng yêu cầu bệnh nhân thanh toán trong vòng 30 phút, nếu không lịch hẹn sẽ tự động bị hủy.
            </div>
        </div>
        
        <div class="content-header"> 
            <a href="?action=taohoso" style="float:right; margin-bottom: 5px;" class="btn-primary btn-small">Tạo hồ sơ</a>
        </div>
        
        <form action="" method="post" id="form_dat_lich" onsubmit="return kiem_tra_form()">
            <div class="card">
                <div class="card-header">
                    <h2>Thông Tin Bệnh Nhân</h2>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="ma_benh_nhan">Mã bệnh nhân <span style="color: red;">*</span></label> 
                                <input list="ds_benh_nhan" id="ma_benh_nhan" name="ma_benh_nhan" placeholder="Nhập mã hoặc tên bệnh nhân..." autocomplete="off" required> 
                                <datalist id="ds_benh_nhan"> 
                                    <?php foreach ($danh_sach_benh_nhan as $benh_nhan): ?> 
                                        <option value="<?php echo $benh_nhan['mabenhnhan']; ?>"> <?php echo $benh_nhan['mabenhnhan'] . ' - ' . $benh_nhan['hoten']; ?> </option> 
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label for="ma_ho_so">Hồ sơ bệnh nhân</label>
                                <select name="ma_ho_so" id="ma_ho_so" required>
                                    <option value="">-- Chọn hồ sơ --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label for="ten_benh_nhan">Họ và tên</label>
                                <input type="text" id="ten_benh_nhan" name="ten_benh_nhan" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="ngay_sinh_benh_nhan">Ngày sinh</label>
                                <input type="text" id="ngay_sinh_benh_nhan" name="ngay_sinh_benh_nhan" readonly>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="gioi_tinh_benh_nhan">Giới tính</label>
                                <input type="text" id="gioi_tinh_benh_nhan" name="gioi_tinh_benh_nhan" readonly>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="sdt_benh_nhan">Số điện thoại</label>
                                <input type="text" id="sdt_benh_nhan" name="sdt_benh_nhan" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Thông Tin Lịch Hẹn</h2>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="ngay_hen">Ngày khám</label>
                                <input type="date" id="ngay_hen" name="ngay_hen" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row" id="khung_gio_kham" style="display: none;">
                        <div class="form-col">
                            <div class="form-group">
                                <label>Khám Online</label>
                                
                                <div class="ca-kham">
                                    <p>Ca Sáng</p>
                                    <div class="khung-gio-grid" id="online_ca_sang"></div>
                                </div>
                                
                                <div class="ca-kham">
                                    <p>Ca Chiều</p>
                                    <div class="khung-gio-grid" id="online_ca_chieu"></div>
                                </div>
                                
                                <div class="ca-kham">
                                    <p>Ca Tối</p>
                                    <div class="khung-gio-grid" id="online_ca_toi"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label>Khám Tại Bệnh Viện</label>
                                
                                <div class="ca-kham">
                                    <p>Ca Sáng</p>
                                    <div class="khung-gio-grid" id="offline_ca_sang"></div>
                                </div>
                                
                                <div class="ca-kham">
                                    <p>Ca Chiều</p>
                                    <div class="khung-gio-grid" id="offline_ca_chieu"></div>
                                </div>
                                
                                <div class="ca-kham">
                                    <p>Ca Tối</p>
                                    <div class="khung-gio-grid" id="offline_ca_toi"></div>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="gio_hen" id="gio_hen_selected" required>
                        <input type="hidden" name="hinh_thuc" id="hinh_thuc_selected" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ghi_chu">Ghi chú</label>
                        <textarea name="ghi_chu" id="ghi_chu" rows="4" placeholder="Nhập các yêu cầu đặc biệt hoặc thông tin bổ sung..."></textarea>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                <button type="reset" class="btn-outline" onclick="lam_moi_form()">
                    <i class="fas fa-redo"></i> Làm mới
                </button>
                <button type="submit" name="btn_xac_nhan" class="btn-primary" id="nut_xac_nhan">
                    <i class="fas fa-calendar-check"></i> Xác nhận đặt lịch
                </button>
            </div>
        </form>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Bệnh Viện Hạnh Phúc. Tất cả các quyền được bảo lưu.
            </div>
            <div class="footer-links">
                <a href="about.php">Về chúng tôi</a>
                <a href="privacy.php">Chính sách bảo mật</a>
                <a href="terms.php">Điều khoản sử dụng</a>
                <a href="contact.php">Liên hệ</a>
            </div>
        </div>
    </footer>

    <script>
        function hien_thi_popup_thanh_cong(tieu_de, noi_dung) {
            // Create popup overlay
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: fadeIn 0.3s ease-out;
            `;
            
            // Create popup content
            const popup = document.createElement('div');
            popup.style.cssText = `
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                max-width: 450px;
                width: 90%;
                text-align: center;
                animation: popupSlideIn 0.4s ease-out;
                position: relative;
            `;
            
            popup.innerHTML = `
                <div style="color: #28a745; font-size: 60px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 style="color: #28a745; margin-bottom: 15px; font-size: 26px; font-weight: 600;">${tieu_de}</h3>
                <p style="color: #555; margin-bottom: 25px; line-height: 1.6; font-size: 16px;">${noi_dung}</p>
                <button onclick="dong_popup()" style="
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 25px;
                    cursor: pointer;
                    font-size: 16px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(40, 167, 69, 0.4)'" 
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(40, 167, 69, 0.3)'">
                    <i class="fas fa-times"></i> Đóng
                </button>
            `;
            
            overlay.appendChild(popup);
            document.body.appendChild(overlay);
            
            // Add CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes popupSlideIn {
                    from {
                        opacity: 0;
                        transform: scale(0.7) translateY(-50px);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1) translateY(0);
                    }
                }
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
            `;
            document.head.appendChild(style);
            
            // Auto close after 6 seconds
            setTimeout(function() {
                dong_popup();
            }, 6000);
        }
        
        function dong_popup() {
            const overlay = document.querySelector('div[style*="position: fixed"][style*="z-index: 9999"]');
            if (overlay) {
                overlay.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(function() {
                    overlay.remove();
                }, 300);
            }
        }

        async function kiem_tra_form() {
            const ma_benh_nhan = document.getElementById('ma_benh_nhan').value;
            const ma_ho_so = document.getElementById('ma_ho_so').value;
            const ngay_hen = document.getElementById('ngay_hen').value;
            const gio_hen = document.getElementById('gio_hen_selected').value;

            const showError = (msg) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Thiếu thông tin',
                    text: msg,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            };

            if (!ma_benh_nhan) { showError('Vui lòng chọn bệnh nhân.'); return false; }
            if (!ma_ho_so) { showError('Vui lòng chọn hồ sơ bệnh nhân.'); return false; }
            if (!ngay_hen) { showError('Vui lòng chọn ngày khám.'); return false; }
            if (!gio_hen) { showError('Vui lòng chọn khung giờ khám.'); return false; }

            const result = await Swal.fire({
                title: 'Xác nhận đặt lịch',
                html: `
                    <p>Bạn có chắc chắn muốn đặt lịch khám?</p>
                    <p><strong>Hệ thống sẽ gửi email thông báo đến bệnh nhân.</strong></p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đặt lịch',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#d33'
            });

            if (result.isConfirmed) {
                document.getElementById('form_dat_lich').submit();
            }
        }

        function lam_moi_form() {
            document.getElementById('ten_benh_nhan').value = '';
            document.getElementById('ngay_sinh_benh_nhan').value = '';
            document.getElementById('gioi_tinh_benh_nhan').value = '';
            document.getElementById('sdt_benh_nhan').value = '';
            
            const chon_ho_so = document.getElementById('ma_ho_so');
            chon_ho_so.innerHTML = '<option value="">-- Chọn hồ sơ --</option>';

        }
    

        document.getElementById('ma_benh_nhan').addEventListener('change', function() {
            const ma_benh_nhan = this.value;

            document.getElementById('ten_benh_nhan').value = '';
            document.getElementById('ngay_sinh_benh_nhan').value = '';
            document.getElementById('gioi_tinh_benh_nhan').value = '';
            document.getElementById('sdt_benh_nhan').value = '';
            
            const chon_ho_so = document.getElementById('ma_ho_so');
            chon_ho_so.innerHTML = '<option value="">-- Chọn hồ sơ --</option>';
   
            if (!ma_benh_nhan) return;

            const danh_sach_benh_nhan = <?php echo json_encode($danh_sach_benh_nhan); ?>;
            const benh_nhan = danh_sach_benh_nhan.find(bn => bn.mabenhnhan === ma_benh_nhan);

            const danh_sach_ho_so = <?php echo json_encode($chosobenhandientu->get_hsba()); ?>;
            const ho_so_benh_nhan = danh_sach_ho_so.filter(hs => hs.mabenhnhan === ma_benh_nhan);

            if (benh_nhan) {
                document.getElementById('ten_benh_nhan').value = benh_nhan.hoten;
                document.getElementById('ngay_sinh_benh_nhan').value = benh_nhan.ngaysinh;
                document.getElementById('gioi_tinh_benh_nhan').value = benh_nhan.gioitinh;
                document.getElementById('sdt_benh_nhan').value = benh_nhan.sdt;
            }

            ho_so_benh_nhan.forEach(hs => {
                const tuy_chon = document.createElement('option');
                tuy_chon.value = hs.mahoso;
                tuy_chon.textContent = `${hs.mahoso} - ${hs.ngaytao}`;
                chon_ho_so.appendChild(tuy_chon);
            });
        });

        document.getElementById('ngay_hen').addEventListener('change', cap_nhat_khung_gio);
        function cap_nhat_khung_gio() {
            const ngay_chon = document.getElementById('ngay_hen').value;
            const ma_benh_nhan = document.getElementById('ma_benh_nhan').value;
            const khungGioKham = document.getElementById('khung_gio_kham');
            // Reset giao diện khung giờ
            const khung_gio_ids = [
                'online_ca_sang','online_ca_chieu','online_ca_toi',
                'offline_ca_sang','offline_ca_chieu','offline_ca_toi'
            ];
            khung_gio_ids.forEach(id => document.getElementById(id).innerHTML = '');
            
            if (!ngay_chon || !ma_benh_nhan) return;

            khungGioKham.style.display = 'block';

            const phieuKham = <?php echo json_encode($phieukham); ?>;
            const lichBacSi = <?php echo json_encode($lichbacsi); ?>;
            const phieuKhamBenhNhan = phieuKham.filter(pk => 
                pk.mabenhnhan === ma_benh_nhan && pk.ngaykham === ngay_chon
            );

            // Nếu bệnh nhân chưa có phiếu khám trong ngày này
            let khungTrong = [];
            console.log(lichBacSi);
            if (phieuKhamBenhNhan.length === 0) {
                // Chưa có phiếu nào => tất cả khung giờ của bác sĩ trong ngày là trống
                khungTrong = lichBacSi.filter(lh => lh.ngaylam === ngay_chon);
            } else {
                // Lấy ra danh sách mã khung giờ đã đặt của bệnh nhân đó trong ngày này
                const khungDaDat = phieuKhamBenhNhan.map(pk => pk.makhunggiokb);
                // Lọc các khung giờ trống trong lịch làm việc của bác sĩ
                khungTrong = lichBacSi.filter(lh => 
                    lh.ngaylam === ngay_chon && !khungDaDat.includes(lh.makhunggiokb)
                );
            }
            if (khungTrong.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Không có khung giờ trống',
                    text: 'Vui lòng chọn ngày khác hoặc liên hệ bác sĩ để sắp xếp lịch.',
                });
                khungGioKham.style.display = 'none';
                return;
            }

            // Nhóm khung giờ theo hình thức và ca
            khungTrong.forEach(kg => {
                const id_target = `${kg.hinhthuclamviec}_${kg.macalamviec === '4' ? 'ca_sang' : kg.macalamviec === '5' ? 'ca_chieu' : 'ca_toi'}`;
                const container = document.getElementById(id_target);

                if (container) {
                    const btn = document.createElement('div');
                    btn.className = 'khung-gio-btn';
                    btn.dataset.makhunggio = kg.makhunggiokb;
                    btn.dataset.hinhthuc = kg.hinhthuclamviec;
                    btn.innerHTML = `
                        <div class="time">${kg.giobatdau.split(':').slice(0, 2).join(':')} - ${kg.gioketthuc.split(':').slice(0, 2).join(':')}</div>
                        <div class="status">${kg.hinhthuclamviec === 'online' ? 'Khám Online' : 'Tại Bệnh Viện'}</div>
                    `;

                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.khung-gio-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        document.getElementById('gio_hen_selected').value = this.dataset.makhunggio;
                        document.getElementById('hinh_thuc_selected').value = this.dataset.hinhthuc;
                    });

                    container.appendChild(btn);
                }
            });
        }
    </script>
</body>
</html>
