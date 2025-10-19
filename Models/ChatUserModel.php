<?php

class ChatUserModel {
    private $matinnhan;
    private $tentk_gui;
    private $tentk_nhan;
    private $noidung;
    private $thoigiangui;
    private $connect;

    public function __construct() {
        require_once(__DIR__ . '/ketnoi.php');
        $db = new clsketnoi();
        $this->connect = $db->moKetNoi();
    }

    public function setSender($tentk_gui) { $this->tentk_gui = $tentk_gui; }
    public function setReceiver($tentk_nhan) { $this->tentk_nhan = $tentk_nhan; }
    public function setMessage($noidung) { $this->noidung = $noidung; }
    public function setIdMessage($matinnhan) { $this->matinnhan = $matinnhan; }

    // Lưu tin nhắn mới
    public function saveMessage() {
        $this->thoigiangui = date('Y-m-d H:i:s');
        $stmt = $this->connect->prepare("INSERT INTO tinnhan (tentk_gui, tentk_nhan, noidung, thoigiangui) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("ssss", $this->tentk_gui, $this->tentk_nhan, $this->noidung, $this->thoigiangui);
        if ($stmt->execute()) return $stmt->insert_id;
        return false;
    }

    // Lấy thời gian của tin nhắn theo ID
    public function getMessageTime($matinnhan) {
        $stmt = $this->connect->prepare("SELECT thoigiangui FROM tinnhan WHERE matinnhan = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $matinnhan);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['thoigiangui'];
        }
        return false;
    }

    // Lấy lịch sử tin nhắn giữa 2 người
    public function getMessages($user1, $user2) {
        $stmt = $this->connect->prepare("
            SELECT * FROM tinnhan
            WHERE (tentk_gui = ? AND tentk_nhan = ?) OR (tentk_gui = ? AND tentk_nhan = ?)
            ORDER BY thoigiangui ASC
        ");
        if (!$stmt) {
            error_log("SQL prepare failed: " . $this->connect->error);
            return false;
        }

        $stmt->bind_param("ssss", $user1, $user2, $user2, $user1);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                'sender' => $row['tentk_gui'],
                'receiver' => $row['tentk_nhan'],
                'message' => $row['noidung'],
                'time' => $row['thoigiangui'],   // time từ DB
                'messageId' => $row['matinnhan']
            ];
        }
        return $messages;
    }

    public function close() { $this->connect->close(); }
}
