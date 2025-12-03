<?php
include_once('Controllers/cphieukhambenh.php');

if(isset($_GET['maphieukhambenh'])){
    $maphieukhambenh = $_GET['maphieukhambenh'];
    $cphieukhambenh = new cPhieuKhamBenh();

    // Gọi hàm update trạng thái
    $update = $cphieukhambenh->updateTrangThaiPKB($maphieukhambenh, 'Đã khám');

    if($update){
        header("Location: ?action=benhnhan");
        exit;
    } else {
        echo "Cập nhật thất bại!";
    }
}
?>
