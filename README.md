# 💖 LovePage — Platform Pembuat Website Ucapan Digital

**LovePage** adalah aplikasi web lengkap berbasis **Native PHP 8+** dan **MySQL/MariaDB (PDO)** untuk membuat website ucapan digital interaktif yang dipersonalisasi tanpa memerlukan registrasi atau login akun pengguna.

Aplikasi ini dirancang khusus dengan standar arsitektur bersih, estetika modern pastel glassmorphic, mobile-first responsif, dan keamanan berstandar industri yang mudah dipahami oleh siswa Rekayasa Perangkat Lunak (SMK RPL) maupun pengembang web profesional.

---

## 🌟 Fitur Utama

1. **Pembuatan Tanpa Perlu Akun (No-Login Required)**:
   - Pengguna dapat langsung membuat dan menerbitkan website ucapan kapan saja tanpa proses pendaftaran yang rumit.
   - Menggunakan sistem **Token Rahasia Kriptografis** (`token_edit`) yang di-hash dengan algoritma **SHA-256** di database untuk mengedit atau menghapus ucapan di kemudian hari.

2. **Wizard Pembuat 5 Langkah (Multi-Step Wizard)**:
   - **Langkah 1: Informasi Dasar** — Judul, Nama Penerima, Nama Pengirim, Jenis Momen, dan Pesan Ucapan dengan penghitung karakter.
   - **Langkah 2: Kustomisasi Tampilan** — 5 pilihan tema visual estetik dan color picker aksen warna.
   - **Langkah 3: Unggah Media** — Drag-and-drop multi foto (maksimal 10 foto), penataan urutan foto, pemberian caption di setiap foto, dan pemilihan musik latar (preset bawaan maupun upload file MP3/WAV sendiri).
   - **Langkah 4: Pengalaman Pembuka / Mini-Game** — Pilihan kejutan pembuka interaktif sebelum membaca surat. Dilengkapi fitur tombol **"Coba Mainkan"** langsung di editor!
   - **Langkah 5: Tinjau & Publikasikan** — Rangkuman data lengkap dan tombol publikasi dengan proteksi double-click submission.

3. **Pratinjau Langsung (Sticky Smartphone Simulator)**:
   - Simulator ponsel interaktif di sisi kanan layar editor yang terupdate secara *real-time* saat pengguna mengetik teks, mengganti tema visual, memilih warna aksen, atau mengunggah foto.

4. **5 Tema Visual Estetik**:
   - 🌸 **Romantic Pastel**: Hangat, lembut, penuh cinta dengan partikel hati melayang.
   - 🌙 **Dark Romance**: Mewah, misterius, dengan latar malam bertabur bintang & nebula glowing.
   - 🎂 **Birthday Celebration**: Ceria, meriah, dengan partikel taburan confetti berwarna-warni.
   - 🌼 **Friendship & Bestie**: Segar, cerah bernuansa biru langit dan tosca untuk sahabat terdekat.
   - ✨ **Elegant Minimalist**: Tipografi serif modern berkelas untuk momen formal atau kelulusan.

5. **4 Pengalaman Pembuka / Mini-Game Interaktif**:
   - 💌 **Buka Langsung (No Mini-Game)**: Kartu amplop estetik dengan animasi pembuka surat.
   - 💖 **Catch the Heart**: Mini-game menangkap 5 hati jatuh dari atas layar ponsel/laptop.
   - 🔍 **Find the Heart**: Menemukan 1 hati bercahaya di balik kisi dekorasi lucu.
   - ❓ **Love Quiz**: Menjawab pertanyaan manis interaktif sebelum membuka surat.

6. **Galeri Kenangan & Lightbox**:
   - Foto kenangan tersusun rapi dalam tata letak responsif dengan caption manis.
   - Klik pada foto untuk memperbesar tampilan (*lightbox view*) resolusi penuh.

7. **Pemutar Musik Latar (Music Player)**:
   - Tombol putar musik mengambang (*floating vinyl pill*) dengan animasi rotasi piringan hitam saat musik diputar.
   - Penanganan kebijakan *browser autoplay restrictions* secara elegan: musik mulai mengalun otomatis begitu pengguna berinteraksi dengan mini-game atau membuka amplop.

