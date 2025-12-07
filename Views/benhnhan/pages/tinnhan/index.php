<?php
if (! isset($_SESSION['user']['tentk'])) {
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
    <link href="https://fonts.googleapis.com/css2? family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare. com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            overflow: hidden;
        }
        
        .chat-wrapper {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
            height: 100vh;
        }
        
        .chat-layout {
            display: flex;
            height: calc(100vh - 40px);
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        }
        
        /* ========== SIDEBAR ========== */
        #userList {
            width: 380px;
            background: linear-gradient(180deg, #2C3E50 0%, #34495E 100%);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .sidebar-header {
            background: rgba(0,0,0,0.2);
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            color: white;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .sidebar-header h3 i {
            font-size: 24px;
            color: #3498db;
        }
        
        .sidebar-header p {
            color: rgba(255,255,255,0. 6);
            font-size: 13px;
            margin-left: 36px;
        }
        
        .users-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        
        .users-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .users-list::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        
        .users-list::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        
        .user {
            padding: 16px;
            margin-bottom: 8px;
            border-radius: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
            position: relative;
        }
        
        .user::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: #3498db;
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .user:hover {
            background: rgba(52, 152, 219, 0.15);
            transform: translateX(8px);
        }
        
        .user.active {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.3), rgba(41, 128, 185, 0.3));
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .user.active::before {
            opacity: 1;
        }
        
        .user-avatar {
            position: relative;
            flex-shrink: 0;
        }
        
        .user img {
            border-radius: 50%;
            width: 56px;
            height: 56px;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        
        .user.active img {
            border-color: #3498db;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
        }
        
        .user-avatar::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            background: #27ae60;
            border: 3px solid #2C3E50;
            border-radius: 50%;
        }
        
        .user-info {
            flex: 1;
            overflow: hidden;
        }
        
        .user-info strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-info small {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .user-info small i {
            font-size: 12px;
        }
        
        /* ========== CHAT AREA ========== */
        #chatContainer {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }
        
        #chatHeader {
            background: white;
            padding: 22px 30px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        
        .header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header-info i {
            font-size: 28px;
            color: #3498db;
        }
        
        #headerText {
            font-size: 19px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        . connection-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .connection-status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .status-connecting {
            background: linear-gradient(135deg, #fff3cd, #ffe69c);
            color: #856404;
        }
        
        .status-connecting::before {
            background: #ffc107;
            animation: pulse 1. 5s infinite;
        }
        
        . status-connected {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        
        .status-connected::before {
            background: #28a745;
        }
        
        .status-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }
        
        .status-error::before {
            background: #dc3545;
        }
        
        @keyframes pulse {
            0%, 100% { 
                opacity: 1;
                transform: scale(1);
            }
            50% { 
                opacity: 0.5;
                transform: scale(0.8);
            }
        }
        
        /* ========== MESSAGES ========== */
        #chatMessages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
        }
        
        #chatMessages::-webkit-scrollbar {
            width: 10px;
        }
        
        #chatMessages::-webkit-scrollbar-track {
            background: transparent;
        }
        
        #chatMessages::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3498db, #2980b9);
            border-radius: 5px;
        }
        
        . message-wrapper {
            margin-bottom: 20px;
            display: flex;
            animation: messageSlide 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message-wrapper.sent {
            justify-content: flex-end;
        }
        
        .message {
            max-width: 60%;
            padding: 14px 18px;
            border-radius: 20px;
            font-size: 15px;
            line-height: 1.5;
            word-wrap: break-word;
            position: relative;
        }
        
        .message-wrapper.sent .message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 6px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .message-wrapper.received .message {
            background: white;
            color: #2c3e50;
            border-bottom-left-radius: 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .message-time {
            font-size: 11px;
            margin-top: 6px;
            opacity: 0.7;
            text-align: right;
        }
        
        .message a {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            padding: 12px 16px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .message a:hover {
            background: rgba(255,255,255,0. 25);
            transform: translateX(5px);
        }
        
        .message a i. fa-file-pdf {
            font-size: 28px;
        }
        
        .message-wrapper.received .message a {
            background: rgba(52, 152, 219, 0.1);
        }
        
        .message-wrapper.received .message a:hover {
            background: rgba(52, 152, 219, 0.2);
        }
        
        .message-wrapper.received .message a i.fa-file-pdf {
            color: #e74c3c;
        }
        
        .empty-state {
            text-align: center;
            color: #95a5a6;
            padding: 60px 20px;
            animation: fadeIn 0.6s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .empty-state i {
            font-size: 80px;
            color: #d5d8dc;
            margin-bottom: 20px;
            display: block;
        }
        
        .empty-state p {
            font-size: 16px;
            font-weight: 500;
        }
        
        /* ========== INPUT AREA ========== */
        . input-container {
            background: white;
            padding: 25px 30px;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.03);
        }
        
        .input-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        
        .textarea-wrapper {
            flex: 1;
            position: relative;
        }
        
        #messageInput {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            resize: none;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8f9fa;
        }
        
        #messageInput:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
        }
        
        #messageInput:disabled {
            background: #e9ecef;
            cursor: not-allowed;
            color: #95a5a6;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
        }
        
        #sendButton, #fileButton {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        #sendButton:hover, #fileButton:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        #sendButton:active, #fileButton:active {
            transform: translateY(-1px);
        }
        
        #sendButton:disabled, #fileButton:disabled {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        #fileButton {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0. 3);
        }
        
        #fileButton:hover {
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0. 4);
        }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 1024px) {
            #userList {
                width: 320px;
            }
            
            .message {
                max-width: 70%;
            }
        }
        
        @media (max-width: 768px) {
            . chat-wrapper {
                padding: 0;
            }
            
            . chat-layout {
                border-radius: 0;
                height: 100vh;
            }
            
            #userList {
                width: 100%;
                max-width: 300px;
                position: absolute;
                z-index: 100;
                height: 100%;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            #userList.show {
                transform: translateX(0);
            }
            
            . message {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
<div class="chat-wrapper">
    <div class="chat-layout">
        <!-- SIDEBAR -->
        <div id="userList">
            <div class="sidebar-header">
                <h3><i class="fas fa-user-md"></i> Bác Sĩ & Chuyên Gia</h3>
                <p>Chọn bác sĩ để bắt đầu tư vấn</p>
            </div>
            
            <div class="users-list">
                <?php
                include_once("Controllers/ctaikhoan.php");
                $p = new ctaiKhoan();
                $tentk1 = $_SESSION['user']['tentk'];
                $tbl = $p->gettkbacsi($tentk1);

                if ($tbl && $tbl->num_rows > 0) {
                    while ($row = $tbl->fetch_assoc()) {
                        $img = ! empty($row['img']) ? htmlspecialchars($row['img']) : 'default. png';
                        $roleLabel = ($row['vaitro'] === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
                        $roleIcon = ($row['vaitro'] === 'bacsi') ? 'fa-stethoscope' : 'fa-user-tie';
                        
                        $tentk_safe = htmlspecialchars($row['tentk'], ENT_QUOTES, 'UTF-8');
                        $hoten_safe = htmlspecialchars($row['hoten'], ENT_QUOTES, 'UTF-8');
                        $vaitro_safe = htmlspecialchars($row['vaitro'], ENT_QUOTES, 'UTF-8');
                        
                        echo "<div class='user' data-tentk='{$tentk_safe}' data-name='{$hoten_safe}' data-vaitro='{$vaitro_safe}'>
                                <div class='user-avatar'>
                                    <img src='Assets/img/{$img}' alt='Avatar' onerror=\"this.src='Assets/img/default.png'\">
                                </div>
                                <div class='user-info'>
                                    <strong>{$row['hoten']}</strong>
                                    <small><i class='fas {$roleIcon}'></i> {$roleLabel}</small>
                                </div>
                            </div>";
                    }
                } else {
                    echo "<div class='empty-state'><i class='fas fa-user-slash'></i><p>Chưa có bác sĩ nào</p></div>";
                }
                ?>
            </div>
        </div>

        <!-- CHAT AREA -->
        <div id="chatContainer">
            <div id="chatHeader">
                <div class="header-info">
                    <i class="fas fa-comments"></i>
                    <span id="headerText">Chọn bác sĩ để bắt đầu</span>
                </div>
                <span id="connectionStatus" class="connection-status status-connecting">Đang kết nối</span>
            </div>
            
            <div id="chatMessages">
                <div class="empty-state">
                    <i class="fas fa-comment-medical"></i>
                    <p>Chọn bác sĩ để bắt đầu tư vấn sức khỏe</p>
                </div>
            </div>
            
            <div class="input-container">
                <div class="input-wrapper">
                    <div class="textarea-wrapper">
                        <textarea id="messageInput" placeholder="Nhập tin nhắn của bạn..." disabled rows="2"></textarea>
                    </div>
                    
                    <input type="file" id="fileInput" style="display:none;" accept="application/pdf">
                    
                    <div class="button-group">
                        <button id="fileButton" disabled>
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button id="sendButton" disabled>
                            <i class="fas fa-paper-plane"></i> Gửi
                        </button>
                    </div>
                </div>
            </div>
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
    updateConnectionStatus('connecting', 'Đang kết nối');
    
    socket = new WebSocket("wss://hanhphuc.site/ws");
    
    socket.onopen = () => {
        console.log("✅ WebSocket connected!");
        updateConnectionStatus('connected', 'Đã kết nối');
        
        socket.send(JSON.stringify({ 
            command: 'register', 
            username: user.tentk, 
            role: user.vaitro 
        }));

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
                messages[data.sender].push({
                    sender: data.sender,
                    message: data.message,
                    filename: data.filename || null,
                    url: data.url || null,
                    thoigiangui: new Date(). toISOString()
                });
                if(currentDoctor && currentDoctor.tentk === data.sender){
                    displayMessage(messages[data.sender][messages[data.sender].length-1]);
                }
                break;
        }
    };

    socket.onerror = (error) => {
        console.error("❌ WebSocket error:", error);
        updateConnectionStatus('error', 'Lỗi kết nối');
    };

    socket.onclose = (event) => {
        console. warn("⚠️ WebSocket closed:", event. code);
        updateConnectionStatus('connecting', 'Đang kết nối lại');
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
    if(! tentk || !name) return;
    
    if(! socket || socket.readyState !== WebSocket. OPEN){
        if(socket && socket.readyState === WebSocket.CONNECTING){
            setTimeout(() => selectUser(tentk, name, vaitro), 1000);
            return;
        }
        alert("Kết nối bị gián đoạn!");
        return;
    }

    $('.user').removeClass('active');
    $('.user'). filter(function() {
        return $(this).data('tentk') === tentk;
    }).addClass('active');
    
    currentDoctor = { tentk, name, vaitro };
    localStorage.setItem('selectedDoctor', tentk);
    localStorage.setItem('selectedDoctorName', name);
    localStorage.setItem('selectedVaitro', vaitro);

    const roleLabel = (vaitro === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
    $('#headerText').text(`${roleLabel} ${name}`);
    
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#fileButton').prop('disabled', false);

    $('#chatMessages').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Đang tải... </p></div>');

    if(messages[tentk] && messages[tentk].length > 0){
        renderMessages(messages[tentk]);
    }

    socket.send(JSON.stringify({
        command: "load_messages",
        tentk: user.tentk,
        receiver_tentk: tentk
    }));
}

function renderMessages(msgArray){
    $('#chatMessages').html('');
    if(msgArray && msgArray.length > 0){
        msgArray.forEach(m => displayMessage(m));
    } else {
        $('#chatMessages'). html('<div class="empty-state"><i class="fas fa-inbox"></i><p>Chưa có tin nhắn</p></div>');
    }
    scrollToBottom();
}

function displayMessage(msg){
    const isPatient = msg.sender === user.tentk;
    const wrapper = $('<div class="message-wrapper"></div>');
    wrapper.addClass(isPatient ? 'sent' : 'received');
    
    const msgDiv = $('<div class="message"></div>');

    if(msg.message && msg.message.startsWith('[FILE]')){
        const url = msg.url || msg.message. replace('[FILE] ', '');
        const filename = msg.filename || 'document.pdf';
        msgDiv.html('<a href="' + url + '" target="_blank" rel="noopener"><i class="fas fa-file-pdf"></i> ' + filename + '</a>');
    } else {
        msgDiv.text(msg.message || '');
    }

    wrapper.append(msgDiv);
    $('#chatMessages').append(wrapper);
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
    if(!text || ! currentDoctor) return;

    if(! socket || socket.readyState !== WebSocket.OPEN){
        alert("❌ Kết nối bị gián đoạn!");
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
                
                socket.send(JSON.stringify(msg));
                
                if(! messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                messages[currentDoctor.tentk].push({
                    sender: user.tentk,
                    message: text,
                    thoigiangui: new Date().toISOString()
                });
                displayMessage(messages[currentDoctor.tentk][messages[currentDoctor.tentk].length-1]);
                
                $('#messageInput').val('');
            } else {
                alert(response.message || "Bạn chưa có lịch hẹn!");
            }
        }
    });
}

