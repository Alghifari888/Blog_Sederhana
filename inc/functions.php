<?php
// functions.php - fungsi umum untuk CRUD dan query blog sederhana dengan dukungan gambar

// Ambil semua artikel yang statusnya 'published', urut dari terbaru (untuk halaman depan)
function getPublishedPosts($conn) {
    $sql = "SELECT id, title, slug, content, created_at, image FROM posts WHERE status = 'published' ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $posts = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Ambil semua artikel tanpa filter status (untuk admin/dashboard)
function getAllPosts($conn) {
    $sql = "SELECT posts.*, users.username 
            FROM posts 
            LEFT JOIN users ON posts.author_id = users.id 
            ORDER BY created_at DESC";
    $result = $conn->query($sql);

    $posts = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Ambil artikel berdasarkan ID dan status 'published' (untuk post.php)
function getPostById($conn, $id) {
    $stmt = $conn->prepare("SELECT id, title, slug, content, created_at, image FROM posts WHERE id = ? AND status = 'published' LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
}

// Ambil artikel berdasarkan ID tanpa cek status (untuk edit)
function getPostByIdForEdit($conn, $id) {
    $stmt = $conn->prepare("SELECT id, title, slug, content, status, image FROM posts WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return ($result && $result->num_rows === 1) ? $result->fetch_assoc() : null;
}

// Buat slug URL-friendly dari judul
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}
