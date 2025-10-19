<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang quản lý nhân viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .sidebar {
      background-color: #f8f9fa;
      min-height: 100vh;
      padding: 15px;
    }
    .sidebar a {
      display: block;
      padding: 10px;
      margin-bottom: 5px;
      color: #333;
      text-decoration: none;
      border-radius: 5px;
    }
    .sidebar a.active, 
    .sidebar a:hover {
      background-color: #0d6efd;
      color: white;
    }

    /* Logo riêng, không bị highlight */
    .sidebar a.logo-link {
      padding: 0;
      margin: 0;
      background: none !important;
      border-radius: 0;
    }
    .sidebar a.logo-link:hover {
      background: none !important;
      color: inherit !important;
    }

    .logo {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo img {
      max-width: 100%;
      height: 100px;
      object-fit: contain;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar">
    

      <a href="index.php?action=lichhen" 
         class="<?= ($_GET['action'] ?? '') === 'lichhen' ? 'active' : '' ?>">
         <i class="bi bi-calendar-event"></i> Lịch hẹn
      </a>

      <a href="index.php?action=chuyengia" 
         class="<?= ($_GET['action'] ?? '') === 'chuyengia' ? 'active' : '' ?>">
         <i class="bi bi-people"></i> Danh sách chuyên gia
      </a>

      <a href="index.php?action=bacsi" 
         class="<?= ($_GET['action'] ?? '') === 'bacsi' ? 'active' : '' ?>">
         <i class="bi bi-people"></i> Danh sách bác sĩ
      </a>

      <a href="index.php?action=lichcanhan" 
         class="<?= ($_GET['action'] ?? '') === 'lichcanhan' ? 'active' : '' ?>">
         <i class="bi bi-clock-history"></i> Lịch làm việc cá nhân
      </a>

      <a href="index.php?action=thongtin" 
         class="<?= ($_GET['action'] ?? '') === 'thongtin' ? 'active' : '' ?>">
         <i class="bi bi-person-circle"></i> Thông tin cá nhân
      </a>
    </div>

    <!-- Nội dung chính -->
    <div class="col-md-9 col-lg-10 content p-4">
      <?php
      $action = $_GET['action'] ?? '';

      switch ($action) {
          case 'lichhen':
              include_once __DIR__ . '/../lichhen/lichhen.php';
              break;

          case 'bacsi':
              include_once __DIR__ . '/../bacsi/danhsach.php';
              break;

          case 'chuyengia':
              include_once __DIR__ . '/../chuyengia/danhsach.php';
              break;

          default: ?>
              <h4 class="mb-4">📋 Tổng quan</h4>
            
              <div class="card">
            <div class="card-header">📝 Đặt lịch</div>
            <div class="card-body">
                <p style="font-size: 14px;">Nhấn nút bên dưới để đặt lịch khám cho bệnh nhân:</p>
                <a href="index.php?action=datlichkham" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Đặt lịch khám
                </a>
            </div>
        </div>

          <?php
              break;
      }
      ?>
    </div>
  </div>
</div>
</body>
</html>
