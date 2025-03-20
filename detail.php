<?php
include "config.php"; // Kết nối database

// Kiểm tra xem có ID sinh viên không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
    exit();
}

$student_id = $_GET['id'];

// Truy vấn lấy thông tin sinh viên và ngành học
$sql = "SELECT SinhVien.*, NganhHoc.TenNganh 
        FROM SinhVien 
        LEFT JOIN NganhHoc ON SinhVien.MaNganh = NganhHoc.MaNganh 
        WHERE MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Hệ Thống</a>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangky.php">Đăng Ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng Nhập</a></li>
                </ul>
            </div>
        </nav>

        <h2 class="mt-3 text-center">Thông Tin Sinh Viên</h2>

        <div class="card mx-auto mt-4" style="max-width: 500px;">
            <div class="card-body text-center">
                <?php if (!empty($student['HinhAnh'])): ?>
                    <img src="uploads/<?= htmlspecialchars($student['HinhAnh']) ?>" alt="Ảnh Sinh Viên" class="img-fluid rounded-circle mb-3" width="150">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" alt="No Image" class="img-fluid rounded-circle mb-3">
                <?php endif; ?>

                <h4 class="card-title"><?= htmlspecialchars($student['HoTen']) ?></h4>
                <p class="card-text"><strong>Giới Tính:</strong> <?= htmlspecialchars($student['GioiTinh']) ?></p>
                <p class="card-text"><strong>Ngày Sinh:</strong> <?= date("d/m/Y", strtotime($student['NgaySinh'])) ?></p>
                <p class="card-text"><strong>Ngành Học:</strong> <?= htmlspecialchars($student['TenNganh'] ?? 'Chưa có thông tin') ?></p>

                <a href="edit.php?id=<?= htmlspecialchars($student['MaSV']) ?>" class="btn btn-primary">Chỉnh sửa</a>
                <a href="index.php" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
