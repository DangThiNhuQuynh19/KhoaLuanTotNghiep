<?php
require 'vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

include_once("Assets/config.php");
include_once('Controllers/cbenhnhan.php');
include_once('Controllers/clinhvuc.php');
include_once('Controllers/chosobenhandientu.php');
include_once('Controllers/cchitiethoso.php');
include_once('Controllers/cchuyengia.php');

$cchuyengia = new cChuyenGia();
$chosobenhandientu = new cHoSoBenhAnDienTu();
$cbenhnhan = new cBenhnhan();
$clinhvuc = new cLinhVuc();
$cchitiethoso = new cChiTietHoSo();
$mahoso = $_GET['mahoso'] ?? null;

/**
 * Helper: convert various return types to array for safe foreach usage
 * - if already array => return
 * - if mysqli_result => fetch all assoc
 * - otherwise return empty array
 */
function ensure_array($v) {
    if (is_array($v)) return $v;
    if (is_object($v) && get_class($v) === 'mysqli_result') {
        $rows = [];
        while ($row = $v->fetch_assoc()) $rows[] = $row;
        return $rows;
    }
    return [];
}

/** Escape helper for output */
function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}


$benhnhan = ensure_array($chosobenhandientu->get_benhnhan_mahoso($mahoso));
$chitiethoso = ensure_array($chosobenhandientu->getDonThuocByIDHS($mahoso));
$chuyengia = $cchuyengia->getChuyenGiaByTenTK($_SESSION['user']['tentk']);
$linhvuc = $clinhvuc->get_linhvuc_machuyengia($chuyengia['machuyengia'] ?? null);
$hoso = ensure_array($chosobenhandientu->get_hoso_mahoso1($mahoso));
$chitiethoso_mahoso = ensure_array($cchitiethoso->get_chitiethoso_mahoso_chuyengia($mahoso));
$message = "";
echo json_encode($chitiethoso_mahoso);
/* Handle form submit */
if (isset($_POST['btnupdate'])) {
    $madonthuoc = null;
    // Create detailed record
    $trieuchung = $_POST['trieuchung'] ?? null;
    $chandoan = $_POST['chandoan'] ?? null;
    $huongdieutri = $_POST['huongdieutri'] ?? null;
    $ketluan = $_POST['ketluan'] ?? null;

    if ($cchitiethoso->create_chitiethoso($mahoso, $bacsi['mabacsi'] ?? null, $trieuchung, $chandoan, $huongdieutri, $madonthuoc, $ketluan)) {
        echo '<script>
            alert("Thành công! Cập nhật hồ sơ thành công");
            window.location.href = "?action=chitiethoso&mahoso=' . esc($mahoso) . '";
        </script>';
        exit();
    } else {
        echo '<script>
            alert("Thất bại! Cập nhật hồ sơ thất bại vui lòng thử lại");
            window.location.href = "?action=chitiethoso&mahoso=' . esc($mahoso) . '";
        </script>';
        exit();
    }
}
?>
<link rel="stylesheet" href="Views/bacsi/assets/css/csschitiethoso.css">
<main class="container">
    <div class="content-header">
        <div class="back-button">
            <a href="?action=chitietbenhnhan&id=<?php echo esc($benhnhan[0]['mabenhnhan'] ?? ''); ?>" class="btn-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Chi Tiết Hồ Sơ Bệnh Án</h1>
        </div>

        <div class="action-buttons">
            <a href="javascript:window.print()" class="btn-small">
                <i class="fas fa-print"></i> In hồ sơ
            </a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($mahoso)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Lưu ý!</strong> Vui lòng chọn hồ sơ bệnh nhân để xem.
            </div>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="?action=benhnhan" class="btn-primary">
                <i class="fas fa-user-injured"></i> Chọn hồ sơ
            </a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user-circle"></i>
                    Thông Tin Bệnh Nhân
                </h2>
            </div>
            <div class="card-body">
                <div class="patient-info">
                    <div class="patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="patient-details">
                        <h3 class="patient-name"><?php echo esc($benhnhan[0]['hoten'] ?? ''); ?></h3>
                        <div class="patient-id"><?php echo esc($benhnhan[0]['mabenhnhan'] ?? ''); ?></div>

                        <div class="patient-data">
                            <div class="patient-data-item">
                                <div class="data-label">Ngày sinh</div>
                                <div class="data-value"><?php echo esc($benhnhan[0]['ngaysinh'] ?? ''); ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Giới tính</div>
                                <div class="data-value"><?php echo esc($benhnhan[0]['gioitinh'] ?? ''); ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Nghề nghiệp</div>
                                <div class="data-value"><?php echo esc($benhnhan[0]['nghenghiep'] ?? ''); ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Tiền sử bệnh tật của bản thân</div>
                                <div class="data-value"><?php echo !empty($benhnhan[0]['tiensubenhtatcuabenhnhan']) ? esc(decryptData($benhnhan[0]['tiensubenhtatcuabenhnhan'])) : "Không có"; ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Tiền sử bệnh tật của gia đình</div>
                                <div class="data-value"><?php echo !empty($benhnhan[0]['tiensubenhtatcuagiadinh']) ? esc(decryptData($benhnhan[0]['tiensubenhtatcuagiadinh'])) : "Không có"; ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Địa chỉ</div>
                                <div class="data-value"><?php echo esc(($benhnhan[0]['sonha'] ?? '') . ',' . ($benhnhan[0]['tenxaphuong'] ?? '') . ',' . ($benhnhan[0]['tentinhthanhpho'] ?? '')); ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Số điện thoại</div>
                                <div class="data-value"><?php echo isset($benhnhan[0]['sdt']) ? esc(decryptData($benhnhan[0]['sdt'])) : ''; ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">Email</div>
                                <div class="data-value"><?php echo isset($benhnhan[0]['email']) ? esc(decryptData($benhnhan[0]['email'])) : ''; ?></div>
                            </div>
                            <div class="patient-data-item">
                                <div class="data-label">CCCD</div>
                                <div class="data-value"><?php echo isset($benhnhan[0]['cccd']) ? esc(decryptData($benhnhan[0]['cccd'])) : ''; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-file-medical"></i>
                    Thông Tin Hồ Sơ
                </h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Mã hồ sơ</div>
                            <div class="info-value"><?php echo esc($mahoso); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Ngày tạo</div>
                            <div class="info-value"><?php echo esc($benhnhan[0]['ngaytao'] ?? ''); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Chuyên khoa</div>
                            <div class="info-value"><?php echo esc($chitiethoso_mahoso[0]['tenchuyenkhoa'] ?? $chuyenkhoa['tenchuyenkhoa'] ?? ''); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Bác sĩ tạo hồ sơ </div>
                            <div class="info-value"> <?php echo esc($hoso[0]['hoten'] ?? ''); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email bác sĩ</div>
                            <div class="info-value"><?php echo isset($hoso[0]['email']) ? esc(decryptData($hoso[0]['email'])) : ''; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Số điện thoại bác sĩ</div>
                            <div class="info-value"><?php echo isset($hoso[0]['sdt']) ? esc(decryptData($hoso[0]['sdt'])) : ''; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($chitiethoso_mahoso[0]['tenlinhvuc']) && ($chitiethoso_mahoso[0]['tenlinhvuc'] ?? '') == ($linhvuc['tenlinhvuc'] ?? '')): ?>
            <button type="button" class="btn-small btn-primary" onclick="openUpdateRecordModal()">
                <i class="fas fa-edit"></i> Cập nhật hồ sơ
            </button>
        <?php endif; ?>

        <div class="tabs">
            <div class="tab-header">
                <a href="#diagnosis" class="tab-link active" onclick="openTab(event, 'diagnosis')">Chẩn đoán & Hướng điều trị</a>
            </div>
            <div id="diagnosis" class="tab-content active">
                <div class="card">
                    <div class="card-body no-padding">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Ngày khám</th>
                                    <th>Bác sĩ</th>
                                    <th>Triệu chứng ban đầu</th>
                                    <th>Kết luận</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($chitiethoso_mahoso) && is_array($chitiethoso_mahoso)):
                                    foreach ($chitiethoso_mahoso as $cd):
                                        $chitiet = ensure_array($cchitiethoso->get_chitiethoso_machitiethoso($cd['machitiethoso']));
                                        if (!empty($chitiet) && isset($chitiet[0]['mabacsi'])) {
                                            $result = $cchuyengia->getChuyenGiaById($chitiet[0]['mabacsi']);
                                            if (is_object($result) && get_class($result) === 'mysqli_result') {
                                                $bacsi_info = $result->fetch_assoc();
                                            } else {
                                                $bacsi_info = is_array($result) ? $result : [];
                                            }
                                            $chitiet[0]['hoten'] = $bacsi_info['hoten'] ?? '-';
                                        } else {
                                            $chitiet[0]['hoten'] = '-';
                                        }
                                        $chitietJson = json_encode($chitiet, JSON_HEX_APOS | JSON_HEX_QUOT);
                                        ?>
                                        <tr>
                                            <td><?php echo esc($cd['ngaykham'] ?? ''); ?></td>
                                            <td><?php echo esc($chitiet[0]['hoten'] ?? '-'); ?></td>
                                            <td><?php echo esc($cd['trieuchungbandau'] ?? ''); ?></td>
                                            <td><?php echo esc($cd['ketluan'] ?? ''); ?></td>
                                            <td>
                                                <button class="btn-small btn-primary" onclick='openChuanDoanPopup(<?php echo $chitietJson; ?>)'>
                                                    Xem chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-content">
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> Bệnh Viện Hạnh Phúc. Tất cả các quyền được bảo lưu.
        </div>
        <div class="footer-links">
            <a href="about.php">Về chúng tôi</a>
            <a href="privacy.php">Chính sách bảo mật</a>
            <a href="terms.php">Điều khoản sử dụng</a>
            <a href="contact.php">Liên hệ</a>
        </div>
    </div>
