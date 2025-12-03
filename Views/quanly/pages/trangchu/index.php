<?php
include_once("Controllers/cbacsi.php");
include_once("Controllers/cchuyenkhoa.php");
include_once("Controllers/cchuyengia.php");
include_once("Controllers/clinhvuc.php");
include_once("Controllers/cnhanvien.php");

$cBacSi = new cBacSi();
$cChuyenKhoa = new cChuyenKhoa();
$cChuyenGia = new cChuyenGia();
$cLinhVuc = new cLinhVuc();
$cNhanVien = new cNhanVien();

// --- Lấy danh sách cho filter ---
$dsKhoa = $cChuyenKhoa->getAllChuyenKhoa();
$dsLinhVuc = $cLinhVuc->getAllLinhVuc();

// --- Filter Bác sĩ ---
$tenbs = trim($_GET['tenbs'] ?? '');
$khoa = $_GET['khoa'] ?? '';
if ($tenbs && $khoa) $dsBacSi = $cBacSi->getBacSiByTenAndKhoa($tenbs, $khoa);
elseif ($tenbs) $dsBacSi = $cBacSi->getBacSiByName($tenbs);
elseif ($khoa) $dsBacSi = $cBacSi->getBacSiByKhoa($khoa);
else $dsBacSi = $cBacSi->getAllBacSi();

// --- Filter Chuyên gia ---
$tencg = trim($_GET['tencg'] ?? '');
$linhvuc = $_GET['linhvuc'] ?? '';
if ($tencg && $linhvuc) $dsChuyenGia = $cChuyenGia->getChuyenGiaByTenAndLinhVuc($tencg, $linhvuc);
elseif ($tencg) $dsChuyenGia = $cChuyenGia->getChuyenGiaByName($tencg);
elseif ($linhvuc) $dsChuyenGia = $cChuyenGia->getChuyenGiaByLinhVuc($linhvuc);
else $dsChuyenGia = $cChuyenGia->getAllChuyenGia();

// --- Filter Nhân viên ---
$tennv = trim($_GET['tennv'] ?? '');
if ($tennv !== '') {
    $dsNhanVien = $cNhanVien->getNhanVienByName($tennv);
} else {
    $dsNhanVien = $cNhanVien->getdanhsachnhanvien();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý nhân sự</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root {
  --primary: #3c1561;
  --primary-light: #5e2c8a;
  --secondary: #f5f5f7;
  --text-dark: #333;
  --text-muted: #666;
  --border-color: #e0e0e0;
  --card-bg: #fff;
  --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  --radius: 14px;
}

body {
  font-family: "Segoe UI", Tahoma, sans-serif;
  background-color: var(--secondary);
  color: var(--text-dark);
  line-height: 1.6;
}

h2 {
  color: var(--primary);
  font-weight: 700;
  letter-spacing: 0.5px;
}

/* Tabs */
.nav-tabs {
  border-bottom: 2px solid var(--primary);
  justify-content: center;
}
.nav-tabs .nav-link {
  color: var(--primary);
  font-weight: 500;
  border: none;
  padding: 10px 20px;
  transition: all 0.3s;
  border-radius: var(--radius) var(--radius) 0 0;
}
.nav-tabs .nav-link:hover {
  background-color: var(--primary-light);
  color: #fff;
}
.nav-tabs .nav-link.active {
  background-color: var(--primary);
  color: #fff;
  box-shadow: 0 -2px 8px rgba(60, 21, 97, 0.3);
}

/* Search bar */
.search-bar {
  margin: 25px 0 20px;
  background-color: var(--card-bg);
  padding: 15px 20px;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}
.textsearch {
  border-radius: 25px;
  width: 220px;
  height: 38px;
  padding: 0 15px;
  border: 1px solid var(--border-color);
  margin-right: 8px;
  font-size: 14px;
  transition: all 0.3s;
}
.textsearch:focus {
  border-color: var(--primary);
  outline: none;
  box-shadow: 0 0 0 2px rgba(60, 21, 97, 0.2);
}
.btnsearch {
  min-width: 100px;
  height: 38px;
  border-radius: 20px;
  color: #fff;
  background-color: var(--primary);
  border: none;
  font-weight: 500;
  transition: background-color 0.3s;
}
.btnsearch:hover { background-color: var(--primary-light); }
.btn-light { border-radius: 20px; border: 1px solid var(--border-color); transition: all 0.3s; }
.btn-light:hover { background-color: #f0f0f0; }
.btn-success { border-radius: 20px; font-weight: 500; box-shadow: 0 3px 6px rgba(25, 135, 84, 0.25); }

/* Card nhân sự */
.card-item {
  display: flex;
  gap: 20px;
  background-color: var(--card-bg);
  padding: 15px;
  margin-bottom: 15px;
  border-radius: var(--radius);
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow);
  transition: transform 0.3s, box-shadow 0.3s;
}
.card-item:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12); }
.card-item img {
  width: 140px; height: 140px; object-fit: cover; border-radius: var(--radius);
  border: 1px solid var(--border-color); flex-shrink: 0;
}
.card-info { flex: 1; display: flex; flex-direction: column; justify-content: center; }
.card-info h5 { margin: 0 0 5px; font-weight: 600; font-size: 18px; color: var(--primary); }
.card-info p { margin: 3px 0; color: var(--text-muted); font-size: 14px; }
.card-info a.btn { margin-top: 8px; border-radius: 20px; font-size: 13px; font-weight: 500; }

