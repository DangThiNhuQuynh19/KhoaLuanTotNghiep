<?php
include_once('Controllers/cphieukhambenh.php');
include_once('Controllers/cbacsi.php');
include_once('Controllers/cchuyenkhoa.php');

$cchuyenkhoa = new cChuyenKhoa();
$chuyenkhoa_list = $cchuyenkhoa->getAllChuyenKhoa();

$cbacsi = new cBacSi();
$cphieukhambenh = new cPhieuKhamBenh();

// Lấy thông tin bác sĩ hiện tại
$bacsi = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);

// Danh sách mặc định
$lichkham_list = $cphieukhambenh->get_lichkhamoff_mabacsi($bacsi['mabacsi']);

// Checkbox Hôm nay
if(isset($_POST['homnay'])){
    $lichkham_list = $cphieukhambenh->get_lichkhamoff_homnay($bacsi['mabacsi']);
}

// Tìm kiếm
if(isset($_POST["btntimkiem"])){
    $tukhoa = $_POST["tukhoa"];
    $trangthai = $_POST["trangthai"];
    $ngay = $_POST["ngay"];
    $lichkham_list = $cphieukhambenh->search_phieukhamoff($tukhoa, $trangthai, $ngay, $bacsi['mabacsi']);
}

// Bỏ tìm kiếm
if(isset($_POST["btnbo"])){
    $lichkham_list = $cphieukhambenh->get_lichkhamoff_mabacsi($bacsi['mabacsi']);
    $_POST = []; // reset form
}
?>
<style>
/* --- General & Layout --- */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}
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

.btn-secondary i { font-size: 14px; }

.status-pending { color: orange; font-weight: bold; }
.status-completed { color: green; font-weight: bold; }
.status-canceled { color: red; font-weight: bold; }
</style>

<div class="container">
    <div class="content-header">
        <h1>Quản lý lịch hẹn trực tiếp</h1>
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
                                    value="<?php echo isset($_POST['tukhoa']) ? htmlspecialchars($_POST['tukhoa']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <select name="trangthai">
                                    <option value="">Trạng thái</option>
                                    <option value="chưa khám" <?php if(isset($_POST['trangthai']) && $_POST['trangthai']=='chưa khám') echo 'selected'; ?>>Chưa khám</option>
                                    <option value="đã khám" <?php if(isset($_POST['trangthai']) && $_POST['trangthai']=='đã khám') echo 'selected'; ?>>Đã khám</option>
                                    <option value="đã hủy" <?php if(isset($_POST['trangthai']) && $_POST['trangthai']=='đã hủy') echo 'selected'; ?>>Đã hủy</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <input type="date" name="ngay"
                                    value="<?php echo isset($_POST['ngay']) ? $_POST['ngay'] : ''; ?>">
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
                <input type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php if (isset($_POST['homnay'])) echo 'checked'; ?>>
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
                            <?php
                            if($lichkham_list){
                                foreach ($lichkham_list as $i) {
                                    switch ($i['tentrangthai']) {
                                        case 'Chưa khám': $statusClass = 'status-pending'; break;
                                        case 'Đã khám': $statusClass = 'status-completed'; break;
                                        case 'Đã hủy': $statusClass = 'status-canceled'; break;
                                        default: $statusClass = '';
                                    }

                                    echo '<tr>';
                                    echo '<td>' . $i['maphieukhambenh'] . '</td>';
                                    echo '<td>' . date('d/m/Y', strtotime($i['ngaykham'])) . '</td>';
                                    echo '<td>' . $i['giobatdau'].'-'.$i['gioketthuc'] . '</td>';
                                    echo '<td>' . $i['hoten'] . '</td>';
                                    echo '<td>' . number_format($i['giakham'], 0, ',', '.') . ' VND</td>';
                                    echo '<td><span class="status-badge ' . $statusClass . '">' . $i['tentrangthai'] . '</span></td>';
                                    echo '<td>';
                                    if($i['tentrangthai']=='Chưa khám'){
                                        echo '<a class="btn-primary btn-small" href="?action=chitietbenhnhan&id=' . $i['mabenhnhan'] . '">
                                        <i class="fas fa-comment-medical"></i> Khám bệnh</a>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7" style="text-align:center; color:gray;">Không có lịch hẹn</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
