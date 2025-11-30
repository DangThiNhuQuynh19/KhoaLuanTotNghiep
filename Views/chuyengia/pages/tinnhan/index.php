<?php
if (!isset($_SESSION['user']['tentk']) || $_SESSION['user']['mavaitro'] != 3) {
    header("Location: index.php");
    exit();
}
$tentk = $_SESSION['user']['tentk'];

// Load environment configuration for client-side use
require_once(__DIR__ . '/../../../../env.php');
$wsEnabled = isWebSocketEnabled() ? 'true' : 'false';
$wsUrl = getWebSocketUrl();
$pollingInterval = config('polling.interval', 3000);
?>
<style>
    body { background-color: #f0f2f5; font-family: Arial, sans-serif; }
    .chat-layout { display: flex; height: calc(100vh - 100px); box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    #userList {
        width: 300px;
        background: white;
        border-right: 1px solid #ddd;
        overflow-y: auto;
    }
    #userList h3 {
        background: #2c3e50;
        color: white;
        padding: 15px;
        margin: 0;
    }
    .user {
        padding: 12px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: background 0.3s;
    }
    .user:hover { background: #f8f8f8; }
    .user img {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        object-fit: cover;
        margin-right: 10px;
    }
    #chatContainer {
        flex: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
        background: white;
    }
    #chatHeader { font-weight: bold; margin-bottom: 10px; }
    #connectionStatus { font-size: 12px; color: #666; margin-bottom: 5px; }
    #connectionStatus.connected { color: #28a745; }
    #connectionStatus.polling { color: #ffc107; }
    #connectionStatus.disconnected { color: #dc3545; }
    #chatMessages {
        flex: 1;
        overflow-y: auto;
        background: #e9ebee;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    .message {
        max-width: 70%;
        padding: 10px 15px;
        margin-bottom: 12px;
        border-radius: 20px;
        font-size: 15px;
        line-height: 1.4;
        clear: both;
    }
    .doctor {
        background: #d4edda;
        float: right;
        border-bottom-left-radius: 0;
    }
    .patient {
        background: #2c3e50;
        color: white;
        float: left;
        border-bottom-right-radius: 0;
    }
    #messageInput {
        padding: 10px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 25px;
        margin-bottom: 10px;
    }
    #sendButton {
        background: #2c3e50;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        cursor: pointer;
        align-self: flex-end;
    }
    #sendButton:disabled {
        background: #ccc;
    }
</style>
<div class="chat-layout">
    <div id="userList">
        <h3>Bệnh nhân</h3>
        <?php
        include_once("Controllers/ctaikhoan.php");
        include_once("Controllers/cchuyengia.php");
        $p = new cChuyenGia();

        // Lấy tentk từ session
        $tentk = $_SESSION['user']['tentk'];

        // Gọi hàm để lấy thông tin bác sĩ
        $chuyengia = $p->getChuyenGiaByTenTK($tentk);
        $p = new ctaiKhoan();
        if (is_array($chuyengia) && isset($chuyengia['machuyengia'])) {
            $machuyengia = $chuyengia['machuyengia']; 
            $tbl = $p->gettkbenhnhan($machuyengia);

            if ($tbl && $tbl->num_rows > 0) {
                while ($row = $tbl->fetch_assoc()) {
                    echo "<div class='user' onclick='selectUser(\"{$row['tentk']}\", \"{$row['hoten']}\")'>
                            <span>{$row['hoten']}</span>
                        </div>";
                }
            } else {
                echo "<p class='p-3'>Không có bệnh nhân nào.</p>";
            }
        } else {
            echo "<p class='p-3 text-danger'>Không tìm thấy thông tin bác sĩ từ tài khoản đăng nhập.</p>";
        }
    ?>

    </div>
    <div id="chatContainer">
        <div id="chatHeader">Chọn bệnh nhân để trò chuyện</div>
        <div id="connectionStatus" class="disconnected">Đang kết nối...</div>
        <div id="chatMessages"></div>
        <textarea id="messageInput" placeholder="Nhập tin nhắn..." disabled></textarea>
        <button id="sendButton" disabled>Gửi</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Configuration from server
const CONFIG = {
    websocket: {
        enabled: <?php echo $wsEnabled; ?>,
        url: '<?php echo $wsUrl; ?>'
    },
    polling: {
        interval: <?php echo $pollingInterval; ?>
    },
    apiUrl: 'Ajax/chat_api.php'
};

let socket;
let useWebSocket = CONFIG.websocket.enabled;
let pollingTimer = null;
let lastTimestamp = null;
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;

let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 };
let currentPatient = null;
let messages = {};

// Update connection status indicator
function updateConnectionStatus(status, message) {
    const statusEl = $('#connectionStatus');
    statusEl.removeClass('connected polling disconnected').addClass(status);
    statusEl.text(message);
}

function connectWebSocket() {
    if (!CONFIG.websocket.enabled) {
        fallbackToPolling();
        return;
    }

    try {
        socket = new WebSocket(CONFIG.websocket.url);
        
        socket.onopen = () => {
            console.log("WebSocket connected!");
            reconnectAttempts = 0;
            updateConnectionStatus('connected', '✅ Kết nối WebSocket');
            socket.send(JSON.stringify({ action: 'register', username: user.tentk, role: user.vaitro }));
        };
        
        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            handleServerMessage(data);
        };
        
        socket.onclose = () => {
            console.warn("WebSocket closed");
            updateConnectionStatus('disconnected', '⚠️ Kết nối đã đóng');
            
            if (reconnectAttempts < maxReconnectAttempts) {
                reconnectAttempts++;
                setTimeout(connectWebSocket, 3000);
            } else {
                fallbackToPolling();
            }
        };

        socket.onerror = (error) => {
            console.error("WebSocket error:", error);
        };
    } catch (error) {
        console.error("Failed to create WebSocket:", error);
        fallbackToPolling();
    }
}

