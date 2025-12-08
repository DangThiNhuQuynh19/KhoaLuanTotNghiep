<?php
// API endpoint để lấy danh sách thông báo
session_start();
header('Content-Type: application/json');

include_once('../Controllers/cthongbao.php');

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user'])) {
   echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
   exit;
}

$manguoidung = $_SESSION['user'];
$action = $_GET['action'] ?? '';

$cThongBao = new cThongBao();

switch ($action) {
   case 'get_all':
       // Lấy tất cả thông báo
       $daxem = isset($_GET['daxem']) ? intval($_GET['daxem']) : null;
       $thongbaoList = $cThongBao->get_thongbao_by_manguoidung($manguoidung, $daxem);
       
       if ($thongbaoList === -1) {
           echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database']);
       } else if ($thongbaoList === 0) {
           echo json_encode(['success' => true, 'data' => [], 'count' => 0]);
       } else {
           echo json_encode(['success' => true, 'data' => $thongbaoList, 'count' => count($thongbaoList)]);
       }
       break;
       
   case 'count_unread':
       // Đếm số thông báo chưa xem
       $count = $cThongBao->count_thongbao_chuaxem($manguoidung);
       echo json_encode(['success' => true, 'count' => $count]);
       break;
       
   case 'mark_read':
       // Đánh dấu thông báo đã xem
       $mathongbao = isset($_GET['mathongbao']) ? intval($_GET['mathongbao']) : 0;
       if ($mathongbao > 0) {
           $result = $cThongBao->mark_thongbao_as_read($mathongbao);
           echo json_encode(['success' => $result, 'message' => $result ? 'Đã đánh dấu đã xem' : 'Lỗi']);
       } else {
           echo json_encode(['success' => false, 'message' => 'Thiếu mathongbao']);
       }
       break;
       
   case 'mark_all_read':
       // Đánh dấu tất cả thông báo đã xem
       $result = $cThongBao->mark_all_thongbao_as_read($manguoidung);
       echo json_encode(['success' => $result, 'message' => $result ? 'Đã đánh dấu tất cả đã xem' : 'Lỗi']);
       break;
       
   case 'delete':
       // Xóa thông báo
       $mathongbao = isset($_GET['mathongbao']) ? intval($_GET['mathongbao']) : 0;
       if ($mathongbao > 0) {
           $result = $cThongBao->delete_thongbao($mathongbao);
           echo json_encode(['success' => $result, 'message' => $result ? 'Đã xóa thông báo' : 'Lỗi']);
       } else {
           echo json_encode(['success' => false, 'message' => 'Thiếu mathongbao']);
       }
       break;
       
   default:
       echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
       break;
}
?>