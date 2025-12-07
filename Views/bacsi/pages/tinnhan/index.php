<?php
if (!isset($_SESSION['user']['tentk'])) {
    header("Location: index.php");
    exit();
}
$tentk = $_SESSION['user']['tentk'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat Bác sĩ – Bệnh nhân</title>
<style>
body { background-color: #f0f2f5; font-family: Arial, sans-serif; }
.chat-layout { display: flex; height: calc(100vh - 100px); box-shadow: 0 0 10px rgba(0,0,0,0.1); }
#userList { width: 300px; background: white; border-right: 1px solid #ddd; overflow-y: auto; }
#userList h3 { background: #2c3e50; color: white; padding: 15px; margin: 0; }
.user { padding: 12px 20px; border-bottom: 1px solid #f0f0f0; cursor: pointer; display: flex; align-items: center; transition: background 0.3s; }
.user:hover { background: #f8f8f8; }
#chatContainer { flex: 1; padding: 20px; display: flex; flex-direction: column; background: white; }
#chatHeader { font-weight: bold; margin-bottom: 10px; }
#chatMessages { flex: 1; overflow-y: auto; background: #e9ebee; padding: 15px; border-radius: 10px; margin-bottom: 10px; }
.message { max-width: 70%; padding: 10px 15px; margin-bottom: 12px; border-radius: 20px; font-size: 15px; line-height: 1.4; clear: both; }
.doctor { background: #d4edda; float: right; border-bottom-left-radius: 0; }
.patient { background: #2c3e50; color: white; float: left; border-bottom-right-radius: 0; }
#messageInput { padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 25px; margin-bottom: 10px; }
#sendButton { background: #2c3e50; color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer; align-self: flex-end; }
#sendButton:disabled { background: #ccc; }
</style>
</head>
<body>

<div class="chat-layout">
    <div id="userList">
        <h3>Bệnh nhân</h3>
        <?php
        include_once("Controllers/ctaikhoan.php");
        include_once("Controllers/cbacsi.php");
        $cbacsi = new cBacSi();
        $bacsi = $cbacsi->getBacSiByTenTK($tentk);
        $ctk = new cTaiKhoan();
        if (is_array($bacsi) && isset($bacsi['mabacsi'])) {
            $mabacsi = $bacsi['mabacsi'];
            $tbl = $ctk->gettkbenhnhan($mabacsi);
            if ($tbl && $tbl->num_rows > 0) {
                while ($row = $tbl->fetch_assoc()) {
                    echo "<div class='user' onclick='selectUser(\"{$row['tentk']}\", \"{$row['hoten']}\")'><span>{$row['hoten']}</span></div>";
                }
            } else echo "<p class='p-3'>Không có bệnh nhân nào.</p>";
        } else echo "<p class='p-3 text-danger'>Không tìm thấy thông tin bác sĩ từ tài khoản đăng nhập.</p>";
        ?>
    </div>

    <div id="chatContainer">
        <div id="chatHeader">Chọn bệnh nhân để trò chuyện</div>
        <div id="chatMessages"></div>
        <textarea id="messageInput" placeholder="Nhập tin nhắn..." disabled></textarea>
        <button id="sendButton" disabled>Gửi</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let socket;
let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 };
let currentPatient = null;
let messages = {};

function connectWebSocket() {
    socket = new WebSocket("wss://hanhphuc.site/ws"); // ✅ Gán vào biến GLOBAL
    
    socket.onopen = () => {
        console.log("✅ WebSocket connected!");
        socket.send(JSON.stringify({ 
            action: 'register', 
            username: user.tentk, 
            role: user.vaitro 
        }));
    };
    
    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);
        console.log("📩 Received:", data);
        
        if (data.command === 'messages') {
            const patientID = data.receiver_tentk;
            messages[patientID] = data.messages;
            if (currentPatient && currentPatient.tentk === patientID) {
                renderMessages(messages[patientID]);
            }
        } else if (data.command === 'receive') {
            if (!messages[data. sender]) messages[data.sender] = [];
            messages[data. sender].push(data);
            if (currentPatient && currentPatient.tentk === data.sender) {
                displayMessage(data);
            }
        }
    };
    
    socket. onerror = (error) => {
        console.error("❌ WebSocket error:", error);
    };
    
    socket.onclose = (event) => {
        console. warn("⚠️ WebSocket closed:", event. reason);
        setTimeout(connectWebSocket, 3000);
    };
}

function selectUser(tentk, name) {
    // Kiểm tra WebSocket trước khi xử lý
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        console.warn("⏳ WebSocket chưa sẵn sàng.  Đang chờ...");
        setTimeout(() => selectUser(tentk, name), 1000);
        return;
    }
    
    currentPatient = { tentk, name };
    $('#chatHeader').text('Đang trò chuyện với bệnh nhân ' + name);
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#chatMessages').html('');

    if (!messages[tentk]) messages[tentk] = [];
    renderMessages(messages[tentk]);

    console.log("📤 Requesting messages for:", tentk);
    socket.send(JSON.stringify({
        command: "load_messages",
        tentk: user.tentk,
        receiver_tentk: tentk
    }));
}

function renderMessages(msgArray) {
    $('#chatMessages'). html('');
    msgArray. forEach(m => displayMessage(m));
}

function displayMessage(msg) {
    const msgDiv = $('<div class="message"></div>');
    msgDiv. text(msg.message);
    msgDiv.addClass(msg.sender === user.tentk ? 'doctor' : 'patient');
    $('#chatMessages').append(msgDiv);
    $('#chatMessages'). scrollTop($('#chatMessages')[0].scrollHeight);
}

$('#sendButton').click(() => {
    const text = $('#messageInput').val().trim();
    if (!text || !currentPatient) return;

    if (! socket || socket.readyState !== WebSocket.OPEN) {
        alert("❌ Kết nối WebSocket bị gián đoạn. Vui lòng thử lại!");
        return;
    }

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
});

// Khởi tạo kết nối khi trang load
connectWebSocket();
</script>
</body>
</html>
