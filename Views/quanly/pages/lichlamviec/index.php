<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Phân ca nhân viên</title>
<style>
:root {
  --main-color: #483a73;
  --hover-color: #5a4b9b;
  --bg-light: #f7f7fc;
  --text-dark: #333;
  --radius: 10px;
  --transition: 0.25s ease;
}

body {
  font-family: "Inter", "Roboto", sans-serif;
  background-color: var(--bg-light);
  margin: 0;
  padding: 20px;
  color: var(--text-dark);
}

/* Bảng danh sách */
.table-container {
  background: white;
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
  font-size: 13.5px;
}

.btn-phan-ca:hover {
  background-color: var(--hover-color);
  transform: scale(1.03);
}

/* Popup overlay */
.popup-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.4);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

/* Popup nội dung */
.popup {
  background: #fff;
  border-radius: var(--radius);
  padding: 24px;
  width: 380px;
  box-shadow: 0 4px 20px rgba(72, 58, 115, 0.2);
  animation: popupFade 0.25s ease;
}

@keyframes popupFade {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.popup h3 {
  color: var(--main-color);
  font-weight: 600;
  text-align: center;
  margin-bottom: 18px;
}

.popup select {
  width: 100%;
  padding: 8px 10px;
  margin-bottom: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 13.5px;
}

/* Danh sách nhân viên */
.nhanvien-list {
  max-height: 120px;
  overflow-y: auto;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 8px;
  background-color: #fafafa;
}

.nhanvien-list label {
  display: block;
  padding: 4px 6px;
  cursor: pointer;
}

.nhanvien-list label:hover {
  background-color: #f0eef8;
}

.popup .btn-confirm {
  background-color: var(--main-color);
  color: #fff;
  border: none;
  width: 100%;
  padding: 8px 0;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: var(--transition);
  margin-top: 10px;
}

.popup .btn-confirm:hover {
  background-color: var(--hover-color);
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
</style>
</head>

<body>

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
      <tr>
        <td>1</td>
        <td>CA01</td>
        <td>Ca Sáng</td>
        <td>06:00</td>
        <td>14:00</td>
        <td><button class="btn-phan-ca">Phân ca nhân viên</button></td>
      </tr>
      <tr>
        <td>2</td>
        <td>CA02</td>
        <td>Ca Chiều</td>
        <td>14:00</td>
        <td>22:00</td>
        <td><button class="btn-phan-ca">Phân ca nhân viên</button></td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Popup 1 -->
<div class="popup-overlay" id="popupPhanCa">
  <div class="popup">
    <h3>Phân ca nhân viên</h3>

    <select id="chucVu">
      <option value="">-- Chọn chức vụ --</option>
      <option>Nhân viên bán hàng</option>
      <option>Thu ngân</option>
      <option>Phụ bếp</option>
    </select>

    <select id="trangThai">
      <option value="">-- Trạng thái phân ca --</option>
      <option>Đã phân ca</option>
      <option>Chưa phân ca</option>
    </select>

    <div style="margin: 8px 0;">
      <label><input type="checkbox" id="chonTatCa"> Chọn tất cả nhân viên</label>
    </div>

    <div class="nhanvien-list" id="nhanVienList">
      <label><input type="checkbox" class="nv-checkbox"> Nguyễn Văn A</label>
      <label><input type="checkbox" class="nv-checkbox"> Trần Thị B</label>
      <label><input type="checkbox" class="nv-checkbox"> Phạm Văn C</label>
      <label><input type="checkbox" class="nv-checkbox"> Lê Thị D</label>
    </div>

    <button class="btn-confirm" id="btnXacNhan">Xác nhận</button>
  </div>
</div>

<!-- Popup 2 -->
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
document.addEventListener("DOMContentLoaded", () => {
  const popupPhanCa = document.getElementById("popupPhanCa");
  const popupHinhThuc = document.getElementById("popupHinhThuc");
  const chonTatCa = document.getElementById("chonTatCa");
  const checkboxes = document.querySelectorAll(".nv-checkbox");

  // Mở popup phân ca
  document.querySelectorAll(".btn-phan-ca").forEach(btn => {
    btn.addEventListener("click", () => {
      popupPhanCa.style.display = "flex";
    });
  });

  // Chọn tất cả nhân viên trong popup
  chonTatCa.addEventListener("change", () => {
    checkboxes.forEach(cb => cb.checked = chonTatCa.checked);
  });

  // Xác nhận -> mở popup hình thức
  document.getElementById("btnXacNhan").addEventListener("click", () => {
    popupPhanCa.style.display = "none";
    popupHinhThuc.style.display = "flex";
  });

  // Cancel đóng popup
  document.querySelector(".cancel").addEventListener("click", () => {
    popupHinhThuc.style.display = "none";
  });

  // Đóng popup khi click ra ngoài
  [popupPhanCa, popupHinhThuc].forEach(popup => {
    popup.addEventListener("click", e => {
      if (e.target === popup) popup.style.display = "none";
    });
  });
});
</script>
</body>
</html>
