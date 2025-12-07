<?php
// Views/bacsi/pages/tinnhan/index.php
// NOTE: ensure this file is placed in the correct repository path.
// Start session only if not already started to avoid session_start() warnings.
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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat Bác sĩ – Bệnh nhân</title>

<!-- LifeCare Theme fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --primary: #4A60D7;      /* LifeCare primary */
  --secondary: #E8EEFF;    /* LifeCare soft secondary */
  --muted: #f4f6fb;
  --text: #2A2A2A;
  --sidebar-width: 260px;
  --header-height: 90px;
  --input-height: 72px;
  --chat-gap: 18px;
  --max-width: 1200px;
  --bubble-radius: 12px;
}

/* Reset & base */
* { box-sizing: border-box; }
html,body { height:100%; }
body {
  margin:0;
  font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: #f2f4f8;
  color: var(--text);
  -webkit-font-smoothing:antialiased;
}

/* Outer layout */
.container {
  max-width: var(--max-width);
  margin: var(--chat-gap) auto;
  height: calc(100vh - var(--header-height) - (var(--chat-gap) * 2));
  background: transparent;
}

/* Chat panel */
.chat-layout {
  display: flex;
  height:100%;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 6px 22px rgba(10,20,40,0.06);
}

/* Sidebar: patient list */
#userList {
  width: var(--sidebar-width);
  min-width: var(--sidebar-width);
  background: #ffffff;
  border-right: 1px solid #e7e9ee;
  overflow-y: auto;
  padding-bottom: 12px;
}

#userList h3 {
  margin: 0;
  padding: 18px 16px;
  font-size: 15px;
  font-weight: 600;
  background: linear-gradient(90deg, rgba(74,96,215,0.06), rgba(232,238,255,0.02));
  color: var(--text);
  border-bottom: 1px solid rgba(0,0,0,0.03);
}

/* Each user row */
.user {
  display:flex;
  gap:12px;
  align-items:center;
  padding: 10px 14px;
  cursor:pointer;
  transition: background .16s, padding-left .16s;
  border-left: 4px solid transparent;
}

.user:hover {
  background: #F4F7FF; /* subtle hover matching spec */
  padding-left: 12px;
}

.user.active {
  background: linear-gradient(90deg, rgba(74,96,215,0.06), rgba(232,238,255,0.02));
  border-left-color: var(--primary);
}

/* Avatar in list */
.user-avatar {
  width:44px;
  height:44px;
  border-radius:50%;
  object-fit:cover;
  background: #f3f4f7;
  flex-shrink:0;
  border:1px solid #eef2f8;
}

/* Name and meta */
.user-info {
  display:flex;
  flex-direction:column;
  min-width:0;
}
.user-name {
  font-weight:600;
  font-size:14px;
  color:var(--text);
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}
.user-sub {
  font-size:12px;
  color:#7b8794;
  margin-top:2px;
}

/* Chat column */
#chatContainer { flex:1; display:flex; flex-direction:column; min-width:0; background: linear-gradient(180deg, #fbfdff, #fbfdff); }

/* Header */
#chatHeader {
  display:flex;
  align-items:center;
  gap:12px;
  padding: 18px 20px;
  border-bottom:1px solid #f0f2f6;
  background: #ffffff;
  position: sticky;
  top:0;
  z-index: 3;
}
.header-avatar {
  width:48px; height:48px; border-radius:50%; overflow:hidden; flex-shrink:0;
  border:1px solid #eef2f8;
}
.header-avatar img { width:100%; height:100%; object-fit:cover; display:block; }
.header-info { flex:1; min-width:0; }
#headerText { font-weight:700; font-size:16px; color:var(--text); }
#headerSub { color:#7b8794; font-size:13px; margin-top:4px; }

/* Messages area */
#chatMessages {
  flex:1;
  min-height:0;
  overflow-y:auto;
  padding: 28px;
  padding-bottom: calc(var(--input-height) + 28px);
  background: linear-gradient(180deg, #f8f9ff 0%, #f8f9ff 100%);
}