/* Buttons Xem chi tiết & Chỉnh sửa */
.card-info a.btn-detail, .card-info a.btn-edit {
    display: inline-flex !important;
    align-items: center; justify-content: center;
    width: auto !important; padding: 2px 8px;
    font-size: 12px; font-weight: 500; line-height: 1; border-radius: 18px;
    white-space: nowrap; text-decoration: none; margin-right: 5px;
}
.card-info a.btn-detail {
    color: var(--primary); border: 1px solid var(--primary); background-color: #fff;
    transition: all 0.25s ease;
}
.card-info a.btn-detail i { font-size: 13px; margin-right: 4px; }
.card-info a.btn-detail:hover { background-color: var(--primary); color: #fff; transform: translateY(-1px); box-shadow: 0 2px 6px rgba(60, 21, 97, 0.25); }

.card-info a.btn-edit {
    color: #198754; border: 1px solid #198754; background-color: #fff;
    transition: all 0.25s ease;
}
.card-info a.btn-edit i { font-size: 13px; margin-right: 4px; }
.card-info a.btn-edit:hover { background-color: #198754; color: #fff; transform: translateY(-1px); box-shadow: 0 2px 6px rgba(25, 135, 84, 0.25); }

/* Responsive */
@media (max-width: 768px) {
  .card-item { flex-direction: column; align-items: center; text-align: center; }
  .card-item img { width: 120px; height: 120px; }
  .search-bar { flex-direction: column; gap: 10px; }
}
</style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="text-center mb-4"><i class="bi bi-people"></i> Quản lý nhân sự</h2>
    <?php if (isset($_GET['status'])): ?>
    <div class="container mt-3">
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <i class="bi bi-check-circle-fill"></i> Cập nhật thành công!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Đã xảy ra lỗi. Vui lòng thử lại!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <!-- TAB NAV -->
    <ul class="nav nav-tabs" id="nhansuTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="bacsi-tab" data-bs-toggle="tab" data-bs-target="#bacsi" type="button">Bác sĩ</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="chuyengia-tab" data-bs-toggle="tab" data-bs-target="#chuyengia" type="button">Chuyên gia</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nhanvien-tab" data-bs-toggle="tab" data-bs-target="#nhanvien" type="button">Nhân viên</button>
        </li>
    </ul>

    <div class="tab-content" id="nhansuTabContent">
        <!-- BÁC SĨ -->
        <div class="tab-pane fade show active" id="bacsi">
            <div class="search-bar d-flex justify-content-between align-items-center">
                <form method="GET" class="d-flex flex-wrap">
                    <input type="hidden" name="action" value="nhanvien">
                    <input type="hidden" name="tab" value="bacsi">
                    <input class="textsearch" type="text" name="tenbs" placeholder="Tên bác sĩ..." value="<?= htmlspecialchars($tenbs) ?>">
                    <select name="khoa" class="textsearch">
                        <option value="">-- Chọn chuyên khoa --</option>
                        <?php if($dsKhoa && $dsKhoa->num_rows>0){ while($row=$dsKhoa->fetch_assoc()){ ?>
                            <option value="<?= $row['machuyenkhoa'] ?>" <?= ($row['machuyenkhoa']==$khoa)?'selected':''; ?>>
                                <?= htmlspecialchars($row['tenchuyenkhoa']) ?>
                            </option>
                        <?php }} ?>
                    </select>
                    <button class="btnsearch" type="submit">Tìm</button>
                    <a href="?action=nhanvien&tab=bacsi" class="btn btn-light">Bỏ lọc</a>
                </form>
                <a href="?action=thembacsi" class="btn btn-success ms-2"><i class="bi bi-plus-circle"></i> Tạo mới</a>
            </div>
            <?php if($dsBacSi && $dsBacSi->num_rows>0){
                while($row=$dsBacSi->fetch_assoc()){ ?>
            <div class="card-item bg-white">
                <img src="Assets/img/<?= htmlspecialchars($row['imgbs']) ?>" alt="">
                <div class="card-info">
                    <h5><?= htmlspecialchars($row['capbac'].' '.$row['hoten']) ?></h5>
                    <p><i class="bi bi-hospital"></i> <?= htmlspecialchars($row['tenchuyenkhoa']) ?></p>
                    <p><i class="bi bi-person-circle"></i> Trạng thái: <?= htmlspecialchars($row['tentrangthai']) ?></p>
                    <p><?= mb_strimwidth(strip_tags($row['motabs']),0,200,'...','UTF-8') ?></p>
                    <div>
                        <a href="?action=chitietbacsi&id=<?= urlencode($row['mabacsi']) ?>" class="btn-detail"><i class="bi bi-eye"></i> Xem chi tiết</a>
                        <a href="?action=suabacsi&id=<?= urlencode($row['mabacsi']) ?>" class="btn-edit"><i class="bi bi-pencil-square"></i> Chỉnh sửa</a>
                    </div>
                </div>
            </div>
            <?php }} else echo "<p class='text-center'>Không có bác sĩ.</p>"; ?>
        </div>

        <!-- CHUYÊN GIA -->
        <div class="tab-pane fade" id="chuyengia">
            <div class="search-bar d-flex justify-content-between align-items-center">
                <form method="GET" class="d-flex flex-wrap">
                    <input type="hidden" name="action" value="nhanvien">
                    <input type="hidden" name="tab" value="chuyengia">
                    <input class="textsearch" type="text" name="tencg" placeholder="Tên chuyên gia..." value="<?= htmlspecialchars($tencg) ?>">
                    <select name="linhvuc" class="textsearch">
                        <option value="">-- Chọn lĩnh vực --</option>
                        <?php if($dsLinhVuc && $dsLinhVuc->num_rows>0){ while($row=$dsLinhVuc->fetch_assoc()){ ?>
                            <option value="<?= $row['malinhvuc'] ?>" <?= ($row['malinhvuc']==$linhvuc)?'selected':''; ?>>
                                <?= htmlspecialchars($row['tenlinhvuc']) ?>
                            </option>
                        <?php }} ?>
                    </select>
                    <button class="btnsearch" type="submit">Tìm</button>
                    <a href="?action=nhanvien&tab=chuyengia" class="btn btn-light">Bỏ lọc</a>
                </form>
                <a href="?action=themchuyengia" class="btn btn-success ms-2"><i class="bi bi-plus-circle"></i> Tạo mới</a>
            </div>
            <?php if($dsChuyenGia && $dsChuyenGia->num_rows>0){
                while($row=$dsChuyenGia->fetch_assoc()){ ?>
            <div class="card-item bg-white">
                <img src="Assets/img/<?= htmlspecialchars($row['imgcg']) ?>" alt="">
                <div class="card-info">
                    <h5><?= htmlspecialchars($row['capbac'].' '.$row['hoten']) ?></h5>
                    <p><i class="bi bi-journal-medical"></i> <?= htmlspecialchars($row['tenlinhvuc']) ?></p>
                    <p><i class="bi bi-person-circle"></i> Trạng thái: <?= htmlspecialchars($row['tentrangthai']) ?></p>
                    <p><?= mb_strimwidth(strip_tags($row['motacg']),0,200,'...','UTF-8') ?></p>
                    <div>
                        <a href="?action=chitietchuyengia&id=<?= urlencode($row['machuyengia']) ?>" class="btn-detail"><i class="bi bi-eye"></i> Xem chi tiết</a>
                        <a href="?action=suachuyengia&id=<?= urlencode($row['machuyengia']) ?>" class="btn-edit"><i class="bi bi-pencil-square"></i> Chỉnh sửa</a>
                    </div>
                </div>
            </div>
            <?php }} else echo "<p class='text-center'>Không có chuyên gia.</p>"; ?>
        </div>

        <!-- NHÂN VIÊN -->
        <div class="tab-pane fade" id="nhanvien">
            <div class="search-bar d-flex justify-content-between align-items-center">
                <form method="GET" class="d-flex flex-wrap">
                    <input type="hidden" name="action" value="nhanvien">
                    <input type="hidden" name="tab" value="nhanvien">
                    <input class="textsearch" type="text" name="tennv" placeholder="Tên nhân viên..." value="<?= htmlspecialchars($tennv) ?>">
                    <button class="btnsearch" type="submit">Tìm</button>
                    <a href="?action=nhanvien&tab=nhanvien" class="btn btn-light">Bỏ lọc</a>
                </form>
                <a href="?action=themnhanvien" class="btn btn-success ms-2"><i class="bi bi-plus-circle"></i> Tạo mới</a>
            </div>
            <?php if($dsNhanVien && $dsNhanVien->num_rows>0){
                while($row=$dsNhanVien->fetch_assoc()){ ?>
            <div class="card-item bg-white">
                <img src="Assets/img/<?= htmlspecialchars($row['imgnv'] ?? 'default.png') ?>" alt="">
                <div class="card-info">
                    <h5><?= htmlspecialchars($row['hoten']) ?></h5>
                    <p><i class="bi bi-person-badge"></i> <?= htmlspecialchars($row['chucvu']) ?></p>
                    <p><i class="bi bi-person-circle"></i> Trạng thái: <?= htmlspecialchars($row['tentrangthai']) ?></p>
                    <div>
                        <a href="?action=chitietnhanvien&id=<?= urlencode($row['manhanvien']) ?>" class="btn-detail"><i class="bi bi-eye"></i> Xem chi tiết</a>
                        <a href="?action=suanhanvien&id=<?= urlencode($row['manhanvien']) ?>" class="btn-edit"><i class="bi bi-pencil-square"></i> Chỉnh sửa</a>
                    </div>
                </div>
            </div>
            <?php }} else echo "<p class='text-center'>Không có nhân viên.</p>"; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Giữ tab khi reload
const urlParams = new URLSearchParams(window.location.search);
const tab = urlParams.get('tab');
if(tab){
  const tabTrigger = document.querySelector(`#${tab}-tab`);
  if(tabTrigger){ new bootstrap.Tab(tabTrigger).show(); }
}
</script>
</body>
</html>
