<?php
include_once("Controllers/cbacsi.php");
include_once("Controllers/clichkham.php");
if (!isset($_SESSION['dangnhap']) || $_SESSION['dangnhap'] != 1) {
    // Lưu URL hiện tại để quay lại sau khi đăng nhập
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    // Chuyển hướng sang trang đăng nhập
    header("Location: index.php?action=dangnhap");
    exit;
}

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
// Đặt múi giờ chính xác
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Lấy giờ hiện tại
$gioHienTai = date('H:i:s'); // Giờ hiện tại dưới định dạng H:i:s
$ngayHienTai = date('Y-m-d'); // Ngày hiện tại

$cLichKham = new cLichKham();
$lichkham = $cLichKham->getLichKhamOfBacSiByNgay($ngay, $mabacsi, $gioHienTai);

?>

<style>
/* Page padding and footer styling requested */
html, body {
    height: 100%;
}
body {
    /* thêm padding 20px quanh trang */
    padding: 20px;
    padding-top:50px;
    margin: 0;
    background: #f7f7fb;
    font-family: Arial, Helvetica, sans-serif;
    color: #222;
    box-sizing: border-box;
}

/* Giữ container căn giữa nhưng không quá cao do padding thêm */
.container1 {
    max-width: 1000px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-top: 60px; /* giảm 1 chút vì body đã có padding */
}

/* Thông tin bác sĩ */
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

/* Date picker */
.date-picker {
    margin-top: 20px;
}
.date-picker input[type="date"], .date-picker input[type="time"] {
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

/* Shift list */
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
    transition: background 0.3s, transform 0.12s;
}
.site-footer {
    padding: 20px;
    text-align: center;
    color: #666;
    font-size: 14px;
    background: transparent;
    margin-top: 24px;
}
.shift-buttons a:hover {
    background: #6b409c;
    transform: translateY(-2px);
}
.shift-group{
    margin-top: 20px;
}
.shift-group h4 {
    font-size: 18px;
    font-weight: normal;
    color: #000;
    margin-bottom: 15px;
}

/* Toggle description button */
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
}

/* Popup đăng nhập */
#login-popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
#login-popup .popup-content {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    max-width: 320px;
}
#login-popup button {
    margin-top: 10px;
    padding: 8px 16px;
    background: #3c1561;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

/* Responsive */
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

/* Footer: luôn có khoảng padding 20px và chiều cao tối thiểu */
.site-footer {
    padding: 20px;
    height: auto;
    min-height: 20px;
    text-align: center;
    color: #666;
    font-size: 14px;
    margin-top: 24px;
}

/* Ensure the layout allows footer to be visible even with short content */
.page-wrapper {
    min-height: calc(100vh - 40px); /* account for body padding top+bottom = 40px */
    display: flex;
    flex-direction: column;
}
.content-wrap {
    flex: 1;
}
</style>

<div class="page-wrapper">
    <div class="content-wrap">
        <div class="container1">
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
                            $motangan = mb_substr(strip_tags($row['motabs']), 0, 800); // Lấy 800 ký tự đầu
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
            </div>

            <!-- Form chọn ngày và giờ -->
            <form method="get" id="form-ngay" class="date-picker">
                <input type="hidden" name="action" value="lichkham">
                <input type="hidden" name="id" value="<?php echo $mabacsi; ?>">
                <input type="hidden" name="giohientai" id="giohientai" value="">

                <label for="ngay">Chọn ngày khám:</label>
                <input type="date" name="ngay" id="ngay" value="<?php echo $ngay; ?>" min="<?php echo date('Y-m-d'); ?>" onchange="updateTimeAndSubmit();">
            </form>

            <script>
                function updateTimeAndSubmit() {
                    var now = new Date();
                    var gio = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0') + ':00';
                    document.getElementById('giohientai').value = gio;
                    document.getElementById('form-ngay').submit();
                }
            </script>

            <div class="shift-list">
                <h3>Danh sách ca làm việc:</h3>
                <?php
                if ($lichkham === false || $lichkham->num_rows === 0) {
                    echo "<p>Không có ca làm trong ngày này.</p>";
                } else {
                    $caOnline = [];
                    $caOffline = [];

                    while ($rowCa = $lichkham->fetch_assoc()) {
                        $makhunggiokb = $rowCa['makhunggiokb'];
                        $giobatdau = date('H:i', strtotime($rowCa['giobatdau']));
                        $gioketthuc = date('H:i', strtotime($rowCa['gioketthuc']));
                        $hinhthuc = $rowCa['hinhthuclamviec']; // "online" hoặc "offline"

                        $link = "";
                        if ($ngay == $ngayHienTai) {
                            if ($giobatdau >= $gioHienTai) {
                                $link = '<a href="index.php?action=datlichkham&idbs=' . $mabacsi . '&ngay=' . $ngay . '&makhunggiokb=' . $makhunggiokb . '">' . $giobatdau . ' - ' . $gioketthuc . '</a>';
                            } else {
                                $link = "<p>Ca này đã qua.</p>";
                            }
                        } else {
                            $link = '<a href="index.php?action=datlichkham&idbs=' . $mabacsi . '&ngay=' . $ngay . '&makhunggiokb=' . $makhunggiokb . '">' . $giobatdau . ' - ' . $gioketthuc . '</a>';
                        }

                        // Phân loại
                        if (strtolower($hinhthuc) == "online") {
                            $caOnline[] = $link;
                        } else {
                            $caOffline[] = $link;
                        }
                    }

                    // Hiển thị Online
                    echo "<div class='shift-group'>";
                    echo "<h4>Khám Online</h4>";
                    echo '<div class="shift-buttons">';
                    if (empty($caOnline)) {
                        echo "<p>Không có ca online.</p>";
                    } else {
                        foreach ($caOnline as $ca) {
                            echo $ca;
                        }
                    }
                    echo "</div></div>";

                    // Hiển thị Offline
                    echo "<div class='shift-group'>";
                    echo "<h4>Khám tại Bệnh viện</h4>";
                    echo '<div class="shift-buttons">';
                    if (empty($caOffline)) {
                        echo "<p>Không có ca offline.</p>";
                    } else {
                        foreach ($caOffline as $ca) {
                            echo $ca;
                        }
                    }
                    echo "</div></div>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
    </footer>
</div>

<!-- popup đăng nhập -->
<div id="login-popup">
    <div class="popup-content">
        <p>Vui lòng đăng nhập để đặt lịch khám.</p>
        <button onclick="redirectToLogin()">Đăng nhập</button>
        <br>
        <button onclick="closePopup()">Đóng</button>
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

    function showLoginPopup(redirectUrl) {
        sessionStorage.setItem('redirectAfterLogin', redirectUrl);
        document.getElementById('login-popup').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('login-popup').style.display = 'none';
    }

    function redirectToLogin() {
        // chuyển đến trang đăng nhập và sau khi đăng nhập sẽ quay lại redirectUrl
        window.location.href = "index.php?action=dangnhap";
    }

    // khi người dùng quay lại sau khi đăng nhập thành công
    <?php if (isset($_SESSION['dangnhap'])): ?>
    let redirectUrl = sessionStorage.getItem('redirectAfterLogin');
    if (redirectUrl) {
        sessionStorage.removeItem('redirectAfterLogin');
        window.location.href = redirectUrl;
    }
    <?php endif; ?>
</script>
