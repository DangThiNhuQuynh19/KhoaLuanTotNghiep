<?php
    require 'vendor/autoload.php';
    use Endroid\QrCode\Builder\Builder;
    use Endroid\QrCode\Writer\PngWriter;
    include_once("Assets/config.php");

    include_once('Controllers/cbenhnhan.php');
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/cloaixetnghiem.php');
    include_once('Controllers/ckhunggioxetnghiem.php');
    include_once('Controllers/chosobenhandientu.php');
    include_once('Controllers/clichxetnghiem.php');
    
    include_once('xu-ly-email.php');
    include_once('xu-ly-thanh-toan.php');
    
    // Khởi tạo các đối tượng controller
    $cbenhnhan = new cBenhNhan();
    $clichxetnghiem = new cLichXetNghiem();
    $cloaixetnghiem = new cLoaiXetNghiem();
    $ckhunggioxetnghiem = new cKhungGioXetNghiem();
    $chosobenhandientu = new cHoSoBenhAnDienTu();
    $cbacsi = new cBacSi();
    $bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
    $danh_sach_benh_nhan = $cbenhnhan->get_benhnhan_mabacsi($bacsi['mabacsi']);
    $danh_muc_xet_nghiem = $cloaixetnghiem->get_danhmucxetnghiem();
    $thong_bao = '';
    $loai_thong_bao = 'thanh_cong';

    if(isset($_GET['id'])){
        $benhnhan = $cbenhnhan->get_benhnhan_mabenhnhan($_GET['id']);
    }
    
    if (isset($_POST['btn_xac_nhan'])) {
        if (!empty($_POST['ma_benh_nhan']) && !empty($_POST['loai_xet_nghiem']) && !empty($_POST['ngay_hen']) && !empty($_POST['gio_hen'])) {
            // Tạo tên file duy nhất cho QR code
            $hosobenhnhan = $chosobenhandientu ->get_hoso_machuyenkhoa($_POST['ma_benh_nhan'],$bacsi['machuyenkhoa']);
            $ten_file_qr = 'qr_' . time() . '.png';
            $duong_dan_luu = 'Assets/img/' . $ten_file_qr;

            $khung_gio = $ckhunggioxetnghiem->get_khunggioxetnghiem_makhunggio($_POST['gio_hen']);
            $loai_xn = $cloaixetnghiem->get_loaixetnghiem_maloaixetnghiem($_POST['loai_xet_nghiem']);
            $thong_tin_bn = $cbenhnhan->getbenhnhanbyid($_POST['ma_benh_nhan']);
            $email_bn = decryptData($thong_tin_bn['email']);
            // Tạo QR code với thông tin tiếng Việt
            $builder = new Builder(
                writer: new PngWriter(),
                data: $du_lieu_qr = "Họ tên: " . $_POST['ten_benh_nhan'] . "\n" .
                "SĐT: " . $_POST['sdt_benh_nhan'] . "\n" .
                "Tên xét nghiệm: ".$loai_xn[0]['tenloaixetnghiem']. "\n" .
                "Ngày xét nghiệm: " . $_POST['ngay_hen'] . "\n" .
                "Giờ xét nghiệm: " . $khung_gio[0]['giobatdau']. "\n" .
                "Bác sĩ đặt lịch: ".$bacsi['hoten']
            );
            $ket_qua_qr = $builder->build();
            file_put_contents($duong_dan_luu, $ket_qua_qr->getString());
            
            if(isset($hosobenhnhan)){
                if ($clichxetnghiem->create_lichxetnghiem($_POST['ma_benh_nhan'],$_POST['loai_xet_nghiem'],$_POST['ngay_hen'],$_POST['gio_hen'],'10',$hosobenhnhan[0]['mahoso'], $ten_file_qr)) {  
                      $xu_ly_email = new XuLyEmail();
                      $ket_qua_gui_email = $xu_ly_email->gui_email_yeu_cau_thanh_toan(
                          'nguyentrang2642003@gmail.com',
                          $_POST['ten_benh_nhan'],
                          $loai_xn[0]['tenloaixetnghiem'],
                          $_POST['ngay_hen'],
                          $khung_gio[0]['giobatdau'],
                          11
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
                } else {
                      $_SESSION['thong_bao'] = '<strong>Thất bại!</strong> Đặt lịch xét nghiệm không thành công. Vui lòng thử lại.';
                      $_SESSION['loai_thong_bao'] = 'loi';
                      header("Location: " . $_SERVER['REQUEST_URI']);
                      exit();
                      
                }
            }else{
                $_SESSION['thong_bao'] = '<strong>Thất bại!</strong> Bệnh nhân chưa có hồ sơ. Vui lòng tạo hồ sơ.';
                $_SESSION['loai_thong_bao'] = 'loi';
                header("Location: ?action=taohoso&id=" . $_POST['ma_benh_nhan'] . "&redirect=" . urlencode($_SERVER['REQUEST_URI']));
                exit();
            }
        } else {
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

    </style>
</head>
<body>
    <main class="container">
        <div class="content-header">
            <div class="back-button">
                <a href="index.php" class="btn-icon">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1>Đặt Lịch Xét Nghiệm</h1>
            </div>
        </div>

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
                <strong>Lưu ý:</strong> Sau khi đặt lịch, bệnh nhân sẽ nhận email yêu cầu thanh toán. Nếu không thanh toán trong 30 phút, lịch hẹn sẽ tự động bị hủy.
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
                                <label for="ma_benh_nhan">Mã bệnh nhân</label>
                                <?php if (isset($_GET['id'])): ?>
                                    <input type="hidden" name="ma_benh_nhan" id="ma_benh_nhan" value="<?php echo $benhnhan[0]['mabenhnhan']; ?>">
                                    <input type="text" value="<?php echo $benhnhan[0]['mabenhnhan']; ?>" readonly>
                                <?php else: ?>
                                    <select name="ma_benh_nhan" id="ma_benh_nhan" required>
                                        <option value="">-- Chọn bệnh nhân --</option>
                                        <?php foreach ($danh_sach_benh_nhan as $benh_nhan): ?>
                                            <option value="<?php echo $benh_nhan['mabenhnhan']; ?>">
                                                <?php echo $benh_nhan['mabenhnhan'] . ' - ' . $benh_nhan['hoten']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label for="ten_benh_nhan">Họ và tên</label>
                                <input type="text" id="ten_benh_nhan" name="ten_benh_nhan"
                                    value="<?php echo isset($benhnhan) ? $benhnhan[0]['hoten'] : ''; ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="ngay_sinh_benh_nhan">Ngày sinh</label>
                                <input type="text" id="ngay_sinh_benh_nhan" name="ngay_sinh_benh_nhan"
                                    value="<?php echo isset($benhnhan) ? $benhnhan[0]['ngaysinh'] : ''; ?>" readonly>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label for="gioi_tinh_benh_nhan">Giới tính</label>
                                <input type="text" id="gioi_tinh_benh_nhan" name="gioi_tinh_benh_nhan"
                                    value="<?php echo isset($benhnhan) ? $benhnhan[0]['gioitinh'] : ''; ?>" readonly>
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label for="sdt_benh_nhan">Số điện thoại</label>
                                <input type="text" id="sdt_benh_nhan" name="sdt_benh_nhan"
                                    value="<?php echo isset($benhnhan) ? decryptData($benhnhan[0]['sdt']) : ''; ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h2>Chọn Xét Nghiệm</h2>
                </div>
                <div class="card-body">
                    <div class="test-categories">
                        <?php foreach ($danh_muc_xet_nghiem as $chi_muc => $danh_muc): ?>
                        <div class="category-item">
                            <div class="category-header <?php echo $chi_muc === 0 ? 'active' : ''; ?>" onclick="dong_mo_danh_muc(this)">
                                <h3><?php echo $danh_muc['tendanhmuc']; ?></h3>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <?php
                                $chi_tiet_danh_muc = $cloaixetnghiem->get_chitietdanhmuc_madanhmuc($danh_muc['madanhmuc']);
                            ?>
                            <div class="category-tests" <?php echo $chi_muc === 0 ? 'style="display: block;"' : ''; ?>>
                                <?php foreach ($chi_tiet_danh_muc as $loai_xn): ?>
                                <div class="test-item">
                                    <input type="radio" name="loai_xet_nghiem" value="<?php echo $loai_xn['maloaixetnghiem']; ?>" id="xet_nghiem_<?php echo $loai_xn['maloaixetnghiem']; ?>" class="test-checkbox">
                                    <label for="xet_nghiem_<?php echo $loai_xn['maloaixetnghiem']; ?>" class="test-info">
                                        <div class="test-name"><?php echo $loai_xn['tenloaixetnghiem']; ?></div>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
                                <label for="ngay_hen">Ngày xét nghiệm</label>
                                <input type="date" id="ngay_hen" name="ngay_hen" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="gio_hen">Giờ xét nghiệm</label>
                                <select id="gio_hen" name="gio_hen" required>
                                    <option value="">-- Chọn giờ --</option>
                                </select>
                                <div id="dang_tai_gio" style="display: none; margin-top: 5px;">
                                    <i class="fas fa-spinner fa-spin"></i> Đang tải khung giờ...
                                </div>
                            </div>
                        </div>
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

        function kiem_tra_form() {
            const ma_benh_nhan = document.getElementById('ma_benh_nhan').value;
            const loai_xet_nghiem = document.querySelector('input[name="loai_xet_nghiem"]:checked')?.value;
            const ngay_hen = document.getElementById('ngay_hen').value;
            const gio_hen = document.getElementById('gio_hen').value;
            
            
            // Hàm hiển thị popup lỗi
            const showError = (msg) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Thiếu thông tin',
                    text: msg,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            };

            if (!ma_benh_nhan) {
                showError('Vui lòng chọn bệnh nhân.');
                return false;
            }
            if (!loai_xet_nghiem) {
                showError('Vui lòng chọn loại xét nghiệm.');
                return false;
            }
            if (!ngay_hen) {
                showError('Vui lòng chọn ngày xét nghiệm.');
                return false;
            }
            if (!gio_hen) {
                showError('Vui lòng chọn giờ xét nghiệm.');
                return false;
            }

            // Xác nhận đặt lịch (popup xác nhận đẹp hơn confirm)
            Swal.fire({
                title: 'Xác nhận đặt lịch',
                html: `
                    <p>Bạn có chắc chắn muốn đặt lịch xét nghiệm?</p>
                    <p><strong>Hệ thống sẽ gửi email yêu cầu thanh toán đến bệnh nhân.</strong></p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đặt lịch',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form_dat_lich').submit();
                }
            });

            return false; // Ngăn submit form ngay lập tức
        }
        
        function lam_moi_form() {
            document.getElementById('ten_benh_nhan').value = '';
            document.getElementById('ngay_sinh_benh_nhan').value = '';
            document.getElementById('gioi_tinh_benh_nhan').value = '';
            document.getElementById('sdt_benh_nhan').value = '';
            
            const chon_gio = document.getElementById('gio_hen');
            chon_gio.innerHTML = '<option value="">-- Chọn giờ --</option>';
            
            document.querySelectorAll('input[name="loai_xet_nghiem"]').forEach(radio => {
                radio.checked = false;
            });
        }
        
        function dong_mo_danh_muc(element) {
            element.classList.toggle('active');
            const danh_sach_xet_nghiem = element.nextElementSibling;
            if (danh_sach_xet_nghiem.style.display === 'block') {
                danh_sach_xet_nghiem.style.display = 'none';
            } else {
                danh_sach_xet_nghiem.style.display = 'block';
            }
        }

        const selectMaBN = document.getElementById('ma_benh_nhan');
        if (selectMaBN) {
            selectMaBN.addEventListener('change', function() {
                const ma_benh_nhan = this.value;

                document.getElementById('ten_benh_nhan').value = '';
                document.getElementById('ngay_sinh_benh_nhan').value = '';
                document.getElementById('gioi_tinh_benh_nhan').value = '';
                document.getElementById('sdt_benh_nhan').value = '';
                
                const chon_gio = document.getElementById('gio_hen');
                chon_gio.innerHTML = '<option value="">-- Chọn giờ --</option>';
                
                document.querySelectorAll('input[name="loai_xet_nghiem"]').forEach(radio => {
                    radio.checked = false;
                });
                
                if (!ma_benh_nhan) return;

                const danh_sach_benh_nhan = <?php echo json_encode($danh_sach_benh_nhan); ?>;
                const benh_nhan = danh_sach_benh_nhan.find(bn => bn.mabenhnhan === ma_benh_nhan);

                if (benh_nhan) {
                    document.getElementById('ten_benh_nhan').value = benh_nhan.hoten;
                    document.getElementById('ngay_sinh_benh_nhan').value = benh_nhan.ngaysinh;
                    document.getElementById('gioi_tinh_benh_nhan').value = benh_nhan.gioitinh;
                    document.getElementById('sdt_benh_nhan').value = benh_nhan.sdt;
                }
            });
        }
        document.querySelectorAll('input[name="loai_xet_nghiem"]').forEach(radio => {
            radio.addEventListener('change', cap_nhat_khung_gio);
        });
        
        document.getElementById('ngay_hen').addEventListener('change', cap_nhat_khung_gio);
        
        function cap_nhat_khung_gio() {
            const loai_xet_nghiem_chon = document.querySelector('input[name="loai_xet_nghiem"]:checked')?.value;
            const ngay_chon = document.getElementById('ngay_hen').value;
            
            const dang_tai_gio = document.getElementById('dang_tai_gio');
            const chon_gio = document.getElementById('gio_hen');
            
            chon_gio.innerHTML = '<option value="">-- Chọn giờ --</option>';
            
            if (!loai_xet_nghiem_chon || !ngay_chon) return;
            
            dang_tai_gio.style.display = 'block';
            
            const danh_sach_lich_hen = <?php echo json_encode($clichxetnghiem->get_lichxetnghiem()); ?>;
            const lich_hen_da_chon = danh_sach_lich_hen.filter(lh => 
               (lh.ngayhen === ngay_chon) && (lh.maloaixetnghiem === loai_xet_nghiem_chon)
            );
            
            const ma_khung_gio_da_chon = lich_hen_da_chon.map(lh => lh.makhunggio);
            const danh_sach_khung_gio = <?php echo json_encode($ckhunggioxetnghiem->get_khunggioxetnghiem()); ?>;
    
            const khung_gio_trong = danh_sach_khung_gio.filter(kg => 
                !ma_khung_gio_da_chon.includes(kg.makhunggioxetnghiem)
            );
            dang_tai_gio.style.display = 'none';
            
            if (khung_gio_trong.length > 0) {
                khung_gio_trong.forEach(kg => {
                    const tuy_chon = document.createElement('option');
                    tuy_chon.value = kg.makhunggioxetnghiem;
                    tuy_chon.textContent = kg.giobatdau;
                    chon_gio.appendChild(tuy_chon);
                });
            } else {
                const tuy_chon = document.createElement('option');
                tuy_chon.value = "";
                tuy_chon.textContent = "Không có khung giờ trống cho ngày này";
                tuy_chon.disabled = true;
                chon_gio.appendChild(tuy_chon);
                
                alert("Không có khung giờ trống cho ngày này. Vui lòng chọn ngày khác.");
            }
        }
    </script>
</body>
</html>
