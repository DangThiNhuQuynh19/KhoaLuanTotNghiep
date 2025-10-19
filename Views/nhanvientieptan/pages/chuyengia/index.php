<?php
include_once("Controllers/cchuyengia.php");
include_once("Controllers/clinhvuc.php");

$cChuyenGia = new cChuyenGia();
$cLinhVuc = new cLinhVuc();

// Lấy danh sách lĩnh vực
$dsLinhVuc = $cLinhVuc->getAllLinhVuc();

// Xử lý filter
$name = trim($_GET['name'] ?? '');
$linhvuc = $_GET['linhvuc'] ?? '';

if ($name && $linhvuc) {
    $ds = $cChuyenGia->getChuyenGiaByTenAndLinhVuc($name, $linhvuc);
} elseif ($name) {
    $ds = $cChuyenGia->getChuyenGiaByName($name);
} elseif ($linhvuc) {
    $ds = $cChuyenGia->getChuyenGiaByLinhVuc($linhvuc);
} else {
    $ds = $cChuyenGia->getAllChuyenGia();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách chuyên gia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{font-family: Arial, sans-serif; background:#fff; margin:0; padding:0;}
h1 {text-align:center; color:#3c1561; margin-top:30px; position: relative;}
.search-forms {text-align:center; margin-top:20px; margin-bottom:20px;}
.textsearch{border-radius:15px; width:200px; height:30px; padding-left:5px; border:1px solid #3c1561;}
.btnsearch{width:90px; height:32px; border-radius:10px; color:#fff; background-color:#3c1561; border:none; margin-left:5px; cursor:pointer;}
.btn-reset {background-color:#f0f0f0; color:#333; border:1px solid #ccc; border-radius:10px; padding:6px 12px; font-size:14px; cursor:pointer; text-decoration:none; margin-left:5px; transition:0.2s;}
.btn-reset:hover {background-color:#ddd; color:#000;}
.doctor-card {display:flex; gap:25px; background:#fff; padding:20px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.08); margin:20px auto; max-width:1100px; align-items:flex-start;}
.doctor-img img {width:180px; border-radius:10px; border:1px solid #ddd; object-fit:cover;}
.doctor-info {flex:1;}
.doctor-name {font-size:22px; font-weight:bold; color:#222; margin-bottom:10px; text-transform:uppercase;}
.doctor-position {font-style:italic; color:#666; margin-bottom:6px;}
.doctor-desc {margin:10px 0 20px; color:#444; line-height:1.6;}
.doctor-buttons{text-align:right;}
.doctor-buttons a{text-decoration:none; padding:10px 18px; margin-right:10px; border-radius:6px; font-weight:600; font-size:14px; background-color:#3c1561; color:#fff;}
.btn-purple {background-color:#6f42c1; color:#fff; border:none; padding:8px 16px; border-radius:6px;}
.btn-purple:hover {background-color:#5a32a3;}
@media (max-width:768px) {
    .doctor-card{flex-direction:column; align-items:center; text-align:center;}
    .doctor-img img{margin-bottom:15px;}
    .doctor-buttons a{display:inline-block; margin:10px 5px 0;}
}
</style>
</head>
<body>

<h1>
    <a href="index.php" style="position:absolute; left:20px; top:0; text-decoration:none; color:#3c1561; display:flex; align-items:center; font-size:16px;">
        <i class="bi bi-house-door-fill" style="font-size:20px; margin-right:5px;"></i> Trang chủ
    </a>
    <span><i class="bi bi-people"></i> Danh sách chuyên gia</span>
</h1>

<div class="search-forms">
    <form method="GET" action="index.php">
        <input type="hidden" name="action" value="chuyengia">
        <input class="textsearch" type="text" name="name" placeholder="Tên chuyên gia..." value="<?= htmlspecialchars($name) ?>">
        <select name="linhvuc" class="textsearch">
            <option value="">-- Chọn lĩnh vực --</option>
            <?php
            if ($dsLinhVuc && $dsLinhVuc->num_rows > 0) {
                while ($row = $dsLinhVuc->fetch_assoc()) {
                    $selected = ($row['malinhvuc'] == $linhvuc) ? "selected" : "";
                    echo "<option value='{$row['malinhvuc']}' $selected>".htmlspecialchars($row['tenlinhvuc'])."</option>";
                }
            }
            ?>
        </select>
        <button class="btnsearch" type="submit">Tìm kiếm</button>
        <a href="index.php?action=chuyengia" class="btn-reset">Bỏ lọc</a>
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
        <img src="Assets/img/<?= htmlspecialchars($row['imgcg']); ?>" alt="Ảnh chuyên gia">
    </div>
    <div class="doctor-info">
        <h2 class="doctor-name"><?= htmlspecialchars($row['capbac']) . ' ' . htmlspecialchars($row['hoten']); ?></h2>
        <p class="doctor-position"><?= htmlspecialchars($row['tenlinhvuc']); ?></p>
        <p class="doctor-desc"><?= strlen(strip_tags($row['motacg']))>300 ? substr(strip_tags($row['motacg']),0,300).'...' : strip_tags($row['motacg']); ?></p>
        <div class="doctor-buttons">
            <a href="index.php?action=chitietchuyengia&idcg=<?= $row['machuyengia']; ?>">XEM CHI TIẾT</a>
        </div>
    </div>
</div>
<?php
    }
}
?>

<div class="text-center mt-4">
    <a href="?action=trangchu" class="btn btn-purple"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

</body>
</html>