// Fallback to AJAX polling
function fallbackToPolling() {
    useWebSocket = false;
    updateConnectionStatus('polling', '📡 Sử dụng AJAX polling');
    startPolling();
}

// Start AJAX polling
function startPolling() {
    if (pollingTimer) clearInterval(pollingTimer);
    
    pollingTimer = setInterval(() => {
        if (currentPatient) {
            pollNewMessages();
        }
    }, CONFIG.polling.interval);
}

// Poll for new messages
function pollNewMessages() {
    if (!currentPatient) return;
    
    $.ajax({
        url: CONFIG.apiUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            action: 'check_new_messages',
            partner: currentPatient.tentk,
            last_timestamp: lastTimestamp || ''
        },
        success: function(data) {
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    if (msg.sender !== user.tentk) {
                        if (!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
                        messages[currentPatient.tentk].push(msg);
                        displayMessage(msg);
                    }
                });
                lastTimestamp = data.timestamp;
            }
        },
        error: function(err) {
            console.error("Polling error:", err);
        }
    });
}

// Handle messages from server
function handleServerMessage(data) {
    if (data.command === 'messages') {
        const patientID = data.receiver_tentk;
        messages[patientID] = data.messages;
        if (currentPatient && currentPatient.tentk === patientID) {
            renderMessages(messages[patientID]);
        }
        if (data.messages && data.messages.length > 0) {
            lastTimestamp = data.messages[data.messages.length - 1].time;
        }
    } else if (data.command === 'receive') {
        if (!messages[data.sender]) messages[data.sender] = [];
        messages[data.sender].push(data);
        if (currentPatient && currentPatient.tentk === data.sender) {
            displayMessage(data);
        }
    }
}

function selectUser(tentk, name) {
    currentPatient = { tentk, name };
    lastTimestamp = null;
    $('#chatHeader').text('Đang trò chuyện với bệnh nhân ' + name);
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#chatMessages').html('<p style="text-align:center;color:#777;">Đang tải tin nhắn...</p>');

    if (!messages[tentk]) messages[tentk] = [];

    if (useWebSocket && socket && socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            command: "load_messages",
            tentk: user.tentk,
            receiver_tentk: tentk
        }));
    } else {
        loadMessagesViaAjax(tentk);
    }
}

// Load messages via AJAX
function loadMessagesViaAjax(partner) {
    $.ajax({
        url: CONFIG.apiUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            action: 'load_messages',
            partner: partner
        },
        success: function(data) {
            if (data.success) {
                messages[partner] = data.messages;
                renderMessages(data.messages);
                if (data.messages && data.messages.length > 0) {
                    lastTimestamp = data.messages[data.messages.length - 1].time;
                }
            } else {
                $('#chatMessages').html('<p style="text-align:center;color:red;">' + (data.error || 'Không thể tải tin nhắn') + '</p>');
            }
        },
        error: function() {
            $('#chatMessages').html('<p style="text-align:center;color:red;">Lỗi kết nối server</p>');
        }
    });
}

function renderMessages(msgArray) {
    $('#chatMessages').html('');
    if (msgArray && msgArray.length > 0) {
        msgArray.forEach(m => displayMessage(m));
    } else {
        $('#chatMessages').html('<p style="text-align:center;color:#777;">Chưa có tin nhắn</p>');
    }
}

function displayMessage(msg) {
    const msgDiv = $('<div class="message"></div>');
    msgDiv.text(msg.message);
    msgDiv.addClass(msg.sender === user.tentk ? 'doctor' : 'patient');
    $('#chatMessages').append(msgDiv);
    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
}

$('#sendButton').click(() => {
    const text = $('#messageInput').val().trim();
    if (!text || !currentPatient) return;

    if (useWebSocket && socket && socket.readyState === WebSocket.OPEN) {
        const msg = {
            command: 'send',
            sender: user.tentk,
            receiver: currentPatient.tentk,
            message: text
        };
        socket.send(JSON.stringify(msg));
        if (!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
        messages[currentPatient.tentk].push(msg);
        displayMessage(msg);
        $('#messageInput').val('');
    } else {
        // Send via AJAX
        $.ajax({
            url: CONFIG.apiUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'send_message',
                receiver: currentPatient.tentk,
                message: text
            },
            success: function(data) {
                if (data.success) {
                    if (!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
                    messages[currentPatient.tentk].push({
                        sender: user.tentk,
                        message: text,
                        time: data.time
                    });
                    displayMessage({sender: user.tentk, message: text});
                    $('#messageInput').val('');
                } else {
                    alert("Gửi tin nhắn thất bại: " + data.error);
                }
            },
            error: function() {
                alert("Lỗi kết nối server");
            }
        });
    }
});

// Start connection
$(document).ready(function(){
    if (CONFIG.websocket.enabled) {
        connectWebSocket();
    } else {
        fallbackToPolling();
    }
});
</script>