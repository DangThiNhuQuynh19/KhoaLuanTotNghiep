<?php
include_once('Controllers/cbacsi.php');
include_once('Controllers/cchuyenkhoa.php');
include_once('Controllers/ctinhthanhpho.php');
include_once('Controllers/cxaphuong.php');
include_once('Assets/config.php');

$cChuyenKhoa = new cChuyenKhoa();
$cthanhpho   = new cTinhThanhPho();
$cxaphuong   = new cXaPhuong();
$cBacSi      = new cBacSi();

$dsKhoa    = $cChuyenKhoa->getAllChuyenKhoa();
$tinh_list = $cthanhpho->get_tinhthanhpho();
$xa_list   = $cxaphuong->get_xaphuong();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $mabs = $cBacSi->generateDoctorCode(); 
} else {
    $mabs = $_POST['mabs'] ?? $cBacSi->generateDoctorCode();
}

$old = $_POST ?? [];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $res = $cBacSi->luuBacSi($_POST, $_FILES);

    if ($res == 1) {
        echo "<script>alert('Lưu bác sĩ thành công');window.location='?action=nhanvien&tab=bacsi';</script>";
        exit();
    } elseif ($res == -1) {
        $msg = '<div class="alert alert-warning text-center">Vui lòng nhập đầy đủ thông tin</div>';
    } else {
        $msg = '<div class="alert alert-danger text-center">Lưu bác sĩ thất bại</div>';
    }
}
?>

<style>
/* ======= FORM CĂN GIỮA ======= */
.page-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 40px 15px;
}

.form-box {
    width: 100%;
    max-width: 850px;
    background: #ffffff;
    padding: 30px 40px;
    border-radius: 16px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.12);
    border-top: 4px solid #6f42c1;
}

.form-box h2 {
    color: #6f42c1;
    font-weight: 700;
    margin-bottom: 25px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #6f42c1;
    margin-top: 20px;
}

/* input focus */
.form-control:focus, 
.form-select:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 0 0.15rem rgba(111,66,193,0.3);
}

/* ảnh preview */
#preview {
    width: 120px;
    height: 140px;
    border-radius: 10px;
    margin-top: 10px;
    display: none;
    object-fit: cover;
    border: 1px solid #6f42c1;
}

/* nút */
.btn-success {
    background-color: #6f42c1;
    border-color: #6f42c1;
    padding: 8px 24px;
    font-weight: 500;
}

.btn-success:hover {
    background-color: #5a2e91;
    border-color: #5a2e91;
}

/* dropdown dân tộc */
#dantoc {
    border-color: #6f42c1;
    color: #4b0082;
}
</style>

<div class="page-wrapper">

<div class="form-box">

<h2 class="text-center">Thêm Bác sĩ mới</h2>

<?= $msg ?>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="mabs" value="<?= $mabs ?>">
<input type="hidden" name="email" value="">

