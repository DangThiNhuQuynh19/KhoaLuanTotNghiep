<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");

// Lấy ngày hiện tại hoặc ngày filter
$ngay = isset($_GET['ngay']) && $_GET['ngay'] != "" ? $_GET['ngay'] : date("Y-m-d");

include_once("Controllers/clichlamviec.php");

$controller = new cLichLamViec();    
$dataLich = $controller->getlichlamviec($ngay);   
?>

<style>
:root {
    --main: #3498db;
    --main-light: #3498db;
}

.title-page {
    font-size: 26px;
    font-weight: 700;
    color: var(--main);
    margin-bottom: 24px;
}

/* AVATAR */
.avatar1 {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--main-light);
    box-shadow: 0px 4px 12px rgba(75, 63, 168, 0.3);
}

/* CARD */
.card {
    border-radius: 18px;
    border: none;
    transition: all 0.35s ease;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 24px;
    margin-bottom: 16px;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow:
        0 12px 28px rgba(0,0,0,0.08),
        0 0 18px rgba(104, 92, 212, 0.28),
        0 0 28px rgba(104, 92, 212, 0.18);
}

/* TAG */
.tag-online {
    display: inline-block;
    background: #27c4a8;
    padding: 4px 10px;
    border-radius: 6px;
    color: white;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 12px;
}

.tag-offline {
    display: inline-block;
    background: #ff7b54;
    padding: 4px 10px;
    border-radius: 6px;
    color: white;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 12px;
}

/* CA LÀM VIỆC */
.ca-item {
    background: #f5f3ff;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0px 4px 10px rgba(75,63,168,0.08);
    transition: 0.25s ease;
}

.ca-item:hover {
    background: #ece8ff;
    transform: translateY(-3px);
    box-shadow: 0px 6px 16px rgba(75,63,168,0.12);
}

.ca-name {
    font-weight: 700;
    color: var(--main);
    font-size: 15px;
    margin-bottom: 4px;
}

.ca-time {
    font-size: 13px;
    color: #6a6a6a;
}

/* Khung giờ gọn gàng */
.ca-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f7ff;
    padding: 10px 14px;
    border-radius: 10px;
    box-shadow: 0px 2px 8px rgba(75,63,168,0.06);
    transition: 0.25s ease;
    margin-bottom: 8px;
}

.ca-line:hover {
    background: #eeeaff;
    transform: translateY(-2px);
    box-shadow: 0px 4px 12px rgba(75,63,168,0.1);
}

.ca-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--main);
}

.ca-clock {
    font-size: 13px;
    color: #777;
}

/* FORM STYLES */
.filter-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

@media (max-width: 768px) {
    .filter-container {
        grid-template-columns: 1fr;
    }
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 14px;
    color: #333;
}

