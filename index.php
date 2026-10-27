<?php
/**
 * LovePage - Landing Page
 * Platform Pembuat Website Ucapan Digital Tanpa Login
 */
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LovePage — Bikin Website Ucapan Digitalmu Sendiri</title>
    <meta name="description" content="Platform mudah dan estetik untuk membuat website ucapan digital romantis, ulang tahun, anniversary, dan persahabatan. Gratis tanpa perlu registrasi!">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Floating Background Orbs -->
    <div class="bg-decorations" aria-hidden="true">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="floating-orb orb-3"></div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="brand-logo" id="brandLogo">
                <div class="logo-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <span>Love<span class="highlight">Page</span></span>
            </a>

            <ul class="nav-links">
                <li><a href="#fitur" class="nav-link"><i class="fa-solid fa-sparkles"></i> Fitur</a></li>
                <li><a href="#cara-kerja" class="nav-link"><i class="fa-solid fa-wand-magic-sparkles"></i> Cara Kerja</a></li>
                <li><a href="#contoh" class="nav-link"><i class="fa-solid fa-eye"></i> Contoh Ucapan</a></li>
            </ul>

            <div class="nav-actions">
                <a href="editor.php" class="btn btn-primary btn-sm" id="navCtaBtn">
                    <i class="fa-solid fa-plus"></i> Mulai Buat
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <div class="hero-badge">
                <i class="fa-solid fa-fire"></i> Tanpa Perlu Daftar / Login — 100% Gratis & Cepat
            </div>
            
            <h1 class="hero-title">
                Bikin Website Ucapan <br>
                <span class="gradient-text">Digitalmu Sendiri</span>
            </h1>

            <p class="hero-subtitle">
                Ungkapkan perasaan terdalam untuk pasangan, sahabat, keluarga, atau rekan terkasih dalam bentuk website interaktif dengan foto kenangan, lagu latar favorit, dan mini-game seru!
            </p>

            <div class="hero-cta">
                <a href="editor.php" class="btn btn-primary btn-lg" id="heroPrimaryCta">
                    <i class="fa-solid fa-heart-circle-bolt"></i> Mulai Buat Ucapan
                </a>
                <a href="#contoh" class="btn btn-secondary btn-lg" id="heroSecondaryCta">
                    <i class="fa-regular fa-compass"></i> Lihat Contoh
                </a>
            </div>

            <!-- Preview Card Showcase Header -->
            <div id="contoh" style="scroll-margin-top: 100px;"></div>
            <div class="section-header" style="margin-bottom: 2rem;">
                <span class="section-subtitle">Inspirasi Ucapan</span>
                <h2 class="section-title">Berbagai Momen Indah, Satu Website Khusus</h2>
                <p style="color: var(--text-secondary);">Setiap ucapan mendapatkan link unik yang dapat langsung kamu kirimkan lewat WhatsApp, DM Instagram, atau media sosial lainnya.</p>
            </div>

            <div class="showcase-grid">
                <!-- Card 1: Romantis / Pasangan -->
                <div class="showcase-card">
                    <div class="showcase-preview" style="background-image: url('https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=600&q=80');">
                        <div class="showcase-overlay">
                            <span class="showcase-tag"><i class="fa-solid fa-heart" style="color: #ff6b8b;"></i> Pasangan & Anniversary</span>
                        </div>
                    </div>
                    <div class="showcase-body">
                        <h3 class="showcase-title">Happy 2nd Anniversary, Sayang! 💖</h3>
                        <div class="showcase-meta">
                            <i class="fa-regular fa-clock"></i> Untuk Adinda Cantika
                        </div>
                        <p class="showcase-desc">Dilengkapi lagu instrumental romantis, galeri foto kencan pertama, dan game Catch the Heart!</p>
                        <button class="btn btn-secondary btn-sm" data-demo-target="anniversary" id="btnDemoAnniversary">
                            <i class="fa-solid fa-eye"></i> Intip Contoh Ini
                        </button>
                    </div>
                </div>

                <!-- Card 2: Ulang Tahun -->
                <div class="showcase-card">
                    <div class="showcase-preview" style="background-image: url('https://images.unsplash.com/photo-1513151233558-d860c5398176?auto=format&fit=crop&w=600&q=80');">
                        <div class="showcase-overlay">
                            <span class="showcase-tag"><i class="fa-solid fa-cake-candles" style="color: #ff9f1c;"></i> Ulang Tahun</span>
                        </div>
                    </div>
                    <div class="showcase-body">
                        <h3 class="showcase-title">Selamat Ulang Tahun ke-21! 🎂✨</h3>
                        <div class="showcase-meta">
                            <i class="fa-regular fa-clock"></i> Untuk Nabila Az-Zahra
                        </div>
                        <p class="showcase-desc">Nuansa pesta ceria dengan taburan confetti, musik piano lofi, dan tebak-tebakan Love Quiz lucu.</p>
                        <button class="btn btn-secondary btn-sm" data-demo-target="birthday" id="btnDemoBirthday">
                            <i class="fa-solid fa-eye"></i> Intip Contoh Ini
                        </button>
                    </div>
                </div>

                <!-- Card 3: Persahabatan -->
                <div class="showcase-card">
                    <div class="showcase-preview" style="background-image: url('https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=600&q=80');">
                        <div class="showcase-overlay">
                            <span class="showcase-tag"><i class="fa-solid fa-user-group" style="color: #48cae4;"></i> Persahabatan</span>
                        </div>
                    </div>
                    <div class="showcase-body">
                        <h3 class="showcase-title">Cheers to 5 Years of Friendship! 🌸</h3>
                        <div class="showcase-meta">
                            <i class="fa-regular fa-clock"></i> Untuk Jessica & Dimas
                        </div>
                        <p class="showcase-desc">Kenangan konyol bersama sahabat dengan musik akustik hangat dan game Find the Heart.</p>
                        <button class="btn btn-secondary btn-sm" data-demo-target="bestie" id="btnDemoBestie">
                            <i class="fa-solid fa-eye"></i> Intip Contoh Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="section" id="fitur">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Fitur Unggulan</span>
                <h2 class="section-title">Sentuhan Ajaib untuk Setiap Momen Berharga</h2>
                <p style="color: var(--text-secondary);">LovePage dirancang dengan penuh kehangatan agar penerima ucapanmu merasa benar-benar spesial dan tersentuh.</p>
            </div>

            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <h3>Galeri Foto Penuh Kenangan</h3>
                    <p>Unggah beberapa foto terbaikmu, atur urutan tampilan, dan sematkan caption manis di setiap fotonya.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <h3>Musik Latar Favorit</h3>
                    <p>Putar lagu kenangan kalian saat website dibuka. Dilengkapi kontrol play/pause yang elegan.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <h3>5 Pilihan Tema Estetik</h3>
                    <p>Mulai dari Romantic Pastel, Dark Romance, Birthday, Friendship, hingga Minimalist yang modern.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    <h3>Mini-Game Interaktif</h3>
                    <p>Kejutan mini game pembuka seperti Catch the Heart atau Love Quiz sebelum membaca surat ucapan.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <h3>Edit Tanpa Perlu Akun</h3>
                    <p>Simpan link rahasia tokenmu untuk mengedit teks atau mengganti foto kapan pun kamu inginkan.</p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <h3>Responsif di Semua Perangkat</h3>
                    <p>Tampilan yang dirancang secara mobile-first, sangat mulus diakses di smartphone iPhone & Android.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 Steps Section -->
    <section class="section" id="cara-kerja" style="background: rgba(255, 255, 255, 0.45);">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Mudah & Cepat</span>
                <h2 class="section-title">Hanya 3 Langkah untuk Menciptakan Keajaiban</h2>
                <p style="color: var(--text-secondary);">Tidak perlu keahlian coding atau desain. Semuanya siap pakai dalam hitungan menit.</p>
            </div>

            <div class="steps-container">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div style="font-size: 2.2rem; color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <h3>1. Tulis Pesan & Pilih Tema</h3>
                    <p>Ketikkan pesan tulusmu, nama penerima, dan pilih tema warna visual yang paling pas untuk suasana hati.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <div style="font-size: 2.2rem; color: var(--secondary); margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <h3>2. Tambah Foto & Mini-Game</h3>
                    <p>Unggah foto-foto kenangan, pilih lagu favorit, dan aktifkan tantangan mini game seru jika mau.</p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <div style="font-size: 2.2rem; color: var(--accent); margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <h3>3. Publikasikan & Bagikan Link</h3>
                    <p>Dapatkan link website ucapan pribadimu serta link rahasia pengeditan. Langsung kirimkan via WhatsApp!</p>
                </div>
            </div>

            <!-- Call to action banner -->
            <div class="cta-banner">
                <h2>Siap Memberikan Kejutan Manis Hari Ini?</h2>
                <p>Mulailah merangkai kata dan kenangan. Bikin seseorang tersenyum bahagia dengan kejutan digital yang tak terlupakan.</p>
                <a href="editor.php" class="btn btn-white btn-lg" id="bottomCtaBtn">
                    <i class="fa-solid fa-heart"></i> Buat Ucapan Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="brand-logo">
                        <div class="logo-icon">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <span>Love<span class="highlight">Page</span></span>
                    </div>
                    <p>Platform kreatif untuk menciptakan website ucapan digital interaktif, penuh cinta, dan mudah dibagikan kepada orang-orang terkasih.</p>
                </div>

                <div class="footer-col">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="editor.php">Mulai Buat Ucapan</a></li>
                        <li><a href="#fitur">Fitur Unggulan</a></li>
                        <li><a href="#cara-kerja">Panduan Cara Kerja</a></li>
                        <li><a href="#contoh">Lihat Contoh</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Keamanan & Privasi</h4>
                    <ul class="footer-links">
                        <li><a href="#" onclick="alert('LovePage menghargai privasi Anda. Setiap ucapan hanya dapat diakses melalui link unik dan hanya dapat diedit/dihapus menggunakan token rahasia milik Anda.'); return false;">Kebijakan Privasi</a></li>
                        <li><a href="#" onclick="alert('Jika Anda menemukan konten yang melanggar norma atau hak cipta, laporkan kepada administrator sistem.'); return false;">Laporkan Konten</a></li>
                        <li><a href="#" onclick="alert('LovePage dibuat dengan Native PHP 8+, MySQL PDO, HTML5, Vanilla CSS3 & Modern JavaScript.'); return false;">Teknologi Stack</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                <div class="awiell-badge">
                    <span class="heart-icon"><i class="fa-solid fa-heart"></i></span>
                    <span>Web by <span class="author-name">Awiell</span></span>
                </div>
                <p>&copy; <?php echo date('Y'); ?> LovePage. Dibuat dengan cinta untuk momen-momen berharga.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Demo Ucapan -->
    <div class="modal-overlay" id="demoModal" aria-hidden="true">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-wand-magic-sparkles" style="color: var(--primary);"></i> Pratinjau Contoh Ucapan</h3>
                <button class="modal-close" aria-label="Tutup Modal" id="btnModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <img id="demoImage" src="" alt="Pratinjau Foto" style="width: 100%; height: 180px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem;">
                
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.8rem; flex-wrap: wrap;">
                    <span id="demoThemeBadge" style="background: #fff0f3; color: var(--primary); font-size: 0.78rem; font-weight: 700; padding: 4px 10px; border-radius: 999px;"></span>
                    <span id="demoGameBadge" style="background: #eef2ff; color: #4338ca; font-size: 0.78rem; font-weight: 700; padding: 4px 10px; border-radius: 999px;"></span>
                </div>

                <h4 id="demoTitle" style="font-size: 1.25rem; margin-bottom: 0.3rem;"></h4>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                    <span id="demoRecipient"></span> &bull; <span id="demoSender"></span>
                </div>

                <div style="background: #faf6f8; padding: 1rem; border-radius: 12px; border-left: 3px solid var(--primary); margin-bottom: 1.5rem;">
                    <p id="demoMessage" style="font-size: 0.92rem; font-style: italic; color: var(--text-secondary); line-height: 1.6;"></p>
                </div>

                <div style="display: flex; gap: 0.8rem;">
                    <a href="editor.php" class="btn btn-primary" style="flex: 1;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Buat Seperti Ini
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
