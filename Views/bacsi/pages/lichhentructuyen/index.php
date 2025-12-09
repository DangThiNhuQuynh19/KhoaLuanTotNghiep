<?php
include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/chosobenhandientu.php');
include_once('Controllers/cthuoc.php');
include_once('Controllers/cloaixetnghiem.php');
include_once('Controllers/ckhunggioxetnghiem.php');
include_once("Assets/config.php");

if (isset($_POST['btnHoanTat'])) {
    include_once('Controllers/xulyhoantatkham.php');
}

// Initialize controllers
$cbacsi = new cBacSi();
$cphieukhambenh = new cPhieuKhamBenh();
$chsba = new cHoSoBenhAnDienTu();
$cthuoc = new cThuoc();
$cloaixetnghiem = new cLoaiXetNghiem();
$ckhunggioxetnghiem = new cKhungGioXetNghiem();

// Get data for form dropdowns
$thuoc_list = $cthuoc->get_thuoc();
$loaixetnghiem_list = $cloaixetnghiem->get_loaixetnghiem();
$khunggioxetnghiem_list = $ckhunggioxetnghiem->get_khunggioxetnghiem();

$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);

// Lấy dữ liệu tìm kiếm
$tukhoa = $_GET['tukhoa'] ?? '';
$trangthai = $_GET['trangthai'] ?? '';
$ngay = $_GET['ngay'] ?? '';
$homnay_checked = isset($_GET['homnay']) ? 'checked' : '';

// Lấy default list theo bác sĩ
$lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);

// Checkbox Hôm nay
if (isset($_GET['homnay'])) {
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_homnay($bacsi['mabacsi'], date('Y-m-d'));
}

// Tìm kiếm
if (isset($_GET["btntimkiem"])) {
    $lichkham_list = $cphieukhambenh->search_phieukhamonl($tukhoa, $trangthai, $ngay, $bacsi['mabacsi']);
}

// Bỏ tìm kiếm
if (isset($_GET["btnbo"])) {
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);
    $tukhoa = $trangthai = $ngay = '';
    $homnay_checked = '';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// PHÂN TRANG
$total = is_array($lichkham_list) ? count($lichkham_list) : 0;
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$totalPages = $total ? max(1, ceil($total / $perPage)) : 1;
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$paged_list = $total ? array_slice($lichkham_list, $offset, $perPage) : [];

require 'vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

include_once("Assets/config.php");
include_once('Controllers/cbenhnhan.php');
include_once('Controllers/cchuyenkhoa.php');
include_once('Controllers/chosobenhandientu.php');
include_once('Controllers/cdonthuoc.php');
include_once('Controllers/cthuoc.php');
include_once('Controllers/cchitietdonthuoc.php');
include_once('Controllers/cchitiethoso.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/clichxetnghiem.php');
include_once('Controllers/cketquaxetnghiem.php');
include_once('Controllers/cloaixetnghiem.php');
include_once('Controllers/ckhunggioxetnghiem.php');

$ckhunggioxetnghiem = new cKhungGioXetNghiem();
$cbacsi = new cBacSi();
$chosobenhandientu = new cHoSoBenhAnDienTu();
$cchitietdongthuoc = new cChiTietDonThuoc();
$cchitiethoso = new cChiTietHoSo();
$cbenhnhan = new cBenhnhan();
$cchuyenkhoa = new cChuyenKhoa();
$cdonthuoc = new cDonThuoc();
$cthuoc = new cThuoc();
$clichxetnghiem = new cLichXetNghiem();
$cketquaxetnghiem = new cKetQuaXetNghiem();
$cloaixetnghiem = new cLoaiXetNghiem();

$thuoc = $cthuoc->get_thuoc();
$loaixetnghiem = $cloaixetnghiem->get_loaixetnghiem();

$mahoso = $_POST['mahoso'] ?? $_GET['mahoso'] ?? null;
$benhnhan = $mahoso ? $chosobenhandientu->get_benhnhan_mahoso($mahoso) : [];
$chitiethoso = $mahoso ? $chosobenhandientu->getDonThuocByIDHS($mahoso) : [];
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
$chuyenkhoa = $cchuyenkhoa->get_chuyenkhoa_mabacsi($bacsi['mabacsi']);
$hoso = $mahoso ? $chosobenhandientu->get_hoso_mahoso($mahoso) : [];
$lichxetnghiem = $mahoso ? $clichxetnghiem->get_lichxetnghiem_mahoso($mahoso) : [];
$donthuoc = $mahoso ? $cdonthuoc->get_donthuoc_mahoso($mahoso) : [];
$chitiethoso_mahoso = $mahoso ? $cchitiethoso->get_chitiethoso_mahoso($mahoso) : [];
$message = "";

