<?php
// Cập nhật view/handler cho trang quản lý lịch khám
// File này xử lý POST khi lưu hồ sơ bệnh án từ modal và cập nhật trạng thái phiếu thành "Đã khám".

include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/chosobenhandientu.php'); // controller hồ sơ bệnh án (nếu có)
include_once('Controllers/cthuoc.php');
include_once('Controllers/cloaixetnghiem.php');
include_once('Controllers/ckhunggioxetnghiem.php');
include_once("Assets/config.php"); // cung cấp $conn nếu cần (PDO hoặc mysqli)

// Include handler for complete examination
if (isset($_POST['btnHoanTat'])) {
    include_once('Controllers/xulyhoantatkham.php');
}

$cbacsi = new cBacSi();
$cphieukhambenh = new cPhieuKhamBenh();
$chsba = new cHoSoBenhAnDienTu();
$cthuoc = new cThuoc();
$cloaixetnghiem = new cLoaiXetNghiem();
$ckhunggioxetnghiem = new cKhungGioXetNghiem();

// Get data for form dropdowns
$thuoc_list = $cthuoc->get_thuoc();
$loaixetnghiem_list = $cloaixetnghiem->get_loaixetnghiem();
$khunggioxetnghiem_list = $ckhunggioxetnghiem->get_khunggioxetnghiem();

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

