<?php
require 'vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

include_once("Assets/config.php");
include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/cthuoc.php');
include_once('Controllers/cloaixetnghiem.php');
include_once('Controllers/ckhunggioxetnghiem.php');
include_once('Controllers/clichxetnghiem.php');
include_once('Controllers/chosobenhandientu.php');
include_once('Controllers/cchuyenkhoa.php');
include_once('Controllers/cdonthuoc.php');
include_once('Controllers/cchitietdonthuoc.php');
include_once('Controllers/cchitiethoso.php');
include_once('Controllers/cbenhnhan.php');

$cbacsi = new cBacSi();
$cphieukhambenh = new cPhieuKhamBenh();
$cthuoc = new cThuoc();
$cloaixetnghiem = new cLoaiXetNghiem();
$ckhunggioxetnghiem = new cKhungGioXetNghiem();
$clichxetnghiem = new cLichXetNghiem();
$chosobenhandientu = new cHoSoBenhAnDienTu();
$cchuyenkhoa = new cChuyenKhoa();
$cdonthuoc = new cDonThuoc();
$cchitietdonthuoc = new cChiTietDonThuoc();
$cchitiethoso = new cChiTietHoSo();
$cbenhnhan = new cBenhnhan();

// Lấy thông tin bác sĩ hiện tại
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
$chuyenkhoa_bacsi = $cchuyenkhoa->get_chuyenkhoa_mabacsi($bacsi['mabacsi']);

// Lấy danh sách thuốc và loại xét nghiệm
$thuoc = $cthuoc->get_thuoc();
$loaixetnghiem = $cloaixetnghiem->get_loaixetnghiem();
$khunggioxetnghiem = $ckhunggioxetnghiem->get_khunggioxetnghiem();
$all_lichxetnghiem = $clichxetnghiem->get_lichxetnghiem();

$message = "";
$messageType = "";

