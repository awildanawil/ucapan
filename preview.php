<?php
/**
 * LovePage - Interactive Fullscreen Preview (preview.php)
 * Memungkinkan pembuat ucapan melihat pratinjau interaktif secara penuh
 */

require_once __DIR__ . '/includes/functions.php';

// Ambil data pratinjau dari query atau default demo
$judul        = sanitizeInput($_GET['judul'] ?? 'Happy Anniversary Sayang! 💖', 150);
$namaPenerima = sanitizeInput($_GET['penerima'] ?? 'Adinda Cantika', 100);
$namaPengirim = sanitizeInput($_GET['pengirim'] ?? 'Rian Pratama', 100);
$isiUcapan    = sanitizeInput($_GET['pesan'] ?? "Terima kasih sudah menjadi bagian terindah dalam hidupku.\nSemoga cinta kita selalu mekar dan abadi selamanya.", 5000);
$tema         = sanitizeInput($_GET['tema'] ?? 'romantic-pastel', 50);
$warna        = sanitizeInput($_GET['warna'] ?? '#ff6b8b', 20);
$game         = sanitizeInput($_GET['game'] ?? 'none', 50);

$themes = getAllowedThemes();
$themeClass = 'theme-' . $tema;
$themeAccent = $warna;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Pratinjau] <?php echo e($judul); ?> — LovePage</title>

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
        .preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(45, 24, 34, 0.95);
            color: white;
            padding: 8px 16px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body class="greeting-page <?php echo e($themeClass); ?>" style="padding-top: 50px;">

    <!-- Banner Mode Pratinjau -->
    <div class="preview-banner">
        <div>
            <i class="fa-solid fa-wand-magic-sparkles" style="color: #ff6b8b;"></i> 
            <strong>Mode Pratinjau Interaktif</strong> — Belum dipublikasikan
        </div>
        <button onclick="window.close()" class="btn btn-secondary btn-sm" style="padding: 3px 10px; font-size: 0.78rem;">
            Tutup Pratinjau
        </button>
    </div>

    <!-- Config Holder untuk JS -->
    <div id="greetingConfig" 
         data-theme="<?php echo e($tema); ?>" 
         data-accent="<?php echo e($themeAccent); ?>" 
         data-game="<?php echo e($game); ?>" 
         data-quiz="<?php echo htmlspecialchars($_GET['game_data'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
         style="display: none;"></div>

    <canvas id="particlesCanvas"></canvas>

    <!-- Opening Experience Overlay -->
    <div class="opening-overlay" id="openingOverlay">
        <?php if ($game === 'kisi-kisi'): ?>
            <div class="game-stage-container kisi-stage-container">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-puzzle-piece" style="color: <?php echo e($themeAccent); ?>;"></i> Teka-Teki Kisi-Kisi Kata</div>
                    <div class="game-score-badge" id="kisiProgressBadge">Kata <span id="kisiWordIndex">1</span>/<span id="kisiWordTotal">1</span></div>
                </div>
                <div class="kisi-play-area" id="kisiPlayArea"></div>
            </div>
        <?php elseif ($game === 'catch-heart'): ?>
            <div class="game-stage-container catch-stage-container">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-hand-holding-heart" style="color: <?php echo e($themeAccent); ?>;"></i> Catch the Heart</div>
                    <div class="game-score-badge">Tertangkap: <span id="catchScore">0 / 5</span></div>
                </div>
                <div class="game-play-area" id="catchPlayArea"></div>
            </div>
        <?php elseif ($game === 'find-heart'): ?>
            <div class="game-stage-container find-stage-container" style="height: auto; min-height: 480px;">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-magnifying-glass" style="color: <?php echo e($themeAccent); ?>;"></i> Find the Heart</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">Cari 1 hati tersembunyi!</div>
                </div>
                <div class="find-heart-grid" id="findHeartGrid"></div>
            </div>
        <?php elseif ($game === 'love-quiz'): ?>
            <div class="game-stage-container quiz-stage-container" style="height: auto; min-height: 450px;">
                <div class="game-header">
                    <div class="game-title"><i class="fa-solid fa-circle-question" style="color: <?php echo e($themeAccent); ?>;"></i> Love Quiz Spesial</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted);">Jawab dengan jujur ya!</div>
                </div>
                <div class="quiz-box" id="quizBox"></div>
            </div>
        <?php else: ?>
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
                    Dari <strong><?php echo e($namaPengirim); ?></strong> untuk <strong><?php echo e($namaPenerima); ?></strong>
                </p>
                <button type="button" class="btn btn-primary btn-lg btn-open-magic" id="btnOpenEnvelopeDirect" onclick="unlockAndRevealGreeting()">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Buka Surat Sekarang
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <main class="greeting-main-container">
        <article class="greeting-card">
            <header class="greeting-header">
                <div class="occasion-badge">
                    <i class="fa-solid fa-sparkles"></i> Mode Pratinjau
                </div>
                <h1 class="greeting-title"><?php echo e($judul); ?></h1>
                <div class="meta-info">
                    Untuk <span class="meta-recipient"><?php echo e($namaPenerima); ?></span>
                    &bull; Dari <span><?php echo e($namaPengirim); ?></span>
                </div>
            </header>

            <div class="message-wrapper">
                <div class="quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                <div class="greeting-text">
                    <?php echo nl2br(e($isiUcapan)); ?>
                </div>
                <div class="greeting-signoff">
                    — Dengan penuh cinta, <?php echo e($namaPengirim); ?>
                </div>
            </div>
        </article>

        <!-- Footer Signature: Web by Awiell -->
        <div style="margin-top: 2rem; text-align: center;">
            <div class="awiell-badge">
                <span class="heart-icon"><i class="fa-solid fa-heart"></i></span>
                <span>Web by <span class="author-name">Awiell</span></span>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/ucapan.js"></script>
</body>
</html>