<!-- Modal: Enhanced modal with tabs for diagnosis, medications, and lab tests -->
<div id="modalBackdrop" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-content" style="max-width: 1000px;">
        <span class="close" id="modalClose">&times;</span>
        <h3 style="margin-bottom: 20px; color: var(--primary);">Khám & Cập nhật hồ sơ bệnh án</h3>
        
        <form id="hosobenhanForm" method="POST" action="">
            <input type="hidden" name="btnHoanTat" value="1">
            <input type="hidden" name="maphieukhambenh" id="form_maphieu">
            <input type="hidden" name="mabenhnhan" id="form_mabenhnhan">
            <input type="hidden" name="mahoso" id="form_mahoso">

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

            <!-- Tabs for different sections -->
            <div class="tabs update-tabs" style="margin-top: 20px;">
                <div class="tab-header">
                    <a href="#diagnosis-tab" class="update-tab-link active" onclick="openModalTab(event, 'diagnosis-tab')">Chẩn đoán</a>
                    <a href="#medication-tab" class="update-tab-link" onclick="openModalTab(event, 'medication-tab')">Đơn thuốc</a>
                    <a href="#labtest-tab" class="update-tab-link" onclick="openModalTab(event, 'labtest-tab')">Xét nghiệm</a>
                </div>

                <!-- Tab: Diagnosis -->
                <div id="diagnosis-tab" class="update-tab-content active" style="padding: 15px 0;">
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
                            <label>Hướng điều trị:</label>
                            <textarea name="huongdieutri" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col" style="flex: 1 1 100%;">
                            <label>Kết luận:</label>
                            <textarea name="ketluan" rows="2" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tab: Medications -->
                <div id="medication-tab" class="update-tab-content" style="padding: 15px 0; display: none;">
                    <div class="medication-form">
                        <h4 class="form-title" style="margin-bottom: 15px;">Thêm thuốc vào đơn</h4>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="tenthuoc">Tên thuốc:</label>
                                <select id="tenthuoc" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                    <option value="">-- Chọn thuốc --</option>
                                    <?php foreach($thuoc_list as $thuoc): ?>
                                        <option value="<?php echo $thuoc['mathuoc']; ?>"><?php echo htmlspecialchars($thuoc['tenthuoc']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="lieudung">Liều dùng:</label>
                                <input type="text" id="lieudung" placeholder="Ví dụ: 3 lần/ngày" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="thoigianuong">Thời gian uống:</label>
                                <input type="text" id="thoigianuong" placeholder="Ví dụ: Sau ăn" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                            <div class="form-col">
                                <label for="songayuong">Số ngày uống:</label>
                                <input type="text" id="songayuong" placeholder="Ví dụ: 7 ngày" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            </div>
                        </div>
                        <div style="text-align: right; margin-top: 10px;">
                            <button type="button" class="btn-primary btn-small" onclick="addMedicationToList()">
                                <i class="fas fa-plus"></i> Thêm
                            </button>
                        </div>
                    </div>
                    
                    <div id="bangthuoc" style="display: none; margin-top: 20px;">
                        <h4 style="margin-bottom: 10px;">Danh sách thuốc</h4>
                        <table class="data-table" id="medicationTable">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên thuốc</th>
                                    <th>Liều dùng</th>
                                    <th>Thời gian</th>
                                    <th>Số ngày</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="medicationTableBody">
                            </tbody>
                        </table>
                    </div>
                    <div id="medicationsContainer"></div>
                </div>

                <!-- Tab: Lab Tests -->
                <div id="labtest-tab" class="update-tab-content" style="padding: 15px 0; display: none;">
                    <div class="form-row">
                        <div class="form-col">
                            <label for="test">Loại xét nghiệm:</label>
                            <select name="test" id="test" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="">-- Chọn loại xét nghiệm --</option>
                                <?php foreach($loaixetnghiem_list as $loai): ?>
                                    <option value="<?php echo $loai['maloaixetnghiem']; ?>"><?php echo htmlspecialchars($loai['tenloaixetnghiem']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <label for="appointmentDate">Ngày hẹn:</label>
                            <input type="date" name="appointmentDate" id="appointmentDate" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                        <div class="form-col">
                            <label for="appointmentTime">Giờ hẹn:</label>
                            <select name="appointmentTime" id="appointmentTime" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="">-- Chọn khung giờ --</option>
                                <?php foreach($khunggioxetnghiem_list as $khunggio): ?>
                                    <option value="<?php echo $khunggio['makhunggio']; ?>"><?php echo htmlspecialchars($khunggio['giobatdau'] . ' - ' . $khunggio['gioketthuc']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col" style="flex: 1 1 100%;">
                            <label for="ghichu">Ghi chú:</label>
                            <textarea name="ghichu" id="ghichu" rows="2" placeholder="Ghi chú thêm về xét nghiệm" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align:right; margin-top:20px; padding-top: 15px; border-top: 1px solid #ddd;">
                <button type="button" id="modalCancelBtn" class="btn-danger btn-small"><i class="fas fa-times"></i> Hủy</button>
                <button type="submit" class="btn-primary btn-small"><i class="fas fa-save"></i> Lưu & Hoàn tất</button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab switching for modal
function openModalTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("update-tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("update-tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabName).style.display = "block";
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
    evt.preventDefault();
}

// Medication list management
var medicationList = [];
var medicationIndex = 0;

function addMedicationToList() {
    var mathuoc = document.getElementById('tenthuoc').value;
    var tenthuoc = document.getElementById('tenthuoc').options[document.getElementById('tenthuoc').selectedIndex].text;
    var lieudung = document.getElementById('lieudung').value;
    var thoigianuong = document.getElementById('thoigianuong').value;
    var songayuong = document.getElementById('songayuong').value;

    if (!mathuoc || !lieudung || !thoigianuong || !songayuong) {
        alert('Vui lòng điền đầy đủ thông tin thuốc');
        return;
    }

    var medication = {
        index: medicationIndex++,
        mathuoc: mathuoc,
        tenthuoc: tenthuoc,
        lieudung: lieudung,
        thoigianuong: thoigianuong,
        songayuong: songayuong
    };

    medicationList.push(medication);
    updateMedicationTable();
    updateMedicationInputs();

    // Clear form
    document.getElementById('tenthuoc').value = '';
    document.getElementById('lieudung').value = '';
    document.getElementById('thoigianuong').value = '';
    document.getElementById('songayuong').value = '';
}

function removeMedication(index) {
    medicationList = medicationList.filter(function(med) {
        return med.index !== index;
    });
    updateMedicationTable();
    updateMedicationInputs();
}

function updateMedicationTable() {
    var tbody = document.getElementById('medicationTableBody');
    tbody.innerHTML = '';

    if (medicationList.length > 0) {
        document.getElementById('bangthuoc').style.display = 'block';
        medicationList.forEach(function(med, idx) {
            var row = tbody.insertRow();
            row.innerHTML = '<td>' + (idx + 1) + '</td>' +
                '<td>' + med.tenthuoc + '</td>' +
                '<td>' + med.lieudung + '</td>' +
                '<td>' + med.thoigianuong + '</td>' +
                '<td>' + med.songayuong + '</td>' +
                '<td><button type="button" class="btn-danger btn-small" onclick="removeMedication(' + med.index + ')"><i class="fas fa-trash"></i></button></td>';
        });
    } else {
        document.getElementById('bangthuoc').style.display = 'none';
    }
}

function updateMedicationInputs() {
    var container = document.getElementById('medicationsContainer');
    container.innerHTML = '';

    medicationList.forEach(function(med, idx) {
        container.innerHTML += '<input type="hidden" name="medications[' + idx + '][mathuoc]" value="' + med.mathuoc + '">';
        container.innerHTML += '<input type="hidden" name="medications[' + idx + '][lieudung]" value="' + med.lieudung + '">';
        container.innerHTML += '<input type="hidden" name="medications[' + idx + '][thoigianuong]" value="' + med.thoigianuong + '">';
        container.innerHTML += '<input type="hidden" name="medications[' + idx + '][songayuong]" value="' + med.songayuong + '">';
    });
}

// Mở modal khi bấm Khám
document.querySelectorAll('.btn-kham').forEach(function(btn){
    btn.addEventListener('click', function(){
        var maphieu = btn.getAttribute('data-maphieu');
        var mabenhnhan = btn.getAttribute('data-mabenhnhan');
        var mahoso = btn.getAttribute('data-mahoso') || '';
        var hoten = btn.getAttribute('data-hoten');
        var ngay = btn.getAttribute('data-ngay');

        document.getElementById('form_maphieu').value = maphieu;
        document.getElementById('form_mabenhnhan').value = mabenhnhan;
        document.getElementById('form_mahoso').value = mahoso;
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

        // Reset tabs to first tab
        document.querySelectorAll('.update-tab-content').forEach(function(tab) {
            tab.style.display = 'none';
            tab.classList.remove('active');
        });
        document.querySelectorAll('.update-tab-link').forEach(function(link) {
            link.classList.remove('active');
        });
        document.getElementById('diagnosis-tab').style.display = 'block';
        document.getElementById('diagnosis-tab').classList.add('active');
        document.querySelector('.update-tab-link').classList.add('active');

        // Reset medication list
        medicationList = [];
        medicationIndex = 0;
        updateMedicationTable();

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
