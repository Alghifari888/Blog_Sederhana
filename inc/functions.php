<?php
// Fungsi slugify untuk membuat slug dari judul artikel
function slugify($text) {
    // Ganti karakter non-alphanumeric menjadi tanda strip
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate karakter ke ASCII
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Hapus karakter yang bukan huruf, angka, atau strip
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Hapus strip di awal dan akhir string
    $text = trim($text, '-');
    // Ganti strip ganda dengan strip tunggal
    $text = preg_replace('~-+~', '-', $text);
    // Ubah ke huruf kecil semua
    $text = strtolower($text);
    // Jika string kosong, ganti dengan "n-a"
    return empty($text) ? 'n-a' : $text;
}

// Fungsi untuk mengambil 1 post berdasarkan ID (untuk edit, admin panel)
function getPostByIdForEdit($conn, $post_id) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
}

// Fungsi untuk mengambil 1 post berdasarkan ID (untuk pengunjung, hanya yg published)
function getPostById($conn, $post_id) {
    $stmt = $conn->prepare("SELECT p.*, u.username AS author 
                            FROM posts p 
                            JOIN users u ON p.author_id = u.id 
                            WHERE p.id = ? AND p.status = 'published'
                            LIMIT 1");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
}

// Fungsi untuk mengupdate post
function updatePost($conn, $id, $title, $slug, $content, $status, $image = null) {
    if ($image) {
        $sql = "UPDATE posts SET title = ?, slug = ?, content = ?, status = ?, image = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("sssssi", $title, $slug, $content, $status, $image, $id);
    } else {
        $sql = "UPDATE posts SET title = ?, slug = ?, content = ?, status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);
        $stmt->bind_param("ssssi", $title, $slug, $content, $status, $id);
    }
    return $stmt->execute();
}

// Fungsi untuk mendapatkan semua post (admin panel) - sudah termasuk kolom image
function getAllPosts($conn) {
    $sql = "SELECT p.id, p.title, p.slug, p.status, p.created_at, p.image, u.username AS author 
            FROM posts p 
            JOIN users u ON p.author_id = u.id
            ORDER BY p.created_at DESC";
    $result = $conn->query($sql);

    $posts = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Fungsi untuk mendapatkan semua post yang sudah dipublish (untuk ditampilkan di beranda)
function getPublishedPosts($conn) {
    $sql = "SELECT p.*, u.username AS author 
            FROM posts p 
            JOIN users u ON p.author_id = u.id
            WHERE p.status = 'published'
            ORDER BY p.created_at DESC";
    $result = $conn->query($sql);

    $posts = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}
?>
