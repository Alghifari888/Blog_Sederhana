<?php
session_start();

function checkLogin() {
    // Cek apakah session user_id sudah diset dan tidak kosong
    if (empty($_SESSION['user_id'])) {
        // Jika belum login, redirect ke halaman login
        header("Location: login.php");
        exit;
    }
}
?>
