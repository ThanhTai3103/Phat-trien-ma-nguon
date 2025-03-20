<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "test1";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
