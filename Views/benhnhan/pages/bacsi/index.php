<?php
include_once("Controllers/cbacsi.php");
include_once("Controllers/cchuyenkhoa.php");

$cBacSi = new cBacSi();
$cChuyenKhoa = new cChuyenKhoa();

// Lấy danh sách chuyên khoa
$dsKhoa = $cChuyenKhoa->getAllChuyenKhoa();

// Kiểm tra các điều kiện lọc
if (!empty($_GET['name']) && !empty($_GET['khoa'])) {
    // Nếu có cả tên và khoa
    $ds = $cBacSi->getBacSiByTenAndKhoa(trim($_GET['name']), $_GET['khoa']);
} elseif (!empty($_GET['name'])) {
    // Chỉ tìm theo tên
    $ds = $cBacSi->getBacSiByName(trim($_GET['name']));
} elseif (!empty($_GET['khoa'])) {
    // Chỉ lọc theo khoa
    $ds = $cBacSi->getBacSiByKhoa($_GET['khoa']);
} else {
    // Không lọc gì cả
    $ds = $cBacSi->getAllBacSi1();
}
?>

<?php
// Assumes $dsKhoa and $ds are already prepared in the controller context as in your previous code.
// If not, include the controller code that builds $dsKhoa and $ds above this template.
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách bác sĩ</title>
    <style>
        body{
            margin-top: 100px;
            font-family: Arial, sans-serif;
            background: white;
            color: #222;
        }

        /* Filter / search form styles */
        .filter-form{ display:flex; gap:10px; align-items:center; justify-content:center; flex-wrap:nowrap; }
        .filter-form .input-group{ position: relative; display:flex; align-items:center; max-width: fit-content; }
        .filter-form .input-group .bi-search{ position: absolute; left: 12px; color: #6b2f8a; font-size: 0.9rem; pointer-events: none; z-index: 1; }
        .filter-form .form-control{
            border-radius: 25px !important;
            width: 140px;
            padding: 6px 12px 6px 32px;
            font-size: 13px;
            border: 2px solid rgba(108,58,148,0.12) !important;
            background: #fff;
            box-shadow: 0 2px 8px rgba(59,21,97,0.06);
            outline: none;
        }
        .filter-form .form-control:focus{
            border-color: rgba(108,58,148,0.3) !important;
            box-shadow: 0 2px 12px rgba(59,21,97,0.12) !important;
        }
        .filter-form .form-select{
            border-radius: 25px;
            width: 140px;
            padding: 6px 12px;
            font-size: 13px;
            border: 2px solid rgba(108,58,148,0.12);
            background: #fff;
            box-shadow: 0 2px 8px rgba(59,21,97,0.06);
        }
        .filter-form .btn-search{
            border-radius: 25px;
            padding: 7px 18px;
            background: #3c1561;
            color: #fff;
            border: 0;
            font-weight:600;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(60,21,97,0.18);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .filter-form .btn-search:hover{ transform: translateY(-2px); background:#4d1a7c; }

        /* Smaller devices */
        @media (max-width: 992px){
            .filter-form{ flex-wrap: wrap; }
            .filter-form .form-control, .filter-form .form-select{ width: 220px; }
        }
        @media (max-width: 576px){
            .filter-form .form-control, .filter-form .form-select{ width: 180px; }
            .filter-form{ gap:8px; }
        }

        h1 {
            text-align: center;
            color: #3c1561;
            margin-top: 20px;
            font-size: 2rem;
        }

        .doctor-card {
            display: flex;
            gap: 25px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin: 30px auto;
            max-width: 1100px;
            align-items: flex-start;
        }

        .doctor-img img {
            width: 180px;
            border-radius: 10px;
            border: 1px solid #ddd;
            object-fit: cover;
        }

        .doctor-info {
            flex: 1;
        }

        .doctor-name {
            font-size: 22px;
            font-weight: bold;
            color: #222;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .doctor-position,
        .doctor-hospital {
            font-style: italic;
            color: #666;
            margin-bottom: 6px;
        }

        .doctor-desc {
            margin: 10px 0 20px;
            color: #444;
            line-height: 1.6;
        }

        .doctor-buttons {
            text-align: right;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* Detail + Schedule buttons */
        .btn-detail, .btn-schedule {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #fff;
            display: inline-block;
            transition: transform .08s ease, box-shadow .12s ease;
            border: none;
        }
        .btn-detail {
            background: linear-gradient(90deg,#4b1f6b,#3c1561);
            box-shadow: 0 4px 12px rgba(60,21,97,0.12);
        }
        .btn-detail:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(60,21,97,0.12); }

        .btn-schedule {
            background: linear-gradient(90deg,#0288d1,#0177b6);
            box-shadow: 0 4px 12px rgba(1,87,155,0.12);
        }
        .btn-schedule:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(1,87,155,0.12); }

        .btn-ghost {
            background: transparent;
            color: #3c1561;
            border: 1px solid #e6dff5;
        }

        @media (max-width: 768px) {
            .doctor-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .doctor-img img {
                margin-bottom: 15px;
            }
            .doctor-buttons {
                justify-content: center;
            }
            .doctor-buttons a {
                display: inline-block;
                margin: 8px 6px 0;
            }
        }
        .search-forms{
            text-align: center;
            margin-top: 40px;
            padding-top: 40px;
        }
    </style>
</head>
<body>
<div style="margin-top: 100px;">
    <h1>Danh sách bác sĩ</h1>

    <div class="search-forms" style="margin-top: 10px;">
        <!-- Form tìm kiếm bác sĩ và lọc theo khoa -->
        <form method="GET" action="index.php" class="filter-form" aria-label="Tìm kiếm bác sĩ">
            <input type="hidden" name="action" value="bacsi">

            <div class="input-group" role="search" aria-label="Tìm theo tên">
                <span class="bi bi-search" aria-hidden="true"></span>
                <input type="text" name="name" class="form-control" placeholder="Nhập tên bác sĩ..."
                    value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
            </div>

            <div class="input-group" aria-label="Chọn chuyên khoa">
                <select name="khoa" class="form-select" aria-label="Chuyên khoa">
                    <option value="">-- Chọn chuyên khoa --</option>
                    <?php
                        if (isset($dsKhoa) && $dsKhoa && $dsKhoa->num_rows > 0) {
                            // rewind result pointer in case this template is included after earlier fetch
                            $dsKhoa->data_seek(0);
                            while ($row = $dsKhoa->fetch_assoc()) {
                                $selected = (isset($_GET['khoa']) && $row['machuyenkhoa'] == $_GET['khoa']) ? "selected" : "";
                                echo "<option value='".htmlspecialchars($row['machuyenkhoa'])."' $selected>".htmlspecialchars($row['tenchuyenkhoa'])."</option>";
                            }
                        }
                    ?>
                </select>
            </div>

            <button class="btn-search" type="submit" aria-label="Tìm kiếm bác sĩ">Tìm kiếm</button>
        </form>
    </div>

<?php
    if (is_int($ds) && $ds == -1) {
        echo "<p style='text-align:center; color:red;'>Lỗi kết nối dữ liệu.</p>";
    } elseif (is_int($ds) && $ds == 0) {
        echo "<p style='text-align:center;'>Không có bác sĩ nào.</p>";
    } else {
        // If $ds is a mysqli_result and pointer may have moved, attempt to reset:
        if (is_object($ds) && method_exists($ds, 'data_seek')) {
            $ds->data_seek(0);
        }
        while ($row = $ds->fetch_assoc()) {
            // prepare safe values
            $mabacsi = htmlspecialchars($row['mabacsi'], ENT_QUOTES);
            $img = htmlspecialchars($row['imgbs'] ?? 'default.png', ENT_QUOTES);
            $hoten = htmlspecialchars($row['hoten'] ?? '', ENT_QUOTES);
            $capbac = htmlspecialchars($row['capbac'] ?? '', ENT_QUOTES);
            $tenkhoa = htmlspecialchars($row['tenchuyenkhoa'] ?? '', ENT_QUOTES);
            $desc = strip_tags($row['motabs'] ?? '');
            $shortDesc = strlen($desc) > 300 ? substr($desc, 0, 300) . '...' : $desc;
?>
        <div class="doctor-card" role="article" aria-label="Thông tin bác sĩ <?php echo $hoten; ?>">
            <div class="doctor-img" aria-hidden="true">
                <img src="Assets/img/<?php echo $img; ?>" alt="Ảnh bác sĩ <?php echo $hoten; ?>">
            </div>
            <div class="doctor-info">
                <h2 class="doctor-name"><?php echo $capbac . ' ' . $hoten; ?></h2>
                <p class="doctor-position"><?php echo $tenkhoa; ?></p>
                <p class="doctor-desc"><?php echo htmlspecialchars($shortDesc); ?></p>
                <div class="doctor-buttons" >
                    <!-- XEM CHI TIẾT -->
                    <a href="?action=chitietbacsi&id=<?php echo $mabacsi; ?>" class="btn-detail" aria-label="Xem chi tiết bác sĩ <?php echo $hoten; ?>">XEM CHI TIẾT</a>
                    <!-- XEM LỊCH KHÁM -->
                    <a href="?action=lichkham&id=<?php echo $mabacsi; ?>" class="btn-schedule" aria-label="Xem lịch khám bác sĩ <?php echo $hoten; ?>">XEM LỊCH KHÁM</a>
                </div>
            </div>
        </div>
<?php
        }
    }
?>
</div>
</body>
</html>