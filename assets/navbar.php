<?php
// navbar.php
// Komponen navigasi utama untuk blog, selalu berada di paling atas halaman
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container">
    <!-- Logo atau nama blog -->
    <a class="navbar-brand" href="index.php">Blog Sederhana</a>

    <!-- Tombol toggle menu saat layar kecil -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Daftar menu navigasi -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Tentang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Kontak</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
