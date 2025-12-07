<?php
session_start();
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
            padding: 20px;
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
            padding: 20px;
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
                    $hoten = htmlspecialchars($row['hoten'], ENT_QUOTES, 'UTF-8');
                    $tentk_row = htmlspecialchars($row['tentk'], ENT_QUOTES, 'UTF-8');
                    echo "<div class='user' data-tentk='{$tentk_row}' data-name='{$hoten}'><div style='width:44px;height:44px;border-radius:50%;background:#f0f0f0;margin-right:10px;flex-shrink:0;'></div><div><strong>{$hoten}</strong></div></div>";
                }
            } else {
                echo "<p style='padding:12px;color:#666;margin:0'>Không có bệnh nhân nào.</p>";
            }
        } else {
            echo "<p style='padding:12px;color:#c0392b;margin:0'>Không tìm thấy thông tin bác sĩ từ tài khoản đăng nhập.</p>";
        }
        ?>
    </div>

    <div id="chatContainer">
        <div id="chatHeader">
            <div class="header-avatar"><img id="headerAvatar" src="Assets/img/default.png" alt="avatar"></div>
            <div class="header-info">
                <div id="headerText">Chọn bệnh nhân để bắt chuyện</div>
                <div id="headerSub">Sẵn sàng để tư vấn</div>
            </div>
            <div id="connectionStatus" style="font-size:13px;color:#6b7280;display:none">Đang kết nối</div>
        </div>

        <div id="chatMessages">
            <div class="message-day" style="text-align:center;color:#9aa3b2;">Chưa có cuộc trò chuyện</div>
        </div>

        <div class="input-container">
            <div class="input-inner">
                <button id="attachBtn" class="icon-btn" title="Gửi file (PDF)"><i class="fas fa-paperclip"></i></button>
                <textarea id="messageInput" placeholder="Nhập tin nhắn..." rows="1" disabled></textarea>
                <input type="file" id="fileInput" accept="application/pdf" style="display:none;">
            </div>
            <button id="sendButton" class="send-btn" disabled><i class="fas fa-paper-plane" style="margin-right:8px;"></i>Gửi</button>
        </div>
    </div>
</div>

<!-- Font Awesome + jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
/* Globals */
let socket = null;
let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 }; // role 0 = doctor
let currentPatient = null;
let messages = {}; // messages keyed by patient tentk

/* Local map so file names persist across reloads if server metadata missing */
const FILE_MAP_KEY = 'chat_file_map_v1';
function loadFileMap(){ try{ const r = localStorage.getItem(FILE_MAP_KEY); return r?JSON.parse(r):[] }catch(e){return[]} }
function saveFileMap(m){ try{ localStorage.setItem(FILE_MAP_KEY, JSON.stringify(m)); }catch(e){console.warn(e)} }
function addFileMapEntry(sender, receiver, thoigiangui, filename, url){
    const map = loadFileMap();
    map.push({ sender, receiver, thoigiangui, filename, url });
    saveFileMap(map);
}
function findFileMapEntryFuzzy(sender, receiver, thoigiangui){
    if(!thoigiangui) return null;
    const map = loadFileMap();
    const want = new Date(thoigiangui).getTime();
    let best=null, bestDiff=Infinity;
    map.forEach(e=>{
        if(e.sender===sender && e.receiver===receiver && e.thoigiangui){
            const t = new Date(e.thoigiangui).getTime();
            const diff = Math.abs(t - want);
            if(diff < 10000 && diff < bestDiff){ bestDiff = diff; best = e; }
        }
    });
    return best;
}

function toAbsoluteUrl(maybeUrl){
    if(!maybeUrl) return null;
    try{ return new URL(maybeUrl, window.location.href).href; } catch(e){ return maybeUrl; }
}
function deriveFilenameFromUrl(url){
    if(!url) return '';
    try{
        const p = new URL(url, window.location.href).pathname;
        const parts = p.split('/');
        return decodeURIComponent(parts.pop() || '');
    }catch(e){
        const parts = url.split('/'); return decodeURIComponent(parts.pop()||'');
    }
}

