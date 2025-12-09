<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once "Controllers/clichkham.php";
include_once "Controllers/cbacsi.php";
include_once "Controllers/cchuyengia.php";
include_once "Controllers/cbenhnhan.php";

$cLichKham = new cLichKham();
$cBacSi = new cBacSi();
$cChuyenGia = new cChuyenGia();
$cBenhNhan = new cBenhNhan();

// POST data
$chonTheo = $_POST['chonTheo'] ?? 'ngay';
$ngaychon = $_POST['ngay'] ?? date('Y-m-d');
$bacsi = $_POST['bacsi'] ?? null;
$chuyengia = $_POST['chuyengia'] ?? null;

// Reset chọn người
if ($bacsi) $chuyengia = null;
if ($chuyengia) $bacsi = null;

// Lấy danh sách
$dsBacSi = $cBacSi->getAllBacSi() ?: [];
$dsChuyenGia = $cChuyenGia->getAllChuyenGia() ?: [];
$dsBenhNhan = $cBenhNhan->getAllBenhNhan() ?: [];

// Hàm xác định ca khám
function xacDinhCa($giobatdau) {
    $time = strtotime($giobatdau);
    if ($time >= strtotime('06:00') && $time <= strtotime('11:30')) return 'Sáng';
    if ($time >= strtotime('12:30') && $time <= strtotime('18:00')) return 'Chiều';
    if ($time >= strtotime('18:30') && $time <= strtotime('21:00')) return 'Tối';
    return 'Khác';
}

// Lấy lịch khám
$lichTheoNguoi = [];
if ($chonTheo == 'ngay') {
    $tatCaLich = $cLichKham->getAllLichKhamByNgay($ngaychon);
} else {
    $manguoi = $bacsi ?? $chuyengia ?? null;
    if ($manguoi && $ngaychon >= date('Y-m-d')) {
        $tatCaLich = $cLichKham->getLichTrongCuaNguoi($manguoi, $ngaychon);
    } else {
        $tatCaLich = false;
    }
}

