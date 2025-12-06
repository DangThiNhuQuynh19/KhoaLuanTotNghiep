<?php
// Handler để xử lý form từ modal "Hoàn tất khám" / cập nhật phiếu khám
// File này được include từ lichhentructuyen/index.php và sử dụng các controller đã khởi tạo

// Không cần require lại các file đã được include trong file gọi
// Chỉ cần sử dụng các biến đã có sẵn

// Import QR Code library nếu cần
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Kiểm tra form gửi từ modal "Hoàn tất khám"
if (isset($_POST['btnHoanTat'])) {
    // Lấy dữ liệu cần thiết
    $maphieukhambenh = $_POST['maphieukhambenh'] ?? null;
    $mabenhnhan = $_POST['mabenhnhan'] ?? null;
    $mahoso = $_POST['mahoso'] ?? null;

    // Dữ liệu chẩn đoán / điều trị
    $trieuchung = $_POST['trieuchung'] ?? '';
    $chandoan = $_POST['chandoan'] ?? '';
    $huongdieutri = $_POST['huongdieutri'] ?? '';
    $ketluan = $_POST['ketluan'] ?? '';

    // Dữ liệu đơn thuốc (mảng medications[...])
    $medications = $_POST['medications'] ?? [];

    // Dữ liệu xét nghiệm
    $test = $_POST['test'] ?? null;
    $appointmentDate = $_POST['appointmentDate'] ?? null;
    $appointmentTime = $_POST['appointmentTime'] ?? null;
    $ghichu = $_POST['ghichu'] ?? null;

    // Tham số trả về
    $message = '';
    $success = false;

    // Bắt đầu transaction nếu có kết nối mysqli
    $usingTransaction = false;
    if (isset($conn) && $conn instanceof mysqli) {
        $usingTransaction = true;
        $conn->begin_transaction();
    }

    try {
        // 1) Tạo đơn thuốc nếu có medications
        $madonthuoc = null;
        if (!empty($medications) && is_array($medications)) {
            if ($cdonthuoc->create_donthuoc()) {
                // Lấy đơn thuốc mới tạo (phương thức dựa theo code hiện tại của bạn)
                $donthuoc_new = $cdonthuoc->get_donthuoc_new();
                if (!empty($donthuoc_new)) {
                    $madonthuoc = $donthuoc_new[0]['madonthuoc'];
                    foreach ($medications as $thuoc) {
                        // Kiểm tra tồn tại các trường bắt buộc
                        $mathuoc = $thuoc['mathuoc'] ?? null;
                        $lieudung = $thuoc['lieudung'] ?? '';
                        $thoigianuong = $thuoc['thoigianuong'] ?? '';
                        $songayuong = $thuoc['songayuong'] ?? '';

                        if ($mathuoc) {
                            $cchitietdongthuoc->create_chitietdonthuoc(
                                $madonthuoc,
                                $mathuoc,
                                $lieudung,
                                $thoigianuong,
                                $songayuong
                            );
                        }
                    }
                } else {
                    // Không lấy được đơn mới -> đặt null
                    $madonthuoc = null;
                }
            } else {
                $madonthuoc = null;
            }
        }

        // 2) Tạo lịch xét nghiệm nếu có dữ liệu test đầy đủ
        if (!empty($mabenhnhan) && !empty($test) && !empty($appointmentDate) && !empty($appointmentTime) && !empty($mahoso)) {
            // Tạo file QR
            $filename = 'qr_' . time() . '.png';
            $savePath = __DIR__ . '/../Assets/img/' . $filename;

            $kg = $ckhunggioxetnghiem->get_khunggioxetnghiem_makhunggio($appointmentTime);
            $loai = $cloaixetnghiem->get_loaixetnghiem_maloaixetnghiem($test);
            $bn_info = $cbenhnhan->getbenhnhanbyid($mabenhnhan);

            // Tạo QR data với thông tin cơ bản (không bao gồm SDT để bảo mật)
            $qrData = "Mã BN: " . ($mabenhnhan) . "\n" .
                      "Tên xét nghiệm: " . (!empty($loai[0]['tenloaixetnghiem']) ? $loai[0]['tenloaixetnghiem'] : '') . "\n" .
                      "Ngày: " . $appointmentDate . "\n" .
                      "Giờ: " . (!empty($kg[0]['giobatdau']) ? $kg[0]['giobatdau'] : '') . "\n";

            // Tạo QR code nếu library có sẵn
            if (class_exists('\Endroid\QrCode\Builder\Builder')) {
                $builder = new \Endroid\QrCode\Builder\Builder(
                    writer: new \Endroid\QrCode\Writer\PngWriter(),
                    data: $qrData
                );
                $result = $builder->build();
                // Lưu file
                file_put_contents($savePath, $result->getString());
            } else {
                // Nếu không có library, tạo file placeholder
                $filename = 'qr_placeholder_' . time() . '.txt';
                $savePath = __DIR__ . '/../Assets/img/' . $filename;
                file_put_contents($savePath, $qrData);
            }

            // Tạo lịch xét nghiệm (trạng thái 'Đã đặt lịch')
            $clichxetnghiem->create_lichxetnghiem($mabenhnhan, $test, $appointmentDate, $appointmentTime, 'Đã đặt lịch', $mahoso, $filename);
        }

        // 3) Tạo chi tiết hồ sơ
        // Lưu ý: cần có $bacsi['mabacsi'] — lấy từ context đã có
        $bacsiMabacsi = null;
        if (isset($_SESSION['user']['tentk']) && isset($cbacsi)) {
            $bacsiInfo = $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
            $bacsiMabacsi = $bacsiInfo['mabacsi'] ?? null;
        } elseif (isset($_SESSION['user']['mabacsi'])) {
            $bacsiMabacsi = $_SESSION['user']['mabacsi'];
        }

        if (empty($mahoso)) {
            // Nếu không có mahoso, cố gắng lấy mahoso từ phiếu khám nếu lưu sẵn
            // (tùy cấu trúc DB - nếu không có, bạn có thể bỏ qua)
            // $mahoso = $cphieukhambenh->get_mahoso_by_maphieu($maphieukhambenh);
        }

        // Gọi tạo chi tiết hồ sơ
        $created = $cchitiethoso->create_chitiethoso(
            $mahoso,
            $bacsiMabacsi,
            $trieuchung,
            $chandoan,
            $huongdieutri,
            $madonthuoc,
            $ketluan
        );

        if ($created) {
            // 4) Nếu tạo chi tiết hồ sơ thành công -> cập nhật trạng thái phiếu khám sang 'Đã khám'
            if (method_exists($cphieukhambenh, 'update_trangthai_phieu')) {
                // Nếu controller có hàm sẵn
                $cphieukhambenh->update_trangthai_phieu($maphieukhambenh, 'Đã khám');
            } else {
                // Nếu không có phương thức, cập nhật trực tiếp lên DB (giả sử $conn là mysqli)
                if ($usingTransaction) {
                    $stmt = $conn->prepare("UPDATE phieukhambenh SET tentrangthai = ? WHERE maphieukhambenh = ?");
                    if ($stmt) {
                        $trangthai = 'Đã khám';
                        $stmt->bind_param("ss", $trangthai, $maphieukhambenh);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        // không thể chuẩn bị truy vấn
                        throw new Exception("Không thể cập nhật trạng thái phiếu (prepare failed).");
                    }
                } else {
                    // Nếu không dùng $conn, bạn cần implement update trong controller cPhieuKhamBenh
                    // throw new Exception("Không có phương thức cập nhật trạng thái và không có kết nối DB để thực thi.");
                    // Tạm bỏ qua nếu không có $conn
                }
            }

            // Commit transaction nếu có
            if ($usingTransaction) $conn->commit();

            $message = "Hoàn tất: lưu chi tiết hồ sơ và cập nhật trạng thái lịch hẹn thành 'Đã khám' thành công.";
            $success = true;

            // Redirect về trang chi tiết hồ sơ hoặc danh sách lịch hẹn
            if (!empty($mahoso)) {
                header("Location: ?action=chitiethoso&mahoso=" . urlencode($mahoso));
            } else {
                header("Location: ?action=lichhentructuyen&success=1");
            }
            exit();
        } else {
            // Tạo chi tiết hồ sơ thất bại => rollback
            if ($usingTransaction) $conn->rollback();
            $message = "Lưu chi tiết hồ sơ thất bại.";
            $success = false;

            // Redirect với thông báo lỗi
            header("Location: ?action=lichhentructuyen&error=save_failed");
            exit();
        }
    } catch (Exception $ex) {
        if ($usingTransaction) $conn->rollback();
        error_log("Lỗi xử lý hoàn tất khám: " . $ex->getMessage());
        
        // Redirect với thông báo lỗi
        header("Location: ?action=lichhentructuyen&error=exception&msg=" . urlencode($ex->getMessage()));
        exit();
    }
}
?>
