<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/auth.php'; // hanya untuk admin yang login

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';
    $author_id = $_SESSION['user_id']; // dari session login admin
    $slug = slugify($title);

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO posts (title, slug, content, status, author_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $slug, $content, $status, $author_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Artikel - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <a class="btn btn-outline-light" href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <h1 class="mb-4">Buat Artikel Baru</h1>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="title" class="form-label">Judul Artikel</label>
            <input type="text" class="form-control" name="title" id="title" required />
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Isi Artikel</label>
            <textarea class="form-control" name="content" id="content" rows="8" required></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
