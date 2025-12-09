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
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    margin: 0;
    padding: 20px 0;
}

h1 {
    text-align: center;
    color: #fff;
    margin: 30px 0;
    position: relative;
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}
h1 i {
    margin-right: 10px;
}
.home-link {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    text-decoration: none;
    color: #fff;
    display: flex;
    align-items: center;
    font-size: 16px;
    background: rgba(255,255,255,0.2);
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}
.home-link:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-50%) scale(1.05);
}
.home-link i {
    font-size: 20px;
    margin-right: 8px;
}
.search-forms {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    margin: 30px auto;
    max-width: 900px;
}
.search-forms form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    align-items: center;
}
.textsearch {
    border-radius: 12px;
    min-width: 220px;
    height: 42px;
    padding: 0 15px;
    border: 2px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.95);
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.textsearch:focus {
    border-color: #fff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
    background: #fff;
}
.btnsearch {
    min-width: 120px;
    height: 42px;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
}
.btnsearch:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 172, 254, 0.6);
}
.btn-reset {
    background: rgba(255,255,255,0.9);
    color: #333;
    border: none;
    border-radius: 12px;
    padding: 10px 20px;
    height: 42px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}
.btn-reset:hover {
    background: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.doctor-card {
    display: flex;
    gap: 30px;
    background: #fff;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    margin: 25px auto;
    max-width: 1100px;
    align-items: flex-start;
    transition: all 0.3s ease;
}
.doctor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}
.doctor-img img {
    width: 200px;
    height: 200px;
    border-radius: 15px;
    border: 4px solid #667eea;
    object-fit: cover;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.doctor-info {
    flex: 1;
}
.doctor-name {
    font-size: 26px;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 12px;
    text-transform: uppercase;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
/* Fallback for browsers that don't support background-clip */
@supports not (background-clip: text) or not (-webkit-background-clip: text) {
    .doctor-name {
        background: none;
        color: #667eea;
    }
}
.doctor-position {
    font-style: italic;
    color: #4a5568;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}
.doctor-position::before {
    content: "🎓";
    font-style: normal;
}
.doctor-desc {
    margin: 15px 0 25px;
    color: #4a5568;
    line-height: 1.8;
    font-size: 15px;
}
.doctor-buttons {
    text-align: right;
}
.doctor-buttons a {
    text-decoration: none;
    padding: 12px 25px;
    margin-right: 10px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}
.doctor-buttons a:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}
.doctor-buttons a::after {
    content: "→";
    font-size: 18px;
}
.btn-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    text-decoration: none;
}
.btn-purple:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}
.text-center {
    text-align: center;
    margin: 30px 0;
}
.mt-4 {
    margin-top: 1.5rem;
}
.empty-message {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 20px;
    text-align: center;
    max-width: 600px;
    margin: 30px auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.empty-message p {
    font-size: 18px;
    color: #4a5568;
    margin: 0;
}
@media (max-width:768px) {
    h1 {
        font-size: 1.8rem;
        padding: 0 20px;
    }
    .home-link {
        position: static;
        transform: none;
        display: inline-flex;
        margin-bottom: 20px;
    }
    .search-forms form {
        flex-direction: column;
    }
    .textsearch {
        width: 100%;
    }
    .doctor-card {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 20px;
    }
    .doctor-img img {
        margin-bottom: 20px;
        width: 180px;
        height: 180px;
    }
    .doctor-buttons {
        text-align: center;
    }
    .doctor-buttons a {
        display: inline-flex;
        margin: 10px 5px 0;
    }
}
</style>
</head>
<body>

<div class="main-container">

<h1>
    <i class="bi bi-award-fill"></i> Danh sách chuyên gia
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
    echo "<div class='empty-message'><p style='color:#e53e3e;'><i class='bi bi-exclamation-triangle-fill'></i> Lỗi kết nối dữ liệu.</p></div>";
} elseif (is_int($ds) && $ds == 0) {
    echo "<div class='empty-message'><p><i class='bi bi-info-circle-fill'></i> Không có chuyên gia nào.</p></div>";
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

</div>
</body>
</html>