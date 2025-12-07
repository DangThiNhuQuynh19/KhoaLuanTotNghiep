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

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            color: #0f1724;
            overflow: hidden;
        }

        .chat-wrapper {
            max-width: 1200px;
            margin: 18px auto;
            height: calc(100vh - 36px);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            background: white;
            display: flex;
            margin-top: calc(var(--site-header-height) + var(--chat-gutter));
            margin-bottom: calc(var(--site-footer-height) + var(--chat-gutter));
        }

        /* Sidebar */
        #userList { width: 320px; border-right: 1px solid #e6e9ee; background: #fff; display:flex; flex-direction:column; }
        .sidebar-top { padding: 18px; display:flex; align-items:center; gap:12px; border-bottom: 1px solid #f0f2f5; }
        .sidebar-top h3 { font-size:16px; font-weight:600; color:#0f1724; }
        .search-box { padding: 10px 16px; border-bottom:1px solid #f0f2f5; }
        .search-box input { width:100%; padding:10px 12px; border-radius:999px; border:1px solid #e6e9ee; background:#f7f9fb; font-size:14px; }

        .users-list { overflow-y:auto; padding:8px; }
        .users-list::-webkit-scrollbar { width: 8px; }
        .users-list::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.06); border-radius:6px; }

        .user { display:flex; gap:12px; align-items:center; padding:12px; border-radius:12px; cursor:pointer; transition: background .12s, transform .12s; }
        .user:hover { background:#f5f7fa; transform: translateX(2px); }
        .user.active { background:#eef6ff; box-shadow: inset 0 0 0 1px rgba(22,119,255,0.06); }

        .user-avatar img { width:52px; height:52px; border-radius:50%; object-fit:cover; border:2px solid #f0f2f5; }
        .user-info strong { display:block; font-size:13px; font-weight:600; color:#0f1724; }
        .user-info small { color:#6b7280; font-size:10px; margin-top:4px; display:flex; gap:8px; align-items:center; }

        /* Chat area */
        #chatContainer { flex:1; display:flex; flex-direction:column; min-width:0; min-height:0; }

        /* HEADER padding 50px as requested */
        #chatHeader {
            padding: 50px;
            display:flex;
            align-items:center;
            gap:12px;
            border-bottom: 1px solid #f0f2f5;
            background: linear-gradient(90deg,#fff,#fbfdff);
        }

        .header-avatar img { width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid transparent; }
        .header-info { flex:1; display:flex; flex-direction:column; }
        #headerText { font-weight:700; font-size:14px; color:#0f1724; }
        #headerSub { color:#6b7280; font-size:13px; margin-top:6px; }

        /* Messages container with padding 50px */
        #chatMessages {
            flex:1;
            padding: 18px;
            overflow-y:auto;
            background: linear-gradient(#f6f7fb,#f6f7fb);
            min-height:0; /* ensure flex child can scroll */
        }
        #chatMessages::-webkit-scrollbar { width: 10px; }
        #chatMessages::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.06); border-radius:6px; }

        .message-day { text-align:center; color:#9aa3b2; font-size:12px; margin:8px 0 18px; }

        /* New simpler text-message styles (kept visually compatible with UI) */
        .message {
            display: block;
            max-width: 68%;
            padding: 10px 14px;
            margin-bottom: 12px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.35;
            clear: both;
            word-wrap: break-word;
            box-shadow: 0 4px 10px rgba(2,6,23,0.04);
        }
        .patient {
            background: linear-gradient(135deg, #eef8ff, #e6f3ff);
            color: #06344a;
            float: right;
            border-bottom-right-radius: 6px;
        }
        .doctor {
            background: #ffffff;
            color: #0f1724;
            float: left;
            border-bottom-left-radius: 6px;
        }
        .message a { color: inherit; text-decoration:none; display:inline-flex; gap:10px; align-items:center; }
        .message .fa-file-pdf { color: #c53030; font-size:20px; margin-right:8px; }

        .message-meta { display:block; font-size:11px; color: rgba(15,23,36,0.5); margin-top:6px; text-align:right; }
        .doctor .message-meta { text-align:left; color:#6b7280; }

        /* INPUT: padding 50px footer as requested */
        .input-container {
            padding: 50px;
            border-top: 1px solid #eef2f7;
            background: #fff;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .input-inner { display:flex; align-items:center; gap:10px; flex:1; background:#f4f6fb;  border-radius:999px; border:1px solid #e6e9ee; }
        .input-inner textarea { border:none; background:transparent; outline:none; resize:none; font-size:14px; width:100%; color:#0f1724; padding:6px 8px; min-height:36px; max-height:120px; }

        .icon-btn { background:transparent; border:none; cursor:pointer; color:#55607a; font-size:18px; padding:6px; border-radius:8px; }
        .icon-btn:hover { background: rgba(0,0,0,0.03); }
        .send-btn { background: #1677ff; color:#fff; border-radius:999px; border:none; padding:5px 10px; font-weight:600; cursor:pointer; box-shadow:0 6px 18px rgba(22,119,255,0.14); }
        .send-btn:disabled { opacity:0.5; cursor:not-allowed; box-shadow:none; }

        @media (max-width: 100%) {
            #userList { width:100%; position:absolute; left:0; top:0; bottom:0; z-index:200; transform:translateX(-100%); transition:transform .22s ease; }
            #userList.show { transform:translateX(0); box-shadow:0 12px 40px rgba(2,6,23,0.12); }
            .chat-wrapper { margin:0; height:100vh; border-radius:0; }
            /* reduce big paddings on small screens */
            #chatHeader, #chatMessages, .input-container { padding: 18px; }
            .message { max-width: 90%; }
        }

        :root{
        --site-header-height: 90px; 
        --site-footer-height: 0px;  
        --chat-input-height: 88px; 
        --chat-gap: 18px;
        }

        .chat-wrapper {
        position: relative;
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
        margin-top: calc(var(--chat-gap)); 
        margin-bottom: calc(var(--chat-gap));
        height: calc(100vh - var(--site-header-height) - var(--site-footer-height) - (var(--chat-gap) * 2));
        display: flex;
        overflow: hidden;
        box-sizing: border-box;
        }

        #chatHeader {
        position: sticky;
        top: 0;
        z-index: 3;
        background: linear-gradient(90deg,#fff,#fbfdff);
        }

        
        #chatMessages {
        flex: 1;
        min-height: 0; 
        overflow-y: auto;
        padding: 20px;
        padding-bottom: calc(var(--chat-input-height) + 10px);
        background: linear-gradient(#f6f7fb,#f6f7fb);
        }

        .input-container {
        position: sticky;
        bottom: 0;
        z-index: 3;
        background: #fff;
        box-shadow: 0 -6px 24px rgba(2,6,23,0.03);
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 5px 10px; /* có thể giảm nếu muốn input nhỏ lại */
        box-sizing: border-box;
        }

        /* Giữ input-inner hiển thị đúng khi keyboard mobile bật */
        .input-inner { z-index: 7; }

        /* Tinh chỉnh cho tin nhắn không dính sát header/footer */
        .message-day { margin-top: 6px; margin-bottom: 10px; }
        .message { margin-bottom: 12px; }
        @media (max-width: 100%) {
        :root {
            --site-header-height: 64px;
            --chat-input-height: 66px;
        }
        .chat-wrapper {
            height: calc(100vh - var(--site-header-height) - var(--site-footer-height));
            margin-top: 0;
            margin-bottom: 0;
            border-radius: 0;
        }
        #chatMessages { padding: 18px; padding-bottom: calc(var(--chat-input-height) + 18px); }
        .input-container { padding: 12px 16px; }
        }
    </style>
</head>
<body>
<div class="chat-wrapper">
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
                echo "<div style='padding:24px;text-align:center;color:#6b7280;'>
                        <i class='fas fa-user-slash' style='font-size:28px;margin-bottom:8px;display:block;'></i>
                        <p>Chưa có bác sĩ nào</p>
                      </div>";
            }
            ?>
        </div>
    </div>

    <div id="chatContainer">
        <div id="chatHeader">
            <div class="header-avatar">
                <img id="headerAvatar" src="Assets/img/default.png" alt="avatar">
            </div>
            <div class="header-info">
                <div id="headerText">Chọn bác sĩ để bắt đầu</div>
                <div id="headerSub">Sẵn sàng để tư vấn</div>
            </div>
            <div id="connectionStatus" style="display:none;font-size:13px;color:#6b7280;">Đang kết nối</div>
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

/* Local file mapping stored in localStorage to preserve filename/url across reloads */
const FILE_MAP_KEY = 'chat_file_map_v1';

function loadFileMap() {
    try {
        const raw = localStorage.getItem(FILE_MAP_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch (e) { return []; }
}
function saveFileMap(map) {
    try { localStorage.setItem(FILE_MAP_KEY, JSON.stringify(map)); } catch (e) { console.warn('saveFileMap failed', e); }
}
// Normalize url when saving mapping
function addFileMapEntry(sender, receiver, thoigiangui, filename, url) {
    const map = loadFileMap();
    const abs = toAbsoluteUrl(url) || url || null;
    map.push({ sender, receiver, thoigiangui, filename, url: abs });
    saveFileMap(map);
}
function findFileMapEntryByUrl(url) {
    if (!url) return null;
    const map = loadFileMap();
    const abs = toAbsoluteUrl(url) || url;
    return map.find(e => e.url && e.url === abs) || null;
}
function findFileMapEntryFuzzy(sender, receiver, thoigiangui) {
    if (!thoigiangui) return null;
    const map = loadFileMap();
    const want = new Date(thoigiangui).getTime();
    // find same participants and within 10s
    let best = null;
    let bestDiff = Infinity;
    map.forEach(e => {
        if (e.sender === sender && e.receiver === receiver && e.thoigiangui) {
            const t = new Date(e.thoigiangui).getTime();
            const diff = Math.abs(t - want);
            if (diff < 10000 && diff < bestDiff) {
                bestDiff = diff;
                best = e;
            }
        }
    });
    return best;
}

/* Utilities */
function formatTime(iso) {
    try {
        const d = new Date(iso);
        const hh = String(d.getHours()).padStart(2,'0');
        const mm = String(d.getMinutes()).padStart(2,'0');
        return `${hh}:${mm}`;
    } catch(e) { return ''; }
}
function toAbsoluteUrl(maybeUrl) {
    if (!maybeUrl) return null;
    try { return new URL(maybeUrl, window.location.href).href; } catch (e) { return maybeUrl; }
}
function deriveFilenameFromUrl(url) {
    if (!url) return null;
    try {
        const pathname = new URL(url, window.location.href).pathname;
        const parts = pathname.split('/');
        let name = parts.pop() || parts.pop();
        return decodeURIComponent(name || '');
    } catch (e) {
        const parts = url.split('/');
        return decodeURIComponent(parts.pop() || '');
    }
}

/* Attempt to repair a single file-message using local maps; return repaired object or null if not usable */
function repairFileMessage(msg) {
    // ensure url absolute
    if (msg.url) msg.url = toAbsoluteUrl(msg.url);
    if (!msg.filename && msg.url) msg.filename = deriveFilenameFromUrl(msg.url);

    // fuzzy map by participants + timestamp
    if ((!msg.filename || !msg.url) && msg.thoigiangui) {
        const mapEntry = findFileMapEntryFuzzy(msg.sender || user.tentk, msg.receiver || (currentDoctor ? currentDoctor.tentk : null), msg.thoigiangui);
        if (mapEntry) {
            if (!msg.filename && mapEntry.filename) msg.filename = mapEntry.filename;
            if (!msg.url && mapEntry.url) msg.url = toAbsoluteUrl(mapEntry.url);
        }
    }

    // if url exists, try lookup by url
    if (msg.url) {
        const byUrl = findFileMapEntryByUrl(msg.url);
        if (byUrl && byUrl.filename) msg.filename = msg.filename || byUrl.filename;
    }

    // if still no url and no filename for [FILE] message, treat as unusable (avoid showing generic placeholder)
    if ((msg.message && msg.message.toString().startsWith('[FILE]')) && !msg.url && !msg.filename) {
        return null;
    }
    return msg;
}

/* Remove duplicates and repair file messages for an array of messages */
function dedupeAndRepairMessages(arr) {
    const out = [];
    const seen = new Set();
    arr.forEach(m => {
        // shallow clone to avoid mutating original
        const msg = Object.assign({}, m);
        // Try to repair file messages
        const txt = (msg.message || '').toString();
        if (txt.startsWith('[FILE]') || msg.filename || msg.url || msg.fileUrl) {
            // normalize url property name
            msg.url = msg.url || msg.fileUrl || null;
            const repaired = repairFileMessage(msg);
            if (!repaired) return; // skip unusable file message
            // use repaired fields
            msg.filename = repaired.filename;
            msg.url = repaired.url;
        }

        // build dedupe key: prefer url, then filename, then message text; include sender + rounded timestamp (2s)
        const timeKey = msg.thoigiangui ? Math.floor(new Date(msg.thoigiangui).getTime() / 2000) : '';
        const keyBase = (msg.url || msg.filename || (msg.message || '')) + '|' + (msg.sender || '') + '|' + timeKey;
        if (seen.has(keyBase)) return;
        seen.add(keyBase);

        out.push(msg);
    });
    return out;
}

/* WebSocket connect */
function connectWebSocket() {
    $('#connectionStatus').text('Đang kết nối').show();
    try {
        socket = new WebSocket("wss://hanhphuc.site/ws");
    } catch (e) {
        console.error('WS init error', e);
        $('#connectionStatus').text('Lỗi kết nối').show();
        setTimeout(connectWebSocket, 3000);
        return;
    }

    socket.onopen = function() {
        $('#connectionStatus').text('Đã kết nối').show();
        try { socket.send(JSON.stringify({ command: 'register', username: user.tentk, role: user.vaitro })); } catch(e){}
        const savedDoctor = localStorage.getItem('selectedDoctor');
        const savedDoctorName = localStorage.getItem('selectedDoctorName');
        const savedVaitro = localStorage.getItem('selectedVaitro') || 'bacsi';
        if (savedDoctor && savedDoctorName) {
            setTimeout(function(){ selectUser(savedDoctor, savedDoctorName, savedVaitro); }, 500);
        }
    };

    socket.onmessage = function(event) {
        let data;
        try {
            data = JSON.parse(event.data);
        } catch (e) {
            console.warn('Invalid WS message', event.data);
            return;
        }

        // handle messages payload
        if (data.command === 'messages') {
            const partner = data.receiver_tentk || data.partner;
            const raw = data.messages || [];
            // normalize file metadata similar to previous logic
            const normalized = raw.map(m => {
                const nm = Object.assign({}, m);
                const txt = (nm.message || '').toString();
                if (txt.startsWith('[FILE]') || nm.filename || nm.url || nm.fileUrl) {
                    let u = nm.url || nm.fileUrl || null;
                    if (!u) {
                        const after = txt.replace(/^\[FILE\]\s*/i, '').trim();
                        if (after) u = after;
                    }
                    if (u) nm.url = toAbsoluteUrl(u);
                    if (!nm.filename && nm.url) nm.filename = deriveFilenameFromUrl(nm.url);
                    // nm.message remains '[FILE]' as marker
                    nm.message = '[FILE]';
                }
                return nm;
            });

            // Repair and dedupe
            messages[partner] = dedupeAndRepairMessages(normalized);
            if (currentDoctor && currentDoctor.tentk === partner) renderMessages(messages[partner]);
        } else if (data.command === 'receive' || data.command === 'message') {
            const sender = data.sender || data.from;
            if (!messages[sender]) messages[sender] = [];
            const msgObj = {
                sender: sender,
                message: data.message || '',
                filename: data.filename || data.fileName || null,
                url: data.url || data.fileUrl || null,
                thoigiangui: data.thoigiangui || new Date().toISOString()
            };
            // normalize & repair file message; if repair returns null -> ignore
            const txt = (msgObj.message || '').toString();
            if (txt.startsWith('[FILE]') || msgObj.filename || msgObj.url) {
                const tmp = repairFileMessage(msgObj);
                if (!tmp) {
                    console.warn('Ignored incoming file message with no url/filename', msgObj);
                    return;
                }
                msgObj.filename = tmp.filename;
                msgObj.url = tmp.url;
            }
            messages[sender].push(msgObj);
            if (currentDoctor && currentDoctor.tentk === sender) displayTextMessage(msgObj);
        } else if (data.command === 'status') {
            console.log('status', data);
        }
    };

    socket.onerror = function(err) {
        console.error('WebSocket error', err);
        $('#connectionStatus').text('Lỗi kết nối').show();
    };

    socket.onclose = function(ev) {
        console.warn('WebSocket closed', ev.code, ev.reason);
        $('#connectionStatus').text('Đang kết nối lại...').show();
        setTimeout(connectWebSocket, 3000);
    };
}

/* Selecting a user */
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

    if (messages[tentk] && messages[tentk].length) renderMessages(messages[tentk]);

    try { socket.send(JSON.stringify({ command: 'load_messages', tentk: user.tentk, receiver_tentk: tentk })); } catch(e){ console.warn(e); }
}

/* Render messages using simple text-style blocks (patient/doctor) */
function renderMessages(arr) {
    const container = $('#chatMessages');
    container.html('');
    if (!arr || arr.length === 0) {
        container.html('<div class="message-day">Chưa có tin nhắn</div>');
        scrollToBottom();
        return;
    }
    arr.forEach(m => displayTextMessage(m));
    scrollToBottom();
}

/* Display a single message as text block (keeps UI consistent but simple) */
function displayTextMessage(msg) {
    const container = $('#chatMessages');
    const isMe = msg.sender === user.tentk;
    const msgDiv = $('<div>').addClass('message').addClass(isMe ? 'patient' : 'doctor');

    const isFile = (msg.message && msg.message.toString().startsWith('[FILE]')) || msg.filename || msg.url;
    if (isFile) {
        // ensure we have at least filename or url (repair function already tried)
        let url = msg.url || null;
        if (!url) {
            const after = (msg.message || '').toString().replace(/^\[FILE\]\s*/i, '').trim();
            if (after) url = after;
        }
        url = toAbsoluteUrl(url);
        let filename = msg.filename || deriveFilenameFromUrl(url) || null;

        // If still no filename and no url -> skip (prevents 'Tập tin' undefined)
        if (!filename && !url) {
            console.warn('Skipping file message because no filename/url', msg);
            return;
        }

        const a = $('<a target="_blank" rel="noopener noreferrer"></a>').attr('href', url || '#');
        a.append($('<i>').addClass('fas fa-file-pdf'));
        a.append($('<span>').text(' ' + (filename || 'Tập tin')));
        msgDiv.append(a);
    } else {
        msgDiv.text(msg.message || '');
    }

    const meta = $('<div>').addClass('message-meta').text(formatTime(msg.thoigiangui || new Date().toISOString()));
    msgDiv.append(meta);

    container.append(msgDiv);
    scrollToBottom();
}

function scrollToBottom() {
    const box = $('#chatMessages');
    requestAnimationFrame(function(){
        try { box.scrollTop(box[0].scrollHeight); } catch(e){}
    });
}

/* Sending text */
function sendMessage() {
    const text = $('#messageInput').val().trim();
    if (!text || !currentDoctor) return;
    if (!socket || socket.readyState !== WebSocket.OPEN) {
        alert('❌ Kết nối bị gián đoạn!');
        return;
    }

    $.ajax({
        url: 'Ajax/getlichhen.php',
        type: 'POST',
        dataType: 'json',
        data: { bs: currentDoctor.tentk, bn: user.tentk },
        success: function(response){
            if (response && response.status === 'ok') {
                const msg = { command: 'send', sender: user.tentk, receiver: currentDoctor.tentk, message: text };
                try { socket.send(JSON.stringify(msg)); } catch(e){}
                if (!messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                const localMsg = { sender: user.tentk, message: text, thoigiangui: new Date().toISOString() };
                messages[currentDoctor.tentk].push(localMsg);
                displayTextMessage(localMsg);
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

/* File upload flow (kept using uploadFile.php as in previous code) */
$('#attachBtn').on('click', function(){
    if (!currentDoctor) { alert('Chọn bác sĩ trước!'); return; }
    $('#fileInput').click();
});

$('#fileInput').change(function(){
    const file = this.files[0];
    if(!file) return;

    if(file.type !== "application/pdf"){
        alert("Chỉ chấp nhận file PDF!");
        $(this).val('');
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        alert("File quá lớn (max 10MB)!");
        $(this).val('');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('receiver', currentDoctor.tentk);

    const origHtml = $('#attachBtn').html();
    $('#attachBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    $.ajax({
        url: 'Views/benhnhan/pages/tinnhan/uploadFile.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res){
            if(res && res.success){
                const filename = res.filename || res.saved_name || file.name;
                const url = res.url || res.fileUrl || null;
                const ts = new Date().toISOString();

                // store mapping locally so reloads can show original filename (normalize url)
                if (url) addFileMapEntry(user.tentk, currentDoctor.tentk, ts, filename, url);

                // send message metadata over WS
                const payload = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentDoctor.tentk,
                    message: '[FILE]',
                    filename: filename,
                    url: toAbsoluteUrl(url) || url,
                    thoigiangui: ts
                };
                try { if(socket && socket.readyState === WebSocket.OPEN) socket.send(JSON.stringify(payload)); } catch(e){ console.warn('WS send file meta failed', e); }

                // locally display (as text-style)
                const localMsg = { sender: user.tentk, message: '[FILE]', filename: filename, url: toAbsoluteUrl(url) || url, thoigiangui: ts };
                if (!messages[currentDoctor.tentk]) messages[currentDoctor.tentk] = [];
                messages[currentDoctor.tentk].push(localMsg);
                displayTextMessage(localMsg);
            } else {
                alert("Upload thất bại: " + (res && res.error ? res.error : 'Không rõ lỗi'));
            }
        },
        error: function(xhr, st, err){
            console.error('Upload error', err, xhr.responseText);
            alert("Upload thất bại!");
        },
        complete: function(){
            $('#attachBtn').prop('disabled', false).html(origHtml);
            $('#fileInput').val('');
        }
    });
});

/* Events */
$('#sendButton').on('click', sendMessage);
$('#messageInput').on('keypress', function(e){
    if (e.which === 13 && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});
$(document).on('click', '.user', function(){
    const t = $(this).data('tentk');
    const n = $(this).data('name');
    const v = $(this).data('vaitro');
    selectUser(t,n,v);
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
    $('#toggleContacts').on('click', function(){ $('#userList').toggleClass('show'); });
});
</script>
</body>
</html>