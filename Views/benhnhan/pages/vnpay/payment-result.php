<?php
include_once('Model/mphieukhambenh.php');
session_start();

$vnp_HashSecret = "NN9LUAMR92XNZDIXS411CEX5AAS7DYOA";

// Nhận giá trị từ URL trả về của VNPAY
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? null;
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? null;

// Kiểm tra mã bảo mật
$inputData = $_GET;
unset($inputData['vnp_SecureHash']); // Loại bỏ SecureHash
ksort($inputData); // Sắp xếp dữ liệu

$query = http_build_query($inputData);
$secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $response = $_GET; // Nhận phản hồi từ VNPAY
    file_put_contents('vnpay_response.log', print_r($response, true), FILE_APPEND);
}
// Kiểm tra SecureHash và ResponseCode để chuyển trang
if ($secureHash === $vnp_SecureHash) {
    if ($vnp_ResponseCode == '00') {
        // Kiểm tra nếu có các giá trị trong session
        if (isset($_SESSION['maphieukhambenh'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'], $_SESSION['mabacsi'], $_SESSION['mabenhnhan'], $_SESSION['matrangthai'])) {
            if(isset($_SESSION['mabacsi'])){
                $result = $pPhieuKham->insertphieukham($_SESSION['maphieukhambenh'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'], $_SESSION['mabacsi'], $_SESSION['mabenhnhan'], $_SESSION['matrangthai']);
            }
            elseif(isset($_SESSION['machuyengia'])){
                $result = $pPhieuKham->insertphieukham($_SESSION['maphieukhambenh'], $_SESSION['ngaykham'], $_SESSION['makhunggiokb'], $_SESSION['machuyengia'], $_SESSION['mabenhnhan'], $_SESSION['matrangthai']);
            }
            if ($result) {
                header('Location: http://localhost/KhoaLuanTotNghiep/?action=datlichkham&status=success&message=Thanh%20toán%20thành%20công!');
                exit;
            } else {
                // Thất bại: Hiển thị thông báo lỗi
                header('Location: http://localhost/KhoaLuanTotNghiep/?action=datlichkham&status=error&message=Lỗi%20khi%20chèn%20dữ%20liệu!');
                exit;
            }
        }
    } else {
        // Thất bại: Chuyển đến trang chính với thông báo lỗi
        header('Location: http://localhost/KhoaLuanTotNghiep/?action=datlichkham&status=error&message=Mã%20lỗi:%20' . $vnp_ResponseCode);
        exit;
    }
} else {
    // Lỗi bảo mật
    header('Location: http://localhost/KhoaLuanTotNghiep/?action=datlichkham&status=error&message=Lỗi%20bảo%20mật!');
    exit;
}
?>
