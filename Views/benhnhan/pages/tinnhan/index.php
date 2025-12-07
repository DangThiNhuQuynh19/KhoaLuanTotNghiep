<?php
if (!isset($_SESSION['user']['tentk'])) {
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
    <link href="https://fonts.googleapis.com/css2? family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            padding-top: 90px;
            height: 100vh;
        }
        
        .chat-layout {
            display: flex;
            height: calc(100vh - 110px);
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        #userList {
            width: 350px;
            background: linear-gradient(180deg, #3C1561 0%, #2d1049 100%);
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        #userList h3 {
            background: rgba(0,0,0,0.2);
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        #userList h3 i {
            font-size: 22px;
        }
        
        . user {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            color: white;
        }
        
        .user:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 25px;
        }
        
        .user.active {
            background: rgba(142, 68, 173, 0.4);
            border-left: 4px solid #8e44ad;
        }
        
        .user img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        
        .user:hover img {
            border-color: #8e44ad;
            transform: scale(1.05);
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-info strong {
            display: block;
            font-size: 15px;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .user-info small {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }
        
        #chatContainer {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }
        
        #chatHeader {
            background: white;
            padding: 20px 25px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        #headerText {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        . connection-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .connection-status::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-connecting {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-connecting::before {
            background: #ffc107;
            animation: pulse 1. 5s infinite;
        }
        
        . status-connected {
            background: #d4edda;
            color: #155724;
        }
        
        .status-connected::before {
            background: #28a745;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-error::before {
            background: #dc3545;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        #chatMessages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        
        #chatMessages::-webkit-scrollbar {
            width: 8px;
        }
        
        #chatMessages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        #chatMessages::-webkit-scrollbar-thumb {
            background: #8e44ad;
            border-radius: 4px;
        }
        
        . message {
            max-width: 65%;
            padding: 12px 16px;
            margin-bottom: 12px;
            border-radius: 18px;
            font-size: 15px;
            line-height: 1.5;
            clear: both;
            word-wrap: break-word;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .patient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            float: right;
            border-bottom-right-radius: 4px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        }
        
        .doctor {
            background: white;
            color: #2c3e50;
            float: left;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0. 1);
        }
        
        .message a {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            padding: 8px 12px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .message a:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(3px);
        }
        
        .message a i. fa-file-pdf {
            font-size: 24px;
        }
        
        .doctor a {
            background: rgba(142, 68, 173, 0.1);
        }
        
        .doctor a:hover {
            background: rgba(142, 68, 173, 0.2);
        }
        
        .doctor a i.fa-file-pdf {
            color: #e74c3c;
        }
        
        .patient a i.fa-file-pdf {
            color: #fff;
        }
        
        .empty-state {
            text-align: center;
            color: #999;
            padding: 40px;
        }
        
        .empty-state i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 15px;
        }
        
        .input-container {
            background: white;
            padding: 20px 25px;
            border-top: 1px solid #e0e0e0;
        }
        
        #messageInput {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            margin-bottom: 12px;
            resize: none;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        #messageInput:focus {
            outline: none;
            border-color: #8e44ad;
            box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
        }
        
        #messageInput:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        
        . button-group {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        #sendButton, #fileButton {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        #sendButton:hover, #fileButton:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        #sendButton:active, #fileButton:active {
            transform: translateY(0);
        }
        
        #sendButton:disabled, #fileButton:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        #fileButton {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        #fileButton:hover {
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }
        
        @media (max-width: 768px) {
            .chat-layout {
                border-radius: 0;
                height: 100vh;
            }
            
            #userList {
                width: 280px;
            }
            
            . message {
                max-width: 80%;
            }
        }
    </style>
