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
if ($lich && $lich !== -1 && is_array($lich)) {
    foreach ($lich as $row) {
        $weekData[$row['ngaylam']][] = $row;
    }
}

// Label cho khoảng ngày trong tuần (ví dụ 01/12/2025 - 07/12/2025)
$labelStart = date("d/m/Y", strtotime($days[0]));
$labelEnd = date("d/m/Y", strtotime($days[6]));
?>

<div class="week-container">

    <div class="week-header">
        <div class="nav-left">
            <button class="week-btn" onclick="changeWeek(-1)" aria-label="Tuần trước">‹</button>
        </div>

        <div class="week-main">
            <div class="week-title">Tuần <?= htmlspecialchars($week) ?> — Năm <?= htmlspecialchars($year) ?></div>
            <div class="week-range"><?= htmlspecialchars($labelStart) ?> — <?= htmlspecialchars($labelEnd) ?></div>
        </div>

        <div class="nav-right">
            <button class="week-btn" onclick="changeWeek(1)" aria-label="Tuần sau">›</button>
        </div>
    </div>

    <div class="legend">
        <div class="legend-item"><span class="legend-chip online"></span> Online</div>
        <div class="legend-item"><span class="legend-chip offline"></span> Offline</div>
    </div>

    <div class="calendar-grid" role="list">
        <?php foreach ($days as $i => $day):
            $isToday = ($day == date("Y-m-d"));
            $dayLabel = date("d/m", strtotime($day));
        ?>
        <div class="calendar-day" role="listitem" aria-label="<?= $thuVN[$i] . ' ' . $dayLabel ?>">
            <div class="day-header <?= $isToday ? 'today' : '' ?>">
                <div class="day-name"><?= $thuVN[$i] ?></div>
                <div class="date-number"><?= $dayLabel ?></div>
                <div class="full-date"><?= date("Y-m-d", strtotime($day)) ?></div>
            </div>

            <div class="day-body">
                <?php if (empty($weekData[$day]) || !is_array($weekData[$day])): ?>
                    <div class="empty">Không có lịch</div>
                <?php else: ?>
                    <?php 
                    // Sắp xếp sự kiện theo giờ bắt đầu nhỏ → lớn để hiển thị hợp lý
                    usort($weekData[$day], function($a, $b) {
                        $ta = strtotime($a['giobatdau']);
                        $tb = strtotime($b['giobatdau']);
                        return $ta <=> $tb;
                    });
                    foreach ($weekData[$day] as $item): 
                        // đảm bảo giá trị tồn tại
                        $type = isset($item["hinhthuclamviec"]) ? strtolower($item["hinhthuclamviec"]) : 'offline';
                        $tenca = htmlspecialchars($item["tenca"] ?? '');
                        $start = htmlspecialchars($item["giobatdau"] ?? '');
                        $end = htmlspecialchars($item["gioketthuc"] ?? '');
                        $sophong = htmlspecialchars($item["sophong"] ?? '');
                        $tentoa = htmlspecialchars($item["tentoa"] ?? '');
                        $tang = htmlspecialchars($item["tang"] ?? '');
                    ?>
                        <div class="event-box <?= $type ?>">
                            <div class="event-top">
                                <div class="event-title"><?= $tenca ?></div>
                                <div class="event-time"><?= $start ?> — <?= $end ?></div>
                            </div>

                            <?php if ($type === "offline"): ?>
                                <div class="event-room">
                                    <span class="room-label">Phòng</span> <?= $sophong ?> · <?= $tentoa ?> · Tầng <?= $tang ?>
                                </div>
                            <?php endif; ?>

                            <div class="event-type"><?= ucfirst($type) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
