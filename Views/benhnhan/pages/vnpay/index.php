<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "https://hanhphuc.site?action=paymentresult";
$vnp_Returnurl = "http://localhost/HanhPhuc/index.php?action=paymentresult";
$vnp_TmnCode = "QG2JUNF3";
$vnp_HashSecret = "08QNEGVZXSFGER8MD7Y0MAGK24DAT6KW";

$vnp_Amount = isset($_SESSION['tongtien']) ? $_SESSION['tongtien'] * 100 : 100000; 
$vnp_OrderInfo = 'Thanh toán chi phí khám';  
// Các tham số khác
$vnp_OrderType = $vnp_OrderInfo;  
$vnp_Locale = "vn";  
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];  
$startTime = date("YmdHis");
$vnp_ExpireDate = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));

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

ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";

foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . rtrim($query, '&');
if (!empty($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= '&vnp_SecureHash='.$vnpSecureHash;  
}

$returnData = array('success' => true, 'paymentUrl' => $vnp_Url);
echo json_encode($returnData);  

// Redirect
header('Location: ' . $vnp_Url);
?>
