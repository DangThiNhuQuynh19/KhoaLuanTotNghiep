<?php
include_once(__DIR__ . '/../Models/mLichHen.php');

class cLichHen {
    public function getAllLichHen($ngay = null, $loaikham = null, $hinhthuc = null, $tenbenhnhan = null) {
        $m = new mLichHen();
        $result = $m->lichhen($ngay, $loaikham, $hinhthuc, $tenbenhnhan);

        if ($result === false) {
            return -1; // lỗi query
        } elseif (empty($result)) {
            return 0;  // không có dữ liệu
        } else {
            return $result;
        }
    }
}
?>
