<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Phân ca nhân viên</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
    --main-color: #483a73;
    --hover-color: #5a4b9b;
    --bg-light: #f7f7fc;
    --text-dark: #333;
    --radius: 10px;
    --transition: 0.25s ease;
    }

    /* ===== RESET & BODY ===== */
    body {
    font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
    margin: 0;
    background-color: var(--bg-light);
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
    width: 230px;
    height: 100vh;
    position: fixed;
    top: 56px;
    left: 0;
    display: flex;
    flex-direction: column;
    padding: 20px 0;
    background: #fff;
    border-right: 1px solid #e5e5ef;
    box-shadow: 2px 0 6px rgba(0, 0, 0, 0.04);
    }

    .sidebar h2 {
    color: var(--main-color);
    text-align: center;
    font-size: 16px;
    margin-bottom: 18px;
    font-weight: 600;
    }

    .sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
    }

    .sidebar ul li {
    margin: 6px 0;
    }

    .sidebar ul li a {
    display: flex;
    align-items: center;
    padding: 10px 18px;
    text-decoration: none;
    color: var(--main-color);
    font-size: 13px;
    transition: var(--transition);
    border-radius: 8px;
    }

    .sidebar ul li a i {
    margin-right: 8px;
    font-size: 14px;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
    background-color: var(--hover-color);
    color: #fff;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
    margin-left: 240px;
    margin-top: 70px;
    padding: 30px 20px;
    width: calc(100% - 240px);
    }

    h3 {
    color: var(--main-color);
    text-align: center;
    }

    /* ===== BẢNG ===== */
    .table-container {
    width: 95%;
    margin: auto;
    background: #fff;
    border-radius: var(--radius);
    box-shadow: 0 2px 10px rgba(72, 58, 115, 0.1);
    overflow: hidden;
    }

    table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    }

    thead {
    background-color: #f0eef8;
    }

    thead th {
    text-align: left;
    padding: 10px 12px;
    color: var(--main-color);
    font-weight: 600;
    border-bottom: 2px solid #eaeaea;
    }

    tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f0f5;
    }

    tbody tr:hover {
    background-color: rgba(72, 58, 115, 0.05);
    transition: var(--transition);
    }

    .btn-phan-ca {
    background-color: var(--main-color);
    color: white;
    border: none;
    padding: 7px 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: var(--transition);
    }

    .btn-phan-ca:hover {
    background-color: var(--hover-color);
    transform: scale(1.03);
    }

    /* ===== POPUP ===== */
    .popup-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999;
    }

    .popup {
    background: #fff;
    border-radius: var(--radius);
    padding: 24px 26px;
    width: 730px;
    max-height: 85vh; /* Giới hạn chiều cao tối đa */
    overflow-y: auto; /* Tự động thêm thanh cuộn dọc nếu nội dung dài */
    box-shadow: 0 4px 20px rgba(72, 58, 115, 0.2);
    animation: popupFade 0.25s ease;
  }

  /* Thu nhỏ bảng nhân viên trong popup */
  #tableNhanVienContainer {
    max-height: 300px; /* Giới hạn vùng bảng có thể cuộn */
    overflow-y: auto;
    margin-top: 10px;
    border: 1px solid #e5e5ef;
    border-radius: 8px;
  }

  /* Header bảng cố định */
  #tableNhanVien thead th {
    position: sticky;
    top: 0;
    background-color: #f0eef8;
    z-index: 2;
  }

    .popup h3 {
    text-align: center;
    color: var(--main-color);
    margin-bottom: 18px;
    }

    .popup select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    margin-bottom: 12px;
    outline: none;
    transition: border-color 0.25s;
    }

    .popup select:focus {
    border-color: var(--main-color);
    }

    #btnXacNhan {
    background-color: var(--main-color);
    border: none;
    padding: 10px;
    border-radius: 6px;
    color: white;
    transition: var(--transition);
    }

    #btnXacNhan:hover {
    background-color: var(--hover-color);
    transform: scale(1.03);
    }

    @keyframes popupFade {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
    }
.online {
  background-color: var(--main-color);
  color: white;
}

.online:hover {
  background-color: var(--hover-color);
}

.offline {
  background-color: #7b6cb2;
  color: white;
}

.offline:hover {
  background-color: #9c89cf;
}

.cancel {
  background-color: #ccc;
  color: #333;
}

.cancel:hover {
  background-color: #b5b5b5;
}
/* Popup hình thức */
.popup-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.popup-buttons button {
  flex: 1;
  margin: 0 5px;
  padding: 8px 0;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13.5px;
  transition: var(--transition);
}