<!-- ======================== 1. THÔNG TIN CÁ NHÂN ======================== -->
<div class="section-title">1. Thông tin cá nhân</div>
<div class="row g-3">

    <div class="col-md-6">
        <label>Họ tên *</label>
        <input type="text" name="hoten" required class="form-control"
               value="<?= htmlspecialchars($old['hoten'] ?? '') ?>">
    </div>

    <div class="col-md-3">
        <label>Ngày sinh</label>
        <input type="date" name="ngaysinh" class="form-control"
               value="<?= htmlspecialchars($old['ngaysinh'] ?? '') ?>">
    </div>

    <div class="col-md-3">
        <label>Giới tính</label>
        <select name="gioitinh" class="form-select">
            <?php
            foreach (['Nam','Nữ','Khác'] as $gt) {
                $sel = (!empty($old['gioitinh']) && $old['gioitinh'] == $gt) ? 'selected' : '';
                echo "<option value='$gt' $sel>$gt</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-6">
        <label>CCCD *</label>
        <input type="text" name="cccd" maxlength="12" required
               class="form-control"
               oninput="this.value=this.value.replace(/\D/g,'')"
               value="<?= htmlspecialchars($old['cccd'] ?? '') ?>">
    </div>

    <div class="col-md-3">
        <label>Dân tộc</label>
        <select name="dantoc" id="dantoc" class="form-select">
        <option value="">--Chọn dân tộc--</option required>
                            <option value="Kinh">Kinh</option>
                            <option value="Tày">Tày</option>
                            <option value="Thái">Thái</option>
                            <option value="Hoa">Hoa</option>
                            <option value="Khơ-me">Khơ-Me</option>
                            <option value="Mường">Mường</option>
                            <option value="Nùng">Nùng</option>
                            <option value="HMông">HMông</option>
                            <option value="Dao">Dao</option>
                            <option value="Gia-rai">Gia-rai</option>
                            <option value="Ngái">Ngái</option>
                            <option value="Ê-đê">Ê-đê</option>
                            <option value="Ba-na">Ba-na</option>
                            <option value="Xơ-Đăng">Xơ-Đăng</option>
                            <option value="Sán chay">Sán chay</option>
                            <option value="Cơ-ho">Cơ-ho</option>
                            <option value="Chăm">Chăm</option>
                            <option value="Sán Dìu">Sán Dìu</option>
                            <option value="Hrê">Hrê</option>
                            <option value="Mnông">Mnông</option>
                            <option value="Ra-glai">Ra-glai</option>
                            <option value="Xtiêng">Xtiêng</option>
                            <option value="Bru-Vân Kiều">Bru-Vân Kiều</option>
                            <option value="Thổ">Giáy</option>
                            <option value="Cơ-tu">Cơ-tu</option>
                            <option value="Gié">Triêng</option>
                            <option value="Mạ">Mạ</option>
                            <option value="Khơ-mú">Khơ-mú</option>
                            <option value="Co">Co</option>
                            <option value="Tà-ôi">Tà-ôi</option>
                            <option value="Chơ-ro">Chơ-ro</option>
                            <option value="Kháng">Kháng</option>
                            <option value="Xinh-mun">Xinh-mun</option>
                            <option value="Hà Nhì">Hà Nhì</option>
                            <option value="Chu ru">Chu ru</option>
                            <option value="Lào">Lào</option>
                            <option value="La Chí">La Chí</option>
                            <option value="La Ha">La Ha</option>
                            <option value="Phù Lá">Phù Lá</option>
                            <option value="La Hủ">La Hủ</option>
                            <option value="Lự">Lự</option>
                            <option value="Lô Lô">Lô Lô</option>
                            <option value="Chứt">Chứt</option>
                            <option value="Mảng">Mảng</option>
                            <option value="Pà Thẻn">Pà Thẻn</option>
                            <option value="Co Lao">Co Lao</option>
                            <option value="Cống">Cống</option>
                            <option value="Bố Y">Bố Y</option>
                            <option value="Si La">Si La</option>
                            <option value="Pu Péo">Pu Péo</option>
                            <option value="Brâu">Brâu</option>
                            <option value="Ơ Đu">Ơ Đu</option>
                            <option value="Rơ măm">Rơ măm</option>
        </select>
    </div>

    <div class="col-md-3">
        <label>SĐT</label>
        <input type="text" name="sdt" class="form-control"
               value="<?= htmlspecialchars($old['sdt'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label>Email cá nhân</label>
        <input type="email" name="emailcanhan" class="form-control"
               value="<?= htmlspecialchars($old['emailcanhan'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label>Số nhà / Địa chỉ</label>
        <input type="text" name="sonha" class="form-control"
               value="<?= htmlspecialchars($old['sonha'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label>Tỉnh / Thành phố *</label>
        <select name="tinhthanhpho" id="tinhthanhpho" required class="form-select">
            <option value="">-- Chọn Tỉnh/TP --</option>
            <?php foreach ($tinh_list as $t) {
                $sel = (!empty($old['tinhthanhpho']) && $old['tinhthanhpho'] == $t['matinhthanhpho']) ? 'selected' : '';
                echo "<option value='{$t['matinhthanhpho']}' $sel>{$t['tentinhthanhpho']}</option>";
            } ?>
        </select>
    </div>

    <div class="col-md-6">
        <label>Xã / Phường *</label>
        <select name="xaphuong" id="xaphuong" required class="form-select">
            <option value="">-- Chọn Xã/Phường --</option>
        </select>
    </div>

</div>

<hr>

<!-- ======================== 2. THÔNG TIN HÀNH NGHỀ ======================== -->
<div class="section-title">2. Thông tin hành nghề</div>

<div class="row g-3">

    <div class="col-md-4">
        <label>Cấp bậc</label>
        <input type="text" name="capbac" class="form-control"
               value="<?= htmlspecialchars($old['capbac'] ?? '') ?>">
    </div>

    <div class="col-md-4">
        <label>Chuyên khoa *</label>
        <select name="machuyenkhoa" required class="form-select">
            <option value="">-- Chọn chuyên khoa --</option>
            <?php foreach ($dsKhoa as $k) {
                $sel = (!empty($old['machuyenkhoa']) && $old['machuyenkhoa'] == $k['machuyenkhoa']) ? 'selected' : '';
                echo "<option value='{$k['machuyenkhoa']}' $sel>{$k['tenchuyenkhoa']}</option>";
            } ?>
        </select>
    </div>

    <div class="col-md-4">
        <label>Giá khám *</label>
        <input type="text" name="giakham" required
               class="form-control"
               oninput="this.value=this.value.replace(/\D/g,'')"
               value="<?= htmlspecialchars($old['giakham'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label>Ngày bắt đầu</label>
        <input type="date" name="ngaybatdau" class="form-control"
               value="<?= htmlspecialchars($old['ngaybatdau'] ?? '') ?>">
    </div>

    <div class="col-md-6">
        <label>Ngày kết thúc</label>
        <input type="date" name="ngayketthuc" class="form-control"
               value="<?= htmlspecialchars($old['ngayketthuc'] ?? '') ?>">
    </div>

</div>

<hr>

<!-- ======================== 3. ẢNH & GIỚI THIỆU ======================== -->
<div class="section-title">3. Hình ảnh & Giới thiệu</div>

<div class="row g-3">

    <div class="col-md-4">
        <label>Ảnh bác sĩ</label>
        <input type="file" name="imgbs" class="form-control" accept="image/*" onchange="previewImg(event)">
        <img id="preview">
    </div>

    <div class="col-md-4">
        <label>CCCD mặt trước</label>
        <input type="file" name="cccd_matruoc" class="form-control" accept="image/*">
    </div>

    <div class="col-md-4">
        <label>CCCD mặt sau</label>
        <input type="file" name="cccd_matsau" class="form-control" accept="image/*">
    </div>

    <div class="col-md-6">
        <label>Mô tả ngắn</label>
        <textarea name="motabs" class="form-control" rows="3"><?= htmlspecialchars($old['motabs'] ?? '') ?></textarea>
    </div>

    <div class="col-md-6">
        <label>Giới thiệu chi tiết</label>
        <textarea name="gioithieubs" class="form-control" rows="3"><?= htmlspecialchars($old['gioithieubs'] ?? '') ?></textarea>
    </div>

</div>

<!-- ======================== BUTTON ======================== -->
<div class="text-end mt-4">
    <a href="?action=nhanvien&tab=bacsi" class="btn btn-secondary">Hủy</a>
    <button class="btn btn-success">Lưu thông tin</button>
</div>

</form>

</div> <!-- form-box -->
</div> <!-- wrapper -->

<script>
function previewImg(e){
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(e.target.files[0]);
    img.style.display = 'block';
}

const xaData = <?= json_encode($xa_list) ?>;

document.getElementById('tinhthanhpho').addEventListener('change', function(){
    loadXa(this.value);
});

function loadXa(matinh, selected = ''){
    const xaSelect = document.getElementById('xaphuong');
    xaSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

    xaData.forEach(x => {
        if (x.matinhthanhpho == matinh) {
            const opt = document.createElement('option');
            opt.value = x.maxaphuong;
            opt.textContent = x.tenxaphuong;
            if (selected == x.maxaphuong) opt.selected = true;
            xaSelect.appendChild(opt);
        }
    });
}

<?php if (!empty($old['tinhthanhpho'])): ?>
loadXa("<?= $old['tinhthanhpho'] ?>", "<?= $old['xaphuong'] ?? '' ?>");
<?php endif; ?>
</script>

</body>
</html>
