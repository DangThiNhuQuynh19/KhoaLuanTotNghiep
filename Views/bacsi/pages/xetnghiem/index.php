<?php
    include_once('Controllers/clichxetnghiem.php');
    include_once('Controllers/cbacsi.php');
    include_once('Controllers/cchuyenkhoa.php');
    include_once('Controllers/cketquaxetnghiem.php');
    $cketquaxetnghiem = new cKetQuaXetNghiem();
    $cchuyenkhoa = new cChuyenKhoa();
    $chuyenkhoa_list=$cchuyenkhoa->getAllChuyenKhoa();
    $cbacsi = new cBacSi();
    $clichxetnghiem = new cLichXetNghiem();
    $bacsi= $cbacsi->getBacSiByTenTK($_SESSION['user']['tentk']);
    $lichxetnghiem_list= $clichxetnghiem->get_lichxetnghiem_mabacsi($bacsi['mabacsi']);

    if(isset($_POST["btntimkiem"])){
        $tukhoa=$_POST["tukhoa"];
        $trangthai=$_POST["trangthai"];
        $machuyenkhoa=$_POST["chuyenkhoa"];
        $lichxetnghiem_list=$clichxetnghiem->get_lichxetnghiem_tukhoa($tukhoa, $machuyenkhoa, $trangthai,$bacsi['mabacsi']);
    }
    if(isset($_POST['homnay'])){
        $lichxetnghiem_list=$cketquaxetnghiem->get_ketquaxetnghiem_homnay($bacsi['mabacsi']);
    }
?>
<div class="container">
    <div class="content-header">
        <h1>Quản lý xét nghiệm</h1>
        <a href="?action=datlichxetnghiem" class="btn-primary"><i class="fas fa-plus"></i> Thêm xét nghiệm</a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Tìm kiếm xét nghiệm</h2>
        </div>
        <div class="card-body">
            <form class="search-form" method="POST">
                <div class="search-grid">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="tukhoa" placeholder="Tìm theo mã, tên bệnh nhân...">
                    </div>
                    
                    <div class="form-group">
                        <select name="chuyenkhoa">
                            <option value="">Chuyên khoa</option>
                            <?php foreach($chuyenkhoa_list as $i){?>
                                <option value="<?php echo $i['machuyenkhoa'];?>"><?php echo $i['tenchuyenkhoa'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <select name="trangthai">
                            <option value="">Trạng thái</option>
                            <option value="Chờ thanh toán">Chờ thanh toán</option>
                            <option value="Đang thực hiện">Đang thực hiện</option>
                            <option value="Hoàn thành">Hoàn thành</option>
                            <option value="Đã hủy">Đã hủy</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary" name="btntimkiem">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>
    <form method="POST" style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 10px;">
        <input value="homnay" type="checkbox" name="homnay" id="homnay" onchange="this.form.submit()" <?php if (isset($_POST['homnay'])) echo 'checked'; ?>>
        <label for="homnay" style="margin-left: 5px;"><b>Kết quả hôm nay</b></label>
    </form>
    <div class="card">
        <div class="card-body no-padding">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã XN</th>
                        <th>Bệnh nhân</th>
                        <th>Loại xét nghiệm</th>
                        <th>Chuyên khoa</th>
                        <th>Ngày hẹn</th>
                        <th>Khung Giờ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if($lichxetnghiem_list){
                            foreach ($lichxetnghiem_list as $i) {
                                $statusClass = '';
                                switch ($i['tentrangthai']) {
                                    case 'Hoàn thành':
                                        $style = 'status-completed';
                                        break;
                                    case 'Đang thực hiện':
                                        $style = 'status-processing';
                                        break;
                                    case 'Chờ thanh toán':
                                        $style = 'status-pending';
                                        break;
                                }
                                    echo '<tr>';
                                    echo '<td>' . $i['malichxetnghiem'] . '</td>';
                                    echo '<td>' . $i['hoten'] . '</td>';
                                    echo '<td>' . $i['tenloaixetnghiem'] . '</td>';
                                    echo '<td>' . $i['tenchuyenkhoa'] . '</td>';
                                    echo '<td>' . $i['ngayhen'] . '</td>';
                                    echo '<td>' . $i['giobatdau'].'-'.$i['gioketthuc'] . '</td>';
                                    echo '<td><span class="status-badge ' .  $style . '">' . $i['tentrangthai'] . '</span></td>';
                                    echo '<td class="actions">';
                                    $kq_list=$cketquaxetnghiem->get_ketquaxetnghiem($i['malichxetnghiem']);
                                    if ($kq_list){
                                        echo '<a class="btn-primary" href="?action=ketquaxetnghiem&id=' . $i['malichxetnghiem'] . '" class="btn-small">Kết quả</a>';
                                    }
                                    elseif($i['tentrangthai']=='Đã hủy'){
                                        echo '<a class="btn-primary" href="#" class="btn-small">Hủy</a>';
                                    }
                                    else{
                                        echo '<a class="btn-primary" href="#" class="btn-small">chưa có</a>';
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                        }else{
                            echo '<tr><td colspan="8" style="text-align:center; color:gray;">Không tìm thấy</td></tr>';

                        }   
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require("Views/bacsi/layout/footer.php"); ?>
