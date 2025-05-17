```markdown
# 📝 Blog Sederhana - Sistem Manajemen Konten (CMS) Mini

**Blog Sederhana** adalah proyek CMS mini berbasis PHP dan MySQL dengan tampilan modern menggunakan Bootstrap dan styling custom. Cocok untuk belajar membuat sistem blog dari nol secara modular dan rapi.

---

> ✨ Kutipan  
> "Aku tidak berilmu; yang berilmu hanyalah DIA. Jika tampak ilmu dariku, itu hanyalah pantulan dari Cahaya-Nya."

---

## 🚀 Fitur Lengkap

- ✅ **Autentikasi Admin**  
  Form login untuk melindungi akses dashboard admin.
  
- 📝 **Manajemen Artikel (CRUD)**  
  Tambah, edit, dan hapus artikel melalui dashboard dinamis.

- 🧾 **Slug Otomatis**  
  Slug URL dibentuk otomatis dari judul artikel (SEO friendly).

- 🖼️ **Upload Gambar**  
  Admin dapat menyisipkan gambar untuk setiap artikel.

- 📅 **Waktu Dibuat & Diupdate**  
  Tercatat otomatis saat artikel dibuat dan diperbarui.

- 👀 **Tampilan Responsif**  
  Didukung oleh Bootstrap 5 dan file CSS custom.

- 🧩 **Modularisasi Template**  
  Navbar dan footer terpisah sebagai komponen reusable.

- 📁 **Folder Upload Aman**  
  Gambar disimpan di folder `uploads/` yang diproses secara aman.

---

## 📁 Struktur Folder & Berkas

```

.
├── admin/
│   ├── dashboard.php         # Halaman utama admin
│   ├── delete.php            # Proses hapus artikel
│   ├── footer.php            # Footer (juga tersedia di assets/)
│   ├── login.php             # Form login admin
│   ├── logout.php            # Logout admin
│   ├── navbar.php            # Navbar admin
│   └── inc/                  # Script pendukung admin
|       └── auth.php          # Sesion start
│
├── assets/
│   ├── footer.php            # Komponen footer umum
│   └── navbar.php            # Komponen navbar umum
│
├── css/
│   ├── dashboard.css         # Gaya untuk dashboard admin
│   ├── index.css             # Gaya untuk halaman utama
│   ├── login.css             # Gaya untuk form login
│   └── post.css              # Gaya untuk halaman artikel
│
├── inc/
│   ├── config.php            # Konfigurasi database
│   └── functions.php         # Fungsi umum (slugify, query, dll)
│
├── uploads/                  # Folder upload gambar artikel
│
├── index.php                 # Halaman utama website (list artikel publik)
├── post.php                  # Halaman detail artikel
└── README.md                 # Dokumentasi proyek ini

````

---

## ⚙️ Cara Instalasi & Menjalankan

1. **Clone repositori ini**:
   ```bash
   git clone https://github.com/kamu/blog_sederhana.git
   cd blog_sederhana
````

2. **Setup database**:

   * Buat database baru dengan nama `blog_sederhana`.
   * Import struktur tabel `users` dan `posts`.

   ```sql
   CREATE DATABASE blog_sederhana;
   USE blog_sederhana;

   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL,
       password VARCHAR(255) NOT NULL,
       email VARCHAR(100),
       created_at DATETIME DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('draft','published') DEFAULT 'published',
  `image` varchar(255) DEFAULT NULL
);
   ```

3. **Masukkan data admin secara manual:**

   ```sql
   INSERT INTO users (username, password, email)
   VALUES ('admin', BCRYPT('admin'), 'admin@example.com');
   ```

4. **Sesuaikan koneksi database** di `inc/config.php`:

   ```php
   $host = 'localhost';
   $user = 'root';
   $pass = '';
   $dbname = 'blog_sederhana';
   ```

5. **Jalankan lokal via XAMPP atau lainnya**
   Akses dari browser:

   ```
   http://localhost/blog_sederhana/
   http://localhost/blog_sederhana/admin/login.php
   ```

---

## 🎨 Tampilan

| Halaman               | Deskripsi                           |
| --------------------- | ----------------------------------- |
| `index.php`           | Menampilkan daftar artikel publik   |
| `post.php`            | Menampilkan detail artikel per slug |
| `admin/dashboard.php` | Panel admin untuk mengelola artikel |
| `admin/login.php`     | Form login admin                    |
| `admin/delete.php`    | Endpoint untuk menghapus artikel    |

---

## 🔐 Keamanan Dasar

* Semua akses admin diperiksa dengan `checkLogin()`.
* Pengguna tidak login akan diarahkan ke `login.php`.
* Validasi tipe file saat upload untuk mencegah eksekusi file berbahaya.
* XSS Prevention dengan `htmlspecialchars()`.

---

## 🧠 Credits

Dibuat oleh [@Alghifari888](https://github.com/Alghifari888) sebagai project **belajar dan open-source**.

---

## 🌟 License

MIT License.
Bebas digunakan untuk:

* Belajar
* Proyek pribadi
* Dikembangkan lebih lanjut secara bebas

---

Selamat belajar dan semoga bermanfaat!
✨ Kalau project ini membantu, boleh kasih ⭐ di GitHub ya!

```

---

Kalau kamu ingin versi ini disimpan sebagai `README.md`, tinggal bilang saja. Saya bisa bantu langsung buatkan file-nya.
```
