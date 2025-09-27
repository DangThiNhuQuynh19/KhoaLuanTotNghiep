<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once __DIR__ . "/../../../../Controllers/cLichKham.php";
include_once __DIR__ . "/../../../../Controllers/cBacSi.php";
include_once __DIR__ . "/../../../../Controllers/cChuyenGia.php";
include_once __DIR__ . "/../../../../Controllers/cBenhNhan.php";

$cLichKham = new cLichKham();
$cBacSi = new cBacSi();
$cChuyenGia = new cChuyenGia();
$cBenhNhan = new cBenhNhan();

// POST data
$chonTheo = $_POST['chonTheo'] ?? 'ngay';
$ngaychon = $_POST['ngay'] ?? date('Y-m-d');
$bacsi = $_POST['bacsi'] ?? null;
$chuyengia = $_POST['chuyengia'] ?? null;

// Reset
if ($bacsi) $chuyengia = null;
if ($chuyengia) $bacsi = null;

// Lấy danh sách
$dsBacSi = $cBacSi->getAllBacSi() ?: [];
$dsChuyenGia = $cChuyenGia->getAllChuyenGia() ?: [];
$dsBenhNhan = $cBenhNhan->getAllBenhNhan() ?: [];

// Lấy lịch khám
$lichTheoNguoi = [];
if ($chonTheo == 'ngay') {
    $tatCaLich = $cLichKham->getAllLichKhamByNgay($ngaychon);
} else {
    $manguoi = $bacsi ?? $chuyengia ?? null;
    $tatCaLich = $manguoi ? $cLichKham->getLichTrongCuaNguoi($ngaychon, $manguoi) : false;
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
            'giobatdau' => $row['giobatdau'],
            'gioketthuc' => $row['gioketthuc'],
            'ngaylam' => $row['ngaylam']
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
body { background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.card-nguoi { margin-bottom: 20px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); border: none; transition: transform 0.2s; }
.card-nguoi:hover { transform: translateY(-3px); }
.card.mb-2 { border-radius: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.05); }
.card-header { background-color: #e9ecef; font-weight: 600; font-size: 0.95rem; }
.btn-gio { margin: 4px 4px 4px 0; padding: 6px 12px; font-size: 0.85rem; border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s; }
.btn-gio:hover { transform: scale(1.05); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
.btn-online { background-color: #0dcaf0; color: white; }
.btn-offline { background-color: #198754; color: white; }
.btn-selected { border: 2px solid #ffc107 !important; background-color: #ffc107 !important; color: black !important; }
.card-body h5 { margin-bottom: 15px; font-weight: 700; color: #343a40; }
.card-body h6 { margin-top: 10px; margin-bottom: 8px; font-weight: 600; color: #495057; }
.ca-group { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
form .form-label { font-weight: 600; font-size: 0.9rem; }
.modal-content { border-radius: 12px; overflow: hidden; }
.modal-header { background-color: #0d6efd; color: white; }
.modal-footer button { border-radius: 8px; }
.select2-container--default .select2-selection--single { border-radius: 8px; height: 38px; padding: 4px 12px; border: 1px solid #ced4da; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; }
@media(max-width:768px){ .ca-group { justify-content: flex-start; } .card-body h5 { font-size: 1rem; } }
</style>
</head>
<body>
<div class="container mt-5">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="m-0"><i class="bi bi-people"></i> Đặt lịch khám</h1>
    <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-house-door-fill"></i> Trang chủ</a>
</div>

<!-- Form lọc -->
<form method="post" class="mb-4 row g-3 align-items-end">
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
            <input type="date" name="ngay" class="form-control" value="<?= $ngaychon ?>" required>
        </div>
    <?php elseif ($chonTheo=='nguoi'): ?>
        <div class="col-auto">
            <label>Bác sĩ</label>
            <select name="bacsi" id="bacsi" class="form-select select2" onchange="onSelectNguoi('bacsi')">
                <option value="">-- Chọn Bác sĩ --</option>
                <?php foreach($dsBacSi as $row): ?>
                    <option value="<?= $row['mabacsi'] ?>" <?= $bacsi==$row['mabacsi']?'selected':'' ?>><?= htmlspecialchars($row['hoten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label>Chuyên gia</label>
            <select name="chuyengia" id="chuyengia" class="form-select select2" onchange="onSelectNguoi('chuyengia')">
                <option value="">-- Chọn Chuyên gia --</option>
                <?php foreach($dsChuyenGia as $row): ?>
                    <option value="<?= $row['machuyengia'] ?>" <?= $chuyengia==$row['machuyengia']?'selected':'' ?>><?= htmlspecialchars($row['hoten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary">Xem lịch</button>
    </div>
</form>

<!-- Lịch khám -->
<?php if (!empty($lichTheoNguoi)) : ?>
<div class="row">
<?php foreach ($lichTheoNguoi as $idnguoi => $nguoi): 
    $roleText = $nguoi['vaitro']==0?'Bác sĩ':'Chuyên gia'; ?>
    <div class="col-12">
        <div class="card card-nguoi mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($nguoi['hoten']) ?> (<?= $roleText ?>)</h5>
                <?php 
                $lichTheoNgay = [];
                foreach (['online','offline'] as $loai) {
                    foreach($nguoi[$loai] as $ca) {
                        $ngay = $ca['ngaylam'];
                        if (!isset($lichTheoNgay[$ngay])) $lichTheoNgay[$ngay] = ['online'=>[], 'offline'=>[]];
                        $lichTheoNgay[$ngay][$loai][] = $ca;
                    }
                }

                foreach($lichTheoNgay as $ngay => $caNgay): ?>
                    <div class="card mb-2">
                        <div class="card-header bg-light">
                            <strong>Ngày: <?= date('d-m-Y', strtotime($ngay)) ?></strong>
                        </div>
                        <div class="card-body">
                            <?php foreach(['online','offline'] as $loai): ?>
                                <?php if(!empty($caNgay[$loai])): ?>
                                    <h6><?= $loai=='online'?'Khám Online':'Khám Bệnh viện' ?></h6>
                                    <div class="ca-group">
                                        <?php foreach($caNgay[$loai] as $ca): ?>
                                            <button type="button"
                                                class="btn <?= $loai=='online'?'btn-online':'btn-offline' ?> btn-sm btn-gio"
                                                data-makhunggiokb="<?= $ca['makhunggiokb'] ?>"
                                                data-manguoidung="<?= $idnguoi ?>"
                                                data-ngaylam="<?= $ca['ngaylam'] ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalChonBenhNhan">
                                                <?= $ca['giobatdau'] ?> - <?= $ca['gioketthuc'] ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
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
<p>Người này chưa có ca khám từ ngày <?= date('d-m-Y', strtotime($ngaychon)) ?> trở đi.</p>
<?php elseif ($chonTheo=='ngay'): ?>
<p>Không có ca khám nào trong ngày <?= date('d-m-Y', strtotime($ngaychon)) ?></p>
<?php endif; ?>

</div>

<!-- Modal chọn bệnh nhân -->
<div class="modal fade" id="modalChonBenhNhan" tabindex="-1" aria-labelledby="modalChonBenhNhanLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="xulydatlich.php" id="formChonBenhNhan">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalChonBenhNhanLabel">Chọn bệnh nhân</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="makhunggiokb" id="modal_makhunggiokb">
          <input type="hidden" name="manguoidung" id="modal_manguoidung">
          <input type="hidden" name="ngaylam" id="modal_ngaylam">
          <div class="mb-3">
            <label for="benhnhan" class="form-label">Bệnh nhân</label>
            <select name="mabenhnhan" id="benhnhan" class="form-select" required>
              <option value="">-- Chọn bệnh nhân --</option>
              <?php foreach($dsBenhNhan as $bn): ?>
                  <option value="<?= $bn['mabenhnhan'] ?>"><?= htmlspecialchars($bn['hoten'] . ' ('.$bn['mabenhnhan'].')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Đặt lịch</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
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
    // Select2 cho các select ngoài modal
    $('.select2').not('#benhnhan').select2({ width:'100%' });
});

// Chọn bác sĩ / chuyên gia chỉ 1 loại
function onSelectNguoi(type){
    if(type==='bacsi' && $('#bacsi').val()) $('#chuyengia').val(null).trigger('change');
    if(type==='chuyengia' && $('#chuyengia').val()) $('#bacsi').val(null).trigger('change');
}

// Modal chọn bệnh nhân
$('#modalChonBenhNhan').on('show.bs.modal', function(event){
    var button = event.relatedTarget;
    $('#modal_makhunggiokb').val(button.getAttribute('data-makhunggiokb'));
    $('#modal_manguoidung').val(button.getAttribute('data-manguoidung'));
    $('#modal_ngaylam').val(button.getAttribute('data-ngaylam'));

    // Reset và khởi tạo Select2 cho modal
    $('#benhnhan').val('').select2({
        width: '100%',
        dropdownParent: $('#modalChonBenhNhan')
    });

    // Highlight nút
    $('.btn-gio').removeClass('btn-selected');
    $(button).addClass('btn-selected');
});
</script>
</body>
</html>