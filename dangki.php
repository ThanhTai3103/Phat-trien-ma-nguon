<?php
session_start();
$conn = new mysqli("localhost", "root", "", "test1");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (!isset($_SESSION['MaSV'])) {
    die("⚠ Bạn cần đăng nhập để tiếp tục.");
}

$maSV = $_SESSION['MaSV'];

// Lấy thông tin sinh viên
$sql_sv = "SELECT * FROM sinhvien WHERE MaSV = ?";
$stmt = $conn->prepare($sql_sv);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result_sv = $stmt->get_result();
$sinhvien = $result_sv->fetch_assoc();

// Lưu đăng ký học phần
if (isset($_POST['save_register'])) {
    $ngayDK = date("Y-m-d");

    $sql_hp = "SELECT MaHP FROM hocphan";
    $result_hp = $conn->query($sql_hp);

    while ($row = $result_hp->fetch_assoc()) {
        $maHP = $row['MaHP'];
        $insert_sql = "INSERT INTO dangky (NgayDK, MaSV, MaHocPhan) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $ngayDK, $maSV, $maHP);
        $stmt->execute();
    }

    echo "<script>window.location.href = 'dangki.php?saved=1';</script>";
    exit();
}

// Xóa một học phần
if (isset($_GET['delete_id'])) {
    $maHP = $_GET['delete_id'];
    $delete_sql = "DELETE FROM dangky WHERE MaSV = ? AND MaHocPhan = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ss", $maSV, $maHP);
    $stmt->execute();
    echo "<script>window.location.href = 'dangki.php';</script>";
    exit();
}

// Xóa tất cả học phần
if (isset($_POST['clear_all'])) {
    $delete_all_sql = "DELETE FROM dangky WHERE MaSV = ?";
    $stmt = $conn->prepare($delete_all_sql);
    $stmt->bind_param("s", $maSV);
    $stmt->execute();
    echo "<script>window.location.href = 'dangki.php';</script>";
    exit();
}

// Lấy danh sách học phần đã đăng ký
$sql = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi 
        FROM dangky dk 
        JOIN hocphan hp ON dk.MaHocPhan = hp.MaHP
        WHERE dk.MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result = $stmt->get_result();

$total_courses = 0;
$total_credits = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Học Phần</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        .btn { background-color: green; color: white; padding: 5px 10px; border: none; cursor: pointer; margin: 5px; }
        .btn-delete { background-color: red; }
        .info-box { 
            border: 1px solid black; 
            padding: 10px; 
            width: 300px; 
            text-align: center; 
            margin: 20px auto; 
            background-color: #f9f9f9; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); 
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <h2>Đăng Ký Học Phần</h2>
    <table>
        <tr>
            <th>Mã HP</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['MaHP']) ?></td>
                <td><?= htmlspecialchars($row['TenHP']) ?></td>
                <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                <td><a href="?delete_id=<?= htmlspecialchars($row['MaHP']) ?>" class="btn btn-delete">❌ Xóa</a></td>
            </tr>
            <?php 
            $total_courses++;
            $total_credits += $row['SoTinChi'];
            ?>
        <?php endwhile; ?>
    </table>

    <p>Số lượng học phần: <strong><?= $total_courses ?></strong></p>
    <p>Tổng số tín chỉ: <strong><?= $total_credits ?></strong></p>

    <form method="POST">
        <button type="submit" name="save_register" class="btn">💾 Lưu Đăng Ký</button>
        <button type="submit" name="clear_all" class="btn btn-delete">🗑 Xóa Đăng Ký</button>
    </form>

    <?php if (isset($_GET['saved'])): ?>
        <div class="info-box">
            <h3>Thông tin Đăng Ký</h3>
            <p><strong>Mã số sinh viên:</strong> <?= htmlspecialchars($sinhvien['MaSV']) ?></p>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($sinhvien['HoTen']) ?></p>
            <p><strong>Ngày sinh:</strong> <?= htmlspecialchars($sinhvien['NgaySinh']) ?></p>
            <p><strong>Ngành học:</strong> <?= htmlspecialchars($sinhvien['NganhHoc']) ?></p>
            <p><strong>Ngày đăng ký:</strong> <?= date("d/m/Y") ?></p>
            <button class="btn" onclick="window.location.href='hocphan.php';">✔ Xác Nhận</button>
            </div>
    <?php endif; ?>
</body>
</html>
