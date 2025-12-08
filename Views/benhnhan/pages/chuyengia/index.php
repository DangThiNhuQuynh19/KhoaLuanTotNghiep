<?php
include_once("Controllers/cchuyengia.php");
include_once("Controllers/clinhvuc.php");

$cChuyenGia = new cChuyenGia();
$cLinhVuc = new cLinhVuc();


$dsLinhVuc = $cLinhVuc->getAllLinhVuc();
// Kiểm tra các điều kiện lọc
if (!empty($_GET['name']) && !empty($_GET['linhvuc'])) {
    // Nếu có cả tên và lĩnh vực
    $ds = $cChuyenGia->getChuyenGiaByTenAndLinhVuc(trim($_GET['name']), $_GET['linhvuc']);
} elseif (!empty($_GET['name'])) {
    // Chỉ tìm theo tên
    $ds = $cChuyenGia->getChuyenGiaByName(trim($_GET['name']));
} elseif (!empty($_GET['linhvuc'])) {
    // Chỉ lọc theo lĩnh vực
    $ds = $cChuyenGia->getChuyenGiaByLinhVuc($_GET['linhvuc']);
} else {
    // Không lọc gì cả
    $ds = $cChuyenGia->getAllChuyenGia1();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách chuyên gia</title>
    <style>
        body{
	        margin-top: 100px;
	        margin: auto;
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
        body {
            font-family: Arial, sans-serif;
            background: white;
        }
        h1 {
            text-align: center;
            color: #3c1561;
            margin-top: 30px;
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
            text-align: right; /* Align the button to the right */
        }

        .doctor-buttons a {
            text-decoration: none;
            padding: 10px 18px;
            margin-right: 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            background-color: #3c1561; /* Set the button color to purple */
            color: #fff; /* White text color */
        }

        .btn-primary {
            background-color: #1a237e;
            color: #fff;
        }

        .btn-secondary {
            background-color: #0288d1;
            color: #fff;
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
            .doctor-buttons a {
                display: inline-block;
                margin: 10px 5px 0;
            }
        }
        .search-forms{
            padding-left: 30%;
        }
        .btn-schedule {
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
        .btn-schedule {
            background: linear-gradient(90deg,#0288d1,#0177b6);
            box-shadow: 0 4px 12px rgba(1,87,155,0.12);
        }
        .btn-schedule:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(1,87,155,0.12); }

    </style>
</head>
<body>
<h1>Danh sách chuyên gia</h1>

<div class="search-forms" style="margin-top: 10px;">
    <!-- Form tìm kiếm bác sĩ và lọc theo khoa -->
    <form method="GET" action="index.php" class="filter-form" aria-label="Tìm kiếm chuyên gia">
        <input type="hidden" name="action" value="chuyengia">

        <div class="input-group" role="search" aria-label="Tìm theo tên">
            <span class="bi bi-search" aria-hidden="true"></span>
            <input type="text" name="name" class="form-control" placeholder="Nhập tên chuyên gia..."
                value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
        </div>

        <div class="input-group" aria-label="Chọn lĩnh vực">
            <select name="linhvuc" class="form-select" aria-label="Lĩnh vực">
                <option value="">-- Chọn lĩnh vực --</option>
                <?php
                    if (isset($dsLinhVuc) && $dsLinhVuc && $dsLinhVuc->num_rows > 0) {
                        // rewind result pointer in case this template is included after earlier fetch
                        $dsLinhVuc->data_seek(0);
                        while ($row = $dsLinhVuc->fetch_assoc()) {
                            $selected = (isset($_GET['linhvuc']) && $row['malinhvuc'] == $_GET['linhvuc']) ? "selected" : "";
                            echo "<option value='".htmlspecialchars($row['malinhvuc'])."' $selected>".htmlspecialchars($row['tenlinhvuc'])."</option>";
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
        echo "<p style='text-align:center;'>Không có chuyên gia nào.</p>";
    } else {
        while ($row = $ds->fetch_assoc()) {
?>
    <div class="doctor-card">
        <div class="doctor-img">
            <img src="Assets/img/<?php echo htmlspecialchars($row['imgcg']); ?>" alt="Ảnh chuyên gia">
        </div>
        <div class="doctor-info">
            <h2 class="doctor-name">
                <?php echo htmlspecialchars($row['capbac']) . ' ' . htmlspecialchars($row['hoten']); ?>
            </h2>
            <p class="doctor-position"><?php echo htmlspecialchars($row['tenlinhvuc']); ?></p>
            <p class="doctor-desc">
                <?php
                    $desc = strip_tags($row['motacg']);  
                    echo strlen($desc) > 300 ? substr($desc, 0, 300) . '...' : $desc;
                ?>
            </p>
            <div class="doctor-buttons" >
                <a href="?action=chitietchuyengia&idcg=<?php echo $row['machuyengia']; ?>" class="btn-secondary">XEM CHI TIẾT</a>
                <a href="?action=lichkhamchuyengia&idcg=<?php echo $row['machuyengia']; ?>" class="btn-schedule" aria-label="Xem lịch khám bác sĩ <?php echo $hoten; ?>">XEM LỊCH KHÁM</a>
            </div>
        </div>
    </div>
<?php
        }
    }
?>
</body>
</html>
