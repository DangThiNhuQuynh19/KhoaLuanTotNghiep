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
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi Tiết Lịch Xét Nghiệm</title>
<style>
body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f5f7fa; margin:0; padding:0; }
.container { max-width:1000px; margin:40px auto; padding:20px; background:#fff; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#6c3483; margin-bottom:30px;}
.info-section { margin-bottom:20px; }
.info-section h3 { margin-bottom:10px; color:#4b0082; border-bottom:1px solid #ddd; padding-bottom:5px;}
.info-section table { width:100%; border-collapse: collapse; }
.info-section table td { padding:8px; vertical-align: top; }

.status-button { padding:4px 12px; font-size:14px; border-radius:6px; font-weight:bold; border:none; cursor:default; }
.btn-pending { background:#ff9800; color:#fff; }
.btn-inprogress { background:#0d6efd; color:#fff; }
.btn-done { background:#6c757d; color:#fff; }

/* Bảng kết quả xét nghiệm */
.results-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.results-table th, .results-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
.results-table th {
    background-color: #f0f0f0;
    color: #333;
}
.results-table tr:nth-child(even) {
    background-color: #fafafa;
}
.back-btn {
    display:inline-block;
    margin-top:20px;
    padding:6px 12px;
    background:#6c3483;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
}
</style>
</head>
<body>
<div class="container">
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
