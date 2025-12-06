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
        <input type="file" id="fileInput" accept="application/pdf" style="margin-bottom: 10px;">
        <button id="sendButton" disabled>Gửi</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let socket;
let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 };
let currentPatient = null;
let messages = {}; // lưu tin nhắn theo bệnh nhân

// Kết nối WebSocket
function connectWebSocket() {
    socket = new WebSocket('ws://localhost:8080');
    socket.onopen = () => {
        console.log("WebSocket connected!");
        socket.send(JSON.stringify({action:'register', username:user.tentk, role:user.vaitro}));
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);

        // Khi server trả tất cả tin nhắn từ DB
        if(data.command === 'messages'){
            messages[data.receiver_tentk] = data.messages;
            if(currentPatient && currentPatient.tentk === data.receiver_tentk){
                renderMessages(messages[data.receiver_tentk]);
            }
        }

        // Khi nhận tin nhắn mới
        if(data.command === 'receive' || data.command === 'receive_file'){
            const sender = data.sender;
            if(!messages[sender]) messages[sender] = [];
            messages[sender].push(data);
            if(currentPatient && currentPatient.tentk === sender){
                data.command === 'receive' ? displayMessage(data) : displayFileMessage(data);
            }
        }

        // Xác nhận file gửi thành công
        if(data.command === 'file_sent'){
            $('#chatMessages .message').last().html(`<a href="${data.url}" target="_blank" download>📄 ${data.filename}</a>`);
        }
    };

    socket.onclose = () => { setTimeout(connectWebSocket, 3000); };
}

// Chọn bệnh nhân để chat
function selectUser(tentk, name){
    currentPatient = {tentk, name};
    $('#chatHeader').text('Đang trò chuyện với bệnh nhân ' + name);
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#chatMessages').html('');

    if(!messages[tentk]) messages[tentk] = [];

    // Gửi lệnh load messages từ DB
    if(socket && socket.readyState === WebSocket.OPEN){
        socket.send(JSON.stringify({command:'load_messages', tentk:user.tentk, receiver_tentk:tentk}));
    }

    renderMessages(messages[tentk]);
}

// Render tất cả tin nhắn (text + file)
function renderMessages(msgArray){
    $('#chatMessages').html('');
    msgArray.forEach(m => {
        if(m.message && m.message.startsWith('[FILE]')){
            displayFileMessage({sender:m.sender, filename:m.message.split('/').pop(), url:m.message.replace('[FILE]','').trim()});
        } else {
            displayMessage(m);
        }
    });
}

// Hiển thị tin nhắn text
function displayMessage(msg){
    const msgDiv = $('<div class="message"></div>');
    msgDiv.addClass(msg.sender === user.tentk || msg.self ? 'doctor' : 'patient');
    msgDiv.text(msg.message || '');
    $('#chatMessages').append(msgDiv);
    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
}

// Hiển thị tin nhắn file
function displayFileMessage(msg){
    const msgDiv = $('<div class="message"></div>');
    msgDiv.addClass(msg.sender === user.tentk || msg.self ? 'doctor' : 'patient');
    const fileLink = msg.url ? `<a href="${msg.url}" target="_blank" download>📄 ${msg.filename}</a>` : `📄 ${msg.filename} (đang tải lên...)`;
    msgDiv.html(fileLink);
    $('#chatMessages').append(msgDiv);
    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
}

// Gửi tin nhắn text
$('#sendButton').click(() => {
    const text = $('#messageInput').val().trim();
    if(!text || !currentPatient) return;
    const msg = {command:'send', sender:user.tentk, receiver:currentPatient.tentk, message:text};
    if(socket && socket.readyState === WebSocket.OPEN){
        socket.send(JSON.stringify(msg));
        if(!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
        messages[currentPatient.tentk].push(msg);
        displayMessage(msg);
        $('#messageInput').val('');
    }
});

// Upload file PDF và gửi WebSocket
$('#fileInput').on('change', function(){
    const file = this.files[0];
    if(!file || file.type !== 'application/pdf'){ alert("Chỉ chọn file PDF!"); return; }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('receiver', currentPatient.tentk); // thêm dòng này

    $.ajax({
        url: 'Views/bacsi/pages/tinnhan/upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(data){
            if(data.success){
                const msg = {
                    command:'send_file',
                    sender:user.tentk,
                    receiver:currentPatient.tentk,
                    filename:data.filename,
                    url:data.url
                };
                socket.send(JSON.stringify(msg));
                displayFileMessage({...msg, self:true});
                $('#fileInput').val('');
            } else alert(data.error);
        },
        error: function(xhr){ alert("Upload thất bại: "+xhr.responseText); }
    });
});


connectWebSocket();

</script>
</body>
</html>