/* WebSocket */
function connectWebSocket(){
    $('#connectionStatus').show().text('Đang kết nối...');
    try {
        socket = new WebSocket("wss://hanhphuc.site/ws");
    } catch(e){
        console.error('WS init error', e);
        $('#connectionStatus').text('Lỗi kết nối');
        setTimeout(connectWebSocket, 3000);
        return;
    }

    socket.onopen = function(){
        $('#connectionStatus').text('Đã kết nối');
        try{ socket.send(JSON.stringify({ command: 'register', username: user.tentk, role: user.vaitro })); }catch(e){}
    };

    socket.onmessage = function(event){
        let data;
        try{ data = JSON.parse(event.data); } catch(e){ console.warn('Invalid WS message', event.data); return; }

        if(data.command === 'messages'){
            const patient = data.receiver_tentk || data.partner;
            const raw = data.messages || [];
            // normalize and repair file entries
            const normalized = raw.map(m => {
                const nm = Object.assign({}, m);
                const txt = (nm.message || '').toString();
                if(txt.startsWith('[FILE]') || nm.filename || nm.url || nm.fileUrl){
                    nm.url = nm.url || nm.fileUrl || null;
                    if(!nm.url){
                        const after = txt.replace(/^\[FILE\]\s*/i,'').trim();
                        if(after) nm.url = after;
                    }
                    if(nm.url) nm.url = toAbsoluteUrl(nm.url);
                    if(!nm.filename && nm.url) nm.filename = deriveFilenameFromUrl(nm.url);
                    nm.message = '[FILE]';
                }
                return nm;
            });

            messages[patient] = normalized;
            if(currentPatient && currentPatient.tentk === patient) renderMessages(messages[patient]);
        }
        else if(data.command === 'receive' || data.command === 'message'){
            const sender = data.sender || data.from;
            if(!messages[sender]) messages[sender] = [];
            const msgObj = {
                sender: sender,
                message: data.message || '',
                filename: data.filename || data.fileName || null,
                url: data.url || data.fileUrl || null,
                thoigiangui: data.thoigiangui || new Date().toISOString()
            };

            // repair file metadata if needed using local map
            const txt = (msgObj.message || '').toString();
            if(txt.startsWith('[FILE]') || msgObj.filename || msgObj.url){
                if(!msgObj.url){
                    const after = txt.replace(/^\[FILE\]\s*/i, '').trim();
                    if(after) msgObj.url = after;
                }
                if(msgObj.url) msgObj.url = toAbsoluteUrl(msgObj.url);
                if(!msgObj.filename && msgObj.url) msgObj.filename = deriveFilenameFromUrl(msgObj.url);
                if((!msgObj.filename || !msgObj.url) && msgObj.thoigiangui){
                    const map = findFileMapEntryFuzzy(msgObj.sender, currentPatient ? currentPatient.tentk : null, msgObj.thoigiangui);
                    if(map){
                        msgObj.filename = msgObj.filename || map.filename;
                        msgObj.url = msgObj.url || map.url;
                    }
                }
                msgObj.message = '[FILE]';
            }

            // Skip unusable file messages (no filename & no url)
            if(msgObj.message === '[FILE]' && !msgObj.filename && !msgObj.url){
                console.warn('Ignored file message without filename/url', msgObj);
                return;
            }

            messages[sender].push(msgObj);
            if(currentPatient && currentPatient.tentk === sender){
                displayMessage(msgObj);
            }
        }
    };

    socket.onerror = function(err){
        console.error('WebSocket error', err);
        $('#connectionStatus').text('Lỗi kết nối');
    };

    socket.onclose = function(ev){
        console.warn('WebSocket closed', ev.code, ev.reason);
        $('#connectionStatus').text('Đang kết nối lại...');
        setTimeout(connectWebSocket, 3000);
    };
}

/* UI helpers */
function selectUserFromElement(el){
    const tentk = $(el).data('tentk');
    const name = $(el).data('name') || 'Bệnh nhân';
    selectUser(tentk, name);
}

function selectUser(tentk, name){
    if(!tentk || !name) return;
    if(!socket || socket.readyState !== WebSocket.OPEN){
        // wait and retry
        setTimeout(()=>selectUser(tentk,name), 500);
        return;
    }

    $('.user').removeClass('active');
    $(`.user[data-tentk="${tentk}"]`).addClass('active');

    currentPatient = { tentk, name };
    $('#headerText').text('Đang trò chuyện với ' + name);
    $('#headerAvatar').attr('src', 'Assets/img/default.png'); // optionally set patient avatar
    $('#messageInput').prop('disabled', false).focus();
    $('#sendButton').prop('disabled', false);

    $('#chatMessages').html('<div style="text-align:center;color:#777;padding:20px">Đang tải tin nhắn...</div>');

    if(!messages[tentk]) messages[tentk] = [];
    renderMessages(messages[tentk]);

    try{
        socket.send(JSON.stringify({ command: 'load_messages', tentk: user.tentk, receiver_tentk: tentk }));
    }catch(e){ console.warn(e); }
}

function renderMessages(arr){
    const container = $('#chatMessages');
    container.html('');
    if(!arr || arr.length === 0){
        container.html('<div style="text-align:center;color:#9aa3b2">Chưa có tin nhắn</div>');
        return;
    }
    arr.forEach(m => displayMessage(m));
    scrollToBottom();
}

