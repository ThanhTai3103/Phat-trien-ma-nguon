<?php
session_start();
session_destroy(); // Hủy tất cả session
header("Location: dangnhap.php"); // Chuyển hướng về trang đăng nhập
exit();
?>
