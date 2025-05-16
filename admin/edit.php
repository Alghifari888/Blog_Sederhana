<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/auth.php';

checkLogin();

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$post = getPostByIdForEdit($conn, $post_id);

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = in_array($_POST['status'], ['draft', 'published']) ? $_POST['status'] : 'draft';
    $slug = slugify($title);

    $imageFileName = null;

    // Cek apakah ada file gambar yang diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $tmpName = $_FILES['image']['tmp_name'];
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageFileName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $destination = $uploadDir . $imageFileName;

        // Validasi tipe file sederhana
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowedTypes)) {
            $errors[] = "Tipe file gambar tidak diperbolehkan.";
        } else {
            if (!move_uploaded_file($tmpName, $destination)) {
                $errors[] = "Gagal mengupload gambar.";
            }
        }
    }

    if (empty($errors)) {
        if ($imageFileName) {
            // Hapus gambar lama kalau ada dan beda
            if (!empty($post['image']) && file_exists('../uploads/' . $post['image'])) {
                unlink('../uploads/' . $post['image']);
            }
        } else {
            $imageFileName = null; // biar updatePost tahu gak ganti gambar
        }

        $updated = updatePost($conn, $post_id, $title, $slug, $content, $status, $imageFileName);
        if ($updated) {
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan perubahan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Artikel - Admin</title>
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
    <h1 class="mb-4">Edit Artikel</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Judul Artikel</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required />
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Isi Artikel</label>
            <textarea class="form-control" id="content" name="content" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Gambar (opsional, upload untuk ganti gambar lama)</label><br />
            <?php if (!empty($post['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Gambar Artikel" style="max-width: 200px; display: block; margin-bottom: 10px;" />
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*" />
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