// Xử lý form cập nhật hồ sơ khi submit
if(isset($_POST['btnHoanTat'])) {
    // Sanitize and validate input data
    $maphieukhambenh = isset($_POST['maphieukhambenh']) ? trim(htmlspecialchars($_POST['maphieukhambenh'], ENT_QUOTES, 'UTF-8')) : '';
    $mabenhnhan = isset($_POST['mabenhnhan']) ? trim(htmlspecialchars($_POST['mabenhnhan'], ENT_QUOTES, 'UTF-8')) : '';
    $mahoso = isset($_POST['mahoso']) ? trim(htmlspecialchars($_POST['mahoso'], ENT_QUOTES, 'UTF-8')) : '';
    $madonthuoc = NULL;
    
    // Validate that the appointment belongs to the current doctor
    $phieukham = $cphieukhambenh->getPhieuKhamBenhOfIDPK($maphieukhambenh);
    if (!$phieukham || $phieukham['mabacsi'] != $bacsi['mabacsi']) {
        $message = 'Bạn không có quyền cập nhật phiếu khám này!';
        $messageType = 'error';
    } else {
        // Xử lý đơn thuốc nếu có
        if(isset($_POST['medications']) && !empty($_POST['medications'])){
            if($cdonthuoc->create_donthuoc()){
                $donthuoc_new = $cdonthuoc->get_donthuoc_new();
                $madonthuoc = $donthuoc_new[0]['madonthuoc'];
                foreach($_POST['medications'] as $thuoc_item){
                    // Sanitize medication data
                    $mathuoc_sanitized = isset($thuoc_item['mathuoc']) ? trim(htmlspecialchars($thuoc_item['mathuoc'], ENT_QUOTES, 'UTF-8')) : '';
                    $lieudung_sanitized = isset($thuoc_item['lieudung']) ? trim(htmlspecialchars($thuoc_item['lieudung'], ENT_QUOTES, 'UTF-8')) : '';
                    $thoigianuong_sanitized = isset($thuoc_item['thoigianuong']) ? trim(htmlspecialchars($thuoc_item['thoigianuong'], ENT_QUOTES, 'UTF-8')) : '';
                    $songayuong_sanitized = isset($thuoc_item['songayuong']) ? trim(htmlspecialchars($thuoc_item['songayuong'], ENT_QUOTES, 'UTF-8')) : '';
                    
                    $cchitietdonthuoc->create_chitietdonthuoc(
                        $madonthuoc,
                        $mathuoc_sanitized,
                        $lieudung_sanitized,
                        $thoigianuong_sanitized,
                        $songayuong_sanitized
                    );
                }
            }
        }
        
        // Sanitize test scheduling data
        $test_sanitized = isset($_POST['test']) ? trim(htmlspecialchars($_POST['test'], ENT_QUOTES, 'UTF-8')) : '';
        $appointmentDate_sanitized = isset($_POST['appointmentDate']) ? trim(htmlspecialchars($_POST['appointmentDate'], ENT_QUOTES, 'UTF-8')) : '';
        $appointmentTime_sanitized = isset($_POST['appointmentTime']) ? trim(htmlspecialchars($_POST['appointmentTime'], ENT_QUOTES, 'UTF-8')) : '';
        
        // Xử lý lịch xét nghiệm nếu có
        if (!empty($mabenhnhan) && !empty($test_sanitized) && !empty($appointmentDate_sanitized) && !empty($appointmentTime_sanitized) && !empty($mahoso)) {
            // Validate date format and check if it's a valid calendar date
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate_sanitized)) {
                $dateParts = explode('-', $appointmentDate_sanitized);
                if (checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
                    $filename = 'qr_' . time() . '.png';
                    $savePath = 'Assets/img/' . $filename;

                    $kg = $ckhunggioxetnghiem->get_khunggioxetnghiem_makhunggio($appointmentTime_sanitized);
                    $loai = $cloaixetnghiem->get_loaixetnghiem_maloaixetnghiem($test_sanitized);
                    $benhnhan_info = $chosobenhandientu->get_benhnhan_mahoso($mahoso);
                    
                    if ($kg && $loai && $benhnhan_info) {
                        // Tạo QR code - using masked phone number for privacy
                        $maskedPhone = substr(decryptData($benhnhan_info[0]['sdt']), 0, 4) . '****' . substr(decryptData($benhnhan_info[0]['sdt']), -3);
                        $builder = new Builder(
                            writer: new PngWriter(),
                            data: $data = "Họ tên: " . $benhnhan_info[0]['hoten'] . "\n" .
                            "SĐT: " . $maskedPhone . "\n" .
                            "Tên xét nghiệm: " . $loai[0]['tenloaixetnghiem'] . "\n" .
                            "Ngày xét nghiệm: " . $appointmentDate_sanitized . "\n" .
                            "Giờ xét nghiệm: " . $kg[0]['giobatdau'] . "\n" .
                            "Bác sĩ đặt lịch: " . $bacsi['hoten']
                        );
                        $result = $builder->build();
                        file_put_contents($savePath, $result->getString());
                        
                        $clichxetnghiem->create_lichxetnghiem($mabenhnhan, $test_sanitized, $appointmentDate_sanitized, $appointmentTime_sanitized, 'Đã đặt lịch', $mahoso, $filename);
                    }
                }
            }
        }
        
        // Sanitize diagnosis data
        $trieuchung_sanitized = isset($_POST['trieuchung']) ? trim(htmlspecialchars($_POST['trieuchung'], ENT_QUOTES, 'UTF-8')) : '';
        $chandoan_sanitized = isset($_POST['chandoan']) ? trim(htmlspecialchars($_POST['chandoan'], ENT_QUOTES, 'UTF-8')) : '';
        $huongdieutri_sanitized = isset($_POST['huongdieutri']) ? trim(htmlspecialchars($_POST['huongdieutri'], ENT_QUOTES, 'UTF-8')) : '';
        $ketluan_sanitized = isset($_POST['ketluan']) ? trim(htmlspecialchars($_POST['ketluan'], ENT_QUOTES, 'UTF-8')) : '';
        
        // Server-side validation for required field
        if (empty($ketluan_sanitized)) {
            $message = 'Vui lòng nhập kết luận!';
            $messageType = 'error';
        } else {
            // Tạo chi tiết hồ sơ
            if($cchitiethoso->create_chitiethoso($mahoso, $bacsi['mabacsi'], $trieuchung_sanitized, $chandoan_sanitized, $huongdieutri_sanitized, $madonthuoc, $ketluan_sanitized)){
                // Cập nhật trạng thái phiếu khám bệnh thành 'Đã khám' (mã 8)
                $cphieukhambenh->updateTrangThaiPKB($maphieukhambenh, 'Đã khám');
                $message = 'Cập nhật hồ sơ và hoàn tất khám thành công!';
                $messageType = 'success';
            } else {
                $message = 'Cập nhật hồ sơ thất bại, vui lòng thử lại!';
                $messageType = 'error';
            }
        }
    }
}

