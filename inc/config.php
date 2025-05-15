<?php
// config.php - koneksi database MySQL

$host = "localhost";
$username = "root";
$password = "";
$dbname = "blog_Sederhana";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 biar support emoji & multilingual
$conn->set_charset("utf8mb4");
