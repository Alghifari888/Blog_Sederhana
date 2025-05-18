<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/inc/auth.php';

checkLogin();

// Proses simpan data (create atau update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $slug = slugify($title);

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

    header("Location: dashboard.php");
    exit;
}

// Jika ada request AJAX untuk ambil data post detail
if (isset($_GET['action']) && $_GET['action'] === 'get_post' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    header('Content-Type: application/json');
    if ($post) {
        echo json_encode([
            'success' => true,
            'data' => $post
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Post tidak ditemukan'
        ]);
    }
    exit;
}

$posts = getAllPosts($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
     <link href="../bootstrap-5.3.6-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/dashboard.css" />
</head>
<body>
<?php include 'navbar.php'; ?>

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
                                data-id="<?= $post['id'] ?>"
                                onclick="openEditModal(<?= $post['id'] ?>)">Edit</button>
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
// Modal Bootstrap instance
const postModal = new bootstrap.Modal(document.getElementById('postModal'));

// Fungsi untuk modal buat baru
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

// Fungsi buka modal edit dengan AJAX load data dari server
function openEditModal(postId) {
    fetch(`dashboard.php?action=get_post&id=${postId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const post = data.data;
                document.getElementById('postModalLabel').textContent = 'Edit Artikel';
                document.getElementById('post-id').value = post.id;
                document.getElementById('post-title').value = post.title;
                document.getElementById('post-content').value = post.content;
                document.getElementById('post-status').value = post.status;
                document.getElementById('post-image').value = '';

                if (post.image) {
                    document.getElementById('current-image').innerHTML = `<p>Gambar saat ini:</p><img src="../uploads/${post.image}" style="max-height: 100px;" alt="Current Image" />`;
                } else {
                    document.getElementById('current-image').innerHTML = '';
                }

                postModal.show();
            } else {
                alert('Gagal memuat data artikel: ' + data.message);
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan saat memuat data artikel.');
            console.error(err);
        });
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>