if (isset($_POST['btnHoanTat']) || isset($_POST['btnupdate'])) {

    $posted_mahoso = $_POST['mahoso'] ?? null;
    $posted_maphieu = $_POST['maphieukhambenh'] ?? null;
    $posted_mabenhnhan = $_POST['mabenhnhan'] ?? null;

    // THUỐC
    $madonthuoc = NULL;
    if (isset($_POST['medications']) && !empty($_POST['medications'])) {
        if ($cdonthuoc->create_donthuoc()) {
            $donthuoc = $cdonthuoc->get_donthuoc_new();
            $madonthuoc = $donthuoc[0]['madonthuoc'] ?? NULL;

            foreach ($_POST['medications'] as $thuoc_item) {
                $mathuoc = $thuoc_item['mathuoc'] ?? null;
                $lieudung = $thuoc_item['lieudung'] ?? null;
                $thoigianuong = $thuoc_item['thoigianuong'] ?? null;
                $songayuong = $thuoc_item['songayuong'] ?? null;

                if ($mathuoc && $lieudung && $thoigianuong && $songayuong) {
                    $cchitietdongthuoc->create_chitietdonthuoc(
                        $madonthuoc,
                        $mathuoc,
                        $lieudung,
                        $thoigianuong,
                        $songayuong
                    );
                }
            }
        }
    }

    // LỊCH XÉT NGHIỆM
    if (!empty($posted_mabenhnhan)
        && !empty($_POST['test'])
        && !empty($_POST['appointmentDate'])
        && !empty($_POST['appointmentTime'])
        && !empty($posted_mahoso)) {

        $filename = 'qr_' . time() . '.png';
        $savePath = 'Assets/img/' . $filename;

        $kg = $ckhunggioxetnghiem->get_khunggioxetnghiem_makhunggio($_POST['appointmentTime']);
        $loai = $cloaixetnghiem->get_loaixetnghiem_maloaixetnghiem($_POST['test']);

        try {
            $builder = new Builder(
                writer: new PngWriter(),
                data: "Họ tên: " . ($benhnhan[0]['mabenhnhan'] ?? $posted_mabenhnhan) . "\n" .
                    "SĐT: " . (isset($benhnhan[0]['sdtbenhnhan']) ? decryptData($benhnhan[0]['sdtbenhnhan']) : '') . "\n" .
                    "Tên xét nghiệm: " . ($loai[0]['tenloaixetnghiem'] ?? '') . "\n" .
                    "Ngày xét nghiệm: " . $_POST['appointmentDate'] . "\n" .
                    "Giờ xét nghiệm: " . ($kg[0]['giobatdau'] ?? '') . "\n" .
                    "Bác sĩ đặt lịch: " . $bacsi['hoten']
            );
            $result = $builder->build();
            file_put_contents($savePath, $result->getString());

            $ok = $clichxetnghiem->create_lichxetnghiem(
                $posted_mabenhnhan,
                $_POST['test'],
                $_POST['appointmentDate'],
                $_POST['appointmentTime'],
                'Đã đặt lịch',
                $posted_mahoso,
                $filename
            );

            if (!$ok) {
                $message .= '<div style="color:orange;">Lưu ý: Lỗi lưu lịch xét nghiệm.</div>';
            }

        } catch (Exception $e) {
            $message .= '<div style="color:orange;">Lỗi tạo QR.</div>';
        }
    }

    // Lưu hồ sơ bệnh án
    $trieuchung = $_POST['trieuchung'] ?? '';
    $chandoan = $_POST['chandoan'] ?? '';
    $huongdieutri = $_POST['huongdieutri'] ?? '';
    $ketluan = $_POST['ketluan'] ?? '';

    $okSave = $cchitiethoso->create_chitiethoso(
        $posted_mahoso,
        $bacsi['mabacsi'],
        $trieuchung,
        $chandoan,
        $huongdieutri,
        $madonthuoc,
        $ketluan
    );

    if ($okSave) {
        if (!empty($posted_maphieu)) {
            $okUpdate = $cphieukhambenh->updateTrangThaiPKB($posted_maphieu, "Đã khám");
        }
    } else {
        $okUpdate = false;
    }

    if ($okUpdate) {
        echo '<script>alert("Cập nhật hồ sơ thành công!"); window.location.href="?action=chitiethoso&mahoso=' . $posted_mahoso . '";</script>';
        exit;
    } else {
        echo '<script>alert("Lưu hồ sơ nhưng cập nhật trạng thái lịch khám thất bại!"); window.location.href="?action=chitiethoso&mahoso=' . $posted_mahoso . '";</script>';
        exit;
    }
}
?>
<link rel="stylesheet" href="Views/bacsi/assets/css/csschitiethoso.css">

