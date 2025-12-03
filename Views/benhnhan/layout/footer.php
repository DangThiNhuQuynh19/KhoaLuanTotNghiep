<!-- Footer fragment: styles and markup only (no html/head/body wrappers) -->
<!-- Bootstrap and icons (if not already loaded in page head) -->
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
.chatbot-icon{ position: fixed; right: 20px; bottom: 24px; width:56px; height:56px; border-radius:50%; background: #fff; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 20px rgba(43,35,102,0.25); cursor:pointer; z-index:1050; }
.chatbot-icon img{ width:40px; height:40px; }
.chatbot-frame{ position: fixed; right: 20px; bottom: 96px; width: 360px; max-width: calc(100% - 40px); display:none; z-index:1050; }
.chatbot-frame iframe{ width:100%; height:520px; border-radius:10px; border:0; }

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

<div class="chatbot-icon" id="chatbotBtn" title="Mở chatbot">
    <img src="Assets/img/logo-banner.png" alt="Hospital Icon">
</div>

<!-- Khung chatbot -->
<div class="chatbot-frame" id="chatbotFrame">
  <iframe src="https://xaito.vn/App/embed/Chatbot/hfi87DePnBf0BLel" name="chatbot"></iframe>
</div>
<script>
  // Footer small scripts
  document.getElementById('year').textContent = new Date().getFullYear();

  const chatbotBtn = document.getElementById('chatbotBtn');
  const chatbotFrame = document.getElementById('chatbotFrame');

  chatbotBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    chatbotFrame.style.display = chatbotFrame.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', (e) => {
    if (!chatbotFrame.contains(e.target) && !chatbotBtn.contains(e.target)) {
      chatbotFrame.style.display = 'none';
    }
  });
</script>
</body>
</html>