<?php
require 'vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbenhnhan.php');
include_once('Controllers/chosobenhandientu.php');
include_once('Controllers/cchitiethoso.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/cloaixetnghiem.php');
include_once('Controllers/ckhunggioxetnghiem.php');
include_once('Controllers/clichxetnghiem.php');
include_once("Assets/config.php");

$cphieukhambenh = new cPhieuKhamBenh();
$cbenhnhan = new cBenhNhan();
$chosobenhandientu = new cHoSoBenhAnDienTu();
$cchitiethoso = new cChiTietHoSo();
$cbacsi = new cBacSi();
$cloaixetnghiem = new cLoaiXetNghiem();
$ckhunggioxetnghiem = new cKhungGioXetNghiem();
$clichxetnghiem = new cLichXetNghiem();

$message = "";
$messageType = "";

// Get current doctor information
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);

if(!isset($_GET['maphieukhambenh'])){
    header("Location: ?action=lichhentructuyen");
    exit;
}

$maphieukhambenh = $_GET['maphieukhambenh'];
$phieukham = $cphieukhambenh->getPhieuKhamBenhOfIDPK($maphieukhambenh);

if(!$phieukham || $phieukham == 0){
    header("Location: ?action=lichhentructuyen");
    exit;
}

$mabenhnhan = $phieukham['mabenhnhan'];
$benhnhan = $cbenhnhan->getbenhnhanbyid($mabenhnhan);

// Get or create hoso for this patient and doctor
$hoso_list = $chosobenhandientu->get_mahoso_by_benhnhan_mabacsi($mabenhnhan, $bacsi['mabacsi']);
$mahoso = null;

if($hoso_list && is_array($hoso_list) && count($hoso_list) > 0){
    $mahoso = $hoso_list[0]['mahoso'];
} else {
    // Create new hoso if doesn't exist
    if($chosobenhandientu->create_hosobenhandientu_mabenhnhan($mabenhnhan, $bacsi['mabacsi'])){
        $hosonew = $chosobenhandientu->get_hsba_new($mabenhnhan);
        if($hosonew && count($hosonew) > 0){
            $mahoso = $hosonew[0]['mahoso'];
        }
    }
}

// Get list of test types
$danh_muc_xet_nghiem = $cloaixetnghiem->get_danhmucxetnghiem();

// Handle form submission for completing examination
if(isset($_POST['hoantat_kham'])){
    if($mahoso){
        // Create chitiethoso with optional fields
        $trieuchung = $_POST['trieuchung'] ?? '';
        $chuandoan = $_POST['chuandoan'] ?? '';
        $huongdieutri = $_POST['huongdieutri'] ?? '';
        $ketluan = $_POST['ketluan'] ?? '';
        
        if($cchitiethoso->create_chitiethoso($mahoso, $bacsi['mabacsi'], $trieuchung, $chuandoan, $huongdieutri, null, $ketluan)){
            // Update phieukhambenh status
            if($cphieukhambenh->updateTrangThaiPKB($maphieukhambenh, 'Đã khám')){
                $message = "Hoàn tất khám bệnh thành công!";
                $messageType = "success";
            }
        } else {
            $message = "Có lỗi khi lưu thông tin khám bệnh!";
            $messageType = "error";
        }
    } else {
        $message = "Không tìm thấy hồ sơ bệnh nhân!";
        $messageType = "error";
    }
}

// Handle form submission for creating test appointment
if(isset($_POST['tao_xet_nghiem'])){
    if($mahoso && !empty($_POST['loai_xet_nghiem']) && !empty($_POST['ngay_hen']) && !empty($_POST['gio_hen'])){
        // Create QR code
        $ten_file_qr = 'qr_' . time() . '.png';
        $duong_dan_luu = 'Assets/img/' . $ten_file_qr;
        
        $khung_gio = $ckhunggioxetnghiem->get_khunggioxetnghiem_makhunggio($_POST['gio_hen']);
        $loai_xn = $cloaixetnghiem->get_loaixetnghiem_maloaixetnghiem($_POST['loai_xet_nghiem']);
        
        $builder = new Builder(
            writer: new PngWriter(),
            data: "Họ tên: " . $benhnhan['hoten'] . "\n" .
                "Mã bệnh nhân: " . $mabenhnhan . "\n" .
                "Tên xét nghiệm: " . ($loai_xn && count($loai_xn) > 0 ? $loai_xn[0]['tenloaixetnghiem'] : 'N/A') . "\n" .
                "Ngày xét nghiệm: " . $_POST['ngay_hen'] . "\n" .
                "Giờ xét nghiệm: " . ($khung_gio && count($khung_gio) > 0 ? $khung_gio[0]['giobatdau'] : 'N/A') . "\n" .
                "Bác sĩ: " . $bacsi['hoten']
        );
        $ket_qua_qr = $builder->build();
        file_put_contents($duong_dan_luu, $ket_qua_qr->getString());
        
        if($clichxetnghiem->create_lichxetnghiem($mabenhnhan, $_POST['loai_xet_nghiem'], $_POST['ngay_hen'], $_POST['gio_hen'], '10', $mahoso, $ten_file_qr)){
            $message = "Đã tạo lịch xét nghiệm thành công!";
            $messageType = "success";
        } else {
            $message = "Có lỗi khi tạo lịch xét nghiệm!";
            $messageType = "error";
        }
    } else {
        $message = "Vui lòng điền đầy đủ thông tin xét nghiệm!";
        $messageType = "warning";
    }
}
?>

