# Hệ Thống Quản Lý Bệnh Viện Hạnh Phúc

## Sơ Đồ Class UML

Sơ đồ class UML của hệ thống đã được tạo và lưu trong thư mục **`docs/`**

### 📂 Xem Sơ Đồ

- **Hình ảnh PNG**: [docs/KhoaLuanTotNghiep_ClassDiagram.png](docs/KhoaLuanTotNghiep_ClassDiagram.png)
- **Hình ảnh SVG**: [docs/KhoaLuanTotNghiep_ClassDiagram.svg](docs/KhoaLuanTotNghiep_ClassDiagram.svg)
- **File nguồn PlantUML**: [docs/class-diagram.puml](docs/class-diagram.puml)

### 📖 Tài Liệu

- **Hướng dẫn chi tiết**: [docs/README.md](docs/README.md)
- **Hướng dẫn sử dụng**: [docs/HUONG_DAN_SU_DUNG.md](docs/HUONG_DAN_SU_DUNG.md)

### 🎯 Nội Dung Sơ Đồ

Sơ đồ UML mô tả toàn bộ kiến trúc hệ thống bao gồm:

- **40+ lớp** trong hệ thống
- **Lớp kết nối Database** (clsKetNoi)
- **Lớp Model** với cấu trúc kế thừa:
  - mNguoiDung (Base class)
  - mBenhNhan, mBacSi, mChuyenGia (Derived classes)
- **Lớp nghiệp vụ**: Phiếu khám, hồ sơ bệnh án, đơn thuốc, xét nghiệm
- **Lớp Controller**: MVC pattern
- **Mối quan hệ**: Inheritance, Composition, Aggregation, Association

### 🛠️ Công Nghệ

- **Ngôn ngữ**: PHP
- **Database**: MySQL
- **Kiến trúc**: MVC (Model-View-Controller)
- **Công cụ vẽ sơ đồ**: PlantUML

---

**Lưu ý**: Đây là tài liệu kỹ thuật cho khóa luận tốt nghiệp. Sơ đồ được tạo tự động từ phân tích code nguồn.
