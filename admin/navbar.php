<?php
// Navbar utama untuk dashboard dan halaman umum
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow fixed-top">
  <div class="container">
    <!-- Brand / logo -->
    <a class="navbar-brand" href="dashboard.php">Blog Sederhana</a>

    <!-- Toggle menu di layar kecil -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu navbar -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Link dashboard -->
        <li class="nav-item">
          <a class="nav-link active" href="dashboard.php">Dashboard</a>
        </li>
        <!-- Link ke beranda blog -->
        <li class="nav-item">
          <a class="nav-link" href="../index.php" target="_blank">Lihat Blog</a>
        </li>
        <!-- Logout -->
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