<style>
.form-section {
    background: white;
    padding: 24px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

.patient-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.info-item {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.info-label {
    font-weight: 600;
    color: #7f8c8d;
    font-size: 12px;
    margin-bottom: 5px;
}

.info-value {
    color: #2c3e50;
    font-size: 14px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-group textarea {
    min-height: 80px;
    resize: vertical;
}

.btn-group {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}
</style>

<div class="container">
    <div class="content-header">
        <h1>Form Khám Bệnh</h1>
    </div>

    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Patient Information -->
    <div class="form-section">
        <h3>Thông Tin Bệnh Nhân</h3>
        <div class="patient-info-grid">
            <div class="info-item">
                <div class="info-label">Mã bệnh nhân</div>
                <div class="info-value"><?php echo $benhnhan['mabenhnhan']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Họ và tên</div>
                <div class="info-value"><?php echo $benhnhan['hoten']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Ngày sinh</div>
                <div class="info-value"><?php echo date('d/m/Y', strtotime($benhnhan['ngaysinh'])); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Giới tính</div>
                <div class="info-value"><?php echo $benhnhan['gioitinh']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Số điện thoại</div>
                <div class="info-value"><?php echo decryptData($benhnhan['sdt']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo decryptData($benhnhan['email']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Mã hồ sơ</div>
                <div class="info-value"><?php echo $mahoso ?? 'Đang tạo...'; ?></div>
            </div>
        </div>
    </div>

    <!-- Examination Form -->
    <form method="POST">
        <div class="form-section">
            <h3>Thông Tin Khám Bệnh</h3>
            
            <div class="form-group">
                <label for="trieuchung">Triệu chứng ban đầu</label>
                <textarea name="trieuchung" id="trieuchung" placeholder="Nhập triệu chứng của bệnh nhân..."><?php echo $_POST['trieuchung'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="chuandoan">Chẩn đoán</label>
                <textarea name="chuandoan" id="chuandoan" placeholder="Nhập chẩn đoán..."><?php echo $_POST['chuandoan'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="huongdieutri">Hướng điều trị</label>
                <textarea name="huongdieutri" id="huongdieutri" placeholder="Nhập hướng điều trị..."><?php echo $_POST['huongdieutri'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="ketluan">Kết luận</label>
                <textarea name="ketluan" id="ketluan" placeholder="Nhập kết luận..."><?php echo $_POST['ketluan'] ?? ''; ?></textarea>
            </div>

            <div class="btn-group">
                <a href="?action=lichhentructuyen" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <button type="submit" name="hoantat_kham" class="btn-primary">
                    <i class="fas fa-check"></i> Hoàn tất khám
                </button>
            </div>
        </div>
    </form>

    <!-- Test Appointment Form -->
    <?php if($mahoso): ?>
    <form method="POST">
        <div class="form-section">
            <h3>Tạo Lịch Xét Nghiệm</h3>
            
            <div class="form-group">
                <label for="loai_xet_nghiem">Loại xét nghiệm <span style="color: red;">*</span></label>
                <select name="loai_xet_nghiem" id="loai_xet_nghiem" required>
                    <option value="">-- Chọn loại xét nghiệm --</option>
                    <?php 
                    if($danh_muc_xet_nghiem && is_array($danh_muc_xet_nghiem)){
                        foreach($danh_muc_xet_nghiem as $xn){
                            echo '<option value="'.$xn['maloaixetnghiem'].'">'.$xn['tenloaixetnghiem'].' - '.number_format($xn['giaxetnghiem'], 0, ',', '.').' VND</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ngay_hen">Ngày hẹn <span style="color: red;">*</span></label>
                <input type="date" name="ngay_hen" id="ngay_hen" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="gio_hen">Khung giờ <span style="color: red;">*</span></label>
                <select name="gio_hen" id="gio_hen" required>
                    <option value="">-- Chọn khung giờ --</option>
                    <?php
                    $khunggio_list = $ckhunggioxetnghiem->get_khunggioxetnghiem();
                    if($khunggio_list && is_array($khunggio_list)){
                        foreach($khunggio_list as $kg){
                            echo '<option value="'.$kg['makhunggioxetnghiem'].'">'.$kg['giobatdau'].' - '.$kg['gioketthuc'].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" name="tao_xet_nghiem" class="btn-success">
                    <i class="fas fa-plus"></i> Tạo lịch xét nghiệm
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>
