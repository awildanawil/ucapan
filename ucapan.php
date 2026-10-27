<?php
/**
 * LovePage - Public Greeting Website Renderer (ucapan.php)
 * Memuat ucapan dari database berdasarkan kode unik publik
 */

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$kode = trim($_GET['kode'] ?? '');
$ucapan = null;
$fotos = [];

if (!empty($kode)) {
    try {
        $db = getDB();
        // Ambil data ucapan (TIDAK menyertakan token_edit demi keamanan)
        $stmt = $db->prepare("SELECT id, kode, judul, nama_penerima, nama_pengirim, isi_ucapan, jenis_ucapan, tema, warna, game, game_data, musik, dibuat_pada 
            FROM ucapan 
            WHERE kode = ? AND status = 'published' 
            LIMIT 1");
        $stmt->execute([$kode]);
        $ucapan = $stmt->fetch();

        if ($ucapan) {
            // Ambil daftar foto
            $stmtPhotos = $db->prepare("SELECT id, nama_file, caption, urutan 
                FROM foto_ucapan 
                WHERE ucapan_id = ? 
                ORDER BY urutan ASC, id ASC");
            $stmtPhotos->execute([$ucapan['id']]);
            $fotos = $stmtPhotos->fetchAll();
        }
    } catch (Exception $e) {
        error_log("[LovePage View Error]: " . $e->getMessage());
        $ucapan = null;
    }
}

// Bila ucapan tidak ditemukan, tampilkan halaman 404 estetik
if (!$ucapan) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ucapan Tidak Ditemukan — LovePage</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <style>
            .notfound-wrapper {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 2rem;
            }
            .notfound-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(16px);
                border: 1px solid var(--surface-border);
                border-radius: var(--radius-md);
                padding: 3.5rem 2rem;
                max-width: 520px;
                box-shadow: var(--shadow-lg);
            }
            .notfound-icon {
                font-size: 4.5rem;
                color: #ff6b8b;
                margin-bottom: 1.2rem;
                animation: heartBeat 2s infinite;
            }
        </style>
    </head>
    <body>
        <div class="notfound-wrapper">
            <div class="notfound-card">
                <div class="notfound-icon"><i class="fa-regular fa-heart-crack"></i></div>
                <h1 style="font-size: 1.8rem; margin-bottom: 0.8rem;">Ucapan Tidak Ditemukan</h1>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
                    Maaf, link ucapan digital yang kamu tuju tidak dapat ditemukan. Mungkin tautan salah ketik atau telah dihapus oleh pengirimnya.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fa-solid fa-house"></i> Ke Beranda
                    </a>
                    <a href="editor.php" class="btn btn-primary">
                        <i class="fa-solid fa-heart"></i> Buat Ucapan Baru
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Meta Data & Konfigurasi Tema
$themes = getAllowedThemes();
$themeKey = $ucapan['tema'];
$themeClass = 'theme-' . $themeKey;
$themeAccent = !empty($ucapan['warna']) ? $ucapan['warna'] : ($themes[$themeKey]['default_color'] ?? '#ff6b8b');

$occasions = getAllowedOccasions();
$occasionTitle = $occasions[$ucapan['jenis_ucapan']] ?? 'Momen Spesial';

// Path Musik (Mendukung YouTube ID atau File Audio Lokal)
$musicUrl = null;
$youtubeId = null;

if (!empty($ucapan['musik'])) {
    $rawMusic = $ucapan['musik'];
    if (str_starts_with($rawMusic, 'yt:')) {
        $youtubeId = substr($rawMusic, 3);
    } else {
        $cleanMusic = str_replace('preset:', '', $rawMusic);
        if (file_exists(__DIR__ . '/uploads/musik/' . $cleanMusic)) {
            $musicUrl = 'uploads/musik/' . rawurlencode($cleanMusic);
        } elseif (file_exists(__DIR__ . '/assets/audio/' . $cleanMusic)) {
            $musicUrl = 'assets/audio/' . rawurlencode($cleanMusic);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($ucapan['judul']); ?> — LovePage</title>
    <meta name="description" content="Surat ucapan digital spesial untuk <?php echo e($ucapan['nama_penerima']); ?> dari <?php echo e($ucapan['nama_pengirim']); ?>.">

    <!-- Open Graph for WhatsApp & Social Media Preview -->
    <meta property="og:title" content="<?php echo e($ucapan['judul']); ?>">
    <meta property="og:description" content="Ada surat ucapan spesial untuk <?php echo e($ucapan['nama_penerima']); ?> dari <?php echo e($ucapan['nama_pengirim']); ?>. Buka sekarang!">
    <meta property="og:type" content="website">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/ucapan.css">

    <style>
        :root {
            --theme-accent: <?php echo e($themeAccent); ?>;
        }
    </style>
</head>
<body class="greeting-page <?php echo e($themeClass); ?>">

    <!-- Config Holder untuk JS -->
    <div id="greetingConfig" 
         data-theme="<?php echo e($themeKey); ?>" 
         data-accent="<?php echo e($themeAccent); ?>" 
         data-game="<?php echo e($ucapan['game']); ?>" 
         data-quiz="<?php echo htmlspecialchars($ucapan['game_data'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
         data-youtube-id="<?php echo e($youtubeId ?? ''); ?>"
         style="display: none;"></div>

    <!-- Canvas Partikel Ambien -->
    <canvas id="particlesCanvas"></canvas>

    <!-- Background Music Audio Element / Player -->
    <?php if ($musicUrl): ?>
        <audio id="bgAudio" loop preload="auto">
            <source src="<?php echo e($musicUrl); ?>">
        </audio>
    <?php endif; ?>

    <?php if ($musicUrl || $youtubeId): ?>
        <!-- Floating Music Player Button -->
        <button type="button" class="music-floating-btn" id="musicToggleBtn" title="Putar Musik">
            <i class="fa-solid fa-music"></i>
        </button>

        <?php if ($youtubeId): ?>
            <!-- Floating YouTube Audio Source Badge -->
            <div class="music-source-pill" id="musicSourcePill">
                <i class="fa-brands fa-youtube yt-icon"></i> 
                <span>Lagu via YouTube</span>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ============================================================
         OPENING EXPERIENCE OVERLAY
         ============================================================ -->
    <div class="opening-overlay" id="openingOverlay">
        
        <?php if ($ucapan['game'] === 'kisi-kisi'): ?>
            <!-- MINI-GAME: KISI-KISI KATA RAHASIA -->
            <div class="game-stage-container kisi-stage-container">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-puzzle-piece" style="color: <?php echo e($themeAccent); ?>;"></i> Teka-Teki Kisi-Kisi Kata</div>
                    <div class="game-score-badge" id="kisiProgressBadge">Kata <span id="kisiWordIndex">1</span>/<span id="kisiWordTotal">1</span></div>
                </div>
                <div class="kisi-play-area" id="kisiPlayArea">
                    <!-- Kisi-kisi letter slots, clues, and interactive keyboard rendered by JS -->
                </div>
            </div>

        <?php elseif ($ucapan['game'] === 'catch-heart'): ?>
            <!-- MINI-GAME: CATCH THE HEART -->
            <div class="game-stage-container catch-stage-container">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-hand-holding-heart" style="color: <?php echo e($themeAccent); ?>;"></i> Catch the Heart</div>
                    <div class="game-score-badge">Tertangkap: <span id="catchScore">0 / 5</span></div>
                </div>
                <div class="game-play-area" id="catchPlayArea">
                    <!-- Heart elements spawned by JS -->
                </div>
            </div>

        <?php elseif ($ucapan['game'] === 'find-heart'): ?>
            <!-- MINI-GAME: FIND THE HEART -->
            <div class="game-stage-container find-stage-container" style="height: auto; min-height: 480px;">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-magnifying-glass" style="color: <?php echo e($themeAccent); ?>;"></i> Find the Heart</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">Cari 1 hati tersembunyi!</div>
                </div>
                <div class="find-heart-grid" id="findHeartGrid">
                    <!-- Grid items created by JS -->
                </div>
            </div>

        <?php elseif ($ucapan['game'] === 'love-quiz'): ?>
            <!-- MINI-GAME: LOVE QUIZ -->
            <div class="game-stage-container quiz-stage-container" style="height: auto; min-height: 450px;">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-circle-question" style="color: <?php echo e($themeAccent); ?>;"></i> Love Quiz Spesial</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">Jawab dengan jujur ya!</div>
                </div>
                <div class="quiz-box" id="quizBox">
                    <!-- Quiz rendered by JS -->
                </div>
            </div>

        <?php else: ?>
            <!-- DIRECT OPENING: LUXURY ENVELOPE CARD -->
            <div class="envelope-box">
                <div class="envelope-interactive-card" id="btnOpenEnvelope">
                    <div class="wax-seal-badge">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div class="envelope-flap"></div>
                    <div class="envelope-letter-peek">
                        <div class="peek-line"></div>
                        <div class="peek-line short"></div>
                    </div>
                </div>
                <h2 class="envelope-title">Surat Spesial Untukmu 💌</h2>
                <p class="envelope-subtitle">
                    Dari <strong><?php echo e($ucapan['nama_pengirim']); ?></strong> untuk <strong><?php echo e($ucapan['nama_penerima']); ?></strong>
                </p>
                <button type="button" class="btn btn-primary btn-lg btn-open-magic" id="btnOpenEnvelopeDirect" onclick="unlockAndRevealGreeting()">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Buka Surat Sekarang
                </button>
            </div>
        <?php endif; ?>

    </div>

    <!-- ============================================================
         MAIN GREETING CONTENT
         ============================================================ -->
    <main class="greeting-main-container">
        
        <article class="greeting-card">
            
            <header class="greeting-header">
                <div class="occasion-badge">
                    <i class="fa-solid fa-sparkles"></i> <?php echo e($occasionTitle); ?>
                </div>
                <h1 class="greeting-title"><?php echo e($ucapan['judul']); ?></h1>
                <div class="meta-info">
                    Untuk <span class="meta-recipient"><?php echo e($ucapan['nama_penerima']); ?></span>
                    &bull; Dari <span><?php echo e($ucapan['nama_pengirim']); ?></span>
                </div>
            </header>

            <!-- Greeting Text Message -->
            <div class="message-wrapper">
                <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                <div class="greeting-text">
                    <?php echo nl2br(e($ucapan['isi_ucapan'])); ?>
                </div>
                <div class="greeting-signoff">
                    — Dengan penuh cinta, <?php echo e($ucapan['nama_pengirim']); ?>
                </div>
            </div>

            <!-- Photo Gallery Section (Jika ada foto) -->
            <?php if (!empty($fotos)): ?>
                <section class="gallery-section">
                    <div class="gallery-title">
                        <i class="fa-solid fa-camera-retro" style="color: var(--theme-accent);"></i> Galeri Kenangan Indah
                    </div>
                    <div class="gallery-grid">
                        <?php foreach ($fotos as $foto): ?>
                            <?php $photoPath = 'uploads/foto/' . rawurlencode($foto['nama_file']); ?>
                            <div class="photo-card">
                                <img src="<?php echo e($photoPath); ?>" alt="<?php echo e($foto['caption'] ?: 'Foto Kenangan'); ?>" class="photo-img" loading="lazy">
                                <?php if (!empty($foto['caption'])): ?>
                                    <div class="photo-caption"><?php echo e($foto['caption']); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </article>

        <!-- Greeting Actions & Viral Sharing -->
        <div class="greeting-actions">
            <div class="share-buttons-row">
                <?php 
                    $currentUrl = getBaseUrl() . '/ucapan.php?kode=' . urlencode($ucapan['kode']);
                    $waShareText = urlencode("Hai! Aku punya website ucapan spesial untukmu: " . $currentUrl);
                ?>
                <a href="https://api.whatsapp.com/send?text=<?php echo $waShareText; ?>" target="_blank" class="btn btn-secondary btn-sm" style="color: #25D366;">
                    <i class="fa-brands fa-whatsapp"></i> Bagikan ke WhatsApp
                </a>
                <button type="button" class="btn btn-secondary btn-sm" id="btnCopyGreetingLink">
                    <i class="fa-regular fa-copy"></i> Salin Link
                </button>
                <button type="button" class="btn btn-secondary btn-sm" id="btnShareWeb">
                    <i class="fa-solid fa-share-nodes"></i> Bagikan
                </button>
            </div>

            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.6rem;">Tersentuh dan ingin membuat kejutan serupa?</p>
                <a href="editor.php" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-heart-circle-plus"></i> Bikin Website Ucapanmu Sendiri (Gratis)
                </a>
            </div>

            <!-- Footer Signature: Web by Awiell -->
            <div style="margin-top: 2.2rem; text-align: center;">
                <div class="awiell-badge">
                    <span class="heart-icon"><i class="fa-solid fa-heart"></i></span>
                    <span>Web by <span class="author-name">Awiell</span></span>
                </div>
            </div>
        </div>

    </main>

    <!-- Photo Lightbox Modal -->
    <div class="lightbox-modal" id="photoLightbox" aria-hidden="true">
        <button class="lightbox-close" id="btnLightboxClose">&times;</button>
        <div class="lightbox-content">
            <img src="" alt="Perbesar Foto" class="lightbox-img" id="lightboxImg">
            <div class="lightbox-caption" id="lightboxCaption"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ucapan.js"></script>
</body>
</html>