// Khởi tạo giá trị mặc định
$lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);

// Lấy dữ liệu tìm kiếm trước đó
$tukhoa = $_POST['tukhoa'] ?? '';
$trangthai = $_POST['trangthai'] ?? '';
$ngay = $_POST['ngay'] ?? '';
$homnay_checked = isset($_POST['homnay']) ? 'checked' : '';

// Checkbox Hôm nay
if(isset($_POST['homnay'])){
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_homnay($bacsi['mabacsi'], date('Y-m-d'));
}

// Tìm kiếm
if(isset($_POST["btntimkiem"])){
    $lichkham_list = $cphieukhambenh->search_phieukhamonl($tukhoa, $trangthai, $ngay, $bacsi['mabacsi']);
}

// Bỏ tìm kiếm
if(isset($_POST["btnbo"])){
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);
    $tukhoa = $trangthai = $ngay = '';
    $homnay_checked = '';
    $_POST = [];
}
?>
<style>
/* Basic styles */
.btn-secondary { background:#f0f0f0; color:#333; border:1px solid #ccc; padding:8px 12px; border-radius:6px; cursor:pointer; }
.btn-primary { background:#007bff; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.btn-success { background:#28a745; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.btn-danger { background:#dc3545; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.btn-small { padding:6px 8px; font-size:13px; }
.status-pending { color:orange; font-weight:bold;}
.status-completed { color:green; font-weight:bold;}
.status-canceled { color:red; font-weight:bold;}

/* Modal styles */
.modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.4); }
.modal-content { background:#fff; margin:40px auto; padding:20px; border-radius:6px; width:900px; max-width:96%; position:relative; }
.modal .close { position:absolute; right:12px; top:8px; font-size:22px; cursor:pointer; color:#666; }

/* Tabs (update modal) */
.update-tabs .tab-header { margin-bottom: 8px; display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.update-tab-link { padding:8px 12px; border-radius:4px 4px 0 0; background:#f5f5f5; color:#333; text-decoration:none; border-bottom:2px solid transparent; cursor:pointer; }
.update-tab-link.active { background:#fff; border-bottom:2px solid #007bff; color:#007bff; }
.update-tab-content { display:none; padding:14px; background:#fff; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.08); }
.update-tab-content.active { display:block; }

.form-row { display:flex; gap:10px; margin-bottom:10px; flex-wrap:wrap; }
.form-group { flex:1; display:flex; flex-direction:column; }
.form-group label { font-weight:600; margin-bottom:6px; }
.medication-table, .data-table { width:100%; border-collapse:collapse; margin-top:10px; }
.medication-table th, .medication-table td, .data-table th, .data-table td { border:1px solid #e6e6e6; padding:8px; text-align:left; }
.action-buttons { display:flex; gap:6px; }

/* Alert styles */
.alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
.alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="container">
<div class="content-header">
    <h1>Quản lý lịch hẹn trực tuyến</h1>
</div>

<?php if(!empty($message)): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="tabs">
    <div class="tab-content">
        <!-- Form tìm kiếm -->
        <div class="card">
            <div class="card-header">
                <h2>Tìm kiếm lịch hẹn</h2>
            </div>
            <div class="card-body">
                <form class="search-form" method="POST">
                    <div class="search-grid">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="tukhoa" placeholder="Tìm theo tên bệnh nhân, mã phiếu..."
                                value="<?php echo htmlspecialchars($tukhoa); ?>">
                        </div>
                        
                        <div class="form-group">
                            <select name="trangthai">
                                <option value="">Trạng thái</option>
                                <option value="chưa khám" <?php if($trangthai=='chưa khám') echo 'selected'; ?>>Chưa khám</option>
                                <option value="đã khám" <?php if($trangthai=='đã khám') echo 'selected'; ?>>Đã khám</option>
                                <option value="đã hủy" <?php if($trangthai=='đã hủy') echo 'selected'; ?>>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <input type="date" name="ngay" value="<?php echo $ngay; ?>">
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 10px;">
                        <button type="submit" class="btn-primary" name="btntimkiem">Tìm kiếm</button>
                        <button type="submit" class="btn-danger" name="btnbo"><i class="fas fa-times"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Checkbox Hôm nay -->
        <form method="POST" style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
            <input type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php echo $homnay_checked; ?>>
            <label for="homnay" style="margin-left: 5px;"><b>Hôm nay</b></label>
        </form>

        <!-- Bảng lịch hẹn -->
        <div class="card">
            <div class="card-body no-padding">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày khám</th>
                            <th>Ca làm việc</th>
                            <th>Bệnh nhân</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($lichkham_list){
                            foreach ($lichkham_list as $i) {
                                switch ($i['tentrangthai']){
                                    case 'Chưa khám': $statusClass='status-pending'; break;
                                    case 'Đã khám': $statusClass='status-completed'; break;
                                    case 'Đã hủy': $statusClass='status-canceled'; break;
                                    default: $statusClass='';
                                }
                                
                                // Kiểm tra bệnh nhân đã có hồ sơ của chuyên khoa này chưa
                                $hoso_chuyenkhoa = $chosobenhandientu->get_hoso_machuyenkhoa($i['mabenhnhan'], $chuyenkhoa_bacsi['machuyenkhoa']);
                                $has_hoso = ($hoso_chuyenkhoa && is_array($hoso_chuyenkhoa) && count($hoso_chuyenkhoa) > 0);
                                
                                // Nếu có hồ sơ, lấy mã hồ sơ
                                $mahoso = $has_hoso ? $hoso_chuyenkhoa[0]['mahoso'] : '';

                                echo '<tr>';
                                echo '<td>'.$i['maphieukhambenh'].'</td>';
                                echo '<td>'.date('d/m/Y', strtotime($i['ngaykham'])).'</td>';
                                echo '<td>'.$i['giobatdau'].'-'.$i['gioketthuc'].'</td>';
                                echo '<td>'.$i['hoten'].'</td>';
                                echo '<td>'.number_format($i['giakham'],0,',','.').' VND</td>';
                                echo '<td><span class="status-badge '.$statusClass.'">'.$i['tentrangthai'].'</span></td>';
                                echo '<td>';
                                if($i['tentrangthai']=='Chưa khám'){
                                    echo '<a class="btn-primary btn-small" href="?action=tinnhan&id='.$i['mabenhnhan'].'"><i class="fas fa-comment-medical"></i> Nhắn tin</a> ';
                                    
                                    if($has_hoso) {
                                        // Nếu đã có hồ sơ, hiển thị nút mở modal cập nhật
                                        $appointmentData = json_encode([
                                            'maphieukhambenh' => $i['maphieukhambenh'],
                                            'mabenhnhan' => $i['mabenhnhan'],
                                            'mahoso' => $mahoso,
                                            'hoten' => $i['hoten']
                                        ], JSON_HEX_APOS|JSON_HEX_QUOT);
                                        echo '<button type="button" class="btn-success btn-small" onclick=\'openCompleteModal('.$appointmentData.')\'><i class="fas fa-check"></i> Hoàn tất khám</button>';
                                    } else {
                                        // Nếu chưa có hồ sơ, điều hướng đến trang tạo hồ sơ
                                        echo '<a class="btn-success btn-small" href="?action=taohoso&mabenhnhan='.$i['mabenhnhan'].'&maphieukhambenh='.$i['maphieukhambenh'].'"><i class="fas fa-plus"></i> Tạo hồ sơ & Khám</a>';
                                    }
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" style="text-align:center; color:gray;">Không có lịch hẹn</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Hoàn tất khám -->
<div id="modalcapnhathoso" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCompleteModal()">&times;</span>
        <h2>Cập nhật hồ sơ & hoàn tất khám</h2>

        <form id="hoantatForm" method="POST">
            <input type="hidden" name="maphieukhambenh" id="maphieu_input" value="">
            <input type="hidden" name="mabenhnhan" id="mabenh_input" value="">
            <input type="hidden" name="mahoso" id="mahoso_input" value="">

            <div class="tabs update-tabs">
                <div class="tab-header">
                    <a class="update-tab-link active" onclick="openUpdateTab(event,'update-prescription')">Thêm đơn thuốc</a>
                    <a class="update-tab-link" onclick="openUpdateTab(event,'update-test')">Thêm lịch xét nghiệm</a>
                    <a class="update-tab-link" onclick="openUpdateTab(event,'update-diagnosis')">Thêm chẩn đoán</a>

                    <div style="margin-left:auto;">
                        <button type="submit" name="btnHoanTat" class="btn-primary"><i class="fas fa-save"></i> Lưu & Hoàn tất</button>
                    </div>
                </div>

                <!-- Tab: Thêm đơn thuốc -->
                <div id="update-prescription" class="update-tab-content active">
                    <div class="medication-form">
                        <h3>Thông tin thuốc</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal_tenthuoc">Tên thuốc</label>
                                <select id="modal_tenthuoc">
                                    <?php foreach($thuoc as $t): ?>
                                        <option value="<?php echo $t['mathuoc']; ?>"><?php echo $t['tenthuoc']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="modal_soluong">Số lượng</label>
                                <input type="number" id="modal_soluong" placeholder="Số lượng">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="modal_lieudung">Liều dùng</label>
                                <input id="modal_lieudung" placeholder="Ví dụ: 3 lần/ngày">
                            </div>
                            <div class="form-group">
                                <label for="modal_songayuong">Số ngày uống</label>
                                <input id="modal_songayuong" placeholder="Ví dụ: 7 ngày">
                            </div>
                            <div class="form-group">
                                <label for="modal_thoigianuong">Thời gian uống</label>
                                <input id="modal_thoigianuong" placeholder="Ví dụ: Sau ăn">
                            </div>
                        </div>

                        <div style="text-align:right;">
                            <button type="button" class="btn-primary" onclick="addMedicationToList()">Thêm vào đơn thuốc</button>
                        </div>

                        <div id="bangthuoc" style="display:none; margin-top:12px;">
                            <h4>Danh sách thuốc</h4>
                            <table class="medication-table" id="medicationTable">
                                <thead>
                                    <tr>
                                        <th>STT</th><th>Tên thuốc</th><th>Số lượng</th><th>Liều dùng</th><th>Số ngày</th><th>Thời gian</th><th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="medicationTableBody"></tbody>
                            </table>
                        </div>

                        <div id="medicationsContainer"></div>
                    </div>
                </div>

                <!-- Tab: Thêm lịch xét nghiệm -->
                <div id="update-test" class="update-tab-content">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modal_test">Loại xét nghiệm</label>
                            <select name="test" id="modal_test">
                                <option value="">--Chọn--</option>
                                <?php foreach($loaixetnghiem as $lx): ?>
                                    <option value="<?php echo $lx['maloaixetnghiem']; ?>"><?php echo $lx['tenloaixetnghiem']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="modal_appointmentDate">Ngày xét nghiệm</label>
                            <input type="date" id="modal_appointmentDate" name="appointmentDate" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="modal_appointmentTime">Giờ xét nghiệm</label>
                            <select id="modal_appointmentTime" name="appointmentTime"><option value="">-- Chọn giờ --</option></select>
                            <div id="modal_timeSlotLoading" style="display:none;">Đang tải...</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="modal_ghichu">Ghi chú</label>
                        <textarea id="modal_ghichu" name="ghichu" rows="3" placeholder="Ghi chú..."></textarea>
                    </div>
                </div>

                <!-- Tab: Thêm chẩn đoán -->
                <div id="update-diagnosis" class="update-tab-content">
                    <div class="form-group">
                        <label for="modal_trieuchung">Triệu chứng ban đầu</label>
                        <textarea id="modal_trieuchung" name="trieuchung" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_chandoan">Chẩn đoán</label>
                        <textarea id="modal_chandoan" name="chandoan" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_huongdieutri">Hướng điều trị</label>
                        <textarea id="modal_huongdieutri" name="huongdieutri" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modal_ketluan">Kết luận</label>
                        <textarea id="modal_ketluan" name="ketluan" rows="3" required></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Tab functions for update modal
function openUpdateTab(evt, tabId) {
    document.querySelectorAll('.update-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.update-tab-link').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

// Modal open/close and populate
function openCompleteModal(appointment) {
    try { if (typeof appointment === 'string') appointment = JSON.parse(appointment); } catch(e){}

    document.getElementById('maphieu_input').value = appointment.maphieukhambenh || '';
    document.getElementById('mabenh_input').value = appointment.mabenhnhan || '';
    document.getElementById('mahoso_input').value = appointment.mahoso || '';

    // Clear modal fields
    document.getElementById('modal_trieuchung').value = '';
    document.getElementById('modal_chandoan').value = '';
    document.getElementById('modal_huongdieutri').value = '';
    document.getElementById('modal_ketluan').value = '';
    document.getElementById('modal_ghichu').value = '';

    // Reset medications
    medications = [];
    updateMedicationTable();
    updateMedicationInputs();

    // Reset appointment test/time selects
    document.getElementById('modal_test').selectedIndex = 0;
    document.getElementById('modal_appointmentDate').value = '';
    const timeSelect = document.getElementById('modal_appointmentTime');
    timeSelect.innerHTML = '<option value="">-- Chọn giờ --</option>';

    // Show modal
    document.getElementById('modalcapnhathoso').style.display = 'block';
}

function closeCompleteModal() {
    document.getElementById('modalcapnhathoso').style.display = 'none';
}

// Close when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalcapnhathoso');
    if (event.target === modal) modal.style.display = 'none';
});

// MEDICATIONS: manage local list and hidden inputs
let medications = [];

function addMedicationToList() {
    const sel = document.getElementById('modal_tenthuoc');
    const mathuoc = sel.value;
    const tenthuoc = sel.options[sel.selectedIndex].text;
    const soluong = document.getElementById('modal_soluong').value;
    const lieudung = document.getElementById('modal_lieudung').value;
    const songayuong = document.getElementById('modal_songayuong').value;
    const thoigianuong = document.getElementById('modal_thoigianuong').value;

    if (!mathuoc || !soluong) {
        alert('Vui lòng chọn thuốc và nhập số lượng.');
        return;
    }

    medications.push({ mathuoc, tenthuoc, soluong, lieudung, songayuong, thoigianuong });
    updateMedicationTable();
    updateMedicationInputs();

    // clear basic inputs
    document.getElementById('modal_soluong').value = '';
    document.getElementById('modal_lieudung').value = '';
    document.getElementById('modal_songayuong').value = '';
    document.getElementById('modal_thoigianuong').value = '';
}

function updateMedicationTable() {
    const tbody = document.getElementById('medicationTableBody');
    tbody.innerHTML = '';
    if (medications.length === 0) {
        document.getElementById('bangthuoc').style.display = 'none';
        return;
    }
    document.getElementById('bangthuoc').style.display = 'block';
    medications.forEach((m, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${idx+1}</td>
            <td>${m.tenthuoc}</td>
            <td>${m.soluong}</td>
            <td>${m.lieudung || '-'}</td>
            <td>${m.songayuong || '-'}</td>
            <td>${m.thoigianuong || '-'}</td>
            <td><button type="button" class="btn-danger btn-small" onclick="removeMedication(${idx})">Xóa</button></td>`;
        tbody.appendChild(tr);
    });
}

function updateMedicationInputs() {
    const container = document.getElementById('medicationsContainer');
    container.innerHTML = '';
    medications.forEach((m, idx) => {
        container.innerHTML += `<input type="hidden" name="medications[${idx}][mathuoc]" value="${m.mathuoc}">
            <input type="hidden" name="medications[${idx}][tenthuoc]" value="${m.tenthuoc}">
            <input type="hidden" name="medications[${idx}][soluong]" value="${m.soluong}">
            <input type="hidden" name="medications[${idx}][lieudung]" value="${m.lieudung || ''}">
            <input type="hidden" name="medications[${idx}][songayuong]" value="${m.songayuong || ''}">
            <input type="hidden" name="medications[${idx}][thoigianuong]" value="${m.thoigianuong || ''}">`;
    });
}

function removeMedication(index) {
    medications.splice(index,1);
    updateMedicationTable();
    updateMedicationInputs();
}

// Time slot loading for test tab
document.getElementById('modal_test').addEventListener('change', updateTimeSlotsForModal);
document.getElementById('modal_appointmentDate').addEventListener('change', updateTimeSlotsForModal);

function updateTimeSlotsForModal() {
    const selectedTest = document.getElementById('modal_test').value;
    const selectedDate = document.getElementById('modal_appointmentDate').value;
    const timeSelect = document.getElementById('modal_appointmentTime');
    const loading = document.getElementById('modal_timeSlotLoading');
    timeSelect.innerHTML = '<option value="">-- Chọn giờ --</option>';
    if (!selectedTest || !selectedDate) return;
    loading.style.display = 'block';

    const allLich = <?php echo json_encode($all_lichxetnghiem ? $all_lichxetnghiem : []); ?>;
    const exists = allLich.filter(h => (h.ngayhen === selectedDate) && (h.maloaixetnghiem == selectedTest));
    const usedSlots = exists.map(h => h.makhunggio);
    const allSlots = <?php echo json_encode($khunggioxetnghiem ? $khunggioxetnghiem : []); ?>;
    const free = allSlots.filter(s => !usedSlots.includes(s.makhunggioxetnghiem));
    loading.style.display = 'none';
    if (free.length) {
        free.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.makhunggioxetnghiem;
            opt.textContent = s.giobatdau;
            timeSelect.appendChild(opt);
        });
    } else {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Không có khung giờ trống';
        opt.disabled = true;
        timeSelect.appendChild(opt);
        alert('Không có khung giờ trống cho ngày này.');
    }
}

// Form submit validation: ensure ketluan exists when saving
document.getElementById('hoantatForm').addEventListener('submit', function(e) {
    const ketluan = document.getElementById('modal_ketluan').value.trim();
    if (!ketluan) {
        alert('Vui lòng nhập Kết luận trước khi lưu.');
        e.preventDefault();
        return false;
    }
    updateMedicationInputs();
    return true;
});
</script>
