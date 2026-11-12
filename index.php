<?php
require_once __DIR__ . '/config/database.php';

// Fetch all dynamic data from database
$settings = get_settings($pdo);
$hero = $pdo->query("SELECT * FROM hero_section WHERE id = 1")->fetch();
$hero_slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")->fetchAll();
$partners = $pdo->query("SELECT * FROM partners ORDER BY sort_order ASC, id ASC")->fetchAll();
$why_us = $pdo->query("SELECT * FROM why_us ORDER BY sort_order ASC, id ASC")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC")->fetchAll();
$portfolio = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id ASC")->fetchAll();
$showcase = $pdo->query("SELECT * FROM showcase WHERE id = 1")->fetch();
$showcase_tabs = $pdo->query("SELECT * FROM showcase_tabs ORDER BY sort_order ASC, id ASC")->fetchAll();
$feature_highlights = $pdo->query("SELECT * FROM feature_highlights ORDER BY sort_order ASC, id ASC")->fetchAll();
$pricing_plans = $pdo->query("SELECT * FROM pricing_plans ORDER BY sort_order ASC, id ASC")->fetchAll();
$workflow_steps = $pdo->query("SELECT * FROM workflow_steps ORDER BY sort_order ASC, id ASC")->fetchAll();
$faqs = $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC")->fetchAll();

// Fallback hero slide if none configured
if (empty($hero_slides)) {
    $hero_slides = [[
        'desktop_image' => $hero['hero_image'] ?? 'assets/images/hero-slide-1-desktop.svg',
        'mobile_image' => 'assets/images/hero-slide-1-mobile.svg',
        'url_label' => 'toko-anda.com',
        'caption' => 'Preview Toko Digital'
    ]];
}

// Fetch service cards for dynamic tabs in "Apa Saja yang kami kerjakan"
$service_cards = $pdo->query("SELECT * FROM service_cards ORDER BY service_id ASC, sort_order ASC, id ASC")->fetchAll();
$cards_by_service = [];
foreach ($service_cards as $sc) {
    $cards_by_service[$sc['service_id']][] = $sc;
}

// Group feature highlights by category for the 3-tab showcase in "Apa yang Anda Dapatkan"
$feat_tab_1 = $settings['features_tab_1'] ?? 'Website';
$feat_tab_2 = $settings['features_tab_2'] ?? 'Poster Design';
$feat_tab_3 = $settings['features_tab_3'] ?? 'Character';
$feature_categories = [$feat_tab_1, $feat_tab_2, $feat_tab_3];
$features_by_cat = [
    $feat_tab_1 => [],
    $feat_tab_2 => [],
    $feat_tab_3 => []
];
foreach ($feature_highlights as $fh) {
    $cat = !empty($fh['category']) ? $fh['category'] : $feat_tab_1;
    if (!isset($features_by_cat[$cat])) {
        $features_by_cat[$feat_tab_1][] = $fh;
    } else {
        if (count($features_by_cat[$cat]) < 8) {
            $features_by_cat[$cat][] = $fh;
        }
    }
}

