<?php
    include_once('Controllers/cbenhnhan.php');
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/cphieukhambenh.php');
    include_once("Assets/config.php");
    $cphieukhambenh = new cPhieuKhamBenh();
    $cbenhnhan = new cBenhNhan();
    $cbacsi = new cBacSi();
    $bacsi= $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
    $benhnhan_list= $cbenhnhan->get_benhnhan_mabacsi( $bacsi['mabacsi']);

    if(isset($_POST["btntimkiem"])){
        $benhnhan_list = $cbenhnhan->get_benhnhan_tukhoa($_POST["tukhoa"], $bacsi['mabacsi']);
    }

    if(isset($_POST['homnay'])){
        $benhnhan_list = $cbenhnhan->get_benhnhan_homnay($bacsi['mabacsi']);
    }

    if(isset($_POST['btnbo'])){ 
        // Load lại danh sách ban đầu
        $benhnhan_list = $cbenhnhan->get_benhnhan_mabacsi($bacsi['mabacsi']);
    }

    
?>
<style>
.btn-secondary {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-secondary:hover {
    background-color: #e6e6e6;
    border-color: #999;
    color: #000;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}

.btn-secondary i {
    font-size: 14px;
}
</style>

<body>
    <div class="container">
        <div class="content-header">
            <h1 style="text-align:center">Quản lý bệnh nhân</h1>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Tìm kiếm bệnh nhân</h2>
              
            </div>
            <div class="card-body">
            <form class="search-form" method="POST">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="tukhoa" placeholder="Tìm theo tên, mã bệnh nhân...">
                </div>
                <br>
                <button type="submit" class="btn-primary" name="btntimkiem">Tìm kiếm</button>
                <button type="submit" class="btn-secondary" name="btnbo">
                    <i class="fas fa-times"></i> Bỏ tìm kiếm
                </button>
            </form>
            </div>
        </div>
        <form method="POST" style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
            <input value="homnay" type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php if (isset($_POST['homnay'])) echo 'checked'; ?>>
            <label for="homnay" style="margin-left: 5px;"><b> Hôm nay</b></label>
        </form>
        <div class="card">
            <div class="card-body no-padding">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã BN</th>
                            <th>Họ tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Số điện thoại</th>
                            <th>cccd</th>
                            <th>Email</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if($benhnhan_list){
                            foreach ($benhnhan_list as $i) {
                                echo '<tr>';
                                echo '<td>' . $i['mabenhnhan'] . '</td>';
                                echo '<td>' . $i['hoten'] . '</td>';
                                echo '<td>' . $i['ngaysinh'] . '</td>';
                                echo '<td>' . $i['gioitinh'] . '</td>';
                                echo '<td>' . $i['sdt'] . '</td>';
                                echo '<td>' . $i['cccd']. '</td>';
                                echo '<td>' . $i['email'] . '</td>';
                                echo '<td class="actions">';
                                echo '<a class="btn-primary btn-small" style="display: flex;" href="?action=chitietbenhnhan&id=' . $i['mabenhnhan'] . '" class="btn-small">Chi tiết</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }else{
                            echo '<tr><td colspan="7" style="text-align:center; color:gray;">Chưa có bệnh nhân</td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require("Views/bacsi/layout/footer.php"); ?>