</head>
<body>
<div class="chat-layout">
    <div id="userList">
        <h3><i class="fas fa-user-md"></i> Bác Sĩ / Chuyên Gia</h3>
        <? php
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
                        <div class='user-info'>
                            <strong>{$row['hoten']}</strong>
                            <small><i class='fas fa-stethoscope'></i> {$roleLabel}</small>
                        </div>
                    </div>";
            }
        } else {
            echo "<div class='empty-state'><i class='fas fa-inbox'></i><p>Không có bác sĩ nào</p></div>";
        }
        ?>
    </div>

    <div id="chatContainer">
        <div id="chatHeader">
            <span id="headerText"><i class="fas fa-comments"></i> Chọn bác sĩ để trò chuyện</span>
            <span id="connectionStatus" class="connection-status status-connecting">Đang kết nối</span>
        </div>
        
        <div id="chatMessages">
            <div class="empty-state">
                <i class="fas fa-comment-dots"></i>
                <p>Chọn bác sĩ để bắt đầu trò chuyện</p>
            </div>
        </div>
        
        <div class="input-container">
            <textarea id="messageInput" placeholder="Nhập tin nhắn của bạn..." disabled rows="2"></textarea>
            
            <input type="file" id="fileInput" style="display:none;" accept="application/pdf">
            
            <div class="button-group">
                <button id="fileButton" disabled>
                    <i class="fas fa-paperclip"></i> Gửi PDF
                </button>
                <button id="sendButton" disabled>
                    <i class="fas fa-paper-plane"></i> Gửi
                </button>
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

        $('#headerText').html('<i class="fas fa-comments"></i> Chọn bác sĩ để trò chuyện');

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
    if(! tentk || !name){
        alert("Lỗi: Không thể chọn bác sĩ!");
        return;
    }
    
    if(! socket || socket.readyState !== WebSocket. OPEN){
        if(socket && socket.readyState === WebSocket.CONNECTING){
            setTimeout(() => selectUser(tentk, name, vaitro), 1000);
            return;
        }
        alert("Kết nối bị gián đoạn. Vui lòng tải lại trang!");
        return;
    }

    $('. user').removeClass('active');
    $('. user'). filter(function() {
        return $(this).data('tentk') === tentk;
    }).addClass('active');
    
    currentDoctor = { tentk, name, vaitro };
    localStorage.setItem('selectedDoctor', tentk);
    localStorage.setItem('selectedDoctorName', name);
    localStorage.setItem('selectedVaitro', vaitro);

    const roleLabel = (vaitro === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
    const icon = (vaitro === 'bacsi') ? 'fa-user-md' : 'fa-user-tie';
    $('#headerText'). html(`<i class="fas ${icon}"></i> ${roleLabel} ${name}`);
    
    $('#messageInput').prop('disabled', false);
    $('#sendButton').prop('disabled', false);
    $('#fileButton').prop('disabled', false);

    $('#chatMessages').html('<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Đang tải tin nhắn...</p></div>');

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
        $('#chatMessages').html('<div class="empty-state"><i class="fas fa-comment-slash"></i><p>Chưa có tin nhắn nào</p></div>');
    }
    scrollToBottom();
}

function displayMessage(msg){
    const msgDiv = $('<div class="message"></div>');
    const isPatient = msg.sender === user.tentk;
    msgDiv.addClass(isPatient ?  'patient' : 'doctor');

    // ✅ FIX: Link PDF mở tab mới, không download
    if(msg.message && msg.message.startsWith('[FILE]')){
        const url = msg.url || msg.message. replace('[FILE] ', '');
        const filename = msg.filename || 'document. pdf';
        
        msgDiv.html(
            '<a href="' + url + '" target="_blank" rel="noopener noreferrer">' +
            '<i class="fas fa-file-pdf"></i> ' + 
            filename + 
            '</a>'
        );
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
                
                socket. send(JSON.stringify(msg));
                
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
        error: function(xhr){
            console.error("Ajax error:", xhr.responseText);
            alert("Không thể kiểm tra lịch hẹn!");
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
        alert("❌ Kết nối bị gián đoạn!");
        return;
    }

    const formData = new FormData();
    formData. append('file', file);
    formData.append('receiver', currentDoctor.tentk);
    formData.append('sender', user.tentk);

    const originalText = $('#fileButton').html();
    $('#fileButton').prop('disabled', true). html('<i class="fas fa-spinner fa-spin"></i> Đang tải...');

    $. ajax({
        url: 'Views/benhnhan/pages/tinnhan/uploadFile.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res){
            console.log("✅ Upload response:", res);
            
            if(res.success){
                const msg = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentDoctor.tentk,
                    message: '[FILE]',
                    filename: res.filename,
                    url: res.url
                };
                
                socket.send(JSON.stringify(msg));
                
                if(!messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                messages[currentDoctor.tentk].push({
                    sender: user.tentk,
                    message: '[FILE]',
                    filename: res.filename,
                    url: res.url,
                    thoigiangui: new Date().toISOString()
                });
                displayMessage(messages[currentDoctor.tentk][messages[currentDoctor.tentk].length-1]);
                
                alert("✅ Gửi file thành công!");
            } else {
                alert("❌ Upload thất bại: " + (res.error || res.message || "Lỗi không xác định"));
            }
        },
        error: function(xhr, status, error){
            console. error("❌ Upload error:", {
                status: status,
                error: error,
                response: xhr.responseText
            });
            alert("❌ Upload thất bại!  Kiểm tra Console (F12) để xem chi tiết.");
        },
        complete: function(){
            $('#fileButton').prop('disabled', false).html(originalText);
        }
    });

    $(this).val('');
});

$(document).on('click', '.user', function(){
    const tentk = $(this).data('tentk');
    const name = $(this).data('name');
    const vaitro = $(this).data('vaitro');
    
    selectUser(tentk, name, vaitro);
});

$(document).ready(function(){
    console.log("🚀 Initializing.. .");
    connectWebSocket();
});
</script>

</body>
</html>