<?php
// (File này là phiên bản đã chỉnh sửa của trang quản lý lịch hẹn trực tuyến)
// Bao gồm các controller cần thiết (giữ nguyên những include ban đầu)
include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbacsi.php');
// Nếu muốn controller xử lý tạo chi tiết hồ sơ/đơn thuốc ở đây, bạn có thể include thêm:
// include_once('Controllers/cchitiethoso.php');
// include_once('Controllers/cdonthuoc.php');
// include_once('Controllers/cchitietdonthuoc.php');

$cbacsi = new cBacSi();
$cphieukhambenh = new cPhieuKhamBenh();

// Lấy thông tin bác sĩ hiện tại
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);

// Khởi tạo giá trị mặc định
$lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);

// Lấy dữ liệu tìm kiếm trước đó
$tukhoa = $_POST['tukhoa'] ?? '';
$trangthai = $_POST['trangthai'] ?? '';
$ngay = $_POST['ngay'] ?? '';
$homnay_checked = isset($_POST['homnay']) ? 'checked' : '';

// Checkbox Hôm nay
if(isset($_POST['homnay'])){
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_homnay($bacsi['mabacsi'], date('Y-m-d'));
}

// Tìm kiếm
if(isset($_POST["btntimkiem"])){
    $lichkham_list = $cphieukhambenh->search_phieukhamonl($tukhoa, $trangthai, $ngay, $bacsi['mabacsi']);
}

// Bỏ tìm kiếm
if(isset($_POST["btnbo"])){
    $lichkham_list = $cphieukhambenh->get_lichkhamonl_mabacsi($bacsi['mabacsi']);
    $tukhoa = $trangthai = $ngay = '';
    $homnay_checked = '';
    $_POST = [];
}

