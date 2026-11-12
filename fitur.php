<?php
require_once __DIR__ . '/config/database.php';

$settings = get_settings($pdo);
$expanded_features = $pdo->query("SELECT * FROM expanded_features ORDER BY category_code ASC, sort_order ASC, id ASC")->fetchAll();

// Group features by category
$categories = [
    'transaksi' => [
        'name' => 'Transaksi & Otomasi',
        'desc' => 'Pemrosesan pesanan 24 jam nonstop terhubung langsung ke API multi-supplier.',
        'items' => []
    ],
    'pembayaran' => [
        'name' => 'Pembayaran',
        'desc' => 'Menerima pembayaran QRIS instan, Virtual Account, dan gerai retail tanpa rekonsiliasi manual.',
        'items' => []
    ],
    'supplier' => [
        'name' => 'Produk & Supplier',
        'desc' => 'Katalog game terlengkap dengan failover multi-supplier dan markup keuntungan otomatis.',
        'items' => []
    ],
    'marketing' => [
        'name' => 'Toko & Marketing',
        'desc' => 'Branding mandiri dengan domain sendiri, kupon promosi, dan banner event turnamen.',
        'items' => []
    ],
    'reseller' => [
        'name' => 'Member & Reseller',
        'desc' => 'Tingkatan harga berjenjang untuk reseller VIP dan jalur koneksi Host-to-Host (H2H).',
        'items' => []
    ],
    'laporan' => [
        'name' => 'Laporan & Analitik',
        'desc' => 'Pantau omset harian, mutasi saldo, kalkulasi laba bersih, dan ekspor data ke Excel.',
        'items' => []
    ],
];

foreach ($expanded_features as $ef) {
    $code = $ef['category_code'];
    if (!isset($categories[$code])) {
        $categories[$code] = [
            'name' => $ef['category_name'],
            'desc' => 'Spesifikasi fitur pendukung operasional toko online.',
            'items' => []
        ];
    }
    $categories[$code]['items'][] = $ef;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Katalog Fitur Lengkap: <?= htmlspecialchars($settings['site_title'] ?? 'UMKM Digital') ?></title>
  <meta name="description" content="Spesifikasi teknis lengkap pembuatan website top-up game otomatis, multi-supplier, dan payment gateway.">
  <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($settings['site_favicon'] ?? 'assets/images/logo.svg') ?>">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">

  <style>
    :root {
      --primary-color: <?= htmlspecialchars($settings['primary_color'] ?? '#2563EB') ?>;
      --accent-color: <?= htmlspecialchars($settings['accent_color'] ?? '#0284C7') ?>;
      --bg-dark: <?= htmlspecialchars($settings['bg_dark'] ?? '#090D16') ?>;
      --bg-card: <?= htmlspecialchars($settings['bg_card'] ?? '#121724') ?>;
      --border-color: <?= htmlspecialchars($settings['border_color'] ?? '#222B3D') ?>;
      --text-primary: <?= htmlspecialchars($settings['text_primary'] ?? '#F8FAFC') ?>;
      --text-secondary: <?= htmlspecialchars($settings['text_secondary'] ?? '#94A3B8') ?>;
    }

    .page-hero {
      padding: 130px 0 50px;
      text-align: center;
      background: radial-gradient(circle at 50% 20%, rgba(37, 99, 235, 0.08), transparent 70%);
      border-bottom: 1px solid var(--border-color);
    }

    .page-hero-tag {
      display: inline-block;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--primary-color);
      margin-bottom: 12px;
    }

    .page-hero-title {
      font-size: 2.5rem;
      font-weight: 900;
      color: #FFFFFF;
      max-width: 820px;
      margin: 0 auto 16px;
      line-height: 1.25;
    }

    .page-hero-subtitle {
      font-size: 1.05rem;
      color: var(--text-secondary);
      max-width: 680px;
      margin: 0 auto 30px;
      line-height: 1.6;
    }

    .category-nav-bar {
      position: sticky;
      top: 72px;
      z-index: 90;
      background: rgba(9, 13, 22, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border-color);
      padding: 14px 0;
    }

    .category-pills-row {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 4px;
      scrollbar-width: none;
    }
    .category-pills-row::-webkit-scrollbar {
      display: none;
    }

    .cat-pill-link {
      white-space: nowrap;
      padding: 8px 18px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-sm);
      color: var(--text-secondary);
      font-size: 0.85rem;
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition);
    }

    .cat-pill-link:hover,
    .cat-pill-link.active {
      color: #FFFFFF;
      background: var(--primary-color);
      border-color: var(--primary-color);
    }

    .feature-group-section {
      padding: 60px 0;
      border-bottom: 1px solid var(--border-color);
    }

    .feature-group-header {
      margin-bottom: 32px;
    }

    .feature-group-code {
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--accent-color);
      margin-bottom: 6px;
    }

    .feature-group-title {
      font-size: 1.8rem;
      font-weight: 800;
      color: #FFFFFF;
      margin-bottom: 8px;
    }

    .feature-group-desc {
      font-size: 0.95rem;
      color: var(--text-secondary);
      max-width: 640px;
    }

    .features-card-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }

    @media (max-width: 992px) {
      .features-card-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 640px) {
      .features-card-grid {
        grid-template-columns: 1fr;
      }
      .page-hero-title {
        font-size: 1.85rem;
      }
    }

    .spec-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 26px 22px;
      transition: var(--transition);
      display: flex;
      flex-direction: column;
    }

    .spec-card:hover {
      border-color: var(--border-color-focus);
      background: var(--bg-card-hover);
      transform: translateY(-2px);
    }

    .spec-card-icon {
      width: 44px;
      height: 44px;
      border-radius: var(--radius-sm);
      background: rgba(37, 99, 235, 0.12);
      border: 1px solid rgba(37, 99, 235, 0.3);
      color: #60A5FA;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.15rem;
      margin-bottom: 18px;
    }

    .spec-card-title {
      font-size: 1.12rem;
      font-weight: 700;
      color: #FFFFFF;
      margin-bottom: 10px;
    }

    .spec-card-text {
      font-size: 0.88rem;
      color: var(--text-secondary);
      line-height: 1.6;
      margin-bottom: 0;
    }

    .fitur-cta-section {
      padding: 80px 0;
      text-align: center;
      background: var(--bg-dark);
    }

    .fitur-cta-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 56px 32px;
      max-width: 840px;
      margin: 0 auto;
    }
  </style>
  <!-- Anti-FOUC Theme Script (Execute immediately before body render) -->
  <script>
    (function() {
      try {
        var saved = localStorage.getItem('umkm_theme');
        if (saved === 'light' || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches)) {
          document.documentElement.setAttribute('data-theme', 'light');
        } else {
          document.documentElement.removeAttribute('data-theme');
        }
      } catch (e) {}
    })();
  </script>
