<?php
/**
 * LovePage - Greeting Creator Editor Wizard
 * Pembuatan Website Ucapan Digital Interaktif (Multi-Step Wizard)
 */
require_once __DIR__ . '/includes/functions.php';

$tokenCsrf = csrfToken();
$themes = getAllowedThemes();
$occasions = getAllowedOccasions();
$games = getAllowedGames();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Ucapan Baru — LovePage</title>
    <meta name="description" content="Rangkai website ucapan digital interaktif pribadimu dengan foto, musik, dan mini game seru.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Outfit:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/editor.css">
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

            <div class="nav-actions">
                <a href="index.php" class="btn btn-secondary btn-sm" id="btnBackHome">
                    <i class="fa-solid fa-arrow-left"></i> Beranda
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Editor Section -->
    <main class="editor-wrapper">
        <div class="container">
            
            <header class="wizard-header">
                <h1 class="wizard-title">Rangkai Website Ucapan Spesialmu</h1>
                <p class="wizard-subtitle">Ikuti langkah mudah di bawah ini dan saksikan hasil sementaranya langsung di layar simulator ponsel!</p>

                <!-- Progress Indicator -->
                <div class="wizard-progress">
                    <div class="progress-line-fill" id="progressLineFill"></div>

                    <div class="progress-step active" data-step="1" id="stepIndicator1">
                        <div class="step-circle">1</div>
                        <span class="step-label">Informasi Dasar</span>
                    </div>

                    <div class="progress-step" data-step="2" id="stepIndicator2">
                        <div class="step-circle">2</div>
                        <span class="step-label">Kustomisasi Tema</span>
                    </div>

                    <div class="progress-step" data-step="3" id="stepIndicator3">
                        <div class="step-circle">3</div>
                        <span class="step-label">Media & Lagu</span>
                    </div>

                    <div class="progress-step" data-step="4" id="stepIndicator4">
                        <div class="step-circle">4</div>
                        <span class="step-label">Mini-Game</span>
                    </div>

                    <div class="progress-step" data-step="5" id="stepIndicator5">
                        <div class="step-circle">5</div>
                        <span class="step-label">Publikasi</span>
                    </div>
                </div>
            </header>

            <!-- Editor Split Grid: Left Form Wizard, Right Live Phone Simulator -->
            <div class="editor-grid">
                
                <!-- Left Column: Step Card -->
                <div class="wizard-card">
                    <form id="editorForm" onsubmit="return false;">
                        <input type="hidden" name="csrf_token" value="<?php echo e($tokenCsrf); ?>">
                        <input type="hidden" name="jenis_ucapan" id="inputJenisUcapan" value="romantis">
                        <input type="hidden" name="tema" id="inputTema" value="romantic-pastel">
                        <input type="hidden" name="warna" id="inputWarna" value="#ff6b8b">
                        <input type="hidden" name="game" id="inputGame" value="none">
                        <input type="hidden" name="musik_preset" id="inputMusikPreset" value="preset:romantic-music-box.wav">
                        <input type="hidden" name="musik_youtube" id="inputMusikYoutube" value="">
                        <input type="hidden" name="musik_tipe" id="inputMusikTipe" value="preset">

                        <!-- STEP 1: Basic Information -->
                        <div class="wizard-step-pane active" data-step="1">
                            <div class="step-pane-header">
                                <h3><i class="fa-solid fa-pen-nib" style="color: var(--primary);"></i> Langkah 1: Informasi Dasar</h3>
                                <p>Tentukan untuk siapa ucapan ini dan tuliskan pesan tulus dari lubuk hatimu.</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="inputJudul">Judul Ucapan <span class="required">*</span></label>
                                <input type="text" id="inputJudul" class="form-control" placeholder="Contoh: Happy 2nd Anniversary Sayang! 💖" maxlength="150" required>
                                <div class="form-hint">Judul utama yang akan muncul pertama kali saat website dibuka.</div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label" for="inputPenerima">Nama Penerima <span class="required">*</span></label>
                                    <input type="text" id="inputPenerima" class="form-control" placeholder="Contoh: Adinda Cantika" maxlength="100" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="inputPengirim">Nama Pengirim <span class="required">*</span></label>
                                    <input type="text" id="inputPengirim" class="form-control" placeholder="Contoh: Rian Pratama" maxlength="100" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jenis Momen Ucapan</label>
                                <div class="occasion-pills">
                                    <?php foreach ($occasions as $key => $label): ?>
                                        <button type="button" class="occasion-pill <?php echo $key === 'romantis' ? 'active' : ''; ?>" data-value="<?php echo e($key); ?>">
                                            <?php echo e($label); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="inputPesan">Isi Pesan Ucapan <span class="required">*</span></label>
                                <textarea id="inputPesan" class="form-control" placeholder="Tuliskan ungkapan perasaanmu secara mendalam di sini... (mendukung baris baru)" required></textarea>
                                <div class="form-hint">
                                    <span>Tuliskan kalimat terindahmu</span>
                                    <span id="pesanCharCount">0 karakter</span>
                                </div>
                            </div>

                            <div class="wizard-footer">
                                <div></div>
                                <button type="button" class="btn btn-primary btn-next-step" id="btnStep1Next">
                                    Lanjut ke Kustomisasi Tema <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2: Customize Appearance -->
                        <div class="wizard-step-pane" data-step="2">
                            <div class="step-pane-header">
                                <h3><i class="fa-solid fa-palette" style="color: var(--primary);"></i> Langkah 2: Kustomisasi Tampilan</h3>
                                <p>Pilih tema visual dan warna aksen yang mewakili perasaanmu.</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pilih Tema Desain</label>
                                <div class="themes-grid">
                                    <?php foreach ($themes as $tKey => $tData): ?>
                                        <div class="theme-card <?php echo $tKey === 'romantic-pastel' ? 'active' : ''; ?>" data-theme="<?php echo e($tKey); ?>" data-default-color="<?php echo e($tData['default_color']); ?>">
                                            <div class="theme-preview-box" style="background: <?php echo e($tData['bg_gradient']); ?>;">
                                                <i class="<?php echo e($tData['icon']); ?>"></i>
                                            </div>
                                            <h4><?php echo e($tData['name']); ?></h4>
                                            <p><?php echo e($tData['desc']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 1.8rem;">
                                <label class="form-label">Warna Aksen Khusus</label>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Pilih warna yang akan menjadi warna tombol, hati, dan ornamen dekorasi:</p>
                                <div class="color-picker-row">
                                    <div class="color-swatch active" style="background: #ff6b8b;" data-color="#ff6b8b" title="Rose Pink"></div>
                                    <div class="color-swatch" style="background: #e63946;" data-color="#e63946" title="Ruby Romance"></div>
                                    <div class="color-swatch" style="background: #ff9f1c;" data-color="#ff9f1c" title="Sunset Gold"></div>
                                    <div class="color-swatch" style="background: #a06cd5;" data-color="#a06cd5" title="Lavender Dream"></div>
                                    <div class="color-swatch" style="background: #48cae4;" data-color="#48cae4" title="Sky Friendship"></div>
                                    <div class="color-swatch" style="background: #2ec4b6;" data-color="#2ec4b6" title="Mint Fresh"></div>
                                    <div class="color-swatch" style="background: #b5838d;" data-color="#b5838d" title="Dusty Rose"></div>
                                    
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: 0.5rem;">
                                        <input type="color" id="inputWarnaCustom" class="color-custom-input" value="#ff6b8b" title="Pilih warna bebas">
                                        <span style="font-size: 0.85rem; color: var(--text-muted);">Pilih Sendiri</span>
                                    </div>
                                </div>
                            </div>

                            <div class="wizard-footer">
                                <button type="button" class="btn btn-secondary btn-prev-step">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                </button>
                                <button type="button" class="btn btn-primary btn-next-step" id="btnStep2Next">
                                    Lanjut ke Upload Media <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: Upload Media -->
                        <div class="wizard-step-pane" data-step="3">
                            <div class="step-pane-header">
                                <h3><i class="fa-solid fa-photo-film" style="color: var(--primary);"></i> Langkah 3: Foto & Musik Latar</h3>
                                <p>Abadikan momen terindah dengan galeri foto dan lagu pengiring yang menyentuh hati.</p>
                            </div>

                            <!-- Upload Foto -->
                            <div class="form-group">
                                <label class="form-label">Unggah Foto Kenangan (Maksimal 10 Foto)</label>
                                <div class="dropzone" id="photoDropzone">
                                    <input type="file" id="photoFileInput" multiple accept="image/jpeg,image/png,image/webp" style="display: none;">
                                    <div class="dropzone-icon">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <div class="dropzone-text">Tarik foto ke sini atau klik untuk memilih</div>
                                    <div class="dropzone-subtext">Mendukung format JPG, PNG, WebP (Maksimal 5 MB per foto)</div>
                                </div>

                                <!-- Photo Preview List with Captioning & Ordering -->
                                <div class="photo-preview-list" id="photoPreviewList">
                                    <!-- Diisi oleh JS -->
                                </div>
                            </div>

                            <!-- Background Music Selection -->
                            <div class="form-group" style="margin-top: 2rem;">
                                <label class="form-label"><i class="fa-solid fa-music" style="color: var(--primary);"></i> Pilih Musik Latar (Import MP3 / YouTube / Preset)</label>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.8rem;">Penerima ucapan akan mendengar alunan musik ini saat membuka suratmu:</p>
                                
                                <!-- Opsi 1: Import Lagu dari File MP3 (Paling Direkomendasikan untuk Lagu Pribadi) -->
                                <div class="music-import-box" id="musicImportBox">
                                    <div class="music-import-header">
                                        <div class="music-import-title">
                                            <i class="fa-solid fa-file-audio" style="color: #a06cd5; font-size: 1.25rem;"></i>
                                            <span>Unggah / Import File Lagu MP3 Sendiri</span>
                                        </div>
                                        <span class="music-badge-pill">MP3 / WAV Bebas</span>
                                    </div>
                                    <p class="music-import-desc">
                                        Pilih lagu romantis atau lagu favoritmu langsung dari memori HP atau komputer (format MP3, WAV, M4A, OGG, maks. 10 MB).
                                    </p>

                                    <!-- Hidden File Input -->
                                    <input type="file" id="musicFileInput" accept=".mp3,audio/mpeg,audio/mp3,audio/wav,audio/x-m4a,audio/ogg" style="display: none;">

                                    <!-- Dropzone Upload Area -->
                                    <div class="music-dropzone" id="musicDropzone">
                                        <div class="music-dropzone-inner">
                                            <div class="music-dropzone-icon">
                                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                            </div>
                                            <div class="music-dropzone-text">Klik untuk Memilih File MP3 dari Perangkat</div>
                                            <div class="music-dropzone-sub">atau seret dan lepas (drag & drop) file lagu ke sini</div>
                                        </div>
                                    </div>

                                    <!-- MP3 Audio Player Card (Tampil setelah file dipilih) -->
                                    <div id="customAudioPreviewCard" class="custom-audio-player-card" style="display: none;">
                                        <div class="custom-audio-info">
                                            <div class="custom-audio-disc" id="customAudioDisc">
                                                <i class="fa-solid fa-compact-disc"></i>
                                            </div>
                                            <div class="custom-audio-meta">
                                                <div class="custom-audio-filename" id="customAudioFileName">lagu-cinta.mp3</div>
                                                <div class="custom-audio-status" id="customAudioStatus">
                                                    <span class="status-ready"><i class="fa-solid fa-circle-check"></i> Siap Diputar</span> • <span id="customAudioFileSize">3.4 MB</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-audio-controls">
                                            <button type="button" class="btn-play-custom-audio" id="btnPlayCustomAudio" title="Putar / Jeda Preview">
                                                <i class="fa-solid fa-play"></i> Putar Lagu
                                            </button>
                                            <button type="button" class="btn-change-custom-audio" id="btnChangeCustomAudio" title="Ganti File MP3">
                                                <i class="fa-solid fa-arrows-rotate"></i> Ganti
                                            </button>
                                            <button type="button" class="btn-remove-custom-audio" id="btnRemoveCustomAudio" title="Hapus Lagu MP3">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Opsi 2: Ambil Lagu dari YouTube (Praktis & Simpel) -->
                                <div style="background: #fff8fa; border: 2px solid var(--primary-light); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.2rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.6rem;">
                                        <div style="font-size: 0.95rem; font-weight: 700; color: #ff0000; display: flex; align-items: center; gap: 6px;">
                                            <i class="fa-brands fa-youtube" style="font-size: 1.2rem;"></i> Atau Gunakan Lagu dari YouTube
                                        </div>
                                        <span style="font-size: 0.75rem; background: #ffe3e3; color: #e03131; padding: 2px 8px; border-radius: 999px; font-weight: 700;">Online</span>
                                    </div>
                                    <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 0.8rem;">
                                        Cukup tempel link video atau lagu favorit dari YouTube jika tidak ingin upload file:
                                    </p>
                                    
                                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.8rem;">
                                        <input type="text" id="inputYouTubeUrl" class="form-control" style="font-size: 0.88rem; padding: 0.6rem 0.9rem;" placeholder="Tempel link YouTube (contoh: https://youtu.be/... atau https://www.youtube.com/watch?v=...)">
                                        <button type="button" class="btn btn-primary btn-sm" id="btnApplyYouTube" style="white-space: nowrap; padding: 0.6rem 1.2rem;">
                                            <i class="fa-solid fa-check"></i> Pasang
                                        </button>
                                    </div>

                                    <!-- YouTube Quick Song Presets Chips -->
                                    <div style="margin-top: 0.6rem;">
                                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 0.4rem;">Pilihan Cepat Lagu Populer:</span>
                                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm yt-preset-chip" data-yt-id="w0tQ7g3cK2g" data-yt-title="Penjaga Hati - Nadhif Basalamah" style="font-size: 0.78rem; padding: 4px 10px;">
                                                💖 Penjaga Hati
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm yt-preset-chip" data-yt-id="rtOvBOTyX00" data-yt-title="A Thousand Years - Christina Perri" style="font-size: 0.78rem; padding: 4px 10px;">
                                                💖 A Thousand Years
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm yt-preset-chip" data-yt-id="b6GdgkG2HwU" data-yt-title="Happy Birthday Piano Lofi" style="font-size: 0.78rem; padding: 4px 10px;">
                                                🎂 Birthday Lofi
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm yt-preset-chip" data-yt-id="6k8cpUkKK4c" data-yt-title="Count On Me - Bruno Mars" style="font-size: 0.78rem; padding: 4px 10px;">
                                                🌸 Count On Me
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm yt-preset-chip" data-yt-id="5sVfW6W6x1c" data-yt-title="Sampai Jadi Debu - Banda Neira" style="font-size: 0.78rem; padding: 4px 10px;">
                                                ✨ Sampai Jadi Debu
                                            </button>
                                        </div>
                                    </div>

                                    <!-- YouTube Preview Display Card -->
                                    <div id="ytPreviewBox" style="display: none; align-items: center; gap: 12px; background: white; border: 1.5px solid var(--primary); border-radius: 10px; padding: 8px 12px; margin-top: 0.8rem;">
                                        <img id="ytPreviewThumb" src="" alt="Thumbnail" style="width: 55px; height: 40px; object-fit: cover; border-radius: 6px;">
                                        <div style="flex-grow: 1; min-width: 0;">
                                            <div id="ytPreviewTitle" style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                                            <div style="font-size: 0.75rem; color: #ff0000;"><i class="fa-brands fa-youtube"></i> Siap diputar via YouTube API</div>
                                        </div>
                                        <button type="button" id="btnRemoveYouTube" style="background: none; border: none; color: #e63946; cursor: pointer; font-size: 1rem;" title="Hapus Lagu YouTube">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Opsi 3: Preset Bawaan -->
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Atau Pilih Melodi Bawaan Sistem:</div>
                                <div class="music-options-list">
                                    <div class="music-card active" data-music="preset:romantic-music-box.wav">
                                        <div class="music-info">
                                            <div class="music-icon"><i class="fa-solid fa-heart"></i></div>
                                            <div>
                                                <div class="music-title">Romantic Music Box (Preset Lokal)</div>
                                                <div class="music-sub">Melodi kotak musik lembut & penuh rasa cinta</div>
                                            </div>
                                        </div>
                                        <button type="button" class="music-preview-btn" data-preview-src="assets/audio/romantic-music-box.wav">
                                            <i class="fa-solid fa-play"></i> Putar
                                        </button>
                                    </div>

                                    <div class="music-card" data-music="preset:birthday-melody.wav">
                                        <div class="music-info">
                                            <div class="music-icon" style="color: #ff9f1c; background: #fff5eb;"><i class="fa-solid fa-cake-candles"></i></div>
                                            <div>
                                                <div class="music-title">Birthday Melody (Preset Lokal)</div>
                                                <div class="music-sub">Alunan selamat ulang tahun ceria & hangat</div>
                                            </div>
                                        </div>
                                        <button type="button" class="music-preview-btn" data-preview-src="assets/audio/birthday-melody.wav">
                                            <i class="fa-solid fa-play"></i> Putar
                                        </button>
                                    </div>

                                    <div class="music-card" data-music="preset:calm-friendship.wav">
                                        <div class="music-info">
                                            <div class="music-icon" style="color: #48cae4; background: #ecfeff;"><i class="fa-solid fa-user-group"></i></div>
                                            <div>
                                                <div class="music-title">Calm Friendship (Preset Lokal)</div>
                                                <div class="music-sub">Petikan melodi akustik tenang penuh keakraban</div>
                                            </div>
                                        </div>
                                        <button type="button" class="music-preview-btn" data-preview-src="assets/audio/calm-friendship.wav">
                                            <i class="fa-solid fa-play"></i> Putar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="wizard-footer">
                                <button type="button" class="btn btn-secondary btn-prev-step">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                </button>
                                <button type="button" class="btn btn-primary btn-next-step" id="btnStep3Next">
                                    Lanjut ke Mini-Game <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 4: Opening Experience Mini-Game -->
                        <div class="wizard-step-pane" data-step="4">
                            <div class="step-pane-header">
                                <h3><i class="fa-solid fa-gamepad" style="color: var(--primary);"></i> Langkah 4: Pengalaman Pembuka Interaktif</h3>
                                <p>Beri kejutan seru sebelum surat ucapan terbuka. Kamu bisa mengubah kata-katanya sesuka hati dan mencobanya langsung!</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pilih Mini-Game Pembuka</label>
                                <div class="games-grid">
                                    <?php foreach ($games as $gKey => $gData): ?>
                                        <div class="game-card <?php echo $gKey === 'none' ? 'active' : ''; ?>" data-game="<?php echo e($gKey); ?>">
                                            <div class="game-card-icon">
                                                <i class="<?php echo e($gData['icon']); ?>"></i>
                                            </div>
                                            <h4><?php echo e($gData['title']); ?></h4>
                                            <p><?php echo e($gData['desc']); ?></p>

                                            <?php if ($gKey !== 'none'): ?>
                                                <button type="button" class="game-test-btn" data-test-game="<?php echo e($gKey); ?>">
                                                    <i class="fa-solid fa-play"></i> Coba Mainkan
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- KISI-KISI KATA CUSTOMIZER SECTION (Tampil jika game kisi-kisi dipilih) -->
                            <div id="kisiConfigBox" class="game-custom-box" style="display: none;">
                                <div class="game-custom-header">
                                    <div>
                                        <h4 class="game-custom-title">
                                            <i class="fa-solid fa-puzzle-piece" style="color: var(--primary);"></i> Pengaturan Kisi-Kisi Kata Rahasia
                                        </h4>
                                        <p class="game-custom-desc">
                                            Atur kata rahasia dan petunjuk kisi-kisinya sesuai keinginanmu. Penerima akan menebak kata-kata ini satu per satu sebelum surat terbuka!
                                        </p>
                                    </div>
                                    <span class="game-custom-badge"><i class="fa-solid fa-sparkles"></i> Bisa Diubah Bebas</span>
                                </div>

                                <!-- Preset Cepat Kisi-Kisi -->
                                <div class="kisi-presets-bar">
                                    <span class="kisi-presets-label">Pilihan Cepat Tema:</span>
                                    <button type="button" class="btn btn-secondary btn-sm btn-kisi-preset" data-kisi-preset="romantis">
                                        💖 Pasangan Romantis
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm btn-kisi-preset" data-kisi-preset="ultah">
                                        🎂 Ulang Tahun
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm btn-kisi-preset" data-kisi-preset="sahabat">
                                        🌸 Sahabat Sejati
                                    </button>
                                </div>

                                <!-- Dynamic List of Words -->
                                <div id="kisiWordsContainer" class="kisi-words-list">
                                    <!-- Rendered dynamically by editor.js -->
                                </div>

                                <div class="kisi-actions-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" id="btnAddKisiWord">
                                        <i class="fa-solid fa-plus"></i> Tambah Kata Rahasia Lainnya
                                    </button>
                                    <span class="kisi-limit-hint">(Maksimal 4 kata)</span>
                                </div>
                            </div>

                            <!-- LOVE QUIZ CUSTOMIZER SECTION (Tampil jika game love-quiz dipilih) -->
                            <div id="quizConfigBox" class="game-custom-box" style="display: none;">
                                <div class="game-custom-header">
                                    <div>
                                        <h4 class="game-custom-title">
                                            <i class="fa-solid fa-heart-circle-check" style="color: var(--primary);"></i> Pengaturan Kuis Manis (Love Quiz)
                                        </h4>
                                        <p class="game-custom-desc">
                                            Atur pertanyaan manis dan pilihan jawabannya. Pastikan jawaban yang benar telah dipilih agar ucapan terbuka!
                                        </p>
                                    </div>
                                    <span class="game-custom-badge"><i class="fa-solid fa-pen-nib"></i> Kustom Pertanyaan</span>
                                </div>

                                <div class="kisi-presets-bar">
                                    <span class="kisi-presets-label">Gunakan Template:</span>
                                    <button type="button" class="btn btn-secondary btn-sm btn-quiz-preset" data-quiz-preset="romantis">
                                        💖 Kuis Romantis
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm btn-quiz-preset" data-quiz-preset="ultah">
                                        🎂 Kuis Ulang Tahun
                                    </button>
                                </div>

                                <!-- Dynamic List of Quiz Questions -->
                                <div id="quizQuestionsContainer" class="quiz-questions-list">
                                    <!-- Rendered dynamically by editor.js -->
                                </div>

                                <div class="kisi-actions-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" id="btnAddQuizQuestion">
                                        <i class="fa-solid fa-plus"></i> Tambah Pertanyaan Kuis
                                    </button>
                                    <span class="kisi-limit-hint">(Maksimal 3 pertanyaan)</span>
                                </div>
                            </div>

                            <div class="wizard-footer">
                                <button type="button" class="btn btn-secondary btn-prev-step">
                                    <i class="fa-solid fa-arrow-left"></i> Kembali
                                </button>
                                <button type="button" class="btn btn-primary btn-next-step" id="btnStep4Next">
                                    Lanjut ke Pratinjau & Publikasi <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 5: Preview & Publish -->
                        <div class="wizard-step-pane" data-step="5">
                            <div class="step-pane-header">
                                <h3><i class="fa-solid fa-circle-check" style="color: #2ec4b6;"></i> Langkah 5: Tinjau & Publikasikan</h3>
                                <p>Periksa kembali rangkuman ucapanmu sebelum diterbitkan menjadi website publik.</p>
                            </div>

                            <!-- Ringkasan Info -->
                            <div style="background: white; border: 1px solid var(--surface-border); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.92rem;">
                                    <div>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Judul Ucapan</div>
                                        <div id="sumJudul" style="font-weight: 700; color: var(--text-primary); margin-top: 2px;"></div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Tema Visual</div>
                                        <div id="sumTema" style="font-weight: 700; color: var(--primary); margin-top: 2px;"></div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Penerima & Pengirim</div>
                                        <div style="color: var(--text-secondary); margin-top: 2px;">
                                            <span id="sumPenerima"></span> &bull; dari <span id="sumPengirim"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Mini-Game Pembuka</div>
                                        <div id="sumGame" style="color: var(--text-secondary); margin-top: 2px;"></div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Foto Terlampir</div>
                                        <div id="sumFotoCount" style="color: var(--text-secondary); margin-top: 2px;"></div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Musik Latar</div>
                                        <div id="sumMusik" style="color: var(--text-secondary); margin-top: 2px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div style="background: #e6fcf5; border: 1px solid #c3fae8; border-radius: 12px; padding: 1rem 1.25rem; font-size: 0.88rem; color: #0ca678; line-height: 1.5; margin-bottom: 1.5rem;">
                                <i class="fa-solid fa-shield-heart" style="margin-right: 6px;"></i> 
                                <strong>Tanpa Registrasi:</strong> Setelah dipublikasikan, kamu akan mendapatkan <strong>Link Publik</strong> untuk dibagikan dan <strong>Link Edit Rahasia</strong> untuk mengubah atau menghapus ucapan ini kapan saja.
                            </div>

                            <div class="wizard-footer">
                                <button type="button" class="btn btn-secondary btn-prev-step">
                                    <i class="fa-solid fa-arrow-left"></i> Edit Kembali
                                </button>
                                <button type="button" class="btn btn-primary btn-lg" id="btnPublishSubmit">
                                    <i class="fa-solid fa-paper-plane"></i> Publikasikan Sekarang
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Right Column: Sticky Live Smartphone Simulator -->
                <div class="preview-pane-sticky">
                    <div style="text-align: center; margin-bottom: 0.85rem; font-size: 0.88rem; font-weight: 700; color: var(--text-secondary);">
                        <i class="fa-solid fa-mobile-screen" style="color: var(--primary);"></i> Pratinjau Langsung (Simulator Ponsel)
                    </div>

                    <div class="phone-mockup">
                        <div class="phone-notch">
                            <div class="phone-camera"></div>
                        </div>

                        <div class="phone-screen" id="phoneScreen">
                            <div class="sim-header">
                                <div class="sim-badge" id="simBadge">Pasangan & Anniversary</div>
                                <h2 class="sim-title" id="simTitle">Happy 2nd Anniversary Sayang! 💖</h2>
                                <div class="sim-meta" id="simMeta">Untuk Adinda Cantika • Dari Rian Pratama</div>
                            </div>

                            <!-- Photos slider preview -->
                            <div class="sim-photos" style="display: none;">
                                <div class="sim-photos-slider" id="simPhotosSlider">
                                    <!-- Diisi JS -->
                                </div>
                            </div>

                            <!-- Message Card -->
                            <div class="sim-message-card">
                                <p class="sim-message-text" id="simMessage">
                                    Tulis pesan manismu di sini. Pesan ini akan tampil indah di layar ponsel penerima...
                                </p>
                            </div>

                            <!-- Music indicator preview -->
                            <div class="sim-music-pill" id="simMusicPill">
                                <i class="fa-solid fa-music"></i>
                                <span class="music-label">Romantic Melody</span>
                            </div>
                        </div>
                    </div>
                </div>

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
    </main>

    <!-- Mini-Game Tester Modal -->
    <div class="modal-overlay" id="gameTesterModal" aria-hidden="true">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="gameTesterTitle"><i class="fa-solid fa-gamepad"></i> Uji Coba Mini-Game</h3>
                <button class="modal-close" aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1rem;">
                <div id="gameTesterStage" style="position: relative; min-height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Publish Success Modal -->
    <div class="modal-overlay" id="publishSuccessModal" aria-hidden="true">
        <div class="modal-card" style="max-width: 580px;">
            <div class="publish-success-card">
                <div class="publish-icon">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h2 style="font-size: 1.6rem; margin-bottom: 0.4rem;">Website Ucapan Berhasil Diterbitkan! 🎉</h2>
                <p style="color: var(--text-secondary); font-size: 0.95rem;">
                    Halaman ucapan spesialmu telah aktif dan siap membuat seseorang tersenyum bahagia.
                </p>

                <!-- Link Publik -->
                <div class="publish-link-box">
                    <label><i class="fa-solid fa-globe"></i> 1. Link Website Ucapan (Bagikan ke Penerima):</label>
                    <div class="publish-link-input-group">
                        <input type="text" id="successPublicUrl" readonly>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyInputText('successPublicUrl', this)">
                            <i class="fa-regular fa-copy"></i> Salin
                        </button>
                    </div>
                </div>

                <!-- Link Edit Rahasia -->
                <div class="publish-link-box" style="border-color: #ffd8a8; background: #fff9db;">
                    <label style="color: #d9480f;"><i class="fa-solid fa-key"></i> 2. Link Edit Rahasia (HANYA UNTUK ANDA):</label>
                    <div class="publish-link-input-group">
                        <input type="text" id="successEditUrl" readonly style="background: white; border-color: #ffd8a8;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyInputText('successEditUrl', this)">
                            <i class="fa-regular fa-copy"></i> Salin
                        </button>
                    </div>
                </div>

                <div class="publish-warning">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 4px;"></i>
                    <strong>PENTING:</strong> Simpan <strong>Link Edit Rahasia</strong> Anda sekarang! Karena platform ini tidak menggunakan akun/password, link rahasia tersebut adalah <strong>satu-satunya cara</strong> untuk mengedit atau menghapus ucapan ini di kemudian hari.
                </div>

                <div style="display: flex; gap: 0.8rem; margin-top: 1.8rem; justify-content: center; flex-wrap: wrap;">
                    <a href="#" id="btnShareWhatsapp" target="_blank" class="btn btn-primary" style="background: #25D366; box-shadow: 0 8px 24px rgba(37, 211, 102, 0.35);">
                        <i class="fa-brands fa-whatsapp"></i> Bagikan ke WhatsApp
                    </a>
                    <a href="#" id="btnViewGreeting" target="_blank" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Website Ucapan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/editor.js"></script>
</body>
</html>