$showcase_enabled = ($settings['showcase_enabled'] ?? '1') === '1';
$pricing_cols = ($settings['pricing_columns'] ?? '4') === '3' ? 'cols-3' : 'cols-4';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($settings['site_title'] ?? 'UMKM Digital: Solusi Pembuatan Website Top Up Game') ?></title>
  <meta name="description" content="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>">
  <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($settings['site_favicon'] ?? 'assets/images/logo.svg') ?>">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Dynamic Theme Palette (Customized via Admin Panel) -->
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
        <li><a href="#keunggulan" class="nav-link">Keunggulan</a></li>
        <li><a href="#layanan" class="nav-link">Layanan</a></li>
        <li><a href="#portofolio" class="nav-link">Portofolio</a></li>
        <li><a href="fitur.php" class="nav-link">Fitur Lengkap</a></li>
        <li><a href="#harga" class="nav-link">Paket Harga</a></li>
        <li><a href="#cara-kerja" class="nav-link">Tahapan Kerja</a></li>
        <li><a href="#faq" class="nav-link">FAQ</a></li>
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

  <!-- ==================== HERO SECTION ==================== -->
  <header class="hero-section" id="hero">
    <div class="container hero-grid">
      <div class="hero-content">
        <div class="hero-tag">
          <?= htmlspecialchars($hero['badge_text'] ?? 'LAYANAN PEMBUATAN WEBSITE RESELLER') ?>
        </div>

        <h1 class="hero-title">
          <?= htmlspecialchars($hero['title'] ?? 'Your Business, Our Expertise. Let\'s Grow Together.') ?>
        </h1>

        <p class="hero-subtitle">
          <?= nl2br(htmlspecialchars($hero['subtitle'] ?? '')) ?>
        </p>

        <div class="hero-btns">
          <a href="<?= htmlspecialchars($hero['btn_primary_link'] ?? 'https://wa.me/6281234567890') ?>" target="_blank" class="btn btn-primary">
            <i class="fa-solid fa-comments" aria-hidden="true"></i> <?= htmlspecialchars($hero['btn_primary_text'] ?? 'Konsultasi Sekarang') ?>
          </a>
          <a href="<?= htmlspecialchars($hero['btn_secondary_link'] ?? '#portofolio') ?>" class="btn btn-outline">
            <i class="fa-solid fa-layer-group" aria-hidden="true"></i> <?= htmlspecialchars($hero['btn_secondary_text'] ?? 'Lihat Portofolio') ?>
          </a>
        </div>
      </div>

      <!-- Dual-Device Auto-Slider Showcase (Browser + Overlapping Mobile) -->
      <div class="hero-mockup-wrapper">
        <div class="hero-device-stage">
          <!-- Floating Feature Badges (5 pills) -->
          <div class="hero-floating-badge badge-pos-1">
            <i class="fa-solid fa-palette"></i> 20+ tema tersedia
          </div>
          <div class="hero-floating-badge badge-pos-2">
            <i class="fa-solid fa-globe"></i> Custom domain
          </div>
          <div class="hero-floating-badge badge-pos-3">
            <i class="fa-solid fa-bolt"></i> Transaksi otomatis
          </div>
          <div class="hero-floating-badge badge-pos-4">
            <i class="fa-solid fa-wallet"></i> Tanpa deposit
          </div>
          <div class="hero-floating-badge badge-pos-5">
            <i class="fa-solid fa-clock"></i> 2 menit setup
          </div>

          <div class="hero-browser-mockup">
            <div class="hero-browser-topbar">
              <div class="browser-dots-group">
                <span class="browser-dot red"></span>
                <span class="browser-dot yellow"></span>
                <span class="browser-dot green"></span>
              </div>
            </div>
            <div class="hero-browser-viewport">
              <?php foreach ($hero_slides as $idx => $slide): ?>
                <img
                  src="<?= htmlspecialchars($slide['desktop_image']) ?>"
                  alt="<?= htmlspecialchars($slide['caption'] ?? 'Preview Website Desktop') ?>"
                  class="hero-slide-img hero-desktop-slide <?= $idx === 0 ? 'active' : '' ?>"
                  data-url="<?= htmlspecialchars($slide['url_label'] ?? 'toko-anda.com') ?>"
                  width="800" height="500"
                  <?= $idx === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Overlapping Smartphone Preview -->
          <div class="hero-phone-mockup">
            <div class="hero-phone-notch"><span></span></div>
            <div class="hero-phone-viewport">
              <?php foreach ($hero_slides as $idx => $slide): ?>
                <img
                  src="<?= htmlspecialchars(!empty($slide['mobile_image']) ? $slide['mobile_image'] : $slide['desktop_image']) ?>"
                  alt="<?= htmlspecialchars($slide['caption'] ?? 'Preview Website Mobile') ?>"
                  class="hero-slide-img hero-mobile-slide <?= $idx === 0 ? 'active' : '' ?>"
                  width="260" height="490"
                  <?= $idx === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <?php if (count($hero_slides) > 1): ?>
          <div class="hero-slider-controls" aria-label="Navigasi Slide Hero">
            <?php foreach ($hero_slides as $idx => $slide): ?>
              <button
                type="button"
                class="hero-slide-dot <?= $idx === 0 ? 'active' : '' ?>"
                data-slide-idx="<?= $idx ?>"
                aria-label="Slide <?= $idx + 1 ?>"></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- ==================== PARTNER / SUPPLIER STRIP (INFINITE MARQUEE) ==================== -->
  <section class="partners-section">
    <div class="container" style="max-width: 100%;">
      <div class="partners-title"><?= htmlspecialchars($settings['partners_title'] ?? 'TERINTEGRASI DENGAN SUPPLIER TERNAMA') ?></div>
      <div class="partners-marquee-container">
        <div class="partners-marquee-track">
          <?php 
          // Render 4 loops so track is guaranteed wider than any 4K screen and never breaks
          for ($loop = 0; $loop < 4; $loop++): 
            foreach ($partners as $p): 
              $mode = $p['display_mode'] ?? 'logo_text';
          ?>
            <div class="partner-item" <?= $loop > 0 ? 'aria-hidden="true"' : '' ?>>
              <?php if ($mode === 'logo_only' && !empty($p['logo_image'])): ?>
                <img src="<?= htmlspecialchars($p['logo_image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="partner-logo-img">
              <?php elseif ($mode === 'text_only' || empty($p['logo_image'])): ?>
                <span class="partner-brand-text"><?= htmlspecialchars($p['name']) ?></span>
              <?php else: ?>
                <img src="<?= htmlspecialchars($p['logo_image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="partner-logo-img">
                <span class="partner-brand-text"><?= htmlspecialchars($p['name']) ?></span>
              <?php endif; ?>
            </div>
          <?php 
            endforeach; 
          endfor; 
          ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== SECTION: KENAPA HARUS DI KAMI ==================== -->
  <section class="why-section" id="keunggulan">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Kenapa Harus Di Kami ?</h2>
        <p class="section-subtitle">Kami memberikan solusi pembuatan website digital terpadu dengan standar kualitas tinggi dan otomatisasi 24 jam.</p>
      </div>

      <div class="why-grid">
        <?php foreach ($why_us as $item): ?>
          <div class="why-card">
            <div class="card-icon-box">
              <i class="fa-solid fa-<?= htmlspecialchars($item['icon']) ?>" aria-hidden="true"></i>
            </div>
            <h3><?= htmlspecialchars($item['title']) ?></h3>
            <p><?= htmlspecialchars($item['description']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==================== SECTION: APA SAJA YANG KAMI KERJAKAN ==================== -->
  <section class="services-section" id="layanan">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Apa Saja yang kami kerjakan</h2>
        <p class="section-subtitle">Dukungan komprehensif mulai dari rancangan kode website, visual branding toko, hingga aset kreatif tematik.</p>
      </div>

      <!-- 3 Clean Text Tabs (Without Icon Clutter) -->
      <div class="service-tabs-wrapper">
        <?php foreach ($services as $idx => $srv): ?>
          <button type="button" class="service-tab-btn <?= $idx === 0 ? 'active' : '' ?>" data-service-id="<?= $srv['id'] ?>">
            <span class="service-tab-title"><?= htmlspecialchars($srv['title']) ?></span>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Dynamic Clean Feature Cards Grid (Plain Typographic Layout) -->
      <?php foreach ($services as $idx => $srv): ?>
        <div class="service-tab-content <?= $idx === 0 ? 'active' : '' ?>" id="service-panel-<?= $srv['id'] ?>">
          <div class="service-cards-grid">
            <?php if (!empty($cards_by_service[$srv['id']])): ?>
              <?php foreach ($cards_by_service[$srv['id']] as $card): ?>
                <div class="service-feature-card">
                  <h4><?= htmlspecialchars($card['title']) ?></h4>
                  <p><?= htmlspecialchars($card['description']) ?></p>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ==================== SECTION: PORTOFOLIO KAMI ==================== -->
  <section class="portfolio-section" id="portofolio">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Portofolio Klien &amp; Hasil Karya Kami</h2>
        <p class="section-subtitle">Beberapa tampilan website toko online, poster promosi, dan aset maskot yang telah kami kerjakan.</p>
      </div>

      <!-- Centered Filter Tabs (Directly start with Website, no "Semua Proyek" button) -->
      <div class="filter-tabs">
        <button type="button" class="filter-btn portfolio-filter-btn active" data-filter="website">Website</button>
        <button type="button" class="filter-btn portfolio-filter-btn" data-filter="poster design">Poster Design</button>
        <button type="button" class="filter-btn portfolio-filter-btn" data-filter="character">Character</button>
      </div>

      <!-- Slider Shell with Arrows + Cards (3 cards per view on desktop) -->
      <div class="portfolio-slider-shell">
        <button type="button" class="portfolio-nav-arrow" id="portPrevBtn" aria-label="Portofolio Sebelumnya">
          <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
        </button>

        <div class="portfolio-grid" id="portfolioGrid">
          <?php foreach ($portfolio as $p): 
            $actionType = $p['action_type'] ?? 'modal';
          ?>
            <div
              class="portfolio-card"
              data-category="<?= htmlspecialchars(strtolower($p['category'])) ?>">
              <div class="portfolio-card-header">
                <div class="portfolio-card-title-row">
                  <?php if (!empty($p['logo_image'])): ?>
                    <img src="<?= htmlspecialchars($p['logo_image']) ?>" alt="<?= htmlspecialchars($p['title']) ?> Logo" class="portfolio-client-logo">
                  <?php else: ?>
                    <div class="portfolio-client-logo-icon">
                      <i class="fa-solid fa-globe"></i>
                    </div>
                  <?php endif; ?>
                  <h3><?= htmlspecialchars($p['title']) ?></h3>
                </div>
              </div>

              <div class="portfolio-thumb">
                <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" width="600" height="375" loading="lazy">
              </div>

              <div class="portfolio-footer">
                <?php if ($actionType === 'link' && !empty($p['demo_url']) && $p['demo_url'] !== '#'): ?>
                  <a href="<?= htmlspecialchars($p['demo_url']) ?>" target="_blank" class="portfolio-preview-btn">
                    Preview Website <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.78rem;" aria-hidden="true"></i>
                  </a>
                <?php else: ?>
                  <button
                    type="button"
                    class="portfolio-preview-btn open-portfolio-modal"
                    data-title="<?= htmlspecialchars($p['title']) ?>"
                    data-category="<?= htmlspecialchars($p['category']) ?>"
                    data-desc="<?= htmlspecialchars($p['description']) ?>"
                    data-tags="<?= htmlspecialchars($p['tags']) ?>"
                    data-image="<?= htmlspecialchars($p['image']) ?>"
                    data-url="<?= htmlspecialchars($p['demo_url']) ?>">
                    Preview Website <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.78rem;" aria-hidden="true"></i>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <button type="button" class="portfolio-nav-arrow" id="portNextBtn" aria-label="Portofolio Berikutnya">
          <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        </button>
      </div>

      <!-- Pagination Dots Indicator -->
      <div class="carousel-dots" id="portfolioDots"></div>
    </div>
  </section>

  <!-- ==================== SECTION: SHOWCASE FITUR INTERAKTIF (ON/OFF FROM ADMIN) ==================== -->
  <?php if ($showcase_enabled && !empty($showcase_tabs)): ?>
  <section class="showcase-section" id="showcase">
    <div class="container">
      <div class="section-header" style="margin-bottom: 28px;">
        <h2 class="section-title"><?= htmlspecialchars($settings['showcase_title'] ?? 'Nikmati berbagai macam fitur yang telah kami sediakan disini') ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($settings['showcase_subtitle'] ?? 'Layanan yang kami tawarkan memiliki banyak fitur yang bisa Anda dapatkan, berikut fiturnya:') ?></p>
      </div>

      <!-- Interactive Tabs -->
      <div class="showcase-tab-nav">
        <div class="showcase-tab-pill-group">
          <?php foreach ($showcase_tabs as $idx => $tab): ?>
            <button
              type="button"
              class="showcase-tab-btn <?= $idx === 0 ? 'active' : '' ?>"
              data-showcase-tab="<?= $tab['id'] ?>"
              data-url="<?= htmlspecialchars($tab['address_url']) ?>"
              data-caption="<?= htmlspecialchars($tab['caption']) ?>">
              <?= htmlspecialchars($tab['tab_label']) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Interactive Browser Mockup Container -->
      <div class="showcase-browser-frame">
        <div class="showcase-browser-top">
          <div class="browser-dots-group">
            <span class="browser-dot red"></span>
            <span class="browser-dot yellow"></span>
            <span class="browser-dot green"></span>
          </div>
        </div>

        <?php foreach ($showcase_tabs as $idx => $tab): ?>
          <div class="showcase-tab-panel <?= $idx === 0 ? 'active' : '' ?>" id="showcase-panel-<?= $tab['id'] ?>">
            <img src="<?= htmlspecialchars($tab['image']) ?>" alt="<?= htmlspecialchars($tab['tab_label']) ?>" width="960" height="520" loading="lazy">
          </div>
        <?php endforeach; ?>
      </div>

      <div class="showcase-caption-bar" id="showcaseCaptionBar">
        <?= htmlspecialchars($showcase_tabs[0]['caption'] ?? '') ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ==================== SECTION: APA YANG ANDA DAPATKAN ==================== -->
  <section class="features-grid-section" id="fitur">
    <div class="container">
      <div class="section-header" style="margin-bottom: 28px;">
        <h2 class="section-title">Apa yang Anda Dapatkan</h2>
        <p class="section-subtitle">Setiap paket website yang kami buat sudah dilengkapi kapabilitas teknis siap pakai tanpa biaya tambahan.</p>
      </div>

      <!-- 3 Centered Tabs (Website, Poster Design, Character) -->
      <div class="filter-tabs">
        <?php foreach ($feature_categories as $idx => $catName): ?>
          <button
            type="button"
            class="filter-btn features-cat-btn <?= $idx === 0 ? 'active' : '' ?>"
            data-feat-cat="<?= htmlspecialchars($catName) ?>"
            data-is-first="<?= $idx === 0 ? '1' : '0' ?>">
            <?= htmlspecialchars($catName) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Feature Cards (Max 8 per category) -->
      <?php foreach ($feature_categories as $idx => $catName): ?>
        <div class="feature-grid-8 feat-cat-panel" id="feat-cat-panel-<?= $idx ?>" data-feat-panel="<?= htmlspecialchars($catName) ?>" style="<?= $idx === 0 ? '' : 'display: none;' ?>">
          <?php if (!empty($features_by_cat[$catName])): ?>
            <?php foreach ($features_by_cat[$catName] as $fh): ?>
              <div class="feature-box">
                <div class="feature-box-icon">
                  <i class="fa-solid fa-<?= htmlspecialchars($fh['icon']) ?>" aria-hidden="true"></i>
                </div>
                <h4><?= htmlspecialchars($fh['title']) ?></h4>
                <p><?= htmlspecialchars($fh['description']) ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-secondary); background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
              Belum ada fitur ditambahkan untuk kategori <?= htmlspecialchars($catName) ?>.
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <!-- "Lihat Semua Fitur" Button ONLY visible on Tab 1 (Website) -->
      <div id="featuresSeeMoreBtnWrap" style="text-align: center; margin-top: 40px;">
        <a href="fitur.php" class="btn btn-primary btn-lg" style="padding: 14px 36px; font-weight: 800;">
          Lihat Semua Fitur <i class="fa-solid fa-arrow-right" aria-hidden="true" style="margin-left: 8px;"></i>
        </a>
      </div>
    </div>
  </section>

  <!-- ==================== SECTION: PAKET HARGA (PRICING TIERS) ==================== -->
  <section class="pricing-section" id="harga">
    <div class="container">
      <div class="section-header" style="margin-bottom: 28px;">
        <span class="section-tag">PILIHAN PAKET WEBSITE</span>
        <h2 class="section-title">PILIH PAKET YANG COCOK BUAT KAMU</h2>
        <p class="section-subtitle">Investasi sekali di awal dengan lisensi sistem mandiri tanpa potongan komisi dari setiap transaksi.</p>
      </div>

      <!-- Centered Pricing Category Tabs -->
      <div class="filter-tabs">
        <button type="button" class="filter-btn pricing-filter-btn active" data-category="website">Website</button>
        <button type="button" class="filter-btn pricing-filter-btn" data-category="resseler">Resseler</button>
        <button type="button" class="filter-btn pricing-filter-btn" data-category="ads-on">Ads On</button>
      </div>

      <!-- Configurable 3 or 4 Columns Grid -->
      <div class="pricing-grid <?= $pricing_cols ?>">
        <?php foreach ($pricing_plans as $plan): ?>
          <div class="pricing-card <?= $plan['is_popular'] ? 'popular' : '' ?>" data-category="<?= htmlspecialchars($plan['category']) ?>">
            <?php if (!empty($plan['badge_text'])): ?>
              <span class="pricing-top-badge <?= $plan['is_popular'] ? 'badge-blue' : ($plan['name'] === 'VIP EXPERT' ? 'badge-cyan' : 'badge-gray') ?>">
                <?= htmlspecialchars($plan['badge_text']) ?>
              </span>
            <?php endif; ?>

            <div class="pricing-name"><?= htmlspecialchars($plan['name']) ?></div>
            <div class="pricing-subtitle"><?= htmlspecialchars($plan['tier_subtitle']) ?></div>

            <div class="pricing-price-box">
              <?php if (!empty($plan['old_price'])): ?>
                <div class="old-price"><?= htmlspecialchars($plan['old_price']) ?></div>
              <?php endif; ?>
              <div class="main-price"><?= htmlspecialchars($plan['price']) ?></div>
              <div class="price-period"><?= htmlspecialchars($plan['period']) ?></div>
            </div>

            <ul class="pricing-features-list">
              <?php 
              $pts = explode("\n", str_replace("\r", "", $plan['features_list']));
              foreach ($pts as $pt):
                if (trim($pt)):
              ?>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i> <span><?= htmlspecialchars(trim($pt)) ?></span></li>
              <?php 
                endif;
              endforeach; 
              ?>
            </ul>

            <div class="pricing-btn-box">
              <a href="<?= htmlspecialchars($plan['btn_link']) ?>" target="_blank" class="btn <?= $plan['is_popular'] ? 'btn-primary' : 'btn-outline' ?>">
                <?= htmlspecialchars($plan['btn_text'] ?: 'Pilih Paket') ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==================== SECTION: ALUR KERJA (HORIZONTAL TIMELINE) ==================== -->
  <section class="workflow-section" id="cara-kerja">
    <div class="container">
      <div class="section-header">
        <span class="section-tag">CARA KERJA</span>
        <h2 class="section-title"><?= htmlspecialchars($settings['workflow_title'] ?? 'Dari ngobrol sampai toko tayang, empat langkah') ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($settings['workflow_subtitle'] ?? 'Kamu tidak perlu paham teknis - bagian rumitnya kami yang kerjakan.') ?></p>
      </div>

      <div class="workflow-timeline">
        <?php foreach ($workflow_steps as $idx => $step): ?>
          <div class="workflow-step-item">
            <div class="workflow-circle-num"><?= htmlspecialchars(ltrim($step['step_number'], '0') ?: ($idx + 1)) ?></div>
            <h3><?= htmlspecialchars($step['title']) ?></h3>
            <p><?= htmlspecialchars($step['description']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==================== SECTION: FAQ ==================== -->
  <section class="faq-section" id="faq">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Pertanyaan yang sering ditanyakan</h2>
        <p class="section-subtitle">Informasi penting mengenai operasional toko, kepemilikan kode, dan dukungan teknis.</p>
      </div>

      <div class="faq-container">
        <?php foreach ($faqs as $f): ?>
          <div class="faq-item">
            <button type="button" class="faq-question" aria-expanded="false">
              <span><?= htmlspecialchars($f['question']) ?></span>
              <i class="fa-solid fa-chevron-down faq-arrow" aria-hidden="true"></i>
            </button>
            <div class="faq-answer">
              <p><?= nl2br(htmlspecialchars($f['answer'])) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==================== CALL TO ACTION BANNER ==================== -->
  <section class="cta-banner-section">
    <div class="container">
      <div class="cta-banner-box">
        <div class="cta-banner-content">
          <h2><?= htmlspecialchars($settings['cta_title'] ?? 'Siap Memulai Website Bisnis Anda?') ?></h2>
          <p><?= htmlspecialchars($settings['cta_subtitle'] ?? 'Bergabunglah dengan 23+ pemilik toko yang sudah berjualan otomatis setiap hari. Konsultasi gratis, tanpa komitmen.') ?></p>
        </div>
        <div class="cta-banner-actions" style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
          <a href="<?= htmlspecialchars($settings['cta_btn_primary_link'] ?? $settings['hero_btn_wa_link'] ?? 'https://wa.me/6281234567890') ?>" target="_blank" class="btn btn-primary btn-lg">
            <i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($settings['cta_btn_primary_text'] ?? 'Hubungi Kami Sekarang') ?>
          </a>
          <?php if (!empty($settings['cta_btn_secondary_text'])): ?>
            <a href="<?= htmlspecialchars($settings['cta_btn_secondary_link'] ?? 'fitur.php') ?>" class="btn btn-secondary btn-lg">
              <?= htmlspecialchars($settings['cta_btn_secondary_text']) ?>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ==================== FOOTER (4-COLUMN LAYOUT) ==================== -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <!-- Col 1: Brand & Bio -->
        <div class="footer-brand">
          <div class="brand-logo" style="margin-bottom: 14px;">
            <img src="<?= htmlspecialchars($settings['site_logo'] ?? 'assets/images/logo.svg') ?>" alt="Logo">
            <span class="brand-name" style="font-size: 1.15rem; font-weight: 800; color: #FFFFFF;"><?= htmlspecialchars($settings['site_title'] ?? 'UMKM Digital') ?></span>
          </div>
          <p><?= htmlspecialchars($settings['footer_bio'] ?? $settings['footer_text'] ?? 'Penyedia solusi pembuatan website digital, topup game otomatis, desain poster, dan maskot visual dengan standar teknologi modern.') ?></p>
          <div class="footer-socials">
            <a href="<?= htmlspecialchars($settings['footer_wa_link'] ?? $settings['hero_btn_wa_link'] ?? 'https://wa.me/6281234567890') ?>" target="_blank" class="social-icon-btn" aria-label="WhatsApp">
              <i class="fa-brands fa-whatsapp"></i>
            </a>
            <a href="<?= htmlspecialchars($settings['footer_instagram_link'] ?? $settings['instagram_url'] ?? 'https://instagram.com') ?>" target="_blank" class="social-icon-btn" aria-label="Instagram">
              <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="<?= htmlspecialchars($settings['footer_telegram_link'] ?? $settings['contact_telegram'] ?? 'https://t.me') ?>" target="_blank" class="social-icon-btn" aria-label="Telegram">
              <i class="fa-brands fa-telegram"></i>
            </a>
          </div>
        </div>

        <!-- Col 2: Navigasi -->
        <div class="footer-col">
          <h4>Navigasi</h4>
          <ul class="footer-links">
            <li><a href="#hero">Beranda</a></li>
            <li><a href="#keunggulan">Kenapa Di Kami</a></li>
            <li><a href="#layanan">Layanan</a></li>
            <li><a href="#portofolio">Portofolio</a></li>
            <li><a href="#harga">Paket Harga</a></li>
          </ul>
        </div>

        <!-- Col 3: Fitur & Produk -->
        <div class="footer-col">
          <h4>Fitur Unggulan</h4>
          <ul class="footer-links">
            <li><a href="fitur.php">Katalog Fitur Lengkap</a></li>
            <li><a href="#cara-kerja">Alur Transaksi</a></li>
            <li><a href="#faq">Pertanyaan (FAQ)</a></li>
            <li><a href="#harga">Lisensi Selamanya</a></li>
          </ul>
        </div>

        <!-- Col 4: Kontak & Konsultasi -->
        <div class="footer-col">
          <h4>Kontak &amp; Bantuan</h4>
          <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin-bottom: 14px;">
            <?= htmlspecialchars($settings['footer_col4_desc'] ?? 'Hubungi tim support teknis kami untuk tanya jawab atau demo platform live:') ?>
          </p>
          <a href="<?= htmlspecialchars($settings['footer_col4_btn_link'] ?? $settings['hero_btn_wa_link'] ?? 'https://wa.me/6281234567890') ?>" target="_blank" class="btn btn-outline" style="font-size: 0.84rem; padding: 8px 16px;">
            <i class="fa-brands fa-whatsapp" style="color: #22C55E;"></i> <?= htmlspecialchars($settings['footer_col4_btn_text'] ?? 'WhatsApp Support') ?>
          </a>
        </div>
      </div>

      <div class="footer-bottom">
        <p><?= htmlspecialchars($settings['footer_copyright'] ?? $settings['copyright_text'] ?? '2026 UMKM Digital. Hak Cipta Dilindungi.') ?></p>
        <div class="footer-bottom-links" style="display: flex; gap: 18px;">
          <a href="#hero" class="admin-link">Kembali ke Atas &uarr;</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- ==================== PORTFOLIO POP-UP MODAL ==================== -->
  <div class="portfolio-modal-overlay" id="portfolioModalOverlay" role="dialog" aria-modal="true" aria-labelledby="portModalTitle">
    <div class="portfolio-modal">
      <button type="button" class="portfolio-modal-close" id="portModalClose" aria-label="Tutup Preview">
        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
      </button>
      <div class="portfolio-modal-media">
        <img id="portModalImg" src="" alt="Preview Portofolio">
      </div>
      <div class="portfolio-modal-body">
        <h3 class="portfolio-modal-title" id="portModalTitle"></h3>
        <p class="portfolio-modal-desc" id="portModalDesc"></p>
        <div class="portfolio-modal-tags" id="portModalTags"></div>
        <div class="portfolio-modal-actions">
          <a href="#" id="portModalLink" target="_blank" class="btn btn-primary">
            Kunjungi Website <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
          </a>
          <button type="button" class="btn btn-outline" id="portModalCancel">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== SCRIPTS ==================== -->
  <script>
    // 0. Dark / Light Mode Switcher with localStorage persistence
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
        const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(nextTheme);
      });
    }

    // 1. Sticky Navbar background
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 30) navbar.classList.add('scrolled');
      else navbar.classList.remove('scrolled');
    });

    // 2. Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');
    if (menuToggle && navLinks) {
      menuToggle.addEventListener('click', () => {
        const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', !expanded);
        navLinks.classList.toggle('active');
      });
      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
          menuToggle.setAttribute('aria-expanded', 'false');
          navLinks.classList.remove('active');
        });
      });
    }

    // 3. Hero Multi-Device Auto-Slider & Touch/Mouse Swipe
    const heroDesktopSlides = document.querySelectorAll('.hero-desktop-slide');
    const heroMobileSlides = document.querySelectorAll('.hero-mobile-slide');
    const heroSlideDots = document.querySelectorAll('.hero-slide-dot');
    const heroStage = document.querySelector('.hero-device-stage');
    let heroCurrent = 0;
    let heroTimer = null;

    function goToHeroSlide(idx) {
      if (heroDesktopSlides.length <= 1) return;
      heroDesktopSlides.forEach(el => el.classList.remove('active'));
      heroMobileSlides.forEach(el => el.classList.remove('active'));
      heroSlideDots.forEach(d => d.classList.remove('active'));

      heroCurrent = (idx + heroDesktopSlides.length) % heroDesktopSlides.length;
      if (heroDesktopSlides[heroCurrent]) {
        heroDesktopSlides[heroCurrent].classList.add('active');
      }
      if (heroMobileSlides[heroCurrent]) heroMobileSlides[heroCurrent].classList.add('active');
      if (heroSlideDots[heroCurrent]) heroSlideDots[heroCurrent].classList.add('active');
    }

    function resetHeroAutoSlide() {
      if (heroTimer) clearInterval(heroTimer);
      if (heroDesktopSlides.length > 1) {
        heroTimer = setInterval(() => goToHeroSlide(heroCurrent + 1), 3800);
      }
    }

    if (heroDesktopSlides.length > 1) {
      resetHeroAutoSlide();
      heroSlideDots.forEach(dot => {
        dot.addEventListener('click', () => {
          goToHeroSlide(parseInt(dot.getAttribute('data-slide-idx'), 10));
          resetHeroAutoSlide();
        });
      });

      // Swipe / Drag support on hero preview frame
      if (heroStage) {
        let touchStartX = 0;
        let touchEndX = 0;
        heroStage.style.cursor = 'grab';

        heroStage.addEventListener('touchstart', (e) => {
          touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        heroStage.addEventListener('touchend', (e) => {
          touchEndX = e.changedTouches[0].screenX;
          handleHeroSwipe();
        }, { passive: true });

        heroStage.addEventListener('mousedown', (e) => {
          touchStartX = e.screenX;
          heroStage.style.cursor = 'grabbing';
        });

        heroStage.addEventListener('mouseup', (e) => {
          touchEndX = e.screenX;
          heroStage.style.cursor = 'grab';
          handleHeroSwipe();
        });

        function handleHeroSwipe() {
          const diff = touchEndX - touchStartX;
          if (Math.abs(diff) > 40) {
            if (diff < 0) {
              // Swipe left -> next slide
              goToHeroSlide(heroCurrent + 1);
            } else {
              // Swipe right -> prev slide
              goToHeroSlide(heroCurrent - 1);
            }
            resetHeroAutoSlide();
          }
        }
      }
    }

    // 4. Dynamic Tabs for "Apa Saja yang kami kerjakan"
    document.querySelectorAll('.service-tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const srvId = this.getAttribute('data-service-id');
        document.querySelectorAll('.service-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.service-tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        const panel = document.getElementById('service-panel-' + srvId);
        if (panel) panel.classList.add('active');
      });
    });

    // 5. Portfolio Slider Pagination + Category Filter + Modal Pop-up
    const portCards = Array.from(document.querySelectorAll('.portfolio-card'));
    const portFilterBtns = document.querySelectorAll('.portfolio-filter-btn');
    const portDotsBox = document.getElementById('portfolioDots');
    const portPrevBtn = document.getElementById('portPrevBtn');
    const portNextBtn = document.getElementById('portNextBtn');
    let currentPortFilter = 'website';
    let currentPortPage = 0;

    function getPerView() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 1024) return 2;
      return 3;
    }

    function renderPortfolioSlider() {
      const perView = getPerView();
      const filtered = portCards.filter(c => currentPortFilter === 'all' || c.getAttribute('data-category') === currentPortFilter);
      const totalPages = Math.max(1, Math.ceil(filtered.length / perView));
      if (currentPortPage >= totalPages) currentPortPage = 0;

      portCards.forEach(c => c.classList.add('is-hidden-slide'));
      const start = currentPortPage * perView;
      const visible = filtered.slice(start, start + perView);
      visible.forEach(c => c.classList.remove('is-hidden-slide'));

      // Render pagination dots
      if (portDotsBox) {
        portDotsBox.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
          const dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'carousel-dot' + (i === currentPortPage ? ' active' : '');
          dot.setAttribute('aria-label', 'Halaman Portofolio ' + (i + 1));
          dot.addEventListener('click', () => {
            currentPortPage = i;
            renderPortfolioSlider();
          });
          portDotsBox.appendChild(dot);
        }
      }
    }

    portFilterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        portFilterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentPortFilter = btn.getAttribute('data-filter');
        currentPortPage = 0;
        renderPortfolioSlider();
      });
    });

    if (portPrevBtn) {
      portPrevBtn.addEventListener('click', () => {
        const perView = getPerView();
        const filtered = portCards.filter(c => currentPortFilter === 'all' || c.getAttribute('data-category') === currentPortFilter);
        const totalPages = Math.max(1, Math.ceil(filtered.length / perView));
        currentPortPage = (currentPortPage - 1 + totalPages) % totalPages;
        renderPortfolioSlider();
      });
    }
    if (portNextBtn) {
      portNextBtn.addEventListener('click', () => {
        const perView = getPerView();
        const filtered = portCards.filter(c => currentPortFilter === 'all' || c.getAttribute('data-category') === currentPortFilter);
        const totalPages = Math.max(1, Math.ceil(filtered.length / perView));
        currentPortPage = (currentPortPage + 1) % totalPages;
        renderPortfolioSlider();
      });
    }
    window.addEventListener('resize', renderPortfolioSlider);
    renderPortfolioSlider();

    // Portfolio Pop-up Modal
    const modalOverlay = document.getElementById('portfolioModalOverlay');
    const modalClose = document.getElementById('portModalClose');
    const modalCancel = document.getElementById('portModalCancel');
    const modalImg = document.getElementById('portModalImg');
    const modalTitle = document.getElementById('portModalTitle');
    const modalDesc = document.getElementById('portModalDesc');
    const modalTags = document.getElementById('portModalTags');
    const modalLink = document.getElementById('portModalLink');

    function closePortfolioModal() {
      if (modalOverlay) modalOverlay.classList.remove('open');
    }

    document.querySelectorAll('.open-portfolio-modal').forEach(btn => {
      btn.addEventListener('click', () => {
        modalTitle.textContent = btn.getAttribute('data-title') || '';
        modalDesc.textContent = btn.getAttribute('data-desc') || '';
        modalImg.src = btn.getAttribute('data-image') || '';
        const url = btn.getAttribute('data-url') || '#';
        if (url && url !== '#') {
          modalLink.href = url;
          modalLink.style.display = 'inline-flex';
        } else {
          modalLink.style.display = 'none';
        }
        const tagsStr = btn.getAttribute('data-tags') || '';
        modalTags.innerHTML = '';
        tagsStr.split(',').forEach(t => {
          if (t.trim()) {
            const span = document.createElement('span');
            span.className = 'portfolio-modal-tag';
            span.textContent = t.trim();
            modalTags.appendChild(span);
          }
        });
        modalOverlay.classList.add('open');
      });
    });

    if (modalClose) modalClose.addEventListener('click', closePortfolioModal);
    if (modalCancel) modalCancel.addEventListener('click', closePortfolioModal);
    if (modalOverlay) {
      modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closePortfolioModal();
      });
    }
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closePortfolioModal();
    });

    // 6. Interactive 3-Tab Showcase Switcher
    const showcaseTabBtns = document.querySelectorAll('.showcase-tab-btn');
    const showcasePanels = document.querySelectorAll('.showcase-tab-panel');
    const showcaseCaptionBar = document.getElementById('showcaseCaptionBar');

    showcaseTabBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-showcase-tab');
        showcaseTabBtns.forEach(b => b.classList.remove('active'));
        showcasePanels.forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const panel = document.getElementById('showcase-panel-' + id);
        if (panel) panel.classList.add('active');
        if (showcaseCaptionBar) showcaseCaptionBar.textContent = btn.getAttribute('data-caption') || '';
      });
    });

    // 7. "Apa yang Anda Dapatkan" 3-Tab Filter + Conditional "Lihat Semua Fitur" Button
    const featCatBtns = document.querySelectorAll('.features-cat-btn');
    const featCatPanels = document.querySelectorAll('.feat-cat-panel');
    const featSeeMoreWrap = document.getElementById('featuresSeeMoreBtnWrap');

    featCatBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetCat = btn.getAttribute('data-feat-cat');
        const isFirst = btn.getAttribute('data-is-first') === '1';

        featCatBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        featCatPanels.forEach(panel => {
          panel.style.display = panel.getAttribute('data-feat-panel') === targetCat ? 'grid' : 'none';
        });

        if (featSeeMoreWrap) {
          featSeeMoreWrap.style.display = isFirst ? 'block' : 'none';
        }
      });
    });

    // 8. Pricing category filters (Default active: website)
    const pricingButtons = document.querySelectorAll('.pricing-filter-btn');
    const pricingCards = document.querySelectorAll('.pricing-card');

    function applyPricingFilter(cat) {
      pricingCards.forEach(card => {
        const cardCat = (card.getAttribute('data-category') || '').toLowerCase();
        if (cat === 'all' || cardCat === cat) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    }

    pricingButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        pricingButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.getAttribute('data-category');
        applyPricingFilter(cat);
      });
    });

    // Run initial filter on load (website)
    applyPricingFilter('website');

    // 9. FAQ Accordion
    document.querySelectorAll('.faq-question').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.parentElement;
        const answer = item.querySelector('.faq-answer');
        const isOpen = btn.getAttribute('aria-expanded') === 'true';

        document.querySelectorAll('.faq-item').forEach(other => {
          if (other !== item) {
            other.classList.remove('active');
            other.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            other.querySelector('.faq-answer').style.maxHeight = null;
          }
        });

        if (isOpen) {
          item.classList.remove('active');
          btn.setAttribute('aria-expanded', 'false');
          answer.style.maxHeight = null;
        } else {
          item.classList.add('active');
          btn.setAttribute('aria-expanded', 'true');
          answer.style.maxHeight = answer.scrollHeight + 'px';
        }
      });
    });
  </script>
</body>
</html>
