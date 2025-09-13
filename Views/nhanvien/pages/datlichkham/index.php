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

// Lấy dữ liệu POST
$chonTheo = $_POST['chonTheo'] ?? 'ngay';
$ngaychon = $_POST['ngay'] ?? date('Y-m-d');
$bacsi = $_POST['bacsi'] ?? null;
$chuyengia = $_POST['chuyengia'] ?? null;

// Nếu chọn bác sĩ thì reset chuyên gia và ngược lại
if ($bacsi) $chuyengia = null;
if ($chuyengia) $bacsi = null;

// Lấy danh sách bác sĩ, chuyên gia, bệnh nhân
$dsBacSi = $cBacSi->getAllBacSi() ?: [];
$dsChuyenGia = $cChuyenGia->getAllChuyenGia() ?: [];
$dsBenhNhan = $cBenhNhan->getAllBenhNhan() ?: [];

// Lấy lịch khám
$lichTheoNguoi = [];
if ($chonTheo == 'ngay') {
    $tatCaLich = $cLichKham->getAllLichKhamByNgay($ngaychon);
} else {
    $manguoi = $bacsi ?? $chuyengia ?? null;
    if ($manguoi) {
        $tatCaLich = $cLichKham->getLichTrongCuaNguoi($ngaychon, $manguoi);
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
            'giobatdau' => $row['giobatdau'],
            'gioketthuc' => $row['gioketthuc'],
            'ngaylam' => $row['ngaylam']
        ];
        $loai = strtolower(trim($row['hinhthuclamviec'] ?? 'offline'));
        if ($loai === 'online') $lichTheoNguoi[$idnguoi]['online'][] = $ca;
        else $lichTheoNguoi[$idnguoi]['offline'][] = $ca;
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
<style>
body { background-color: #f8f9fa; }
.card-nguoi { margin-bottom: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
.btn-gio { margin: 3px 3px 3px 0; }
.btn-online { background-color: #0dcaf0; color: white; }
.btn-offline { background-color: #198754; color: white; }
.btn-selected { border: 2px solid #ffc107 !important; background-color: #ffc107 !important; color: black !important; }
.card-body h6 { margin-top: 10px; margin-bottom: 5px; font-weight: 600; }
.ca-group { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; }
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
            <select name="bacsi" id="bacsi" class="form-select" onchange="onSelectNguoi('bacsi')">
                <option value="">-- Chọn Bác sĩ --</option>
                <?php foreach($dsBacSi as $row): ?>
                    <option value="<?= $row['mabacsi'] ?>" <?= $bacsi==$row['mabacsi']?'selected':'' ?>><?= htmlspecialchars($row['hoten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <label>Chuyên gia</label>
            <select name="chuyengia" id="chuyengia" class="form-select" onchange="onSelectNguoi('chuyengia')">
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
                  <option value="<?= $bn['mabenhnhan'] ?>"><?= htmlspecialchars($bn['hoten']) ?></option>
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
<script>
function onSelectNguoi(type) {
    const bacsiSelect = document.getElementById('bacsi');
    const chuyengiaSelect = document.getElementById('chuyengia');
    if (type === 'bacsi' && bacsiSelect.value) chuyengiaSelect.value = '';
    if (type === 'chuyengia' && chuyengiaSelect.value) bacsiSelect.value = '';
}

// Highlight nút và gán dữ liệu modal
var modalChonBenhNhan = document.getElementById('modalChonBenhNhan');
modalChonBenhNhan.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('modal_makhunggiokb').value = button.getAttribute('data-makhunggiokb');
    document.getElementById('modal_manguoidung').value = button.getAttribute('data-manguoidung');
    document.getElementById('modal_ngaylam').value = button.getAttribute('data-ngaylam');
    document.querySelectorAll('.btn-gio').forEach(btn => btn.classList.remove('btn-selected'));
    button.classList.add('btn-selected');
});
</script>
</body>
</html>
