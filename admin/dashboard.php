<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/inc/auth.php';

checkLogin();

$posts = getAllPosts($conn); // Ambil semua post untuk admin
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand">Admin Panel</a>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container">
    <h1>Dashboard</h1>
    <a href="create.php" class="btn btn-success mb-3">Buat Artikel Baru</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Status</th>
                <th>Gambar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($post['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php 
                            $imagePath = __DIR__ . '/../uploads/' . $post['image'];
                            if (!empty($post['image']) && file_exists($imagePath)): ?>
                                <img src="../uploads/<?= htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8') ?>" alt="gambar" style="max-height:50px;" />
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$post['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete.php?id=<?= (int)$post['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Belum ada artikel</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
