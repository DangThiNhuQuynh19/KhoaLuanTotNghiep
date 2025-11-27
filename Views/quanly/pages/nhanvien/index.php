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

<style>
/* ===== HEADER ===== */
.main-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.main-header h2 {
    font-size: 24px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.main-header i {
    color: #3b82f6;
    font-size: 28px;
}

/* ===== ALERT ===== */
.alert-status {
    margin-bottom: 20px;
    padding: 14px 16px;
    border-radius: 6px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease-out;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== TAB NAVIGATION ===== */
.nav-tabs {
    display: flex;
    border-bottom: 2px solid #e0e0e0;
    margin-bottom: 24px;
    gap: 8px;
}

.nav-tabs .nav-link {
    padding: 12px 16px;
    background: transparent;
    color: #666;
    font-weight: 500;
    font-size: 14px;
    border: none;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
}

.nav-tabs .nav-link.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

/* ===== SEARCH BAR ===== */
.search-bar {
    background: white;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.search-bar form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    flex: 1;
}

.textsearch {
    padding: 10px 12px;
    border: 1px solid #d0d0d0;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s ease;
    background: white;
    color: #333;
    min-width: 200px;
}

.textsearch:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.textsearch::placeholder {
    color: #999;
}

.btnsearch {
    padding: 10px 16px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btnsearch:hover {
    background: #2563eb;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-light {
    padding: 10px 16px;
    background: #f0f0f0;
    color: #333;
    border: 1px solid #d0d0d0;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}

.btn-light:hover {
    background: #e0e0e0;
    border-color: #999;
}

.btn-success {
    padding: 10px 16px;
    background: #10b981;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: #059669;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

/* ===== CARD ITEMS ===== */
.card-item {
    background: white;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    display: flex;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e8e8e8;
}

.card-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    border-color: #3b82f6;
}

.card-item img {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    background: #f0f0f0;
}

.card-info {
    flex: 1;
}

.card-info h5 {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
}

.card-info p {
    font-size: 13px;
    color: #666;
    margin: 4px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-info p i {
    color: #3b82f6;
    font-size: 14px;
}

.card-info > div {
    display: flex;
    gap: 12px;
    margin-top: 12px;
}

/* ===== BUTTONS ===== */
.btn-detail, .btn-edit {
    padding: 8px 14px;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-detail {
    background: #3b82f6;
    color: white;
}

.btn-detail:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-edit {
    background: #f59e0b;
    color: white;
}

.btn-edit:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
}

/* ===== EMPTY STATE ===== */
.text-center {
    text-align: center;
    color: #999;
    padding: 40px 20px;
    font-size: 14px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .main-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .main-header h2 {
        font-size: 20px;
    }

    .search-bar {
        flex-direction: column;
        align-items: stretch;
    }

    .search-bar form {
        flex-direction: column;
    }

    .textsearch {
        min-width: 100%;
    }

    .card-item {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .card-item img {
        width: 80px;
        height: 80px;
    }

    .card-info > div {
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-success {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .main-container {
        padding: 16px;
    }

    .main-header h2 {
        font-size: 18px;
    }

    .nav-tabs {
        overflow-x: auto;
    }

    .card-item {
        padding: 12px;
    }

    .card-info h5 {
        font-size: 14px;
    }

    .btn-detail, .btn-edit {
        flex: 1;
        justify-content: center;
    }
}
</style>

<div class="main-container">
    <div class="main-header">
        <h2><i class="bi bi-people"></i> Quản lý nhân sự</h2>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="alert-status alert-success">
                <i class="bi bi-check-circle-fill"></i> Cập nhật thành công!
            </div>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <div class="alert-status alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> Đã xảy ra lỗi. Vui lòng thử lại!
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- TAB NAV -->
    <div class="nav-tabs" role="tablist">
        <button class="nav-link active" data-tab="bacsi" onclick="switchTab(event, 'bacsi')">Bác sĩ</button>
        <button class="nav-link" data-tab="chuyengia" onclick="switchTab(event, 'chuyengia')">Chuyên gia</button>
        <button class="nav-link" data-tab="nhanvien" onclick="switchTab(event, 'nhanvien')">Nhân viên</button>
    </div>

    <div class="tab-content">
        <!-- BÁC SĨ -->
        <div class="tab-pane active" id="bacsi">
            <div class="search-bar">
                <form method="GET">
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
                    <button class="btnsearch" type="submit"><i class="bi bi-search"></i> Tìm</button>
                    <a href="?action=nhanvien&tab=bacsi" class="btn-light"><i class="bi bi-x-circle"></i> Bỏ lọc</a>
                </form>
                <a href="?action=thembacsi" class="btn-success"><i class="bi bi-plus-circle"></i> Tạo mới</a>
            </div>
            <?php if($dsBacSi && $dsBacSi->num_rows>0){
                while($row=$dsBacSi->fetch_assoc()){ ?>
                <div class="card-item">
                    <img src="Assets/img/<?= htmlspecialchars($row['imgbs']) ?>" alt="<?= htmlspecialchars($row['hoten']) ?>">
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
        <div class="tab-pane" id="chuyengia" style="display: none;">
            <div class="search-bar">
                <form method="GET">
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
                    <button class="btnsearch" type="submit"><i class="bi bi-search"></i> Tìm</button>
                    <a href="?action=nhanvien&tab=chuyengia" class="btn-light"><i class="bi bi-x-circle"></i> Bỏ lọc</a>
                </form>
                <a href="?action=themchuyengia" class="btn-success"><i class="bi bi-plus-circle"></i> Tạo mới</a>
            </div>
            <?php if($dsChuyenGia && $dsChuyenGia->num_rows>0){
                while($row=$dsChuyenGia->fetch_assoc()){ ?>
                <div class="card-item">
                    <img src="Assets/img/<?= htmlspecialchars($row['imgcg']) ?>" alt="<?= htmlspecialchars($row['hoten']) ?>">
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
        <div class="tab-pane" id="nhanvien" style="display: none;">
            <div class="search-bar">
                <form method="GET">
                    <input type="hidden" name="action" value="nhanvien">
                    <input type="hidden" name="tab" value="nhanvien">
                    <input class="textsearch" type="text" name="tennv" placeholder="Tên nhân viên..." value="<?= htmlspecialchars($tennv) ?>">
                    <button class="btnsearch" type="submit"><i class="bi bi-search"></i> Tìm</button>
                    <a href="?action=nhanvien&tab=nhanvien" class="btn-light"><i class="bi bi-x-circle"></i> Bỏ lọc</a>
                </form>
                <a href="?action=themnhanvien" class="btn-success"><i class="bi bi-plus-circle"></i> Tạo mới</a>
            </div>
            <?php if($dsNhanVien && $dsNhanVien->num_rows>0){
                while($row=$dsNhanVien->fetch_assoc()){ ?>
                <div class="card-item">
                    <img src="Assets/img/<?= htmlspecialchars($row['imgnv'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($row['hoten']) ?>">
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

<script>
function switchTab(event, tabName) {
    event.preventDefault();
    
    // Ẩn tất cả tab pane
    const panes = document.querySelectorAll('.tab-pane');
    panes.forEach(pane => pane.style.display = 'none');
    
    // Bỏ active từ tất cả nav-link
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => link.classList.remove('active'));
    
    // Hiện tab được chọn
    document.getElementById(tabName).style.display = 'block';
    
    // Đánh dấu nav-link là active
    event.target.classList.add('active');
    
    // Cập nhật URL
    window.history.pushState(null, null, `?action=nhanvien&tab=${tabName}`);
}

// Lấy tab từ URL
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'bacsi';
    const tabButton = document.querySelector(`[data-tab="${tab}"]`);
    if (tabButton) {
        tabButton.click();
    }
});
</script>

