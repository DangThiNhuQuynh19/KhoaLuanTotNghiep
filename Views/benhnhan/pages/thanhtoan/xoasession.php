<?php
session_start();

// Xóa chỉ những session liên quan đến thanh toán
unset($_SESSION['maphieukhambenh']);
unset($_SESSION['ngaykham']);
unset($_SESSION['macalamviec']);
unset($_SESSION['mabacsi']);
unset($_SESSION['mabenhnhan']);
unset($_SESSION['tongtien']);
unset($_SESSION['trangthai']);
unset($_SESSION['machuyengia']);

echo "done";
?>