<?php
// Cập nhật view/handler cho trang quản lý lịch khám
// File này xử lý POST khi lưu hồ sơ bệnh án từ modal và cập nhật trạng thái phiếu thành "Đã khám".

include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/chosobenhandientu.php'); // controller hồ sơ bệnh án (nếu có)
include_once("Assets/config.php"); // cung cấp $conn nếu cần (PDO hoặc mysqli)

$cbacsi = new cBacSi();
$cphieukhambenh = new cPhieuKhamBenh();
$chsba = new cHoSoBenhAnDienTu();

// Xử lý POST lưu hồ sơ bệnh án từ modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_hsba'])) {
    // Lấy dữ liệu từ form
    $maphieu = $_POST['maphieukhambenh'] ?? '';
    $mabenhnhan = $_POST['mabenhnhan'] ?? '';
    $ngaykham = $_POST['ngaykham'] ?? date('Y-m-d');
    $trieuchung = $_POST['trieuchung'] ?? '';
    $chandoan = $_POST['chandoan'] ?? '';
    $huongdieutri = $_POST['huongdieutri'] ?? ($_POST['donthuoc'] ?? '');
    $donthuoc = $_POST['donthuoc'] ?? '';
    $thanhtien = $_POST['thanhtien'] ?? 0;
    $trangthai_post = $_POST['trangthai'] ?? 'Đã khám'; // mặc định lưu là Đã khám

    // 1) Tạo hồ sơ bệnh án (sử dụng controller nếu có function tương ứng, ngược lại dùng raw SQL)
    $insert_ok = false;
    if (isset($chsba) && method_exists($chsba, 'create_hsba')) {
        // Nếu controller có method create_hsba(array $data)
        try {
            $data = [
                'mabenhnhan' => $mabenhnhan,
                'mahoso' => '', // nếu cần mã tự sinh, controller sẽ xử lý
                'ngaytao' => $ngaykham,
                'trieuchungbandau' => $trieuchung,
                'chandoan' => $chandoan,
                'huongdieutri' => $huongdieutri,
                'donthuoc' => $donthuoc,
                'thanhtien' => $thanhtien,
                'mabacsi' => $_SESSION['user']['mabacsi'] ?? null
            ];
            $insert_ok = $chsba->create_hsba($data);
        } catch (Exception $e) {
            // nếu exception, fallback xuống SQL
            $insert_ok = false;
        }
    }

    if (!$insert_ok) {
        // Fallback: chèn trực tiếp vào DB. TÊN BẢNG VÀ CỘT có thể khác trong hệ thống của bạn.
        // Hãy điều chỉnh tên bảng/cột cho khớp nếu cần. Mình dùng tên giả là hosobenhandientu.
        try {
            if (isset($conn)) {
                // Giả sử $conn là PDO (nếu không thì cần thay đổi)
                $sql = "INSERT INTO hosobenhandientu (mabenhnhan, ngaytao, trieuchungbandau, chandoan, huongdieutri, donthuoc, thanhtien, mabacsi)
                        VALUES (:mabenhnhan, :ngaytao, :trieuchungbandau, :chandoan, :huongdieutri, :donthuoc, :thanhtien, :mabacsi)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':mabenhnhan' => $mabenhnhan,
                    ':ngaytao' => $ngaykham,
                    ':trieuchungbandau' => $trieuchung,
                    ':chandoan' => $chandoan,
                    ':huongdieutri' => $huongdieutri,
                    ':donthuoc' => $donthuoc,
                    ':thanhtien' => $thanhtien,
                    ':mabacsi' => $_SESSION['user']['mabacsi'] ?? null
                ]);
                $insert_ok = true;
            } else {
                // Nếu không có $conn, không chèn được — set false
                $insert_ok = false;
            }
        } catch (Exception $e) {
            $insert_ok = false;
        }
    }

    // 2) Cập nhật trạng thái phiếu khám sang "Đã khám" (sử dụng method controller nếu có)
    $update_ok = false;
    if (!empty($maphieu)) {
        if (method_exists($cphieukhambenh, 'update_trangthai')) {
            try {
                $update_ok = $cphieukhambenh->update_trangthai($maphieu, 'Đã khám');
            } catch (Exception $e) {
                $update_ok = false;
            }
        } else {
            // Fallback raw SQL cập nhật bảng phieukhambenh (tên bảng có thể khác)
            try {
                if (isset($conn)) {
                    $sql2 = "UPDATE phieukhambenh SET tentrangthai = :trangthai WHERE maphieukhambenh = :maphieu";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->execute([':trangthai' => 'Đã khám', ':maphieu' => $maphieu]);
                    $update_ok = $stmt2->rowCount() >= 0;
                } else {
                    $update_ok = false;
                }
            } catch (Exception $e) {
                $update_ok = false;
            }
        }
    }

    // Sau khi lưu + cập nhật, redirect về trang quản lý lịch để tránh submit lại khi reload
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// ------------------- PHẦN HIỂN THỊ -------------------
// Lấy thông tin bác sĩ hiện tại
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);

