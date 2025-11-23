<?php
include_once('Controllers/cBenhNhan.php');
if (isset($_GET['mabenhnhan'])) {
    $benhnhanController = new cBenhNhan();
    if ($benhnhanController->deletebenhnhan($_GET['mabenhnhan'])) {
        header("Location: index.php?action=caidat");
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>Không thể xóa bệnh nhân vì đã có phiếu khám hoặc lỗi hệ thống.</p>";
    }
} else {
    echo "<p style='color:red; text-align:center;'>Không tìm thấy mã bệnh nhân.</p>";
}

?>