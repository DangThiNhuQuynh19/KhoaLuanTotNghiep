<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class XuLyEmail {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Cấu hình SMTP
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com'; // Thay đổi theo SMTP server của bạn
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'your-email@gmail.com'; // Email của bạn
        $this->mail->Password = 'your-app-password'; // Mật khẩu ứng dụng
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->CharSet = 'UTF-8';
        
        // Thông tin người gửi
        $this->mail->setFrom('your-email@gmail.com', 'Bệnh Viện Hạnh Phúc');
    }
    
    /**
     * Gửi email đặt lịch khám (KHÔNG có QR code)
     */
    public function gui_email_dat_lich_kham($email_nguoi_nhan, $ten_benh_nhan, $ten_bac_si, $ngay_kham, $gio_kham, $hinh_thuc_kham) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email_nguoi_nhan);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Thông báo đặt lịch khám - Bệnh Viện Hạnh Phúc';
            
            $hinh_thuc_text = $hinh_thuc_kham === 'online' ? 'Khám Online' : 'Khám tại Bệnh viện';
            
            $this->mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f7fa;'>
                    <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 15px 15px 0 0;'>
                        <h1 style='color: white; margin: 0;'>Bệnh Viện Hạnh Phúc</h1>
                        <p style='color: white; margin: 10px 0 0 0;'>Thông báo đặt lịch khám</p>
                    </div>
                    
                    <div style='background: white; padding: 30px; border-radius: 0 0 15px 15px;'>
                        <p style='font-size: 16px; color: #333;'>Kính gửi <strong>{$ten_benh_nhan}</strong>,</p>
                        
                        <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                            Lịch khám của bạn đã được đặt thành công. Dưới đây là thông tin chi tiết:
                        </p>
                        
                        <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 10px 0; color: #666; font-size: 14px;'>
                                        <strong>👨‍⚕️ Bác sĩ:</strong>
                                    </td>
                                    <td style='padding: 10px 0; color: #333; font-size: 14px; text-align: right;'>
                                        {$ten_bac_si}
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #e0e0e0;'>
                                        <strong>📅 Ngày khám:</strong>
                                    </td>
                                    <td style='padding: 10px 0; color: #333; font-size: 14px; text-align: right; border-top: 1px solid #e0e0e0;'>
                                        {$ngay_kham}
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #e0e0e0;'>
                                        <strong>🕐 Giờ khám:</strong>
                                    </td>
                                    <td style='padding: 10px 0; color: #333; font-size: 14px; text-align: right; border-top: 1px solid #e0e0e0;'>
                                        {$gio_kham}
                                    </td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #e0e0e0;'>
                                        <strong>🏥 Hình thức:</strong>
                                    </td>
                                    <td style='padding: 10px 0; color: #333; font-size: 14px; text-align: right; border-top: 1px solid #e0e0e0;'>
                                        {$hinh_thuc_text}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;'>
                            <p style='margin: 0; color: #856404; font-size: 14px;'>
                                <strong>⚠️ Lưu ý quan trọng:</strong><br>
                                Vui lòng thanh toán trong vòng <strong>30 phút</strong> để giữ lịch hẹn. 
                                Nếu không thanh toán, lịch hẹn sẽ tự động bị hủy.
                            </p>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='#' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block; font-weight: 600;'>
                                Thanh toán ngay
                            </a>
                        </div>
                        
                        <p style='font-size: 14px; color: #666; line-height: 1.6;'>
                            Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua:
                        </p>
                        
                        <ul style='color: #666; font-size: 14px; line-height: 1.8;'>
                            <li>📞 Hotline: 1900-xxxx</li>
                            <li>📧 Email: support@benhvienhanhphuc.vn</li>
                            <li>🌐 Website: www.benhvienhanhphuc.vn</li>
                        </ul>
                        
                        <p style='font-size: 14px; color: #666; margin-top: 30px;'>
                            Trân trọng,<br>
                            <strong>Bệnh Viện Hạnh Phúc</strong>
                        </p>
                    </div>
                    
                    <div style='text-align: center; padding: 20px; color: #999; font-size: 12px;'>
                        <p>© 2025 Bệnh Viện Hạnh Phúc. Tất cả các quyền được bảo lưu.</p>
                    </div>
                </div>
            ";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi gửi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Gửi email yêu cầu thanh toán xét nghiệm (có thể có QR code)
     */
    public function gui_email_yeu_cau_thanh_toan($email_nguoi_nhan, $ten_benh_nhan, $ten_xet_nghiem, $ngay_xet_nghiem, $gio_xet_nghiem, $so_tien) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($email_nguoi_nhan);
            
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Yêu cầu thanh toán xét nghiệm - Bệnh Viện Hạnh Phúc';
            
            $this->mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #667eea;'>Yêu cầu thanh toán xét nghiệm</h2>
                    <p>Kính gửi <strong>{$ten_benh_nhan}</strong>,</p>
                    <p>Bạn có một lịch xét nghiệm cần thanh toán:</p>
                    <ul>
                        <li><strong>Xét nghiệm:</strong> {$ten_xet_nghiem}</li>
                        <li><strong>Ngày:</strong> {$ngay_xet_nghiem}</li>
                        <li><strong>Giờ:</strong> {$gio_xet_nghiem}</li>
                        <li><strong>Số tiền:</strong> " . number_format($so_tien) . " VNĐ</li>
                    </ul>
                    <p>Vui lòng thanh toán trong vòng 30 phút.</p>
                </div>
            ";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Lỗi gửi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
?>
