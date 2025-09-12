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

    if (isset($_POST['btn_xac_nhan'])) {
        if (!empty($_POST['ma_benh_nhan']) && !empty($_POST['loai_xet_nghiem']) && !empty($_POST['ngay_hen']) && !empty($_POST['gio_hen']) && !empty($_POST['ma_ho_so'])) {
            // Tạo tên file duy nhất cho QR code
            $ten_file_qr = 'qr_' . time() . '.png';
            $duong_dan_luu = 'Assets/img/' . $ten_file_qr;

            $khung_gio = $ckhunggioxetnghiem->get_khunggioxetnghiem_makhunggio($_POST['gio_hen']);
            $loai_xn = $cloaixetnghiem->get_loaixetnghiem_maloaixetnghiem($_POST['loai_xet_nghiem']);
            $thong_tin_bn = $cbenhnhan->getbenhnhanbyid($_POST['ma_benh_nhan']);
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
            
            if ($clichxetnghiem->create_lichxetnghiem($_POST['ma_benh_nhan'],$_POST['loai_xet_nghiem'],$_POST['ngay_hen'],$_POST['gio_hen'],'10',$_POST['ma_ho_so'], $ten_file_qr)) {
                
              //  $ma_lich_hen = $clichxetnghiem->lay_ma_lich_hen_moi_nhat();
                
                $xu_ly_email = new XuLyEmail();
                $ket_qua_gui_email = $xu_ly_email->gui_email_yeu_cau_thanh_toan(
                    'nguyenthanhthuytrang12@gmail.com',
                    'Nguyễn Thanh Thùy Trang',
                    $loai_xn[0]['tenloaixetnghiem'],
                    $_POST['ngay_hen'],
                    $khung_gio[0]['giobatdau'],
                    11
                );
                
                if ($ket_qua_gui_email) {
                    $thong_bao = '<strong>Thành công!</strong> Đã đặt lịch xét nghiệm và gửi email yêu cầu thanh toán đến bệnh nhân.';
                    $loai_thong_bao = 'thanh_cong';
                } else {
                    $thong_bao = '<strong>Cảnh báo!</strong> Đã đặt lịch thành công nhưng không thể gửi email. Vui lòng liên hệ bệnh nhân trực tiếp.';
                    $loai_thong_bao = 'canh_bao';
                }
            } else {
                $thong_bao = '<strong>Thất bại!</strong> Đặt lịch xét nghiệm không thành công. Vui lòng thử lại.';
                $loai_thong_bao = 'loi';
            }
        } else {
            $thong_bao = '<strong>Lỗi!</strong> Vui lòng điền đầy đủ thông tin.';
            $loai_thong_bao = 'loi';
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lịch Xét Nghiệm - Bệnh Viện Hạnh Phúc</title>
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

        <?php if (!empty($thong_bao)): ?>
        <div class="alert alert-<?php echo $loai_thong_bao; ?>">
            <i class="fas fa-<?php echo $loai_thong_bao === 'thanh_cong' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <div>
                <?php echo $thong_bao; ?>
            </div>
        </div>
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
        function kiem_tra_form() {
            const ma_benh_nhan = document.getElementById('ma_benh_nhan').value;
            const ma_ho_so = document.getElementById('ma_ho_so').value;
            const loai_xet_nghiem = document.querySelector('input[name="loai_xet_nghiem"]:checked')?.value;
            const ngay_hen = document.getElementById('ngay_hen').value;
            const gio_hen = document.getElementById('gio_hen').value;
            
            if (!ma_benh_nhan) {
                alert('Vui lòng chọn bệnh nhân.');
                return false;
            }
            
            if (!ma_ho_so) {
                alert('Vui lòng chọn hồ sơ bệnh nhân.');
                return false;
            }
            
            if (!loai_xet_nghiem) {
                alert('Vui lòng chọn loại xét nghiệm.');
                return false;
            }
            
            if (!ngay_hen) {
                alert('Vui lòng chọn ngày xét nghiệm.');
                return false;
            }
            
            if (!gio_hen) {
                alert('Vui lòng chọn giờ xét nghiệm.');
                return false;
            }
            
            return confirm('Bạn có chắc chắn muốn đặt lịch xét nghiệm này không? Hệ thống sẽ gửi email yêu cầu thanh toán đến bệnh nhân.');
        }
        
        function lam_moi_form() {
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
