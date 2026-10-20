<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "web_thoi_trang_douyin";

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>