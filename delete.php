<?php
include "config.php"; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $MaSV = $_GET["id"];

    // Kiểm tra sinh viên có tồn tại không
    $checkQuery = $conn->prepare("SELECT * FROM SinhVien WHERE MaSV = ?");
    $checkQuery->bind_param("s", $MaSV);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Không tìm thấy sinh viên!'); window.location='index.php';</script>";
        exit();
    }

    $sinhVien = $result->fetch_assoc(); // Lấy thông tin sinh viên
} elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["MaSV"])) {
    $MaSV = $_POST["MaSV"];

    // Xóa dữ liệu liên quan trong bảng khác trước (nếu có ràng buộc)
    $conn->query("DELETE FROM DangKy WHERE MaSV = '$MaSV'");

    // Xóa sinh viên
    $deleteQuery = $conn->prepare("DELETE FROM SinhVien WHERE MaSV = ?");
    $deleteQuery->bind_param("s", $MaSV);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Xóa sinh viên thành công!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa sinh viên: " . $conn->error . "');</script>";
    }
} else {
    echo "<script>alert('Lỗi: Tham số không hợp lệ!'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 10px; width: 50%; margin: auto; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { color: red; }
        button { background: red; color: white; border: none; padding: 10px; margin: 10px; cursor: pointer; border-radius: 5px; }
        a { text-decoration: none; padding: 10px; background: gray; color: white; border-radius: 5px; }
        img { border-radius: 10px; border: 2px solid #ddd; width: 100px; }
    </style>
</head>
<body>

<div class="container">
    <h2>⚠ Xác nhận xóa sinh viên</h2>
    <p><strong>Mã SV:</strong> <?= htmlspecialchars($sinhVien['MaSV']) ?></p>
    <p><strong>Họ Tên:</strong> <?= htmlspecialchars($sinhVien['HoTen']) ?></p>
    <p><strong>Giới Tính:</strong> <?= htmlspecialchars($sinhVien['GioiTinh']) ?></p>
    <p><strong>Ngày Sinh:</strong> <?= date("d/m/Y", strtotime($sinhVien['NgaySinh'])) ?></p>

    <!-- Hiển thị ảnh sinh viên -->
    <?php 
    $imagePath = "uploads/" . htmlspecialchars($sinhVien['Hinh']);
    if (!file_exists($imagePath) || empty($sinhVien['Hinh'])) {
        $imagePath = "uploads/default.png"; // Ảnh mặc định
    }
    ?>
    <p><img src="<?= $imagePath ?>" alt="Ảnh Sinh Viên"></p>

    <form method="POST">
        <input type="hidden" name="MaSV" value="<?= htmlspecialchars($sinhVien['MaSV']) ?>">
        <button type="submit">✅ Xác nhận xóa</button>
        <a href="index.php">❌ Hủy</a>
    </form>
</div>

</body>
</html>
