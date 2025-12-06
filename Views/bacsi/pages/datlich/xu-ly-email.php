<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once('Controllers/cemailthanhtoan.php');
class XuLyEmail {
    private $cau_hinh_email;
    
    public function __construct() {
        $this->cau_hinh_email = [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => 'nguyenthanhthuytrang12@gmail.com', // Thay đổi email của bạn
            'password' => 'zxuf skva amck qced',    // Thay đổi mật khẩu ứng dụng
            'from_email' => 'nguyenthanhthuytrang12@gmail.com',
            'from_name' => 'Bệnh Viện Hạnh Phúc'
        ];
    }
    
    public function gui_email_yeu_cau_thanh_toan($email_benh_nhan, $ten_benh_nhan,$hinh_thuc, $ngay_hen, $gio_hen, $ma_lich_hen, $gia_kham) {
        $mail = new PHPMailer(true);
        
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = $this->cau_hinh_email['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->cau_hinh_email['username'];
            $mail->Password = $this->cau_hinh_email['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->cau_hinh_email['port'];
            $mail->CharSet = 'UTF-8';
            
            // Thông tin người gửi và người nhận
            $mail->setFrom($this->cau_hinh_email['from_email'], $this->cau_hinh_email['from_name']);
            $mail->addAddress($email_benh_nhan, $ten_benh_nhan);
            
            // Tạo link thanh toán có mã hóa
            $ma_bao_mat = $this->tao_ma_bao_mat($ma_lich_hen);
            $link_thanh_toan = "https://hanhphuc.site?action=thanhtoan&ma=" . urlencode($ma_bao_mat) . "&maphieukhambenh=" . $ma_lich_hen;
            
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Yêu Cầu Thanh Toán - Lịch hẹn khám #' . $ma_lich_hen;
            $mail->Body = $this->tao_noi_dung_email_html($ten_benh_nhan,$hinh_thuc, $ngay_hen, $gio_hen, $ma_lich_hen, $link_thanh_toan, $gia_kham);
            
            $mail->send();
            
            // Lưu thông tin email đã gửi vào database
            $cemailthanhtoan = new cEmail();
            $thoi_gian_gui = date('Y-m-d H:i:s');
            $thoi_gian_het_han = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $cemailthanhtoan->insert_emailyeucauthanhtoan($ma_lich_hen, $email_benh_nhan, $thoi_gian_gui, $thoi_gian_het_han,$gia_kham);
            
            return true;
        } catch (Exception $e) {
            error_log("Lỗi gửi email: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    private function tao_ma_bao_mat($ma_lich_hen) {
        $chuoi_bi_mat = "BENH_VIEN_HANH_PHUC_2025";
        return hash('sha256', $ma_lich_hen . $chuoi_bi_mat . time());
    }
    
    private function tao_noi_dung_email_html($ten_benh_nhan, $hinh_thuc, $ngay_hen, $gio_hen, $ma_lich_hen, $link_thanh_toan, $gia_kham) {
        return "
        <!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8fafc; padding: 30px; border-radius: 0 0 8px 8px; }
                .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #2563eb; }
                .btn-thanh-toan { display: inline-block; background: #16a34a; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
                .canh-bao { background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🏥 Bệnh Viện Hạnh Phúc</h1>
                    <p>Yêu Cầu Thanh Toán Lịch Hẹn Khám Bệnh</p>
                </div>
                
                <div class='content'>
                    <h2>Xin chào {$ten_benh_nhan},</h2>
                    <p>Chúng tôi đã nhận được yêu cầu đặt lịch khám của bạn. Để hoàn tất việc đặt lịch, vui lòng thanh toán trong vòng <strong>30 phút</strong>.</p>
                    
                    <div class='info-box'>
                        <h3>📋 Thông Tin Lịch Hẹn</h3>
                        <p><strong>Mã lịch hẹn:</strong> {$ma_lich_hen}</p>
                        <p><strong>Hình thức:</strong> #{$hinh_thuc}</p>
                        <p><strong>Ngày hẹn:</strong> {$ngay_hen}</p>
                        <p><strong>Giờ hẹn:</strong> {$gio_hen}</p>
                        <p><strong>Giá khám:</strong> {$gia_kham}</p>
                    </div>
                    
                    <div class='canh-bao'>
                        <strong>⚠️ Lưu ý quan trọng:</strong><br>
                        Nếu không thanh toán trong vòng 30 phút, lịch hẹn sẽ tự động bị hủy và bạn cần đặt lại.
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$link_thanh_toan}' class='btn-thanh-toan'>💳 THANH TOÁN NGAY</a>
                    </div>
                    
                    <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ:</p>
                    <ul>
                        <li>📞 Hotline: 1900-1234</li>
                        <li>📧 Email: hotro@benhvienhanhphuc.com</li>
                        <li>🌐 Website: www.benhvienhanhphuc.com</li>
                    </ul>
                </div>
                
                <div class='footer'>
                    <p>&copy; 2024 Bệnh Viện Hạnh Phúc. Tất cả quyền được bảo lưu.</p>
                    <p>Email này được gửi tự động, vui lòng không trả lời.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
