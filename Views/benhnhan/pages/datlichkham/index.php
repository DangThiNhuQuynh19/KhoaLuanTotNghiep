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
      --input-border: #ced4da;
      --input-focus: var(--custom-purple);
    }

    body {
      background-color: #f4f6f9;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin-top: 100px;
    }

    h2 {
      color: var(--custom-purple);
      font-weight: 600;
    }

    label {
      font-weight: 500;
      margin-bottom: 4px;
    }

    .form-control,
    .form-select {
      padding: 10px 12px;
    }

    .btn-primary {
      background-color: var(--custom-purple);
      border-color: var(--custom-purple);
      border-radius: 50px;
      font-weight: 500;
      font-size: 16px;
      transition: 0.3s;
    }

    .btn-primary:hover {
      background-color: var(--custom-purple-dark);
      border-color: var(--custom-purple-dark);
    }

    #thanNhanSection {
      border: 2px dashed var(--custom-purple);
      border-radius: 12px;
      background-color: #fdf9ff;
      padding: 20px;
    }

    #thanNhanSection h5 {
      font-weight: 600;
      margin-bottom: 15px;
    }

    .container {
      max-width: 950px;
      background-color: #ffffff;
      padding: 35px 30px;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .text-center button {
      box-shadow: 0 4px 12px rgba(85, 45, 125, 0.3);
    }

    .card {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    .card-body {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
    }

    .doctor-info {
      flex: 1;
    }

    .doctor-image {
      width: 130px;
      height: 130px;
    }

    .doctor-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .card-title {
      font-size: 1.5rem;
      color: var(--custom-purple);
      font-weight: 600;
    }

    .card p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 10px;
    }

    .card .card-body p {
      margin-bottom: 15px;
    }

    .card .card-body p:last-child {
      margin-bottom: 0;
    }

    .card-body .card-title,
    .card-body p {
      padding-left: 10px;
      padding-right: 10px;
    }

    .card-body {
      background-color: #f9f9f9;
      border-radius: 10px;
    }

    .btn-success {
      border-radius: 50px;
      font-weight: 500;
      padding: 10px 20px;
      background-color: var(--custom-purple);
    }

    .text-danger {
      color: #dc3545;
      font-size: 1rem;
      font-weight: bold;
      padding: 10px;
      background-color: #f8d7da;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      margin-top: 10px;
      display: inline-block;
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      0% {
        transform: translateX(-100%);
        opacity: 0;
      }
      100% {
        transform: translateX(0);
        opacity: 1;
      }
    }

    .btn-primary[disabled] {
      background-color: #ccc;
      border-color: #ccc;
      cursor: not-allowed;
    }
  </style>
</head>
<?php
// Include các file controller cần thiết
include_once('Assets/config.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/clichkham.php');
include_once('Controllers/cchuyengia.php');

// Xử lý lấy thông tin bác sĩ, lịch làm việc
$idbs = $_GET['idbs'] ?? null;
$ngay = $_GET['ngay'] ?? null;
$ca = $_GET['ca'] ?? null;
$idcg = $_GET['idcg'] ?? null;
if ($idbs && $ngay && $ca) {
    $pBacSi = new cBacSi();
    $tblBacSi = $pBacSi->getBacSiById($idbs);
    if ($tblBacSi && $tblBacSi->num_rows > 0) {
        $bacSi = $tblBacSi->fetch_assoc();
        $hoten = $bacSi['hoten'];
        $capbac = $bacSi['capbac'];
        $chuyenKhoa = $bacSi['tenchuyenkhoa'];
        $gia = $bacSi['giakham'];
        $anh = $bacSi['imgbs'];
    } else {
        $error = "Không tìm thấy thông tin bác sĩ.";
    }

    $pLichKham = new cLichKham();
    $tblLich = $pLichKham->getlich($ca);
    if ($tblLich && $tblLich->num_rows > 0) {
        $lich = $tblLich->fetch_assoc();
        $giobatdau = $lich['giobatdau'];
        $gioketthuc = $lich['gioketthuc'];
    } else {
        $error = "Không tìm thấy thông tin ca làm việc.";
    }
}elseif ($idcg && $ngay && $ca) {
    $pChuyenGia = new cChuyenGia();
    $tblChuyenGia = $pChuyenGia->getChuyenGiaById($idcg);
    if ($tblChuyenGia && $tblChuyenGia->num_rows > 0) {
        $chuyengia = $tblChuyenGia->fetch_assoc();
        $hoten = $chuyengia['hoten'];
        $capbac = $chuyengia['capbac'];
        $chuyenKhoa = $chuyengia['tenlinhvuc'];
        $gia = $chuyengia['giatuvan'];
        $anh = $chuyengia['imgcg'];
    } else {
        $error = "Không tìm thấy thông tin chuyên gia.";
    }

    $pLichKham = new cLichKham();
    $tblLich = $pLichKham->getlich($ca);
    if ($tblLich && $tblLich->num_rows > 0) {
        $lich = $tblLich->fetch_assoc();
        $giobatdau = $lich['giobatdau'];
        $gioketthuc = $lich['gioketthuc'];
    } else {
        $error = "Không tìm thấy thông tin ca làm việc.";
    }
}
else {
    $error = "Thiếu tham số trên URL.";
}

include_once('Controllers/cBenhNhan.php');
$benhnhans = [];
if (isset($_SESSION['user']) && isset($_SESSION['user']['tentk'])) {
    $tentk = $_SESSION['user']['tentk'];
    // laymanguoidungbytentk
    $pBenhNhan = new cBenhNhan();
    $taikhoan = $pBenhNhan ->getbenhnhanbytk($tentk);
    $benhnhans = $pBenhNhan->getAllBenhNhanByTK($taikhoan['mabenhnhan']);
}

$batBuoc = [
    'hoten', 'ngaysinh', 'gioitinh',
    'dantoc', 'tentinhthanhpho', 'tenxaphuong', 'sonha'
];

// Kiểm tra các trường thông tin bắt buộc
function checkMissingFields($record, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (!isset($record[$field]) || trim($record[$field]) === '') {
            return true; // Có thiếu thông tin
        }
    }
    return false; // Đủ thông tin
}


