<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$post = getPostById($conn, $id);

if (!$post) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($post['title']) ?> - Blog Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .post-image {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            margin-bottom: 1rem;
            border-radius: 8px;
        }
    </style>
</head>
<body class="container mt-4">

    <a href="index.php" class="btn btn-secondary mb-3">Kembali ke Beranda</a>

    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <small class="text-muted">Dipublikasikan pada <?= htmlspecialchars($post['created_at']) ?></small>

    <?php if ($post['image'] && file_exists('uploads/' . $post['image'])): ?>
        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Gambar <?= htmlspecialchars($post['title']) ?>" class="post-image" />
    <?php endif; ?>

    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

</body>
</html>
