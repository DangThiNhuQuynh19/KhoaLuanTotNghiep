<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();

include_once('Controllers/cchuyengia.php');
include_once('Controllers/clichkham.php');
include_once('Controllers/cbenhnhan.php'); // thêm controller lấy bệnh nhân

if (!isset($_SESSION["dangnhap"]) || !isset($_SESSION["user"])) {
    echo "<p>Bạn chưa đăng nhập!</p>";
    exit;
}

$cbacsi = new cChuyenGia();
$bacsi = $cbacsi->getChuyenGiaByTenTK($_SESSION["user"]["tentk"] ?? '');
if (!$bacsi) {
    echo "<p>Không tìm thấy chuyên gia.</p>";
    exit;
}

$manguoi = $bacsi['machuyengia'] ?? null;
if (!$manguoi) {
    echo "<p>Mã bác sĩ không hợp lệ.</p>";
    exit;
}

// Xử lý ngày được chọn từ input
$tuNgay = isset($_GET['ngaychon']) && $_GET['ngaychon'] !== ''
    ? $_GET['ngaychon']
    : date('Y-m-d');

$lich = new cLichKham();
$tbl = $lich->getLichTrongCuaNguoi($manguoi, $tuNgay);

$lichOnline = [];
$lichOffline = [];

if ($tbl && $tbl !== -1 && $tbl !== 0) {
    while ($row = $tbl->fetch_assoc()) {
        $loai = strtolower($row['hinhthuclamviec']);
        if ($loai === 'online') $lichOnline[] = $row;
        else $lichOffline[] = $row;
    }
}

// Hàm xác định ca theo giờ
function xacDinhCa($gio) {
    $time = strtotime($gio);
    if ($time >= strtotime('06:00') && $time <= strtotime('11:30')) return 'Sáng';
    if ($time >= strtotime('12:30') && $time <= strtotime('18:00')) return 'Chiều';
    if ($time >= strtotime('18:30') && $time <= strtotime('21:00')) return 'Tối';
    return 'Khác';
}

// Gom lịch theo ca
function gomTheoCa($lich) {
    $result = ['Sáng'=>[], 'Chiều'=>[], 'Tối'=>[]];
    foreach ($lich as $row) {
        $ca = xacDinhCa($row['kg_giobatdau']);
        if ($ca !== 'Khác') {
            $result[$ca][] = $row;
        }
    }
    return $result;
}

$lichOnlineCa = gomTheoCa($lichOnline);
$lichOfflineCa = gomTheoCa($lichOffline);