function displayMessage(msg){
    // skip invalid
    if(!msg || typeof msg !== 'object') return;
    // optionally repair missing filename/url from local map
    if((msg.message && msg.message.toString().startsWith('[FILE]')) && !msg.filename && msg.url){
        msg.filename = deriveFilenameFromUrl(msg.url);
    }

    const isDoctor = msg.sender === user.tentk;
    const msgDiv = $('<div>').addClass('message').addClass(isDoctor ? 'doctor' : 'patient');

    const isFile = (msg.message && msg.message.toString().startsWith('[FILE]')) || msg.filename || msg.url;
    if(isFile){
        // ensure at least filename or url
        let url = msg.url || null;
        if(!url){
            const after = (msg.message||'').toString().replace(/^\[FILE\]\s*/i,'').trim();
            if(after) url = after;
        }
        url = toAbsoluteUrl(url);
        let filename = msg.filename || deriveFilenameFromUrl(url) || null;

        if(!filename && !url){
            // avoid showing undefined placeholder
            console.warn('Skipping unusable file message', msg);
            return;
        }

        const a = $('<a target="_blank" rel="noopener noreferrer"></a>').attr('href', url || '#');
        a.append($('<i>').addClass('fa fa-file-pdf'));
        a.append($('<span>').text(' ' + (filename || 'Tập tin')));
        msgDiv.append(a);
    } else {
        msgDiv.text(msg.message || '');
    }

    const meta = $('<div>').addClass('message-meta').text(formatTime(msg.thoigiangui || new Date().toISOString()));
    msgDiv.append(meta);

    $('#chatMessages').append(msgDiv);
    scrollToBottom();
}

function scrollToBottom(){
    const box = $('#chatMessages');
    requestAnimationFrame(()=>{ try{ box.scrollTop(box[0].scrollHeight); }catch(e){} });
}

function formatTime(iso){
    try{
        const d = new Date(iso);
        return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    }catch(e){ return ''; }
}

/* Sending text */
$('#sendButton').on('click', sendMessage);
$('#messageInput').on('keypress', function(e){
    if(e.which === 13 && !e.shiftKey){ e.preventDefault(); sendMessage(); }
});

function sendMessage(){
    const text = $('#messageInput').val().trim();
    if(!text || !currentPatient) return;
    if(!socket || socket.readyState !== WebSocket.OPEN){ alert('Kết nối bị gián đoạn'); return; }

    const payload = { command: 'send', sender: user.tentk, receiver: currentPatient.tentk, message: text, thoigiangui: new Date().toISOString() };
    try{ socket.send(JSON.stringify(payload)); }catch(e){ console.warn(e); }

    if(!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
    messages[currentPatient.tentk].push(payload);
    displayMessage(payload);
    $('#messageInput').val('');
}

/* File upload for doctor */
$('#attachBtn').on('click', function(){
    if(!currentPatient){ alert('Chọn bệnh nhân trước!'); return; }
    $('#fileInput').click();
});

$('#fileInput').on('change', function(){
    const file = this.files[0];
    if(!file) return;
    if(file.type !== 'application/pdf'){ alert('Chỉ chấp nhận file PDF'); $(this).val(''); return; }
    if(file.size > 10 * 1024 * 1024){ alert('File quá lớn (max 10MB)'); $(this).val(''); return; }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('receiver', currentPatient.tentk);

    const origHtml = $('#attachBtn').html();
    $('#attachBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
        url: 'Views/bacsi/pages/tinnhan/upload.php', 
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

                if(url) addFileMapEntry(user.tentk, currentPatient.tentk, ts, filename, url);

                const payload = {
                    command: 'send',
                    sender: user.tentk,
                    receiver: currentPatient.tentk,
                    message: '[FILE]',
                    filename: filename,
                    url: url,
                    thoigiangui: ts
                };
                try{ if(socket && socket.readyState === WebSocket.OPEN) socket.send(JSON.stringify(payload)); }catch(e){ console.warn('WS send file meta failed', e); }

                if(!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
                messages[currentPatient.tentk].push(payload);
                displayMessage(payload);
            } else {
                alert('Upload thất bại: ' + (res && res.error ? res.error : 'Không rõ lỗi'));
            }
        },
        error: function(xhr, st, err){
            console.error('Upload error', err, xhr.responseText);
            alert('Upload thất bại!');
        },
        complete: function(){
            $('#attachBtn').prop('disabled', false).html(origHtml);
            $('#fileInput').val('');
        }
    });
});

/* Events: click user */
$(document).on('click', '.user', function(){ selectUserFromElement(this); });

/* Init */
$(function(){
    connectWebSocket();
    // make sure header/status displays
    $('#connectionStatus').hide();
});
</script>
</body>
</html>