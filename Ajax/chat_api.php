<?php
/**
 * AJAX Polling API for Messaging
 * 
 * This provides a fallback for real-time messaging when WebSocket
 * is unavailable or disabled.
 * 
 * Supported actions:
 * - load_messages: Load conversation between two users
 * - send_message: Send a new message
 * - check_new_messages: Check for new messages since a timestamp
 */

session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load environment configuration
require_once(__DIR__ . '/../env.php');
require_once(__DIR__ . '/../Models/ChatUserModel.php');

// Check authentication
if (!isset($_SESSION['user']['tentk'])) {
    echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập']);
    exit;
}

$currentUser = $_SESSION['user']['tentk'];
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'load_messages':
        loadMessages($currentUser);
        break;
    
    case 'send_message':
        sendMessage($currentUser);
        break;
    
    case 'check_new_messages':
        checkNewMessages($currentUser);
        break;
    
    case 'get_config':
        getConfig();
        break;
    
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

/**
 * Load messages between current user and partner
 */
function loadMessages($currentUser) {
    $partner = $_REQUEST['partner'] ?? '';
    
    if (empty($partner)) {
        echo json_encode(['success' => false, 'error' => 'Missing partner']);
        return;
    }
    
    $chat = new ChatUserModel();
    $messages = $chat->getMessages($currentUser, $partner);
    
    if ($messages === false) {
        echo json_encode(['success' => false, 'error' => 'Không thể lấy tin nhắn']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'partner' => $partner,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Send a new message
 */
function sendMessage($currentUser) {
    $receiver = $_REQUEST['receiver'] ?? '';
    $message = $_REQUEST['message'] ?? '';
    
    if (empty($receiver) || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Missing receiver or message']);
        return;
    }
    
    $chat = new ChatUserModel();
    $chat->setSender($currentUser);
    $chat->setReceiver($receiver);
    $chat->setMessage($message);
    $messageId = $chat->saveMessage();
    
    if ($messageId) {
        echo json_encode([
            'success' => true,
            'messageId' => $messageId,
            'time' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save message']);
    }
}

/**
 * Check for new messages since a given timestamp
 */
function checkNewMessages($currentUser) {
    $partner = $_REQUEST['partner'] ?? '';
    $lastTimestamp = $_REQUEST['last_timestamp'] ?? '';
    
    if (empty($partner)) {
        echo json_encode(['success' => false, 'error' => 'Missing partner']);
        return;
    }
    
    $chat = new ChatUserModel();
    $messages = $chat->getNewMessages($currentUser, $partner, $lastTimestamp);
    
    if ($messages === false) {
        echo json_encode(['success' => false, 'error' => 'Không thể kiểm tra tin nhắn mới']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Get client configuration (WebSocket URL, polling settings, etc.)
 */
function getConfig() {
    echo json_encode([
        'success' => true,
        'websocket' => [
            'enabled' => isWebSocketEnabled(),
            'url' => getWebSocketUrl()
        ],
        'polling' => [
            'interval' => config('polling.interval', 3000)
        ],
        'baseUrl' => getBaseUrl()
    ]);
}
