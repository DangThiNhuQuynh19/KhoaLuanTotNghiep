<?php
// Fixed chat UI for bác sĩ/patient view
// - Start session safely to avoid repeated session_start() warnings
// - Improved layout, LifeCare theme colors, avatar, patient list, message bubbles, file card display
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['tentk'])) {
    header("Location: index.php");
    exit();
}
$tentk = $_SESSION['user']['tentk'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Chat Bác sĩ – Bệnh nhân</title>

<!-- Use Inter for crisp UI -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --primary: #4A60D7;     /* LifeCare primary */
  --secondary: #E8EEFF;   /* LifeCare soft secondary */
  --neutral-bg: #f6f7fb;
  --muted: #7b8794;
  --text: #2A2A2A;
  --sidebar-width: 260px;
  --header-h: 90px;
  --input-h: 80px;
  --bubble-radius: 12px;
  --max-w: 100%px;
}

*{box-sizing:border-box}
html,body{height:100%; margin:0; font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,Arial; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;}
body{background:#f2f4f8;color:var(--text);}

/* Container */
.container{
  max-width:var(--max-w);
  margin:18px auto;
  height: calc(100vh - var(--header-h) - 36px);
  display:flex;
  align-items:stretch;
  padding:0 12px;
}

/* Chat layout card */
.chat {
  width:100%;
  background:#fff;
  border-radius:12px;
  display:flex;
  overflow:hidden;
  box-shadow:0 8px 28px rgba(10,20,40,0.06);
}

/* Sidebar (patient list) */
.sidebar {
  width:var(--sidebar-width);
  min-width:var(--sidebar-width);
  background:#fff;
  border-right:1px solid #eceef3;
  display:flex;
  flex-direction:column;
}

.sidebar-head {
  padding:14px 16px;
  border-bottom:1px solid #f1f3f6;
  display:flex;
  align-items:center;
  gap:12px;
}
.sidebar-head h3{margin:0;font-size:16px;font-weight:600;color:var(--text);}

/* Search */
.search {
  padding:10px 16px;
  border-bottom:1px solid #f6f8fb;
  background:linear-gradient(90deg, rgba(74,96,215,0.03), rgba(232,238,255,0.01));
}
.search input{
  width:100%;
  padding:10px 12px;
  border-radius:999px;
  border:1px solid #e9eef8;
  background:#fbfdff;
  font-size:14px;
  outline:none;
}

/* List */
.users {
  overflow-y:auto;
  padding:10px;
}
.users::-webkit-scrollbar{width:8px}
.users::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.06);border-radius:6px}