// Gom dữ liệu theo người
if ($tatCaLich && $tatCaLich->num_rows > 0) {
    while ($row = $tatCaLich->fetch_assoc()) {
        $idnguoi = $row['manguoidung'];
        if (!isset($lichTheoNguoi[$idnguoi])) {
            $lichTheoNguoi[$idnguoi] = [
                'hoten' => $row['hoten'],
                'vaitro' => !empty($row['mabacsi']) ? 0 : 1,
                'online' => [],
                'offline' => []
            ];
        }
        $ca = [
            'makhunggiokb' => $row['makhunggiokb'],
            'giobatdau' => $row['kg_giobatdau'],
            'gioketthuc' => $row['kg_gioketthuc'],
            'ngaylam' => $row['ngaylam'],
            'thongtin_phong' => $row['thongtin_phong'] ?? '',
        ];
        $loai = strtolower(trim($row['hinhthuclamviec'] ?? 'offline'));
        $lichTheoNguoi[$idnguoi][$loai][] = $ca;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt Lịch Khám</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
body { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding-bottom: 30px;
}
.container {
    background-color: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    padding: 30px;
    margin-top: 30px;
    margin-bottom: 30px;
}
.page-header {
    border-bottom: 3px solid #667eea;
    padding-bottom: 15px;
    margin-bottom: 30px;
}
.page-header h1 {
    color: #2d3748;
    font-weight: 700;
    font-size: 2rem;
}
.page-header h1 i {
    color: #667eea;
    margin-right: 10px;
}
.btn-home {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}
.btn-home:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}
.filter-section {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 5px 20px rgba(240, 147, 251, 0.3);
}
.filter-section label {
    color: #ffffff;
    font-weight: 600;
    font-size: 0.95rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}
.filter-section .form-control,
.filter-section .form-select {
    border-radius: 10px;
    border: 2px solid rgba(255,255,255,0.3);
    background-color: rgba(255,255,255,0.95);
    font-weight: 500;
    transition: all 0.3s ease;
}
.filter-section .form-control:focus,
.filter-section .form-select:focus {
    border-color: #ffffff;
    box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.3);
    background-color: #ffffff;
}
.btn-primary {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    font-weight: 600;
    padding: 10px 25px;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 172, 254, 0.6);
}
.card-nguoi { 
    margin-bottom: 25px; 
    border-radius: 20px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
    border: none; 
    transition: all 0.3s ease;
    overflow: hidden;
}
.card-nguoi:hover { 
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}
.card-nguoi .card-body {
    padding: 30px;
}
.card-nguoi .card-title {
    color: #2d3748;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid #667eea;
}
.card-nguoi .card-title .badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding: 8px 15px;
    font-size: 0.85rem;
    border-radius: 20px;
    margin-left: 10px;
}
.card.mb-2 { 
    border-radius: 15px; 
    box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
    border: 2px solid #e2e8f0;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}
.card.mb-2:hover {
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}
.card-header { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    font-weight: 700; 
    font-size: 1.05rem;
    padding: 15px 20px;
    border: none;
}
.btn-gio { 
    margin: 6px 6px 6px 0; 
    padding: 10px 18px; 
    font-size: 0.9rem; 
    font-weight: 600;
    border-radius: 12px; 
    border: 2px solid transparent;
    transition: all 0.3s ease; 
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
.btn-gio:hover { 
    transform: translateY(-3px) scale(1.05); 
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}
.btn-online { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border-color: #00f2fe;
}
.btn-online:hover {
    background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
    color: white;
}
.btn-offline { 
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
    border-color: #38f9d7;
}
.btn-offline:hover {
    background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
    color: white;
}
.btn-selected { 
    border: 3px solid #fbbf24 !important; 
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
    color: #1f2937 !important;
    font-weight: 700 !important;
    box-shadow: 0 8px 25px rgba(251, 191, 36, 0.5) !important;
}
.ten-loai-kham { 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    padding: 10px 18px; 
    font-weight: 700; 
    font-size: 1rem; 
    border-radius: 12px; 
    color: #fff; 
    margin-top: 20px;
    margin-bottom: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.ten-loai-kham.online { 
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.ten-loai-kham.offline { 
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
.ten-loai-kham i { 
    font-size: 1.2rem;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3); 
}
.card-body strong { 
    color: #4a5568;
    font-weight: 700;
}
.ca-group { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 8px; 
    margin-top: 8px; 
    margin-bottom: 15px; 
}
.modal-content { 
    border-radius: 20px; 
    overflow: hidden;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.modal-header { 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    border: none;
}
.modal-header .modal-title {
    font-weight: 700;
    font-size: 1.3rem;
}
.modal-body {
    padding: 25px;
}
.modal-footer {
    padding: 20px 25px;
    border-top: 2px solid #e2e8f0;
}
.modal-footer button { 
    border-radius: 10px;
    padding: 10px 25px;
    font-weight: 600;
}
.btn-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(67, 233, 123, 0.4);
}
.btn-success:hover {
    background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
    box-shadow: 0 6px 20px rgba(67, 233, 123, 0.6);
}
.btn-secondary {
    background: linear-gradient(135deg, #868f96 0%, #596164 100%);
    border: none;
}
.select2-container--default .select2-selection--single { 
    border-radius: 10px; 
    height: 42px; 
    padding: 6px 15px; 
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}
.select2-container--default .select2-selection--single:focus { 
    border-color: #667eea;
}
.select2-container--default .select2-selection--single .select2-selection__rendered { 
    line-height: 28px;
    color: #2d3748;
    font-weight: 500;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { 
    height: 42px; 
}
#thongTinCa {
    background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%);
    padding: 15px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    margin-bottom: 20px;
}
#thongTinCa strong {
    color: #4c1d95;
}
@media(max-width:768px){ 
    .ca-group { justify-content: flex-start; } 
    .card-nguoi .card-title { font-size: 1.2rem; }
    .page-header h1 { font-size: 1.5rem; }
    .container { padding: 20px; margin-top: 15px; }
}
</style>
</head>
<body>
<div class="container mt-5">

<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="m-0"><i class="bi bi-calendar-check-fill"></i> Đặt lịch khám</h1>
    <a href="index.php" class="btn btn-home text-white"><i class="bi bi-house-door-fill"></i> Trang chủ</a>
</div>

<div class="filter-section">
<form method="post" class="row g-3 align-items-end">
    <div class="col-auto">
        <label>Chọn hiển thị</label>
        <select name="chonTheo" class="form-select" onchange="this.form.submit()">
            <option value="ngay" <?= $chonTheo=='ngay'?'selected':'' ?>>Theo ngày</option>
            <option value="nguoi" <?= $chonTheo=='nguoi'?'selected':'' ?>>Theo người khám</option>
        </select>
    </div>

    <?php if ($chonTheo=='ngay'): ?>
        <div class="col-auto">
            <label>Chọn ngày</label>
            <input type="date" name="ngay" class="form-control" value="<?= $ngaychon ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
    <?php elseif ($chonTheo=='nguoi'): ?>
        <div class="col-auto">
            <label>Bác sĩ</label>
            <select name="bacsi" id="bacsi" class="form-select select2" onchange="onSelectNguoi('bacsi')">
                <option value="">-- Chọn Bác sĩ --</option>
                <?php foreach($dsBacSi as $row): ?>
                    <option value="<?= $row['mabacsi'] ?>" <?= $bacsi==$row['mabacsi']?'selected':'' ?>>
                        <?= htmlspecialchars($row['hoten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label>Chuyên gia</label>
            <select name="chuyengia" id="chuyengia" class="form-select select2" onchange="onSelectNguoi('chuyengia')">
                <option value="">-- Chọn Chuyên gia --</option>
                <?php foreach($dsChuyenGia as $row): ?>
                    <option value="<?= $row['machuyengia'] ?>" <?= $chuyengia==$row['machuyengia']?'selected':'' ?>>
                        <?= htmlspecialchars($row['hoten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label>Chọn ngày khám</label>
            <input type="date" name="ngay" class="form-control" value="<?= $ngaychon ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
    <?php endif; ?>

    <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Xem lịch</button>
    </div>
</form>
</div>

<?php if (!empty($lichTheoNguoi)) : ?>
<div class="row">
<?php foreach ($lichTheoNguoi as $idnguoi => $nguoi): 
    $roleText = $nguoi['vaitro']==0?'Bác sĩ':'Chuyên gia'; ?>
    <div class="col-12">
        <div class="card card-nguoi">
            <div class="card-body">
                <h5 class="card-title">
                    <?= htmlspecialchars($nguoi['hoten']) ?> 
                    <span class="badge"><?= $roleText ?></span>
                </h5>
                <?php 
                $lichTheoNgay = [];
                foreach (['online','offline'] as $loai) {
                    foreach($nguoi[$loai] as $ca) {
                        $ngay = $ca['ngaylam'];
                        $tenCa = xacDinhCa($ca['giobatdau']);
                        if (!isset($lichTheoNgay[$ngay])) {
                            $lichTheoNgay[$ngay] = [
                                'online'=>['Sáng'=>[],'Chiều'=>[],'Tối'=>[]],
                                'offline'=>['Sáng'=>[],'Chiều'=>[],'Tối'=>[]]
                            ];
                        }
                        $lichTheoNgay[$ngay][$loai][$tenCa][] = $ca;
                    }
                }

                foreach($lichTheoNgay as $ngay => $caNgay): ?>
                    <div class="card mb-2">
                        <div class="card-header">
                            <i class="bi bi-calendar3"></i> <strong>Ngày: <?= date('d/m/Y', strtotime($ngay)) ?></strong>
                        </div>
                        <div class="card-body">
                            <?php foreach(['online','offline'] as $loai): ?>
                                <?php 
                                $tenLoai = $loai=='online' ? 'Khám Online' : 'Khám Bệnh viện';
                                $coLich = false;
                                foreach(['Sáng','Chiều','Tối'] as $tenCa) {
                                    if (!empty($caNgay[$loai][$tenCa])) { $coLich = true; break; }
                                }
                                ?>
                                <?php if($coLich): ?>
                                    <div class="ten-loai-kham <?= $loai ?>">
                                        <i class="bi <?= $loai=='online'?'bi-laptop':'bi-hospital' ?>"></i>
                                        <?= $tenLoai ?>
                                    </div>
                                    <?php foreach(['Sáng','Chiều','Tối'] as $tenCa): ?>
                                        <?php if(!empty($caNgay[$loai][$tenCa])): 
                                            $thongtinPhong = $caNgay[$loai][$tenCa][0]['thongtin_phong'] ?? '';
                                        ?>
                                            <div class="mt-2">
                                                <strong>
                                                    <?= $tenCa ?>
                                                    <?php if (!empty($thongtinPhong)): ?>
                                                        (<?= htmlspecialchars($thongtinPhong) ?>)
                                                    <?php endif; ?>:
                                                </strong>
                                                 <div class="ca-group">
                                                    <?php foreach($caNgay[$loai][$tenCa] as $ca): ?>
                                                        <button type="button"
                                                            class="btn <?= $loai=='online'?'btn-online':'btn-offline' ?> btn-gio"
                                                            data-makhunggiokb="<?= $ca['makhunggiokb'] ?>"
                                                            data-manguoidung="<?= $idnguoi ?>"
                                                            data-ngaylam="<?= $ca['ngaylam'] ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalChonBenhNhan">
                                                            <i class="bi bi-clock"></i> <?= $ca['giobatdau'] ?> - <?= $ca['gioketthuc'] ?>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php elseif ($chonTheo=='nguoi' && ($bacsi || $chuyengia)): ?>
<div class="alert alert-info" role="alert">
    <i class="bi bi-info-circle-fill"></i> Người này chưa có ca khám từ ngày <strong><?= date('d/m/Y', strtotime($ngaychon)) ?></strong> trở đi.
</div>
<?php elseif ($chonTheo=='ngay'): ?>
<div class="alert alert-warning" role="alert">
    <i class="bi bi-exclamation-triangle-fill"></i> Không có ca khám nào trong ngày <strong><?= date('d/m/Y', strtotime($ngaychon)) ?></strong>
</div>
<?php endif; ?>

</div>

<div class="modal fade" id="modalChonBenhNhan" tabindex="-1" aria-labelledby="modalChonBenhNhanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="xulydatlich.php" id="formChonBenhNhan">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalChonBenhNhanLabel"><i class="bi bi-person-plus-fill"></i> Chọn bệnh nhân</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="makhunggiokb" id="modal_makhunggiokb">
          <input type="hidden" name="manguoidung" id="modal_manguoidung">
          <input type="hidden" name="ngaylam" id="modal_ngaylam">
          <div class="mb-3">
            <label for="benhnhan" class="form-label"><i class="bi bi-person-badge"></i> Bệnh nhân</label>
            <select name="mabenhnhan" id="benhnhan" class="form-select" required>
              <option value="">-- Chọn bệnh nhân --</option>
              <?php foreach($dsBenhNhan as $bn): ?>
                  <option value="<?= $bn['mabenhnhan'] ?>"><?= htmlspecialchars($bn['hoten'] . ' ('.$bn['mabenhnhan'].')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="bi bi-check-circle-fill"></i> Đặt lịch</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Hủy</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('.select2').not('#benhnhan').select2({ width:'100%' });
});

// Chọn bác sĩ / chuyên gia chỉ 1 loại
function onSelectNguoi(type){
    if(type==='bacsi' && $('#bacsi').val()) $('#chuyengia').val(null).trigger('change');
    if(type==='chuyengia' && $('#chuyengia').val()) $('#bacsi').val(null).trigger('change');
}

// Modal chọn bệnh nhân + highlight ca
var modal = document.getElementById('modalChonBenhNhan');
var lastSelectedBtn = null;

modal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;

    if(lastSelectedBtn) lastSelectedBtn.classList.remove('btn-selected');
    button.classList.add('btn-selected');
    lastSelectedBtn = button;

    document.getElementById('modal_makhunggiokb').value = button.getAttribute('data-makhunggiokb');
    document.getElementById('modal_manguoidung').value = button.getAttribute('data-manguoidung');
    document.getElementById('modal_ngaylam').value = button.getAttribute('data-ngaylam');

    var nguoiName = button.closest('.card-nguoi').querySelector('.card-title').innerText;
    var loaiKham = button.classList.contains('btn-online') ? 'Khám Online' : 'Khám Bệnh viện';
    var caText = button.innerText;

    var infoDiv = document.getElementById('thongTinCa');
    if(!infoDiv){
        infoDiv = document.createElement('div');
        infoDiv.id = 'thongTinCa';
        infoDiv.className = 'mb-2';
        var modalBody = modal.querySelector('.modal-body');
        modalBody.insertBefore(infoDiv, modalBody.firstChild);
    }
    infoDiv.innerHTML = `<strong>Người khám:</strong> ${nguoiName}<br>
                         <strong>Loại khám:</strong> ${loaiKham}<br>
                         <strong>Ca:</strong> ${caText}`;
});

// Ngăn chọn ngày nhỏ hơn hôm nay
var ngayInput = document.querySelectorAll('input[type="date"]');
var today = new Date().toISOString().split('T')[0];
ngayInput.forEach(function(input) {
    input.setAttribute('min', today);
    input.addEventListener('change', function() {
        if(this.value < today){
            alert('Vui lòng chọn ngày hợp lệ (không được nhỏ hơn ngày hôm nay)');
            this.value = today;
        }
    });
});
</script>
</body>
</html>