8. **Fitur Berbagi Cepat (Social Sharing)**:
   - 1-Klik bagikan langsung ke WhatsApp dengan pesan teks otomatis.
   - Salin link langsung ke clipboard dengan notifikasi toast.
   - Integrasi Web Share API bawaan perangkat smartphone.

---

## 🛠️ Arsitektur Teknologi

- **Backend**: Native PHP 8.0+ (Tanpa Framework seperti Laravel/Node.js, murni logika Native yang bersih).
- **Database**: MySQL / MariaDB dengan koneksi **PDO (PHP Data Objects)** & Prepared Statements.
- **Frontend**: HTML5 Semantik, Vanilla CSS3 (Custom Properties / Design Tokens, Flexbox, CSS Grid, Glassmorphism), dan Vanilla Modern JavaScript (ES6+).
- **Ikon**: Font Awesome 6.5 CDN.
- **Tipografi**: Google Fonts (`Outfit`, `Plus Jakarta Sans`, `Playfair Display`, `Caveat`).
- **Web Server Lokal**: XAMPP Apache / PHP Built-in Server.

---

## 📁 Struktur Direktori Proyek

```text
lovepage/
├── index.php                 # Halaman utama (Landing Page)
├── editor.php                # Wizard pembuatan ucapan & simulator ponsel
├── preview.php               # Pratinjau interaktif ucapan
├── simpan.php                # Backend proses publikasi & upload media
├── ucapan.php                # Halaman publik website ucapan (Renderer)
├── edit.php                  # Halaman edit menggunakan token rahasia
├── hapus.php                 # Penghapusan ucapan & pembersihan file fisik
├── setup_db.php              # Script otomatis inisialisasi database
├── .htaccess                 # Konfigurasi routing URL & security headers
├── config/
│   └── koneksi.php           # Koneksi PDO dengan auto-fallback port 3306 & 3307
├── includes/
│   └── functions.php         # Fungsi utilitas (keamanan, CSRF, validasi MIME, sanitasi)
├── database/
│   └── db_lovepage.sql       # Skema SQL tabel ucapan & foto_ucapan beserta seed data
├── assets/
│   ├── css/
│   │   ├── style.css         # Master design system & landing page styles
│   │   ├── editor.css        # Stylesheet wizard editor & phone mockup
│   │   └── ucapan.css        # Stylesheet tema ucapan, game, & kanvas partikel
│   ├── js/
│   │   ├── main.js           # Script umum & modal demo
│   │   ├── editor.js         # Logika wizard langkah, live sync, drag-drop, audio
│   │   └── ucapan.js         # Logika mini game, kanvas partikel tema, audio, lightbox
│   └── audio/                # Preset audio instrumen melodi lembut bawaan
└── uploads/
    ├── .htaccess             # Keamanan tingkat tinggi: blokir eksekusi file script PHP
    ├── foto/                 # Folder penyimpanan foto yang diunggah
    └── musik/                # Folder penyimpanan file audio kustom
```

---

## 🚀 Panduan Instalasi & Menjalankan di XAMPP

### Langkah 1: Pindahkan Folder Proyek
1. Buka folder instalasi XAMPP Anda (biasanya di `C:\xampp\htdocs`).
2. Salin atau letakkan folder proyek ini ke dalam `C:\xampp\htdocs\lovepage`.

### Langkah 2: Buat Database di phpMyAdmin
1. Buka browser dan buka `http://localhost/phpmyadmin`.
2. Klik tab **Import** (Impor).
3. Pilih file `database/db_lovepage.sql` dari folder proyek.
4. Klik tombol **Go** (Kirim). Database `db_lovepage` beserta seluruh tabel dan data sampel otomatis terbuat!

> *Atau alternatif cepat melalui CLI:*
> Jalankan perintah berikut di terminal:
> ```powershell
> php setup_db.php
> ```

### Langkah 3: Jalankan di Browser
Buka browser Anda dan akses:
- **Landing Page**: `http://localhost/lovepage/index.php`
- **Buat Ucapan**: `http://localhost/lovepage/editor.php`
- **Lihat Contoh Bawaan**:
  - `http://localhost/lovepage/ucapan.php?kode=romantis26` (Mini-game Catch the Heart)
  - `http://localhost/lovepage/ucapan.php?kode=ultah21` (Mini-game Love Quiz)

