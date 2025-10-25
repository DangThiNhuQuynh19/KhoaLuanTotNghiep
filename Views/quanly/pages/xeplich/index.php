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
  top: 56px; /* chiều cao header */
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
  font-weight: 600;
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
  padding: 24px;
  width: 380px;
  box-shadow: 0 4px 20px rgba(72, 58, 115, 0.2);
  animation: popupFade 0.25s ease;
}

@keyframes popupFade {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <h2>Quản lý ca</h2>
  <ul>
    <li><a href="?action=lichlamviec"><i class="fas fa-user-clock"></i> Phân ca</a></li>
    <li><a href="?action=xeplich" class="active"><i class="fas fa-calendar-check"></i> Xếp lịch</a></li>
  </ul>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
  <h3>Lịch làm việc của nhân viên</h3>
  <div class="table-container">
    ở đây
  </div>
</div>

</body>
</html>
