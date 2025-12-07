<?php
// Views/bacsi/pages/tinnhan/index.php
// Continued improvements:
// - safe session_start()
// - initials avatar fallback (list + header)
// - group consecutive messages from same sender (within 5 min)
// - dedupe messages/files using messageId or deterministic hash
// - sidebar toggle for small screens
// - fixed CSS vars (max width), improved styles
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
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Chat Bác sĩ – Bệnh nhân</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --primary: #4A60D7;
  --secondary: #E8EEFF;
  --neutral-bg: #f6f7fb;
  --muted: #7b8794;
  --text: #2A2A2A;
  --sidebar-width: 260px;
  --header-h: 90px;
  --input-h: 80px;
  --bubble-radius: 12px;
  --max-w: 1200px;
  --group-gap: 8px;
  --group-time-ms: 5 * 60 * 1000; /* 5 minutes in ms, used by JS */
}

*{box-sizing:border-box}
html,body{height:100%; margin:0; font-family:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,Arial; -webkit-font-smoothing:antialiased;}
body{background:#f2f4f8;color:var(--text);}

/* Layout */
.container{ max-width:var(--max-w); margin:18px auto; height:calc(100vh - var(--header-h) - 36px); display:flex; padding:0 12px; }
.chat{ width:100%; background:#fff; border-radius:12px; display:flex; overflow:hidden; box-shadow:0 8px 28px rgba(10,20,40,0.06); }

/* Sidebar */
.sidebar{ width:var(--sidebar-width); min-width:var(--sidebar-width); background:#fff; border-right:1px solid #eceef3; display:flex; flex-direction:column; }
.sidebar-head{ padding:14px 16px; border-bottom:1px solid #f1f3f6; display:flex; align-items:center; justify-content:space-between; gap:12px; }
.sidebar-head h3{margin:0;font-size:16px;font-weight:600}
.sidebar-toggle{ display:none; background:transparent;border:none;font-size:18px; cursor:pointer; color:var(--muted) }

.search{ padding:10px 16px; border-bottom:1px solid #f6f8fb; background:linear-gradient(90deg, rgba(74,96,215,0.03), rgba(232,238,255,0.01)); }
.search input{ width:100%; padding:10px 12px; border-radius:999px; border:1px solid #e9eef8; background:#fbfdff; font-size:14px; outline:none; }

.users{ overflow-y:auto; padding:10px; }
.users::-webkit-scrollbar{ width:8px } .users::-webkit-scrollbar-thumb{ background:rgba(0,0,0,0.06); border-radius:6px }

.user{ display:flex; gap:12px; align-items:center; padding:10px; border-radius:10px; cursor:pointer; transition:background .12s, transform .08s; position:relative; }
.user:hover{ background:#f4f7ff; transform:translateX(2px); }
.user.active{ background:linear-gradient(90deg, rgba(74,96,215,0.06), rgba(232,238,255,0.02)); border-left:4px solid var(--primary); padding-left:8px; }
.u-avatar{ width:48px; height:48px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f3f5fb; border:1px solid #f0f2f7; flex-shrink:0; }
.u-avatar img{ width:100%; height:100%; object-fit:cover; display:block; }
.u-initial{ width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:700; color:#fff; background:linear-gradient(180deg,var(--primary),#3c50c8); text-transform:uppercase; }
.u-info{ min-width:0; }
.u-name{ font-size:14px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
.u-sub{ font-size:12px; color:var(--muted); margin-top:4px; }

/* unread badge */
.u-badge{ position:absolute; right:12px; top:10px; background:#ff4d4f; color:#fff; font-size:11px; padding:2px 6px; border-radius:999px; display:none; }

/* Chat area */
.area{ flex:1; display:flex; flex-direction:column; min-width:0; background:var(--neutral-bg); }
.header{ padding:16px 20px; display:flex; align-items:center; gap:12px; background:#fff; border-bottom:1px solid #eef2f7; position:sticky; top:0; z-index:4; }
.h-avatar{ width:54px; height:54px; border-radius:50%; overflow:hidden; display:flex; align-items:center; justify-content:center; border:1px solid #eef2f7; }
.h-avatar img{ width:100%; height:100%; object-fit:cover; display:block; }
.h-avatar .avatar-initial{ width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; color:#fff; background:linear-gradient(180deg,var(--primary),#3c50c8); text-transform:uppercase; }
.h-info{ flex:1; min-width:0; }
.h-title{ font-size:16px; font-weight:700; color:var(--text); }
.h-sub{ font-size:13px; color:var(--muted); margin-top:4px; }

/* Messages */
.messages{ flex:1; padding:22px; overflow-y:auto; min-height:0; background:linear-gradient(180deg,#f8f9ff,#f8f9ff); }
.messages::-webkit-scrollbar{ width:10px } .messages::-webkit-scrollbar-thumb{ background:rgba(0,0,0,0.06); border-radius:6px; }

.day{ text-align:center; color:#9da9bd; font-size:12px; margin:8px 0 18px }

/* Grouped block (consecutive messages from same sender) */
.group { margin-bottom:var(--group-gap); display:flex; gap:12px; align-items:flex-end; }
.group.left{ justify-content:flex-start; }
.group.right{ justify-content:flex-end; }

/* bubble group container (stack of messages inside) */
.group-bubble{ display:flex; flex-direction:column; gap:6px; max-width:72%; }
.group-bubble .bubble { padding:10px 14px; border-radius:var(--bubble-radius); font-size:14px; line-height:1.35; box-shadow:0 1px 3px rgba(10,20,40,0.04); word-break:break-word; position:relative; }

/* types */
.bubble.patient{ background:var(--primary); color:#fff; border-bottom-left-radius:10px; }
.bubble.doctor{ background:var(--secondary); color:var(--text); border-bottom-right-radius:10px; }

/* per-message meta (placed inside each bubble or file card) */
.msg-meta{ font-size:12px; color:rgba(255,255,255,0.85); margin-top:6px; }
.bubble.doctor .msg-meta{ color:#6b7280; text-align:right; }

/* File card inside group bubble */
.file-card{ display:flex; gap:10px; align-items:center; max-width:320px; padding:8px 10px; border-radius:10px; background:#fff; border:1px solid #e9edf6; }
.file-icon{ width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#d33a3a; font-weight:700; font-size:14px; background:#fff; border:1px solid rgba(0,0,0,0.02); }
.file-meta{ display:flex; flex-direction:column; min-width:0; gap:4px; }
.file-name{ font-size:13px; color:var(--text); overflow:hidden; white-space:nowrap; text-overflow:ellipsis; max-width:220px; }
.file-time{ font-size:12px; color:#7b8794; }

/* Input */
.input{ padding:8px 10px; background:#fff; border-top:1px solid #eef2f7; display:flex; gap:12px; align-items:center; position:sticky; bottom:0; z-index:5; }
.input-inner{ flex:1; display:flex; gap:8px; align-items:center; background:#fff; border:1px solid #e9eef9; border-radius:12px; padding:8px; }
.textarea{ width:100%; min-height:44px; max-height:140px; border:none; outline:none; resize:none; font-size:14px; color:var(--text); background:transparent; }
.btn{ display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; border:none; cursor:pointer; font-weight:600; }
.btn.send{ background:var(--primary); color:#fff; box-shadow:0 8px 18px rgba(74,96,215,0.12); }
.btn.send:disabled{ opacity:0.6; cursor:not-allowed; box-shadow:none; }
.btn.attach{ background:transparent; border:1px solid #eef2f7; padding:8px; border-radius:8px; }

/* small screens */
@media (max-width:900px){
  .container{ margin:0; height:100vh; padding:0; }
  .sidebar{ position:absolute; left:0; top:0; bottom:0; transform:translateX(-100%); transition:transform .22s; z-index:40; box-shadow:0 12px 40px rgba(2,6,23,0.1); }
  .sidebar.show{ transform:translateX(0); }
  .sidebar{ width:320px; min-width:320px; }
  .sidebar-toggle{ display:block; }
  .messages{ padding:18px }
  .group-bubble{ max-width:90% }
}
</style>
</head>
<body>

<div class="container">
  <div class="chat" role="application">
    <aside class="sidebar" id="userList" aria-label="Danh sách bệnh nhân">
      <div class="sidebar-head">
        <h3>Bệnh nhân</h3>
        <button id="sidebarToggle" class="sidebar-toggle" title="Hiện/Ẩn danh sách">☰</button>
      </div>

      <div class="search">
        <input id="searchInput" type="text" placeholder="Tìm bệnh nhân..." aria-label="Tìm bệnh nhân" />
      </div>

      <div class="users" id="usersContainer" role="list">
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
                    $hoten_raw = $row['hoten'] ?? '';
                    $hoten = htmlspecialchars(trim($hoten_raw) ?: 'Bệnh nhân', ENT_QUOTES, 'UTF-8');
                    $tentk_row = htmlspecialchars($row['tentk'] ?? '', ENT_QUOTES, 'UTF-8');

                    // initial (multibyte-safe)
                    $initial = '';
                    $name_parts = preg_split('/\s+/', trim($hoten_raw));
                    if ($name_parts && count($name_parts) > 0) {
                        $last = end($name_parts);
                        $initial = mb_strtoupper(mb_substr($last, 0, 1, 'UTF-8'), 'UTF-8');
                        $initial = htmlspecialchars($initial, ENT_QUOTES, 'UTF-8');
                    }

                    $avatar_field = isset($row['avatar']) ? trim($row['avatar']) : '';
                    if ($avatar_field) {
                        $avatar_safe = htmlspecialchars($avatar_field, ENT_QUOTES, 'UTF-8');
                        echo "<div class='user' data-tentk='{$tentk_row}' data-name='{$hoten}' role='listitem'>
                                <div class='u-avatar'><img src='{$avatar_safe}' alt=''></div>
                                <div class='u-info'>
                                  <div class='u-name'>{$hoten}</div>
                                  <div class='u-sub'>Bệnh nhân</div>
                                </div>
                                <div class='u-badge' aria-hidden='true'></div>
                              </div>";
                    } else {
                        echo "<div class='user' data-tentk='{$tentk_row}' data-name='{$hoten}' role='listitem'>
                                <div class='u-avatar'><div class='u-initial'>{$initial}</div></div>
                                <div class='u-info'>
                                  <div class='u-name'>{$hoten}</div>
                                  <div class='u-sub'>Bệnh nhân</div>
                                </div>
                                <div class='u-badge' aria-hidden='true'></div>
                              </div>";
                    }
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

    <section class="area" aria-live="polite">
      <header class="header" id="chatHeader">
        <div class="h-avatar" id="headerAvatar">
          <img id="headerAvatarImg" src="Assets/img/default.png" alt="">
        </div>
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
          <button id="attachBtn" class="btn attach" title="Gửi file (PDF)">📎</button>
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
/* Globals & state */
let socket = null;
let user = { tentk: "<?php echo htmlspecialchars($tentk, ENT_QUOTES, 'UTF-8'); ?>", vaitro: 0 };
let currentPatient = null;
let messages = {}; // keyed by tentk -> array of message objects
const renderedMessageIds = new Set(); // dedupe by id/hash
const FILE_MAP_KEY = 'chat_file_map_v3';

/* Storage helpers */
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

function toAbsoluteUrl(m){ if(!m) return null; try{ return new URL(m, window.location.href).href; }catch(e){ return m; } }
function deriveFilenameFromUrl(url){ if(!url) return ''; try{ const p = new URL(url, window.location.href).pathname; const parts = p.split('/'); return decodeURIComponent(parts.pop() || ''); }catch(e){ const parts = url.split('/'); return decodeURIComponent(parts.pop()||''); } }

/* Deterministic id/hash for message if server didn't provide one */
function makeMessageId(msg){
    if(msg.id) return String(msg.id);
    if(msg.messageId) return String(msg.messageId);
    const base = (msg.sender||'') + '|' + (msg.thoigiangui||'') + '|' + ((msg.url||msg.message||'').toString().slice(0,200));
    // simple hash:
    let h=0; for(let i=0;i<base.length;i++){ h = ((h<<5)-h) + base.charCodeAt(i); h |= 0; }
    return 'h' + String(h);
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
        try{ socket.send(JSON.stringify({ command:'register', username: user.tentk, role: user.vaitro })); }catch(e){}
        // enable input when WS ready (if a patient selected)
        if(currentPatient){ $('#messageInput').prop('disabled', false); $('#sendButton').prop('disabled', false); }
    };

    socket.onmessage = function(ev){
        let data;
        try{ data = JSON.parse(ev.data); }catch(e){ console.warn('Invalid WS', ev.data); return; }

        // messages list loaded
        if(data.command === 'messages'){
            const patient = data.receiver_tentk || data.partner;
            const raw = data.messages || [];
            const normalized = raw.map(normalizeMsg);
            messages[patient] = normalized;
            if(currentPatient && currentPatient.tentk === patient) renderMessages(messages[patient]);
            return;
        }

        // single incoming
        if(['receive','message','new_message'].includes(data.command)){
            const sender = data.sender || data.from;
            if(!messages[sender]) messages[sender] = [];
            const msgObj = normalizeMsg(data);
            // dedupe by id
            const id = makeMessageId(msgObj);
            if(renderedMessageIds.has(id)){
                // already rendered previously
            } else {
                messages[sender].push(msgObj);
                if(currentPatient && currentPatient.tentk === sender){
                    renderMessages(messages[sender]); // re-render group-aware
                }
            }
            return;
        }
    };

    socket.onerror = function(err){ console.error('WS error', err); $('#connectionStatus').text('Lỗi kết nối'); };
    socket.onclose = function(ev){ console.warn('WS closed', ev); $('#connectionStatus').text('Đang kết nối lại...'); setTimeout(connectWebSocket, 3000); };
}

/* Normalize message object and repair file fields */
function normalizeMsg(m){
    const nm = Object.assign({}, m);
    nm.message = nm.message || '';
    nm.filename = nm.filename || nm.fileName || null;
    nm.url = nm.url || nm.fileUrl || null;
    nm.thoigiangui = nm.thoigiangui || new Date().toISOString();

    const txt = (nm.message||'').toString();
    if(txt.startsWith('[FILE]') || nm.filename || nm.url){
        if(!nm.url){
            const after = txt.replace(/^\[FILE\]\s*/i,'').trim();
            if(after) nm.url = after;
        }
        if(nm.url) nm.url = toAbsoluteUrl(nm.url);
        if(!nm.filename && nm.url) nm.filename = deriveFilenameFromUrl(nm.url);
        if((!nm.filename || !nm.url) && nm.thoigiangui){
            const map = findFileMapEntryFuzzy(nm.sender || nm.from, nm.receiver || null, nm.thoigiangui);
            if(map){
                nm.filename = nm.filename || map.filename;
                nm.url = nm.url || map.url;
            }
        }
        nm.message = '[FILE]';
    }
    return nm;
}

/* UI helpers */
function selectUserFromElement(el){ const tentk = $(el).data('tentk'); const name = $(el).data('name') || 'Bệnh nhân'; selectUser(tentk,name); }
function selectUser(tentk, name){
    if(!tentk || !name) return;
    $('.user').removeClass('active');
    $(`.user[data-tentk="${tentk}"]`).addClass('active');

    currentPatient = { tentk, name };
    $('#headerText').text('Đang trò chuyện với ' + name);

    // header avatar: use image if present else initial
    const avatarImg = $(`.user[data-tentk="${tentk}"] .u-avatar img`);
    const avatarInitial = $(`.user[data-tentk="${tentk}"] .u-avatar .u-initial`);
    const headerAvatar = $('#headerAvatar');
    headerAvatar.empty();
    if (avatarImg.length){
        headerAvatar.append($('<img>').attr('src', avatarImg.attr('src')).attr('alt',''));
    } else if (avatarInitial.length){
        headerAvatar.append($('<div>').addClass('avatar-initial').text(avatarInitial.text().charAt(0).toUpperCase()));
    } else {
        headerAvatar.append($('<img>').attr('src','Assets/img/default.png').attr('alt',''));
    }

    $('#messageInput').prop('disabled', false).focus();
    $('#sendButton').prop('disabled', false);
    $('#chatMessages').html('<div class="day" style="text-align:center;color:#777;padding:20px">Đang tải tin nhắn...</div>');

    if(!messages[tentk]) messages[tentk] = [];
    renderMessages(messages[tentk]);

    try{ if(socket && socket.readyState === WebSocket.OPEN) socket.send(JSON.stringify({ command:'load_messages', tentk: user.tentk, receiver_tentk: tentk })); }catch(e){ console.warn(e) }
}

/* Render with grouping: consecutive messages by same sender within 5 minutes are grouped */
function renderMessages(arr){
    const c = $('#chatMessages');
    c.html('');
    if(!arr || arr.length === 0){ c.html('<div class="day">Chưa có tin nhắn</div>'); return; }

    renderedMessageIds.clear();

    const groups = [];
    let currentGroup = null;
    const timeWindow = 5 * 60 * 1000; // ms

    arr.forEach(msg=>{
        const id = makeMessageId(msg);
        msg._id = id; // attach
        const sender = msg.sender || msg.from || '';
        const ts = new Date(msg.thoigiangui).getTime();

        if(currentGroup &&
           currentGroup.sender === sender &&
           (ts - currentGroup.lastTs) <= timeWindow) {
            currentGroup.items.push(msg);
            currentGroup.lastTs = ts;
        } else {
            if(currentGroup) groups.push(currentGroup);
            currentGroup = { sender, items: [msg], lastTs: ts };
        }
    });
    if(currentGroup) groups.push(currentGroup);

    groups.forEach(g=>{
        const isMe = g.sender === user.tentk;
        const row = $('<div>').addClass('group').addClass(isMe ? 'right' : 'left');
        const bubbleCol = $('<div>').addClass('group-bubble');
        g.items.forEach(it=>{
            // dedupe
            if(renderedMessageIds.has(it._id)) return;
            renderedMessageIds.add(it._id);

            if(it.message === '[FILE]' || it.filename || it.url){
                // file card
                let url = it.url || null;
                if(!url){
                    const after = (it.message||'').toString().replace(/^\[FILE\]\s*/i,'').trim();
                    if(after) url = after;
                }
                url = toAbsoluteUrl(url);
                const filename = it.filename || deriveFilenameFromUrl(url) || 'Tập tin.pdf';
                const fileLink = $('<a>').attr('href', url || '#').attr('target','_blank').attr('rel','noopener noreferrer');
                const card = $('<div>').addClass('file-card');
                const icon = $('<div>').addClass('file-icon').text('PDF');
                const meta = $('<div>').addClass('file-meta');
                meta.append($('<div>').addClass('file-name').text(filename));
                meta.append($('<div>').addClass('file-time').text(formatTime(it.thoigiangui)));
                card.append(icon).append(meta);
                fileLink.append(card);
                const wrap = $('<div>').addClass('bubble').addClass(isMe ? 'doctor' : 'patient');
                wrap.append(fileLink);
                bubbleCol.append(wrap);
            } else {
                const wrap = $('<div>').addClass('bubble').addClass(isMe ? 'doctor' : 'patient');
                // preserve line breaks
                const lines = (it.message||'').toString().split(/\r?\n/);
                lines.forEach((ln, idx)=>{
                    const p = $('<div>').text(ln);
                    wrap.append(p);
                });
                wrap.append($('<div>').addClass('msg-meta').text(formatTime(it.thoigiangui)));
                bubbleCol.append(wrap);
            }
        });
        row.append(bubbleCol);
        c.append(row);
    });

    scrollToBottom();
}

/* helpers */
function scrollToBottom(){ const box = $('#chatMessages'); requestAnimationFrame(()=>{ try{ box.scrollTop(box[0].scrollHeight); }catch(e){} }); }
function formatTime(iso){ try{ const d = new Date(iso); return `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`; }catch(e){ return ''; } }

/* Send text */
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
    renderMessages(messages[currentPatient.tentk]);
    $('#messageInput').val('');
}

/* File upload */
$('#attachBtn').on('click', function(){ if(!currentPatient){ alert('Chọn bệnh nhân trước!'); return; } $('#fileInput').click(); });
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

                const payload = { command:'send', sender:user.tentk, receiver:currentPatient.tentk, message:'[FILE]', filename:filename, url:url, thoigiangui:ts };
                try{ if(socket && socket.readyState === WebSocket.OPEN) socket.send(JSON.stringify(payload)); }catch(e){ console.warn(e); }

                if(!messages[currentPatient.tentk]) messages[currentPatient.tentk] = [];
                messages[currentPatient.tentk].push(payload);
                renderMessages(messages[currentPatient.tentk]);
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

/* Sidebar toggle for small screens */
$('#sidebarToggle').on('click', function(){
    $('#userList').toggleClass('show');
});

/* Click user */
$(document).on('click', '.user', function(){ selectUserFromElement(this); $('#userList').removeClass('show'); });

/* Search filter */
$('#searchInput').on('input', function(){
    const q = $(this).val().trim().toLowerCase();
    if(!q){ $('.user').show(); return; }
    $('.user').each(function(){ const name = $(this).data('name') || ''; $(this)[ (name.toLowerCase().indexOf(q) !== -1) ? 'show' : 'hide' ](); });
});

/* init */
$(function(){ connectWebSocket(); $('#connectionStatus').hide(); });
</script>
</body>
</html>