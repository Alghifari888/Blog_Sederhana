<?php
session_start();
require_once 'inc/auth.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

$posts = getAllPosts($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body class="container mt-4">
    <h1>Dashboard Admin</h1>
    <a href="create.php" class="btn btn-primary mb-3">Buat Artikel Baru</a>
    <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Penulis</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($posts) === 0): ?>
                <tr><td colspan="6" class="text-center">Tidak ada artikel.</td></tr>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td>
                        <?php if ($post['image'] && file_exists("../uploads/" . $post['image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Thumbnail" class="thumbnail" />
                        <?php else: ?>
                            <span class="text-muted">Tidak ada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($post['title']) ?></td>
                    <td><?= htmlspecialchars($post['status']) ?></td>
                    <td><?= htmlspecialchars($post['username'] ?? 'Admin') ?></td>
                    <td><?= htmlspecialchars($post['created_at']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
