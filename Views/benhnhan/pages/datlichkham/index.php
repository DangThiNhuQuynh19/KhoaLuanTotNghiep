<?php
include_once('Assets/config.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/clichkham.php');
include_once('Controllers/cchuyengia.php');
ini_set('display_errors', 1);

$idbs = $_GET['idbs'] ?? null;
$ngay = $_GET['ngay'] ?? null;
$ca   = $_GET['makhunggiokb'] ?? null;
$idcg = $_GET['idcg'] ?? null;

$hoten = $capbac = $chuyenKhoa = $gia = $anh = $giokham = $thongtin = '';
$error = '';

if ($idbs && $ngay && $ca) {
    // Lấy thông tin bác sĩ
    $pBacSi = new cBacSi();
    $tblBacSi = $pBacSi->getBacSiById($idbs);
    if ($tblBacSi && $tblBacSi->num_rows > 0) {
        $bs = $tblBacSi->fetch_assoc();
        $hoten = $bs['hoten'];
        $capbac = $bs['capbac'];
        $chuyenKhoa = $bs['tenchuyenkhoa'];
        $gia = $bs['giakham'];
        $anh = $bs['imgbs'];
    } else {
        $error = "Không tìm thấy thông tin bác sĩ.";
    }

    // Lấy lịch khám
    $pLichKham = new cLichKham();
    $tblLich = $pLichKham->getlich($ca, $ngay, $idbs);
    if (is_array($tblLich) && count($tblLich) > 0) {
        $lich = $tblLich[0];
        $giokham = $lich['giokham'];
        $thongtin = $lich['thongtin'];
    } else {
        $error = "Không tìm thấy lịch khám.";
    }

} elseif ($idcg && $ngay && $ca) {
    // Lấy thông tin chuyên gia
    $pChuyenGia = new cChuyenGia();
    $tblChuyenGia = $pChuyenGia->getChuyenGiaById($idcg);
    if ($tblChuyenGia && $tblChuyenGia->num_rows > 0) {
        $cg = $tblChuyenGia->fetch_assoc();
        $hoten = $cg['hoten'];
        $capbac = $cg['capbac'];
        $chuyenKhoa = $cg['tenlinhvuc'];
        $gia = $cg['giatuvan'];
        $anh = $cg['imgcg'];
    }

    // Lấy lịch khám
    $pLichKham = new cLichKham();
    $tblLich = $pLichKham->getlich($ca, $ngay, $idcg);
    if (is_array($tblLich) && count($tblLich) > 0) {
        $lich = $tblLich[0];
        $giokham = $lich['giokham'];
        $thongtin = $lich['thongtin'];
    } else {
        $error = "Không tìm thấy lịch khám.";
    }

} else {
    $error = "Thiếu tham số trên URL.";
}

include_once('Controllers/cbenhnhan.php');
$benhnhans = [];
if (isset($_SESSION['user']['tentk'])) {
    $tentk = $_SESSION['user']['tentk'];
    $pBenhNhan = new cBenhNhan();
    $tk = $pBenhNhan->getbenhnhanbytk($tentk);
    $benhnhans = $pBenhNhan->getAllBenhNhanByTKdl($tk['mabenhnhan']);
}

$batBuoc = ['hoten','ngaysinh','cccd','gioitinh','tentinhthanhpho','tenxaphuong','sonha'];
function checkMissingFields($record, $required) {
    foreach ($required as $f) if (!isset($record[$f]) || trim($record[$f]) === '') return true;
    return false;
}

include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/clichlamviec.php');