---

## ⚡ Menjalankan Menggunakan PHP Built-in Server

Anda juga dapat menjalankannya tanpa Apache hanya menggunakan PHP CLI bawaan:

```powershell
cd "c:\teks ucapan web"
php -S 127.0.0.1:8000
```

Lalu buka `http://127.0.0.1:8000` pada peramban web Anda.

---

## 🔒 Fitur Keamanan yang Diterapkan

1. **Pencegahan SQL Injection**: Seluruh *query* database 100% menggunakan **PDO Prepared Statements** dengan *parameter binding*.
2. **Pencegahan Cross-Site Scripting (XSS)**: Semua output dinamis yang ditampilkan ke pengguna di-escape menggunakan fungsi pembungkus `e()` (`htmlspecialchars` dengan flag `ENT_QUOTES`).
3. **Perlindungan CSRF (Cross-Site Request Forgery)**: Setiap form pengiriman (*publishing*, *editing*, *deletion*) diverifikasi dengan token CSRF acak yang tersimpan dalam *session*.
4. **Validasi File Ganda (Frontend & Backend)**:
   - Validasi ekstensi file (*allowlist*: JPG, PNG, WebP untuk gambar; MP3, WAV, M4A, OGG untuk audio).
   - Validasi tipe MIME asli menggunakan ekstensi `finfo` server (bukan sekadar membaca ekstensi file).
   - Batasan ukuran file (maksimal 5 MB untuk gambar, 10 MB untuk audio).
5. **Nama File Acak (Anti Path Traversal)**: Seluruh file yang diunggah diberi nama acak kriptografis (`bin2hex(random_bytes(16))`).
6. **Blokir Eksekusi PHP di Folder `uploads/`**: Terdapat file `uploads/.htaccess` yang mematikan *PHP engine* dan menolak akses ke seluruh ekstensi skrip yang dapat dieksekusi.
7. **Otorisasi Token Ter-hash**: Kode unik publik (`kode`) hanya untuk melihat ucapan. Hak mengedit dan menghapus ucapan memerlukan `token_edit` rahasia, di mana database hanya menyimpan hash SHA-256 dari token tersebut.
8. **Pembersihan File Fisik Terverifikasi**: Fungsi `safelyDeleteUpload()` memastikan penghapusan file hanya terjadi di dalam direktori `uploads/` yang sah sehingga mencegah serangan *directory traversal*.

---

## 🌐 Panduan Deployment ke Web Hosting (cPanel / DirectAdmin)

1. **Upload File**:
   - Kompres seluruh isi folder proyek menjadi file `.zip`.
   - Masuk ke cPanel File Manager dan ekstrak ke folder `public_html` (atau subdomain).
2. **Import Database**:
   - Buat database baru di cPanel MySQL Databases (misal: `u123_lovepage`).
   - Buka phpMyAdmin di cPanel dan import file `database/db_lovepage.sql`.
3. **Atur Koneksi Database**:
   - Buka file `config/koneksi.php` atau atur environment variable:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'nama_database_anda');
     define('DB_USER', 'username_database_anda');
     define('DB_PASS', 'password_database_anda');
     ```
4. **Izin Folder Uploads**:
   - Pastikan folder `uploads/foto/` dan `uploads/musik/` memiliki permission `755` atau `775` agar server web dapat menyimpan file upload.

---

## 👥 Data Contoh & Token Pengujian

| Kode Publik | Jenis | Tema | Mini-Game | Token Edit Rahasia | Link Edit |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `romantis26` | Pasangan / Anniversary | Romantic Pastel | Catch the Heart | `demo12345` | `edit.php?kode=romantis26&token=demo12345` |
| `ultah21` | Ulang Tahun | Birthday Celebration | Love Quiz | `demo67890` | `edit.php?kode=ultah21&token=demo67890` |

---

&copy; LovePage — Platform Website Ucapan Digital Interaktif. Dibuat dengan cinta untuk setiap momen berharga.
