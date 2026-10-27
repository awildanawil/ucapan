<?php
/**
 * LovePage - Safe Greeting Deletion (hapus.php)
 * Menghapus ucapan, membersihkan foto & musik fisik, dengan otorisasi token ketat
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$kode  = trim($_POST['kode'] ?? $_GET['kode'] ?? '');
$token = trim($_POST['token'] ?? $_GET['token'] ?? '');

$db = getDB();

// 1. Validasi Token & Kode
if (empty($kode) || empty($token)) {
    http_response_code(403);
    die("Akses ditolak: Parameter tidak lengkap.");
}

$stmt = $db->prepare("SELECT * FROM ucapan WHERE kode = ? LIMIT 1");
$stmt->execute([$kode]);
$ucapan = $stmt->fetch();

if (!$ucapan || !verifyToken($token, $ucapan['token_edit'])) {
    http_response_code(403);
    die("Akses ditolak: Token rahasia tidak cocok.");
}

// 2. Jika request POST, verifikasi CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        http_response_code(403);
        die("Sesi keamanan kedaluwarsa. Silakan coba lagi.");
    }
}

// 3. Ambil Semua Foto untuk Dihapus dari Disk
$stmtPhotos = $db->prepare("SELECT nama_file FROM foto_ucapan WHERE ucapan_id = ?");
$stmtPhotos->execute([$ucapan['id']]);
$photos = $stmtPhotos->fetchAll();

foreach ($photos as $p) {
    safelyDeleteUpload('foto', $p['nama_file']);
}

// 4. Hapus Musik Kustom (jika bukan preset bawaan)
if (!empty($ucapan['musik']) && str_starts_with($ucapan['musik'], 'music_')) {
    safelyDeleteUpload('musik', $ucapan['musik']);
}

// 5. Hapus Record dari Database (foto_ucapan otomatis terhapus via CASCADE)
$stmtDelete = $db->prepare("DELETE FROM ucapan WHERE id = ?");
$stmtDelete->execute([$ucapan['id']]);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ucapan Berhasil Dihapus — LovePage</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">

    <div style="background: white; border-radius: var(--radius-md); padding: 3.5rem 2.5rem; max-width: 520px; text-align: center; box-shadow: var(--shadow-lg); border: 1px solid var(--surface-border);">
        <div style="font-size: 4rem; color: #2ec4b6; margin-bottom: 1.2rem;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1 style="font-size: 1.8rem; margin-bottom: 0.8rem;">Ucapan Berhasil Dihapus</h1>
        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
            Website ucapan <strong>"<?php echo e($ucapan['judul']); ?>"</strong> dan seluruh file foto kenangan telah dihapus secara bersih dan aman dari server kami.
        </p>
        <a href="index.php" class="btn btn-primary">
            <i class="fa-solid fa-house"></i> Kembali ke Beranda LovePage
        </a>
    </div>

</body>
</html>
