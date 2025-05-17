<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';

$posts = getPublishedPosts($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Blog Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/index.css" />
</head>
<body>

    <?php include 'assets/navbar.php'; // ✅ Menyisipkan navbar hanya sekali di dalam body ?>

    <!-- ✅ Konten utama dibungkus main agar layout fleksibel dan responsif -->
    <main class="container">

        <h1 class="mt-4">Blog Sederhana</h1>

        <?php if (count($posts) === 0): ?>
            <p>Tidak ada artikel.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-4 post-card">
                        <div class="card">
                            <?php if ($post['image'] && file_exists('uploads/' . $post['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="card-img-top post-img" alt="Gambar <?= htmlspecialchars($post['title']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr(strip_tags($post['content']), 0, 120)) ?>...</p>
                                <a href="post.php?id=<?= $post['id'] ?>" class="btn btn-primary">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <?php include 'assets/footer.php'; // ✅ Footer di paling bawah ?>

</body>
</html>
