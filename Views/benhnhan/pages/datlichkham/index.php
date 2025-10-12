<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đặt Lịch Khám Online</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    :root {
      --custom-purple: rgb(85, 45, 125);
      --custom-purple-dark: rgb(70, 35, 110);
    }
    body { 
      background-color: #f4f6f9; 
      margin-top: 100px; 
      font-family: "Segoe UI", Tahoma, sans-serif; 
    }
    h2 { 
      color: var(--custom-purple); 
      font-weight: 600; 
    }
    .btn-primary { 
      background: var(--custom-purple); 
      border-radius: 50px; 
    }
    .btn-primary:hover { 
      background: var(--custom-purple-dark); 
    }
    .container { 
      max-width: 950px; 
      background: #fff; 
      padding: 35px; 
      border-radius: 20px; 
      box-shadow: 0 8px 20px rgba(0,0,0,0.05); 
    }

    /* 🎨 Card thông tin bác sĩ */
    .doctor-card {
      border: none;
      border-radius: 20px;
      padding: 25px;
      background: linear-gradient(135deg, #ffffff, #f9f9fc);
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .doctor-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    .doctor-info h5 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--custom-purple);
      margin-bottom: 12px;
    }
    .doctor-info p {
      margin: 6px 0;
      font-size: 0.98rem;
      color: #444;
    }
    .doctor-image {
      flex-shrink: 0;
      width: 150px;
      height: 150px;
      border-radius: 50%;
      overflow: hidden;
      border: 4px solid var(--custom-purple);
      box-shadow: 0 6px 15px rgba(0,0,0,0.12);
    }
    .doctor-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .text-danger { 
      color: #dc3545; 
      font-weight: bold; 
    }
  </style>
</head>
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

include_once('Controllers/cBenhNhan.php');
$benhnhans = [];
if (isset($_SESSION['user']['tentk'])) {
    $tentk = $_SESSION['user']['tentk'];
    $pBenhNhan = new cBenhNhan();
    $tk = $pBenhNhan->getbenhnhanbytk($tentk);
    $benhnhans = $pBenhNhan->getAllBenhNhanByTK($tk['mabenhnhan']);
}

$batBuoc = ['hoten','ngaysinh','gioitinh','dantoc','tentinhthanhpho','tenxaphuong','sonha'];
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
<body>
<?php if ($error == ''): ?>
  <div class="container text-center">
    <div class="doctor-card d-flex justify-content-between align-items-center flex-wrap">
      <div class="doctor-info text-start">
        <h5><?php echo htmlspecialchars($capbac).' - '.htmlspecialchars($hoten); ?></h5>
        <?php if ($idbs): ?><p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($chuyenKhoa); ?></p><?php endif; ?>
        <?php if ($idcg): ?><p><strong>Lĩnh vực:</strong> <?php echo htmlspecialchars($chuyenKhoa); ?></p><?php endif; ?>
        <p><strong>Ngày khám:</strong> <?php echo htmlspecialchars($ngay); ?></p>
        <p><strong>Giờ:</strong> <?php echo htmlspecialchars($giokham); ?></p>
        <p><strong>Thông tin:</strong> <?php echo htmlspecialchars($thongtin); ?></p>
        <p><strong>Giá:</strong> <?php echo number_format($gia, 0, ',', '.'); ?> đ</p>
      </div>
      <div class="doctor-image ms-4 mt-3 mt-md-0">
        <img src="Assets/img/<?php echo htmlspecialchars($anh); ?>" alt="Ảnh bác sĩ">
      </div>
    </div>
  </div>
<?php else: ?>
  <p class="text-danger text-center"><?php echo $error; ?></p>
<?php endif; ?>