include_once('Controllers/cphieukhambenh.php'); 
include_once('Controllers/clichlamviec.php');

if (isset($_POST['datlich'])) {
    $_SESSION['mabenhnhan'] = $_POST['mabenhnhan'];
    $_SESSION['makhunggiokb'] = $_POST['makhunggiokb'];
    $_SESSION['mabacsi'] = $_POST['mabacsi'];
    $_SESSION['ngaykham'] = $_POST['ngaykham'];
    $_SESSION['tongtien'] = $_POST['giakham'];
    $_SESSION['trangthai'] = 'Chưa khám';

    // Tạo mã phiếu khám bệnh ngẫu nhiên
    $maphieukb = 'PKB' . time() . rand(100, 999);
    $_SESSION['maphieukhambenh']=$maphieukb;
    $pPhieuKham = new cPhieuKhambenh();
    if ($pPhieuKham->kiemTraTrungLich($_SESSION['mabenhnhan'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'])) {
        echo '<div class="text-danger text-center">Bạn đã có lịch hẹn khám trong ca này vào ngày này rồi.</div>';
    }else{
        header("Location: ?action=thanhtoan");
        exit;
    }
  }

?>
<body>
<?php if ($idbs && $ngay && $ca || $idcg): ?>
  <div class="container text-center">
    <div class="card">
      <div class="card-body d-flex justify-content-center align-items-center">
        <div class="doctor-info text-start">
          <h5 class="card-title"><?php echo htmlspecialchars($capbac); ?> - <?php echo htmlspecialchars($hoten); ?></h5>
          <?php if ($idbs):?>
            <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($chuyenKhoa); ?></p>
          <?php elseif ($idcg):?>
            <p><strong>Lĩnh vực:</strong> <?php echo htmlspecialchars($chuyenKhoa); ?></p>
          <?php endif?>
          <p><strong>Ngày khám:</strong> <?php echo htmlspecialchars($ngay); ?></p>
          <p><strong>Giờ bắt đầu:</strong> <?php echo htmlspecialchars($giobatdau); ?></p>
          <p><strong>Giờ kết thúc:</strong> <?php echo htmlspecialchars($gioketthuc); ?></p>
          <?php if($idbs):?>
            <p><strong>Giá khám:</strong> <?php echo htmlspecialchars($gia);  ?> đồng</p>
          <?php elseif($idcg):?>
            <p><strong>Giá tư vấn:</strong> <?php echo htmlspecialchars($gia);  ?> đồng</p>
          <?php endif?>
        </div>
        <div class="doctor-image ms-4">
          <img src="Assets/img/<?php echo htmlspecialchars($anh); ?>" alt="Ảnh bác sĩ" class="img-fluid rounded-circle">
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <p class="text-danger text-center">Không có thông tin đặt lịch khám.</p>
<?php endif; ?>

<div class="container mt-5 mb-5">
  <h2 class="mb-4 text-center">Chọn hồ sơ bệnh nhân</h2>
  <?php if (!empty($benhnhans)): ?>
  <div class="row">
  <div class="accordion" id="benhNhanAccordion">
  <?php foreach ($benhnhans as $index => $bn): ?>
    <?php $thieuThongTin = checkMissingFields($bn, $batBuoc); ?>
    <div class="accordion-item mb-3">
      <h2 class="accordion-header" id="heading<?php echo $index; ?>">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
          <?php echo htmlspecialchars($bn['hoten']); ?>
        </button>
      </h2>
      <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#benhNhanAccordion">
        <div class="accordion-body">
          <div class="d-flex justify-content-between">
            <div class="w-60">
              <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($bn['ngaysinh']); ?></p>
              <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($bn['gioitinh']); ?></p>
              <p><strong>Nghề nghiệp:</strong> <?php echo htmlspecialchars($bn['nghenghiep']); ?></p>
              <p><strong>CCCD:</strong> <?php echo htmlspecialchars(decryptData($bn['cccd'])); ?></p>
              <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($bn['sonha']) . ', ' . htmlspecialchars($bn['tenxaphuong']) . ', '. htmlspecialchars($bn['tentinhthanhpho']); ?></p>
              <p><strong>Tiền sử bệnh tật của gia đình:</strong> <?php echo htmlspecialchars(decryptData($bn['tiensubenhtatcuagiadinh'])); ?></p>
              <p><strong>Tiền sử bệnh tật của bản thân:</strong> <?php echo htmlspecialchars(decryptData($bn['tiensubenhtatcuabenhnhan'])); ?></p>
            </div>
            <div class="w-40">
              <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars(decryptData($bn['sdt'])); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars(decryptData($bn['email'])); ?></p>
              <p><strong>Dân tộc:</strong> <?php echo htmlspecialchars($bn['dantoc']); ?></p>
            </div>
          </div>
          <?php if (!$thieuThongTin): ?>
            <form method="POST">
              <input type="hidden" name="mabenhnhan" value="<?php echo $bn['mabenhnhan']; ?>">
              <input type="hidden" name="makhunggiokb" value="<?php echo $ca; ?>">
              <input type="hidden" name="mabacsi" value="<?php echo $idbs; ?>">
              <input type="hidden" name="ngaykham" value="<?php echo $ngay; ?>">
              <input type="hidden" name="giakham" value="<?php echo $gia; ?>">
              <div class="text-center mt-3">
                <button type="submit" name="datlich" class="btn btn-primary">Đặt lịch khám</button>
              </div>
            </form>
          <?php else: ?>
            <div class="text-danger text-center mt-3">
              Hồ sơ chưa đủ thông tin để đặt lịch. Vui lòng cập nhật.
            </div>
          <?php endif; ?>
          <form method="POST" action="">
          <a href="?action=suahoso&mabenhnhan=<?php echo $bn['mabenhnhan']; ?>" class="btn btn-warning">Sửa hồ sơ</a>

            <a href="xoahoso.php?mabenhnhan=<?php echo $bn['mabenhnhan']; ?>" class="btn btn-danger"
              onclick="return confirm('Bạn có chắc chắn muốn xóa hồ sơ bệnh nhân này?');">
              Xóa hồ sơ
            </a>

          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
  </div>
  <?php else: ?>
    <p class="text-danger">Không có bệnh nhân nào được tìm thấy.</p>

  <?php endif; ?>
    <a href="?action=taohoso" class="btn btn-success"> + Tạo hồ sơ bệnh nhân mới</a>
</div>
</body>
</html>
