<?php
session_start();
if (!isset($_SESSION['user']['tentk'])) {
    header("Location: ?action=dangnhap");
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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Reset and base */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            padding-top: 100px;
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #111827;
            overflow: hidden;
        }

        /* Layout */
        .chat-wrapper {
            max-width: 1200px;
            margin: 18px auto;
            height: calc(60vh - 36px);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            background: white;
            display: flex;
        }

        /* Sidebar (contacts) */
        #userList {
            width: 320px;
            border-right: 1px solid #e6e9ee;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .sidebar-top {
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #f0f2f5;
        }

        .sidebar-top h3 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .search-box {
            padding: 10px 16px;
            border-bottom: 1px solid #f0f2f5;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 999px;
            border: 1px solid #e6e9ee;
            background: #f7f9fb;
            font-size: 14px;
        }

        .users-list {
            overflow-y: auto;
            padding: 8px;
        }
        .users-list::-webkit-scrollbar { width: 8px; }
        .users-list::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 6px; }

        .user {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: background .15s, transform .12s;
        }
        .user:hover { background: #f5f7fa; transform: translateX(2px); }
        .user.active { background: #e8f0ff; box-shadow: inset 0 0 0 1px rgba(52,120,255,0.08); }

        .user-avatar img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f0f2f5;
        }

        .user-info strong { display:block; font-size: 15px; font-weight:600; color:#0f1724; }
        .user-info small { color:#6b7280; font-size:13px; margin-top:4px; display:flex; gap:8px; align-items:center; }

        /* Chat area */
        #chatContainer {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        #chatHeader {
            padding: 14px 18px;
            display:flex;
            align-items:center;
            gap:12px;
            border-bottom: 1px solid #f0f2f5;
            background: linear-gradient(90deg, #fff, #fbfdff);
        }

        .header-avatar img {
            width:44px; height:44px; border-radius:50%; object-fit:cover;
            border: 2px solid transparent;
        }
        .header-info { flex:1; display:flex; flex-direction:column; }
        #headerText { font-weight:600; font-size:16px; color:#0f1724; }
        #headerSub { color:#6b7280; font-size:13px; margin-top:2px; }

        /* Messages container */
        #chatMessages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: linear-gradient(#f6f7fb, #f6f7fb);
        }
        #chatMessages::-webkit-scrollbar { width: 10px; }
        #chatMessages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.06); border-radius: 6px; }

        .message-day {
            text-align:center;
            color:#9aa3b2;
            font-size:12px;
            margin: 8px 0 18px;
        }

        .message-wrapper {
            display:flex;
            margin-bottom: 12px;
            align-items: flex-end;
            gap: 10px;
        }
        .message-wrapper.sent { justify-content: flex-end; }
        .message-wrapper.received { justify-content: flex-start; }

        .bubble {
            max-width: 68%;
            padding: 10px 14px;
            border-radius: 18px;
            font-size:14px;
            line-height:1.35;
            position: relative;
            box-shadow: 0 4px 10px rgba(2,6,23,0.06);
            word-wrap: break-word;
        }

        /* sent (me) */
        .message-wrapper.sent .bubble {
            background: linear-gradient(135deg,#1677ff,#0b63d6);
            color: #fff;
            border-bottom-right-radius: 6px;
        }
        /* received */
        .message-wrapper.received .bubble {
            background: #ffffff;
            color: #0f1724;
            border-bottom-left-radius: 6px;
        }

        .avatar-small {
            width:32px; height:32px; border-radius:50%; object-fit:cover;
            flex-shrink:0;
        }

        .message-meta {
            display:block;
            font-size:11px;
            color: rgba(15,23,36,0.6);
            margin-top:6px;
            text-align: right;
        }
        .message-wrapper.received .message-meta { text-align: left; color:#6b7280; }

        .bubble a { color: inherit; text-decoration: none; display:inline-flex; gap:10px; align-items:center; }
        .bubble a .fa-file-pdf { color: #e3342f; font-size:20px; }

        /* Input area fixed */
        .input-container {
            padding: 12px 16px;
            border-top: 1px solid #eef2f7;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .input-inner {
            display:flex;
            align-items:center;
            gap:10px;
            flex:1;
            background: #f4f6fb;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #e6e9ee;
        }

        .input-inner input[type="text"], .input-inner textarea {
            border: none;
            background: transparent;
            outline: none;
            resize: none;
            font-size: 14px;
            width: 100%;
            color: #0f1724;
            padding: 6px 8px;
        }

        .icon-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #55607a;
            font-size: 18px;
            padding: 6px;
            border-radius: 8px;
        }
        .icon-btn:hover { background: rgba(0,0,0,0.03); }

        .send-btn {
            background: #1677ff;
            color: #fff;
            border-radius: 999px;
            border: none;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(22,119,255,0.18);
        }
        .send-btn:disabled { opacity: 0.5; cursor: not-allowed; box-shadow:none; }

        /* small screens */
        @media (max-width: 900px) {
            #userList { width: 100%; position: absolute; left:0; top:0; bottom:0; z-index:200; transform: translateX(-100%); transition: transform .22s ease; }
            #userList.show { transform: translateX(0); box-shadow: 0 12px 40px rgba(2,6,23,0.12); }
            .chat-wrapper { margin:0; height:100vh; border-radius:0; }
        }
    </style>
</head>
<body>
<div class="chat-wrapper">
    <!-- SIDEBAR -->
    <div id="userList" aria-label="Danh sách bác sĩ">
        <div class="sidebar-top">
            <h3><i class="fas fa-user-md" style="color:#1677ff;margin-right:8px;"></i> Bác Sĩ & Chuyên Gia</h3>
            <button id="toggleContacts" class="icon-btn" style="margin-left:auto;display:none;"><i class="fas fa-bars"></i></button>
        </div>

        <div class="search-box">
            <input type="text" id="searchContacts" placeholder="Tìm kiếm bác sĩ hoặc chuyên gia...">
        </div>

        <div class="users-list" id="usersListInner">
            <?php
            include_once("Controllers/ctaikhoan.php");
            $p = new ctaiKhoan();
            $tentk1 = $_SESSION['user']['tentk'];
            $tbl = $p->gettkbacsi($tentk1);

            if ($tbl && $tbl->num_rows > 0) {
                while ($row = $tbl->fetch_assoc()) {
                    $img = !empty($row['img']) ? htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8') : 'default.png';
                    $roleLabel = ($row['vaitro'] === 'bacsi') ? 'Bác sĩ' : 'Chuyên gia';
                    $roleIcon = ($row['vaitro'] === 'bacsi') ? 'fa-stethoscope' : 'fa-user-tie';

                    $tentk_safe = htmlspecialchars($row['tentk'], ENT_QUOTES, 'UTF-8');
                    $hoten_safe = htmlspecialchars($row['hoten'], ENT_QUOTES, 'UTF-8');
                    $vaitro_safe = htmlspecialchars($row['vaitro'], ENT_QUOTES, 'UTF-8');

                    echo "<div class='user' data-tentk='{$tentk_safe}' data-name='{$hoten_safe}' data-vaitro='{$vaitro_safe}'>
                            <div class='user-avatar'><img src='Assets/img/{$img}' alt='Avatar' onerror=\"this.src='Assets/img/default.png'\"></div>
                            <div class='user-info'>
                                <strong>{$hoten_safe}</strong>
                                <small><i class='fas {$roleIcon}'></i> {$roleLabel}</small>
                            </div>
                        </div>";
                }
            } else {
                echo "<div style='padding:24px;text-align:center;color:#6b7280;'><i class='fas fa-user-slash' style='font-size:28px;margin-bottom:8px;display:block;'></i><p>Chưa có bác sĩ nào</p></div>";
            }
            ?>
        </div>
    </div>

    <!-- CHAT AREA -->
    <div id="chatContainer">
        <div id="chatHeader">
            <div class="header-avatar">
                <img id="headerAvatar" src="Assets/img/default.png" alt="avatar">
            </div>
            <div class="header-info">
                <div id="headerText">Chọn bác sĩ để bắt đầu</div>
                <div id="headerSub">Sẵn sàng để tư vấn</div>
            </div>
            <div id="connectionStatus" class="connection-status" style="display:none;font-size:13px;color:#6b7280;">Đang kết nối</div>
        </div>

        <div id="chatMessages">
            <div class="message-day">Chưa có cuộc trò chuyện</div>
        </div>

        <div class="input-container">
            <div class="input-inner">
                <button class="icon-btn" id="attachBtn" title="Gửi file (PDF)"><i class="fas fa-paperclip"></i></button>
                <textarea id="messageInput" rows="1" placeholder="Nhập tin nhắn..." disabled></textarea>
                <input type="file" id="fileInput" accept="application/pdf" style="display:none;">
            </div>
            <button id="sendButton" class="send-btn" disabled><i class="fas fa-paper-plane" style="margin-right:8px;"></i>Gửi</button>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
/* Globals */
let socket = null;
let user = {
    tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>",
    vaitro: 1
};
let currentDoctor = null;
let messages = {}; // messages by partner tentk

/* Utilities */
function formatTime(iso) {
    try {
        const d = new Date(iso);
        const hh = String(d.getHours()).padStart(2,'0');
        const mm = String(d.getMinutes()).padStart(2,'0');
        return `${hh}:${mm}`;
    } catch(e) { return ''; }
}

function updateConnectionStatusClass(text, visible=true) {
    const el = $('#connectionStatus');
    el.text(text);
    el.css('display', visible ? 'block' : 'none');
}

/* WebSocket connect */
function connectWebSocket() {
    updateConnectionStatusClass('Đang kết nối...', true);

    try {
        socket = new WebSocket("wss://hanhphuc.site/ws");
    } catch (e) {
        console.error('WS init error', e);
        updateConnectionStatusClass('Lỗi kết nối', true);
        setTimeout(connectWebSocket, 3000);
        return;
    }

    socket.onopen = function() {
        console.log('WebSocket opened');
        updateConnectionStatusClass('Đã kết nối', true);
        try {
            socket.send(JSON.stringify({ command: 'register', username: user.tentk, role: user.vaitro }));
        } catch (e) { console.warn('Send register failed', e); }

        const savedDoctor = localStorage.getItem('selectedDoctor');
        const savedDoctorName = localStorage.getItem('selectedDoctorName');
        const savedVaitro = localStorage.getItem('selectedVaitro') || 'bacsi';
        if (savedDoctor && savedDoctorName) {
            setTimeout(function(){ selectUser(savedDoctor, savedDoctorName, savedVaitro); }, 500);
        }
    };

    socket.onmessage = function(event) {
        let data;
        try { data = JSON.parse(event.data); } catch(e){ console.warn('Invalid WS message', event.data); return; }
        console.log('WS received', data);

        if (data.command === 'messages') {
            const partner = data.receiver_tentk || data.partner;
            messages[partner] = data.messages || [];
            if (currentDoctor && currentDoctor.tentk === partner) {
                renderMessages(messages[partner]);
            }
        } else if (data.command === 'receive') {
            const sender = data.sender || data.from;
            if (!messages[sender]) messages[sender] = [];
            const msgObj = {
                sender: sender,
                message: data.message || '',
                filename: data.filename || data.fileName || null,
                url: data.url || data.fileUrl || null,
                thoigiangui: data.thoigiangui || new Date().toISOString()
            };
            messages[sender].push(msgObj);
            if (currentDoctor && currentDoctor.tentk === sender) {
                displayMessage(msgObj);
            } else {
                // optional: highlight contact (not implemented)
            }
        }
    };

    socket.onerror = function(err) {
        console.error('WebSocket error', err);
        updateConnectionStatusClass('Lỗi kết nối', true);
    };

    socket.onclose = function(ev) {
        console.warn('WebSocket closed', ev.code, ev.reason);
        updateConnectionStatusClass('Đang kết nối lại...', true);
        setTimeout(connectWebSocket, 3000);
    };
}

/* Selecting a user from list */
function selectUser(tentk, name, vaitro) {
    if (!tentk || !name) return;

    if (!socket || socket.readyState !== WebSocket.OPEN) {
        if (socket && socket.readyState === WebSocket.CONNECTING) {
            setTimeout(function(){ selectUser(tentk, name, vaitro); }, 800);
            return;
        }
        alert('Kết nối bị gián đoạn!');
        return;
    }

    $('.user').removeClass('active');
    $('.user').filter(function(){ return $(this).data('tentk') === tentk; }).addClass('active');

    currentDoctor = { tentk: tentk, name: name, vaitro: vaitro };
    localStorage.setItem('selectedDoctor', tentk);
    localStorage.setItem('selectedDoctorName', name);
    localStorage.setItem('selectedVaitro', vaitro);

    $('#headerText').text(name);
    $('#headerAvatar').attr('src', $('.user.active .user-avatar img').attr('src') || 'Assets/img/default.png');

    $('#messageInput').prop('disabled', false).focus();
    $('#sendButton').prop('disabled', false);
    $('#attachBtn').prop('disabled', false);

    $('#chatMessages').html('<div class="message-day"><i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Đang tải...</div>');

    if (messages[tentk] && messages[tentk].length) {
        renderMessages(messages[tentk]);
    }

    // Ask server to load messages
    try {
        socket.send(JSON.stringify({ command: 'load_messages', tentk: user.tentk, receiver_tentk: tentk }));
    } catch(e) {
        console.warn('load_messages send failed', e);
    }
}

function renderMessages(arr) {
    const container = $('#chatMessages');
    container.html('');
    if (!arr || arr.length === 0) {
        container.html('<div class="message-day">Chưa có tin nhắn</div>');
        scrollToBottom();
        return;
    }
    arr.forEach(m => displayMessage(m));
    scrollToBottom();
}

function displayMessage(msg) {
    const container = $('#chatMessages');
    const isMe = msg.sender === user.tentk;
    const wrapper = $('<div class="message-wrapper"></div>').addClass(isMe ? 'sent' : 'received');

    // optional avatar for received
    if (!isMe) {
        const avatarUrl = $('.user.active .user-avatar img').attr('src') || 'Assets/img/default.png';
        wrapper.append($('<img>').addClass('avatar-small').attr('src', avatarUrl).on('error', function(){ $(this).attr('src','Assets/img/default.png'); }));
    } else {
        // keep space for alignment
        wrapper.append($('<div>').css('width','32px'));
    }

    const bubble = $('<div class="bubble"></div>');

    // file message detection: if explicit filename or url provided prefer them
    if ((msg.filename && msg.url) || (msg.message && msg.message === '[FILE]')) {
        const filename = msg.filename || 'document.pdf';
        const url = msg.url || msg.fileUrl || (typeof msg.message === 'string' ? msg.message.replace(/^\[FILE\]\s*/,'') : '');
        const a = $('<a target="_blank" rel="noopener"></a>').attr('href', url || '#');
        a.append($('<i>').addClass('fas fa-file-pdf'));
        a.append($('<span>').text(' ' + filename));
        bubble.append(a);
    } else {
        // plain text
        bubble.text(msg.message || '');
    }

    const meta = $('<span class="message-meta"></span>').text(formatTime(msg.thoigiangui || new Date().toISOString()));
    bubble.append(meta);

    wrapper.append(bubble);

    container.append(wrapper);
    scrollToBottom();
}

function scrollToBottom() {
    const box = $('#chatMessages');
    box.stop().animate({ scrollTop: box[0].scrollHeight }, 300);
}

/* Sending message (text) */
function sendMessage() {
    const text = $('#messageInput').val().trim();
    if (!text || !currentDoctor) return;

    if (!socket || socket.readyState !== WebSocket.OPEN) {
        alert('❌ Kết nối bị gián đoạn!');
        return;
    }

    // Check appointment via AJAX before sending (as original)
    $.ajax({
        url: 'Ajax/getlichhen.php',
        type: 'POST',
        dataType: 'json',
        data: { bs: currentDoctor.tentk, bn: user.tentk },
        success: function(response){
            if (response && response.status === 'ok') {
                const msg = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentDoctor.tentk,
                    message: text
                };
                try { socket.send(JSON.stringify(msg)); } catch(e){ console.warn('socket send failed', e); }

                if (!messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                const localMsg = { sender: user.tentk, message: text, thoigiangui: new Date().toISOString() };
                messages[currentDoctor.tentk].push(localMsg);
                displayMessage(localMsg);

                $('#messageInput').val('');
            } else {
                alert(response.message || 'Bạn chưa có lịch hẹn!');
            }
        },
        error: function() {
            alert('Không thể kiểm tra lịch hẹn. Vui lòng thử lại.');
        }
    });
}

/* File upload flow */
$('#attachBtn').on('click', function(){
    if (!currentDoctor) {
        alert('Chọn bác sĩ trước!');
        return;
    }
    $('#fileInput').click();
});

$('#fileInput').on('change', function(){
    const file = this.files[0];
    if (!file) return;

    if (file.type !== 'application/pdf') {
        alert('⚠️ Chỉ chấp nhận PDF!');
        $(this).val('');
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        alert('⚠️ File quá lớn (max 10MB)!');
        $(this).val('');
        return;
    }

    if (!socket || socket.readyState !== WebSocket.OPEN) {
        alert('❌ Kết nối bị gián đoạn!');
        $(this).val('');
        return;
    }

    const fd = new FormData();
    fd.append('file', file);
    fd.append('receiver', currentDoctor.tentk);
    fd.append('sender', user.tentk);

    const orig = $('#attachBtn').html();
    $('#attachBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
        url: 'Views/benhnhan/pages/tinnhan/uploadFile.php',
        method: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res) {
            // Accept multiple response shapes for robustness
            const ok = res && (res.success === true || res.status === 'ok' || res.success == 1);
            const filename = res && (res.filename || res.fileName || (res.data && res.data.filename));
            const url = res && (res.url || res.fileUrl || (res.data && res.data.url));

            if (ok && (url || res.messageUrl || res.fileUrl)) {
                const payload = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentDoctor.tentk,
                    message: '[FILE]',
                    filename: filename || (file && file.name),
                    url: url || res.messageUrl || res.fileUrl
                };
                try { socket.send(JSON.stringify(payload)); } catch(e){ console.warn('WS send file meta failed', e); }

                if (!messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                const localMsg = {
                    sender: user.tentk,
                    message: '[FILE]',
                    filename: payload.filename,
                    url: payload.url,
                    thoigiangui: new Date().toISOString()
                };
                messages[currentDoctor.tentk].push(localMsg);
                displayMessage(localMsg);
            } else {
                alert(res && res.message ? res.message : '❌ Upload thất bại!');
            }
        },
        error: function(xhr, st, err) {
            console.error('Upload error', err);
            alert('❌ Upload thất bại!');
        },
        complete: function() {
            $('#attachBtn').prop('disabled', false).html(orig);
            $('#fileInput').val('');
        }
    });
});

/* Events */
$('#sendButton').on('click', sendMessage);
$('#messageInput').on('keypress', function(e){
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

$(document).on('click', '.user', function(){
    const t = $(this).data('tentk');
    const n = $(this).data('name');
    const v = $(this).data('vaitro');
    selectUser(t, n, v);
});

$('#searchContacts').on('input', function(){
    const q = $(this).val().toLowerCase();
    $('.user').each(function(){
        const name = $(this).data('name') ? String($(this).data('name')).toLowerCase() : '';
        if (name.indexOf(q) === -1) $(this).hide(); else $(this).show();
    });
});

/* Init */
$(function(){
    connectWebSocket();
    // allow toggle on small screens
    $('#toggleContacts').on('click', function(){ $('#userList').toggleClass('show'); });
});
</script>
</body>
</html>