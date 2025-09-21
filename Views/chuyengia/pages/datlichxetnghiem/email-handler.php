<?php
// Email handler for appointment notifications
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHandler {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    private function setupSMTP() {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com'; // Thay đổi theo SMTP server của bạn
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = 'your-email@gmail.com'; // Email của bệnh viện
            $this->mail->Password   = 'your-app-password'; // App password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            $this->mail->CharSet    = 'UTF-8';
        } catch (Exception $e) {
            error_log("SMTP setup failed: " . $e->getMessage());
        }
    }
    
    public function sendPaymentRequest($patientEmail, $patientName, $appointmentDetails, $paymentLink) {
        try {
            // Recipients
            $this->mail->setFrom('your-email@gmail.com', 'Bệnh Viện Hạnh Phúc');
            $this->mail->addAddress($patientEmail, $patientName);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Yêu cầu thanh toán - Lịch xét nghiệm';
            
            $emailBody = $this->getPaymentEmailTemplate($patientName, $appointmentDetails, $paymentLink);
            $this->mail->Body = $emailBody;
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function getPaymentEmailTemplate($patientName, $appointmentDetails, $paymentLink) {
        $expiryTime = date('H:i d/m/Y', strtotime('+30 minutes'));
        
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2c5aa0; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .appointment-details { background-color: white; padding: 15px; margin: 15px 0; border-left: 4px solid #2c5aa0; }
                .payment-button { display: inline-block; background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 15px 0; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Bệnh Viện Hạnh Phúc</h1>
                    <p>Yêu cầu thanh toán lịch xét nghiệm</p>
                </div>
                
                <div class='content'>
                    <h2>Xin chào {$patientName},</h2>
                    <p>Chúng tôi đã nhận được yêu cầu đặt lịch xét nghiệm của bạn. Vui lòng thanh toán để xác nhận lịch hẹn.</p>
                    
                    <div class='appointment-details'>
                        <h3>Thông tin lịch hẹn:</h3>
                        <p><strong>Loại xét nghiệm:</strong> {$appointmentDetails['test_name']}</p>
                        <p><strong>Ngày xét nghiệm:</strong> {$appointmentDetails['date']}</p>
                        <p><strong>Giờ xét nghiệm:</strong> {$appointmentDetails['time']}</p>
                        <p><strong>Bác sĩ đặt lịch:</strong> {$appointmentDetails['doctor']}</p>
                        <p><strong>Số tiền cần thanh toán:</strong> {$appointmentDetails['amount']} VNĐ</p>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Lưu ý quan trọng:</strong> Bạn cần thanh toán trước <strong>{$expiryTime}</strong>. 
                        Nếu không thanh toán trong thời gian này, lịch hẹn sẽ tự động bị hủy.
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$paymentLink}' class='payment-button'>THANH TOÁN NGAY</a>
                    </div>
                    
                    <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua số điện thoại: <strong>1900-xxxx</strong></p>
                </div>
                
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Bệnh Viện Hạnh Phúc. Tất cả các quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
