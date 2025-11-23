<?php
include_once("Assets/config.php");
include_once("Controllers/cbenhnhan.php");
include_once("Controllers/ctaikhoan.php");
include_once("Controllers/ctinhthanhpho.php");
include_once("Controllers/cxaphuong.php");
include_once("Assets/config.php");

// 🟡 Kiểm tra đăng nhập
if (!isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập.</p>";
    exit;
}

// 🟡 Lấy mã bệnh nhân (string)
$id = $_GET['mabenhnhan'] ?? null;
if (!$id) {
    echo "<p>Không có mã bệnh nhân để sửa.</p>";
    exit;
}

// 🟡 Lấy danh sách tỉnh, xã/phường
$cthanhpho = new cTinhThanhPho();
$thanhpho_list = $cthanhpho->get_tinhthanhpho();

$cxaphuong = new cXaPhuong();
$xaphuong_list = $cxaphuong->get_xaphuong();

// 🟡 Lấy thông tin bệnh nhân
$pBenhNhan = new cBenhNhan();
$benhnhan = $pBenhNhan->getbenhnhanbyid($id);
if (!$benhnhan) {
    echo "<p>Không tìm thấy hồ sơ bệnh nhân với mã: $id</p>";
    exit;
}



// 🟡 Tính tuổi
$tuoi = 0;
if(!empty($benhnhan['ngaysinh'])){
    $birthDate = new DateTime($benhnhan['ngaysinh']);
    $today = new DateTime();
    $tuoi = $today->diff($birthDate)->y;
}

$message = "";

// 🟡 Hàm giữ dữ liệu cũ (tránh ghi đè bằng chuỗi rỗng)
function keepOld($newValue, $oldValue) {
    return (isset($newValue) && $newValue !== '' && $newValue !== null) ? $newValue : $oldValue;
}

