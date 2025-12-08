<?php
include_once("Models/mthongbao.php");

class cThongBao {
    // Tạo thông báo mới
    public function create_thongbao($manguoidung, $tieude, $noidung, $loaithongbao = 'ketquaxetnghiem', $malichxetnghiem = null) {
        $p = new mThongBao();
        $result = $p->insert_thongbao($manguoidung, $tieude, $noidung, $loaithongbao, $malichxetnghiem);
        return $result;
    }
    
    // Lấy danh sách thông báo
    public function get_thongbao_by_manguoidung($manguoidung, $daxem = null) {
        $p = new mThongBao();
        $tbl = $p->select_thongbao_by_manguoidung($manguoidung, $daxem);
        
        if (!$tbl) {
            return -1;
        } else {
            if ($tbl->num_rows > 0) {
                $list = array();
                while ($r = $tbl->fetch_assoc()) {
                    $list[] = $r;
                }
                return $list;
            } else {
                return 0;
            }
        }
    }
    
    // Đếm số thông báo chưa xem
    public function count_thongbao_chuaxem($manguoidung) {
        $p = new mThongBao();
        return $p->count_thongbao_chuaxem($manguoidung);
    }
    
    // Đánh dấu thông báo đã xem
    public function mark_thongbao_as_read($mathongbao) {
        $p = new mThongBao();
        return $p->update_thongbao_daxem($mathongbao);
    }
    
    // Đánh dấu tất cả đã xem
    public function mark_all_thongbao_as_read($manguoidung) {
        $p = new mThongBao();
        return $p->update_all_thongbao_daxem($manguoidung);
    }
    
    // Xóa thông báo
    public function delete_thongbao($mathongbao) {
        $p = new mThongBao();
        return $p->delete_thongbao($mathongbao);
    }
    
    // Tạo và gửi thông báo kết quả xét nghiệm cho bác sĩ
    public function send_test_result_notification($malichxetnghiem) {
        $p = new mThongBao();
        
        // Lấy thông tin bác sĩ từ lịch xét nghiệm
        $bacsi_info = $p->get_bacsi_from_lichxetnghiem($malichxetnghiem);
        
        if ($bacsi_info && $bacsi_info->num_rows > 0) {
            $bacsi = $bacsi_info->fetch_assoc();
            $mabacsi = $bacsi['mabacsi'];
            $tenbacsi = $bacsi['hoten'];
            
            // Tạo nội dung thông báo
            $tieude = "Kết quả xét nghiệm đã có";
            $noidung = "Kết quả xét nghiệm cho lịch #$malichxetnghiem đã được cập nhật. Vui lòng kiểm tra.";
            
            // Lưu thông báo vào database
            $mathongbao = $this->create_thongbao($mabacsi, $tieude, $noidung, 'ketquaxetnghiem', $malichxetnghiem);
            
            if ($mathongbao) {
                // Gửi thông báo real-time qua WebSocket
                $this->send_websocket_notification($mabacsi, $tieude, $noidung, $malichxetnghiem, $mathongbao);
                return true;
            }
        }
        
        return false;
    }
    
    // Gửi thông báo qua WebSocket
    private function send_websocket_notification($manguoidung, $tieude, $noidung, $malichxetnghiem, $mathongbao) {
        // Kết nối đến WebSocket server
        $host = 'localhost';
        $port = 8080;
        
        try {
            // Tạo payload thông báo
            $notification_data = array(
                'command' => 'notification',
                'type' => 'ketquaxetnghiem',
                'receiver' => $manguoidung,
                'title' => $tieude,
                'content' => $noidung,
                'malichxetnghiem' => $malichxetnghiem,
                'mathongbao' => $mathongbao,
                'timestamp' => date('Y-m-d H:i:s')
            );
            
            // Gửi thông báo đến WebSocket server
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($socket !== false) {
                $result = @socket_connect($socket, $host, $port);
                if ($result) {
                    $message = json_encode($notification_data);
                    socket_write($socket, $message, strlen($message));
                    socket_close($socket);
                    return true;
                }
                socket_close($socket);
            }
        } catch (Exception $e) {
            error_log("WebSocket notification error: " . $e->getMessage());
        }
        
        return false;
    }
}
?>
