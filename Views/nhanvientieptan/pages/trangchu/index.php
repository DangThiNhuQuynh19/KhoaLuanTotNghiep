<div class="main-container p-4">

    <style>
        /* --- RESET NHẸ --- */
        .content {
            background: #f5f8fa;
            min-height: 100vh;
        }

        /* --- TITLE --- */
        .dashboard-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f4c75;
            margin-bottom: 25px;
            letter-spacing: 0.3px;
        }

        /* --- CARD TỔNG QUAN --- */
        .overview-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e3edf4;
            transition: 0.25s ease;
        }

        .overview-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.10);
        }

        .overview-card h5 {
            font-size: 17px;
            font-weight: 600;
            color: #0f4c75;
            margin-bottom: 10px;
        }

        .overview-card p {
            color: #4a4a4a;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* --- GRID LAYOUT --- */
        .overview-grid {
            display: grid;
            gap: 24px;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            margin-top: 10px;
        }

        /* --- NÚT --- */
        .btn-his {
            background: #3282b8;
            border: none;
            padding: 10px 20px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 8px;
            color: #fff;
            transition: 0.2s ease;
        }

        .btn-his:hover {
            background: #256b96;
            color: #fff;
        }

        /* ICON đẹp hơn */
        .his-icon {
            font-size: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }
    </style>

    <div class="row">

        <div class="col-md-9 col-lg-10 content">

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

                default:
            ?>

            <!-- TIÊU ĐỀ -->
            <h4 class="dashboard-title">Tổng quan hệ thống</h4>

            <!-- GRID CARD -->
            <div class="overview-grid">

                <!-- Card 1 -->
                <div class="overview-card">
                    <h5><i class="bi bi-calendar-plus his-icon"></i>Đặt lịch khám</h5>
                    <p>Tạo lịch khám bệnh nhanh chóng cho bệnh nhân.</p>
                    <a href="index.php?action=datlichkham" class="btn btn-his">
                        <i class="bi bi-plus-circle"></i> Tạo lịch mới
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="overview-card">
                    <h5><i class="bi bi-heart-pulse his-icon"></i>Danh sách bác sĩ</h5>
                    <p>Xem thông tin bác sĩ, chuyên khoa, lịch làm việc.</p>
                    <a href="index.php?action=bacsi" class="btn btn-his">
                        <i class="bi bi-person-badge"></i> Xem bác sĩ
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="overview-card">
                    <h5><i class="bi bi-people his-icon"></i>Chuyên gia</h5>
                    <p>Quản lý các chuyên gia đầu ngành & hồ sơ chi tiết.</p>
                    <a href="index.php?action=chuyengia" class="btn btn-his">
                        <i class="bi bi-people-fill"></i> Xem chuyên gia
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