</footer>

<!-- Modals & JS (giữ nguyên chức năng tương tự như trước) -->

<!-- Modal Chẩn đoán -->
<div id="modalchuandoan" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeChuanDoanPopup()">&times;</span>
        <h2>Chi tiết chẩn đoán và hướng điều trị</h2>

        <div class="info-row">
            <div class="info-label">Ngày khám</div>
            <div class="info-value" id="cd-ngaykham"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Bác sĩ</div>
            <div class="info-value" id="cd-bacsi"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Triệu chứng ban đầu</div>
            <div class="info-value" id="cd-trieuchung"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Chẩn đoán</div>
            <div class="info-value" id="cd-chandoan"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Hướng điều trị</div>
            <div class="info-value" id="cd-kehoachdieutri"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Kết luận</div>
            <div class="info-value" id="cd-ketluan"></div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px; gap: 10px;">
            <button type="button" class="btn-outline" onclick="closeChuanDoanPopup()">
                <i class="fas fa-times"></i> Đóng
            </button>
            <button type="button" class="btn-primary" onclick="printChuanDoan()">
                <i class="fas fa-print"></i> In chi tiết
            </button>
        </div>
    </div>
</div>

<!-- Modal cập nhật hồ sơ (giữ nội dung giống bản trước, nếu cần tôi có thể tách file này) -->
<div id="modalcapnhathoso" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUpdateRecordModal()">&times;</span>
        <h2>Cập nhật hồ sơ bệnh án</h2>
        <form action="" method="post" id="prescriptionForm">
            <div class="tabs update-tabs">
                <div class="tab-header">
                    <a href="#update-diagnosis" class="update-tab-link" onclick="openUpdateTab(event, 'update-diagnosis')">Thêm chẩn đoán</a>
                    <div style="display: flex; justify-content: flex-end; margin-top: 20px; gap: 10px; margin-bottom: 20px; margin-left: 150px;">
                        <button type="submit" name="btnupdate" class="btn-small btn-primary">
                            <i class="fas fa-edit"></i> Cập nhật hồ sơ
                        </button>
                    </div>
                </div>

                <div id="update-diagnosis" class="update-tab-content">
                    <input type="hidden" name="action" value="themchuandoan">
                    <input type="hidden" name="mahoso" value="<?php echo esc($mahoso); ?>">
                    <div class="form-group">
                        <label for="trieuchung">Triệu chứng ban đầu</label>
                        <textarea id="trieuchung" name="trieuchung" rows="3" placeholder="Mô tả triệu chứng ban đầu của bệnh nhân..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="chandoan">Chẩn đoán</label>
                        <textarea id="chandoan" name="chandoan" rows="3" placeholder="Chẩn đoán của bác sĩ..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="huongdieutri">Hướng điều trị</label>
                        <textarea id="huongdieutri" name="huongdieutri" rows="3" placeholder="Hướng điều trị cho bệnh nhân..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="ketluan">Kết luận</label>
                        <textarea id="ketluan" name="ketluan" rows="3" placeholder="Kết luận của bác sĩ..." required></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Thêm CSS cho modal cập nhật hồ sơ (giữ cơ bản, bạn có thể load file css riêng) */