if (isset($_POST['datlich'])) {
  $_SESSION['mabenhnhan']   = $_POST['mabenhnhan'];
  $_SESSION['makhunggiokb'] = $_POST['makhunggiokb'];
  if ($idbs) $_SESSION['mabacsi'] = $_POST['mabacsi'];
  elseif ($idcg) $_SESSION['machuyengia'] = $_POST['machuyengia'];
  $_SESSION['ngaykham']     = $_POST['ngaykham'];
  $_SESSION['tongtien']     = $_POST['giakham'];
  $_SESSION['matrangthai']  = '6';

    $maphieukb = 'PKB' . time() . rand(100, 999);
    $_SESSION['maphieukhambenh'] = $maphieukb;

    $pPhieu = new cPhieuKhambenh();
    if ($pPhieu->kiemTraTrungLich($_SESSION['mabenhnhan'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'])) {
        echo '<div class="text-danger text-center">Bạn đã có lịch hẹn trong ca này vào ngày này.</div>';
    } else {
        header("Location: ?action=thanhtoan");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đặt Lịch Khám Online</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --custom-purple: #6f2fa3; --accent:#47206a; }
    body { background:#f4f6f9; margin:0; font-family:"Segoe UI", Tahoma, sans-serif; padding:20px; }
    .container2 { margin:auto; max-width:980px; background:#fff; padding:28px; border-radius:14px; box-shadow:0 10px 30px rgba(29,14,45,0.06); }
    .doctor-card { border-radius:12px; padding:18px; background:linear-gradient(135deg,#fff,#fbfbfd); display:flex; gap:18px; align-items:center; justify-content:space-between; }
    .doctor-info h5 { font-size:1.15rem; font-weight:700; color:var(--custom-purple); margin-bottom:6px; }
    .doctor-image { width:120px; height:120px; border-radius:50%; overflow:hidden; border:3px solid var(--custom-purple); box-shadow:0 8px 18px rgba(111,47,163,0.12); }
    .doctor-image img { width:100%; height:100%; object-fit:cover; }
    .text-danger { color:#dc3545; font-weight:700; }

    /* Improve modal style (Bootstrap modal will be used) */
    .modal-header.custom {
      background: linear-gradient(90deg,var(--custom-purple),var(--accent));
      color: #fff;
      border-bottom:none;
    }
    .modal-body.custom {
      font-size:0.98rem;
    }
    .btn-primary.custom {
      background: linear-gradient(90deg,var(--custom-purple),var(--accent));
      border:none;
    }

    /* Accordion: ensure the clickable header is full width */
    .accordion-button { font-weight:700; color:#2b2b2b; }
    .accordion-button::after { filter: none; }
  </style>
</head>
<body>

<?php if ($error == ''): ?>
  <div class="container2 mb-4">
    <div class="doctor-card">
      <div class="doctor-info">
        <h5><?php echo htmlspecialchars($capbac).' - '.htmlspecialchars($hoten); ?></h5>
        <?php if ($idbs): ?><p class="mb-1"><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($chuyenKhoa); ?></p><?php endif; ?>
        <?php if ($idcg): ?><p class="mb-1"><strong>Lĩnh vực:</strong> <?php echo htmlspecialchars($chuyenKhoa); ?></p><?php endif; ?>
        <p class="mb-1"><strong>Ngày khám:</strong> <?php echo htmlspecialchars($ngay); ?></p>
        <p class="mb-1"><strong>Giờ:</strong> <?php echo htmlspecialchars($giokham); ?></p>
        <p class="mb-1"><strong>Giá:</strong> <?php echo number_format($gia, 0, ',', '.'); ?> đ</p>
      </div>
      <div class="doctor-image ms-3">
        <img src="Assets/img/<?php echo htmlspecialchars($anh); ?>" alt="Ảnh bác sĩ">
      </div>
    </div>
  </div>
<?php else: ?>
  <p class="text-danger text-center"><?php echo $error; ?></p>
<?php endif; ?>

<div class="container2">
  <h2 class="mb-4 text-center">Chọn hồ sơ bệnh nhân</h2>

  <?php
    $profileCount = is_array($benhnhans) ? count($benhnhans) : 0;
    $maxProfiles = 5;
  ?>

  <?php if (!empty($benhnhans)): ?>
  <div class="accordion" id="benhNhanAccordion">
    <?php foreach ($benhnhans as $i => $bn): ?>
      <?php $thieu = checkMissingFields($bn, $batBuoc); ?>
      <div class="accordion-item mb-3">
        <h2 class="accordion-header" id="heading<?php echo $i; ?>">
          <button class="accordion-button collapsed" type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapse<?php echo $i; ?>"
                  aria-expanded="false"
                  aria-controls="collapse<?php echo $i; ?>">
            <?php echo htmlspecialchars($bn['hoten']); ?>
          </button>
        </h2>
        <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse"
             aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#benhNhanAccordion">
          <div class="accordion-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($bn['ngaysinh']); ?></p>
                <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($bn['gioitinh']); ?></p>
                <p><strong>CCCD:</strong> <?php echo htmlspecialchars(isset($bn['cccd']) ? decryptData($bn['cccd']) : ""); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($bn['sonha']).', '.htmlspecialchars($bn['tenxaphuong']).', '.htmlspecialchars($bn['tentinhthanhpho']); ?></p>
              </div>
              <div class="col-md-6">
                <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars(decryptData($bn['sdt'])); ?></p>
                <p><strong>Email tài khoản:</strong> <?php echo htmlspecialchars(isset($bn['email']) ? decryptData($bn['email']) : ""); ?></p>
                <p><strong>Email cá nhân:</strong> <?php echo htmlspecialchars(isset($bn['emailcanhan']) ? decryptData($bn['emailcanhan']) : ""); ?></p>
                <p><strong>Dân tộc:</strong> <?php echo htmlspecialchars($bn['dantoc']); ?></p>
              </div>
            </div>

            <?php if (!$thieu): ?>
              <form method="POST" class="mt-3">
                <input type="hidden" name="mabenhnhan" value="<?php echo $bn['mabenhnhan']; ?>">
                <input type="hidden" name="makhunggiokb" value="<?php echo $ca; ?>">
                <?php if ($idbs): ?>
                  <input type="hidden" name="mabacsi" value="<?php echo $idbs; ?>">
                <?php elseif ($idcg): ?>
                  <input type="hidden" name="machuyengia" value="<?php echo $idcg; ?>">
                <?php endif; ?>
                <input type="hidden" name="ngaykham" value="<?php echo $ngay; ?>">
                <input type="hidden" name="giakham" value="<?php echo $gia; ?>">
                <div class="text-center">
                  <button type="submit" name="datlich" class="btn btn-primary">Đặt lịch khám</button>
                </div>
              </form>
            <?php else: ?>
              <div class="text-danger text-center mt-3">Hồ sơ chưa đủ thông tin để đặt lịch.</div>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-2 mt-3">
              <a href="?action=suahoso&mabenhnhan=<?php echo $bn['mabenhnhan']; ?>" class="btn btn-warning">Sửa hồ sơ</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p class="text-danger">Không có bệnh nhân nào được tìm thấy.</p>
  <?php endif; ?>

  <div class="text-center mt-3">
    <button id="createProfileBtn" class="btn btn-success btn-lg"
            data-current-count="<?php echo $profileCount; ?>"
            data-max="<?php echo $maxProfiles; ?>">
      + Tạo hồ sơ bệnh nhân mới
    </button>
    <div class="mt-2 text-muted" style="font-size:0.95rem;">
      Mỗi tài khoản chỉ được tạo tối đa <?php echo $maxProfiles; ?> hồ sơ.
    </div>
  </div>
</div>

<!-- Modal used for nice notifications -->
<div class="modal fade" id="limitModal" tabindex="-1" aria-labelledby="limitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header custom">
        <h5 class="modal-title" id="limitModalLabel">Giới hạn hồ sơ</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body custom">
        <div class="d-flex align-items-start gap-3">
          <div style="width:48px;height:48px;border-radius:50%;background:#fff8;display:flex;align-items:center;justify-content:center;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 9v4M12 17h.01" stroke="#6f2fa3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div id="limitModalMessage" style="font-size:0.98rem;color:#222"></div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="?action=suahoso" class="btn btn-outline-secondary">Quản lý hồ sơ</a>
        <button type="button" class="btn btn-primary custom" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS (required for accordion and modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  const btn = document.getElementById('createProfileBtn');
  btn.addEventListener('click', function(e){
    const current = parseInt(btn.getAttribute('data-current-count') || '0', 10);
    const max = parseInt(btn.getAttribute('data-max') || '4', 10);

    if (current >= max) {
      const message = `Một tài khoản chỉ được tạo tối đa ${max} hồ sơ bệnh nhân. Vui lòng sửa hoặc xóa một hồ sơ hiện có nếu muốn tạo hồ sơ mới.`;
      // show bootstrap modal with message
      const limitModal = new bootstrap.Modal(document.getElementById('limitModal'));
      document.getElementById('limitModalMessage').textContent = message;
      limitModal.show();
      return;
    }

    // proceed to create page
    window.location.href = '?action=taohoso';
  });

  // If accordion not opening due to missing JS previously, ensure that each collapse triggers focus for accessibility
  document.querySelectorAll('.accordion-button').forEach(btn => {
    btn.addEventListener('click', () => {
      // small timeout to allow bootstrap to toggle classes
      setTimeout(() => {
        const target = document.querySelector(btn.getAttribute('data-bs-target'));
        if (target && target.classList.contains('show')) {
          const firstFocusable = target.querySelector('button, a, input, textarea, select');
          if (firstFocusable) firstFocusable.focus();
        }
      }, 200);
    });
  });
})();
</script>

</body>
</html>