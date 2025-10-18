<?php
session_start();

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['tentk'])) {
    echo "<p>Bạn chưa đăng nhập. <a href='dangnhap.php'>Đăng nhập</a></p>";
    exit;
}

$tentk = $_SESSION['user']['tentk'];
$vaitro = $_SESSION['user']['vaitro']; // 0 = bác sĩ, 1 = bệnh nhân
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>💬 Chat Real-time</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        #chat-box {
            height: 400px;
            overflow-y: auto;
            background: #fff;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .message { margin-bottom: 10px; display: flex; }
        .message.sent { justify-content: flex-end; }
        .message.received { justify-content: flex-start; }
        .message .bubble {
            padding: 8px 12px;
            border-radius: 15px;
            max-width: 60%;
            word-wrap: break-word;
        }
        .message.sent .bubble { background: #0d6efd; color: white; }
        .message.received .bubble { background: #e9ecef; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h3 class="mb-3 text-center">💬 Chat Real-time</h3>

    <!-- Nhập người nhận -->
    <div class="mb-3">
        <label for="receiver" class="form-label">Người nhận:</label>
        <input type="text" id="receiver" class="form-control" placeholder="Nhập tên tài khoản người nhận...">
    </div>

    <!-- Khu vực hiển thị tin nhắn -->
    <div id="chat-box"></div>

    <!-- Nhập và gửi tin -->
    <div class="input-group mt-3">
        <input type="text" id="messageInput" class="form-control" placeholder="Nhập tin nhắn..." disabled>
        <button class="btn btn-primary" id="sendBtn" disabled>Gửi</button>
    </div>
</div>

<script>
const tentk = "<?php echo $tentk; ?>";
const ws = new WebSocket("ws://localhost:8080");
const receiverInput = document.getElementById('receiver');
let currentReceiver = null;

// ✅ Lưu lịch sử chat tạm trên client
let chatHistory = {};

// ✅ Khôi phục lịch sử chat từ localStorage khi tải trang
window.addEventListener('load', () => {
    const savedHistory = localStorage.getItem('chat_history');
    if (savedHistory) chatHistory = JSON.parse(savedHistory);

    const savedReceiver = localStorage.getItem('chat_receiver');
    if (savedReceiver) {
        currentReceiver = savedReceiver;
        receiverInput.value = savedReceiver;
        renderChatHistory(savedReceiver);
        checkAppointmentAndLoad(savedReceiver);
    }
});

// ✅ Lưu lịch sử chat vào localStorage trước khi rời trang
window.addEventListener('beforeunload', () => {
    localStorage.setItem('chat_history', JSON.stringify(chatHistory));
});

// ✅ Khi nhấn Enter trong ô người nhận → đổi người nhận thực sự
receiverInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        const newReceiver = receiverInput.value.trim();
        if (newReceiver && newReceiver !== currentReceiver) {
            currentReceiver = newReceiver;
            localStorage.setItem('chat_receiver', newReceiver);
            renderChatHistory(newReceiver);
            checkAppointmentAndLoad(newReceiver);
        }
    }
});

// ✅ Khi blur: KHÔNG xóa lịch sử, chỉ khôi phục lại tên cũ nếu chưa đổi
receiverInput.addEventListener('blur', () => {
    const typedValue = receiverInput.value.trim();
    if (typedValue === '' && currentReceiver) {
        receiverInput.value = currentReceiver;
    } else if (typedValue !== currentReceiver) {
        receiverInput.value = currentReceiver;
    }

    // Render lại lịch sử hiện tại
    if (currentReceiver) renderChatHistory(currentReceiver);
});

// ✅ Kết nối WebSocket
ws.onopen = () => {
    console.log("✅ Đã kết nối WebSocket");
    ws.send(JSON.stringify({ command: 'register', username: tentk }));

    if (currentReceiver) checkAppointmentAndLoad(currentReceiver);
};

// ✅ Nhận tin nhắn từ WebSocket
ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log("📩 Nhận:", data);

    if (data.command === 'receive') {
        if (!chatHistory[data.sender]) chatHistory[data.sender] = [];
        chatHistory[data.sender].push({ sender: data.sender, message: data.message });
        localStorage.setItem('chat_history', JSON.stringify(chatHistory));
        if (currentReceiver === data.sender) appendMessage(data.sender, data.message, 'received');
    }

    if (data.command === 'sent') {
        if (!chatHistory[data.receiver]) chatHistory[data.receiver] = [];
        chatHistory[data.receiver].push({ sender: tentk, message: data.message });
        localStorage.setItem('chat_history', JSON.stringify(chatHistory));
        if (currentReceiver === data.receiver) appendMessage(tentk, data.message, 'sent');
    }

    if (data.command === 'messages') {
        chatHistory[data.partner] = data.messages;
        localStorage.setItem('chat_history', JSON.stringify(chatHistory));
        if (currentReceiver === data.partner) renderChatHistory(data.partner);
    }
};

ws.onclose = () => console.log("❌ WebSocket đã đóng");
ws.onerror = (err) => console.error("⚠️ Lỗi WebSocket:", err);

// ✅ Gửi tin nhắn
document.getElementById('sendBtn').addEventListener('click', sendMessage);
document.getElementById('messageInput').addEventListener('keypress', (e) => { if (e.key==='Enter') sendMessage(); });

function sendMessage() {
    const receiver = receiverInput.value.trim();
    const message = document.getElementById('messageInput').value.trim();
    if (!receiver || !message) { alert("Nhập người nhận và tin nhắn!"); return; }

    ws.send(JSON.stringify({ command: 'send', sender: tentk, receiver: receiver, message: message }));
    document.getElementById('messageInput').value = '';
}

function appendMessage(sender, message, type) {
    const chatBox = document.getElementById('chat-box');
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('message', type);

    const bubble = document.createElement('div');
    bubble.classList.add('bubble');
    bubble.textContent = message;

    msgDiv.appendChild(bubble);
    chatBox.appendChild(msgDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function renderChatHistory(receiver) {
    const chatBox = document.getElementById('chat-box');
    chatBox.innerHTML = '';
    if (!chatHistory[receiver]) return;
    chatHistory[receiver].forEach(msg => {
        appendMessage(msg.sender, msg.message, msg.sender === tentk ? 'sent' : 'received');
    });
}

function loadChatHistory(receiver) {
    ws.send(JSON.stringify({ command: 'load_messages', tentk: tentk, receiver_tentk: receiver }));
}

// ✅ Kiểm tra giờ hẹn trước khi cho chat
function checkAppointmentAndLoad(receiver) {
    fetch(`kiemtragiohen.php?receiver=${encodeURIComponent(receiver)}`)
        .then(res => res.json())
        .then(data => {
            const input = document.getElementById('messageInput');
            const btn = document.getElementById('sendBtn');
            if (data.status === "ok") {
                input.disabled = false; btn.disabled = false;
                loadChatHistory(receiver);
            } else {
                input.disabled = true; btn.disabled = true;
                alert(data.message || "Chưa đến giờ hẹn, không thể nhắn tin.");
            }
        })
        .catch(err => console.error("Lỗi kiểm tra giờ hẹn:", err));
}
</script>

</body>
</html>
