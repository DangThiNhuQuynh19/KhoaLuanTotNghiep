<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo hồ sơ - Bệnh viện Hạnh Phúc</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="Views/benhnhan/pages/taohoso/css.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("Assets/config.php");
include_once("Controllers/cbenhnhan.php");

include_once("Controllers/ctaikhoan.php");
include_once("Controllers/ctinhthanhpho.php");
include_once ("Controllers/cxaphuong.php");

$cthanhpho = new cTinhThanhPho();
$thanhpho_list = $cthanhpho->get_tinhthanhpho();

$cxaphuong = new cXaPhuong();
$xaphuong_list = $cxaphuong->get_xaphuong();
$message = "";

$email = $_SESSION['user']['tentk'];
$pBenhNhan = new cBenhNhan();
$taikhoan = $pBenhNhan ->getbenhnhanbytk($email);
// Xử lý khi submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $hoten = $_POST['fullname'];
    $ngaysinh = $_POST['dob'];
    $dantoc = $_POST['dantoc'] ?? '';
    $cccd = $_POST['cccd'] ?? '';
    $gioitinh = $_POST['gender'];
    $nghenghiep = $_POST['job'];
    $job_other = $_POST['job_other'] ?? '';
    if ($nghenghiep === 'Khác' && !empty($job_other)) {
        $nghenghiep = $job_other;
    }
    $tiensucuagiadinh = $_POST['history_family'];
    $tiensucuabanthan = $_POST['history_my'];
    $sonha = $_POST['sonha'];
    $xa = $_POST['xa'];
    $tinh = $_POST['tinh'];
    $quanhe = $_POST['relative'];
    $age = getAge($ngaysinh);

    // Validate cơ bản
    if ($age >= 16 && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email không hợp lệ.";
    } elseif ($age >= 16 && !preg_match("/^[0-9]{10}$/", $sdt)) {
        $message = "Số điện thoại không hợp lệ (10 số).";
    } elseif ($age >= 16 && !preg_match("/^[0-9]{9,12}$/", $cccd)) {
        $message = "Số CCCD không hợp lệ (9-12 số).";
    } elseif (!preg_match("/^[a-zA-ZÀ-ỹ\s]+$/u", $hoten)) {
        $message = "Họ tên chỉ được chứa chữ cái và khoảng trắng.";
    } else {
        // Nếu không có lỗi thì gọi controller
        if ($message == "") {
            $controller = new cTaiKhoan();
            
            // File upload function
            function uploadFile($fileInput){
                if(isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error']==0){
                    $ext = pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION);
                    if(!in_array(strtolower($ext), ['jpg','jpeg','png'])) return "";
                    $name = uniqid('upload_').'.'.$ext;
                    $path = 'Assets/img/cccd/'.$name;
                    if(move_uploaded_file($_FILES[$fileInput]['tmp_name'], $path))
                      return [
                        'path' => $path,
                        'name' => $name
                    ];
                }
                return "";
            }
            
            $mabenhnhan = "BN_" . rand(10000000, 99999999);

            $cccd_truoc_path = uploadFile('cccd_truoc');
            $cccd_sau_path = uploadFile('cccd_sau');
            $birth_cert_path = uploadFile('birth_cert');
            $gh_cccd_truoc_path = uploadFile('gh_cccd_truoc');
            $gh_cccd_sau_path = uploadFile('gh_cccd_sau');
            
            // Validate required file uploads based on age
            if ($age >= 16) {
                if (empty($cccd_truoc_path) || empty($cccd_sau_path)) {
                    $message = "Vui lòng upload đầy đủ ảnh CCCD mặt trước và mặt sau.";
                }
            } else {
                if (empty($birth_cert_path)) {
                    $message = "Vui lòng upload ảnh giấy khai sinh.";
                }
            }
            
            $cccd_truoc_name = is_array($cccd_truoc_path) ? $cccd_truoc_path['name'] : '';
            $cccd_sau_name = is_array($cccd_sau_path) ? $cccd_sau_path['name'] : '';
            $birth_cert_name = is_array($birth_cert_path) ? $birth_cert_path['name'] : '';
            $gh_cccd_truoc_name = is_array($gh_cccd_truoc_path) ? $gh_cccd_truoc_path['name'] : '';
            $gh_cccd_sau_name = is_array($gh_cccd_sau_path) ? $gh_cccd_sau_path['name'] : '';
            echo $taikhoan['manguoidung'];
            if (empty($message)) {
                $result = $pBenhNhan->insertbenhnhan($mabenhnhan, $email, $hoten, $ngaysinh, $sdt, $dantoc, $cccd, $cccd_truoc_name, $birth_cert_name, $cccd_sau_name, $gioitinh, $nghenghiep, $tiensucuagiadinh, $tiensucuabanthan, $sonha, $xa, $tinh, $taikhoan['manguoidung'], $quanhe);
                if ($result === true) {
                    echo "<script>
                    Swal.fire({
                        title: 'Thành công!',
                        text: 'Tạo hồ sơ thành công!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location='?action=caidat';
                        }
                    });
                    </script>";
                    exit();
                } else {
                    $message = $result ? $result : "Đã có lỗi xảy ra khi tạo hồ sơ.";
                }
            }
        }
    }
}

