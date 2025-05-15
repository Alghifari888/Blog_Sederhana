<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika user belum login, redirect ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