$('#fileButton').click(function(){
    if(!currentDoctor){
        alert("Chọn bác sĩ trước!");
        return;
    }
    $('#fileInput').click();
});

$('#fileInput').change(function(){
    const file = this.files[0];
    if(! file) return;

    if(file.type !== "application/pdf"){
        alert("⚠️ Chỉ chấp nhận PDF!");
        $(this).val('');
        return;
    }

    if(file.size > 10 * 1024 * 1024){
        alert("⚠️ File quá lớn (max 10MB)!");
        $(this).val('');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('receiver', currentDoctor.tentk);
    formData.append('sender', user.tentk);

    const originalText = $('#fileButton').html();
    $('#fileButton').prop('disabled', true). html('<i class="fas fa-spinner fa-spin"></i> Đang tải');

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
                
                if(! messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                messages[currentDoctor.tentk].push({
                    sender: user. tentk,
                    message: '[FILE]',
                    filename: res.filename,
                    url: res.url,
                    thoigiangui: new Date().toISOString()
                });
                displayMessage(messages[currentDoctor.tentk][messages[currentDoctor.tentk].length-1]);
            } else {
                alert("❌ Upload thất bại!");
            }
        },
        complete: function(){
            $('#fileButton').prop('disabled', false).html(originalText);
        }
    });

    $(this).val('');
});

$(document).on('click', '.user', function(){
    selectUser($(this).data('tentk'), $(this).data('name'), $(this).data('vaitro'));
});

$(document). ready(function(){
    connectWebSocket();
});
</script>
</body>
</html>