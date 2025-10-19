<?php
include_once("Controllers/cbacsi.php");
include_once("Assets/config.php");

// Kiểm tra ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Không tìm thấy bác sĩ.";
    exit;
}
$mabacsi = $_GET['id'];

// Lấy thông tin đầy đủ
$cBacSi = new cBacSi();
$bacsi = $cBacSi->getBacSiById($mabacsi);

if (!$bacsi || $bacsi->num_rows === 0) {
    echo "Không tìm thấy thông tin bác sĩ.";
    exit;
}

$row = $bacsi->fetch_assoc();
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
    gap: 30px;
    align-items: flex-start;
    margin-bottom: 30px;
}
.doctor-header img {
    width: 220px;
    border-radius: 12px;
    border: 1px solid #ccc;
    object-fit: cover;
}
.doctor-info h2 {
    margin-top: 0;
    color: #3c1561;
    font-size: 26px;
}
.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
.info-table th, .info-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
.info-table th {
    width: 25%;
    background: #f8f8f8;
}
.desc-section {
    margin-top: 20px;
}
.desc-section h3 {
    color: #3c1561;
    margin-bottom: 10px;
}
#toggle-mota-button {
    background: none;
    border: none;
    color: #7b1fa2;
    font-size: 16px;
    cursor: pointer;
    text-decoration: underline;
}
</style>

<div class="container">
    <a href="?action=nhanvien&tab=bacsi" class="btn btn-secondary mb-3">← Quay lại</a>

    <div class="doctor-header">
        <img src="Assets/img/<?php echo htmlspecialchars($row['imgbs']); ?>" alt="Ảnh bác sĩ">
        <div class="doctor-info">
            <h2><?php echo htmlspecialchars($row['capbac'].' '.$row['hoten']); ?></h2>
            <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($row['tenchuyenkhoa']); ?></p>
            <p><strong>Giá khám:</strong> <?php echo number_format($row['giakham']); ?> VNĐ</p>
            <p><strong>Ngày bắt đầu:</strong> <?php echo htmlspecialchars($row['ngaybatdau']); ?></p>
            <p><strong>Ngày kết thúc:</strong> <?php echo htmlspecialchars($row['ngayketthuc']); ?></p>
            <p><i class="bi bi-person-circle"></i> Trạng thái: <?= htmlspecialchars($row['tentrangthai']) ?></p>
        </div>
    </div>

    <table class="info-table">
        <tr><th>Họ tên</th><td><?php echo htmlspecialchars($row['hoten']); ?></td></tr>
        <tr><th>Ngày sinh</th><td><?php echo htmlspecialchars($row['ngaysinh']); ?></td></tr>
        <tr><th>Giới tính</th><td><?php echo htmlspecialchars($row['gioitinh']); ?></td></tr>
        <tr><th>CCCD</th><td><?php echo htmlspecialchars($row['cccd']); ?></td></tr>
        <tr><th>Dân tộc</th><td><?php echo htmlspecialchars($row['dantoc']); ?></td></tr>
        <tr><th>SĐT</th><td><?php echo htmlspecialchars(decryptData($row['sdt'])); ?></td></tr>
        <tr><th>Email TK</th><td><?php echo htmlspecialchars(decryptData($row['email'])); ?></td></tr>
        <tr><th>Email cá nhân</th><td><?php echo htmlspecialchars($row['emailcanhan']); ?></td></tr>
        <tr><th>Địa chỉ:</th> 
        <td><?= htmlspecialchars($row['sonha']) . ', ' 
            . htmlspecialchars($row['tenxaphuong']) . ', ' 
            . htmlspecialchars($row['tentinhthanhpho']) ?>
        </td></tr>
    </table>

    <div class="desc-section">
        <h3>Giới thiệu & Mô tả</h3>
        <div id="short-description">
            <?php
                $motangan = mb_substr(strip_tags($row['motabs']), 0, 500);
                echo nl2br(htmlspecialchars($motangan)).'...';
            ?>
        </div>
        <div id="full-description" style="display:none;">
            <?php
                echo nl2br(htmlspecialchars($row['motabs']))."<br><br>";
                echo nl2br(htmlspecialchars($row['gioithieubs']));
            ?>
        </div>
        <button id="toggle-mota-button">Xem thêm</button>
    </div>
</div>

<script>
const toggleButton = document.getElementById('toggle-mota-button');
const shortDesc = document.getElementById('short-description');
const fullDesc = document.getElementById('full-description');

toggleButton.addEventListener('click', () => {
    if (fullDesc.style.display === 'none') {
        fullDesc.style.display = 'block';
        shortDesc.style.display = 'none';
        toggleButton.textContent = 'Thu gọn';
    } else {
        fullDesc.style.display = 'none';
        shortDesc.style.display = 'block';
        toggleButton.textContent = 'Xem thêm';
    }
});
</script>
