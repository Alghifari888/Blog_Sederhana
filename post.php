<?php
// Memuat konfigurasi database dan fungsi-fungsi pendukung
require_once 'inc/config.php';
require_once 'inc/functions.php';

// Cek apakah parameter 'id' ada di URL, jika tidak redirect ke beranda
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Ambil id dari URL dan pastikan bertipe integer untuk keamanan
$id = (int)$_GET['id'];

// Ambil data post berdasarkan id dari fungsi getPostById
$post = getPostById($conn, $id);

// Jika data post tidak ditemukan, redirect ke halaman beranda
if (!$post) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <!-- Judul halaman sesuai judul post, dengan fungsi htmlspecialchars untuk keamanan -->
    <title><?= htmlspecialchars($post['title']) ?> - Blog Sederhana</title>

    <!-- Load Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Load CSS khusus untuk halaman ini -->
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/post.css" />  <!-- Tambah link css baru -->
</head>
<body>

    <!-- Menyisipkan navbar, yang sudah kita buat fixed di atas -->
    <?php include 'assets/navbar.php'; ?>

    <!-- Konten utama dibungkus main dengan container agar responsif dan layout rapi -->
    <main class="container" style="padding-top: 80px; padding-bottom: 40px;">

        <!-- Judul post -->
        <h1><?= htmlspecialchars($post['title']) ?></h1>

        
        <!-- Jika ada gambar dan file benar-benar ada di folder uploads -->
        <?php if ($post['image'] && file_exists('uploads/' . $post['image'])): ?>
            <img 
                src="uploads/<?= htmlspecialchars($post['image']) ?>" 
                alt="Gambar <?= htmlspecialchars($post['title']) ?>" 
                class="post-img my-4" 
            />
        <?php endif; ?>
        <!-- Tanggal publikasi post, diberi class muted agar tampil lebih samar -->
        <small class="text-muted">Dipublikasikan pada <?= htmlspecialchars($post['created_at']) ?></small>


        <!-- Isi konten post, pakai nl2br agar enter pada teks diubah jadi baris baru -->
        <div class="post-content">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>

    </main>

    <!-- Menyisipkan footer di bawah -->
    <?php include 'assets/footer.php'; ?>

    <!-- Load Bootstrap JS dan dependencies untuk komponen interaktif (navbar toggler) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
