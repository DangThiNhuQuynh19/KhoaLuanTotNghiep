<?php
include_once("ketnoi.php");

class mLichHen {
    public function lichhen($ngay = null, $loaikham = null, $hinhthuclamviec = null, $tenbenhnhan = null) {
        $p = new clsketnoi();
        $conn = $p->moketnoi();

        // --- KHẮC PHỤC LỖI: Tắt chế độ ONLY_FULL_GROUP_BY cho phiên làm việc này ---
        // Lệnh này giúp MySQL chấp nhận GROUP BY mà không bắt buộc liệt kê toàn bộ các cột
        $conn->query("SET sql_mode = 'NO_ENGINE_SUBSTITUTION'"); 
        // --------------------------------------------------------------------------

        $sql = "SELECT 
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
            LEFT JOIN bacsi bs ON pkb.mabacsi = bs.mabacsi
            LEFT JOIN chuyengia cg ON pkb.mabacsi = cg.machuyengia
            JOIN trangthai tt ON pkb.matrangthai = tt.matrangthai
            WHERE 1=1 ";

        $params = [];
        $types = ""; 

        // 1. Lọc theo ngày
        if (!empty($ngay)) {
            $sql .= " AND DATE(pkb.ngaykham) = ? ";
            $params[] = $ngay;
            $types .= "s";
        } else {
            $sql .= " AND DATE(pkb.ngaykham) = CURDATE() ";
        }

        // 2. Lọc theo loại khám
        if (!empty($loaikham)) {
            if ($loaikham === "bacsi") {
                $sql .= " AND bs.mabacsi IS NOT NULL ";
            } elseif ($loaikham === "chuyengia") {
                $sql .= " AND cg.machuyengia IS NOT NULL ";
            }
        }

        // 3. Lọc theo hình thức
        if (!empty($hinhthuclamviec)) {
            $sql .= " AND llv.hinhthuclamviec = ? ";
            $params[] = $hinhthuclamviec;
            $types .= "s";
        }

        // 4. Lọc theo tên bệnh nhân
        if (!empty($tenbenhnhan)) {
            $sql .= " AND bn.hoten LIKE ? ";
            $params[] = "%" . $tenbenhnhan . "%";
            $types .= "s";
        }

        // Gom nhóm để tránh trùng lặp dữ liệu (nguyên nhân gây lỗi double trước đó)
        $sql .= " GROUP BY pkb.maphieukhambenh ";

        $sql .= " ORDER BY pkb.ngaykham ASC, kg.giobatdau ASC";

        $stmt = $conn->prepare($sql);
        
        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $p->dongketnoi($conn);
            return $result;
        } else {
            // In lỗi ra nếu vẫn còn lỗi khác (để debug)
            // echo $stmt->error; 
            $stmt->close();
            $p->dongketnoi($conn);
            return false;
        }
    }
}
?>