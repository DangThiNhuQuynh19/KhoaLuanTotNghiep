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
            $tentk = $bacsi['tentk']; // Use tentk for WebSocket registration
            
            // Tạo nội dung thông báo
            $tieude = "Kết quả xét nghiệm đã có";
            $noidung = "Kết quả xét nghiệm cho lịch #$malichxetnghiem đã được cập nhật. Vui lòng kiểm tra.";
            
            // Lưu thông báo vào database
            $mathongbao = $this->create_thongbao($mabacsi, $tieude, $noidung, 'ketquaxetnghiem', $malichxetnghiem);
            
            if ($mathongbao) {
                // Gửi thông báo real-time qua WebSocket
                $this->send_websocket_notification($tentk, $tieude, $noidung, $malichxetnghiem, $mathongbao);
                return true;
            }
        }
        
        return false;
    }
    
    // Gửi thông báo qua WebSocket
    private function send_websocket_notification($tentk, $tieude, $noidung, $malichxetnghiem, $mathongbao) {
        // Create notification payload
        $notification_data = array(
            'command' => 'notification',
            'type' => 'ketquaxetnghiem',
            'receiver' => $tentk,
            'title' => $tieude,
            'content' => $noidung,
            'malichxetnghiem' => $malichxetnghiem,
            'mathongbao' => $mathongbao,
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        // Send notification to WebSocket server via HTTP
        // The WebSocket server should be listening on the same server
        $host = 'localhost';
        $port = 8080;
        
        try {
            // Create a WebSocket client connection
            $context = stream_context_create();
            $socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 2, STREAM_CLIENT_CONNECT, $context);
            
            if ($socket) {
                // Perform WebSocket handshake
                $key = base64_encode(random_bytes(16));
                $handshake = "GET / HTTP/1.1\r\n" .
                            "Host: {$host}:{$port}\r\n" .
                            "Upgrade: websocket\r\n" .
                            "Connection: Upgrade\r\n" .
                            "Sec-WebSocket-Key: {$key}\r\n" .
                            "Sec-WebSocket-Version: 13\r\n\r\n";
                
                fwrite($socket, $handshake);
                
                // Read handshake response with timeout
                $response = '';
                $maxIterations = 100; // Prevent infinite loop
                $iteration = 0;
                while ($line = fgets($socket)) {
                    $response .= $line;
                    if (trim($line) === '') break;
                    if (++$iteration > $maxIterations) {
                        fclose($socket);
                        error_log("❌ WebSocket handshake timeout");
                        return false;
                    }
                }
                
                // Send notification message
                $message = json_encode($notification_data);
                $messageLength = strlen($message);
                
                // WebSocket frame format for text message
                $frame = chr(0x81); // FIN + text frame
                if ($messageLength < 126) {
                    $frame .= chr($messageLength | 0x80); // Mask bit set
                } else if ($messageLength < 65536) {
                    $frame .= chr(126 | 0x80) . pack('n', $messageLength);
                } else {
                    $frame .= chr(127 | 0x80) . pack('J', $messageLength);
                }
                
                // Add masking key and masked payload
                $maskingKey = random_bytes(4);
                $frame .= $maskingKey;
                for ($i = 0; $i < $messageLength; $i++) {
                    $frame .= $message[$i] ^ $maskingKey[$i % 4];
                }
                
                fwrite($socket, $frame);
                fclose($socket);
                
                error_log("✅ WebSocket notification sent to {$tentk}");
                return true;
            } else {
                error_log("❌ Failed to connect to WebSocket server: {$errstr}");
            }
        } catch (Exception $e) {
            error_log("❌ WebSocket notification error: " . $e->getMessage());
        }
        
        return false;
    }
}
?>