</style>
</head>
<?php
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/ccalam.php');
    include_once('Controllers/cvaitro.php');
    include_once('Controllers/ctaikhoan.php');
    $cCaLam = new cCaLam();
    $cVaiTro = new cVaiTro();
    $tblCaLam = $cCaLam->get_calam(); 
    $tblVaiTro = $cVaiTro->get_vaitro();
    $bacsi = new cBacSi();
    $tblBacSi = $bacsi->getAllBacSi();
    $cTaiKhoan = new cTaiKhoan();
    $taikhoan = $cTaiKhoan->get_taikhoan();
?>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <h2>Quản lý ca</h2>
  <ul>
    <li><a href="?action=lichlamviec" class="active"><i class="fas fa-user-clock"></i> Phân ca</a></li>
    <li><a href="?action=xeplich"><i class="fas fa-calendar-check"></i> Xếp lịch</a></li>
  </ul>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
  <h3>Phân ca nhân viên</h3>
  <div class="table-container">  
    <table>
      <thead>
        <tr>
          <th>STT</th>
          <th>MÃ CA</th>
          <th>TÊN CA</th>
          <th>GIỜ VÀO</th>
          <th>GIỜ RA</th>
          <th>PHÂN CA</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($tblCaLam == -1) {
            echo "<tr><td colspan='6'>Lỗi truy xuất dữ liệu.</td></tr>";
        } elseif ($tblCaLam == 0) {
            echo "<tr><td colspan='6'>Chưa có ca làm việc nào.</td></tr>";
        } 
        else {
            $stt = 1;
                foreach ($tblCaLam as $ca) {
                    echo "<tr>
                            <td>" . $stt++ . "</td>
                            <td>" . htmlspecialchars($ca['macalamviec']) . "</td>
                            <td>" . htmlspecialchars($ca['tenca']) . "</td>
                            <td>" . htmlspecialchars($ca['giobatdau']) . "</td>
                            <td>" . htmlspecialchars($ca['gioketthuc']) . "</td>
                            <td><button class='btn-phan-ca' data-macalamviec='" . htmlspecialchars($ca['macalamviec']) . "'>Phân ca nhân viên</button></td>
                          </tr>";
                }
         }
        ?> 
      </tbody>
    </table>
  </div>
</div>

<!-- POPUP -->
<div class="popup-overlay" id="popupPhanCa">
  <div class="popup">
    <h3>Phân ca nhân viên</h3>

    <div class="row mb-3">
      <div class="col-6">
        <label for="chucVu" class="form-label fw-semibold text-secondary small">Chức vụ</label>
        <select id="chucVu" class="form-select form-select-sm">
          <option value="">-- Chọn chức vụ --</option>
          <option value="2">Bác sĩ</option>
          <option value="3">Chuyên gia</option>
          <option value="4">Nhân viên tiếp tân</option>
          <option value="5">Nhân viên xét nghiệm</option>
        </select>
      </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-2">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="chonTatCa">
        <label class="form-check-label small text-secondary" for="chonTatCa">Chọn tất cả</label>
      </div>
      <button id="btnXacNhan" class="btn btn-sm btn-primary" style="background-color: var(--main-color); border:none;">Xác nhận</button>
    </div>

    <div id="tableNhanVienContainer" style="display:none;">
      <table id="tableNhanVien" class="table table-hover table-sm align-middle">
        <thead>
          <tr>
            <th style="width: 60px;">Chọn</th>
            <th>Mã Nhân Viên</th>
            <th>Tên Nhân Viên</th>
            <th>Phòng</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="popup-overlay" id="popupHinhThuc">
  <div class="popup">
    <h3>Chọn hình thức làm việc</h3>
    <div class="popup-buttons">
      <button class="online">Online</button>
      <button class="offline">Offline</button>
      <button class="cancel">Cancel</button>
    </div>
  </div>
</div>

<script>
let danhSachPhong = [];  // Lưu phòng toàn hệ thống

