<?php
include_once("ketnoi.php");

class mLichHen {
    public function lichhen($ngay = null, $loaikham = null, $hinhthuclamviec = null, $tenbenhnhan = null) {
        $p = new clsketnoi();
        $conn = $p->moketnoi();

            $sql = "SELECT distinct
            pkb.maphieukhambenh,
            pkb.ngaykham,
            kg.giobatdau,
            bn.hoten AS ten_benhnhan,
            nd.hoten AS ten_nguoi_kham,
            CASE
                WHEN bs.mabacsi IS NOT NULL THEN 'bacsi'
                WHEN cg.machuyengia IS NOT NULL THEN 'chuyengia'
                ELSE 'khac'
            END AS loaikham,
            llv.hinhthuclamviec,
            tt.tentrangthai
        FROM phieukhambenh pkb
        JOIN khunggiokhambenh kg ON pkb.makhunggiokb = kg.makhunggiokb
        JOIN lichlamviec llv ON llv.macalamviec = kg.macalamviec
        JOIN nguoidung bn ON pkb.mabenhnhan = bn.manguoidung
        JOIN nguoidung nd ON pkb.mabacsi = nd.manguoidung
        LEFT JOIN bacsi bs ON nd.manguoidung = bs.mabacsi
        LEFT JOIN chuyengia cg ON nd.manguoidung = cg.machuyengia
        JOIN trangthai tt ON pkb.matrangthai = tt.matrangthai
        WHERE 1=1";

        $params = [];

        // Lọc theo ngày
        if (!empty($ngay)) {
            $sql .= " AND DATE(pkb.ngaykham) = ? ";
            $params[] = $ngay;
        } else {
            $sql .= " AND DATE(pkb.ngaykham) = CURDATE() ";
        }

        // Lọc theo loại khám: bacsi / chuyengia
        if (!empty($loaikham)) {
            if ($loaikham === "bacsi") {
                $sql .= " AND bs.mabacsi IS NOT NULL ";
            } elseif ($loaikham === "chuyengia") {
                $sql .= " AND cg.machuyengia IS NOT NULL ";
            }
        }

        // Lọc theo hình thức: online / offline
        if (!empty($hinhthuclamviec)) {
            $sql .= " AND llv.hinhthuclamviec = ? ";
            $params[] = $hinhthuclamviec;
        }

        // Lọc theo tên bệnh nhân
        if (!empty($tenbenhnhan)) {
            $sql .= " AND bn.hoten LIKE ? ";
            $params[] = "%" . $tenbenhnhan . "%";
        }

        $sql .= " ORDER BY pkb.ngaykham ASC, kg.giobatdau ASC";

        $stmt = $conn->prepare($sql);
        if ($params) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $p->dongketnoi($conn);
            return $result;
        } else {
            $stmt->close();
            $p->dongketnoi($conn);
            return false;
        }
    }
}
?>