// Lấy danh sách bệnh nhân
$cbenhnhan = new cBenhNhan();
$benhnhanList = $cbenhnhan->get_benhnhan_mabacsi($manguoi);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Lịch Trống Chuyên Gia</title>
<style>
/* --- giữ nguyên toàn bộ style của bạn --- */
body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0;}
.lich-wrapper { max-width: 980px; margin: 40px auto; background: linear-gradient(145deg, #ffffff, #f0e7ff); padding: 30px 25px; border-radius: 15px; box-shadow: 0 10px 30px rgba(108, 52, 131, 0.15); transition: transform 0.2s;}
.lich-wrapper:hover { transform: translateY(-3px);}
.lich-wrapper h2 { text-align: center; color: #6c3483; font-size: 30px; font-weight: 800; margin-bottom: 30px; text-shadow: 1px 1px 2px rgba(108, 52, 131, 0.3);}
.date-form { display: flex; justify-content: center; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 30px;}
.date-form input[type="date"] { padding: 8px 14px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px; cursor: pointer; background-color: #fff; transition: all 0.3s;}
.date-form input[type="date"]:focus { outline: none; border-color: #6c3483; box-shadow: 0 0 5px rgba(108, 52, 131, 0.3);}
.date-form button { padding: 8px 20px; border-radius: 8px; border: none; background: #6c3483; color: #fff; font-weight: 600; cursor: pointer; font-size: 14px; transition: 0.3s;}
.date-form button:hover { background: #4b0082; transform: scale(1.05); box-shadow: 0 5px 15px rgba(108, 52, 131, 0.3);}
h4 { font-size: 22px; font-weight: 700; color: #4b0082; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #6c3483; display: inline-block; padding-bottom: 3px;}
.title-ca { font-size: 16px; font-weight: 600; color: #6c3483; margin: 12px 0 6px;}
.ca-group { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;}
.btn-ca { padding: 8px 16px; border-radius: 10px; font-size: 14px; font-weight: 500; border: none; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s, background 0.2s;}
.btn-online { background: #0dcaf0; color: #fff;}
.btn-online:hover { background: #0bb7d0; transform: scale(1.07); box-shadow: 0 5px 15px rgba(13, 202, 240, 0.4);}
.btn-offline { background: #198754; color: #fff;}
.btn-offline:hover { background: #146c43; transform: scale(1.07); box-shadow: 0 5px 15px rgba(25, 135, 84, 0.4);}
.btn-ca:disabled, .btn-ca.btn-disabled { background: #ccc !important; cursor: not-allowed; opacity: 0.6;}
/* Modal */
.modal { display: none; position: fixed; z-index: 1000; padding-top: 100px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);}
.modal-content { background-color: #fff; margin: auto; padding: 20px; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 10px 25px rgba(108,52,131,0.3);}
.close { color: #aaa; float: right; font-size: 24px; font-weight: bold; cursor: pointer; }
.close:hover { color: #6c3483; }
#formBenhNhan select, #formBenhNhan button { width: 100%; padding: 8px 10px; margin-top: 8px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px;}
#formBenhNhan button { background: #6c3483; color: #fff; border: none; cursor: pointer; }
#formBenhNhan button:hover { background: #4b0082;}
@media (max-width: 768px) { .lich-wrapper { margin: 20px 15px; padding: 25px 20px;} .lich-wrapper h2 { font-size: 24px;} h4 { font-size: 20px;} .title-ca { font-size: 14px;} .btn-ca { font-size: 13px; padding: 6px 12px;} .date-form { flex-direction: column; } }
</style>
</head>
<body>

<div class="lich-wrapper">
<h2>Xem Lịch Khám Chuyên gia</h2>

<form method="get" class="date-form">
    <input type="hidden" name="action" value="datlich">
    <label for="ngaychon">Chọn ngày: </label>
    <?php $today = date('Y-m-d'); ?>
    <input type="date" id="ngaychon" name="ngaychon" value="<?= htmlspecialchars($tuNgay) ?>" min="<?= $today ?>" required>
    <button type="submit">Xem lịch</button>
</form>
<?php foreach (['online'=>'Khám Online','offline'=>'Khám Bệnh viện'] as $loai => $title): ?>
    <div style="margin-top: 30px; margin-bottom: 10px;">
        <h4><?= $title ?></h4>
    </div>
    <?php 
        $lichCa = $loai=='online' ? $lichOnlineCa : $lichOfflineCa;
        $btnClass = $loai=='online' ? 'btn-online' : 'btn-offline';
    ?>
    <?php foreach(['Sáng','Chiều','Tối'] as $caTen): ?>
        <?php if(!empty($lichCa[$caTen])): 
            $gioBatDau = $lichCa[$caTen][0]['kg_giobatdau'];
            $gioKetThuc = $lichCa[$caTen][count($lichCa[$caTen])-1]['kg_gioketthuc'];
            $thongTinPhong = $loai=='offline' ? ($lichCa[$caTen][0]['thongtin_phong'] ?? '') : '';
        ?>
            <div class="title-ca">
                <?= $caTen ?><?php if($thongTinPhong): ?> (<?= htmlspecialchars($thongTinPhong) ?>)<?php endif; ?>:
            </div>
            <div class="ca-group">
                <?php foreach($lichCa[$caTen] as $row): ?>
                    <button type="button" class="btn-ca <?= $btnClass ?>" 
                        data-ca="<?= $caTen ?>"
                        data-giobd="<?= $row['kg_giobatdau'] ?>"
                        data-giokt="<?= $row['kg_gioketthuc'] ?>">
                        <?= $row['kg_giobatdau'] ?> - <?= $row['kg_gioketthuc'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>

</div>

<!-- Modal chọn bệnh nhân -->
<div id="modalBenhNhan" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Chọn bệnh nhân</h3>
        <form id="formBenhNhan">
            <label for="benhnhanSelect">Bệnh nhân:</label>
            <select id="benhnhanSelect" required>
                <option value="">-- Chọn bệnh nhân --</option>
                <?php
                if(is_array($benhnhanList)){
                    foreach($benhnhanList as $bn){
                        echo '<option value="'.$bn['mabenhnhan'].'">'.htmlspecialchars($bn['hoten']).' - '.htmlspecialchars($bn['sdt']).'</option>';
                    }
                }
                ?>
            </select>
            <input type="hidden" id="caChon">
            <input type="hidden" id="gioBatDau">
            <input type="hidden" id="gioKetThuc">
            <div style="margin-top: 15px; text-align: right;">
                <button type="submit">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<script>
const dateInput = document.getElementById('ngaychon');
dateInput.addEventListener('keydown', e => e.preventDefault());

// Modal
const modal = document.getElementById("modalBenhNhan");
const spanClose = document.querySelector(".modal .close");
const formBenhNhan = document.getElementById("formBenhNhan");

document.querySelectorAll('.btn-ca').forEach(btn => {
    btn.addEventListener('click', function(){
        modal.style.display = "block";
        document.getElementById('caChon').value = this.dataset.ca;
        document.getElementById('gioBatDau').value = this.dataset.giobd;
        document.getElementById('gioKetThuc').value = this.dataset.giokt;
    });
});

spanClose.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if(e.target==modal) modal.style.display = "none"; }

formBenhNhan.addEventListener('submit', e => {
    e.preventDefault();
    const benhnhan = document.getElementById('benhnhanSelect').value;
    const ca = document.getElementById('caChon').value;
    const gioBD = document.getElementById('gioBatDau').value;
    const gioKT = document.getElementById('gioKetThuc').value;
    if(benhnhan){
        alert(`Đặt lịch cho bệnh nhân ${benhnhan}\nCa: ${ca}\nThời gian: ${gioBD} - ${gioKT}`);
        modal.style.display = "none";
    }
});
</script>

</body>
</html>
