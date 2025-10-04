<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once("Assets/config.php");
include_once('Controllers/clichxetnghiem.php');

// =======================
// XỬ LÝ LƯU FORM POST
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy id từ URL (GET), không từ form
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo "<p>Thiếu ID lịch xét nghiệm.</p>";
        exit;
    }

    $malich = intval($_GET['id']); // 👈 Lấy trực tiếp từ URL

    $tenchisoArr = $_POST['tenchiso'] ?? [];
    $giatriArr = $_POST['giatri'] ?? [];
    $donviArr = $_POST['donvi'] ?? [];
    $khoangArr = $_POST['thamchieu'] ?? [];
    $gioLay = $_POST['giolaymau'] ?? '';
    $nhanxet = $_POST['nhanxet'] ?? '';

    $con = mysqli_connect("localhost", "root", "", "hanhphuc");
    mysqli_set_charset($con, "utf8");

    $now = date('Y-m-d H:i:s');

    // Xóa kết quả cũ của lịch này
    mysqli_query($con, "DELETE FROM ketquaxetnghiem WHERE malichxetnghiem = $malich");

    // Thêm mới
    for ($i = 0; $i < count($tenchisoArr); $i++) {
        $tenchiso = mysqli_real_escape_string($con, $tenchisoArr[$i]);
        $giatri = mysqli_real_escape_string($con, $giatriArr[$i]);
        $donvi = mysqli_real_escape_string($con, $donviArr[$i]);
        $khoang = mysqli_real_escape_string($con, $khoangArr[$i]);
        $gioLayEsc = mysqli_real_escape_string($con, $gioLay);
        $nhanxetEsc = mysqli_real_escape_string($con, $nhanxet);

        if ($tenchiso !== '' && $giatri !== '') {
            $sql = "INSERT INTO ketquaxetnghiem (
                        malichxetnghiem, tenchisoxetnghiem, giatriketqua, donviketqua,
                        khoangthamchieu, ngaygiotraketqua, giolaymau, nhanxet
                    ) VALUES (
                        $malich, '$tenchiso', '$giatri', '$donvi',
                        '$khoang', '$now', '$gioLayEsc', '$nhanxetEsc'
                    )";
            mysqli_query($con, $sql);
        }
    }

    $updateStatus = "UPDATE lichxetnghiem SET matrangthai = 12 WHERE malichxetnghiem = $malich";
    mysqli_query($con, $updateStatus);

    mysqli_close($con);

    echo "<script>
        alert('✅ Cập nhật kết quả xét nghiệm thành công!');
        window.location.href = 'index.php';
    </script>";
    exit;
}


// =======================
// HIỂN THỊ GIAO DIỆN FORM
// =======================
if (!isset($_SESSION["dangnhap"]) || !isset($_SESSION["user"])) {
    echo "<p>Bạn chưa đăng nhập!</p>";
    exit;
}

// Kiểm tra id lịch
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<p>Không có lịch xét nghiệm được chọn.</p>";
    exit;
}

$cLichXN = new cLichXetNghiem();
$id = intval($_GET['id']);
$lichChiTiet = $cLichXN->get_chitietlichxetnghiem($id);

if(!$lichChiTiet || $lichChiTiet === 0){
    echo "<p>Không tìm thấy chi tiết lịch xét nghiệm.</p>";
    exit;
}

$lich = $lichChiTiet[0];

