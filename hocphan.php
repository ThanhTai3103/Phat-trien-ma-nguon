<?php
include "config.php";
session_start();

if (!isset($_SESSION['MaSV'])) {
    die("⚠ Bạn cần đăng nhập để tiếp tục.");
}

$maSV = $_SESSION['MaSV'];

// Truy vấn danh sách học phần đã đăng ký của sinh viên
$sql = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi 
        FROM dangky dk 
        JOIN hocphan hp ON dk.MaHocPhan = hp.MaHP
        WHERE dk.MaSV = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maSV);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Học Phần Đã Đăng Ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            text-decoration: none;
            background: #007bff;
            color: white;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h2>📚 HỌC PHẦN ĐÃ ĐĂNG KÝ</h2>

    <table>
        <tr>
            <th>Mã Học Phần</th>
            <th>Tên Học Phần</th>
            <th>Số Tín Chỉ</th>
            <th></th>
        </tr>
        <?php 
        if ($result && $result->num_rows > 0): 
            while ($row = $result->fetch_assoc()): 
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['MaHP']) ?></td>
                    <td><?= htmlspecialchars($row['TenHP']) ?></td>
                    <td><?= htmlspecialchars($row['SoTinChi']) ?></td>
                    <td><a href="dangki.php?MaHocPhan=<?= htmlspecialchars($row['MaHP']) ?>" class="btn">📝 Đăng Ký</a></td>
                </tr>
                <?php 
            endwhile; 
        else:
            echo "<tr><td colspan='4'>Không có học phần nào.</td></tr>";
        endif;
        ?>
    </table>

    <!-- Nút quay lại trang chủ (thêm ở cuối trang) -->
    <a href="index.php" class="btn">🏠 Quay lại Trang Chủ</a>
</body>
</html>
