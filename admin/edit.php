<?php
session_start();
require_once 'inc/auth.php';
require_once '../inc/config.php';
require_once '../inc/functions.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int)$_GET['id'];
$post = getPostByIdForEdit($conn, $id);

if (!$post) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$title = $post['title'];
$content = $post['content'];
$status = $post['status'];
$currentImage = $post['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';

    if (!$title) {
        $errors[] = "Judul wajib diisi.";
    }
    if (!$content) {
        $errors[] = "Konten wajib diisi.";
    }

    // Proses upload gambar baru jika ada
    $imageName = $currentImage; // default tetap gambar lama
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = "Format gambar harus JPG, PNG, atau GIF.";
        } else {
            $imageTmp = $_FILES['image']['tmp_name'];
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $targetPath = '../uploads/' . $imageName;

            if (!move_uploaded_file($imageTmp, $targetPath)) {
                $errors[] = "Gagal mengunggah gambar.";
            } else {
                // Hapus gambar lama jika ada dan berbeda
                if ($currentImage && file_exists('../uploads/' . $currentImage)) {
                    unlink('../uploads/' . $currentImage);
                }
            }
        }
    }

    if (empty($errors)) {
        $slug = slugify($title);
        $stmt = $conn->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, status = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $title, $slug, $content, $status, $imageName, $id);

        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=Artikel berhasil diperbarui");
            exit;
        } else {
            $errors[] = "Gagal memperbarui data di database.";
            // Jika upload gambar baru gagal simpan, hapus file baru agar tidak menumpuk
            if ($imageName !== $currentImage && file_exists('../uploads/' . $imageName)) {
                unlink('../uploads/' . $imageName);
            }
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
<body class="container mt-4">

    <h1>Edit Artikel</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Judul</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Konten</label>
            <textarea name="content" id="content" class="form-control" rows="8" required><?= htmlspecialchars($content) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Gambar Utama (opsional)</label>
            <?php if ($currentImage): ?>
                <div class="mb-2">
                    <img src="../uploads/<?= htmlspecialchars($currentImage) ?>" alt="Gambar lama" style="max-width: 200px; max-height: 150px; object-fit: contain;">
                </div>
            <?php endif; ?>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            <small class="text-muted">Upload hanya jika ingin mengganti gambar.</small>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>

</body>
</html>
