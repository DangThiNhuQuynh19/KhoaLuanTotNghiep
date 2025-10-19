<?php
include_once("Controllers/cbacsi.php");
include_once("Controllers/ctinhthanhpho.php");
include_once("Controllers/cxaphuong.php");
include_once("Controllers/cchuyenkhoa.php");
include_once("Assets/config.php");

$error_message = '';

// Kiểm tra ID bác sĩ
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Không tìm thấy bác sĩ.";
    exit;
}
$mabacsi = $_GET['id'];

// Lấy thông tin bác sĩ
$cBacSi = new cBacSi();
$bacsi = $cBacSi->getBacSiById($mabacsi);

if (!$bacsi || $bacsi->num_rows === 0) {
    echo "Không tìm thấy thông tin bác sĩ.";
    exit;
}

$row = $bacsi->fetch_assoc();

// Lấy danh sách chuyên khoa
$cChuyenKhoa = new cChuyenkhoa();
$chuyenkhoaList = $cChuyenKhoa->getAllChuyenKhoa();
// Xử lý khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mabacsi = $_POST['mabacsi'];

    $data = [
        'capbac'            => $_POST['capbac'] ?? '',
        'hoten'             => $_POST['hoten'] ?? '',
        'ngaysinh'          => $_POST['ngaysinh'] ?? '',
        'gioitinh'          => $_POST['gioitinh'] ?? '',
        'cccd'              => $_POST['cccd'] ?? '',
        'dantoc'            => $_POST['dantoc'] ?? '',
        'sdt'               => $_POST['sdt'] ?? '',
        'emailcanhan'       => $_POST['emailcanhan'] ?? '',
        'machuyenkhoa'      => $_POST['machuyenkhoa'] ?? '',
        'giakham'           => $_POST['giakham'] ?? 0,
        'sonha'             => $_POST['sonha'] ?? '',
        'tenxaphuong'       => $_POST['xa'] ?? '',
        'tentinhthanhpho'   => $_POST['tinh'] ?? '',
        'motabs'            => $_POST['motabs'] ?? '',
        'gioithieubs'       => $_POST['gioithieubs'] ?? '',
    ];

    $cBacSi = new cBacSi();
    $result = $cBacSi->updateBacSi($mabacsi, $data);

    if ($result) {
        // ✅ Thành công → quay về danh sách kèm thông báo
        header("Location: index.php?action=nhanvien&tab=bacsi&status=success");
        exit;
    } else {
        // ❌ Lỗi → ở lại và hiện thông báo lỗi
        $error_message = "Cập nhật thông tin bác sĩ thất bại. Vui lòng thử lại.";
    }
}
$cthanhpho = new cTinhThanhPho();
$thanhpho_list = $cthanhpho->get_tinhthanhpho();

$cxaphuong = new cXaPhuong();
$xaphuong_list = $cxaphuong->get_xaphuong();
?>

<style>
.container {
    max-width: 1000px;
    margin: auto;
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-top: 20px;
}
.doctor-header {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    margin-bottom: 30px;
}
.doctor-header img {
    width: 180px;
    height: 220px;
    border-radius: 12px;
    border: 1px solid #ddd;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.doctor-info {
    flex: 1;
    min-width: 250px;
}
.doctor-info h2 {
    margin-top: 0;
    color: #3c1561;
    font-size: 26px;
    margin-bottom: 10px;
}
.form-table {
    width: 100%;
    border-collapse: collapse;
}
.form-table th {
    width: 25%;
    text-align: left;
    padding: 10px;
    background: #f8f8f8;
    vertical-align: top;
    font-weight: 600;
}
.form-table td {
    padding: 10px;
}
.form-control {
    width: 100%;
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border-color 0.2s;
}
.form-control:focus {
    border-color: #7b1fa2;
    outline: none;
}
textarea.form-control {
    resize: vertical;
    min-height: 80px;
}
.action-buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 25px;
}
.btn-action {
    flex: 1;
    text-align: center;
    padding: 12px 20px;
    font-size: 15px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.2s, transform 0.1s;
}
.btn-update {
    background-color: #552d7d;
    color: white;
    border: none;
}
.btn-update:hover {
    background-color: #6c3483;
    transform: scale(1.02);
}
.btn-back {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
}
.btn-back:hover {
    background-color: #e0e0e0;
    transform: scale(1.02);
}

