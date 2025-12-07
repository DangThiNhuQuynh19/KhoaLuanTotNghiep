<?php
if (! isset($_SESSION['user']['tentk'])){
    header("Location: action=dangnhap");
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
        .user.active {
            background: #e8d5f5;
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
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
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
            border-bottom-right-radius: 5px;
        }
        .doctor {
            background: #8e44ad;
            color: white;
            float: left;
            border-bottom-left-radius: 5px;
        }
        #messageInput {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 25px;
            margin-bottom: 10px;
            resize: none;
            font-family: Arial, sans-serif;
        }
        . button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        #sendButton, #fileButton {
            background: #8e44ad;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
        }
        #sendButton:hover, #fileButton:hover {
            background: #6c3483;
        }
        #sendButton:disabled, #fileButton:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        . message a {
            color: inherit;
            text-decoration: underline;
            word-break: break-all;
        }
        .connection-status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            display: inline-block;
            margin-left: 10px;
        }
        .status-connecting {
            background: #ffc107;
            color: #000;
        }
        .status-connected {
            background: #28a745;
            color: white;
        }
        . status-error {
            background: #dc3545;
            color: white;
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
                $img = ! empty($row['img']) ? htmlspecialchars($row['img']) : 'default. png';
                $roleLabel = ($row['vaitro'] === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
                
                $tentk_safe = htmlspecialchars($row['tentk'], ENT_QUOTES, 'UTF-8');
                $hoten_safe = htmlspecialchars($row['hoten'], ENT_QUOTES, 'UTF-8');
                $vaitro_safe = htmlspecialchars($row['vaitro'], ENT_QUOTES, 'UTF-8');
                
                echo "<div class='user' data-tentk='{$tentk_safe}' data-name='{$hoten_safe}' data-vaitro='{$vaitro_safe}'>
                        <img src='Assets/img/{$img}' alt='Ảnh' onerror=\"this.src='Assets/img/default.png'\">
                        <div>
                            <strong>{$row['hoten']}</strong><br>
                            <small>{$roleLabel}</small>
                        </div>
                    </div>";
            }
        } else {
            echo "<p class='p-3' style='text-align:center;color:#999;'>Không có bác sĩ hoặc chuyên gia nào.</p>";
        }
        ?>
    </div>

    <div id="chatContainer">
        <div id="chatHeader">
            <span id="headerText">⏳ Đang kết nối... </span>
            <span id="connectionStatus" class="connection-status status-connecting">Đang kết nối</span>
        </div>
        <div id="chatMessages"></div>
        <textarea id="messageInput" placeholder="Nhập tin nhắn..." disabled rows="2"></textarea>
        
        <input type="file" id="fileInput" style="display:none;" accept="application/pdf">
        
        <div class="button-group">
            <button id="fileButton" disabled>📎 Gửi file PDF</button>
            <button id="sendButton" disabled>Gửi tin nhắn</button>
        </div>
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
let messages = {};

