<?php
include_once("Controllers/cchuyengia.php");
include_once("Controllers/ctinhthanhpho.php");
include_once("Controllers/cxaphuong.php");
include_once("Controllers/clinhvuc.php");
include_once("Assets/config.php");

$error_message = '';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Không tìm thấy chuyên gia.";
    exit;
}
$machuyengia = $_GET['id'];

$cChuyenGia = new cChuyenGia();
$chuyengia = $cChuyenGia->getChuyenGiaById($machuyengia);

if (!$chuyengia || $chuyengia->num_rows === 0) {
    echo "Không tìm thấy thông tin chuyên gia.";
    exit;
}

$row = $chuyengia->fetch_assoc();

$cLinhVuc = new cLinhVuc();
$linhvucList = $cLinhVuc->getAllLinhVuc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $machuyengia = $_POST['machuyengia'] ?? '';

    $data = [
        'capbac'            => $_POST['capbac'] ?? '',
        'hoten'             => $_POST['hoten'] ?? '',
        'ngaysinh'          => $_POST['ngaysinh'] ?? '',
        'gioitinh'          => $_POST['gioitinh'] ?? '',
        'cccd'              => $_POST['cccd'] ?? '',
        'dantoc'            => $_POST['dantoc'] ?? '',
        'sdt'               => $_POST['sdt'] ?? '',
        'emailcanhan'       => $_POST['emailcanhan'] ?? '',
        'malinhvuc'         => $_POST['malinhvuc'] ?? '',
        'giatuvan'          => $_POST['giatuvan'] ?? 0,
        'sonha'             => $_POST['sonha'] ?? '',
        'tenxaphuong'       => $_POST['xa'] ?? '',
        'tentinhthanhpho'   => $_POST['tinh'] ?? '',
        'motacg'            => $_POST['motacg'] ?? '',
        'gioithieucg'       => $_POST['gioithieucg'] ?? '',
    ];

    $cChuyenGia = new cChuyenGia();
    $result = $cChuyenGia->updateChuyenGia($machuyengia, $data);

    if ($result) {
        header("Location: index.php?action=nhanvien&tab=chuyengia&status=success");
        exit;
    } else {
        $error_message = "Cập nhật thông tin chuyên gia thất bại. Vui lòng thử lại.";
    }
}

$cthanhpho = new cTinhThanhPho();
$thanhpho_list = $cthanhpho->get_tinhthanhpho();