:root{
    --bg:#f6f8fb;
    --card:#ffffff;
    --muted:#6b7280;
    --primary:#0b5ed7;
    --accent:#0a8f08;
    --radius:10px;
    --shadow: 0 6px 18px rgba(11,37,71,0.06);
    --gap:12px;
    font-family: Inter, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.week-container { padding: 18px; background: var(--bg); border-radius: 12px; max-width: 1200px; margin: 12px auto; box-shadow: var(--shadow); }

/* Header */
.week-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px; }
.week-btn { background:var(--card); border:1px solid #e6eefc; color:var(--primary); padding:8px 12px; font-size:18px; border-radius:8px; cursor:pointer; transition:transform .12s; box-shadow: 0 1px 2px rgba(0,0,0,0.04);}
.week-btn:hover { transform:translateY(-2px); background:#fff; }
.week-main { text-align:center; flex:1; }
.week-title{ font-weight:700; font-size:16px; color:#0f1724; }
.week-range{ font-size:13px; color:var(--muted); }

/* Legend */
.legend { display:flex; gap:12px; align-items:center; margin-bottom:12px; justify-content:flex-end; }
.legend-item{ display:flex; gap:8px; align-items:center; color:var(--muted); font-size:13px; }
.legend-chip{ width:14px; height:14px; border-radius:4px; display:inline-block; border:1px solid rgba(0,0,0,0.06); }
.legend-chip.online{ background: linear-gradient(90deg,#0a8f08,#2ec15e);}
.legend-chip.offline{ background: linear-gradient(90deg,#007bff,#4093ff);}

/* Grid */
.calendar-grid { display:grid; grid-template-columns: repeat(7, 1fr); gap:var(--gap); }

/* Each day card */
.calendar-day { background:var(--card); border-radius:var(--radius); overflow:hidden; border:1px solid #eef2f6; display:flex; flex-direction:column; min-height:130px;}
.day-header { padding:10px; background:linear-gradient(180deg,var(--primary),#0a6fd6); color:#fff; text-align:center; }
.day-header.today { background:linear-gradient(180deg,#053e8a,#0a6fd6); box-shadow: inset 0 -2px 0 rgba(255,255,255,0.06); }
.day-name { font-weight:600; font-size:13px; }
.date-number { font-weight:700; font-size:14px; margin-top:4px; }
.day-body { padding:10px; flex:1; min-height:90px; }

/* Event box */
.event-box { display:flex; flex-direction:column; gap:6px; padding:10px; border-radius:8px; border:1px solid #eef3fb; background:linear-gradient(180deg,#fff,#fbfdff); margin-bottom:8px; box-shadow: 0 2px 6px rgba(9,30,66,0.04); font-size:13px; }
.event-top { display:flex; justify-content:space-between; align-items:center; gap:8px; }
.event-title { font-weight:600; color:#0f1724; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: calc(100% - 80px); }
.event-time { background:#f1f5f9; color: #0f1724; padding:4px 8px; border-radius:6px; font-size:12px; min-width:74px; text-align:right; }
.event-room { font-size:12px; color:var(--muted); }
.event-type { font-size:12px; color:var(--muted); align-self:flex-end; }

/* Borders for types */
.event-box.online { border-left:4px solid #0a8f08; }
.event-box.offline { border-left:4px solid var(--primary); }

/* Empty hint */
.empty { color:var(--muted); font-style:italic; text-align:center; padding:8px; }

/* Responsive */
@media (max-width: 980px) {
    .calendar-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 680px) {
    .calendar-grid { grid-template-columns: repeat(1, 1fr); }
    .week-header { flex-direction:row; align-items:center; gap:8px; }
    .week-main { text-align:left; }
    .legend { justify-content:flex-start; }
    .event-top { flex-direction:column; align-items:flex-start; gap:4px; }
    .event-time { text-align:left; }
}
</style>

<script>
function changeWeek(step) {
    const params = new URLSearchParams(window.location.search);
    let week = parseInt(params.get("week") || <?= json_encode($week) ?>, 10);
    let year = parseInt(params.get("year") || <?= json_encode($year) ?>, 10);

    week += step;

    function getISOWeeksInYear(y) {
        // ISO weeks: week that contains Jan 4 determines week count
        const d = new Date(y, 11, 31);
        // Thursday is used to determine the year of the week - compute ISO week number for Dec 31
        const day = d.getDay();
        // get ISO week number algorithm for last day
        d.setDate(d.getDate() - ((day + 6) % 7) + 3);
        const firstThursday = new Date(d.getFullYear(), 0, 4);
        const diff = d - firstThursday;
        return 1 + Math.round(diff / 86400000 / 7);
    }

    let maxWeek = getISOWeeksInYear(year);

    if (week > maxWeek) {
        week = 1;
        year++;
    }
    if (week < 1) {
        year--;
        week = getISOWeeksInYear(year);
    }
    const action = params.get("action") || "xemlichlamviec";
    window.location.href = window.location.pathname + "?action=" + action + "&week=" + week + "&year=" + year;
}
</script>