function connectWebSocket() {
    updateConnectionStatus('connecting', '⏳ Đang kết nối...');
    
    socket = new WebSocket("wss://hanhphuc.site/ws");
    
    socket.onopen = () => {
        console.log("✅ WebSocket connected!");
        updateConnectionStatus('connected', '✅ Đã kết nối');
        
        socket.send(JSON.stringify({ 
            command: 'register', 
            username: user.tentk, 
            role: user.vaitro 
        }));

        $('#headerText').text('Chọn bác sĩ/chuyên gia để trò chuyện');

        const savedDoctor = localStorage.getItem('selectedDoctor');
        const savedDoctorName = localStorage.getItem('selectedDoctorName');
        const savedVaitro = localStorage.getItem('selectedVaitro') || 'bacsi';
        
        if(savedDoctor && savedDoctorName){
            setTimeout(() => selectUser(savedDoctor, savedDoctorName, savedVaitro), 500);
        }
    };

    socket.onmessage = (event) => {
        const data = JSON.parse(event. data);
        console.log("📩 Received:", data);

        switch(data.command){
            case 'messages':
                const partner = data.receiver_tentk;
                messages[partner] = data.messages || [];
                if(currentDoctor && currentDoctor.tentk === partner){
                    renderMessages(messages[partner]);
                }
                break;

            case 'receive':
                if(! messages[data.sender]) messages[data.sender] = [];
                messages[data.sender]. push({
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

            case 'sent':
                break;
        }
    };

    socket.onerror = (error) => {
        console.error("❌ WebSocket error:", error);
        updateConnectionStatus('error', '❌ Lỗi kết nối');
    };

    socket.onclose = (event) => {
        console. warn("⚠️ WebSocket closed:", event. code, event.reason);
        updateConnectionStatus('connecting', '⚠️ Mất kết nối, đang thử lại...');
        setTimeout(connectWebSocket, 3000);
    };
}

function updateConnectionStatus(status, text) {
    const statusEl = $('#connectionStatus');
    statusEl. removeClass('status-connecting status-connected status-error');
    statusEl.addClass('status-' + status);
    statusEl.text(text);
}

function selectUser(tentk, name, vaitro){
    console.log("=== selectUser START ===");
    console.log("📥 Tham số:", {tentk, name, vaitro});
    console.log("🔌 Socket state:", socket?. readyState);
    
    if(! tentk || !name){
        console.error("❌ Thiếu tham số");
        alert("Lỗi: Không thể chọn bác sĩ.  Vui lòng thử lại!");
        return;
    }
    
    if(! socket){
        console.error("❌ Socket chưa được khởi tạo");
        alert("Đang kết nối...  Vui lòng đợi và thử lại!");
        return;
    }
    
    if(socket.readyState === WebSocket.CONNECTING){
        console.warn("⏳ Socket đang kết nối...  retry");
        setTimeout(() => selectUser(tentk, name, vaitro), 1000);
        return;
    }
    
    if(socket. readyState !== WebSocket.OPEN){
        console.error("❌ Socket không OPEN:", socket.readyState);
        alert("Kết nối bị gián đoạn. Vui lòng tải lại trang!");
        return;
    }

    console.log("✅ Bắt đầu chọn bác sĩ");
    
    // ✅ SỬA LỖI: Bỏ dấu cách trong selector
    $('.user').removeClass('active');
    $('.user[data-tentk="' + tentk + '"]').addClass('active');
    
    currentDoctor = { tentk, name, vaitro };
    localStorage.setItem('selectedDoctor', tentk);
    localStorage.setItem('selectedDoctorName', name);
    localStorage.setItem('selectedVaitro', vaitro);

    const roleLabel = (vaitro === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
    $('#headerText').text('Đang trò chuyện với ' + roleLabel + ' ' + name);
    
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#fileButton').prop('disabled', false);

    $('#chatMessages').html('<p style="text-align:center;color:#777;">Đang tải tin nhắn...</p>');

    if(messages[tentk] && messages[tentk].length > 0){
        renderMessages(messages[tentk]);
    }

    const loadMsg = {
        command: "load_messages",
        tentk: user.tentk,
        receiver_tentk: tentk
    };
    
    console.log("📤 Gửi load_messages:", loadMsg);
    socket.send(JSON.stringify(loadMsg));
    console.log("=== selectUser END ===");
}

function renderMessages(msgArray){
    $('#chatMessages').html('');
    if(msgArray && msgArray.length > 0){
        msgArray.forEach(m => displayMessage(m));
    } else {
        $('#chatMessages'). html('<p style="text-align:center;color:#999;">Chưa có tin nhắn nào</p>');
    }
    scrollToBottom();
}

function displayMessage(msg){
    const msgDiv = $('<div class="message"></div>');
    const isPatient = msg.sender === user.tentk;
    msgDiv.addClass(isPatient ?  'patient' : 'doctor');

    if(msg.message && msg.message.startsWith('[FILE]')){
        const url = msg.url || msg.message. replace('[FILE] ', '');
        const filename = msg.filename || url.split('/').pop();
        msgDiv.html('<a href="' + url + '" target="_blank" download>📄 ' + filename + '</a>');
    } else {
        msgDiv.text(msg.message || '');
    }

    $('#chatMessages').append(msgDiv);
    scrollToBottom();
}

function scrollToBottom(){
    const chatBox = $('#chatMessages');
    chatBox.scrollTop(chatBox[0].scrollHeight);
}

$('#sendButton').click(sendMessage);
$('#messageInput').keypress(function(e){
    if(e.which === 13 && !e.shiftKey){
        e.preventDefault();
        sendMessage();
    }
});

function sendMessage(){
    const text = $('#messageInput').val(). trim();
    if(!text || !currentDoctor) return;

    if(! socket || socket.readyState !== WebSocket.OPEN){
        alert("❌ Kết nối WebSocket bị gián đoạn!");
        return;
    }

    $. ajax({
        url: 'Ajax/getlichhen.php',
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
                
                socket. send(JSON.stringify(msg));
                console.log("📤 Sent:", msg);
                
                if(! messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                messages[currentDoctor.tentk].push({
                    sender: user.tentk,
                    message: text,
                    thoigiangui: new Date().toISOString()
                });
                displayMessage(messages[currentDoctor.tentk][messages[currentDoctor.tentk].length-1]);
                
                $('#messageInput').val('');
            } else {
                alert(response.message || "Bạn chưa có lịch hẹn với bác sĩ này!");
            }
        },
        error: function(xhr, status, error){
            console.error("Ajax error:", error);
            alert("Không thể kiểm tra lịch hẹn. Vui lòng thử lại!");
        }
    });
}

$('#fileButton').click(function(){
    if(!currentDoctor){
        alert("Vui lòng chọn bác sĩ trước!");
        return;
    }
    $('#fileInput').click();
});

$('#fileInput').change(function(){
    const file = this.files[0];
    if(! file) return;

    if(file.type !== "application/pdf"){
        alert("⚠️ Chỉ chấp nhận file PDF!");
        $(this).val('');
        return;
    }

    if(file.size > 10 * 1024 * 1024){
        alert("⚠️ File không được vượt quá 10MB!");
        $(this).val('');
        return;
    }

    if(!socket || socket.readyState !== WebSocket.OPEN){
        alert("❌ Kết nối WebSocket bị gián đoạn!");
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('receiver', currentDoctor.tentk);
    formData.append('sender', user.tentk);

    $('#fileButton').prop('disabled', true). text('⏳ Đang tải lên...');

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
                    url: res. url
                };
                
                socket.send(JSON.stringify(msg));
                console.log("📤 File sent:", res. filename);
                
                if(! messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                messages[currentDoctor.tentk].push({
                    sender: user. tentk,
                    message: '[FILE]',
                    filename: res.filename,
                    url: res.url,
                    thoigiangui: new Date().toISOString()
                });
                displayMessage(messages[currentDoctor.tentk][messages[currentDoctor.tentk].length-1]);
                
                alert("✅ Gửi file thành công!");
            } else {
                alert("❌ Upload thất bại: " + (res.error || "Lỗi không xác định"));
            }
        },
        error: function(xhr, status, error){
            console.error("Upload error:", error, xhr. responseText);
            alert("❌ Upload thất bại!  Vui lòng thử lại.");
        },
        complete: function(){
            $('#fileButton').prop('disabled', false).text('📎 Gửi file PDF');
        }
    });

    $(this).val('');
});

$(document).on('click', '.user', function(){
    const tentk = $(this).data('tentk');
    const name = $(this).data('name');
    const vaitro = $(this).data('vaitro');
    
    console.log("🖱️ User clicked:", {tentk, name, vaitro});
    selectUser(tentk, name, vaitro);
});

$(document).ready(function(){
    console.log("🚀 Page loaded, connecting WebSocket...");
    console.log("👤 Current user:", user);
    connectWebSocket();
});
</script>

</body>
</html>