/* Alert thông báo */
.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 14px;
    position: relative;
}
.alert-success {
    background: #e8f5e9;
    border: 1px solid #4caf50;
    color: #256029;
}
.alert-danger {
    background: #fdecea;
    border: 1px solid #f44336;
    color: #b71c1c;
}
.alert button.close {
    position: absolute;
    right: 10px;
    top: 5px;
    background: none;
    border: none;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    color: inherit;
}

    /* Tăng chiều cao và bo góc dropdown */
    .form-select {
        height: 45px;
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.2s ease;
        background-color: #fff;
    }

    /* Khi focus vào thì đổi màu viền */
    .form-select:focus {
        border-color: #4e73df;  /* xanh dịu */
        box-shadow: 0 0 0 0.15rem rgba(78, 115, 223, 0.25);
        outline: none;
    }

    /* Hiệu ứng hover */
    .form-select:hover {
        border-color: #4e73df;
    }

    /* Tùy chỉnh nhãn bảng */
    th {
        width: 180px;
        background-color: #f8f9fc;
        padding: 10px;
        text-align: left;
        font-weight: 600;
    }

    /* Tùy chỉnh ô nhập */
    td {
        padding: 10px;
        vertical-align: middle;
    }

    /* Căn bảng cho gọn */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }


</style>

