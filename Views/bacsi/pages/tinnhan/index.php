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
<style>
:root{
  --site-header-height: 90px; /* chỉnh nếu header cao/thấp hơn */
  --site-footer-height: 0px;
  --chat-gap: 18px;
  --input-area-height: 84px;
  --max-chat-width: 1200px;
}

*{box-sizing:border-box}
body { background-color: #f0f2f5; font-family: Arial, sans-serif; margin:0; padding:0; }

/* Container keeps chat area within viewport between site header/footer */
.chat-layout {
  display: flex;
  width: 100%;
  max-width: var(--max-chat-width);
  margin: var(--chat-gap) auto;
  height: calc(100vh - var(--site-header-height) - var(--site-footer-height) - (var(--chat-gap) * 2));
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 0 18px rgba(0,0,0,0.06);
}

/* Sidebar */
#userList { width: 320px; background: #fff; border-right: 1px solid #eee; overflow-y: auto; padding-bottom: 8px; }
#userList h3 { background: #2c3e50; color: white; padding: 16px; margin: 0; font-size:16px; }
.user { padding: 12px 18px; border-bottom: 1px solid #f6f6f6; cursor: pointer; display: flex; align-items: center; gap:12px; transition: background .15s; }
.user:hover { background: #fafafa; }
.user.active { background: #eef6ff; }

/* Chat column */
#chatContainer { flex: 1; display: flex; flex-direction: column; min-width:0; }

/* Header inside chat column should stay visible */
#chatHeader {
  padding: 18px 28px;
  background: linear-gradient(90deg,#fff,#fbfdff);
  border-bottom: 1px solid #f3f6f9;
  display:flex;
  align-items:center;
  gap:12px;
  position: sticky;
  top: 0;
  z-index: 3;
}
.header-avatar { width:48px; height:48px; border-radius:50%; overflow:hidden; flex-shrink:0; }
.header-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
.header-info { flex:1; }
#headerText { font-weight:700; font-size:18px; color:#0f1724; }
#headerSub { color:#6b7280; font-size:13px; margin-top:4px; }

/* Messages area should scroll independently */
#chatMessages {
  flex: 1;
  min-height: 0; /* important: allows flex child to scroll */
  overflow-y: auto;
  padding: 40px;
  padding-bottom: calc(var(--input-area-height) + 24px); /* ensure messages not hidden under input */
  background: linear-gradient(#f6f7fb,#f6f7fb);
}

/* Message block styles (simple, consistent) */
.message { display:block; max-width:68%; padding:10px 14px; margin-bottom:12px; border-radius:18px; font-size:14px; line-height:1.35; box-shadow: 0 4px 10px rgba(2,6,23,0.03); word-wrap:break-word; }
.doctor { background: linear-gradient(135deg, #eef8ff, #e6f3ff); color:#06344a; float:right; border-bottom-right-radius:6px; }
.patient { background:#ffffff; color:#0f1724; float:left; border-bottom-left-radius:6px; }
.message a { color: inherit; text-decoration:none; display:inline-flex; gap:10px; align-items:center; }
.fa-file-pdf { color:#c53030; font-size:20px; margin-right:8px; }

/* Meta time */
.message-meta { display:block; font-size:11px; color: rgba(15,23,36,0.45); margin-top:8px; text-align:right; }
.patient .message-meta { text-align:left; color:#6b7280; }

/* Input area pinned to bottom of chat column */
.input-container {
  position: sticky;
  bottom: 0;
  z-index: 4;
  background: #fff;
  border-top: 1px solid #eef2f7;
  padding: 18px 28px;
  display:flex;
  gap:10px;
  align-items:center;
}
.input-inner { display:flex; align-items:center; gap:10px; flex:1; background:#f4f6fb; padding:10px 14px; border-radius:999px; border:1px solid #e6e9ee; }
.input-inner textarea { border:none; background:transparent; outline:none; resize:none; width:100%; min-height:42px; max-height:120px; font-size:14px; color:#0f1724; }

/* Attach & send buttons */
.icon-btn { background:transparent; border:none; cursor:pointer; color:#55607a; font-size:18px; padding:6px; border-radius:8px; }
.send-btn { background:#1677ff; color:#fff; border:none; padding:10px 16px; border-radius:999px; cursor:pointer; font-weight:600; box-shadow:0 6px 18px rgba(22,119,255,0.12); }
.send-btn:disabled { opacity:.5; cursor:not-allowed; box-shadow:none; }

/* Small screens */
@media (max-width:900px){
  .chat-layout{ margin:0; height:calc(100vh - var(--site-header-height)); border-radius:0; }
  #userList{ width:100%; position:absolute; left:0; top:0; bottom:0; transform:translateX(-100%); transition:transform .22s; z-index:20; }
  #userList.show { transform:translateX(0); box-shadow: 0 12px 40px rgba(2,6,23,0.1); }
  #chatMessages{ padding:18px; padding-bottom: calc(var(--input-area-height) + 18px); }
  .input-container{ padding:12px 16px; }
  .message{ max-width:90%; }
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