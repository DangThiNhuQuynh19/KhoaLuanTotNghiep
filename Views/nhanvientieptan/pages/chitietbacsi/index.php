<?php
include_once("Controllers/cbacsi.php");
include_once("Controllers/clichkham.php");

// Kiểm tra id bác sĩ
if (!isset($_GET['id'])) {
    echo "Không tìm thấy bác sĩ.";
    exit;
}
$mabacsi = $_GET['id'];
$ngay = isset($_GET['ngay']) ? $_GET['ngay'] : date('Y-m-d'); // Nếu chưa chọn ngày thì lấy ngày hôm nay
// Lấy thông tin bác sĩ
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
    margin-top: 10px;
}
.doctor-header {
    display: flex;
    gap: 30px;
    align-items: flex-start;
    margin-bottom: 20px;
}
.doctor-header img {
    width: 220px;
    border-radius: 12px;
    border: 1px solid #ccc;
    object-fit: cover;
}
.doctor-info {
    flex: 1;
}
.doctor-info h2 {
    margin-top: 0;
    color: #3c1561;
    font-size: 26px;
}
.doctor-info p {
    margin: 8px 0;
    color: #444;
    line-height: 1.6;
}
.date-picker {
    margin-top: 20px;
}
.date-picker input[type="date"], .date-picker input[type="time"] {
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
.shift-list {
    margin-top: 20px;
}
.shift-list h3 {
    margin-bottom: 10px;
    color: #3c1561;
}
.shift-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.shift-buttons a {
    display: inline-block;
    padding: 10px 20px;
    background: #3c1561;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s;
}
.shift-buttons a:hover {
    background: #6b409c;
}
.shift-group{
    margin-top: 20px;
}
.shift-group h4 {
    font-size: 18px;
    font-weight: normal; /* chữ đen bình thường */
    color: #000; /* màu đen */
    margin-bottom: 15px; /* cách xa khung giờ */
}
@media (max-width: 768px) {
    .doctor-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .doctor-info {
        text-align: center;
    }
}
#toggle-mota-button {
    background: none;
    border: none;
    color: #7b1fa2;
    font-size: 16px;
    font-weight: normal;
    cursor: pointer;
    text-decoration: underline;
    padding: 5px 10px;
    transition: color 0.3s ease;
}
#toggle-mota-button:hover {
    color: #4a148c;
    text-decoration: underline;
}
</style>

<div class="container">
    <!-- Thông tin bác sĩ -->
    <div class="doctor-header">
        <img src="Assets/img/<?php echo htmlspecialchars($row['imgbs']); ?>" alt="Ảnh bác sĩ">
        <div class="doctor-info">
            <h2><?php echo htmlspecialchars($row['capbac']) . ' ' . htmlspecialchars($row['hoten']); ?></h2>
            <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($row['tenchuyenkhoa']); ?></p>
            <p><strong>Thông tin mô tả:</strong></p>

            <!-- Nội dung mô tả thu gọn -->
            <div id="short-description">
                <?php
                    $motangan = mb_substr(strip_tags($row['motabs']), 0, 800); // Lấy 200 ký tự đầu
                    echo nl2br(htmlspecialchars($motangan)) . '...';
                ?>
            </div>

            <!-- Nội dung mô tả đầy đủ -->
            <div id="full-description" style="display: none;">
                <?php
                    echo nl2br(htmlspecialchars($row['motabs']));
                    echo '<br><br>';
                    echo nl2br(htmlspecialchars($row['gioithieubs']));
                ?>
            </div>

            <!-- Nút xem thêm -->
            <button id="toggle-mota-button">Xem thêm</button>
        </div>
        <a href="?action=bacsi" class="btn btn-primary me-2">Quay lại</a>
    </div>

    
</div>

</div>
</div>
<script>
    const toggleButton = document.getElementById('toggle-mota-button');
    const shortDesc = document.getElementById('short-description');
    const fullDesc = document.getElementById('full-description');

    toggleButton.addEventListener('click', function() {
        if (fullDesc.style.display === "none") {
            fullDesc.style.display = "block";
            shortDesc.style.display = "none";
            toggleButton.textContent = "Thu gọn";
        } else {
            fullDesc.style.display = "none";
            shortDesc.style.display = "block";
            toggleButton.textContent = "Xem thêm";
        }
    });
</script>
