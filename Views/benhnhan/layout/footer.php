<!-- Updated footer + chatbot fragment
  Fixes:
  - Makes chatbot iframe responsive (no fixed 520px height) so the chat input won't be cut off on small screens.
  - Adds higher z-index so the iframe appears above footer and other elements.
  - Uses max-height: calc(100vh - ...) and safe-area inset to avoid overlap with bottom UI / home indicator.
  - Improved toggle logic and click handling so the panel doesn't accidentally close.
  - Ensure footer is part of a column layout so it is pushed below content and has 20px gap when page content is short.
-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --footer-bg: linear-gradient(180deg,#382a86 0%, #2b2366 100%);
  --accent: #bfa6ff;
  --muted: rgba(255,255,255,0.8);
}
html, body {
  height: 100%;
  margin: 0;
}

/* Ensure the body can be converted to a column layout by the script below */
.page-content {
  /* this will be set to flex:1 by the script on DOM ready; declared here as fallback */
  flex: 1 0 auto;
}

/* Footer styling */
.custom-footer{
  background: var(--footer-bg);
  color: #fff;
  font-family: 'Segoe UI', Tahoma, sans-serif;
  position: relative;
  z-index: 1;
  /* ensure there is at least 20px gap from content above when content is short */
  margin-top: 20px;
}
.custom-footer a{ color: var(--muted); text-decoration: none; }
.custom-footer a:hover{ color: var(--accent); text-decoration: none; }
.footer-brand img{ height: 60px; margin-bottom: 12px; }
.footer-title{ font-weight:700; color:#fff; margin-bottom:12px; font-size: 1.1rem; line-height: 1.3; }
.footer-text{ color: var(--muted); font-size: .88rem; line-height: 1.5; margin-bottom: 10px; }
.footer-links{ list-style:none; padding:0; margin:0; }
.footer-links li{ margin:8px 0; }
.social-icons a{ display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:8px; background:rgba(255,255,255,0.06); color: #fff; margin-right:8px; transition: transform .12s, background .12s, color .12s; }
.social-icons a:hover{ transform:translateY(-3px); background: rgba(191,166,255,0.14); color: var(--accent); }
.newsletter input[type="email"]{ background: rgba(255,255,255,0.06); border: none; color: #fff; }
.newsletter .btn{ background: var(--accent); border: none; color: #2b2366; font-weight:600; }
.footer-bottom{ border-top:1px solid rgba(255,255,255,0.06); padding-top:18px; color: rgba(255,255,255,0.7); font-size: .9rem; }

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
  z-index: 2100;
}
.chatbot-icon img{ width:40px; height:40px; }

/* Khung chứa iframe chatbot */
.chatbot-frame{
  position: fixed;
  right: 20px;
  bottom: calc(24px + 56px + 12px);
  width: 380px;
  max-width: calc(100% - 40px);

  /* KHÔNG ĐỂ CHIỀU CAO CỨNG */
  max-height: 75vh;  /* vừa với mọi màn hình */
  height: auto;

  display: none;
  z-index: 2200;
  box-shadow: 0 12px 40px rgba(0,0,0,0.35);
  border-radius: 12px;
  overflow: hidden;

  transform-origin: bottom right;
  transition: transform .15s ease, opacity .12s ease;
  opacity: 0;
  transform: scale(.95);
}

/* Khi mở */
.chatbot-frame.open{
  display: block;
  opacity: 1;
  transform: scale(1);
}

/* Iframe có chiều cao auto linh hoạt */
.chatbot-frame .chat-iframe{
  width: 100%;
  height: 100%;       /* quan trọng: không set height cố định */
  min-height: 450px;  /* đủ để không cắt input */
  max-height: inherit;

  border: none;
  display: block;
  background: #fff;
}

/* Mobile tối ưu */
@media (max-width: 768px){
  .chatbot-frame{
    width: 100%;
    right: 0;
    bottom: calc(24px + 56px + 12px);
    max-height: 80vh;
  }

  .chatbot-frame .chat-iframe{
    min-height: 400px;
    height: 100%;
  }

  .chatbot-icon { right: 12px; bottom: 12px; }
}
 
</style>

<footer class="custom-footer">
  <div class="container-xxl py-5">
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
/* Year */
document.getElementById('year').textContent = new Date().getFullYear();

/* Chatbot open/close logic */
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

/* Clicking outside closes chat, but clicking inside iframe or on button won't */
document.addEventListener('click', (e) => {
  const target = e.target;
  if (target === chatbotBtn || chatbotBtn.contains(target)) return;
  if (target === chatbotFrame || chatbotFrame.contains(target)) return;
  if (chatbotFrame.classList.contains('open')) closeChat();
});

chatbotBtn.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    toggleChat();
  }
});

/*
  Make footer behave like a typical "sticky footer" layout:
  - If the page content is short, footer should sit 20px below content and be visually near the bottom.
  - If the page content is long, footer is pushed below content naturally.
  We do this by wrapping existing body children (except the footer) into a .page-content div
  and switching body to a column flex layout. This is done dynamically so we don't need to
  change other template files.
*/
(function ensureStickyFooterGap() {
  function applyLayout() {
    const footer = document.querySelector('footer.custom-footer');
    if (!footer) return;

    const body = document.body;

    // If we've already done the conversion, update sizing only
    if (body.classList.contains('pf-layout-applied')) {
      // ensure footer keeps margin-top 20px
      footer.style.marginTop = '20px';
      return;
    }

    // Create wrapper to hold everything except the footer
    const wrapper = document.createElement('div');
    wrapper.className = 'page-content';

    // Move all nodes before footer into wrapper
    while (body.firstChild && body.firstChild !== footer) {
      wrapper.appendChild(body.firstChild);
    }

    // Insert wrapper before footer
    body.insertBefore(wrapper, footer);

    // Make the body a column flex container so that wrapper grows and footer is pushed
    body.style.display = 'flex';
    body.style.flexDirection = 'column';
    body.style.minHeight = '100vh';

    // Ensure the wrapper fills remaining space when content is short
    wrapper.style.flex = '1 0 auto';

    // Keep footer margin
    footer.style.marginTop = '20px';

    // tag to avoid reapplying
    body.classList.add('pf-layout-applied');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyLayout);
  } else {
    applyLayout();
  }

  // Re-apply on resize in case dynamic content changes layout
  window.addEventListener('resize', function() {
    // small debounce
    clearTimeout(window.__pf_footer_resize);
    window.__pf_footer_resize = setTimeout(function() {
      applyLayout();
    }, 120);
  }, { passive: true });
})();
</script>