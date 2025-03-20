<?php
session_start();
include "config.php";

// Kiểm tra nếu chưa đăng nhập, chuyển hướng về trang login
if (!isset($_SESSION['MaSV'])) {
    header("Location: dangnhap.php");
    exit();
}

$ma_sv = $_SESSION['MaSV']; // MSSV của người dùng hiện tại

$result = $conn->query("SELECT * FROM SinhVien JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <!-- Thanh menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Hệ Thống</a>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangki.php">Đăng Ký</a></li>
                </ul>
                <!-- Hiển thị MSSV người đăng nhập -->
                <span class="navbar-text text-light me-3">
                    MSSV: <strong><?= htmlspecialchars($ma_sv) ?></strong>
                </span>
                <a href="logout.php" class="btn btn-danger btn-sm">Đăng Xuất</a>
            </div>
        </nav>

        <h2 class="mt-3 text-center">TRANG SINH VIÊN</h2>
        <a href="create.php" class="btn btn-primary mb-3">Thêm Sinh Viên</a>

        <!-- Bảng danh sách sinh viên -->
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Mã SV</th>
                    <th>Họ Tên</th>
                    <th>Giới Tính</th>
                    <th>Ngày Sinh</th>
                    <th>Hình</th>
                    <th>Ngành Học</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["MaSV"] ?></td>
                        <td><?= $row["HoTen"] ?></td>
                        <td><?= $row["GioiTinh"] ?></td>
                        <td><?= date("d/m/Y", strtotime($row["NgaySinh"])) ?></td>
                        <td>
                            <img src="<?= $row["Hinh"] ?>" alt="Hình SV" width="80">
                        </td>
                        <td><?= $row["TenNganh"] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['MaSV'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="detail.php?id=<?= $row['MaSV'] ?>" class="btn btn-info btn-sm">Details</a>
                            <a href="delete.php?id=<?= $row['MaSV'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