/*
  LƯU Ý: Tôi để form modal gửi tới ?action=update_phieukhambenh (method POST).
  Endpoint đó cần thực hiện:
    - cập nhật trạng thái phieukhambenh = 'Đã khám' cho maphieukhambenh truyền lên
    - và (tuỳ nhu cầu) lưu chi tiết chẩn đoán / hướng điều trị / đơn thuốc

  Nếu bạn muốn xử lý trực tiếp tại trang này, có thể thêm code xử lý POST ở đây (nhưng tôi giữ việc gửi về action hiện có để nhất quán).
*/
?>
<style>
.btn-secondary {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-secondary:hover {
    background-color: #e6e6e6;
    border-color: #999;
    color: #000;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}
.btn-success { background-color: #28a745; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;}
.btn-primary { background-color: #007bff; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;}
.btn-danger { background-color: #dc3545; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer;}
.btn-small { padding: 6px 8px; font-size: 13px; }
.status-pending { color: orange; font-weight: bold; }
.status-completed { color: green; font-weight: bold; }
.status-canceled { color: red; font-weight: bold; }

/* Modal basic styles */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto;
    background-color: rgba(0,0,0,0.4); }
.modal-content { background-color: #fff; margin: 60px auto; padding: 20px; border-radius: 6px; width: 720px; max-width: 95%; position: relative; }
.modal .close { position: absolute; right: 12px; top: 8px; font-size: 22px; cursor: pointer; color: #666; }
.form-row { display:flex; gap:10px; margin-bottom:10px; }
.form-group { flex:1; display:flex; flex-direction:column; }
.form-group label { font-weight:600; margin-bottom:6px; }
.form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:12px; }
</style>

<div class="container">
<div class="content-header">
    <h1>Quản lý lịch hẹn trực tuyến</h1>
</div>

<div class="tabs">
    <div class="tab-content">
        <!-- Form tìm kiếm -->
        <div class="card">
            <div class="card-header">
                <h2>Tìm kiếm lịch hẹn</h2>
            </div>
            <div class="card-body">
                <form class="search-form" method="POST">
                    <div class="search-grid">
                        <div class="search-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="tukhoa" placeholder="Tìm theo tên bệnh nhân, mã phiếu..."
                                value="<?php echo htmlspecialchars($tukhoa); ?>">
                        </div>
                        
                        <div class="form-group">
                            <select name="trangthai">
                                <option value="">Trạng thái</option>
                                <option value="chưa khám" <?php if($trangthai=='chưa khám') echo 'selected'; ?>>Chưa khám</option>
                                <option value="đã khám" <?php if($trangthai=='đã khám') echo 'selected'; ?>>Đã khám</option>
                                <option value="đã hủy" <?php if($trangthai=='đã hủy') echo 'selected'; ?>>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <input type="date" name="ngay" value="<?php echo $ngay; ?>">
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 10px;">
                        <button type="submit" class="btn-primary" name="btntimkiem">Tìm kiếm</button>
                        <button type="submit" class="btn-danger" name="btnbo"><i class="fas fa-times"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Checkbox Hôm nay -->
        <form method="POST" style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
            <input type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php echo $homnay_checked; ?>>
            <label for="homnay" style="margin-left: 5px;"><b>Hôm nay</b></label>
        </form>

        <!-- Bảng lịch hẹn -->
        <div class="card">
            <div class="card-body no-padding">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày khám</th>
                            <th>Ca làm việc</th>
                            <th>Bệnh nhân</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($lichkham_list): ?>
                            <?php foreach ($lichkham_list as $i):
                                switch (strtolower($i['tentrangthai'])) {
                                    case 'chưa khám': $statusClass='status-pending'; break;
                                    case 'đã khám': $statusClass='status-completed'; break;
                                    case 'đã hủy': $statusClass='status-canceled'; break;
                                    default: $statusClass='';
                                }
                            ?>
                                <tr>
                                    <td><?php echo $i['maphieukhambenh']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($i['ngaykham'])); ?></td>
                                    <td><?php echo $i['giobatdau'].' - '.$i['gioketthuc']; ?></td>
                                    <td><?php echo $i['hoten']; ?></td>
                                    <td><?php echo number_format($i['giakham'],0,',','.').' VND'; ?></td>
                                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $i['tentrangthai']; ?></span></td>
                                    <td>
                                        <?php if($i['tentrangthai']=='Chưa khám' || strtolower($i['tentrangthai'])=='chưa khám'): ?>
                                            <button class="btn-primary btn-small" onclick="location.href='?action=tinnhan&id=<?php echo $i['mabenhnhan']; ?>'">
                                                <i class="fas fa-comment-medical"></i> Nhắn tin
                                            </button>

                                            <!-- Nút Hoàn tất khám: mở modal -->
                                            <button class="btn-success btn-small" onclick='openHoanTatModal(<?php echo json_encode($i, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                                <i class="fas fa-check"></i> Hoàn tất khám
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center; color:gray;">Không có lịch hẹn</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hoàn tất khám (mô phỏng giống modal cập nhật hồ sơ) -->
<div id="modalHoanTat" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeHoanTatModal()">&times;</span>
        <h2>Hoàn tất khám</h2>
        <p id="modalInfo" style="margin-bottom: 12px; color:#444;"></p>

        <!-- Gửi về action hiện tại xử lý cập nhật trạng thái -->
        <form id="hoantatForm" action="?action=update_phieukhambenh" method="POST">
            <!-- Hidden để controller nhận biết mã phiếu và bệnh nhân -->
            <input type="hidden" name="maphieukhambenh" id="modal_maphieu" value="">
            <input type="hidden" name="mabenhnhan" id="modal_mabenhnhan" value="">

            <div class="form-row">
                <div class="form-group">
                    <label for="trieuchung">Triệu chứng ban đầu</label>
                    <textarea id="modal_trieuchung" name="trieuchung" rows="3" placeholder="Mô tả triệu chứng..."></textarea>
                </div>
                <div class="form-group">
                    <label for="chandoan">Chẩn đoán</label>
                    <textarea id="modal_chandoan" name="chandoan" rows="3" placeholder="Chẩn đoán..."></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="huongdieutri">Hướng điều trị</label>
                    <textarea id="modal_huongdieutri" name="huongdieutri" rows="3" placeholder="Hướng điều trị..."></textarea>
                </div>
                <div class="form-group">
                    <label for="ketluan">Kết luận</label>
                    <textarea id="modal_ketluan" name="ketluan" rows="3" placeholder="Kết luận..." required></textarea>
                </div>
            </div>

            <!-- Nếu muốn gửi thêm dữ liệu thuốc, có thể thêm phần medications giống modal cập nhật hồ sơ -->
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeHoanTatModal()"><i class="fas fa-times"></i> Hủy</button>
                <button type="submit" name="btnHoanTat" class="btn-primary"><i class="fas fa-save"></i> Lưu & Cập nhật trạng thái</button>
            </div>
        </form>
        <small style="display:block; margin-top:10px; color:#666;">
            Sau khi lưu, hệ thống sẽ gửi dữ liệu đến endpoint cập nhật lịch (update_phieukhambenh).
        </small>
    </div>
</div>

<script>
    // Mở modal Hoàn tất khám và điền dữ liệu từ appointment object
    function openHoanTatModal(appointment) {
        try {
            // Nếu appointment là chuỗi JSON (trường hợp trình duyệt older), parse nó
            if (typeof appointment === 'string') {
                appointment = JSON.parse(appointment);
            }
        } catch (e) {
            console.error('Không thể parse dữ liệu appointment', e);
        }

        // Điền thông tin cơ bản
        document.getElementById('modalInfo').textContent = 'Phiếu: ' + (appointment.maphieukhambenh || '') +
            ' — Bệnh nhân: ' + (appointment.hoten || '') +
            ' — Ngày: ' + (appointment.ngaykham || '');

        document.getElementById('modal_maphieu').value = appointment.maphieukhambenh || '';
        document.getElementById('modal_mabenhnhan').value = appointment.mabenhnhan || '';

        // Xoá giá trị cũ của các textarea (nếu muốn giữ có thể comment)
        document.getElementById('modal_trieuchung').value = '';
        document.getElementById('modal_chandoan').value = '';
        document.getElementById('modal_huongdieutri').value = '';
        document.getElementById('modal_ketluan').value = '';

        // Hiển thị modal
        document.getElementById('modalHoanTat').style.display = 'block';
    }

    function closeHoanTatModal() {
        document.getElementById('modalHoanTat').style.display = 'none';
    }

    // Đóng modal khi click ra ngoài
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalHoanTat');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Thêm xác nhận trước khi submit (tùy chọn)
    document.getElementById('hoantatForm').addEventListener('submit', function(e) {
        // Optional: kiểm tra trường bắt buộc
        const ketluan = document.getElementById('modal_ketluan').value.trim();
        if (!ketluan) {
            alert('Vui lòng nhập Kết luận trước khi lưu.');
            e.preventDefault();
            return false;
        }

        // Nếu muốn hiển thị confirm:
        const ok = confirm('Xác nhận hoàn tất khám và cập nhật trạng thái sang "Đã khám"?');
        if (!ok) {
            e.preventDefault();
            return false;
        }
        // Form sẽ submit đến ?action=update_phieukhambenh (controller xử lý tiếp)
    });
</script>
</div>
