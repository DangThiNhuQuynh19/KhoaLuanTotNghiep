<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// VNPAY configuration
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost/KhoaLuanTotNghiep/Views/benhnhan/pages/vnpay/payment-result.php";
$vnp_TmnCode = "A1B2C3D4";
$vnp_HashSecret = "NN9LUAMR92XNZDIXS411CEX5AAS7DYOA";
session_start(); 

// Check if required POST parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['selectedDate'], $_POST['selectedTime'], $_POST['department'], $_POST['doctor'])) {
    
    // Store data in session
    $_SESSION['selectedDate'] = htmlspecialchars($_POST['selectedDate']);
    $_SESSION['selectedTime'] = htmlspecialchars($_POST['selectedTime']);
    $_SESSION['department'] = htmlspecialchars($_POST['department']);
    $_SESSION['doctor'] = htmlspecialchars($_POST['doctor']);
}

// Amount and order information
$vnp_Amount = isset($_SESSION['tongtien']) ? intval($_SESSION['tongtien']) * 100 : 100000; 
$vnp_OrderInfo = isset($_POST['thongtinhoadon']) ? htmlspecialchars($_POST['thongtinhoadon']) : 'Thanh toán chi phí khám';  

// Additional parameters
$vnp_OrderType = $vnp_OrderInfo;  
$vnp_Locale = "vn";  
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];  
$startTime = date("YmdHis");
$vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

// Prepare input data
$inputData = array(
    "vnp_Version" => "2.1.0",        
    "vnp_TmnCode" => $vnp_TmnCode,    
    "vnp_Amount" => $vnp_Amount,     
    "vnp_Command" => "pay",           
    "vnp_CreateDate" => date('YmdHis'), 
    "vnp_CurrCode" => "VND",        
    "vnp_IpAddr" => $vnp_IpAddr,      
    "vnp_Locale" => $vnp_Locale,      
    "vnp_OrderInfo" => $vnp_OrderInfo, 
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl, 
    "vnp_TxnRef" => uniqid(),         
    "vnp_ExpireDate" => $vnp_ExpireDate 
);

// Sort and prepare query string
ksort($inputData);
$query = http_build_query($inputData);
$hashdata = http_build_query($inputData, '', '&');

// Generate secure hash
if (!empty($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $query .= '&vnp_SecureHash=' . $vnpSecureHash;  
}

// Create the payment URL
$vnp_Url .= "?" . $query;

// Return the payment URL as JSON
$returnData = array('success' => true, 'paymentUrl' => $vnp_Url);
echo json_encode($returnData);  
?>