// Trạng thái
$statusMap = [
    10 => ['text'=>'Chờ thanh toán','class'=>'btn-pending'],
    11 => ['text'=>'Đang thực hiện','class'=>'btn-inprogress'],
    12 => ['text'=>'Đã có kết quả','class'=>'btn-done']
];
$statusId = (int)$lich['matrangthai'];
$statusText = $statusMap[$statusId]['text'] ?? $lich['tentrangthai'];
$statusClass = $statusMap[$statusId]['class'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chỉnh Sửa Kết Quả Xét Nghiệm</title>
<style>
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f5f7fa; margin:0; padding:0; }
.container { max-width:900px; margin:40px auto; padding:20px; background:#fff; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#6c3483; margin-bottom:30px;}
.info-section { margin-bottom:20px; }
.info-section h3 { margin-bottom:10px; color:#4b0082; border-bottom:1px solid #ddd; padding-bottom:5px;}
.info-section table { width:100%; border-collapse: collapse; }
.info-section table td { padding:8px; vertical-align: top; }
input[type="text"], textarea, input[type="time"] { width:100%; padding:6px 10px; border-radius:6px; border:1px solid #ccc; }
button { padding:6px 15px; border:none; border-radius:6px; background:#6c3483; color:#fff; cursor:pointer; }
button:hover { background:#4b0082; }
.status-button { padding:4px 12px; font-size:14px; border-radius:6px; font-weight:bold; border:none; cursor:default; }
.btn-pending { background:#ff9800; color:#fff; }
.btn-inprogress { background:#0d6efd; color:#fff; }
.btn-done { background:#6c757d; color:#fff; }
</style>
</head>
<body>
<div class="container">
<h2>Chỉnh Sửa Kết Quả Xét Nghiệm</h2>

<form method="post">
 

    <!-- Thông Tin Bác Sĩ -->
    <div class="info-section">
        <h3>Thông Tin Bác Sĩ</h3>
        <table>
            <tr><td><strong>Tên Bác Sĩ:</strong></td><td><?= htmlspecialchars($lich['ten_bacsi']) ?></td></tr>
            <tr><td><strong>Khoa:</strong></td><td><?= htmlspecialchars($lich['tenchuyenkhoa']) ?></td></tr>
            <tr><td><strong>SĐT:</strong></td><td><?= htmlspecialchars($lich['sdt_bacsi']) ?></td></tr>
            <tr><td><strong>Chức Vụ:</strong></td><td><?= htmlspecialchars($lich['chucvu_bacsi']) ?></td></tr>
        </table>
    </div>

    <!-- Thông Tin Bệnh Nhân -->
    <div class="info-section">
        <h3>Thông Tin Bệnh Nhân</h3>
        <table>
            <tr><td><strong>Tên Bệnh Nhân:</strong></td><td><?= htmlspecialchars($lich['ten_benhnhan']) ?></td></tr>
            <tr><td><strong>SĐT:</strong></td><td><?= htmlspecialchars(decryptData($lich['sdt_benhnhan'])) ?></td></tr>
            <tr><td><strong>Mã Bệnh Nhân:</strong></td><td><?= htmlspecialchars($lich['mabenhnhan']) ?></td></tr>
        </table>
    </div>
    <!-- Thông Tin Khám Bệnh -->
    <div class="info-section">
        <h3>Thông Tin Xét Nghiệm</h3>
        <table>
            <tr><td><strong>Triệu chứng ban đầu:</strong></td><td><?= htmlspecialchars($lich['trieuchungbandau']) ?></td></tr>
            <tr><td><strong>Chẩn đoán ban đầu của bác sĩ:</strong></td><td><?= htmlspecialchars($lich['chandoan']) ?></td></tr>
        </table>
    </div>
    <!-- Thông Tin Kết Quả -->
    <div class="info-section">
        <h3>Kết Quả Xét Nghiệm</h3>
        <table id="resultsTable" border="1" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th>Tên Chỉ Số</th>
                    <th>Giá Trị</th>
                    <th>Đơn Vị</th>
                    <th>Khoảng Tham Chiếu</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="tenchiso[]" placeholder="VD: Glucose"></td>
                    <td><input type="text" name="giatri[]" placeholder="VD: 5.6"></td>
                    <td><input type="text" name="donvi[]" placeholder="VD: mmol/L"></td>
                    <td><input type="text" name="thamchieu[]" placeholder="VD: 3.9 - 6.4"></td>
                    <td><button type="button" onclick="removeRow(this)">🗑️</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="addRow()" style="margin-top:10px; background:#0d6efd;">➕ Thêm chỉ số</button>

        <table style="margin-top:20px; width:100%;">
            <tr>
                <td><strong>Giờ Lấy Mẫu:</strong></td>
                <td><input type="time" name="giolaymau" value="<?= htmlspecialchars($lich['giolaymau'] ?? '') ?>"></td>
            </tr>
            <tr>
                <td><strong>Nhận xét:</strong></td>
                <td><textarea name="nhanxet" rows="5"><?= htmlspecialchars($lich['nhanxet'] ?? '') ?></textarea></td>
            </tr>
        </table>
    </div>

    <div style="display:flex; gap:10px; margin-bottom:20px;">
        <button type="submit">💾 Lưu Cập Nhật</button>
        <a href="index.php" style="padding:6px 12px; background:#6c3483; color:#fff; text-decoration:none; border-radius:6px;">← Trang chủ</a>
    </div>
</form>
</div>

<script>
function addRow() {
    const tbody = document.querySelector('#resultsTable tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="tenchiso[]" placeholder="VD: Glucose"></td>
        <td><input type="text" name="giatri[]" placeholder="VD: 5.6"></td>
        <td><input type="text" name="donvi[]" placeholder="VD: mmol/L"></td>
        <td><input type="text" name="thamchieu[]" placeholder="VD: 3.9 - 6.4"></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                🗑️
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

function removeRow(btn) {
    btn.closest('tr').remove();
}

document.querySelector('form').addEventListener('submit', function(e) {
    let valid = true;
    let errorMessages = [];

    const rows = document.querySelectorAll('#resultsTable tbody tr');
    if (rows.length === 0) {
        valid = false;
        errorMessages.push("⚠️ Vui lòng nhập ít nhất 1 chỉ số xét nghiệm.");
    }

    rows.forEach((row, index) => {
        const tenchiso = row.querySelector('input[name="tenchiso[]"]').value.trim();
        const giatri   = row.querySelector('input[name="giatri[]"]').value.trim();
        const donvi    = row.querySelector('input[name="donvi[]"]').value.trim();
        const thamchieu= row.querySelector('input[name="thamchieu[]"]').value.trim();

        if (tenchiso === '' || giatri === '' || donvi === '' || thamchieu === '') {
            valid = false;
            errorMessages.push(`⚠️ Dòng ${index + 1}: Vui lòng nhập đầy đủ Tên chỉ số, Giá trị, Đơn vị và Khoảng tham chiếu.`);
        }
    });

    const gioLay = document.querySelector('input[name="giolaymau"]').value.trim();
    if (gioLay === '') {
        valid = false;
        errorMessages.push("⚠️ Vui lòng nhập Giờ lấy mẫu.");
    }

    const nhanxet = document.querySelector('textarea[name="nhanxet"]').value.trim();
    if (nhanxet === '') {
        valid = false;
        errorMessages.push("⚠️ Vui lòng nhập Nhận xét.");
    }

    if (!valid) {
        e.preventDefault(); 
        alert(errorMessages.join("\n"));
    }
});
</script>


</body>
</html>