$cxaphuong = new cXaPhuong();
$xaphuong_list = $cxaphuong->get_xaphuong();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa chuyên gia</title>
    <style>
        :root {
            --primary: #333333; /* Màu nút cập nhật */
            --primary-hover: #1f1f1f;
            --primary-foreground: #ffffff;
            --secondary: #f3f4f6; /* Màu nút quay lại */
            --secondary-foreground: #4b5563;
            --background: #f9fafb; 
            --foreground: #1f2937; 
            --muted: #e5e7eb; 
            --muted-foreground: #6b7280; 
            --border: #d1d5db; 
            --input: #d1d5db; 
            --card: #ffffff; 
            --destructive: #ef4444; 
            /* Màu Alert */
            --success-bg: #f0fdf4;
            --success-border: #86efac;
            --success-text: #166534;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            --danger-text: #991b1b;
        }
        /* --- 3. Card chung --- */
        .card {
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 30px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        /* --- 4. Header chuyên gia (Giữ lại cấu trúc) --- */
        .header-section {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--border);
        }
        .avatar-box img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3b82f6;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .info-box h3 {
            font-size: 26px;
            font-weight: 700;
            color: var(--foreground);
            margin: 0 0 5px 0;
        }

        .info-box p {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted-foreground);
            margin: 0;
        }

        .info-box i {
            color: var(--primary);
            font-size: 16px;
        }

        /* --- 5. Tiêu đề và Cấu trúc Form --- */
        .form-section {
            margin-bottom: 2.5rem;
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .form-grid {
            display: grid;
            /* Tối ưu hóa layout 2 cột hoặc tràn tùy theo kích thước màn hình */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .form-grid.full {
            grid-template-columns: 1fr;
        }

        .address-group {
            /* Layout cho 3 trường địa chỉ */
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            display: grid;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        /* --- 6. Form Groups và Labels --- */
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--foreground);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .form-label.required::after {
            content: '*';
            color: var(--destructive);
            margin-left: 2px;
        }

        /* --- 7. Input, Select, Textarea (Controls) --- */
        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid var(--input);
            border-radius: 0.6rem;
            font-size: 0.9rem;
            font-family: inherit;
            background-color: var(--card);
            color: var(--foreground);
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box; 
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(51, 51, 51, 0.1); 
        }

        .form-control:disabled {
            background-color: var(--muted);
            color: var(--muted-foreground);
            cursor: not-allowed;
            opacity: 0.7;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* --- 8. Action Buttons --- */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            justify-content: flex-end;
        }

        .btn-action {
            padding: 0.75rem 1.75rem;
            border: none;
            border-radius: 0.6rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .btn-update {
            background-color: #3b82f6;
            color: var(--primary-foreground);
        }

        .btn-update:hover {
            background-color: var(--primary-hover);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-back {
            background-color: var(--secondary);
            color: var(--secondary-foreground);
            border: 1px solid var(--border);
        }

        .btn-back:hover {
            background-color: var(--muted);
        }

        .btn-action:active {
            transform: scale(0.98);
        }

        /* --- 9. Responsive design (Cho form) --- */
        @media (max-width: 600px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
                gap: 15px;
            }
            
            .form-grid, .address-group {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.75rem;
            }

            .btn-action {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Alert messages -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success">
                <span>✅ Cập nhật thông tin chuyên gia thành công!</span>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <span>❌ <?= htmlspecialchars($error_message) ?></span>
                <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Main form card -->
        <div class="card">
            <form method="post" action="">
                <!-- Doctor header section -->
                <div class="header-section">
                    <div class="avatar-box">
                        <img src="Assets/img/<?= htmlspecialchars($row['imgcg'] ?? 'default.png') ?>" alt="Avatar <?= htmlspecialchars($row['hoten']) ?>">
                    </div>
                    <div class="info-box">
                        <h3><?= htmlspecialchars($row['hoten']) ?></h3>
                        <p><i class="bi bi-person-badge"></i> <?= htmlspecialchars($row['tenlinhvuc']) ?></p>
                    </div>
                </div>

                <input type="hidden" name="machuyengia" value="<?= htmlspecialchars($row['machuyengia']) ?>">

                <!-- Basic information section -->
                <div class="form-section">
                    <h3 class="form-section-title">Thông tin cá nhân</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="ngaysinh" class="form-control" value="<?= htmlspecialchars($row['ngaysinh']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Giới tính</label>
                            <select name="gioitinh" class="form-control">
                                <option value="Nam" <?= $row['gioitinh']=='Nam'?'selected':'' ?>>Nam</option>
                                <option value="Nữ" <?= $row['gioitinh']=='Nữ'?'selected':'' ?>>Nữ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">CCCD</label>
                            <input type="text" name="cccd" class="form-control" value="<?= htmlspecialchars($row['cccd']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Dân tộc</label>
                            <select name="dantoc" id="dantoc" class="form-control">
                                <option value="">--Chọn dân tộc--</option>
                                <?php 
                                $danTocList = [
                                    "Kinh","Tày","Thái","Hoa","Khơ-me","Mường","Nùng","HMông","Dao","Gia-rai","Ngái","Ê-đê",
                                    "Ba-na","Xơ-Đăng","Sán chay","Cơ-ho","Chăm","Sán Dìu","Hrê","Mnông","Ra-glai","Xtiêng",
                                    "Bru-Vân Kiều","Thổ","Cơ-tu","Gié","Mạ","Khơ-mú","Co","Tà-ôi","Chơ-ro","Kháng","Xinh-mun",
                                    "Hà Nhì","Chu ru","Lào","La Chí","La Ha","Phù Lá","La Hủ","Lự","Lô Lô","Chứt","Mảng",
                                    "Pà Thẻn","Co Lao","Cống","Bố Y","Si La","Pu Péo","Brâu","Ơ Đu","Rơ măm"
                                ];
                                $currentDanToc = trim($row['dantoc'] ?? '');
                                foreach ($danTocList as $dt) {
                                    $selected = (strcasecmp(trim($dt), $currentDanToc) === 0) ? 'selected' : '';
                                    echo "<option value=\"{$dt}\" {$selected}>{$dt}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact information section -->
                <div class="form-section">
                    <h3 class="form-section-title">Thông tin liên hệ</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">SĐT</label>
                            <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars(decryptData($row['sdt'])) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email TK</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars(decryptData($row['email'])) ?>" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email cá nhân</label>
                            <input type="email" name="emailcanhan" class="form-control" value="<?= htmlspecialchars($row['emailcanhan']) ?>">
                        </div>
                    </div>
                </div>

                <!-- Professional information section -->
                <div class="form-section">
                    <h3 class="form-section-title">Thông tin chuyên môn</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label required">Chuyên khoa</label>
                            <select name="malinhvuc" class="form-control" required>
                                <option value="">--Chọn chuyên khoa--</option>
                                <?php while($ck = $linhvucList->fetch_assoc()): ?>
                                    <option value="<?= $ck['malinhvuc'] ?>" 
                                        <?= $ck['tenlinhvuc'] == $row['tenlinhvuc'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ck['tenlinhvuc']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Giá khám</label>
                            <input 
                                type="number" 
                                name="giatuvan" 
                                class="form-control" 
                                value="<?= htmlspecialchars($row['giatuvan']) ?>" 
                                step="1" 
                                min="0"
                            >
                        </div>
                    </div>
                </div>

                <!-- Address section -->
                <div class="form-section">
                    <h3 class="form-section-title">Địa chỉ</h3>
                    <div class="form-grid full">
                        <div class="form-group">
                            <label class="form-label">Số nhà</label>
                            <input type="text" name="sonha" class="form-control" placeholder="Số nhà, đường phố" value="<?= htmlspecialchars($row['sonha']) ?>">
                        </div>
                    </div>
                    <div class="address-group" style="margin-top: 1rem;">
                        <div class="form-group">
                            <label class="form-label required">Tỉnh/Thành phố</label>
                            <select name="tinh" id="tinh" class="form-control" onchange="loadXaPhuong()" required>
                                <option value="">-- Chọn tỉnh/thành phố --</option>
                                <?php foreach($thanhpho_list as $i): ?>
                                    <option value="<?= $i['matinhthanhpho']; ?>" <?= ($row['matinhthanhpho'] ?? '') == $i['matinhthanhpho'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($i['tentinhthanhpho']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Xã/Phường</label>
                            <select name="xa" id="xa" class="form-control" required>
                                <option value="">-- Chọn xã/phường --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description section -->
                <div class="form-section">
                    <h3 class="form-section-title">Mô tả</h3>
                    <div class="form-grid full">
                        <div class="form-group">
                            <label class="form-label">Mô tả chuyên gia</label>
                            <textarea name="motacg" class="form-control" placeholder="Nhập mô tả chi tiết..."><?= htmlspecialchars($row['motacg']) ?></textarea>
                        </div>
                    </div>
                    <div class="form-grid full" style="margin-top: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Giới thiệu</label>
                            <textarea name="gioithieucg" class="form-control" placeholder="Nhập giới thiệu chi tiết..."><?= htmlspecialchars($row['gioithieucg']) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="action-buttons">
                    <a href="?action=nhanvien&tab=chuyengia" class="btn-action btn-back">← Quay lại</a>
                    <button type="submit" class="btn-action btn-update">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const xaphuongs = <?php echo json_encode($xaphuong_list); ?>;

        function loadXaPhuong() {
            const tinhSelect = document.getElementById("tinh");
            const xaSelect = document.getElementById("xa");
            const mathanhpho = tinhSelect.value;

            xaSelect.innerHTML = '<option value="">-- Chọn xã/phường --</option>';

            if (!mathanhpho) return;

            const filtered = xaphuongs.filter(x => x.matinhthanhpho === mathanhpho);
            filtered.forEach(x => {
                const option = document.createElement('option');
                option.value = x.maxaphuong;
                option.textContent = x.tenxaphuong;
                xaSelect.appendChild(option);
            });
        }

        window.addEventListener('DOMContentLoaded', () => {
            loadXaPhuong();
            const currentXa = '<?= $row['maxaphuong'] ?? '' ?>';
            if (currentXa) {
                document.getElementById('xa').value = currentXa;
            }
        });
    </script>
</body>
</html>