<div class="container">
    <div class="content-header">
        <h1>Quản lý lịch hẹn trực tuyến</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Tìm kiếm lịch hẹn</h2>
        </div>
        <div class="card-body">
            <form class="search-form" method="GET">
                <input type="hidden" name="action" value="lichhentructuyen">
                <div class="search-grid">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="tukhoa" placeholder="Tìm theo tên bệnh nhân, mã phiếu..." value="<?php echo htmlspecialchars($tukhoa); ?>">
                    </div>

                    <div class="form-group">
                        <select name="trangthai">
                            <option value="">Trạng thái</option>
                            <option value="Chưa khám" <?php if ($trangthai == 'Chưa khám') echo 'selected'; ?>>Chưa khám</option>
                            <option value="Đã khám" <?php if ($trangthai == 'Đã khám') echo 'selected'; ?>>Đã khám</option>
                            <option value="Đã hủy" <?php if ($trangthai == 'Đã hủy') echo 'selected'; ?>>Đã hủy</option>
                            <option value="Chờ thanh toán" <?php if ($trangthai == 'Chờ thanh toán') echo 'selected'; ?>>Chờ thanh toán</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="date" name="ngay" value="<?php echo htmlspecialchars($ngay); ?>">
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn-primary" name="btntimkiem"><i class="fas fa-search"></i> Tìm kiếm</button>
                    <button type="submit" class="btn-danger" name="btnbo"><i class="fas fa-times"></i> Bỏ</button>
                </div>
            </form>
        </div>
    </div>

    <form method="GET" style="display:flex; justify-content:flex-end; margin-bottom:10px;">
        <input type="hidden" name="action" value="lichhentructuyen">
        <input type="hidden" name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
        <input type="hidden" name="trangthai" value="<?php echo htmlspecialchars($trangthai); ?>">
        <input type="hidden" name="ngay" value="<?php echo htmlspecialchars($ngay); ?>">
        <input type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php echo $homnay_checked; ?>>
        <label for="homnay" style="margin-left:5px;"><b>Hôm nay</b></label>
    </form>

    <div class="card">
        <div class="card-body no-padding">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Ngày</th>
                        <th>Ca</th>
                        <th>Bệnh nhân</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($paged_list)): ?>
                    <?php foreach ($paged_list as $i):
                        $tentrangthai = trim($i['tentrangthai']);
                        switch ($tentrangthai) {
                            case 'Chưa khám': $statusClass = 'status-pending'; break;
                            case 'Đã khám': $statusClass = 'status-completed'; break;
                            case 'Đã hủy': $statusClass = 'status-canceled'; break;
                            case 'Chờ thanh toán': $statusClass = 'status-pending'; break;
                            default: $statusClass = '';
                        }

                        $hosobenhnhan = $chsba->get_mahoso_by_benhnhan_nguoikham($i['mabenhnhan'], $bacsi['machuyenkhoa']);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($i['maphieukhambenh']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($i['ngaykham'])); ?></td>
                            <td><?php echo htmlspecialchars($i['giobatdau']) . ' - ' . htmlspecialchars($i['gioketthuc']); ?></td>
                            <td><?php echo htmlspecialchars($i['hoten']); ?></td>
                            <td><?php echo number_format($i['giakham'], 0, ',', '.'); ?> VND</td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($i['tentrangthai']); ?></span></td>

                            <td>
                            <?php if ($tentrangthai === 'Chưa khám'): ?>
                                <a class="btn-primary btn-small" href="?action=tinnhan&id=<?php echo urlencode($i['mabenhnhan']); ?>">
                                    <i class="fas fa-comment-medical"></i> Nhắn tin
                                </a>

                                <button type="button" class="btn-success btn-small btn-kham"
                                    data-maphieu="<?php echo htmlspecialchars($i['maphieukhambenh']); ?>"
                                    data-mabenhnhan="<?php echo htmlspecialchars($i['mabenhnhan']); ?>"
                                    data-hoten="<?php echo htmlspecialchars($i['hoten']); ?>"
                                    data-ngay="<?php echo htmlspecialchars($i['ngaykham']); ?>"
                                    data-mahoso="<?php echo htmlspecialchars($hosobenhnhan['mahoso'] ?? ''); ?>"
                                >
                                    <i class="fas fa-stethoscope"></i> Khám
                                </button>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; color:gray;">Không có lịch hẹn</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
            <div style="margin-top:12px; text-align:center; padding: 12px;">
                <?php
                $baseURL = "?action=lichhentructuyen&tukhoa=$tukhoa&trangthai=$trangthai&ngay=$ngay&homnay=$homnay_checked&page=";
                if ($page > 1) {
                    echo '<a class="btn-primary btn-page" href="' . $baseURL . ($page - 1) . '">«</a>';
                }
                for ($p = 1; $p <= $totalPages; $p++) {
                    $active = ($p == $page) ? 'active' : '';
                    echo '<a class="btn-page ' . $active . '" href="' . $baseURL . $p . '">' . $p . '</a>';
                }
                if ($page < $totalPages) {
                    echo '<a class="btn-primary btn-page" href="' . $baseURL . ($page + 1) . '">»</a>';
                }
                ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
