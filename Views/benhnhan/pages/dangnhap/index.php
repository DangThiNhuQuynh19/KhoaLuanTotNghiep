<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Đăng nhập - Bệnh viện Hạnh Phúc</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    .login-wrapper {
      background-color: #f8f9fa;
      min-height: calc(100vh - 80px);
      margin-top: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      width: 100%;
    }

    .login-wrapper .container {
        display: flex;
	margin: 0;
	padding: 0;
        width: 100%;
        max-width: 1200px;
        background: white;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        border-radius: 20px;
        overflow: hidden;
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .image-side {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        flex: 1;
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .image-side::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        animation: rotate 20s linear infinite;
        z-index: 1;
    }

    @keyframes rotate {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }
    
    .image-side h1 {
        font-size: 32px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .image-side p {
      font-size: 16px;
      text-align: center;
      margin-bottom: 30px;
      line-height: 1.8;
      position: relative;
      z-index: 2;
      font-weight: 300;
      opacity: 0.95;
      animation: fadeInUp 0.8s ease-out 0.4s both;
    }
    
    .image-side img {
      width: 200px;
      height: auto;
      margin-bottom: 30px;
      position: relative;
      z-index: 2;
      filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
      animation: fadeInUp 0.8s ease-out both;
    }
    
    .login-side {
      background-color: #ffffff;
      flex: 1;
      padding: 50px 45px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      animation: fadeIn 0.8s ease-out 0.3s both;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    
    .login-box {
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }
    
    .login-box img.logo {
      width: 80px;
      height: auto;
      margin-bottom: 20px;
    }
    
    .login-box h2 {
      color: #333;
      margin-bottom: 35px;
      font-size: 32px;
      font-weight: 700;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .input-group {
      position: relative;
      margin-bottom: 25px;
    }
    
    .input-group label {
      display: block;
      text-align: left;
      margin-bottom: 10px;
      font-weight: 500;
      color: #555;
      font-size: 14px;
      letter-spacing: 0.3px;
    }
    
    .input-group input {
      width: 100%;
      padding: 15px 18px;
      padding-left: 48px;
      border: 2px solid #e8e8e8;
      border-radius: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      background-color: #fafafa;
      font-family: 'Poppins', sans-serif;
      line-height: 1.5;
    }
    
    .input-group input:focus {
      outline: none;
      border-color: #667eea;
      background-color: #fff;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
      transform: translateY(-2px);
    }

    .input-group input::placeholder {
      color: #bbb;
      font-size: 14px;
    }
    
    .input-group i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      margin-top: 17px;
      color: #aaa;
      font-size: 16px;
      transition: color 0.3s ease;
      pointer-events: none;
    }

    .input-group input:focus ~ i {
      color: #667eea;
    }
    
    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    
    .remember {
      display: flex;
      align-items: center;
    }
    
    .remember input {
      margin-right: 8px;
      cursor: pointer;
    }

    .remember label {
      font-size: 14px;
      color: #666;
      cursor: pointer;
    }
    
    .forgot-password {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s;
    }
    
    .forgot-password:hover {
      color: #764ba2;
      text-decoration: none;
      transform: translateX(3px);
    }
    
    button {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
      font-family: 'Poppins', sans-serif;
      letter-spacing: 0.5px;
    }
    
    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
    }
    
    button:active {
      transform: translateY(-1px);
    }
    
    .message {
      margin-bottom: 20px;
      color: #e74c3c;
      font-size: 14px;
      text-align: center;
      padding: 12px 16px;
      border-radius: 10px;
      background-color: rgba(231, 76, 60, 0.1);
      border-left: 4px solid #e74c3c;
      display: none;
      animation: shake 0.5s ease;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }
    
    .message.show {
      display: block;
    }
    
    .register-link {
      text-align: center;
      margin-top: 30px;
      font-size: 14px;
      color: #666;
    }
    
    .register-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s;
    }
    
    .register-link a:hover {
      color: #764ba2;
      text-decoration: none;
    }
    
    .social-login {
      margin-top: 30px;
      text-align: center;
    }
    
    .social-login p {
      color: #999;
      margin-bottom: 20px;
      position: relative;
      font-size: 13px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    .social-login p::before,
    .social-login p::after {
      content: "";
      position: absolute;
      top: 50%;
      width: 35%;
      height: 1px;
      background: linear-gradient(to right, transparent, #ddd, transparent);
    }
    
    .social-login p::before {
      left: 0;
    }
    
    .social-login p::after {
      right: 0;
    }
    
    .social-icons {
      display: flex;
      justify-content: center;
      gap: 15px;
    }
    
    .social-icons a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background-color: #f8f9fa;
      color: #333;
      font-size: 20px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    
    .social-icons a:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    
    .social-icons a.facebook {
      color: #3b5998;
    }

    .social-icons a.facebook:hover {
      background-color: #3b5998;
      color: white;
    }
    
    .social-icons a.google {
      color: #db4437;
    }

    .social-icons a.google:hover {
      background-color: #db4437;
      color: white;
    }
    
    .social-icons a.apple {
      color: #000;
    }

    .social-icons a.apple:hover {
      background-color: #000;
      color: white;
    }
    
    /* Responsive styles */
    @media (max-width: 768px) {
      .login-wrapper {
        padding: 20px 10px;
      }

      .container {
        flex-direction: column;
        max-width: 500px;
      }
      
      .image-side {
        padding: 40px 25px;
      }
      
      .image-side h1 {
        font-size: 26px;
      }
      
      .image-side p {
        font-size: 14px;
      }

      .image-side img {
        width: 150px;
      }
      
      .login-side {
        padding: 40px 25px;
      }

      .login-box h2 {
        font-size: 28px;
      }
    }
    
    @media (max-width: 480px) {
      .options {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
      
      .social-icons {
        gap: 12px;
      }
      
      .social-icons a {
        width: 44px;
        height: 44px;
        font-size: 18px;
      }
      
      .login-box h2 {
        font-size: 26px;
      }

      .image-side h1 {
        font-size: 22px;
      }
    }

    .google-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 14px 20px;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        background-color: #fff;
        color: #555;
        font-weight: 500;
        font-size: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-top: 20px;
        font-family: 'Poppins', sans-serif;
    }

    .google-btn img {
        width: 22px;
        height: 22px;
        margin-right: 12px;
    }

    .google-btn:hover {
        background-color: #f8f9fa;
        border-color: #667eea;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }

  </style>
</head>
<div class="login-wrapper">
  <div class="container">
      <div class="image-side">
        <img src="Assets/img/logo-banner.png" alt="Logo" />
        <h1>Chào mừng đến với Bệnh viện Hạnh Phúc</h1>
        <p>Đăng nhập để đặt lịch khám, xem hồ sơ bệnh án và nhận tư vấn từ đội ngũ bác sĩ chuyên nghiệp của chúng tôi.</p>
      </div>
    
    <div class="login-side">
      <div class="login-box">
        <h2>Đăng nhập</h2>
        
        <div class="message" id="errorMessage"></div>
        
        <form method="POST" id="loginForm">
          <div class="input-group">
            <label for="tentk">Tên tài khoản:</label>
            <input type="email" id="tentk" name="tentk" placeholder="Nhập email của bạn" required />
            <i class="fas fa-envelope"></i>
          </div>
          
          <div class="input-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required />
            <i class="fas fa-lock"></i>
          </div>
          
          <div class="options">
            <div class="remember">
              <!-- <input type="checkbox" id="remember" name="remember" />
              <label for="remember">Ghi nhớ đăng nhập</label> -->
            </div>
            <a href="?quenmatkhau" class="forgot-password">Quên mật khẩu?</a>
          </div>
          
          <button type="submit" name="btndangnhap">Đăng nhập</button>
        </form>
        <?php
          require_once 'config.php';

          $login_url = $client->createAuthUrl();
        ?>
        <a href="<?= htmlspecialchars($login_url) ?>" class="google-btn">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo">
            <span>Đăng nhập bằng Google</span>
        </a>
        
        <div class="register-link">
          Bạn chưa có tài khoản? <a href="?action=dangky">Đăng ký ngay</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
    include_once("Views/benhnhan/pages/dangnhap/xulydangnhap.php");
?>
