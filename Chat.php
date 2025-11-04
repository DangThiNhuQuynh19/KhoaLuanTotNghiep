<?php
require_once('vendor/autoload.php');
require_once('Models/ChatUserModel.php');
include_once('Assets/config.php');
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
date_default_timezone_set('Asia/Ho_Chi_Minh'); 
class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $userConnections = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection: {$conn->resourceId}\n";
        $this->clients->attach($conn);
    }

    public function onClose(ConnectionInterface $conn) {
        echo "Connection closed: {$conn->resourceId}\n";
        $this->clients->detach($conn);
        if (isset($conn->tentk)) {
            unset($this->userConnections[$conn->tentk]);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $sender = $data['sender'] ?? null;
        $receiver = $data['receiver'] ?? null;
        
        if (!$data) {
            echo "⚠️ json_decode failed. Raw message: $msg\n";
            $from->send(json_encode(['status' => 'error', 'message' => 'Invalid JSON format.'])); 
            return;
        }
    
        // Kiểm tra xem có trường command hay action không
        $command = $data['command'] ?? $data['action'] ?? null;
        if (!$command) {
            echo "⚠️ Missing command/action in the message.\n";
            $from->send(json_encode(['status' => 'error', 'message' => 'Missing command/action.'])); 
            return;
        }

        // Xử lý lệnh "register" để đăng ký kết nối với tentk
        if ($command === 'register') {
            $username = $data['username'] ?? null;
            if ($username) {
                $from->tentk = $username;
                $this->userConnections[$username] = $from;
                echo "User registered: {$username}\n";
                echo "Current connected users: " . implode(", ", array_keys($this->userConnections)) . "\n";
                $from->send(json_encode(['status' => 'success', 'message' => 'Registered successfully.']));
            } else {
                $from->send(json_encode(['status' => 'error', 'message' => 'Missing username for registration.']));
            }
            return;
        }
        
        // Lệnh "send" - Gửi tin nhắn
        if ($command === 'send') {
            $sender = $data['sender'] ?? null;
            $receiver = $data['receiver'] ?? null;
            $message = $data['message'] ?? null;

            if (!$sender || !$receiver || !$message) {
                $from->send(json_encode(['status' => 'error', 'message' => 'Missing required fields.']));
                return;
            }

            // Log thông tin gửi tin nhắn
            echo "Sending message from {$sender} to {$receiver}: {$message}\n";

            // Lưu tin nhắn vào DB
            $chat = new ChatUserModel();
            $chat->setSender($sender);
            $chat->setReceiver($receiver);
            $chat->setMessage($message);
            $messageId = $chat->saveMessage();

            if ($messageId) {
                // Gửi tin nhắn cho người nhận nếu họ đang online
                if (isset($this->userConnections[$receiver])) {
                    $this->userConnections[$receiver]->send(json_encode([
                        'command' => 'receive',
                        'sender' => $sender,
                        'message' => $message,
                        'time' => date('Y-m-d H:i:s'),
                        'messageId' => $messageId
                    ]));
                }

                // Gửi lại xác nhận cho người gửi
                $from->send(json_encode([
                    'command' => 'sent',
                    'receiver' => $receiver,
                    'message' => $message,
                    'time' => date('Y-m-d H:i:s'),
                    'messageId' => $messageId
                ]));
            } else {
                $from->send(json_encode(['status' => 'error', 'message' => 'Failed to save message.']));
            }
            return;
        }
        if ($command === 'send_file') {
            $sender = $data['sender'] ?? null;
            $receiver = $data['receiver'] ?? null;
            $filename = $data['filename'] ?? null;
            $fileData = $data['data'] ?? null;
        
            if (!$sender || !$receiver || !$fileData || !$filename) {
                $from->send(json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu file.']));
                return;
            }
        
            // Tạo thư mục uploads nếu chưa có
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        
            // Lưu file PDF
            $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
            $filePath = $uploadDir . $safeName;
            $fileUrl = "http://localhost/KLTN/uploads/" . $filename;
        
            file_put_contents($filePath, base64_decode($fileData));
        
            // Lưu vào DB (tùy bạn, có thể lưu như 1 tin nhắn kiểu file)
            $chat = new ChatUserModel();
            $chat->setSender($sender);
            $chat->setReceiver($receiver);
            $chat->setMessage("[FILE] $fileUrl");
            $chat->saveMessage();
        
            // Gửi thông báo file cho người nhận
            if (isset($this->userConnections[$receiver])) {
                $this->userConnections[$receiver]->send(json_encode([
                    'command' => 'receive_file',
                    'sender' => $sender,
                    'filename' => $filename,
                    'url' => $fileUrl
                ]));
            }
        
            // Phản hồi cho người gửi
            $from->send(json_encode([
                'command' => 'file_sent',
                'filename' => $filename,
                'url' => $fileUrl
            ]));
            return;
        }
        

        // Lệnh "load_messages" - Lấy tin nhắn cũ
        // Lệnh "load_messages" - Lấy tin nhắn cũ
        if ($command === 'load_messages') {
            $sender = $data['tentk'] ?? null;
            $receiver = $data['receiver_tentk'] ?? null;

            if (!$sender || !$receiver) {
                $from->send(json_encode(['status' => 'error', 'message' => 'Missing tentk or receiver_tentk.']));
                return;
            }
            $chat = new ChatUserModel();
            $messages = $chat->getMessages($sender, $receiver);

            if ($messages === false) {
                $from->send(json_encode(['status' => 'error', 'message' => 'Không thể lấy tin nhắn.']));
                return;
            }

            $from->send(json_encode([
                'command' => 'messages',
                'messages' => $messages,
                'receiver_tentk' => $receiver
            ]));
            return;
        }


    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}