.form-control {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--main);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.filter-button {
    align-self: flex-end;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.btn-primary {
    background: var(--main);
    color: white;
    width: 100%;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

/* ALERT */
.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-danger {
    background: #fadbd8;
    color: #c0392b;
    border: 1px solid #f5b7b1;
}

.alert-warning {
    background: #fef5e7;
    color: #9a7d0a;
    border: 1px solid #f9e79f;
}

/* GRID */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .grid-container {
        grid-template-columns: 1fr;
    }
}

/* FLEX */
.flex-center {
    display: flex;
    align-items: center;
    gap: 12px;
}

.flex-space-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hr {
    border: none;
    border-top: 1px solid #eee;
    margin: 16px 0;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-info {
    background: #d6eaf8;
    color: #0c5aa0;
}

.text-primary {
    color: var(--main);
}

.text-muted {
    color: #999;
}

.mb-2 {
    margin-bottom: 8px;
}

.mb-3 {
    margin-bottom: 12px;
}

.mt-2 {
    margin-top: 8px;
}

.fw-bold {
    font-weight: 700;
}

.me-1 {
    margin-right: 4px;
}

</style>
</head>

<body>
<!-- MAIN CONTENT -->
<div class="main-container">

    <h1 class="title-page">Lịch làm việc của nhân sự</h1>

    <!-- FILTER -->
    <form method="GET" class="filter-container">
        <input type="hidden" name="action" value="xeplich">
        <div class="form-group">
            <label class="form-label">Chọn ngày</label>
            <input type="date" name="ngay" value="<?= $ngay ?>" class="form-control">
        </div>
        <div class="form-group filter-button">
            <button type="submit" class="btn btn-primary">
                <span>🔍</span> Lọc
            </button>
        </div>
    </form>

    <!-- RESULT -->
    <?php if ($dataLich === -1): ?>
        <div class="alert alert-danger">⚠ Lỗi truy vấn dữ liệu!</div>

    <?php elseif ($dataLich === 0): ?>
        <div class="alert alert-warning">⛔ Không có lịch trong ngày này.</div>

    <?php else: ?>

    <?php
    // Gom dữ liệu theo bác sĩ / chuyên gia
    $grouped = [];

    while ($row = $dataLich->fetch_assoc()) {

        $id = $row['manguoidung'];

        // Chọn avatar & vai trò
        if (!empty($row['mabacsi'])) {
            $avatar = $row['avatar_bacsi'];
            $vaitro = "Bác sĩ";
        } 
        elseif (!empty($row['machuyengia'])) {
            $avatar = $row['avatar_chuyengia'];
            $vaitro = "Chuyên gia";
        } 
        else {
            continue;
        }

        if (!isset($grouped[$id])) {
            $grouped[$id] = [
                'ten' => $row['hoten'],
                'avatar' => $avatar,
                'vaitro' => $vaitro,
                'hinhthuclamviec' => strtolower($row['hinhthuclamviec']),
                'phong' => strtolower($row['hinhthuclamviec']) == "online"
                            ? null
                            : ($row['tentoa'] . " • Tầng " . $row['tang'] . " • Phòng " . $row['sophong']),
                'lich' => []
            ];
        }

        // Thêm ca
        $grouped[$id]['lich'][] = [
            'tenca' => $row['tenca'],
            'time'  => $row['giobatdau'] . " - " . $row['gioketthuc']
        ];
    }
    ?>

    <div class="grid-container">
    <?php foreach ($grouped as $nguoidung): ?>
        <div class="card">

            <!-- Avatar + tên + vai trò -->
            <div class="flex-center mb-3">
                <img src="Assets/img/<?= $nguoidung['avatar'] ?>" class="avatar1">
                <div>
                    <h4 class="text-primary fw-bold mb-2"><?= $nguoidung['ten'] ?></h4>
                    <span class="badge badge-info"><?= $nguoidung['vaitro'] ?></span>
                </div>
            </div>

            <!-- Hình thức -->
            <?php if ($nguoidung['hinhthuclamviec'] == "online"): ?>
                <span class="tag-online">Khám trực tuyến (Online)</span>
            <?php else: ?>
                <span class="tag-offline">Làm tại bệnh viện (Offline)</span>
            <?php endif; ?>

            <!-- Phòng -->
            <?php if (!empty($nguoidung['phong'])): ?>
            <p class="text-muted mt-2">
                <span class="me-1">🏢</span>
                <?= $nguoidung['phong'] ?>
            </p>
            <?php endif; ?>

            <div class="hr"></div>

            <!-- Danh sách ca -->
            <h6 class="fw-bold mb-2">Ca làm việc:</h6>

            <?php foreach ($nguoidung['lich'] as $ca): ?>
              <div class="ca-line">
                  <div class="ca-title"><?= $ca['tenca'] ?></div>
                  <div class="ca-clock">
                      <span class="me-1">🕐</span><?= $ca['time'] ?>
                  </div>
              </div>

            <?php endforeach; ?>

        </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>

</body>
</html>
