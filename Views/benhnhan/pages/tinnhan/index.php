<?php
if (!isset($_SESSION['user']['tentk'])) {
    header("Location: dangnhap.php");
    exit();
}
$tentk = $_SESSION['user']['tentk'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trò chuyện với Bác sĩ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <style>
        body {
            background-color: #f0f2f5;
            padding-top: 90px;
            font-family: Arial, sans-serif;
        }
        .chat-layout {
            display: flex;
            height: calc(100vh - 100px);
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #userList {
            width: 300px;
            background: white;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }
        #userList h3 {
            background: #3C1561;
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
        .user:hover {
            background: #f8f8f8;
        }
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
        #chatHeader {
            font-weight: bold;
            margin-bottom: 10px;
        }
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
        .patient {
            background: #d4edda;
            float: right;
            border-bottom-left-radius: 0;
        }
        .doctor {
            background: #8e44ad;
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
            background: #8e44ad;
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
</head>
<body>
<div class="chat-layout">
<div id="userList">
        <h3>Bác Sĩ / Chuyên Gia</h3>
        <?php
        include_once("Controllers/ctaikhoan.php");
        $p = new ctaiKhoan();
        $tentk1 = $_SESSION['user']['tentk'];
        $tbl = $p->gettkbacsi($tentk1);

        if ($tbl && $tbl->num_rows > 0) {
            while ($row = $tbl->fetch_assoc()) {
                $img = !empty($row['img']) ? $row['img'] : 'default.png';
                $roleLabel = ($row['vaitro'] === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
                    echo "<div class='user' onclick='selectUser(\"{$row['tentk']}\", \"{$row['hoten']}\", \"{$row['vaitro']}\")'>
                            <img src='Assets/img/{$img}' alt='Ảnh'>
                            <div>
                                <strong>{$row['hoten']}</strong><br>
                                <small>{$roleLabel}</small>
                            </div>
                        </div>";

            }
        } else {
            echo "<p class='p-3'>Không có bác sĩ hoặc chuyên gia nào.</p>";
        }
        ?>
    </div>

    <div id="chatContainer">
        <div id="chatHeader">Chọn bác sĩ/chuyên gia để trò chuyện</div>
        <div id="chatMessages"></div>
        <textarea id="messageInput" placeholder="Nhập tin nhắn..." disabled></textarea>
        
        <!-- Upload file -->
        <input type="file" id="fileInput" style="display:none;">
        <button id="fileButton">📎 Gửi file</button>
        
        <button id="sendButton" disabled>Gửi</button>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let socket;
let user = { 
    tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", 
    vaitro: 1 
};
let currentDoctor = null;
let messages = {}; // lưu lịch sử theo từng bác sĩ

// 📡 Kết nối WebSocket
function connectWebSocket() {
    socket = new WebSocket('ws://localhost:8080');

    socket.onopen = () => {
        console.log("✅ WebSocket connected");
        socket.send(JSON.stringify({ 
            command: 'register', 
            username: user.tentk, 
            role: user.vaitro 
        }));

        // Tự động mở chat với bác sĩ lưu trước đó
        const savedDoctor = localStorage.getItem('selectedDoctor');
        const savedDoctorName = localStorage.getItem('selectedDoctorName');
        if(savedDoctor && savedDoctorName){
            setTimeout(() => selectUser(savedDoctor, savedDoctorName), 300);
        }
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event.data);

        switch(data.command){
            case 'messages': // lịch sử tin nhắn
                const partner = data.receiver_tentk;
                messages[partner] = data.messages;
                if(currentDoctor && currentDoctor.tentk === partner){
                    renderMessages(messages[partner]);
                }
                break;

            case 'receive': // nhận tin nhắn mới
                if(!messages[data.sender]) messages[data.sender] = [];
                messages[data.sender].push({
                    sender: data.sender,
                    message: data.message,
                    filename: data.filename || null,
                    url: data.url || null,
                    thoigiangui: new Date().toISOString()
                });
                if(currentDoctor && currentDoctor.tentk === data.sender){
                    displayMessage(messages[data.sender][messages[data.sender].length-1]);
                }
                break;

            case 'sent': // xác nhận gửi tin nhắn
                if(!messages[data.receiver]) messages[data.receiver] = [];
                messages[data.receiver].push({
                    sender: user.tentk,
                    message: data.message,
                    filename: data.filename || null,
                    url: data.url || null,
                    thoigiangui: new Date().toISOString()
                });
                if(currentDoctor && currentDoctor.tentk === data.receiver){
                    displayMessage(messages[data.receiver][messages[data.receiver].length-1]);
                }
                break;
        }
    };

    socket.onclose = () => {
        console.warn("⚠️ WebSocket closed. Reconnecting...");
        setTimeout(connectWebSocket, 3000);
    };
}

// 👨‍⚕️ Chọn bác sĩ để chat
function selectUser(tentk, name){
    currentDoctor = { tentk, name };
    localStorage.setItem('selectedDoctor', tentk);
    localStorage.setItem('selectedDoctorName', name);

    $('#chatHeader').text('Bạn đang trò chuyện với ' + name);
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);

    $('#chatMessages').html('<p style="text-align:center;color:#777;">Đang tải tin nhắn...</p>');

    // Gửi yêu cầu load lịch sử
    if(socket && socket.readyState === WebSocket.OPEN){
        socket.send(JSON.stringify({
            command: "load_messages",
            tentk: user.tentk,
            receiver_tentk: tentk
        }));
    }
}

