<?php
session_start();
require_once 'inc/auth.php';
require_once '../inc/config.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int)$_GET['id'];

// Cari dulu nama file gambarnya
$stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

$imageName = $post['image'];

// Hapus artikel dari database
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    // Jika ada gambar dan file ada di folder uploads, hapus file gambarnya
    if ($imageName && file_exists('../uploads/' . $imageName)) {
        unlink('../uploads/' . $imageName);
    }
    header("Location: dashboard.php?msg=Artikel berhasil dihapus");
    exit;
} else {
    header("Location: dashboard.php?msg=Gagal menghapus artikel");
    exit;
}
