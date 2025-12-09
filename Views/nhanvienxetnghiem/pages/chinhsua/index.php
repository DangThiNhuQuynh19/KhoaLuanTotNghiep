<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once("Assets/config.php");
include_once('Controllers/clichxetnghiem.php');
include_once('Controllers/cthongbao.php');

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

    $con = mysqli_connect("localhost", "kltn", "Kltntrangquynh2025@", "hanhphuc");
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

    // Gửi thông báo cho bác sĩ đã tạo phiếu xét nghiệm
    $cThongBao = new cThongBao();
    $cThongBao->send_test_result_notification($malich);

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

// Extract existing test results if they exist
$existingResults = [];
// Check all rows for existing test results, not just the first one
foreach ($lichChiTiet as $row) {
    if (!empty($row['tenchisoxetnghiem'])) {
        $existingResults[] = [
            'tenchiso' => $row['tenchisoxetnghiem'],
            'giatri' => $row['giatriketqua'],
            'donvi' => $row['donviketqua'],
            'thamchieu' => $row['khoangthamchieu']
        ];
    }
}

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
<style>
/* Reset */
h2 {
    text-align: center;
    margin-bottom: 28px;
    color: #005f85;
    font-size: 26px;
    font-weight: 700;
}

/* ===== SECTION BOX ===== */
.info-section {
    background: #f9fbfd;
    border: 1px solid #d9e4ee;
    padding: 20px 22px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.info-section h3 {
    font-size: 18px;
    font-weight: 700;
    color: #005f85;
    margin-bottom: 15px;
}

.info-section table {
    width: 100%;
}

.info-section table td {
    padding: 7px 0;
    font-size: 15px;
    color: #333;
}

.info-section table strong {
    color: #004d66;
}

/* ===== TABLE RESULTS ===== */
.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.results-table th {
    background: #e4f1f7;
    padding: 10px;
    font-weight: 700;
    text-align: left;
    color: #005f85;
    border-bottom: 1px solid #c9dbe5;
}

.results-table td {
    padding: 10px 8px;
    border-bottom: 1px solid #e6edf2;
    color: #333;
}

.results-table tr:hover {
    background: #f2f7fa;
}

/* ===== STATUS BUTTONS ===== */
.status-button {
    padding: 6px 16px;
    border-radius: 20px;
    border: none;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
}

/* màu chuẩn theo trạng thái */
.btn-pending { background: #f0ad4e; }
.btn-inprogress { background: #0275d8; }
.btn-done { background: #5cb85c; }

/* ===== BACK BUTTON ===== */
.back-btn {
    display: inline-block;
    margin-top: 22px;
    padding: 10px 18px;
    background: #0275d8;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 15px;
    transition: 0.2s;
}

.back-btn:hover {
    background: #015fa8;
}

/* ===== MOBILE ===== */
@media (max-width: 600px) {
    .main-container {
        padding: 20px;
    }
    .info-section table td {
        display: block;
        width: 100%;
    }
}
/* Table chỉ số */
#resultsTable {
    margin-top: 10px;
}

#resultsTable th {
    padding: 10px;
    text-align: left;
    font-weight: 600;
}

#resultsTable td {
    padding: 8px;
}

#resultsTable input {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid #bbb;
    border-radius: 8px;
    outline: none;
    transition: 0.25s;
}

#resultsTable input:focus {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 2px rgba(108,92,231,0.2);
}

/* Button thêm chỉ số */
button {
    background: #6c5ce7;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.25s;
}

button:hover {
    background: #5549d8;
    transform: translateY(-1px);
}

/* Button xóa */
.btn-danger {
    background: #e74c3c !important;
}

.btn-danger:hover {
    background: #c0392b !important;
}

/* Form dưới */
table input[type="time"],
textarea {
    width: 100%;
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #bbb;
    margin-top: 6px;
}

textarea {
    resize: vertical;
}

a {
    display: inline-block;
    border-radius: 8px;
}

a:hover {
    opacity: 0.85;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

</style>
</head>
<body>
<div class="main-container">
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
                <?php if (!empty($existingResults)): ?>
                    <?php foreach ($existingResults as $result): ?>
                        <tr>
                            <td><input type="text" name="tenchiso[]" placeholder="VD: Glucose" value="<?= htmlspecialchars($result['tenchiso']) ?>"></td>
                            <td><input type="text" name="giatri[]" placeholder="VD: 5.6" value="<?= htmlspecialchars($result['giatri']) ?>"></td>
                            <td><input type="text" name="donvi[]" placeholder="VD: mmol/L" value="<?= htmlspecialchars($result['donvi']) ?>"></td>
                            <td><input type="text" name="thamchieu[]" placeholder="VD: 3.9 - 6.4" value="<?= htmlspecialchars($result['thamchieu']) ?>"></td>
                            <td><button type="button" onclick="removeRow(this)">🗑️</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td><input type="text" name="tenchiso[]" placeholder="VD: Glucose"></td>
                        <td><input type="text" name="giatri[]" placeholder="VD: 5.6"></td>
                        <td><input type="text" name="donvi[]" placeholder="VD: mmol/L"></td>
                        <td><input type="text" name="thamchieu[]" placeholder="VD: 3.9 - 6.4"></td>
                        <td><button type="button" onclick="removeRow(this)">🗑️</button></td>
                    </tr>
                <?php endif; ?>
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