.user {
  display:flex;
  gap:12px;
  align-items:center;
  padding:10px;
  border-radius:10px;
  cursor:pointer;
  transition:background .14s, transform .08s;
}
.user:hover{ background:#f4f7ff; transform:translateX(2px); }
.user.active{ background:linear-gradient(90deg, rgba(74,96,215,0.06), rgba(232,238,255,0.02)); border-left:4px solid var(--primary); padding-left:8px; }

/* avatar + text */
.u-avatar {
  width:48px; height:48px; border-radius:50%; overflow:hidden; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
  background:#f3f5fb; border:1px solid #f0f2f7;
}
.u-avatar img{ width:100%; height:100%; object-fit:cover; display:block; }

.u-info{min-width:0;}
.u-name{font-size:14px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.u-sub{font-size:12px;color:var(--muted);margin-top:4px}

/* Chat area */
.area { flex:1; display:flex; flex-direction:column; min-width:0; background:var(--neutral-bg); }

/* Header */
.header {
  padding:16px 20px;
  display:flex;
  align-items:center;
  gap:12px;
  background:#fff;
  border-bottom:1px solid #eef2f7;
  position:sticky;
  top:0; z-index:4;
}
.h-avatar{ width:54px;height:54px;border-radius:50%;overflow:hidden;border:1px solid #eef2f7; }
.h-avatar img{ width:100%;height:100%;object-fit:cover;display:block; }
.h-info{flex:1; min-width:0}
.h-title{ font-size:16px;font-weight:700;color:var(--text) }
.h-sub{ font-size:13px;color:var(--muted); margin-top:4px }

/* Messages */
.messages {
  flex:1;
  padding:22px;
  overflow-y:auto;
  min-height:0;
  background:linear-gradient(180deg, #f8f9ff 0%, #f8f9ff 100%);
}
.messages::-webkit-scrollbar{ width:10px }
.messages::-webkit-scrollbar-thumb{ background:rgba(0,0,0,0.06); border-radius:6px }

/* Day separator */
.day { text-align:center; color:#9da9bd; font-size:12px; margin:8px 0 18px; }

/* Message rows */
.msg-row { display:flex; gap:12px; margin-bottom:12px; clear:both; align-items:flex-end; }
.msg-row.left { justify-content:flex-start; }
.msg-row.right { justify-content:flex-end; }

/* bubble */
.bubble {
  display:inline-block;
  max-width:72%;
  padding:10px 14px;
  border-radius:var(--bubble-radius);
  font-size:14px;
  line-height:1.35;
  word-break:break-word;
  box-shadow:0 1px 3px rgba(10,20,40,0.04);
  position:relative;
}

/* patient = other person; doctor = current user */
.bubble.patient {
  background:var(--primary);
  color:#fff;
  border-bottom-left-radius:10px;
}
.bubble.doctor {
  background:var(--secondary);
  color:var(--text);
  border-bottom-right-radius:10px;
}

/* meta time */
.meta { font-size:12px; color:rgba(255,255,255,0.85); margin-top:8px; display:block; text-align:left; }
.bubble.doctor .meta { color:#6b7280; text-align:right; }

/* File card */
.file-card {
  display:flex;
  gap:10px;
  align-items:center;
  max-width:300px;
  min-width:0;
  padding:8px 10px;
  border-radius:10px;
  background:#fff;
  border:1px solid #e9edf6;
}
.file-icon {
  width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;
  background:linear-gradient(180deg,#fff,#fff); color:#d33a3a;font-weight:700;font-size:14px;border:1px solid rgba(0,0,0,0.02);
}
.file-meta{ min-width:0; display:flex; flex-direction:column; gap:4px; }
.file-name{ font-size:13px; color:var(--text); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px; }
.file-time{ font-size:12px; color:#7b8794; }

/* Input area */
.input {
  padding:8px 10px;
  background:#fff;
  border-top:1px solid #eef2f7;
  display:flex;
  gap:12px;
  align-items:center;
  position:sticky;
  bottom:0; z-index:5;
}
.input-inner {
  flex:1; display:flex; gap:8px; align-items:center; background:#fff; border:1px solid #e9eef9; border-radius:12px;
}
.textarea {
  width:100%; min-height:44px; max-height:140px; border:none; outline:none; resize:none; font-size:14px; color:var(--text); background:transparent;
}
.btn {
  display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; border:none; cursor:pointer; font-weight:600;
}
.btn.send { background:var(--primary); color:#fff; box-shadow:0 8px 18px rgba(74,96,215,0.12) }
.btn.send:disabled{ opacity:0.6; cursor:not-allowed; box-shadow:none; }
.btn.attach{ background:transparent; border:1px solid #eef2f7; padding:8px;border-radius:8px; }

/* Responsive */
@media (max-width:900px){
  .container { margin:0; height:100vh; padding:0; }
  .sidebar{ position:absolute; left:0; top:0; bottom:0; transform:translateX(-100%); transition:transform .22s; z-index:40; box-shadow:0 12px 40px rgba(2,6,23,0.1); }
  .sidebar.show{ transform:translateX(0); }
  .sidebar { width:320px; min-width:320px; }
  .messages { padding:18px }
  .bubble { max-width:90% }
}
</style>
</head>
<body>

<div class="container">
  <div class="chat">
    <aside class="sidebar" id="userList" aria-label="Danh sách bệnh nhân">
      <div class="sidebar-head">
        <h3>Bệnh nhân</h3>
      </div>

      <div class="search">
        <input id="searchInput" type="text" placeholder="Tìm bệnh nhân..." />
      </div>

      <div class="users" id="usersContainer">
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
                    $hoten = htmlspecialchars($row['hoten'] ?: 'Bệnh nhân', ENT_QUOTES, 'UTF-8');
                    $tentk_row = htmlspecialchars($row['tentk'], ENT_QUOTES, 'UTF-8');
                    // If avatar exists on row, use it; fallback to default
                    $avatar = (isset($row['avatar']) && $row['avatar']) ? htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8') : 'Assets/img/default.png';
                    echo "<div class='user' data-tentk='{$tentk_row}' data-name='{$hoten}'>
                            <div class='u-avatar'><img src='{$avatar}' alt='avatar'></div>
                            <div class='u-info'>
                              <div class='u-name'>{$hoten}</div>
                              <div class='u-sub'>Bệnh nhân</div>
                            </div>
                          </div>";
                }
            } else {
                echo "<p style='padding:12px;color:#666;margin:0'>Không có bệnh nhân nào.</p>";
            }
        } else {
            echo "<p style='padding:12px;color:#c0392b;margin:0'>Không tìm thấy thông tin bác sĩ từ tài khoản đăng nhập.</p>";
        }
        ?>
      </div>
    </aside>

    <section class="area">
      <header class="header" id="chatHeader">
        <div class="h-avatar"><img id="headerAvatar" src="Assets/img/default.png" alt="avatar"></div>
        <div class="h-info">
          <div class="h-title" id="headerText">Chọn bệnh nhân để bắt chuyện</div>
          <div class="h-sub" id="headerSub">Sẵn sàng để tư vấn</div>
        </div>
        <div id="connectionStatus" style="font-size:13px;color:var(--muted);display:none">Đang kết nối</div>
      </header>

      <div class="messages" id="chatMessages" role="log" aria-live="polite">
        <div class="day">Chưa có cuộc trò chuyện</div>
      </div>

      <div class="input" id="chatInput">
        <div class="input-inner">
          <button id="attachBtn" class="btn attach" title="Gửi file (PDF)"><i class="fas fa-paperclip"></i></button>
          <textarea id="messageInput" class="textarea" placeholder="Nhập tin nhắn..." rows="1" disabled></textarea>
          <input type="file" id="fileInput" accept="application/pdf" style="display:none" />
        </div>
        <button id="sendButton" class="btn send" disabled>Gửi ✈️</button>
      </div>
    </section>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
/* Globals */
let socket = null;
let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 }; // 0 = doctor (current user)
let currentPatient = null;
let messages = {}; // keyed by patient tentk

/* Local file map so metadata persists */
const FILE_MAP_KEY = 'chat_file_map_v2';
function loadFileMap(){ try{ const r = localStorage.getItem(FILE_MAP_KEY); return r ? JSON.parse(r) : []; }catch(e){return[]} }
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

function toAbsoluteUrl(m){ if(!m) return null; try{ return new URL(m, window.location.href).href }catch(e){ return m } }
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

/* WebSocket connection */
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
        try{ socket.send(JSON.stringify({ command:'register', username: user.tentk, role: user.vaitro })); }catch(e){}
    };

    socket.onmessage = function(ev){
        let data;
        try{ data = JSON.parse(ev.data); }catch(e){ console.warn('Invalid WS', ev.data); return; }

        if(data.command === 'messages'){
            const patient = data.receiver_tentk || data.partner;
            const raw = data.messages || [];
            // normalize file messages
            const normalized = raw.map(m => {
                const nm = Object.assign({}, m);
                const txt = (nm.message||'').toString();
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
        else if(data.command === 'receive' || data.command === 'message' || data.command === 'new_message'){
            const sender = data.sender || data.from;
            if(!messages[sender]) messages[sender] = [];
            const msgObj = {
                sender: sender,
                message: data.message || '',
                filename: data.filename || data.fileName || null,
                url: data.url || data.fileUrl || null,
                thoigiangui: data.thoigiangui || new Date().toISOString()
            };

            // repair file entries via map
            const txt = (msgObj.message||'').toString();
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

            if(msgObj.message === '[FILE]' && !msgObj.filename && !msgObj.url){
                console.warn('Ignored unusable file msg', msgObj);
                return;
            }

            messages[sender].push(msgObj);
            if(currentPatient && currentPatient.tentk === sender){
                displayMessage(msgObj);
            }
        }
    };

    socket.onerror = function(err){ console.error('WS error', err); $('#connectionStatus').text('Lỗi kết nối'); };
    socket.onclose = function(ev){ console.warn('WS closed', ev); $('#connectionStatus').text('Đang kết nối lại...'); setTimeout(connectWebSocket, 3000); };
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
        setTimeout(()=>selectUser(tentk,name), 400);
        return;
    }

    $('.user').removeClass('active');
    $(`.user[data-tentk="${tentk}"]`).addClass('active');

    currentPatient = { tentk, name };
    $('#headerText').text('Đang trò chuyện với ' + name);
    // attempt to use the avatar from list
    const avatarEl = $(`.user[data-tentk="${tentk}"] .u-avatar img`);
    if(avatarEl.length) $('#headerAvatar').attr('src', avatarEl.attr('src'));
    $('#messageInput').prop('disabled', false).focus();
    $('#sendButton').prop('disabled', false);

    $('#chatMessages').html('<div class="day" style="text-align:center;color:#777;padding:20px">Đang tải tin nhắn...</div>');

    if(!messages[tentk]) messages[tentk] = [];
    renderMessages(messages[tentk]);

    try{ socket.send(JSON.stringify({ command:'load_messages', tentk: user.tentk, receiver_tentk: tentk })); }catch(e){ console.warn(e) }
}

function renderMessages(arr){
    const c = $('#chatMessages');
    c.html('');
    if(!arr || arr.length===0){ c.html('<div class="day">Chưa có tin nhắn</div>'); return; }
    arr.forEach(m => displayMessage(m));
    scrollToBottom();
}

/* Avoid duplicate file rendering: compare last rendered file link */
function lastRenderedFileInfo(){
    const last = $('#chatMessages').find('.file-card').last();
    if(!last || last.length===0) return null;
    const href = last.closest('a').attr('href') || '';
    const name = last.find('.file-name').text() || '';
    const time = last.find('.file-time').text() || '';
    return { href, name, time };
}

function displayMessage(msg){
    if(!msg || typeof msg !== 'object') return;

    // repair file name if missing
    if((msg.message && msg.message.toString().startsWith('[FILE]')) && !msg.filename && msg.url){
        msg.filename = deriveFilenameFromUrl(msg.url);
    }

    const isDoctor = msg.sender === user.tentk;
    const row = $('<div>').addClass('msg-row').addClass(isDoctor ? 'right' : 'left');
    const bubble = $('<div>').addClass('bubble').addClass(isDoctor ? 'doctor' : 'patient');

    const isFile = (msg.message && msg.message.toString().startsWith('[FILE]')) || msg.filename || msg.url;
    if(isFile){
        let url = msg.url || null;
        if(!url){
            const after = (msg.message||'').toString().replace(/^\[FILE\]\s*/i,'').trim();
            if(after) url = after;
        }
        url = toAbsoluteUrl(url);
        let filename = msg.filename || deriveFilenameFromUrl(url) || 'Tập tin.pdf';

        // dedupe: if last rendered file has same href/name and same sender/time approx, skip
        const last = lastRenderedFileInfo();
        const timeStr = formatTime(msg.thoigiangui || new Date().toISOString());
        if(last && last.href === url && last.name === filename && last.time === timeStr){
            // duplicate, ignore
            return;
        }

        const link = $('<a>').attr('href', url || '#').attr('target','_blank').attr('rel','noopener noreferrer');
        const card = $('<div>').addClass('file-card');
        const icon = $('<div>').addClass('file-icon').text('PDF');
        const meta = $('<div>').addClass('file-meta');
        const nameEl = $('<div>').addClass('file-name').text(filename);
        const timeEl = $('<div>').addClass('file-time').text(timeStr);
        meta.append(nameEl).append(timeEl);
        card.append(icon).append(meta);
        link.append(card);
        bubble.append(link);
    } else {
        bubble.text(msg.message || '');
        const m = $('<div>').addClass('meta').text(formatTime(msg.thoigiangui || new Date().toISOString()));
        bubble.append(m);
    }

    row.append(bubble);
    $('#chatMessages').append(row);
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

/* Sending messages */
$('#sendButton').on('click', sendMessage);
$('#messageInput').on('keypress', function(e){ if(e.which === 13 && !e.shiftKey){ e.preventDefault(); sendMessage(); } });

function sendMessage(){
    const text = $('#messageInput').val().trim();
    if(!text || !currentPatient) return;
    if(!socket || socket.readyState !== WebSocket.OPEN){ alert('Kết nối bị gián đoạn'); return; }

    const payload = { command:'send', sender: user.tentk, receiver: currentPatient.tentk, message: text, thoigiangui: new Date().toISOString() };
    try{ socket.send(JSON.stringify(payload)); }catch(e){ console.warn(e) }

    if(!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
    messages[currentPatient.tentk].push(payload);
    displayMessage(payload);
    $('#messageInput').val('');
}

/* File upload */
$('#attachBtn').on('click', function(){
    if(!currentPatient){ alert('Chọn bệnh nhân trước!'); return; }
    $('#fileInput').click();
});

$('#fileInput').on('change', function(){
    const file = this.files[0];
    if(!file) return;
    if(file.type !== 'application/pdf'){ alert('Chỉ chấp nhận file PDF'); $(this).val(''); return; }
    if(file.size > 10 * 1024 * 1024){ alert('File quá lớn (max 10MB)'); $(this).val(''); return; }

    const fd = new FormData();
    fd.append('file', file);
    fd.append('receiver', currentPatient.tentk);

    const orig = $('#attachBtn').html();
    $('#attachBtn').prop('disabled', true).text('⏳');

    $.ajax({
        url: 'Views/bacsi/pages/tinnhan/upload.php',
        type: 'POST',
        data: fd,
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
            console.error('Upload error', err, xhr && xhr.responseText);
            alert('Upload thất bại!');
        },
        complete: function(){
            $('#attachBtn').prop('disabled', false).html(orig);
            $('#fileInput').val('');
        }
    });
});

/* Click user */
$(document).on('click', '.user', function(){ selectUserFromElement(this); });

/* Search filter */
$('#searchInput').on('input', function(){
    const q = $(this).val().trim().toLowerCase();
    if(!q){ $('.user').show(); return; }
    $('.user').each(function(){
        const name = $(this).data('name') || '';
        $(this)[ (name.toLowerCase().indexOf(q) !== -1) ? 'show' : 'hide' ]();
    });
});

/* Init */
$(function(){
    connectWebSocket();
    $('#connectionStatus').hide();
});
</script>
</body>
</html>