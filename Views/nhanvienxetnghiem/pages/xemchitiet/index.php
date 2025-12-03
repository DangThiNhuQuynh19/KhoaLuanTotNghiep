<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

include_once("Assets/config.php");
include_once('Controllers/clichxetnghiem.php');

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

// Bản ghi đầu tiên chứa thông tin chung
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
<style>
/* ===== TITLE ===== */
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #006699;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

/* ===== SECTION ===== */
.info-section {
    background: #f9fbfd;
    border: 1px solid #d9e4ee;
    padding: 18px 20px;
    border-radius: 6px;
    margin-bottom: 22px;
}

.info-section h3 {
    font-size: 17px;
    font-weight: 600;
    color: #004d66;
    margin-bottom: 12px;
}

.info-section table {
    width: 100%;
}

.info-section table td {
    padding: 6px 0;
    font-size: 15px;
    color: #333;
}

.info-section table strong {
    color: #004d66;
}

/* ===== TABLE KẾT QUẢ ===== */
.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

.results-table th {
    background: #e6f1f7;
    padding: 10px;
    font-weight: 600;
    text-align: left;
    color: #004d66;
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

/* ===== STATUS BUTTON ===== */
.status-button {
    padding: 5px 14px;
    border-radius: 20px;
    border: none;
    color: #fff;
    font-size: 14px;
    font-weight: 500;
}

/* chuẩn màu hệ thống y tế */
.status-waiting { background: #f0ad4e; }
.status-processing { background: #0275d8; }
.status-done { background: #5cb85c; }
.status-cancel { background: #d9534f; }

/* ===== BACK BUTTON ===== */
.back-btn {
    display: inline-block;
    margin-top: 18px;
    padding: 10px 16px;
    background: #0275d8;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
    transition: 0.2s ease-in-out;
}

.back-btn:hover {
    background: #025fa8;
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
</style>
<div class="main-container">
<h2>Chi Tiết Lịch Xét Nghiệm</h2>

<!-- Thông Tin Bác Sĩ -->
<div class="info-section">
    <h3>Thông Tin Bác Sĩ</h3>
    <table>
        <tr><td><strong>Tên Bác Sĩ:</strong></td><td><?= htmlspecialchars($lich['ten_bacsi']) ?></td></tr>
        <tr><td><strong>Khoa:</strong></td><td><?= htmlspecialchars($lich['tenchuyenkhoa']) ?></td></tr>
        <tr><td><strong>Số Điện Thoại:</strong></td><td><?= htmlspecialchars($lich['sdt_bacsi']) ?></td></tr>
        <tr><td><strong>Chức Vụ:</strong></td><td><?= htmlspecialchars($lich['chucvu_bacsi']) ?></td></tr>
    </table>
</div>

<!-- Thông Tin Bệnh Nhân -->
<div class="info-section">
    <h3>Thông Tin Bệnh Nhân</h3>
    <table>
        <tr><td><strong>Tên Bệnh Nhân:</strong></td><td><?= htmlspecialchars($lich['ten_benhnhan']) ?></td></tr>
        <tr><td><strong>SĐT:</strong></td><td><?= htmlspecialchars(decryptData($lich['sdt_benhnhan'] ?? '')) ?></td></tr>
        <tr><td><strong>Mã Bệnh Nhân:</strong></td><td><?= htmlspecialchars($lich['mabenhnhan']) ?></td></tr>
    </table>
</div>
<!-- Thông Tin Khám Bệnh -->
<div class="info-section">
    <h3>Thông Tin khám bệnh</h3>
    <table>
        <tr><td><strong>Triệu chứng ban đầu:</strong></td><td><?= htmlspecialchars($lich['trieuchungbandau']) ?></td></tr>
        <tr><td><strong>Chẩn đoán ban đầu của bác sĩ:</strong></td><td><?= htmlspecialchars($lich['chandoan']) ?></td></tr>
    </table>
</div>
<!-- Thông Tin Xét Nghiệm -->
<div class="info-section">
    <h3>Thông Tin Xét Nghiệm</h3>
    <table>
        <tr><td><strong>Loại Xét Nghiệm:</strong></td><td><?= htmlspecialchars($lich['tenloaixetnghiem']) ?></td></tr>
        <tr><td><strong>Ngày Hẹn:</strong></td><td><?= htmlspecialchars($lich['ngayhen']) ?></td></tr>
        <tr><td><strong>Giờ Thực Hiện:</strong></td><td><?= htmlspecialchars($lich['giobatdau']) ?> - <?= htmlspecialchars($lich['gioketthuc']) ?></td></tr>
        <tr><td><strong>Trạng Thái:</strong></td><td><button class="status-button <?= $statusClass ?>"><?= htmlspecialchars($statusText) ?></button></td></tr>
    </table>
</div>

<!-- Bảng Kết Quả Xét Nghiệm -->
<div class="info-section">
    <h3>Kết Quả Xét Nghiệm</h3>
    <?php if (!empty($lichChiTiet[0]['tenchisoxetnghiem'])): ?>
    <table class="results-table">
        <thead>
            <tr>
                <th>Tên Chỉ Số</th>
                <th>Giá Trị Kết Quả</th>
                <th>Đơn Vị</th>
                <th>Khoảng Tham Chiếu</th>
                <th>Giờ Lấy Mẫu</th>
                <th>Ngày Giờ Trả KQ</th>
                <th>Nhận Xét</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lichChiTiet as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tenchisoxetnghiem']) ?></td>
                    <td><?= htmlspecialchars($row['giatriketqua']) ?></td>
                    <td><?= htmlspecialchars($row['donviketqua']) ?></td>
                    <td><?= htmlspecialchars($row['khoangthamchieu']) ?></td>
                    <td><?= htmlspecialchars($row['giolaymau']) ?></td>
                    <td><?= htmlspecialchars($row['ngaygiotraketqua']) ?></td>
                    <td><?= htmlspecialchars($row['nhanxet']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="color:#888;">Chưa có kết quả xét nghiệm.</p>
    <?php endif; ?>
</div>

<!-- Nút quay lại -->
<a href="javascript:history.back()" class="back-btn">&#8592; Quay lại</a>

</div>
</body>
</html>