// 🟡 Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sdt = keepOld($_POST['sdt'] ?? '', decryptData($benhnhan['sdt']));
    $email = !empty($_POST['emailcanhan']) ? $_POST['emailcanhan'] : null;
    $dantoc = $_POST['dantoc'] ?? $benhnhan['dantoc'];
    $nghenghiep = keepOld($_POST['nghenghiep'] ?? '', $benhnhan['nghenghiep']);
    $tiensu_banthan = $_POST['tiensubenhtatcuabenhnhan'] ?? '';
    $tiensu_giadinh = $_POST['tiensubenhtatcuagiadinh'] ?? '';
    $sonha = $_POST['diachi'] ?? '';
    $tinh = $_POST['tinh'] ?? '';
    $xa = $_POST['xa'] ?? '';

    $uploadDir = "Assets/img/cccd/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $cccd_truoc_name = $benhnhan['cccd_matruoc'] ?? null;
    $cccd_sau_name = $benhnhan['cccd_matsau'] ?? null;
    $giaykhaisinh_name = $benhnhan['giaykhaisinh'] ?? null;

    // 🟡 Hàm upload ảnh an toàn
    function uploadFile($fileInput, $prefix, $id, $uploadDir){
        if(isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error']===0){
            $allowed = ['jpg','jpeg','png','gif'];
            $ext = strtolower(pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION));
            if(!in_array($ext, $allowed)) return null;
            $filename = $prefix . "_{$id}_" . time() . "." . $ext;
            move_uploaded_file($_FILES[$fileInput]['tmp_name'], $uploadDir . $filename);
            return $filename;
        }
        return null;
    }

    if($tuoi < 16){
        $giaykhaisinh_uploaded = uploadFile('giaykhaisinh','giaykhaisinh',$id,$uploadDir);
        if($giaykhaisinh_uploaded) $giaykhaisinh_name = $giaykhaisinh_uploaded;
    } else {
        $cccd_truoc_uploaded = uploadFile('cccd_truoc','truoc',$id,$uploadDir);
        if($cccd_truoc_uploaded) $cccd_truoc_name = $cccd_truoc_uploaded;

        $cccd_sau_uploaded = uploadFile('cccd_sau','sau',$id,$uploadDir);
        if($cccd_sau_uploaded) $cccd_sau_name = $cccd_sau_uploaded;
    }

    // 🟡 Gọi model update
    $updateResult = $pBenhNhan->updateBenhNhan(
        $id,
        $benhnhan['hoten'],
        $benhnhan['ngaysinh'],
        $benhnhan['gioitinh'],
        $benhnhan['cccd'],
        $dantoc,
        encryptData($sdt),
        encryptData($email),
        $sonha,
        $xa,
        $nghenghiep,
        $tiensu_giadinh,
        $tiensu_banthan,
        $giaykhaisinh_name,
        $cccd_truoc_name,
        $cccd_sau_name
    );

    $message = $updateResult ? "✅ Cập nhật hồ sơ thành công!" : "❌ Có lỗi xảy ra khi cập nhật hồ sơ.";
    $benhnhan = $pBenhNhan->getbenhnhanbyid($id); // Load lại sau khi update
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa hồ sơ bệnh nhân</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f5f7fb; font-family: 'Segoe UI', sans-serif; padding-top:60px;}
.container {max-width:1000px; margin:auto; background:#fff; padding:50px; border-radius:25px; box-shadow:0 15px 35px rgba(0,0,0,0.1);}
h2 {text-align:center; margin-bottom:40px; background:linear-gradient(90deg,#5e2d91,#9b59b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent;}
.form-row {display:flex; flex-wrap:wrap; gap:30px;}
.form-col {flex:1; min-width:300px;}
input.form-control, select.form-select, textarea.form-control {border-radius:12px; border:1px solid #d1d7e0; padding:14px; width:100%; background:#fafafa;}
input.form-control:focus, select.form-select:focus, textarea.form-control:focus {border-color:#5e2d91; box-shadow:0 0 10px rgba(94,45,145,0.15); background:#fff;}
.image-preview-gh {max-width:220px; max-height:160px; border-radius:15px; box-shadow:0 8px 18px rgba(0,0,0,0.12);}
.btn-primary {background:linear-gradient(90deg,#5e2d91,#9b59b6); border:none; font-weight:600; padding:12px 25px;}
.btn-secondary {background:#6c757d; border:none; font-weight:500; padding:12px 25px;}
.alert {border-radius:12px; padding:15px 20px;}
@media(max-width:768px){.form-row{flex-direction:column;}}
</style>
</head>
<body>
<div class="container">
<h2>Sửa hồ sơ bệnh nhân</h2>

<?php if($message): ?>
<div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
<div class="form-row">
<div class="form-col">
    <div class="mb-3">
        <label class="form-label">Họ tên</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($benhnhan['hoten']) ?>" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Ngày sinh</label>
        <input type="date" class="form-control" value="<?= htmlspecialchars($benhnhan['ngaysinh']) ?>" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" name="sdt" value="<?= htmlspecialchars(decryptData($benhnhan['sdt'] ?? '')) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Email cá nhân</label>
        <input type="text" class="form-control" name="emailcanhan" value="<?= htmlspecialchars(decryptData($benhnhan['emailcanhan'] ?? '')) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Tiền sử bệnh tật của bản thân</label>
        <textarea class="form-control" name="tiensubenhtatcuabenhnhan" rows="3"><?= htmlspecialchars($benhnhan['tiensubenhtatcuabenhnhan'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Tiền sử bệnh tật của gia đình</label>
        <textarea class="form-control" name="tiensubenhtatcuagiadinh" rows="3"><?= htmlspecialchars($benhnhan['tiensubenhtatcuagiadinh'] ?? '') ?></textarea>
    </div>
</div>

    <div class="form-col">
        <div class="mb-3">
            <label class="form-label">Giới tính</label>
            <select class="form-select" disabled>
                <option <?= $benhnhan['gioitinh']=='Nam'?'selected':'' ?>>Nam</option>
                <option <?= $benhnhan['gioitinh']=='Nữ'?'selected':'' ?>>Nữ</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Số CCCD/CMND</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars(decryptData($benhnhan['cccd'])) ?>" readonly>
        </div>
        <div class="mb-3">
    <label class="form-label" for="dantoc">Dân tộc</label>
    <select name="dantoc" id="dantoc" class="form-control">
        <!-- Placeholder -->
        <option value="" disabled <?= empty($benhnhan['dantoc']) ? 'selected' : '' ?>>--Chọn dân tộc--</option>
        
        <?php 
        $ds_dantoc = ["Kinh","Tày","Thái","Hoa","Khơ-me","Mường","Nùng","HMông","Dao",
                      "Gia-rai","Ngái","Ê-đê","Ba-na","Xơ-Đăng","Sán chay","Cơ-ho","Chăm",
                      "Sán Dìu","Hrê","Mnông","Ra-glai","Xtiêng","Bru-Vân Kiều","Giáy",
                      "Cơ-tu","Triêng","Mạ","Khơ-mú","Co","Tà-ôi","Chơ-ro","Kháng",
                      "Xinh-mun","Hà Nhì","Chu ru","Lào","La Chí","La Ha","Phù Lá",
                      "La Hủ","Lự","Lô Lô","Chứt","Mảng","Pà Thẻn","Co Lao","Cống",
                      "Bố Y","Si La","Pu Péo","Brâu","Ơ Đu","Rơ măm"];

        foreach($ds_dantoc as $dt) {
            $selected = ($benhnhan['dantoc'] ?? '') === $dt ? 'selected' : '';
            echo "<option value=\"$dt\" $selected>$dt</option>";
        }
        ?>
    </select>
</div>


    <div class="mb-3">
        <label class="form-label">Nghề nghiệp</label>
        <input type="text" class="form-control" name="nghenghiep" value="<?= htmlspecialchars($benhnhan['nghenghiep'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Địa chỉ (Số nhà, đường)</label>
        <input type="text" class="form-control" name="diachi" value="<?= htmlspecialchars($benhnhan['sonha'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Tỉnh / Thành phố</label>
        <select class="form-select" name="tinh" id="tinh" onchange="loadXaPhuong()" required>
            <option value="">-- Chọn tỉnh/thành phố --</option>
            <?php foreach($thanhpho_list as $tp): ?>
            <option value="<?= $tp['matinhthanhpho'] ?>" <?= ($benhnhan['matinhthanhpho']??'')==$tp['matinhthanhpho']?'selected':'' ?>><?= htmlspecialchars($tp['tentinhthanhpho']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Xã / Phường</label>
        <select class="form-select" name="xa" id="xa" required>
            <option value="">-- Chọn xã/phường --</option>
        </select>
    </div>
</div>
</div>

<div class="mt-4 p-3 bg-light rounded-4">
<h5>Ảnh giấy tờ</h5>
<div class="d-flex flex-wrap gap-3">
<?php if($tuoi<16): ?>
<div class="mb-3">
<label>Giấy khai sinh</label>
<input type="file" name="giaykhaisinh" accept="image/*" onchange="previewImage(this,'preview-ks')">
<img id="preview-ks" class="image-preview-gh" src="<?= !empty($benhnhan['giaykhaisinh'])?'Assets/img/cccd/'.$benhnhan['giaykhaisinh']:'' ?>" style="<?= empty($benhnhan['giaykhaisinh'])?'display:none;':'' ?>">
</div>
<?php else: ?>
<div class="mb-3">
<label>CCCD mặt trước</label>
<input type="file" name="cccd_truoc" accept="image/*" onchange="previewImage(this,'preview-truoc')">
<img id="preview-truoc" class="image-preview-gh" src="<?= !empty($benhnhan['cccd_matruoc'])?'Assets/img/cccd/'.$benhnhan['cccd_matruoc']:'' ?>" style="<?= empty($benhnhan['cccd_matruoc'])?'display:none;':'' ?>">
</div>
<div class="mb-3">
<label>CCCD mặt sau</label>
<input type="file" name="cccd_sau" accept="image/*" onchange="previewImage(this,'preview-sau')">
<img id="preview-sau" class="image-preview-gh" src="<?= !empty($benhnhan['cccd_matsau'])?'Assets/img/cccd/'.$benhnhan['cccd_matsau']:'' ?>" style="<?= empty($benhnhan['cccd_matsau'])?'display:none;':'' ?>">
</div>
<?php endif; ?>
</div>
</div>

<div class="d-flex justify-content-between mt-3">
<a href="?action=caidat" class="btn btn-secondary">← Quay lại</a>
<button type="submit" class="btn btn-primary">Lưu thay đổi</button>
</div>
</form>
</div>

<script>
const xaphuongs = <?= json_encode($xaphuong_list) ?>;
function loadXaPhuong(){
    const tinh = document.getElementById("tinh").value;
    const xa = document.getElementById("xa");
    xa.innerHTML = '<option value="">-- Chọn xã/phường --</option>';
    if(!tinh) return;
    xaphuongs.filter(x=>x.matinhthanhpho==tinh).forEach(xaItem=>{
        const opt = document.createElement('option');
        opt.value = xaItem.maxaphuong;
        opt.textContent = xaItem.tenxaphuong;
        xa.appendChild(opt);
    });
    xa.value = "<?= $benhnhan['maxaphuong'] ?? '' ?>";
}

window.addEventListener('DOMContentLoaded', ()=>{ 
    if("<?= $benhnhan['matinhthanhpho'] ?? '' ?>") loadXaPhuong(); 
});

function previewImage(input, id){
    const file = input.files[0];
    const preview = document.getElementById(id);
    if(file){
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display='block'; };
        reader.readAsDataURL(file);
    } else { preview.style.display='none'; }
}
</script>
</body>
</html>