document.addEventListener("DOMContentLoaded", () => {
    const popupPhanCa = document.getElementById("popupPhanCa");
    const popupHinhThuc = document.getElementById("popupHinhThuc");
    const chucVuSelect = document.getElementById("chucVu");

    const tableNhanVienContainer = document.getElementById("tableNhanVienContainer");
    const tbody = document.querySelector("#tableNhanVien tbody");
    const chonTatCa = document.getElementById("chonTatCa");
    const btnXacNhan = document.getElementById("btnXacNhan");

    // ========== LOAD PHÒNG KHI BẤM "PHÂN CA" ==========
    document.querySelectorAll(".btn-phan-ca").forEach(btn => {
        
        if (danhSachPhong.length === 0) {
            fetch("/KLTN/Ajax/getphong.php")
                .then(res => res.json())
                .then(data => {
                    danhSachPhong = data;
                })
                .catch(err => console.error("Lỗi tải phòng:", err));
        }

        btn.addEventListener("click", () => {
            const macalam = btn.dataset.macalamviec;
            popupPhanCa.dataset.macalam = macalam;

            chucVuSelect.value = "";
            tbody.innerHTML = "";
            tableNhanVienContainer.style.display = "none";
            chonTatCa.checked = false;

            popupPhanCa.style.display = "flex";
        });
    });

    // ========== LOAD NHÂN VIÊN THEO CHỨC VỤ ==========
    chucVuSelect.addEventListener("change", function() {
        const machucvu = this.value;
        const macalam = popupPhanCa.dataset.macalam;

        tbody.innerHTML = "";
        tableNhanVienContainer.style.display = "none";
        chonTatCa.checked = false;

        if (!machucvu) return;

        fetch("/KLTN/Ajax/getnhanvien.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `machucvu=${machucvu}&macalam=${macalam}` 
        })
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">Không có nhân viên phù hợp</td></tr>`;
                tableNhanVienContainer.style.display = "block";
                return;
            }

            tableNhanVienContainer.style.display = "block";

            data.forEach(nv => {
                let optionPhong = "";

                danhSachPhong.forEach(p => {
                    optionPhong += `
                        <option value="${p.maphong}">
                            Tòa ${p.tentoa} - Tầng ${p.tang} - Phòng ${p.sophong} (${p.loaiphong})
                        </option>`;
                });

                const tr = document.createElement("tr");

                tr.innerHTML = `
                    <td><input type="checkbox" class="form-check-input nv-checkbox" value="${nv.manv}"></td>
                    <td>${nv.manv}</td>
                    <td>${nv.hoten}</td>
                    <td>
                        <select class="form-select form-select-sm phong-select" data-manv="${nv.manv}">
                            ${optionPhong}
                        </select>
                    </td>
                `;

                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            console.error("Lỗi fetch nhân viên:", err);
        });
    });

    // CHỌN TẤT CẢ
    chonTatCa.addEventListener("change", function() {
        document.querySelectorAll(".nv-checkbox").forEach(cb => cb.checked = this.checked);
    });

    // BẤM XÁC NHẬN → QUA POPUP HÌNH THỨC
    btnXacNhan.addEventListener("click", () => {
        const selectedNV = document.querySelectorAll('.nv-checkbox:checked');
        if (selectedNV.length === 0) {
            alert("Vui lòng chọn ít nhất một nhân viên để phân ca.");
            return;
        }
        popupPhanCa.style.display = "none";
        popupHinhThuc.style.display = "flex";
    });
    popupHinhThuc.querySelector(".online").addEventListener("click", () => {

// ✅ Hiện thông báo ngay lập tức
alert("Bạn chọn hình thức ONLINE — các lựa chọn phòng sẽ bị xóa.");

// ✅ Xóa phòng trong UI để người dùng thấy rõ
document.querySelectorAll(".phong-select").forEach(select => {
    select.value = ""; // hoặc select.innerHTML = ""; nếu muốn xóa hết option
});

handlePhanCa('Online');
});

    // HÌNH THỨC LÀM VIỆC
    popupHinhThuc.querySelector(".cancel").addEventListener("click", () => {
        popupHinhThuc.style.display = "none";
    });

    popupHinhThuc.querySelector(".offline").addEventListener("click", () => {
        handlePhanCa("Offline");
    });

    popupHinhThuc.querySelector(".online").addEventListener("click", () => {
        handlePhanCa("Online");
    });

    // ========== XỬ LÝ PHÂN CA ==========
    function handlePhanCa(hinhThuc) {
        const macalam = popupPhanCa.dataset.macalam;

        const selected = document.querySelectorAll(".nv-checkbox:checked");

        const ds = [];

        selected.forEach(cb => {
            const manv = cb.value;
            const phong = document.querySelector(`select.phong-select[data-manv="${manv}"]`).value;

            ds.push({
                manv: manv,
                maphong: phong
            });
        });

        const formData = new FormData();
        formData.append("macalam", macalam);
        formData.append("hinhthuc", hinhThuc);
        formData.append("manv_list", JSON.stringify(ds));

        fetch("/KLTN/Ajax/phancanhanvien.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Phân ca thành công!");
                location.reload();
            } else {
                alert("Lỗi phân ca: " + data.message);
            }
        })
        .catch(err => {
            alert("Lỗi kết nối server.");
            console.error(err);
        });
    }

    // ĐÓNG POPUP KHI BẤM RA NGOÀI
    [popupPhanCa, popupHinhThuc].forEach(popup => {
        popup.addEventListener("click", e => {
            if (e.target === popup) popup.style.display = "none";
        });
    });
});
</script>

</body>
</html>
