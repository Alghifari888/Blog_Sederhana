<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/inc/auth.php';

checkLogin();

// Proses simpan data (create atau update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $slug = slugify($title);

    // Upload gambar jika ada
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['image']['tmp_name'];
        $originalName = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $imageName = uniqid() . '.' . $ext;
            move_uploaded_file($tmpName, __DIR__ . '/../uploads/' . $imageName);
        }
    }

    if ($id > 0) {
        // Update artikel
        if ($imageName) {
            updatePost($conn, $id, $title, $slug, $content, $status, $imageName);
        } else {
            updatePost($conn, $id, $title, $slug, $content, $status);
        }
    } else {
        // Insert baru
        $stmt = $conn->prepare("INSERT INTO posts (title, slug, content, status, image, author_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $author_id = $_SESSION['user_id'];
        $stmt->bind_param("sssssi", $title, $slug, $content, $status, $imageName, $author_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect agar tidak repost data jika refresh
    header("Location: dashboard.php");
    exit;
}

// Ambil semua post
$posts = getAllPosts($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/dashboard.css" />
</head>
<body>
<?php include 'navbar.php'; // ✅ Footer di paling bawah ?>

 <div class="container">
    <h1>Dashboard</h1>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#postModal" onclick="openCreateModal()">Buat Artikel Baru</button>

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
                            <button class="btn btn-primary btn-sm" 
                                onclick='openEditModal(<?= json_encode($post) ?>)'>Edit</button>
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

<!-- Modal untuk Create/Edit -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="postModalLabel">Buat Artikel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="id" id="post-id" value="0" />
          <div class="mb-3">
              <label for="post-title" class="form-label">Judul</label>
              <input type="text" class="form-control" id="post-title" name="title" required />
          </div>
          <div class="mb-3">
              <label for="post-content" class="form-label">Konten</label>
              <textarea class="form-control" id="post-content" name="content" rows="6" required></textarea>
          </div>
          <div class="mb-3">
              <label for="post-status" class="form-label">Status</label>
              <select class="form-select" id="post-status" name="status" required>
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
              </select>
          </div>
          <div class="mb-3">
              <label for="post-image" class="form-label">Gambar (opsional)</label>
              <input type="file" class="form-control" id="post-image" name="image" accept="image/*" />
              <div id="current-image" class="mt-2"></div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const postModal = new bootstrap.Modal(document.getElementById('postModal'));

function openCreateModal() {
    document.getElementById('postModalLabel').textContent = 'Buat Artikel Baru';
    document.getElementById('post-id').value = 0;
    document.getElementById('post-title').value = '';
    document.getElementById('post-content').value = '';
    document.getElementById('post-status').value = 'draft';
    document.getElementById('post-image').value = '';
    document.getElementById('current-image').innerHTML = '';
    postModal.show();
}

function openEditModal(post) {
    document.getElementById('postModalLabel').textContent = 'Edit Artikel';
    document.getElementById('post-id').value = post.id;
    document.getElementById('post-title').value = post.title;
    document.getElementById('post-content').value = decodeHTMLEntities(post.content || '');

    document.getElementById('post-status').value = post.status;
    document.getElementById('post-image').value = '';
    
    if (post.image) {
        const imgHtml = `<p>Gambar saat ini:</p><img src="../uploads/${post.image}" style="max-height: 100px;" alt="Current Image" />`;
        document.getElementById('current-image').innerHTML = imgHtml;
    } else {
        document.getElementById('current-image').innerHTML = '';
    }

    postModal.show();
}


// Fungsi untuk mengubah HTML entities menjadi karakter biasa
function decodeHTMLEntities(text) {
    const txt = document.createElement("textarea");
    txt.innerHTML = text;
    return txt.value;
}

</script>
<?php include 'footer.php'; // ✅ Footer di paling bawah ?>
</body>
</html>
