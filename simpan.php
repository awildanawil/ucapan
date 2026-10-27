<?php
/**
 * LovePage - Backend Publishing Process (simpan.php)
 * Memproses penyimpanan ucapan, validasi ketat, upload foto/audio,
 * transaksi database, dan pembuatan token rahasia
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

// Pastikan request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Metode request tidak diizinkan.'], 405);
}

// 1. Verifikasi CSRF Token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    jsonResponse(['success' => false, 'message' => 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan refresh halaman.'], 403);
}

// 2. Proteksi Double-Submission / Idempotensi Ringan (minimal jeda 2 detik antar submission)
$lastSubmit = $_SESSION['last_publish_time'] ?? 0;
if (time() - $lastSubmit < 2) {
    jsonResponse(['success' => false, 'message' => 'Permintaan sedang diproses. Mohon tidak mengklik berulang-ulang.'], 429);
}
$_SESSION['last_publish_time'] = time();

// 3. Ambil dan Sanitasi Input Teks
$judul        = sanitizeInput($_POST['judul'] ?? '', 150);
$namaPenerima = sanitizeInput($_POST['nama_penerima'] ?? '', 100);
$namaPengirim = sanitizeInput($_POST['nama_pengirim'] ?? '', 100);
$isiUcapan    = sanitizeInput($_POST['isi_ucapan'] ?? '', 10000);
$jenisUcapan  = sanitizeInput($_POST['jenis_ucapan'] ?? 'romantis', 50);
$tema         = sanitizeInput($_POST['tema'] ?? 'romantic-pastel', 50);
$warna        = sanitizeInput($_POST['warna'] ?? '#ff6b8b', 20);
$game         = sanitizeInput($_POST['game'] ?? 'none', 50);
$gameData     = $_POST['game_data'] ?? null;

// Validasi Wajib
if (empty($judul) || empty($namaPenerima) || empty($namaPengirim) || empty($isiUcapan)) {
    jsonResponse(['success' => false, 'message' => 'Semua kolom bertanda bintang wajib diisi.'], 422);
}

// 4. Validasi Allowlist Server-Side
$allowedThemes = array_keys(getAllowedThemes());
if (!in_array($tema, $allowedThemes, true)) {
    $tema = 'romantic-pastel';
}

$allowedOccasions = array_keys(getAllowedOccasions());
if (!in_array($jenisUcapan, $allowedOccasions, true)) {
    $jenisUcapan = 'romantis';
}

$allowedGames = array_keys(getAllowedGames());
if (!in_array($game, $allowedGames, true)) {
    $game = 'none';
}

// Validasi format warna hex (#rrggbb)
if (!preg_match('/^#[a-fA-F0-9]{6}$/', $warna)) {
    $warna = '#ff6b8b';
}

// Validasi format JSON game_data jika ada
$sanitizedGameData = null;
if (!empty($gameData)) {
    $decoded = json_decode($gameData, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $sanitizedGameData = json_encode($decoded, JSON_UNESCAPED_UNICODE);
    }
}

// Array untuk mencatat file-file baru yang diunggah agar bisa dibersihkan jika rollback
$newlyUploadedFiles = [];

try {
    $db = getDB();

    // 5. Proses Musik Latar (YouTube Link, File Upload, atau Preset)
    $finalMusicFilename = null;
    $musicType = $_POST['musik_tipe'] ?? 'preset';

    if ($musicType === 'youtube' || !empty($_POST['musik_youtube'])) {
        $ytInput = $_POST['musik_youtube'] ?? $_POST['musik_preset'] ?? '';
        $ytId = extractYouTubeId($ytInput);
        if ($ytId) {
            $finalMusicFilename = 'yt:' . $ytId;
        } else {
            jsonResponse(['success' => false, 'message' => 'Format link YouTube tidak valid. Harap gunakan link video atau shorts YouTube yang benar.'], 422);
        }
    } elseif ($musicType === 'custom' && isset($_FILES['musik_file']) && $_FILES['musik_file']['error'] === UPLOAD_ERR_OK) {
        $audioValidation = validateUploadedAudio($_FILES['musik_file']);
        if (!$audioValidation['valid']) {
            jsonResponse(['success' => false, 'message' => $audioValidation['message']], 422);
        }

        $audioExt = $audioValidation['ext'];
        $finalMusicFilename = 'music_' . bin2hex(random_bytes(16)) . '.' . $audioExt;
        $audioDest = __DIR__ . '/uploads/musik/' . $finalMusicFilename;

        if (!move_uploaded_file($_FILES['musik_file']['tmp_name'], $audioDest)) {
            jsonResponse(['success' => false, 'message' => 'Gagal memindahkan file audio yang diunggah.'], 500);
        }

        $newlyUploadedFiles[] = $audioDest;
    } elseif ($musicType === 'preset') {
        $presetVal = $_POST['musik_preset'] ?? '';
        if (str_starts_with($presetVal, 'yt:')) {
            $ytId = extractYouTubeId(substr($presetVal, 3));
            if ($ytId) {
                $finalMusicFilename = 'yt:' . $ytId;
            }
        } else {
            $presetFile = str_replace('preset:', '', $presetVal);
            $allowedPresets = ['romantic-music-box.wav', 'birthday-melody.wav', 'calm-friendship.wav'];
            if (in_array($presetFile, $allowedPresets, true)) {
                $finalMusicFilename = $presetFile;
            }
        }
    }

    // 6. Generate Unique Public Code & Secret Edit Token
    $kode = null;
    $maxAttempts = 10;
    for ($i = 0; $i < $maxAttempts; $i++) {
        $candidateCode = generateRandomCode(8);
        $checkStmt = $db->prepare("SELECT id FROM ucapan WHERE kode = ? LIMIT 1");
        $checkStmt->execute([$candidateCode]);
        if (!$checkStmt->fetch()) {
            $kode = $candidateCode;
            break;
        }
    }

    if (!$kode) {
        throw new Exception("Gagal menghasilkan kode unik ucapan setelah beberapa percobaan.");
    }

    $plainToken = generateSecretToken(); // 32 hex chars
    $tokenHash = hashToken($plainToken);

    // 7. Mulai Transaksi Database
    $db->beginTransaction();

    $stmtInsert = $db->prepare("INSERT INTO ucapan 
        (kode, token_edit, judul, nama_penerima, nama_pengirim, isi_ucapan, jenis_ucapan, tema, warna, game, game_data, musik, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published')");

    $stmtInsert->execute([
        $kode,
        $tokenHash,
        $judul,
        $namaPenerima,
        $namaPengirim,
        $isiUcapan,
        $jenisUcapan,
        $tema,
        $warna,
        $game,
        $sanitizedGameData,
        $finalMusicFilename
    ]);

    $ucapanId = (int)$db->lastInsertId();

    // 8. Proses Upload Foto & Metadata
    $totalFoto = isset($_POST['total_foto']) ? (int)$_POST['total_foto'] : 0;
    $stmtPhoto = $db->prepare("INSERT INTO foto_ucapan (ucapan_id, nama_file, caption, urutan) VALUES (?, ?, ?, ?)");

    for ($i = 0; $i < $totalFoto; $i++) {
        $fileKey = "foto_{$i}";
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $validation = validateUploadedImage($_FILES[$fileKey]);
            if (!$validation['valid']) {
                throw new Exception("Foto ke-" . ($i + 1) . ": " . $validation['message']);
            }

            $imgExt = $validation['ext'];
            $randomFilename = 'photo_' . bin2hex(random_bytes(16)) . '.' . $imgExt;
            $destination = __DIR__ . '/uploads/foto/' . $randomFilename;

            if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $destination)) {
                throw new Exception("Gagal menyimpan foto ke-" . ($i + 1) . " ke server.");
            }

            $newlyUploadedFiles[] = $destination;

            $caption = sanitizeInput($_POST["caption_{$i}"] ?? '', 255);
            $urutan = isset($_POST["urutan_{$i}"]) ? (int)$_POST["urutan_{$i}"] : ($i + 1);

            $stmtPhoto->execute([$ucapanId, $randomFilename, $caption, $urutan]);
        }
    }

    // 9. Commit Transaksi
    $db->commit();

    // 10. Generate URL Lengkap
    $baseUrl = getBaseUrl();
    $urlPublic = $baseUrl . '/ucapan.php?kode=' . urlencode($kode);
    $urlEdit   = $baseUrl . '/edit.php?kode=' . urlencode($kode) . '&token=' . urlencode($plainToken);

    // Kirim Respon Berhasil
    jsonResponse([
        'success'    => true,
        'message'    => 'Ucapan berhasil dipublikasikan!',
        'kode'       => $kode,
        'token_edit' => $plainToken, // Dikirim sekali saja saat pertama kali dibuat
        'url_public' => $urlPublic,
        'url_edit'   => $urlEdit
    ]);

} catch (Exception $e) {
    // Rollback jika terjadi kegagalan
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    // Bersihkan file fisik yang sudah sempat diunggah
    foreach ($newlyUploadedFiles as $filePath) {
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    error_log("[LovePage Publish Error]: " . $e->getMessage());

    jsonResponse([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()
    ], 500);
}