.update-tabs .tab-header { margin-bottom: 5px; }
.update-tab-link { padding: 10px 15px; margin-right: 5px; border-radius: 4px 4px 0 0; background-color: #f5f5f5; color: #333; text-decoration: none; border-bottom: 2px solid transparent; }
.update-tab-link.active { background-color: #fff; border-bottom: 2px solid #2563eb; color: #2563eb; }
.update-tab-content { display: none; padding: 20px; background-color: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.update-tab-content.active { display: block; }
.alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
.alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
</style>
<script>
function openUpdateRecordModal() { document.getElementById("modalcapnhathoso").style.display = "block"; }
function closeUpdateRecordModal() { document.getElementById("modalcapnhathoso").style.display = "none"; }

        // Mở modal chi tiết chẩn đoán
function openChuanDoanPopup(chitiet) {
    console.log("Chi tiết chẩn đoán:", chitiet); // Debug
    
    // Hiển thị modal
    document.getElementById("modalchuandoan").style.display = "block";
    
    // Cập nhật thông tin cơ bản
    document.getElementById("cd-ngaykham").textContent = chitiet[0].ngaykham;
    document.getElementById("cd-bacsi").textContent = chitiet[0].hoten|| "-";
    document.getElementById("cd-trieuchung").textContent = chitiet[0].trieuchungbandau|| "-";
    document.getElementById("cd-chandoan").textContent = chitiet[0].chandoan || "-";
    document.getElementById("cd-kehoachdieutri").textContent = chitiet[0].huongdieutri || "-";
    document.getElementById("cd-ketluan").textContent = chitiet[0].ketluan;
}

// Đóng modal chi tiết chẩn đoán
function closeChuanDoanPopup() {
    document.getElementById("modalchuandoan").style.display = "none";
}

// In chi tiết chẩn đoán
function printChuanDoan() {
    const ngaykham = document.getElementById("cd-ngaykham").textContent;
    const bacsi = document.getElementById("cd-bacsi").textContent;
    const trieuchung = document.getElementById("cd-trieuchung").textContent;
    const chandoan = document.getElementById("cd-chandoan").textContent;
    const kehoachdieutri = document.getElementById("cd-kehoachdieutri").textContent;
    const ketluan = document.getElementById("cd-ketluan").textContent;
    
    const originalContents = document.body.innerHTML;
    
    document.body.innerHTML = `
        <div style="padding: 20px;">
            <h1 style="text-align: center; margin-bottom: 20px;">Chi Tiết Chẩn Đoán và Hướng Điều Trị</h1>
            
            <div style="margin-bottom: 10px;">
                <div style="font-weight: bold;">Ngày khám:</div>
                <div>${ngaykham}</div>
            </div>
            <div style="margin-bottom: 10px;">
                <div style="font-weight: bold;">Bác sĩ:</div>
                <div>${bacsi}</div>
            </div>
            <div style="margin-bottom: 10px;">
                <div style="font-weight: bold;">Triệu chứng ban đầu:</div>
                <div>${trieuchung}</div>
            </div>
            <div style="margin-bottom: 10px;">
                <div style="font-weight: bold;">Chẩn đoán:</div>
                <div>${chandoan}</div>
            </div>
            <div style="margin-bottom: 10px;">
                <div style="font-weight: bold;">Hướng điều trị:</div>
                <div>${kehoachdieutri}</div>
            </div>
            <div style="margin-bottom: 10px;">
                <div style="font-weight: bold;">Kết luận:</div>
                <div>${ketluan}</div>
            </div>
        </div>
    `;
    
    window.print();
    document.body.innerHTML = originalContents;
    
    // Khôi phục lại các event listener sau khi in
    setTimeout(function() {
        // Khởi tạo lại các event listener
        document.querySelectorAll('.tab-link').forEach(function(tab) {
            tab.addEventListener('click', function(event) {
                const tabId = this.getAttribute('href').substring(1);
                openTab(event, tabId);
            });
        });
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modal = document.getElementById("modalxetnghiem");
            if (event.target === modal) {
                modal.style.display = "none";
            }
            
            const modalDonThuoc = document.getElementById("modalchitietdonthuoc");
            if (event.target === modalDonThuoc) {
                modalDonThuoc.style.display = "none";
            }
            
            const modalChuanDoan = document.getElementById("modalchuandoan");
            if (event.target === modalChuanDoan) {
                modalChuanDoan.style.display = "none";
            }
        };
    }, 100);
}
</script>
</body>
</html>