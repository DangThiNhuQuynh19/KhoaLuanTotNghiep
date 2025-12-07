<?php
date_default_timezone_set('Asia/Ho_Chi_Minh'); // đặt múi giờ Việt Nam

include_once("Controllers/cchuyengia.php");
include_once("Controllers/clichkham.php");

// Kiểm tra id chuyên gia
if (!isset($_GET['idcg'])) {
    echo "Không tìm thấy chuyên gia.";
    exit;
}
$machuyengia = $_GET['idcg'];
$ngay = isset($_GET['ngay']) ? $_GET['ngay'] : date('Y-m-d'); // Nếu chưa chọn ngày thì lấy ngày hôm nay
// Lấy thông tin chuyên gia
$cChuyenGia = new cChuyenGia();
$chuyengia = $cChuyenGia->getChuyenGiaById($machuyengia);

if (!$chuyengia || $chuyengia->num_rows === 0) {
    echo "Không tìm thấy thông tin chuyên gia.";
    exit;
}
$row = $chuyengia->fetch_assoc();
// Đặt múi giờ chính xác
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Lấy giờ hiện tại
$gioHienTai = date('H:i:s'); // Giờ hiện tại dưới định dạng H:i:s
$ngayHienTai = date('Y-m-d'); // Ngày hiện tại

$cLichKham = new cLichKham();
$lichkham = $cLichKham->getLichKhamOfChuyenGiaByNgay($ngay, $machuyengia, $gioHienTai);
?>

<style>
/* Layout */
html, body { height: 100%; }
body {
    /* padding 20px total around page, include footer spacing at bottom */
    padding-top: 50px;
    padding-bottom: 20px; /* ensure footer spacing requested */
    margin: 0;
    background: #f7f7fb;
    font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
    color: #222;
    box-sizing: border-box;
}

/* Page wrapper to keep footer at bottom */
.page-wrapper {
    min-height: calc(100vh - 40px); /* account for body vertical padding (top + bottom) */
    display: flex;
    flex-direction: column;
}

/* Content area grows to push footer down */
.content-wrap {
    flex: 1;
}

/* Container */
.container1 {
    max-width: 1000px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    padding: 28px;
    box-shadow: 0 8px 30px rgba(29,14,45,0.06);
    margin-top: 40px;
}

/* Header */
.doctor-header {
    display: flex;
    gap: 28px;
    align-items: flex-start;
    margin-bottom: 18px;
}
.doctor-header img {
    width: 220px;
    height: 220px;
    border-radius: 12px;
    border: 1px solid #e6e6e9;
    object-fit: cover;
    background: #fafafa;
}
.doctor-info { flex: 1; }
.doctor-info h2 {
    margin: 0 0 6px;
    color: #3c1561;
    font-size: 24px;
}
.doctor-info p { margin: 6px 0; color: #444; line-height: 1.6; }

/* Description panel */
.description-panel {
    margin-top: 12px;
    border-radius: 10px;
    background: linear-gradient(180deg,#ffffff,#fbfbfd);
    padding: 14px;
    border: 1px solid #f0f0f3;
    position: relative;
}

/* Short preview with subtle fade */
#short-description {
    max-height: 220px;
    overflow: hidden;
    line-height: 1.6;
    position: relative;
}

/* fade overlay to hint more content */
.description-panel .fade-bottom {
    pointer-events: none;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 56px; /* above controls */
    height: 72px;
    background: linear-gradient(180deg, rgba(255,255,255,0), #fbfbfd);
    transition: opacity .2s;
}

/* Full description when shown */
#full-description {
    display: none;
    max-height: 60vh;
    overflow-y: auto;
    line-height: 1.6;
    padding-right: 8px;
}

/* custom scrollbar for full description */
#full-description::-webkit-scrollbar { width: 10px; }
#full-description::-webkit-scrollbar-track { background: transparent; }
#full-description::-webkit-scrollbar-thumb { background: rgba(60,21,97,0.12); border-radius: 8px; }
#full-description::-webkit-scrollbar-thumb:hover { background: rgba(60,21,97,0.18); }

/* Controls: inline nice buttons */
.controls {
    margin-top: 14px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

/* "Xem thêm" as subtle outline button */
#toggle-mota-button {
    background: transparent;
    border: 1px solid rgba(111,47,163,0.12);
    color: #4b1f6b;
    padding: 8px 14px;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 600;
    transition: all .16s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
#toggle-mota-button:hover {
    background: rgba(111,47,163,0.04);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(111,47,163,0.06);
}

/* "Đặt lịch" primary action */
.btn-schedule {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    border-radius: 999px;
    color: #fff;
    font-weight: 700;
    text-decoration: none;
    background: linear-gradient(90deg,#6f2fa3,#47206a);
    box-shadow: 0 8px 18px rgba(111,47,163,0.14);
    transition: transform .14s ease, box-shadow .14s ease;
    border: none;
}
.btn-schedule:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 40px rgba(111,47,163,0.18);
}

/* When expanded, hide fade overlay and show full panel */
.description-panel.expanded .fade-bottom { opacity: 0; }
.description-panel.expanded #short-description { display: none; }
.description-panel.expanded #full-description { display: block; }

/* small screens */
@media (max-width: 768px) {
    .doctor-header { flex-direction: column; align-items: center; text-align: center; }
    .doctor-info { text-align: center; }
    .description-panel .fade-bottom { display: none; }
    #short-description { max-height: none; }
    .controls { justify-content: center; }
}

/* Footer: padding 20px as requested */
.site-footer {
    padding: 20px;
    text-align: center;
    color: #666;
    font-size: 14px;
    background: transparent;
    margin-top: 24px;
}
</style>

<div class="page-wrapper">
    <div class ="content-wrap">
        <div class="container1">
            <!-- Thông tin chuyên gia -->
            <div class="doctor-header">
                <img src="Assets/img/<?php echo htmlspecialchars($row['imgcg']); ?>" alt="Ảnh chuyên gia">
                <div class="doctor-info">
                    <h2><?php echo htmlspecialchars($row['capbac']) . ' ' . htmlspecialchars($row['hoten']); ?></h2>
                    <p><strong>Lĩnh vực:</strong> <?php echo htmlspecialchars($row['tenlinhvuc']); ?></p>
                    <p><strong>Thông tin mô tả:</strong></p>

                    <!-- Nội dung mô tả thu gọn -->
                    <div id="short-description">
                        <?php
                            $motangan = mb_substr(strip_tags($row['motacg']), 0, 800); // Lấy 200 ký tự đầu
                            echo nl2br(htmlspecialchars($motangan)) . '...';
                        ?>
                    </div>

                    <!-- Nội dung mô tả đầy đủ -->
                    <div id="full-description" style="display: none;">
                        <?php
                            echo nl2br(htmlspecialchars($row['motacg']));
                            echo '<br><br>';
                            echo nl2br(htmlspecialchars($row['gioithieucg']));
                        ?>
                    </div>

                    <div class="controls">
                        <button id="toggle-mota-button" aria-expanded="false" aria-controls="full-description">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5v14M5 12h14" stroke="#4b1f6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Xem thêm
                        </button>

                        <a href="?action=lichkhamchuyengia&id=<?php echo $_GET['idcg']; ?>" class="btn-schedule" aria-label="Đặt lịch bác sĩ">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="5" width="18" height="16" rx="2" stroke="rgba(255,255,255,0.9)" stroke-width="1.2"/>
                                <path d="M16 3v4M8 3v4" stroke="rgba(255,255,255,0.9)" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                            Đặt lịch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="site-footer">
    </footer>
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