<div class="container mt-5 mb-5">
  <h2 class="mb-4 text-center">Chọn hồ sơ bệnh nhân</h2>
  <?php if (!empty($benhnhans)): ?>
  <div class="accordion" id="benhNhanAccordion">
    <?php foreach ($benhnhans as $i => $bn): ?>
      <?php $thieu = checkMissingFields($bn, $batBuoc); ?>
      <div class="accordion-item mb-3">
        <h2 class="accordion-header" id="heading<?php echo $i; ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>">
            <?php echo htmlspecialchars($bn['hoten']); ?>
          </button>
        </h2>
        <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse" data-bs-parent="#benhNhanAccordion">
          <div class="accordion-body">
            <div class="d-flex justify-content-between">
              <div>
                <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($bn['ngaysinh']); ?></p>
                <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($bn['gioitinh']); ?></p>
                <p><strong>CCCD:</strong> <?php echo htmlspecialchars(decryptData($bn['cccd'])); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($bn['sonha']).', '.htmlspecialchars($bn['tenxaphuong']).', '.htmlspecialchars($bn['tentinhthanhpho']); ?></p>
              </div>
              <div>
                <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars(decryptData($bn['sdt'])); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars(decryptData($bn['email'])); ?></p>
                <p><strong>Dân tộc:</strong> <?php echo htmlspecialchars($bn['dantoc']); ?></p>
              </div>
            </div>
            <?php if (!$thieu): ?>
              <form method="POST">
                <input type="hidden" name="mabenhnhan" value="<?php echo $bn['mabenhnhan']; ?>">
                <input type="hidden" name="makhunggiokb" value="<?php echo $ca; ?>">
                <?php if ($idbs): ?>
                  <input type="hidden" name="mabacsi" value="<?php echo $idbs; ?>">
                <?php elseif ($idcg): ?>
                  <input type="hidden" name="machuyengia" value="<?php echo $idcg; ?>">
                <?php endif; ?>
                <input type="hidden" name="ngaykham" value="<?php echo $ngay; ?>">
                <input type="hidden" name="giakham" value="<?php echo $gia; ?>">
                <div class="text-center mt-3">
                  <button type="submit" name="datlich" class="btn btn-primary">Đặt lịch khám</button>
                </div>
              </form>
            <?php else: ?>
              <div class="text-danger text-center mt-3">Hồ sơ chưa đủ thông tin để đặt lịch.</div>
            <?php endif; ?>
            <div class="d-flex justify-content-center gap-2 mt-3">
              <a href="?action=suahoso&mabenhnhan=<?php echo $bn['mabenhnhan']; ?>" class="btn btn-warning">Sửa hồ sơ</a>
              <a href="xoahoso.php?mabenhnhan=<?php echo $bn['mabenhnhan']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa hồ sơ này?');">Xóa hồ sơ</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p class="text-danger">Không có bệnh nhân nào được tìm thấy.</p>
  <?php endif; ?>
  <a href="?action=taohoso" class="btn btn-success mt-3">+ Tạo hồ sơ bệnh nhân mới</a>
</div>
</body>
</html>

<!-- <script>
  function confirmBooking() {
    event.preventDefault();
    const thongtinhoadon = 'Thanh toán chi phí khám';
    const giakham = document.getElementById("giakham").value;
    const ngaykham = document.getElementById("ngaykham").value;
    const khunggiokb = document.getElementById("makhunggiokb").value;
    const chuyenkhoaEl = document.getElementById("tenchuyenkhoa");
    const chuyenkhoa = chuyenkhoaEl ? chuyenkhoaEl.value : '';
    const bacsiEl = document.getElementById("mabacsi");
    const bacsi = bacsiEl ? bacsiEl.value : '';

    const data = new URLSearchParams();
    data.append('giakham', giakham);
    data.append('thongtinhoadon', thongtinhoadon);
    data.append('ngaykham', ngaykham);
    data.append('khunggiokb', khunggiokb);
    data.append('chuyenkhoa', chuyenkhoa);
    data.append('bacsi', bacsi);

    fetch('views/benhnhan/pages/vnpay/create-payment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: data.toString()
    })
    .then(response => response.json())
    .then(data => {
      if (data.success && data.paymentUrl) window.location.href = data.paymentUrl;
      else alert('Có lỗi xảy ra trong quá trình tạo yêu cầu thanh toán.');
    })
    .catch(error => {
      console.error('Lỗi:', error);
      alert('Có lỗi xảy ra, vui lòng thử lại.');
    });
    return false;
  }
</script> -->


