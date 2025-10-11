<?php
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    session_start();
    require 'vendor/autoload.php';
    include_once("Assets/config.php");

    include_once('Controllers/cbenhnhan.php');
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/clichkham.php');
    include_once('Controllers/ckhunggio.php');
    include_once('Controllers/chosobenhandientu.php');
    
    include_once('xu-ly-email.php');
    
    // Kiểm tra đăng nhập
    if (!isset($_SESSION["dangnhap"]) || !isset($_SESSION["user"])) {
        echo "<p>Bạn chưa đăng nhập!</p>";
        exit;
    }
    
    // Khởi tạo các đối tượng controller
    $cbenhnhan = new cBenhNhan();
    $clichkham = new cLichKham();
    $cbacsi = new cBacSi();
    $ckhunggiolichkham = new cKhungGio();
    $chosobenhandientu = new cHoSoBenhAnDienTu();
    
    // Lấy thông tin bác sĩ
    $bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
    if (!$bacsi) {
        echo "<p>Không tìm thấy bác sĩ.</p>";
        exit;
    }
    
    $danh_sach_benh_nhan = $cbenhnhan->get_benhnhan_mabacsi($bacsi['mabacsi']);
    $thong_bao = '';
    $loai_thong_bao = 'thanh_cong';

    // Xử lý đặt lịch khám
    if (isset($_POST['btn_xac_nhan'])) {
        if (!empty($_POST['ma_benh_nhan']) && !empty($_POST['ngay_hen']) && !empty($_POST['gio_hen']) && !empty($_POST['ma_ho_so']) && !empty($_POST['hinh_thuc_kham'])) {
            
            $khung_gio = $ckhunggiolichkham->get_khunggiolichkham_makhunggio($_POST['gio_hen']);
            $thong_tin_bn = $cbenhnhan->getbenhnhanbyid($_POST['ma_benh_nhan']);
            $email_bn = decryptData($thong_tin_bn['email']);
            
            if ($clichkham->create_lichkham(
                $_POST['ma_benh_nhan'],
                $bacsi['mabacsi'],
                $_POST['ngay_hen'],
                $_POST['gio_hen'],
                $_POST['hinh_thuc_kham'],
                '10', // trạng thái chờ thanh toán
                $_POST['ma_ho_so'],
                $_POST['ghi_chu'] ?? ''
            )) {
                
                $xu_ly_email = new XuLyEmail();
                $ket_qua_gui_email = $xu_ly_email->gui_email_dat_lich_kham(
                    $email_bn,
                    $_POST['ten_benh_nhan'],
                    $bacsi['hoten'],
                    $_POST['ngay_hen'],
                    $khung_gio[0]['giobatdau'],
                    $_POST['hinh_thuc_kham']
                );
                
                if ($ket_qua_gui_email) {
                    $_SESSION['popup_success'] = true;
                    $_SESSION['popup_title'] = 'Đặt lịch thành công!';
                    $_SESSION['popup_message'] = 'Đã đặt lịch khám và gửi email thông báo đến bệnh nhân. Vui lòng yêu cầu bệnh nhân thanh toán trong vòng 30 phút.';
                    
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
                $_SESSION['thong_bao'] = '<strong>Thất bại!</strong> Đặt lịch khám không thành công. Vui lòng thử lại.';
                $_SESSION['loai_thong_bao'] = 'loi';
                header("Location: " . $_SERVER['REQUEST_URI']);
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
                <h1>Đặt Lịch Khám Bệnh</h1>
            </div>
        </div>

        <?php if (!empty($_SESSION['thong_bao'])): ?>
            <div class="alert alert-<?php echo $_SESSION['loai_thong_bao']; ?>" id="thong_bao_alert">
                <i class="fas fa-<?php echo $_SESSION['loai_thong_bao'] === 'thanh_cong' ? 'check-circle' : ($_SESSION['loai_thong_bao'] === 'canh_bao' ? 'exclamation-triangle' : 'exclamation-circle'); ?>"></i>
                <div>
                    <?php echo $_SESSION['thong_bao']; ?>
                </div>
            </div>
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
                    }, 6000);
                });
            </script>
            <?php 
                unset($_SESSION['thong_bao']); 
                unset($_SESSION['loai_thong_bao']); 
            ?>
        <?php endif; ?>

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
            <a href="?action=taohoso" style="float:right; margin-bottom: 5px;" class="btn-primary btn-small">
                Tạo hồ sơ
            </a>
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
                                <select name="ma_benh_nhan" id="ma_benh_nhan" required>
                                    <option value="">-- Chọn bệnh nhân --</option>
                                    <?php foreach ($danh_sach_benh_nhan as $benh_nhan): ?>
                                    <option value="<?php echo $benh_nhan['mabenhnhan']; ?>"><?php echo $benh_nhan['mabenhnhan'] . ' - ' . $benh_nhan['hoten']; ?></option>
                                    <?php endforeach; ?>
                                </select>
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
                    <h2><i class="fas fa-stethoscope"></i> Hình Thức Khám</h2>
                </div>
                <div class="card-body">
                    <div class="hinh-thuc-kham">
                        <div class="hinh-thuc-item">
                            <input type="radio" name="hinh_thuc_kham" value="online" id="hinh_thuc_online" required>
                            <label for="hinh_thuc_online" class="hinh-thuc-label">
                                <i class="fas fa-video hinh-thuc-icon"></i>
                                <span class="hinh-thuc-text">Khám Online</span>
                            </label>
                        </div>
                        <div class="hinh-thuc-item">
                            <input type="radio" name="hinh_thuc_kham" value="offline" id="hinh_thuc_offline" required>
                            <label for="hinh_thuc_offline" class="hinh-thuc-label">
                                <i class="fas fa-hospital hinh-thuc-icon"></i>
                                <span class="hinh-thuc-text">Khám Tại Bệnh Viện</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Thông Tin Lịch Hẹn</h2>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ngay_hen">Ngày khám <span style="color: red;">*</span></label>
                            <input type="date" id="ngay_hen" name="ngay_hen" required 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                    </div>
                    
                    <div id="khung_gio_container" style="display: none;">
                        <label style="font-weight: 600; color: #333; margin-bottom: 15px; display: block;">
                            Chọn giờ khám <span style="color: red;">*</span>
                        </label>
                        <div id="dang_tai_gio" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Đang tải khung giờ...
                        </div>
                        <div id="danh_sach_gio"></div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="ghi_chu">Ghi chú</label>
                        <textarea name="ghi_chu" id="ghi_chu" placeholder="Nhập các yêu cầu đặc biệt hoặc triệu chứng của bệnh nhân..."></textarea>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 24px; gap: 15px; flex-wrap: wrap;">
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
        // Hàm hiển thị popup thành công
        function hien_thi_popup_thanh_cong(tieu_de, noi_dung) {
            Swal.fire({
                icon: 'success',
                title: tieu_de,
                html: noi_dung,
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#667eea',
                timer: 6000,
                timerProgressBar: true
            });
        }
        
        // Hàm kiểm tra form trước khi submit
        function kiem_tra_form() {
            const ma_benh_nhan = document.getElementById('ma_benh_nhan').value;
            const ma_ho_so = document.getElementById('ma_ho_so').value;
            const hinh_thuc_kham = document.querySelector('input[name="hinh_thuc_kham"]:checked')?.value;
            const ngay_hen = document.getElementById('ngay_hen').value;
            const gio_hen = document.querySelector('input[name="gio_hen"]:checked')?.value;
            
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
            if (!ma_ho_so) {
                showError('Vui lòng chọn hồ sơ bệnh nhân.');
                return false;
            }
            if (!hinh_thuc_kham) {
                showError('Vui lòng chọn hình thức khám.');
                return false;
            }
            if (!ngay_hen) {
                showError('Vui lòng chọn ngày khám.');
                return false;
            }
            if (!gio_hen) {
                showError('Vui lòng chọn giờ khám.');
                return false;
            }

            // Xác nhận đặt lịch
            Swal.fire({
                title: 'Xác nhận đặt lịch',
                html: `
                    <p>Bạn có chắc chắn muốn đặt lịch khám?</p>
                    <p><strong>Hệ thống sẽ gửi email thông báo đến bệnh nhân.</strong></p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đặt lịch',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form_dat_lich').submit();
                }
            });

            return false;
        }
        
        // Hàm làm mới form
        function lam_moi_form() {
            document.getElementById('ten_benh_nhan').value = '';
            document.getElementById('ngay_sinh_benh_nhan').value = '';
            document.getElementById('gioi_tinh_benh_nhan').value = '';
            document.getElementById('sdt_benh_nhan').value = '';
            
            const chon_ho_so = document.getElementById('ma_ho_so');
            chon_ho_so.innerHTML = '<option value="">-- Chọn hồ sơ --</option>';
            
            document.getElementById('khung_gio_container').style.display = 'none';
            document.getElementById('danh_sach_gio').innerHTML = '';
            
            document.querySelectorAll('input[name="hinh_thuc_kham"]').forEach(radio => {
                radio.checked = false;
            });
        }
        
        // Xử lý khi chọn bệnh nhân
        document.getElementById('ma_benh_nhan').addEventListener('change', function() {
            const ma_benh_nhan = this.value;

            document.getElementById('ten_benh_nhan').value = '';
            document.getElementById('ngay_sinh_benh_nhan').value = '';
            document.getElementById('gioi_tinh_benh_nhan').value = '';
            document.getElementById('sdt_benh_nhan').value = '';
            
            const chon_ho_so = document.getElementById('ma_ho_so');
            chon_ho_so.innerHTML = '<option value="">-- Chọn hồ sơ --</option>';

            const chon_gio = document.getElementById('gio_hen');
            chon_gio.innerHTML = '<option value="">-- Chọn giờ --</option>';
            
            document.querySelectorAll('input[name="loai_xet_nghiem"]').forEach(radio => {
                radio.checked = false;
            });
            
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

        // Xử lý khi chọn hình thức khám hoặc ngày
        document.querySelectorAll('input[name="hinh_thuc_kham"]').forEach(radio => {
            radio.addEventListener('change', cap_nhat_khung_gio);
        });
        
        document.getElementById('ngay_hen').addEventListener('change', cap_nhat_khung_gio);
        
        // Hàm xác định ca theo giờ
        function xac_dinh_ca(gio) {
            const [h, m] = gio.split(':').map(Number);
            const phut = h * 60 + m;
            
            if (phut >= 6*60 && phut <= 11*60+30) return 'Sáng';
            if (phut >= 12*60+30 && phut <= 18*60) return 'Chiều';
            if (phut >= 18*60+30 && phut <= 21*60) return 'Tối';
            return 'Khác';
        }
        
        // Hàm cập nhật khung giờ
        function cap_nhat_khung_gio() {
            const hinh_thuc_chon = document.querySelector('input[name="hinh_thuc_kham"]:checked')?.value;
            const ngay_chon = document.getElementById('ngay_hen').value;
            
            const dang_tai_gio = document.getElementById('dang_tai_gio');
            const khung_gio_container = document.getElementById('khung_gio_container');
            const danh_sach_gio = document.getElementById('danh_sach_gio');
            
            danh_sach_gio.innerHTML = '';
            
            if (!hinh_thuc_chon || !ngay_chon) {
                khung_gio_container.style.display = 'none';
                return;
            }
            
            khung_gio_container.style.display = 'block';
            dang_tai_gio.style.display = 'block';
            
            // Giả lập load dữ liệu từ server
            setTimeout(() => {
                const danh_sach_lich_hen = <?php echo json_encode($clichkham->get_lichkham()); ?>;
                const lich_hen_da_chon = danh_sach_lich_hen.filter(lh => 
                   (lh.ngayhen === ngay_chon) && (lh.hinhthuckham === hinh_thuc_chon)
                );
                
                const ma_khung_gio_da_chon = lich_hen_da_chon.map(lh => lh.makhunggio);
                const danh_sach_khung_gio = <?php echo json_encode($ckhunggiolichkham->get_khunggiolichkham()); ?>;
        
                const khung_gio_trong = danh_sach_khung_gio.filter(kg => 
                    !ma_khung_gio_da_chon.includes(kg.makhunggiolichkham)
                );
                
                dang_tai_gio.style.display = 'none';
                
                if (khung_gio_trong.length > 0) {
                    // Gom theo ca
                    const theo_ca = {
                        'Sáng': [],
                        'Chiều': [],
                        'Tối': []
                    };
                    
                    khung_gio_trong.forEach(kg => {
                        const ca = xac_dinh_ca(kg.giobatdau);
                        if (ca !== 'Khác') {
                            theo_ca[ca].push(kg);
                        }
                    });
                    
                    // Hiển thị theo ca
                    ['Sáng', 'Chiều', 'Tối'].forEach(ca => {
                        if (theo_ca[ca].length > 0) {
                            const shift_group = document.createElement('div');
                            shift_group.className = 'shift-group';
                            
                            const shift_title = document.createElement('div');
                            shift_title.className = 'shift-title';
                            
                            let icon = '';
                            if (ca === 'Sáng') icon = '<i class="fas fa-sun"></i>';
                            else if (ca === 'Chiều') icon = '<i class="fas fa-cloud-sun"></i>';
                            else icon = '<i class="fas fa-moon"></i>';
                            
                            shift_title.innerHTML = `${icon} Ca ${ca}`;
                            shift_group.appendChild(shift_title);
                            
                            const time_slots = document.createElement('div');
                            time_slots.className = 'time-slots';
                            
                            theo_ca[ca].forEach(kg => {
                                const time_slot = document.createElement('div');
                                time_slot.className = 'time-slot';
                                
                                const input = document.createElement('input');
                                input.type = 'radio';
                                input.name = 'gio_hen';
                                input.value = kg.makhunggiolichkham;
                                input.id = 'gio_' + kg.makhunggiolichkham;
                                input.required = true;
                                
                                const label = document.createElement('label');
                                label.className = 'time-slot-label';
                                label.htmlFor = 'gio_' + kg.makhunggiolichkham;
                                label.textContent = kg.giobatdau + ' - ' + kg.gioketthuc;
                                
                                time_slot.appendChild(input);
                                time_slot.appendChild(label);
                                time_slots.appendChild(time_slot);
                            });
                            
                            shift_group.appendChild(time_slots);
                            danh_sach_gio.appendChild(shift_group);
                        }
                    });
                } else {
                    danh_sach_gio.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;"><i class="fas fa-exclamation-circle"></i> Không có khung giờ trống cho ngày này. Vui lòng chọn ngày khác.</p>';
                }
            }, 500);
        }
    </script>
</body>
</html>



