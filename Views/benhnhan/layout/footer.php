<!-- Updated footer + chatbot fragment
  Fixes:
  - Makes chatbot iframe responsive (no fixed 520px height) so the chat input won't be cut off on small screens.
  - Adds higher z-index so the iframe appears above footer and other elements.
  - Uses max-height: calc(100vh - ...) and safe-area inset to avoid overlap with bottom UI / home indicator.
  - Improved toggle logic and click handling so the panel doesn't accidentally close.
-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{
  --footer-bg: linear-gradient(180deg,#382a86 0%, #2b2366 100%);
  --accent: #bfa6ff;
  --muted: rgba(255,255,255,0.8);
}
.custom-footer{
  background: var(--footer-bg);
  color: #fff;
  font-family: 'Segoe UI', Tahoma, sans-serif;
  position: relative; /* keep normal document flow */
  z-index: 1; /* ensure the footer is below the chatbot */
}
.custom-footer a{ color: var(--muted); text-decoration: none; }
.custom-footer a:hover{ color: var(--accent); text-decoration: none; }
.footer-brand img{ height: 60px; margin-bottom: 12px; }
.footer-title{ font-weight:700; color:#fff; margin-bottom:12px; font-size: 1.1rem; line-height: 1.3; }
.footer-text{ color: var(--muted); font-size: .88rem; line-height: 1.5; margin-bottom: 10px; }
.footer-links{ list-style:none; padding:0; margin:0; }
.footer-links li{ margin:8px 0; }
.social-icons a{ display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:8px; background:rgba(255,255,255,0.06); color: #fff; margin-right:8px; transition:all .15s ease; }
.social-icons a:hover{ transform:translateY(-3px); background: rgba(191,166,255,0.14); color: var(--accent); }
.newsletter input[type="email"]{ background: rgba(255,255,255,0.06); border: none; color: #fff; }
.newsletter .btn{ background: var(--accent); border: none; color: #2b2366; font-weight:600; }
.footer-bottom{ border-top:1px solid rgba(255,255,255,0.06); padding-top:18px; color: rgba(255,255,255,0.7); font-size: .9rem; }
@media (max-width:767px){ .social-icons a{ width:36px; height:36px; } }

/* Chatbot */
.chatbot-icon{
  position: fixed;
  right: 20px;
  bottom: 24px;
  width:56px;
  height:56px;
  border-radius:50%;
  background: #fff;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 8px 20px rgba(43,35,102,0.25);
  cursor:pointer;
  z-index: 2100; /* above footer and page content */
}
.chatbot-icon img{ width:40px; height:40px; }

/* Use a responsive frame: avoid fixed height so the iframe's internal input won't be cut off */
.chatbot-frame{
  position: fixed;
  right: 20px;
  bottom: calc(24px + 56px + 12px); /* above the icon */
  width: 360px;
  max-width: calc(100% - 40px);
  display: none;
  z-index: 2200; /* ensure overlay sits above most UI */
  box-shadow: 0 12px 40px rgba(0,0,0,0.35);
  border-radius: 10px;
  overflow: hidden;
  /* small animation for nicer UX */
  transform-origin: bottom right;
  transition: transform .15s ease, opacity .12s ease;
  opacity: 0;
  transform: scale(.98);
}

/* When open we show it and animate */
.chatbot-frame.open{
  display: block;
  opacity: 1;
  transform: scale(1);
}

/* Make iframe fill the container and be responsive.
   Use max-height relative to viewport so internal chat input remains visible.
*/
.chatbot-frame .chat-iframe{
  width: 100%;
  height: 520px; /* fallback for large screens */
  max-height: calc(100vh - 140px - env(safe-area-inset-bottom)); /* keep it above bottom UI on mobile */
  min-height: 300px;
  border: 0;
  display: block;
  background: #fff;
}

/* For very short viewports reduce height */
@media (max-height:600px){
  .chatbot-frame .chat-iframe{
    height: auto;
    max-height: calc(100vh - 120px - env(safe-area-inset-bottom));
  }
}

/* Accessibility focus outline */
.chatbot-icon:focus{ outline: 3px solid rgba(191,166,255,0.35); outline-offset: 3px; }

/* Make sure clicking outside closes the frame but allow interactions inside the iframe */
</style>

<footer class="custom-footer">
  <div class="container py-5">
    <div class="row gy-4 align-items-start">
      <div class="col-md-4">
        <div class="footer-brand mb-2">
          <img src="Assets/img/logo-footer.png" alt="Logo">
        </div>
        <div class="footer-title">Bệnh viện Đa khoa Quốc tế Hạnh Phúc</div>
        <p class="mb-0"><i class="bi bi-telephone-fill"></i> 1900 6765</p>
      </div>

      <div class="col-md-2">
        <div class="footer-title">Khám nhanh</div>
        <ul class="footer-links">
          <li><a href="?action=chuyenkhoa">Chuyên khoa</a></li>
          <li><a href="?action=bacsi">Bác sĩ</a></li>
          <li><a href="?action=lichhen">Đặt lịch</a></li>
          <li><a href="?action=goikham">Gói khám</a></li>
        </ul>
      </div>

      <div class="col-md-3">
        <div class="footer-title">Thông tin</div>
        <ul class="footer-links">
          <li><a href="?action=gioithieu">Về chúng tôi</a></li>
          <li><a href="?action=tintuc">Tin tức</a></li>
          <li><a href="?action=chinhsach">Chính sách</a></li>
          <li><a href="?action=lienhe">Liên hệ</a></li>
        </ul>
      </div>

      <div class="col-md-3">
        <div class="footer-title">Kết nối với chúng tôi</div>
        <div class="mb-3 footer-text">Theo dõi chúng tôi qua mạng xã hội để cập nhật tin tức y tế mới nhất.</div>
        <div class="social-icons">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          <a href="#" aria-label="Zalo"><img src="Assets/img/zalo-icon.png" alt="Zalo" style="height:18px;"></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
    </div>

    <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
      <div>© <span id="year"></span> Hanh Phuc International. All rights reserved.</div>
      <div class="mt-2 mt-md-0">Thiết kế bởi <a href="#">Bệnh viện Hạnh Phúc</a></div>
    </div>
  </div>
</footer>

<div class="chatbot-icon" id="chatbotBtn" title="Mở chatbot" role="button" aria-expanded="false" aria-controls="chatbotFrame">
  <img src="Assets/img/logo-banner.png" alt="Hospital Icon">
</div>

<!-- Khung chatbot -->
<div class="chatbot-frame" id="chatbotFrame" aria-hidden="true">
  <iframe class="chat-iframe"
          src="https://xaito.vn/App/embed/Chatbot/hfi87DePnBf0BLel"
          name="chatbot"
          title="Chatbot Hạnh Phúc"
          allow="clipboard-read; clipboard-write; geolocation; microphone; camera"
          sandbox="allow-forms allow-scripts allow-same-origin allow-popups"
          ></iframe>
</div>

<script>
  // Footer small scripts
  document.getElementById('year').textContent = new Date().getFullYear();

  const chatbotBtn = document.getElementById('chatbotBtn');
  const chatbotFrame = document.getElementById('chatbotFrame');

  function openChat(){
    chatbotFrame.classList.add('open');
    chatbotFrame.setAttribute('aria-hidden', 'false');
    chatbotBtn.setAttribute('aria-expanded', 'true');
  }
  function closeChat(){
    chatbotFrame.classList.remove('open');
    chatbotFrame.setAttribute('aria-hidden', 'true');
    chatbotBtn.setAttribute('aria-expanded', 'false');
  }
  function toggleChat(){
    if (chatbotFrame.classList.contains('open')) closeChat();
    else openChat();
  }

  chatbotBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleChat();
  });

  // Clicking outside should close the chat. When user clicks inside the iframe,
  // browser doesn't fire click events on iframe contents in parent, so we only need
  // to ensure we don't close when clicking the icon or the iframe element itself.
  document.addEventListener('click', (e) => {
    const target = e.target;
    if (target === chatbotBtn || chatbotBtn.contains(target)) return;
    if (target === chatbotFrame || chatbotFrame.contains(target)) return;
    // if frame is open, close it
    if (chatbotFrame.classList.contains('open')) closeChat();
  });

  // If the user focuses the button via keyboard, allow toggling with Enter/Space
  chatbotBtn.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      toggleChat();
    }
  });

  // When the window resizes, ensure the iframe stays within viewport height
  window.addEventListener('resize', () => {
    // no-op here because CSS handles responsive sizes; this is a placeholder
    // if you need to programmatically adjust height, do it here.
  });
</script>
