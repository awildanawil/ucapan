<?php
/**
 * LovePage - Secret Edit Page (edit.php)
 * Memperbarui ucapan menggunakan token rahasia tanpa akun
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$kode  = trim($_GET['kode'] ?? $_POST['kode'] ?? '');
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');

$db = getDB();
$errorMessage = null;
$successMessage = null;

// 1. Verifikasi Keberadaan Kode & Token
if (empty($kode) || empty($token)) {
    http_response_code(403);
    die(renderAccessDenied("Parameter kode atau token rahasia tidak ditemukan. Anda memerlukan link edit rahasia lengkap untuk mengakses halaman ini."));
}

// 2. Ambil Record dari Database & Cocokkan Hash Token
$stmt = $db->prepare("SELECT * FROM ucapan WHERE kode = ? AND status != 'deleted' LIMIT 1");
$stmt->execute([$kode]);
$ucapan = $stmt->fetch();

if (!$ucapan || !verifyToken($token, $ucapan['token_edit'])) {
    http_response_code(403);
    die(renderAccessDenied("Token rahasia tidak cocok atau tidak valid. Anda tidak memiliki izin untuk mengedit ucapan ini."));
}

// Ambil Foto-Foto Saat Ini
$stmtPhotos = $db->prepare("SELECT * FROM foto_ucapan WHERE ucapan_id = ? ORDER BY urutan ASC, id ASC");
$stmtPhotos->execute([$ucapan['id']]);
$fotos = $stmtPhotos->fetchAll();

// 3. Tangani Form Submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrf)) {
        $errorMessage = "Sesi keamanan kedaluwarsa. Silakan refresh dan coba lagi.";
    } else {
        $judul        = sanitizeInput($_POST['judul'] ?? '', 150);
        $namaPenerima = sanitizeInput($_POST['nama_penerima'] ?? '', 100);
        $namaPengirim = sanitizeInput($_POST['nama_pengirim'] ?? '', 100);
        $isiUcapan    = sanitizeInput($_POST['isi_ucapan'] ?? '', 10000);
        $jenisUcapan  = sanitizeInput($_POST['jenis_ucapan'] ?? 'romantis', 50);
        $tema         = sanitizeInput($_POST['tema'] ?? 'romantic-pastel', 50);
        $warna        = sanitizeInput($_POST['warna'] ?? '#ff6b8b', 20);
        $game         = sanitizeInput($_POST['game'] ?? 'none', 50);

        if (empty($judul) || empty($namaPenerima) || empty($namaPengirim) || empty($isiUcapan)) {
            $errorMessage = "Semua kolom bertanda bintang wajib diisi.";
        } else {
            try {
                $db->beginTransaction();

                // Musik (YouTube, Upload, atau Preset)
                $finalMusic = $ucapan['musik'];
                $ytInput = trim($_POST['musik_youtube'] ?? '');
                $presetMusik = $_POST['musik_preset'] ?? '';

                if (!empty($ytInput)) {
                    $ytId = extractYouTubeId($ytInput);
                    if ($ytId) {
                        if ($finalMusic && str_starts_with($finalMusic, 'music_')) {
                            safelyDeleteUpload('musik', $finalMusic);
                        }
                        $finalMusic = 'yt:' . $ytId;
                    }
                } elseif ($presetMusik === 'none') {
                    // Hapus musik
                    if ($finalMusic && str_starts_with($finalMusic, 'music_')) {
                        safelyDeleteUpload('musik', $finalMusic);
                    }
                    $finalMusic = null;
                } elseif (str_starts_with($presetMusik, 'yt:')) {
                    $ytId = extractYouTubeId(substr($presetMusik, 3));
                    if ($ytId) {
                        if ($finalMusic && str_starts_with($finalMusic, 'music_')) {
                            safelyDeleteUpload('musik', $finalMusic);
                        }
                        $finalMusic = 'yt:' . $ytId;
                    }
                } elseif (str_starts_with($presetMusik, 'preset:')) {
                    $finalMusic = str_replace('preset:', '', $presetMusik);
                } elseif (isset($_FILES['musik_file']) && $_FILES['musik_file']['error'] === UPLOAD_ERR_OK) {
                    $audioVal = validateUploadedAudio($_FILES['musik_file']);
                    if ($audioVal['valid']) {
                        if ($finalMusic && str_starts_with($finalMusic, 'music_')) {
                            safelyDeleteUpload('musik', $finalMusic);
                        }
                        $finalMusic = 'music_' . bin2hex(random_bytes(16)) . '.' . $audioVal['ext'];
                        move_uploaded_file($_FILES['musik_file']['tmp_name'], __DIR__ . '/uploads/musik/' . $finalMusic);
                    }
                }

                // Game data
                $finalGameData = $ucapan['game_data'];
                if (isset($_POST['game_data']) && !empty($_POST['game_data'])) {
                    $decoded = json_decode($_POST['game_data'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $finalGameData = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                    }
                }

                // Update Tabel Ucapan
                $stmtUpdate = $db->prepare("UPDATE ucapan SET 
                    judul = ?, nama_penerima = ?, nama_pengirim = ?, isi_ucapan = ?, 
                    jenis_ucapan = ?, tema = ?, warna = ?, game = ?, game_data = ?, musik = ?, diperbarui_pada = NOW() 
                    WHERE id = ?");
                $stmtUpdate->execute([
                    $judul, $namaPenerima, $namaPengirim, $isiUcapan,
                    $jenisUcapan, $tema, $warna, $game, $finalGameData, $finalMusic,
                    $ucapan['id']
                ]);

                // Update Caption Foto yang Ada
                if (isset($_POST['caption_existing']) && is_array($_POST['caption_existing'])) {
                    $stmtUpdateCap = $db->prepare("UPDATE foto_ucapan SET caption = ? WHERE id = ? AND ucapan_id = ?");
                    foreach ($_POST['caption_existing'] as $fotoId => $capText) {
                        $stmtUpdateCap->execute([sanitizeInput($capText, 255), (int)$fotoId, $ucapan['id']]);
                    }
                }

                // Hapus Foto yang Ditandai Hapus
                if (isset($_POST['delete_photos']) && is_array($_POST['delete_photos'])) {
                    $stmtGetPhoto = $db->prepare("SELECT nama_file FROM foto_ucapan WHERE id = ? AND ucapan_id = ?");
                    $stmtDelPhoto = $db->prepare("DELETE FROM foto_ucapan WHERE id = ? AND ucapan_id = ?");

                    foreach ($_POST['delete_photos'] as $delId) {
                        $stmtGetPhoto->execute([(int)$delId, $ucapan['id']]);
                        $fRow = $stmtGetPhoto->fetch();
                        if ($fRow) {
                            safelyDeleteUpload('foto', $fRow['nama_file']);
                            $stmtDelPhoto->execute([(int)$delId, $ucapan['id']]);
                        }
                    }
                }

                // Tambah Foto Baru (jika ada)
                if (isset($_FILES['new_photos']) && is_array($_FILES['new_photos']['name'])) {
                    $stmtInsertFoto = $db->prepare("INSERT INTO foto_ucapan (ucapan_id, nama_file, caption, urutan) VALUES (?, ?, ?, ?)");
                    $countFiles = count($_FILES['new_photos']['name']);

                    for ($i = 0; $i < $countFiles; $i++) {
                        if ($_FILES['new_photos']['error'][$i] === UPLOAD_ERR_OK) {
                            $singleFile = [
                                'name'     => $_FILES['new_photos']['name'][$i],
                                'type'     => $_FILES['new_photos']['type'][$i],
                                'tmp_name' => $_FILES['new_photos']['tmp_name'][$i],
                                'error'    => $_FILES['new_photos']['error'][$i],
                                'size'     => $_FILES['new_photos']['size'][$i]
                            ];
                            $imgVal = validateUploadedImage($singleFile);
                            if ($imgVal['valid']) {
                                $newPhotoName = 'photo_' . bin2hex(random_bytes(16)) . '.' . $imgVal['ext'];
                                if (move_uploaded_file($singleFile['tmp_name'], __DIR__ . '/uploads/foto/' . $newPhotoName)) {
                                    $stmtInsertFoto->execute([$ucapan['id'], $newPhotoName, '', 99]);
                                }
                            }
                        }
                    }
                }

                $db->commit();
                $successMessage = "Perubahan ucapan berhasil disimpan!";

                // Refresh data
                $stmt->execute([$kode]);
                $ucapan = $stmt->fetch();
                $stmtPhotos->execute([$ucapan['id']]);
                $fotos = $stmtPhotos->fetchAll();

            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                $errorMessage = "Gagal memperbarui: " . $e->getMessage();
            }
        }
    }
}

$themes = getAllowedThemes();
$occasions = getAllowedOccasions();
$games = getAllowedGames();
$tokenCsrf = csrfToken();

function renderAccessDenied($reason) {
    return <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8"><title>Akses Ditolak — LovePage</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div style="background: white; border-radius: 20px; padding: 3rem 2rem; max-width: 500px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div style="font-size: 4rem; color: #e63946; margin-bottom: 1rem;"><i class="fa-solid fa-shield-halved"></i></div>
            <h2 style="margin-bottom: 0.8rem;">Akses Edit Ditolak</h2>
            <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.8rem;">{$reason}</p>
            <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-house"></i> Kembali ke Beranda</a>
        </div>
    </body>
    </html>
HTML;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ucapan: <?php echo e($ucapan['judul']); ?> — LovePage</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/editor.css">
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="brand-logo">
                <div class="logo-icon"><i class="fa-solid fa-heart"></i></div>
                <span>Love<span class="highlight">Page</span></span>
            </a>

            <div class="nav-actions" style="display: flex; gap: 0.6rem;">
                <a href="ucapan.php?kode=<?php echo urlencode($kode); ?>" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-eye"></i> Lihat Halaman Publik
                </a>
            </div>
        </div>
    </nav>

    <div class="container" style="max-width: 820px; padding: 2.5rem 1.5rem 4rem;">
        
        <div style="margin-bottom: 2rem;">
            <div style="display: inline-block; padding: 4px 12px; background: #e0f2fe; color: #0284c7; border-radius: 999px; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-lock-open"></i> Akses Terotorisasi Token Rahasia
            </div>
            <h1 style="font-size: 2rem; margin-bottom: 0.3rem;">Edit Ucapan: <?php echo e($ucapan['judul']); ?></h1>
            <p style="color: var(--text-secondary); font-size: 0.95rem;">
                Perbarui pesan, tema tampilan, foto kenangan, atau lagu latar ucapan ini.
            </p>
        </div>

        <?php if ($successMessage): ?>
            <div style="background: #e6fcf5; border: 1px solid #b2f2bb; color: #0ca678; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> <?php echo e($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div style="background: #fff5f5; border: 1px solid #ffc9c9; color: #e03131; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem; font-weight: 600;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo e($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Form Edit -->
        <div class="wizard-card" style="margin-bottom: 2.5rem;">
            <form action="edit.php?kode=<?php echo urlencode($kode); ?>&token=<?php echo urlencode($token); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo e($tokenCsrf); ?>">
                <input type="hidden" name="kode" value="<?php echo e($kode); ?>">
                <input type="hidden" name="token" value="<?php echo e($token); ?>">

                <!-- Bagian 1: Teks -->
                <h3 style="font-size: 1.25rem; margin-bottom: 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 0.6rem;">
                    <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i> 1. Informasi & Pesan
                </h3>

                <div class="form-group">
                    <label class="form-label">Judul Ucapan <span class="required">*</span></label>
                    <input type="text" name="judul" class="form-control" value="<?php echo e($ucapan['judul']); ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Nama Penerima <span class="required">*</span></label>
                        <input type="text" name="nama_penerima" class="form-control" value="<?php echo e($ucapan['nama_penerima']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Pengirim <span class="required">*</span></label>
                        <input type="text" name="nama_pengirim" class="form-control" value="<?php echo e($ucapan['nama_pengirim']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Momen</label>
                    <select name="jenis_ucapan" class="form-control">
                        <?php foreach ($occasions as $oKey => $oVal): ?>
                            <option value="<?php echo e($oKey); ?>" <?php echo $ucapan['jenis_ucapan'] === $oKey ? 'selected' : ''; ?>>
                                <?php echo e($oVal); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Isi Pesan Ucapan <span class="required">*</span></label>
                    <textarea name="isi_ucapan" class="form-control" style="min-height: 150px;" required><?php echo e($ucapan['isi_ucapan']); ?></textarea>
                </div>

                <!-- Bagian 2: Tampilan & Game -->
                <h3 style="font-size: 1.25rem; margin: 2rem 0 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 0.6rem;">
                    <i class="fa-solid fa-palette" style="color: var(--primary);"></i> 2. Tema & Mini-Game
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Tema Desain</label>
                        <select name="tema" class="form-control">
                            <?php foreach ($themes as $tKey => $tVal): ?>
                                <option value="<?php echo e($tKey); ?>" <?php echo $ucapan['tema'] === $tKey ? 'selected' : ''; ?>>
                                    <?php echo e($tVal['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warna Aksen</label>
                        <div style="display: flex; align-items: center; gap: 0.8rem; margin-top: 5px;">
                            <input type="color" name="warna" class="color-custom-input" value="<?php echo e($ucapan['warna']); ?>">
                            <span style="font-size: 0.9rem; font-weight: 600;"><?php echo e($ucapan['warna']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mini-Game Pembuka</label>
                    <select name="game" id="editGameSelect" class="form-control">
                        <?php foreach ($games as $gKey => $gVal): ?>
                            <option value="<?php echo e($gKey); ?>" <?php echo $ucapan['game'] === $gKey ? 'selected' : ''; ?>>
                                <?php echo e($gVal['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Game Data Box for Kisi-Kisi / Quiz -->
                <div class="form-group" id="editGameDataBox" style="background: #fff8fa; border: 1.5px dashed var(--primary-light); padding: 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    <label class="form-label" style="font-size: 0.92rem; color: var(--primary);"><i class="fa-solid fa-puzzle-piece"></i> Pengaturan Kata / Soal Game (JSON)</label>
                    <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                        Untuk game <strong>Kisi-Kisi Kata</strong> atau <strong>Love Quiz</strong>, sesuaikan kata rahasia dan petunjuknya:
                    </p>
                    <textarea name="game_data" class="form-control" style="font-family: monospace; font-size: 0.85rem; min-height: 90px;"><?php echo e($ucapan['game_data'] ?: '{"type":"kisi-kisi","words":[{"word":"SAYANG","clue":"Panggilan paling tulus dari lubuk hatiku 💕"}]}'); ?></textarea>
                </div>

                <!-- Bagian 3: Foto Kenangan -->
                <h3 style="font-size: 1.25rem; margin: 2rem 0 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 0.6rem;">
                    <i class="fa-solid fa-images" style="color: var(--primary);"></i> 3. Kelola Foto
                </h3>

                <?php if (!empty($fotos)): ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                        <?php foreach ($fotos as $f): ?>
                            <div style="display: flex; align-items: center; gap: 1rem; background: #fff8fa; border: 1px solid var(--surface-border); border-radius: 12px; padding: 0.8rem;">
                                <img src="uploads/foto/<?php echo e($f['nama_file']); ?>" alt="Foto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <div style="flex-grow: 1;">
                                    <input type="text" name="caption_existing[<?php echo $f['id']; ?>]" value="<?php echo e($f['caption']); ?>" placeholder="Caption foto..." class="form-control" style="padding: 0.45rem 0.8rem; font-size: 0.88rem;">
                                </div>
                                <label style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #e63946; cursor: pointer; white-space: nowrap;">
                                    <input type="checkbox" name="delete_photos[]" value="<?php echo $f['id']; ?>"> Hapus Foto
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1rem;">Belum ada foto terunggah.</p>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Tambah Foto Baru (Bisa pilih beberapa)</label>
                    <input type="file" name="new_photos[]" multiple accept="image/jpeg,image/png,image/webp" class="form-control">
                </div>

                <!-- Bagian 4: Musik Latar -->
                <h3 style="font-size: 1.25rem; margin: 2rem 0 1.2rem; border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 0.6rem;">
                    <i class="fa-solid fa-music" style="color: var(--primary);"></i> 4. Musik Latar
                </h3>

                <div class="form-group" style="background: #fff8fa; border: 1.5px solid var(--primary-light); padding: 1.2rem; border-radius: 12px; margin-bottom: 1.2rem;">
                    <label class="form-label" style="color: #ff0000; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-brands fa-youtube" style="font-size: 1.2rem;"></i> Ambil Lagu dari YouTube (Praktis & Bebas Pilih Lagu Apapun)
                    </label>
                    <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 0.6rem;">
                        Masukkan link video YouTube baru jika ingin mengganti musik:
                    </p>
                    <input type="text" name="musik_youtube" class="form-control" placeholder="Contoh: https://youtu.be/... atau https://www.youtube.com/watch?v=..." value="<?php echo str_starts_with($ucapan['musik'] ?? '', 'yt:') ? 'https://youtu.be/' . substr($ucapan['musik'], 3) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Musik Preset Lokal Saat Ini: <strong><?php echo e($ucapan['musik'] ?: 'Tidak ada'); ?></strong></label>
                    <select name="musik_preset" class="form-control">
                        <option value="<?php echo e($ucapan['musik'] ? 'preset:' . $ucapan['musik'] : 'none'); ?>">Tetap gunakan musik saat ini</option>
                        <option value="preset:romantic-music-box.wav">Preset: Romantic Music Box</option>
                        <option value="preset:birthday-melody.wav">Preset: Birthday Melody</option>
                        <option value="preset:calm-friendship.wav">Preset: Calm Friendship</option>
                        <option value="yt:w0tQ7g3cK2g">YouTube: Penjaga Hati (Nadhif Basalamah)</option>
                        <option value="yt:rtOvBOTyX00">YouTube: A Thousand Years (Christina Perri)</option>
                        <option value="yt:b6GdgkG2HwU">YouTube: Happy Birthday Piano Lofi</option>
                        <option value="yt:6k8cpUkKK4c">YouTube: Count On Me (Bruno Mars)</option>
                        <option value="none">Hapus Musik (Tanpa Musik)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Atau Unggah File Audio Baru (MP3 / WAV)</label>
                    <input type="file" name="musik_file" accept="audio/mpeg,audio/mp3,audio/wav,audio/x-m4a,audio/ogg" class="form-control">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2.5rem; justify-content: flex-end;">
                    <a href="ucapan.php?kode=<?php echo urlencode($kode); ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

        <!-- Zona Bahaya: Hapus Ucapan -->
        <div style="background: #fff5f5; border: 1.5px solid #ffc9c9; border-radius: var(--radius-md); padding: 2rem; margin-top: 3rem;">
            <h3 style="color: #e03131; font-size: 1.25rem; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Zona Bahaya: Hapus Ucapan
            </h3>
            <p style="font-size: 0.92rem; color: #495057; line-height: 1.6; margin-bottom: 1.5rem;">
                Tindakan ini akan <strong>menghapus website ucapan ini secara permanen</strong> beserta seluruh foto dan file musik dari server. Tindakan ini tidak dapat dibatalkan.
            </p>
            <form action="hapus.php" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda benar-benar yakin ingin menghapus ucapan ini selamanya? Semua foto dan file akan dihapus.');">
                <input type="hidden" name="csrf_token" value="<?php echo e($tokenCsrf); ?>">
                <input type="hidden" name="kode" value="<?php echo e($kode); ?>">
                <input type="hidden" name="token" value="<?php echo e($token); ?>">
                <button type="submit" class="btn" style="background: #e03131; color: white;">
                    <i class="fa-solid fa-trash-can"></i> Hapus Ucapan Ini Selamanya
                </button>
            </form>
        </div>

        <!-- Footer Signature: Web by Awiell -->
        <div style="margin-top: 3.5rem; text-align: center;">
            <div class="awiell-badge">
                <span class="heart-icon"><i class="fa-solid fa-heart"></i></span>
                <span>Web by <span class="author-name">Awiell</span></span>
            </div>
            <div style="margin-top: 8px; font-size: 0.8rem; color: var(--text-muted);">
                LovePage — Dibuat dengan cinta untuk momen berharga
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>
