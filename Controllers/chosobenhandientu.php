<?php
include_once("Models/mhosobenhandientu.php");

class cHoSoBenhAnDienTu{
    public function getAllHSBADTOfTK($tentk) {
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->gethosotheotentk($tentk);
        if (!$tbl) {
            return -1;
        } else {
            if ($tbl->num_rows > 0) {
                return $tbl->fetch_assoc();
            } else {
                return 0;
            }
        }
    }
    public function getAllHSBADTOfTK1($tentk) {
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->gethosotheotentk($tentk);
        if ($tbl === false) {
            return false;  
        }
        return $tbl;
    }
    
    
    public function getChiTietHSBADTOfTK($id) {
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->getchitiethosotheotentk($id);
        if (!$tbl) {
            return -1;
        } else {
            if ($tbl->num_rows > 0) {
                return $tbl;
            } else {
                return 0;
            }
        }
    }
        // Lấy thông tin đơn thuốc của bệnh nhân theo hồ sơ
    public function getDonThuocByIDHS($id) {
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->getdonthuoctheohoso($id);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }

    // Lấy thông tin xét nghiệm của bệnh nhân theo hồ sơ
    public function getXetNghiemByIDHS($id) {
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->getxetnghiemtheohoso($id);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }

    public function get_hsba_mabenhnhan($mabenhnhan){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hsba_mabenhnhan($mabenhnhan);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }
    public function get_hsba_mabenhnhan1($mabenhnhan){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hsba_mabenhnhan1($mabenhnhan);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }
    public function get_hsba(){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hsba();
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }

    public function create_hosobenhan_mabenhnhan($mabenhnhan){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->insert_hosobenhandientu_mabenhnhan($mabenhnhan);
        if(!$tbl){
            return -1;
        }else{
            return $tbl;
        }
    }

    public function get_hsba_new($mabenhnhan){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hsba_new($mabenhnhan);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }

    public function get_benhnhan_mahoso($mahoso){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_benhnhan_mahoso($mahoso);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }

    public function get_hoso_mahoso($mahoso){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hoso_mahoso($mahoso);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }
    public function get_hoso_mahoso1($mahoso){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hoso_mahoso1($mahoso);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }
    }
    
    /**
     * Lấy hồ sơ bệnh án theo chuyên khoa của người tạo và mã bệnh nhân
     * Get medical records by specialty of creator and patient ID
     * 
     * @param string $mabenhnhan - Mã bệnh nhân (Patient ID)
     * @param int $machuyenkhoa - Mã chuyên khoa (Specialty ID)
     * @return array|int - Trả về mảng dữ liệu, 0 nếu không có kết quả, -1 nếu lỗi
     * 
     * Return values:
     * - array: Mảng chứa các hồ sơ bệnh án
     * - 0: Không tìm thấy hồ sơ nào
     * - -1: Lỗi kết nối hoặc truy vấn
     * 
     * Example usage:
     * $result = $chosobenhandientu->get_hoso_machuyenkhoa('BN_123456', 1);
     * if (is_array($result)) {
     *     // Có hồ sơ
     *     foreach ($result as $hoso) {
     *         echo $hoso['mahoso'];
     *     }
     * } elseif ($result === 0) {
     *     // Không có hồ sơ
     * } else {
     *     // Lỗi
     * }
     */
    public function get_hoso_machuyenkhoa($mabenhnhan,$machuyenkhoa){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hoso_machuyenkhoa($mabenhnhan,$machuyenkhoa);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }

    }
    public function get_hoso_malinhvuc($mabenhnhan,$machuyenkhoa){
        $p = new mHoSoBenhAnDienTu();
        $tbl = $p->select_hoso_malinhvuc($mabenhnhan,$machuyenkhoa);
        if (!$tbl) {
            return -1; 
        } else {
            if ($tbl->num_rows > 0) {
                $data = [];
                while ($row = $tbl->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return 0; 
            }
        }

    }
}
?>