// Lấy dữ liệu tìm kiếm (chuyển sang GET để dễ phân trang và giữ params)
$tukhoa = $_GET['tukhoa'] ?? '';
$trangthai = $_GET['trangthai'] ?? '';
$ngay = $_GET['ngay'] ?? '';
$homnay_checked = isset($_GET['homnay']) ? 'checked' : '';

// Lấy default list theo bác sĩ
$lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);

// Checkbox Hôm nay
if(isset($_GET['homnay'])){
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_homnay($bacsi['mabacsi'], date('Y-m-d'));
}

// Tìm kiếm
if(isset($_GET["btntimkiem"])){
    $lichkham_list = $cphieukhambenh->search_phieukhamonl($tukhoa, $trangthai, $ngay, $bacsi['mabacsi']);
}

// Bỏ tìm kiếm
if(isset($_GET["btnbo"])){
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);
    $tukhoa = $trangthai = $ngay = '';
    $homnay_checked = '';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// PHÂN TRANG (server-side, đơn giản)
$total = is_array($lichkham_list) ? count($lichkham_list) : 0;
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$totalPages = $total ? max(1, ceil($total / $perPage)) : 1;
if($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$paged_list = $total ? array_slice($lichkham_list, $offset, $perPage) : [];

?>
<link rel="stylesheet" href="Views/bacsi/assets/css/csschitiethoso.css">

<div class="container">
    <div class="content-header">
        <h1>Quản lý lịch hẹn trực tuyến</h1>
    </div>

    <!-- SEARCH FORM (GET) -->
    <div class="card">
        <div class="card-header">
            <h2>Tìm kiếm lịch hẹn</h2>
        </div>
        <div class="card-body">
            <form class="search-form" method="GET">
                <div class="search-grid">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="tukhoa" placeholder="Tìm theo tên bệnh nhân, mã phiếu..." value="<?php echo htmlspecialchars($tukhoa); ?>">
                    </div>
                    
                    <div class="form-group">
                        <select name="trangthai">
                            <option value="">Trạng thái</option>
                            <option value="Chưa khám" <?php if($trangthai=='Chưa khám') echo 'selected'; ?>>Chưa khám</option>
                            <option value="Đã khám" <?php if($trangthai=='Đã khám') echo 'selected'; ?>>Đã khám</option>
                            <option value="Đã hủy" <?php if($trangthai=='Đã hủy') echo 'selected'; ?>>Đã hủy</option>
                            <option value="Chờ thanh toán" <?php if($trangthai=='Chờ thanh toán') echo 'selected'; ?>>Chờ thanh toán</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="date" name="ngay" value="<?php echo htmlspecialchars($ngay); ?>">
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn-primary" name="btntimkiem"><i class="fas fa-search"></i> Tìm kiếm</button>
                    <button type="submit" class="btn-danger" name="btnbo"><i class="fas fa-times"></i> Bỏ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Checkbox Hôm nay -->
    <form method="GET" style="display:flex; justify-content:flex-end; margin-bottom:10px;">
        <input type="hidden" name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
        <input type="hidden" name="trangthai" value="<?php echo htmlspecialchars($trangthai); ?>">
        <input type="hidden" name="ngay" value="<?php echo htmlspecialchars($ngay); ?>">
        <input type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php echo $homnay_checked; ?>>
        <label for="homnay" style="margin-left:5px;"><b>Hôm nay</b></label>
    </form>

    <!-- Table -->
    <div class="card">
        <div class="card-body no-padding">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Ngày</th>
                        <th>Ca</th>
                        <th>Bệnh nhân</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($paged_list)): ?>
                        <?php foreach($paged_list as $i): 
                            $tentrangthai = trim($i['tentrangthai']);
                            switch ($tentrangthai){
                                case 'Chưa khám': $statusClass='status-pending'; break;
                                case 'Đã khám': $statusClass='status-completed'; break;
                                case 'Đã hủy': $statusClass='status-canceled'; break;
                                case 'Chờ thanh toán': $statusClass='status-pending'; break;
                                default: $statusClass='';
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($i['maphieukhambenh']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($i['ngaykham'])); ?></td>
                            <td><?php echo htmlspecialchars($i['giobatdau']).' - '.htmlspecialchars($i['gioketthuc']); ?></td>
                            <td><?php echo htmlspecialchars($i['hoten']); ?></td>
                            <td><?php echo number_format($i['giakham'],0,',','.'); ?> VND</td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($i['tentrangthai']); ?></span></td>
                            <td>
                                <?php if($tentrangthai === 'Chưa khám'): ?>
                                    <a class="btn-primary btn-small" href="?action=tinnhan&id=<?php echo urlencode($i['mabenhnhan']); ?>"><i class="fas fa-comment-medical"></i> Nhắn tin</a>
                                    <button type="button" class="btn-success btn-small btn-kham" 
                                        data-maphieu="<?php echo htmlspecialchars($i['maphieukhambenh']); ?>"
                                        data-mabenhnhan="<?php echo htmlspecialchars($i['mabenhnhan']); ?>"
                                        data-hoten="<?php echo htmlspecialchars($i['hoten']); ?>"
                                        data-ngay="<?php echo htmlspecialchars($i['ngaykham']); ?>"
                                        ><i class="fas fa-stethoscope"></i> Khám</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; color:gray;">Không có lịch hẹn</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
            <div style="margin-top:12px; text-align:center; padding: 12px;">
                <?php
                $currentParams = $_GET;
                for($p=1;$p<=$totalPages;$p++){
                    $qs = http_build_query(array_merge($currentParams, ['page'=>$p]));
                    $active = $p == $page ? 'font-weight:bold; color: var(--primary);' : '';
                    echo '<a style="margin:0 6px; '.$active.' text-decoration: none;" href="?'.$qs.'">'.$p.'</a>';
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: giống form update trong chitiethosobenhan -->
<div id="modalBackdrop" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content">
        <span class="close" id="modalClose">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--primary);">Khám & Cập nhật hồ sơ bệnh án</h3>
        <form id="hosobenhanForm" method="POST" action="">
            <input type="hidden" name="save_hsba" value="1">
            <input type="hidden" name="maphieukhambenh" id="form_maphieu">
            <input type="hidden" name="mabenhnhan" id="form_mabenhnhan">

            <div class="form-row">
                <div class="form-col">
                    <label>Họ tên:</label>
                    <input type="text" id="form_hoten" readonly style="background-color: #f5f5f5;">
                </div>
                <div class="form-col">
                    <label>Ngày khám:</label>
                    <input type="date" name="ngaykham" id="form_ngay">
                </div>
            </div>

            <div class="form-row">
                <div class="form-col" style="flex: 1 1 100%;">
                    <label>Triệu chứng ban đầu:</label>
                    <textarea name="trieuchung" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col" style="flex: 1 1 100%;">
                    <label>Chẩn đoán:</label>
                    <textarea name="chandoan" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col" style="flex: 1 1 100%;">
                    <label>Hướng điều trị / Ghi chú:</label>
                    <textarea name="huongdieutri" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col" style="flex: 1 1 100%;">
                    <label>Đơn thuốc:</label>
                    <textarea name="donthuoc" rows="3" placeholder="Ghi rõ thuốc và liều dùng" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label>Tổng tiền (VND):</label>
                    <input type="number" name="thanhtien" min="0" step="1000" placeholder="Ví dụ: 200000" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div class="form-col">
                    <label>Trạng thái:</label>
                    <select name="trangthai" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="Đã khám">Đã khám</option>
                        <option value="Chờ thanh toán">Chờ thanh toán</option>
                    </select>
                </div>
            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="button" id="modalCancelBtn" class="btn-danger btn-small">Hủy</button>
                <button type="submit" class="btn-primary btn-small"><i class="fas fa-save"></i> Lưu & Hoàn tất</button>
            </div>
        </form>
    </div>
</div>

<script>
// Mở modal khi bấm Khám
document.querySelectorAll('.btn-kham').forEach(function(btn){
    btn.addEventListener('click', function(){
        var maphieu = btn.getAttribute('data-maphieu');
        var mabenhnhan = btn.getAttribute('data-mabenhnhan');
        var hoten = btn.getAttribute('data-hoten');
        var ngay = btn.getAttribute('data-ngay');

        document.getElementById('form_maphieu').value = maphieu;
        document.getElementById('form_mabenhnhan').value = mabenhnhan;
        document.getElementById('form_hoten').value = hoten;
        // format ngày cho input date
        try {
            var d = new Date(ngay);
            var yyyy = d.getFullYear();
            var mm = ('0' + (d.getMonth()+1)).slice(-2);
            var dd = ('0' + d.getDate()).slice(-2);
            document.getElementById('form_ngay').value = yyyy + '-' + mm + '-' + dd;
        } catch(e) {
            document.getElementById('form_ngay').value = '';
        }

        document.getElementById('modalBackdrop').style.display = 'block';
        document.getElementById('modalBackdrop').setAttribute('aria-hidden','false');
    });
});

document.getElementById('modalClose').addEventListener('click', function(){
    document.getElementById('modalBackdrop').style.display = 'none';
    document.getElementById('modalBackdrop').setAttribute('aria-hidden','true');
});

document.getElementById('modalCancelBtn').addEventListener('click', function(){
    document.getElementById('modalBackdrop').style.display = 'none';
    document.getElementById('modalBackdrop').setAttribute('aria-hidden','true');
});

document.getElementById('modalBackdrop').addEventListener('click', function(e){
    if (e.target === this) {
        this.style.display = 'none';
        this.setAttribute('aria-hidden','true');
    }
});
</script>