function getAge($dob) {
    $dobTimestamp = strtotime($dob);
    $today = strtotime(date("Y-m-d"));
    return floor(($today - $dobTimestamp) / (365.25*24*60*60));
}
?>
<body>
    <div class="container">
        <div class="form-container">
            <?php if($message != ""): ?>
                <script>
                    Swal.fire({
                        title: 'Thông báo',
                        text: '<?php echo $message; ?>',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>
            <?php endif; ?>
            <h2 style ="margin-bottom: 10px;">Tạo hồ sơ khám bệnh</h2>
            <form method="POST" enctype="multipart/form-data" id="registrationForm">
                <!-- Thông tin cá nhân -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user"></i>
                        Thông tin cá nhân
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên <span class="required">*</span></label>
                            <input type="text" name="fullname" required value="<?php echo $_POST['fullname'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Giới tính <span class="required">*</span></label>
                            <select name="gender" required>
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" <?php echo ($_POST['gender'] ?? '') == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo ($_POST['gender'] ?? '') == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="Khác" <?php echo ($_POST['gender'] ?? '') == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ngày sinh <span class="required">*</span></label>
                        <input type="date" name="dob" id="dob" required 
                               max="<?php echo date('Y-m-d'); ?>" 
                               min="<?php echo date('Y-m-d', strtotime('-120 years')); ?>" 
                               onchange="toggleGuardian()" 
                               value="<?php echo $_POST['dob'] ?? ''; ?>">
                        <div id="age-display" class="age-info"></div>
                    </div>

                    <div class="form-group">
                        <label for="">Dân tộc</label>
                        <select name="dantoc" id="dantoc">
                            <option value="">--Chọn dân tộc--</option>
                            <option value="Kinh">Kinh</option>
                            <option value="Tày">Tày</option>
                            <option value="Thái">Thái</option>
                            <option value="Hoa">Hoa</option>
                            <option value="Khơ-me">Khơ-Me</option>
                            <option value="Mường">Mường</option>
                            <option value="Nùng">Nùng</option>
                            <option value="HMông">HMông</option>
                            <option value="Dao">Dao</option>
                            <option value="Gia-rai">Gia-rai</option>
                            <option value="Ngái">Ngái</option>
                            <option value="Ê-đê">Ê-đê</option>
                            <option value="Ba-na">Ba-na</option>
                            <option value="Xơ-Đăng">Xơ-Đăng</option>
                            <option value="Sán chay">Sán chay</option>
                            <option value="Cơ-ho">Cơ-ho</option>
                            <option value="Chăm">Chăm</option>
                            <option value="Sán Dìu">Sán Dìu</option>
                            <option value="Hrê">Hrê</option>
                            <option value="Mnông">Mnông</option>
                            <option value="Ra-glai">Ra-glai</option>
                            <option value="Xtiêng">Xtiêng</option>
                            <option value="Bru-Vân Kiều">Bru-Vân Kiều</option>
                            <option value="Thổ">Giáy</option>
                            <option value="Cơ-tu">Cơ-tu</option>
                            <option value="Gié">Triêng</option>
                            <option value="Mạ">Mạ</option>
                            <option value="Khơ-mú">Khơ-mú</option>
                            <option value="Co">Co</option>
                            <option value="Tà-ôi">Tà-ôi</option>
                            <option value="Chơ-ro">Chơ-ro</option>
                            <option value="Kháng">Kháng</option>
                            <option value="Xinh-mun">Xinh-mun</option>
                            <option value="Hà Nhì">Hà Nhì</option>
                            <option value="Chu ru">Chu ru</option>
                            <option value="Lào">Lào</option>
                            <option value="La Chí">La Chí</option>
                            <option value="La Ha">La Ha</option>
                            <option value="Phù Lá">Phù Lá</option>
                            <option value="La Hủ">La Hủ</option>
                            <option value="Lự">Lự</option>
                            <option value="Lô Lô">Lô Lô</option>
                            <option value="Chứt">Chứt</option>
                            <option value="Mảng">Mảng</option>
                            <option value="Pà Thẻn">Pà Thẻn</option>
                            <option value="Co Lao">Co Lao</option>
                            <option value="Cống">Cống</option>
                            <option value="Bố Y">Bố Y</option>
                            <option value="Si La">Si La</option>
                            <option value="Pu Péo">Pu Péo</option>
                            <option value="Brâu">Brâu</option>
                            <option value="Ơ Đu">Ơ Đu</option>
                            <option value="Rơ măm">Rơ măm</option>
                        </select>
                    </div>
                </div>

                <!-- Thông tin liên hệ -->
                <div class="form-section" id="contact-section">
                    <div class="section-title">
                        <i class="fas fa-phone"></i>
                        Thông tin liên hệ
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" id="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại <span class="required">*</span></label>
                            <input type="text" name="sdt" id="sdt" placeholder="0123456789" value="<?php echo $_POST['sdt'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Giấy tờ tùy thân -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-id-card"></i>
                        Giấy tờ tùy thân
                    </div>

                    <!-- CCCD cho người >= 16 tuổi -->
                    <div id="cccd-section">
                        <div class="form-group">
                            <label>Số CCCD/CMND <span class="required">*</span></label>
                            <input type="text" name="cccd" id="cccd" placeholder="Nhập 9-12 số" value="<?php echo $_POST['cccd'] ?? ''; ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>CCCD mặt trước <span class="required">*</span></label>
                                <div class="file-upload">
                                    <input type="file" name="cccd_truoc" accept="image/*" onchange="previewImage(this, 'preview-truoc')">
                                    <div class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Chọn ảnh CCCD mặt trước</span>
                                    </div>
                                </div>
                                <img id="preview-truoc" class="image-preview">
                            </div>
                            <div class="form-group">
                                <label>CCCD mặt sau <span class="required">*</span></label>
                                <div class="file-upload">
                                    <input type="file" name="cccd_sau" accept="image/*" onchange="previewImage(this, 'preview-sau')">
                                    <div class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Chọn ảnh CCCD mặt sau</span>
                                    </div>
                                </div>
                                <img id="preview-sau" class="image-preview">
                            </div>
                        </div>
                    </div>

                    <!-- Giấy khai sinh cho trẻ em < 16 tuổi -->
                    <div id="birth-cert-section" class="hidden">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i>
                            Trẻ em dưới 16 tuổi cần cung cấp giấy khai sinh thay vì CCCD
                        </div>
                        <div class="form-group">
                            <label>Giấy khai sinh <span class="required">*</span></label>
                            <div class="file-upload">
                                <input type="file" name="birth_cert" accept="image/*" onchange="previewImage(this, 'preview-birth')">
                                <div class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Chọn ảnh giấy khai sinh</span>
                                </div>
                            </div>
                            <img id="preview-birth" class="image-preview">
                        </div>
                    </div>
                </div>

                <!-- Thông tin nghề nghiệp -->
                <div class="form-section" id="job-section">
                    <div class="section-title">
                        <i class="fas fa-briefcase"></i>
                        Thông tin nghề nghiệp
                    </div>
                    
                    <div class="form-group">
                        <label>Nghề nghiệp <span class="required">*</span></label>
                        <select name="job" id="job" onchange="toggleOtherJob()" required>
                            <option value="">-- Chọn nghề nghiệp --</option>
                            <option value="Học sinh" <?php echo ($_POST['job'] ?? '') == 'Học sinh' ? 'selected' : ''; ?>>Học sinh</option>
                            <option value="Sinh viên" <?php echo ($_POST['job'] ?? '') == 'Sinh viên' ? 'selected' : ''; ?>>Sinh viên</option>
                            <option value="Công nhân" <?php echo ($_POST['job'] ?? '') == 'Công nhân' ? 'selected' : ''; ?>>Công nhân</option>
                            <option value="Nông dân" <?php echo ($_POST['job'] ?? '') == 'Nông dân' ? 'selected' : ''; ?>>Nông dân</option>
                            <option value="Nhân viên văn phòng" <?php echo ($_POST['job'] ?? '') == 'Nhân viên văn phòng' ? 'selected' : ''; ?>>Nhân viên văn phòng</option>
                            <option value="Kinh doanh tự do" <?php echo ($_POST['job'] ?? '') == 'Kinh doanh tự do' ? 'selected' : ''; ?>>Kinh doanh tự do</option>
                            <option value="Bác sĩ" <?php echo ($_POST['job'] ?? '') == 'Bác sĩ' ? 'selected' : ''; ?>>Bác sĩ</option>
                            <option value="Y tá/Điều dưỡng" <?php echo ($_POST['job'] ?? '') == 'Y tá/Điều dưỡng' ? 'selected' : ''; ?>>Y tá/Điều dưỡng</option>
                            <option value="Kỹ sư" <?php echo ($_POST['job'] ?? '') == 'Kỹ sư' ? 'selected' : ''; ?>>Kỹ sư</option>
                            <option value="Giáo viên" <?php echo ($_POST['job'] ?? '') == 'Giáo viên' ? 'selected' : ''; ?>>Giáo viên</option>
                            <option value="Nghỉ hưu" <?php echo ($_POST['job'] ?? '') == 'Nghỉ hưu' ? 'selected' : ''; ?>>Nghỉ hưu</option>
                            <option value="Khác" <?php echo ($_POST['job'] ?? '') == 'Khác' ? 'selected' : ''; ?>>Khác</option>
                        </select>
                    </div>

                    <div class="form-group hidden" id="job-other-group">
                        <label>Nghề nghiệp khác <span class="required">*</span></label>
                        <input type="text" name="job_other" id="job-other" placeholder="Nhập nghề nghiệp cụ thể" value="<?php echo $_POST['job_other'] ?? ''; ?>">
                    </div>
                </div>

                <!-- Tiền sử bệnh -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-notes-medical"></i>
                        Tiền sử bệnh
                    </div>
                    
                    <div class="form-group">
                        <label>Tiền sử bệnh của bản thân</label>
                        <textarea name="history_my" placeholder="Mô tả các bệnh đã mắc, thuốc đang sử dụng, dị ứng..."><?php echo $_POST['history_my'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tiền sử bệnh của gia đình</label>
                        <textarea name="history_family" placeholder="Mô tả các bệnh di truyền, bệnh mãn tính trong gia đình..."><?php echo $_POST['history_family'] ?? ''; ?></textarea>
                    </div>
                </div>

                <!-- Địa chỉ -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Địa chỉ
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tỉnh/Thành phố <span class="required">*</span></label>
                            <select name="tinh" id="tinh" onchange="loadXaPhuong()" required>
                                <option value="">-- Chọn tỉnh/thành phố --</option>
                                <?php foreach($thanhpho_list as $i): ?>
                                    <option value="<?php echo $i['matinhthanhpho']; ?>" <?php echo ($_POST['tinh'] ?? '') == $i['matinhthanhpho'] ? 'selected' : ''; ?>>
                                        <?php echo $i['tentinhthanhpho']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Xã/Phường <span class="required">*</span></label>
                            <select name="xa" id="xa" required>
                                <option value="">-- Chọn xã/phường --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Số nhà, tên đường <span class="required">*</span></label>
                        <input type="text" name="sonha" placeholder="Ví dụ: 123 Nguyễn Văn A" required value="<?php echo $_POST['sonha'] ?? ''; ?>">
                    </div>
                </div>
                <!-- Thông tin người giám hộ -->
                <div class="guardian-section" id="guardian-info">
                    <div class="section-title">
                        <i class="fas fa-user-shield"></i>
                        Thông tin người thân
                    </div>   
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ tên người thân</label>
                            <input type="text" name="gh_hoten" value="<?= htmlspecialchars($taikhoan['hoten'])?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Mối quan hệ với người thân</label>
                            <select name="relative" id="relative">
                                <option value="Bố">Bố</option>
                                <option value="Mẹ">Mẹ</option>
                                <option value="Con">Con</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="gh_dob" value="<?= htmlspecialchars($taikhoan['ngaysinh'])?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="gh_sdt" placeholder="0988467345" value="<?= htmlspecialchars(decryptData($taikhoan['sdt']))?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Email<span class="required">*</span></label>
                            <input type="text" name="gh_email" placeholder="bac@gmail.com" value="<?= htmlspecialchars(decryptData($taikhoan['email']))?>"disabled>
                        </div>
                        <div class="form-group">
                            <label>Số CCCD <span class="required">*</span></label>
                            <input type="text" name="gh_cccd" placeholder="Nhập 9-12 số" value="<?= htmlspecialchars(decryptData($taikhoan['cccd']))?>" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tỉnh/Thành phố <span class="required">*</span></label>
                            <select name="gh_tinh" id="gh_tinh" disabled>
                                    <option value ="<?= htmlspecialchars($taikhoan['matinhthanhpho'])?>"><?= htmlspecialchars($taikhoan['tentinhthanhpho'])?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Xã/Phường <span class="required">*</span></label>
                            <select name="gh_xa" id="gh_xa" disabled>
                                <option value ="<?= htmlspecialchars($taikhoan['maxaphuong'])?>"><?= htmlspecialchars($taikhoan['tenxaphuong'])?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Added missing address field for guardian -->
                    <div class="form-group">
                        <label>Số nhà, tên đường</label>
                        <input type="text" name="gh_sonha" placeholder="Ví dụ: 123 Nguyễn Văn A" value="<?= htmlspecialchars($taikhoan['sonha'])?>" disabled>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>CCCD mặt trước <span class="required">*</span></label>
                            <div class="file-upload">
                                <input type="file" name="gh_cccd_truoc" disabled>
                                <div class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Chọn ảnh CCCD mặt trước</span>
                                </div>
                            </div>
                            <img id="preview-gh-truoc" class="image-preview-gh " src="Assets/img/cccd/<?= $taikhoan['cccd_matruoc']?>" style=" margin-top: 15px; max-width: 200px; max-height: 150px;border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        </div>
                        <div class="form-group">
                            <label>CCCD mặt sau <span class="required">*</span></label>
                            <div class="file-upload">
                                <input type="file" name="gh_cccd_sau" disabled>
                                <div class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Chọn ảnh CCCD mặt sau</span>
                                </div>
                            </div>
                            <img id="preview-gh-sau" class="image-preview-gh "  src="Assets/img/cccd/<?= $taikhoan['cccd_matsau']?>" alt="CCCD mặt sau" style=" margin-top: 15px; max-width: 200px; max-height: 150px;border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"/>
                        </div>
                    </div>
                </div>

                <button type="submit" name="dangky" class="submit-btn">
                    <i class="fas fa-user-plus"></i>
                    Tạo mới
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php include_once ("js.php")?>
