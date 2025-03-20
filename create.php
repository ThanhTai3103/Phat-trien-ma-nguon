<?php
include "config.php"; // Kết nối database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MaSV = trim($_POST["MaSV"]);
    $HoTen = trim($_POST["HoTen"]);
    $GioiTinh = $_POST["GioiTinh"];
    $NgaySinh = $_POST["NgaySinh"];
    $MaNganh = trim($_POST["MaNganh"]);

    // 🔹 Kiểm tra MaSV đã tồn tại chưa
    $check_sql = "SELECT MaSV FROM SinhVien WHERE MaSV = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $MaSV);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("<script>alert('⚠ Lỗi: Mã sinh viên đã tồn tại!'); window.history.back();</script>");
    }

    // 🔹 Xử lý upload ảnh
    $Hinh = "";
    if (!empty($_FILES["Hinh"]["name"])) {
        $target_dir = "uploads/";

        // Tạo thư mục uploads nếu chưa có
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Tạo tên file duy nhất để tránh trùng lặp
        $file_ext = pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION);
        $new_file_name = $MaSV . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_file_name;

        // Di chuyển file vào thư mục uploads
        if (move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
            $Hinh = $target_file;
        } else {
            die("<script>alert('⚠ Lỗi: Không thể tải ảnh lên!'); window.history.back();</script>");
        }
    }

    // 🔹 Thêm sinh viên vào database
    $sql = "INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $MaSV, $HoTen, $GioiTinh, $NgaySinh, $Hinh, $MaNganh);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Thêm sinh viên thành công!'); window.location.href = 'index.php';</script>";
        exit();
    } else {
        echo "<script>alert('⚠ Lỗi: " . $conn->error . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sinh Viên</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <!-- Thanh menu -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Test1</a>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Sinh Viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="hocphan.php">Học Phần</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangky.php">Đăng Ký</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng Nhập</a></li>
                </ul>
            </div>
        </nav>

        <h2 class="mt-3 text-center">THÊM SINH VIÊN</h2>
        <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto border p-4 shadow">
            <div class="mb-3">
                <label class="form-label">MaSV</label>
                <input type="text" name="MaSV" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">HoTen</label>
                <input type="text" name="HoTen" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">GioiTinh</label>
                <select name="GioiTinh" class="form-control">
                    <option value="Nam">Nam</option>
                    <option value="Nữ">Nữ</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">NgaySinh</label>
                <input type="date" name="NgaySinh" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Hình</label>
                <input type="file" name="Hinh" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">MaNganh</label>
                <input type="text" name="MaNganh" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </form>
    </div>
</body>
</html>