<div class="container">

    <!-- Thông báo thành công khi redirect -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success">
            ✅ Cập nhật thông tin bác sĩ thành công!
            <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Thông báo lỗi nếu cập nhật thất bại -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            ❌ <?= htmlspecialchars($error_message) ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="doctor-header">
            <img src="Assets/img/<?php echo htmlspecialchars($row['imgbs']); ?>" alt="Ảnh bác sĩ">
            <div class="doctor-info">
                <table class="form-table">
                    <tr>
                        <th>Cấp bậc</th>
                        <td><input type="text" name="capbac" class="form-control" value="<?= htmlspecialchars($row['capbac']) ?>"></td>
                    </tr>
                    <tr>
                        <th>Họ tên</th>
                        <td><input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($row['hoten']) ?>"></td>
                    </tr>
                </table>
            </div>
        </div>

        <input type="hidden" name="mabacsi" value="<?= htmlspecialchars($row['mabacsi']) ?>">

        <table class="form-table">
            <tr>
                <th>Ngày sinh</th>
                <td><input type="date" name="ngaysinh" class="form-control" value="<?= htmlspecialchars($row['ngaysinh']) ?>"></td>
            </tr>
            <tr>
                <th>Giới tính</th>
                <td>
                    <select name="gioitinh" class="form-control">
                        <option value="Nam" <?= $row['gioitinh']=='Nam'?'selected':'' ?>>Nam</option>
                        <option value="Nữ" <?= $row['gioitinh']=='Nữ'?'selected':'' ?>>Nữ</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>CCCD</th>
                <td><input type="text" name="cccd" class="form-control" value="<?= htmlspecialchars($row['cccd']) ?>"></td>
            </tr>
            <tr>
                <th>Dân tộc</th>
                <td>
                    <select name="dantoc" id="dantoc" class="form-select" required>
                        <option value="">--Chọn dân tộc--</option>
                        <?php 
                        $danTocList = [
                            "Kinh","Tày","Thái","Hoa","Khơ-me","Mường","Nùng","HMông","Dao","Gia-rai","Ngái","Ê-đê",
                            "Ba-na","Xơ-Đăng","Sán chay","Cơ-ho","Chăm","Sán Dìu","Hrê","Mnông","Ra-glai","Xtiêng",
                            "Bru-Vân Kiều","Thổ","Cơ-tu","Gié","Mạ","Khơ-mú","Co","Tà-ôi","Chơ-ro","Kháng","Xinh-mun",
                            "Hà Nhì","Chu ru","Lào","La Chí","La Ha","Phù Lá","La Hủ","Lự","Lô Lô","Chứt","Mảng",
                            "Pà Thẻn","Co Lao","Cống","Bố Y","Si La","Pu Péo","Brâu","Ơ Đu","Rơ măm"
                        ];

                        // Chuẩn hóa dữ liệu trong DB (loại bỏ khoảng trắng thừa)
                        $currentDanToc = trim($row['dantoc'] ?? '');

                        foreach ($danTocList as $dt) {
                            $selected = (strcasecmp(trim($dt), $currentDanToc) === 0) ? 'selected' : '';
                            echo "<option value=\"{$dt}\" {$selected}>{$dt}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>


            <tr>
                <th>SĐT</th>
                <td><input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars(decryptData($row['sdt'])) ?>"></td>
            </tr>
            <tr>
                <th>Email TK</th>
                <td><input type="email" name="email" class="form-control" value="<?= htmlspecialchars(decryptData($row['email'])) ?>" readonly></td>
            </tr>
            <tr>
                <th>Email cá nhân</th>
                <td><input type="email" name="emailcanhan" class="form-control" value="<?= htmlspecialchars($row['emailcanhan']) ?>"></td>
            </tr>
            <tr>
                <th>Chuyên khoa</th>
                <td>
                    <select name="machuyenkhoa" class="form-control">
                        <?php while($ck = $chuyenkhoaList->fetch_assoc()): ?>
                            <option value="<?= $ck['machuyenkhoa'] ?>" 
                                <?= $ck['tenchuyenkhoa'] == $row['tenchuyenkhoa'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ck['tenchuyenkhoa']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Giá khám</th>
                <td>
                    <input 
                        type="number" 
                        name="giakham" 
                        class="form-control" 
                        value="<?= htmlspecialchars($row['giakham']) ?>" 
                        step="1" 
                        min="0" 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                    >
                </td>
            </tr>
            <tr>
                <th>Địa chỉ</th>
                <td>
                    <input type="text" name="sonha" class="form-control mb-2" placeholder="Số nhà" value="<?= htmlspecialchars($row['sonha']) ?>">
                    <div style="display: flex; gap: 10px;">
                        <select name="tinh" id="tinh" class="form-select" onchange="loadXaPhuong()">
                            <option value="">-- Chọn tỉnh/thành phố --</option>
                            <?php foreach($thanhpho_list as $i): ?>
                                <option value="<?= $i['matinhthanhpho']; ?>" <?= ($row['matinhthanhpho'] ?? '') == $i['matinhthanhpho'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($i['tentinhthanhpho']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="xa" id="xa" class="form-select">
                            <option value="">-- Chọn xã/phường --</option>
                        </select>
                    </div>
                </td>
            </tr>

            <tr>
                <th>Mô tả</th>
                <td><textarea name="motabs" class="form-control"><?= htmlspecialchars($row['motabs']) ?></textarea></td>
            </tr>
            <tr>
                <th>Giới thiệu</th>
                <td><textarea name="gioithieubs" class="form-control"><?= htmlspecialchars($row['gioithieubs']) ?></textarea></td>
            </tr>
        </table>

        <div class="action-buttons">
            <button type="submit" class="btn-action btn-update">Cập nhật</button>
            <a href="?action=nhanvien&tab=bacsi" class="btn-action btn-back">← Quay lại</a>
        </div>
    </form>
</div>


 <script>
    // Truyền danh sách xã từ PHP sang JS
    const xaphuongs = <?php echo json_encode($xaphuong_list); ?>;

    function loadXaPhuong() {
        const tinhSelect = document.getElementById("tinh");
        const xaSelect = document.getElementById("xa");
        const mathanhpho = tinhSelect.value;

        xaSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';

        if (!mathanhpho) return;

        const filtered = xaphuongs.filter(x => x.matinhthanhpho === mathanhpho);
        filtered.forEach(x => {
            const option = document.createElement('option');
            option.value = x.maxaphuong;
            option.textContent = x.tenxaphuong;
            xaSelect.appendChild(option);
        });
    }

    // Khi trang load xong → hiển thị xã cũ
    window.addEventListener('DOMContentLoaded', () => {
        loadXaPhuong();
        const currentXa = '<?= $row['maxaphuong'] ?? '' ?>';
        if (currentXa) {
            document.getElementById('xa').value = currentXa;
        }
    });
</script>

