<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';
    $author_id = $_SESSION['user_id'];
    $slug = slugify($title);

    // Upload gambar
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadPath = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $newFileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO posts (title, slug, content, status, author_id, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $title, $slug, $content, $status, $author_id, $image);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Buat Artikel Baru</title>
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
    <h1>Buat Artikel Baru</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Judul Artikel</label>
            <input type="text" id="title" name="title" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Isi Artikel</label>
            <textarea id="content" name="content" rows="8" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Gambar (opsional)</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" />
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="draft">Draft</option>
                <option value="published" selected>Published</option>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
