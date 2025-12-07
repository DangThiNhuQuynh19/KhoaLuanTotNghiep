<?php
include_once("Controllers/cbacsi.php");
include_once("Controllers/clichkham.php");
if (!isset($_GET['id'])) {
    echo "Không tìm thấy bác sĩ.";
    exit;
}
$mabacsi = $_GET['id'];
$ngay = isset($_GET['ngay']) ? $_GET['ngay'] : date('Y-m-d');
$cBacSi = new cBacSi();
$bacsi = $cBacSi->getBacSiById($mabacsi);

if (!$bacsi || $bacsi->num_rows === 0) {
    echo "Không tìm thấy thông tin bác sĩ.";
    exit;
}
$row = $bacsi->fetch_assoc();
date_default_timezone_set('Asia/Ho_Chi_Minh');
$gioHienTai = date('H:i:s');
$ngayHienTai = date('Y-m-d');

$cLichKham = new cLichKham();
$lichkham = $cLichKham->getLichKhamOfBacSiByNgay($ngay, $mabacsi, $gioHienTai);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Chi tiết bác sĩ</title>
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
</head>
<body>

<div class="page-wrapper">
    <div class="content-wrap">
        <div class="container1">
            <div class="doctor-header">
                <img src="Assets/img/<?php echo htmlspecialchars($row['imgbs']); ?>" alt="Ảnh bác sĩ">
                <div class="doctor-info">
                    <h2><?php echo htmlspecialchars($row['capbac']) . ' ' . htmlspecialchars($row['hoten']); ?></h2>
                    <p><strong>Chuyên khoa:</strong> <?php echo htmlspecialchars($row['tenchuyenkhoa']); ?></p>

                    <div class="description-panel" id="descriptionPanel" aria-live="polite">
                        <div id="short-description" aria-hidden="false">
                            <?php
                                $motangan = mb_substr(strip_tags($row['motabs']), 0, 800);
                                echo nl2br(htmlspecialchars($motangan));
                                if (mb_strlen(strip_tags($row['motabs'])) > 800) echo '...';
                            ?>
                        </div>

                        <div id="full-description" aria-hidden="true" tabindex="0">
                            <?php
                                echo nl2br(htmlspecialchars($row['motabs']));
                                echo '<br><br>';
                                echo nl2br(htmlspecialchars($row['gioithieubs']));
                            ?>
                        </div>

                        <div class="fade-bottom" aria-hidden="true"></div>

                        <div class="controls">
                            <button id="toggle-mota-button" aria-expanded="false" aria-controls="full-description">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5v14M5 12h14" stroke="#4b1f6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Xem thêm
                            </button>

                            <a href="?action=lichkham&id=<?php echo $mabacsi; ?>" class="btn-schedule" aria-label="Đặt lịch bác sĩ">
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
            <!-- Shift list omitted for brevity in this view, keep previous logic if needed -->
        </div>
    </div>

    <footer class="site-footer">
    </footer>
</div>

<script>
(function(){
    const toggleButton = document.getElementById('toggle-mota-button');
    const panel = document.getElementById('descriptionPanel');

    function openFull() {
        panel.classList.add('expanded');
        toggleButton.setAttribute('aria-expanded', 'true');
        toggleButton.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M12 19V5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Thu gọn';
        const fullDesc = document.getElementById('full-description');
        fullDesc.focus();
        const rect = fullDesc.getBoundingClientRect();
        if (rect.bottom > window.innerHeight) fullDesc.scrollIntoView({behavior:'smooth', block:'start'});
    }

    function closeFull() {
        panel.classList.remove('expanded');
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="#4b1f6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Xem thêm';
        toggleButton.scrollIntoView({behavior:'smooth', block:'nearest'});
    }

    toggleButton.addEventListener('click', function(){
        if (panel.classList.contains('expanded')) closeFull(); else openFull();
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && panel.classList.contains('expanded')) closeFull();
    });
})();
</script>

</body>
</html>