// 📝 Hiển thị toàn bộ tin nhắn
function renderMessages(msgArray){
    $('#chatMessages').html('');
    msgArray.forEach(m => displayMessage(m));
}

function displayMessage(msg){
    const msgDiv = $('<div class="message"></div>');
    const isPatient = msg.sender === user.tentk;
    msgDiv.addClass(isPatient ? 'patient' : 'doctor');

    // 🔥 Kiểm tra tin nhắn file (bắt đầu bằng [FILE])
    if(msg.message && msg.message.startsWith('[FILE]')){
        const url = msg.url || msg.message.replace('[FILE] ', '');
        const filename = msg.filename || url.split('/').pop();

        msgDiv.html(`<a href="${url}" target="_blank" download>📄 ${filename}</a>`);
    } 
    else {
        msgDiv.text(msg.message || '');
    }

    $('#chatMessages').append(msgDiv);
    $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
}


// ✉️ Gửi tin nhắn text
$('#sendButton').click(()=>{
    const text = $('#messageInput').val().trim();
    if(!text || !currentDoctor) return;

    $.ajax({
        url: '/KLTN/Ajax/getlichhen.php',
        type: 'POST',
        dataType: 'json', 
        data: { bs: currentDoctor.tentk, bn: user.tentk },
        success: function(response){
            if(response.status === 'ok'){
                const msg = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentDoctor.tentk,
                    message: text
                };
                if(socket && socket.readyState === WebSocket.OPEN){
                    socket.send(JSON.stringify(msg));
                }
                $('#messageInput').val('');
            } else {
                alert(response.message);
            }
        },
        error: function(){
            alert("Không thể kiểm tra lịch hẹn.");
        }
    });
});

// 📎 Gửi file PDF
$('#fileButton').click(()=>{
    if(!currentDoctor) return alert("Chọn bác sĩ trước!");
    $('#fileInput').click();
});

$('#fileInput').change(function(){
    const file = this.files[0];
    if(!file) return;

    if(file.type !== "application/pdf"){
        alert("Chỉ chấp nhận file PDF!");
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('receiver', currentDoctor.tentk);

    $.ajax({
        url: 'Views/benhnhan/pages/tinnhan/uploadFile.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res){
            if(res.success){
                const msg = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentDoctor.tentk,
                    message: '[FILE]',
                    filename: res.filename,
                    url: res.url
                };
                if(socket && socket.readyState === WebSocket.OPEN){
                    socket.send(JSON.stringify(msg));
                }
            } else {
                alert("Upload thất bại: " + res.error);
            }
        },
        error: function(){
            alert("Upload thất bại!");
        }
    });

    $(this).val('');
});

// 🚀 Khởi động WebSocket
$(document).ready(function(){
    connectWebSocket();
});

</script>

</body>
</html>