<?php
include_once("Controllers/cbacsi.php");

if (!isset($_SESSION["user"]["tentk"])) {
    echo "Bạn chưa đăng nhập!";
    exit;
}

// Lấy dữ liệu lịch bác sĩ
$cbacsi = new cbacsi();
$lich = $cbacsi->getLichLamViecBacSi($_SESSION["user"]["tentk"]);

// Tuần và năm hiện tại hoặc lấy từ GET
$week = isset($_GET['week']) ? intval($_GET['week']) : date('W');
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Tạo mảng các ngày trong tuần từ Thứ 2 → Chủ nhật
$days = [];
$date = new DateTime();
$date->setISODate($year, $week, 1); // Thứ 2
for ($i = 0; $i < 7; $i++) {
    $days[] = $date->format('Y-m-d');
    $date->modify('+1 day');
}

$thuVN = ["Thứ 2","Thứ 3","Thứ 4","Thứ 5","Thứ 6","Thứ 7","Chủ nhật"];

// Gom dữ liệu lịch theo ngày
$weekData = [];
if ($lich && $lich !== -1) {
    foreach ($lich as $row) {
        $weekData[$row['ngaylam']][] = $row;
    }
}
?>

<div class="week-container">

    <div class="week-header">
        <button class="week-btn" onclick="changeWeek(-1)">‹ Tuần trước</button>
        <div class="week-label">
            Tuần <?= $week ?> - Năm <?= $year ?>
        </div>
        <button class="week-btn" onclick="changeWeek(1)">Tuần sau ›</button>
    </div>

    <div class="calendar-grid">
        <?php foreach ($days as $i => $day):
            $isToday = ($day == date("Y-m-d"));
        ?>
        <div class="calendar-day">
            <div class="day-header <?= $isToday ? 'today' : '' ?>">
                <?= $thuVN[$i] ?><br>
                <span class="date-number"><?= date("d/m/Y", strtotime($day)) ?></span>
            </div>

            <div class="day-body">
                <?php if (empty($weekData[$day])): ?>
                    <div class="empty">Không có lịch</div>
                <?php else: ?>
                    <?php foreach ($weekData[$day] as $item): ?>
                        <div class="event-box <?= strtolower($item["hinhthuclamviec"]) ?>">
                            <div><strong><?= $item["tenca"] ?></strong></div>
                            <div><?= $item["giobatdau"] ?> - <?= $item["gioketthuc"] ?></div>

                            <?php if ($item["hinhthuclamviec"] == "Offline"): ?>
                                <div class="event-room">
                                    Phòng <?= $item["sophong"] ?> – Tòa <?= $item["tentoa"] ?> - Tầng <?= $item["tang"] ?>
                                </div>
                            <?php endif; ?>

                            <div class="event-type"><?= $item["hinhthuclamviec"] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.week-container { padding: 20px; font-family: Inter, sans-serif; }
.week-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.week-btn { padding: 6px 12px; background-color: #007bff; border: none; color: white; border-radius: 4px; cursor: pointer; transition: 0.2s; }
.week-btn:hover { background-color: #0056b3; }
.week-label { font-weight: 600; font-size: 16px; }

.calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; }
.calendar-day { background: #fafafa; border: 1px solid #e0e0e0; border-radius: 8px; display: flex; flex-direction: column; }
.day-header { background: #007bff; color: white; padding: 6px; font-weight: 600; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px; }
.day-header.today { background: #0056b3; }
.day-body { padding: 6px; min-height: 80px; }

.event-box { background: white; border: 1px solid #ddd; padding: 6px; margin-bottom: 6px; border-radius: 6px; font-size: 13px; box-shadow: 0px 1px 3px rgba(0,0,0,0.05); }
.empty { color: #999; font-style: italic; text-align: center; }

.online { border-left: 4px solid #0a8f08; }
.offline { border-left: 4px solid #007bff; }

.event-room { font-size: 12px; color: #555; }
</style>

<script>
function changeWeek(step) {
    const params = new URLSearchParams(window.location.search);
    let week = parseInt(params.get("week") || <?= $week ?>);
    let year = parseInt(params.get("year") || <?= $year ?>);

    week += step;

    function getWeeksInYear(y) {
        const d = new Date(y, 11, 31);
        const week = Math.ceil((((d - new Date(y,0,1))/86400000 + 1)/7));
        return week;
    }

    let maxWeek = getWeeksInYear(year);

    if (week > maxWeek) { 
        week = 1; 
        year++;
    }
    if (week < 1) { 
        year--; 
        week = getWeeksInYear(year);
    }

    const action = params.get("action") || "xemlichlamviec";
    window.location.href = window.location.pathname + "?action=" + action + "&week=" + week + "&year=" + year;
}
</script>