</head>
<body>

  <!-- ==================== NAVIGATION BAR ==================== -->
  <nav class="navbar" id="navbar">
    <div class="container nav-container">
      <a href="index.php" class="brand-logo" aria-label="Beranda UMKM Digital">
        <img src="<?= htmlspecialchars($settings['site_logo'] ?? 'assets/images/logo.svg') ?>" alt="UMKM Digital Logo">
      </a>

      <ul class="nav-links" id="navLinks">
        <li><a href="index.php#keunggulan" class="nav-link">Keunggulan</a></li>
        <li><a href="index.php#layanan" class="nav-link">Layanan</a></li>
        <li><a href="index.php#portofolio" class="nav-link">Portofolio</a></li>
        <li><a href="fitur.php" class="nav-link" style="color: var(--primary-color); font-weight: 700;">Fitur Lengkap</a></li>
        <li><a href="index.php#harga" class="nav-link">Paket Harga</a></li>
        <li><a href="index.php#cara-kerja" class="nav-link">Tahapan Kerja</a></li>
        <li><a href="index.php#faq" class="nav-link">FAQ</a></li>
      </ul>

      <div class="nav-actions">
        <!-- Dark / Light Mode Toggle Button -->
        <button type="button" class="theme-toggle-btn" id="themeToggleBtn" aria-label="Ganti Mode Terang atau Gelap" title="Ganti Mode Terang / Gelap">
          <i class="fa-solid fa-moon" id="themeToggleIcon" aria-hidden="true"></i>
        </button>
        <a href="<?= htmlspecialchars($settings['nav_cta_link'] ?? 'https://wa.me/6281234567890') ?>" target="_blank" class="btn btn-primary btn-sm">
          <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?= htmlspecialchars($settings['nav_cta_text'] ?? 'Hubungi Kami') ?>
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Buka Menu Navigasi" aria-expanded="false">
          <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- ==================== PAGE HERO ==================== -->
  <header class="page-hero">
    <div class="container">
      <span class="page-hero-tag">KATALOG SPESIFIKASI LENGKAP</span>
      <h1 class="page-hero-title">Fitur Paling Lengkap Untuk Membantu Bisnismu Berkembang</h1>
      <p class="page-hero-subtitle">
        Sistem toko online top up game dan voucher digital terotomasi penuh untuk UMKM dan Reseller. Dirancang dengan keandalan server tinggi, integrasi multi-supplier, dan payment gateway mandiri.
      </p>
      <div>
        <a href="index.php#harga" class="btn btn-primary" style="margin-right: 12px;">
          <i class="fa-solid fa-layer-group" aria-hidden="true"></i> Lihat Pilihan Paket
        </a>
        <a href="index.php" class="btn btn-outline">
          <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Kembali ke Beranda
        </a>
      </div>
    </div>
  </header>

  <!-- ==================== CATEGORY NAVIGATION TABS ==================== -->
  <div class="category-nav-bar">
    <div class="container">
      <div class="category-pills-row">
        <?php $idx = 1; foreach ($categories as $catCode => $catData): ?>
          <a href="#cat-<?= htmlspecialchars($catCode) ?>" class="cat-pill-link <?= $idx === 1 ? 'active' : '' ?>">
            <?= sprintf('%02d', $idx) ?>. <?= htmlspecialchars($catData['name']) ?>
          </a>
        <?php $idx++; endforeach; ?>
      </div>
    </div>
  </div>

  <!-- ==================== FEATURE GROUPS ==================== -->
  <main>
    <?php $catIdx = 1; foreach ($categories as $catCode => $catData): ?>
      <section class="feature-group-section" id="cat-<?= htmlspecialchars($catCode) ?>">
        <div class="container">
          <div class="feature-group-header">
            <div class="feature-group-code">KATEGORI <?= sprintf('%02d', $catIdx) ?></div>
            <h2 class="feature-group-title"><?= htmlspecialchars($catData['name']) ?></h2>
            <p class="feature-group-desc"><?= htmlspecialchars($catData['desc']) ?></p>
          </div>

          <div class="features-card-grid">
            <?php foreach ($catData['items'] as $item): ?>
              <article class="spec-card">
                <div class="spec-card-icon">
                  <i class="fa-solid fa-<?= htmlspecialchars($item['icon'] ?: 'circle-check') ?>" aria-hidden="true"></i>
                </div>
                <h3 class="spec-card-title"><?= htmlspecialchars($item['title']) ?></h3>
                <p class="spec-card-text"><?= htmlspecialchars($item['description']) ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php $catIdx++; endforeach; ?>

    <!-- ==================== BOTTOM CTA ==================== -->
    <section class="fitur-cta-section">
      <div class="container">
        <div class="fitur-cta-card">
          <h2 style="font-size: 2rem; font-weight: 900; color: #FFFFFF; margin-bottom: 14px;">
            Siap Membangun Website Toko Top Up Game Anda?
          </h2>
          <p style="color: var(--text-secondary); max-width: 620px; margin: 0 auto 28px; line-height: 1.6;">
            Pilih paket pembuatan website yang sesuai dengan kebutuhan Anda atau hubungi tim kami untuk konsultasi teknis dan integrasi supplier gratis.
          </p>
          <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
            <a href="index.php#harga" class="btn btn-primary btn-lg">
              <i class="fa-solid fa-tags" aria-hidden="true"></i> Pilih Paket Website
            </a>
            <a href="<?= htmlspecialchars($settings['nav_cta_link'] ?? 'https://wa.me/6281234567890') ?>" target="_blank" class="btn btn-outline btn-lg">
              <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Konsultasi via WhatsApp
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ==================== FOOTER ==================== -->
  <footer class="footer">
    <div class="container footer-bottom">
      <p><?= htmlspecialchars($settings['copyright_text'] ?? $settings['footer_copyright'] ?? '2026 UMKM Digital. Hak Cipta Dilindungi.') ?></p>
      <div class="footer-links">
        <a href="index.php">Beranda</a>
        <a href="fitur.php">Katalog Fitur</a>
        <a href="index.php#harga">Paket Harga</a>
        <a href="admin/login.php">Admin Panel</a>
      </div>
    </div>
  </footer>

  <script>
    // Dark / Light Mode Switcher with localStorage persistence
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const themeToggleIcon = document.getElementById('themeToggleIcon');

    function applyTheme(theme) {
      if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        if (themeToggleIcon) {
          themeToggleIcon.classList.remove('fa-moon');
          themeToggleIcon.classList.add('fa-sun');
        }
      } else {
        document.documentElement.removeAttribute('data-theme');
        if (themeToggleIcon) {
          themeToggleIcon.classList.remove('fa-sun');
          themeToggleIcon.classList.add('fa-moon');
        }
      }
      try { localStorage.setItem('umkm_theme', theme); } catch (e) {}
    }

    // Initialize icon based on already set data-theme attribute
    const initialTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    applyTheme(initialTheme);

    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
        applyTheme(currentTheme === 'light' ? 'dark' : 'light');
      });
    }

    // Smooth scrolling for category pills
    document.querySelectorAll('.cat-pill-link').forEach(link => {
      link.addEventListener('click', function(e) {
        document.querySelectorAll('.cat-pill-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
      });
    });

    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    if (menuToggle && navLinks) {
      menuToggle.addEventListener('click', () => {
        const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', !expanded);
        navLinks.classList.toggle('active');
      });
    }
  </script>
</body>
</html>
