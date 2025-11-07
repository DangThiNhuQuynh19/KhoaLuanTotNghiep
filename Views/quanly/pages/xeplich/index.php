<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");

// Lấy ngày hiện tại hoặc ngày filter
$ngay = isset($_GET['ngay']) && $_GET['ngay'] != "" ? $_GET['ngay'] : date("Y-m-d");

include_once("Controllers/clichlamviec.php");

$controller = new cLichLamViec();    
$dataLich = $controller->getlichlamviec($ngay);   
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lịch làm việc nhân sự</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
    --main: #4b3fa8;
    --main-light: #6a5bd7;
    --bg: #f6f6fb;
}

/* BODY */
body {
    background: var(--bg);
    font-family: "Inter", sans-serif;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    height: 100vh;
    background: #fff;
    border-right: 1px solid #eaeaea;
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 20px;
    box-shadow: 3px 0 12px rgba(0,0,0,0.05);
}

.sidebar h2 {
    font-size: 18px;
    text-align: center;
    font-weight: 700;
    color: var(--main);
    margin-bottom: 20px;
}

.sidebar ul { padding-left: 0; list-style: none; }

.sidebar a {
    text-decoration: none;
    color: var(--main);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    border-radius: 8px;
    font-weight: 500;
}
.sidebar a:hover,
.sidebar a.active {
    background: var(--main);
    color: white;
}

/* MAIN */
.main-content {
    margin-left: 260px;
    padding: 40px 30px;
}

.title-page {
    font-size: 26px;
    font-weight: 700;
    color: var(--main);
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
    border-radius: 18px !important;
    border: none;
    transition: all 0.35s ease;
    background: #ffffff;
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
    background: #27c4a8;
    padding: 4px 10px;
    border-radius: 6px;
    color: white;
}

.tag-offline {
    background: #ff7b54;
    padding: 4px 10px;
    border-radius: 6px;
    color: white;
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

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Quản lý lịch</h2>
    <ul>
        <li><a href="?action=lichlamviec"><i class="fas fa-user-clock me-2"></i> Phân ca</a></li>
        <li><a href="?action=xeplich" class="active"><i class="fas fa-calendar-check me-2"></i> Xếp lịch</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<h1 class="title-page">Lịch làm việc của nhân sự</h1>

<div class="table-container mt-3">

    <!-- FILTER -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="action" value="xeplich">
        <div class="col-md-3">
            <label class="form-label fw-bold">Chọn ngày</label>
            <input type="date" name="ngay" value="<?= $ngay ?>" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">
                <i class="fas fa-filter me-2"></i>Lọc
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

    <div class="row">
    <?php foreach ($grouped as $nguoidung): ?>
        <div class="col-md-6 mb-4">
            <div class="card p-4 shadow-sm">

                <!-- Avatar + tên + vai trò -->
                <div class="d-flex align-items-center mb-3">
                    <img src="Assets/img/<?= $nguoidung['avatar'] ?>" class="avatar1 me-3">
                    <div>
                        <h4 class="text-primary fw-bold mb-1"><?= $nguoidung['ten'] ?></h4>
                        <span class="badge bg-info"><?= $nguoidung['vaitro'] ?></span>
                    </div>
                </div>

                <!-- Hình thức -->
                <?php if ($nguoidung['hinhthuclamviec'] == "online"): ?>
                    <span class="tag-online mb-2">Khám trực tuyến (Online)</span>
                <?php else: ?>
                    <span class="tag-offline mb-2">Làm tại bệnh viện (Offline)</span>
                <?php endif; ?>

                <!-- Phòng -->
                <?php if (!empty($nguoidung['phong'])): ?>
                <p class="text-muted mt-2">
                    <i class="fa-solid fa-building me-1"></i>
                    <?= $nguoidung['phong'] ?>
                </p>
                <?php endif; ?>

                <hr>

                <!-- Danh sách ca -->
                <h6 class="fw-bold mb-2">Ca làm việc:</h6>

                <?php foreach ($nguoidung['lich'] as $ca): ?>
                  <div class="ca-line mb-2">
                      <div class="ca-title"><?= $ca['tenca'] ?></div>
                      <div class="ca-clock">
                          <i class="fa-solid fa-clock me-1"></i><?= $ca['time'] ?>
                      </div>
                  </div>

                <?php endforeach; ?>

            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>
</div>

</body>
</html>