/* Message bubbles */
.message-row { display:flex; margin-bottom:12px; align-items:flex-end; gap:12px; clear:both; }
.message-row.right { justify-content:flex-end; }
.message-row.left { justify-content:flex-start; }

.message {
  display:inline-block;
  max-width:68%;
  padding:10px 14px;
  border-radius: var(--bubble-radius);
  font-size:14px;
  line-height:1.35;
  box-shadow: 0 1px 3px rgba(10,20,40,0.04);
  word-wrap:break-word;
  position:relative;
}

/* Patient (left) = sent by patient, should be primary colored with white text */
.patient {
  background: var(--primary);
  color: #fff;
  border-bottom-left-radius: 6px;
}

/* Doctor (right) = current user (doctor) */
.doctor {
  background: var(--secondary);
  color: var(--text);
  border-bottom-right-radius: 6px;
}

/* Message metadata (time) */
.message-meta {
  display:block;
  font-size:12px;
  color: rgba(15,23,36,0.45);
  margin-top:8px;
  text-align:right;
}
.patient .message-meta { color: rgba(255,255,255,0.75); text-align:left; font-size:12px; }

/* File card style */
.file-card {
  display:flex;
  align-items:center;
  gap:10px;
  background:#fff;
  border:1px solid #e6e6e6;
  padding:8px 12px;
  border-radius:10px;
  max-width: 250px;
  box-shadow:none;
}
.file-icon {
  width:36px; height:36px; flex-shrink:0; display:flex; align-items:center; justify-content:center;
  background: linear-gradient(180deg, #fff 0%, #fff 100%);
  border-radius:8px;
  color: #d23a3a;
  font-weight:700;
  font-size:16px;
}
.file-meta { min-width:0; display:flex; flex-direction:column; gap:4px; }
.file-name {
  font-size:13px;
  color:var(--text);
  overflow:hidden;
  white-space:nowrap;
  text-overflow:ellipsis;
  max-width: 200px;
}
.file-time { font-size:12px; color:#7b8794; }

/* make the whole file card a link */
.file-link { text-decoration:none; color:inherit; display:flex; align-items:center; gap:10px; }

/* Input area pinned to bottom */
.input-container {
  position: sticky;
  bottom:0;
  z-index:4;
  background:#fff;
  border-top:1px solid #e8e8ee;
  padding: 14px 18px;
  display:flex;
  gap:12px;
  align-items:center;
  height: var(--input-height);
}

/* Input inner */
.input-inner {
  display:flex;
  align-items:center;
  gap:12px;
  flex:1;
  background:#f7f9ff;
  padding:10px 14px;
  border-radius:12px;
  border:1px solid rgba(74,96,215,0.06);
}

.input-inner textarea {
  border:none;
  background:transparent;
  outline:none;
  resize:none;
  width:100%;
  min-height:42px;
  max-height:120px;
  font-size:14px;
  color:var(--text);
}

/* Attach & send */
.icon-btn {
  background:transparent;
  border:none;
  cursor:pointer;
  color:#55607a;
  font-size:18px;
  padding:6px;
  border-radius:8px;
}
.attach-icon {
  width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:16px;
}
.send-btn {
  background: var(--primary);
  color:#fff;
  border:none;
  padding:10px 16px;
  border-radius:10px;
  cursor:pointer;
  font-weight:600;
  display:inline-flex;
  align-items:center;
  gap:8px;
  box-shadow: 0 8px 18px rgba(74,96,215,0.12);
}
.send-btn:disabled { opacity:.6; cursor:not-allowed; box-shadow:none; }

/* Responsive tweaks */
@media (max-width:900px){
  .container { margin:0; height:calc(100vh - var(--header-height)); }
  #userList { position:absolute; left:0; top:0; bottom:0; transform:translateX(-100%); transition:transform .22s; z-index:20; box-shadow:0 12px 40px rgba(2,6,23,0.12); }
  #userList.show { transform:translateX(0); }
  #userList { width: 320px; min-width:320px; }
  .message { max-width:90%; }
}
</style>
</head>
<body>

<div class="container">
  <div class="chat-layout">
    <div id="userList" aria-label="Danh sách bệnh nhân">
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
                  // try to include avatar url if available in row; fallback to default image
                  $avatar = isset($row['avatar']) && $row['avatar'] ? htmlspecialchars($row['avatar'], ENT_QUOTES, 'UTF-8') : 'Assets/img/default.png';
                  echo "<div class='user' data-tentk='{$tentk_row}' data-name='{$hoten}'>
                          <img class='user-avatar' src='{$avatar}' alt='avatar'>
                          <div class='user-info'>
                            <div class='user-name'>{$hoten}</div>
                            <div class='user-sub'>Bệnh nhân</div>
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

    <div id="chatContainer">
      <div id="chatHeader">
        <div class="header-avatar"><img id="headerAvatar" src="Assets/img/default.png" alt="avatar"></div>
        <div class="header-info">
          <div id="headerText">Chọn bệnh nhân để bắt chuyện</div>
          <div id="headerSub">Sẵn sàng để tư vấn</div>
        </div>
        <div id="connectionStatus" style="font-size:13px;color:#7b8794;display:none">Đang kết nối</div>
      </div>

      <div id="chatMessages" role="log" aria-live="polite">
        <div class="message-day" style="text-align:center;color:#9aa3b2;">Chưa có cuộc trò chuyện</div>
      </div>

      <div class="input-container" role="region" aria-label="Gửi tin nhắn">
        <div class="input-inner">
          <button id="attachBtn" class="icon-btn" title="Gửi file (PDF)"><span class="attach-icon">📎</span></button>
          <textarea id="messageInput" placeholder="Nhập tin nhắn..." rows="1" disabled></textarea>
          <input type="file" id="fileInput" accept="application/pdf" style="display:none;">
        </div>
        <button id="sendButton" class="send-btn" disabled><span style="transform:translateY(1px)">✈️</span> <span>Gửi</span></button>
      </div>
    </div>
  </div>
</div>

<!-- Font Awesome lightweight usage is not required; use emojis/icons for reliability -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
/* Globals */
let socket = null;
let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 }; // role 0 = doctor
let currentPatient = null;
let messages = {}; // messages keyed by patient tentk

/* Local file map */
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
    // Render sequentially; avoid grouping duplicates by timestamp heuristic
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
    const row = $('<div>').addClass('message-row').addClass(isDoctor ? 'right' : 'left');
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
        let filename = msg.filename || deriveFilenameFromUrl(url) || 'Tập tin.pdf';

        if(!filename && !url){
            // avoid showing undefined placeholder
            console.warn('Skipping unusable file message', msg);
            return;
        }

        // build file card
        const link = $('<a>')
            .addClass('file-link')
            .attr('href', url || '#')
            .attr('target','_blank')
            .attr('rel','noopener noreferrer');

        const card = $('<div>').addClass('file-card');
        const icon = $('<div>').addClass('file-icon').text('PDF');
        const meta = $('<div>').addClass('file-meta');
        const nameEl = $('<div>').addClass('file-name').text(filename);
        const timeEl = $('<div>').addClass('file-time').text(formatTime(msg.thoigiangui || new Date().toISOString()));
        meta.append(nameEl).append(timeEl);
        card.append(icon).append(meta);
        link.append(card);
        msgDiv.append(link);

    } else {
        msgDiv.text(msg.message || '');
        const meta = $('<div>').addClass('message-meta').text(formatTime(msg.thoigiangui || new Date().toISOString()));
        msgDiv.append(meta);
    }

    row.append(msgDiv);
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
    $('#attachBtn').prop('disabled', true).html('<span class="attach-icon">⏳</span>');

    $.ajax({
        url: 'Views/benhnhan/pages/tinnhan/uploadFile.php', // upload endpoint
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
    $('#connectionStatus').hide();
});
</script>
</body>
</html>