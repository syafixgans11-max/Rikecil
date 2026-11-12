<?php
$page_title = 'Dashboard Overview';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch counts for dashboard widgets
$counts = [
    'partners' => $pdo->query("SELECT COUNT(*) FROM partners")->fetchColumn(),
    'portfolio' => $pdo->query("SELECT COUNT(*) FROM portfolio")->fetchColumn(),
    'features' => $pdo->query("SELECT COUNT(*) FROM feature_highlights")->fetchColumn(),
    'plans' => $pdo->query("SELECT COUNT(*) FROM pricing_plans")->fetchColumn(),
    'faqs' => $pdo->query("SELECT COUNT(*) FROM faqs")->fetchColumn(),
    'expanded' => $pdo->query("SELECT COUNT(*) FROM expanded_features")->fetchColumn(),
];
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Selamat Datang di Panel Admin</h1>
      <p>Kelola seluruh elemen teks, palet warna tema, gambar, dan fitur landing page Anda secara instan.</p>
    </div>
    <div class="topbar-actions">
      <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Website
      </a>
      <a href="settings.php" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-palette"></i> Atur Warna &amp; Tema
      </a>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- System Status Banner -->
    <div class="admin-card" style="border-left: 4px solid var(--admin-accent); background: linear-gradient(90deg, rgba(6, 182, 212, 0.08) 0%, var(--admin-card) 100%);">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: gap: 16px;">
        <div>
          <h3 style="font-size: 1.1rem; font-weight: 800; color: #FFFFFF;">
            <i class="fa-solid fa-database" style="color: var(--admin-accent); margin-right: 8px;"></i>
            Status Database: <span style="color: #38BDF8;"><?= strtoupper($db_type_used) ?> AKTIF</span>
          </h3>
          <p style="font-size: 0.88rem; color: #94A3B8; margin-top: 4px;">
            Sistem berjalan di PHP <?= PHP_VERSION ?>. Semua perubahan yang Anda simpan di panel ini akan langsung terupdate di halaman depan secara real-time.
          </p>
        </div>
        <div>
          <span style="display: inline-flex; align-items: center; gap: 8px; background: rgba(16, 185, 129, 0.15); color: #34D399; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.8rem;">
            <i class="fa-solid fa-circle" style="font-size: 0.55rem;"></i> Sinkronisasi 100% Siap
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="form-row" style="margin-bottom: 28px;">
      <div class="admin-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.82rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Paket Layanan</div>
            <div style="font-size: 1.8rem; font-weight: 900; color: #FFFFFF; margin-top: 4px;"><?= $counts['plans'] ?> Paket</div>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(37, 99, 235, 0.15); display: flex; align-items: center; justify-content: center; color: #38BDF8; font-size: 1.3rem;">
            <i class="fa-solid fa-tags"></i>
          </div>
        </div>
        <div style="margin-top: 14px; border-top: 1px solid var(--admin-border); padding-top: 10px;">
          <a href="pricing.php" style="color: var(--admin-accent); font-size: 0.82rem; font-weight: 700; text-decoration: none;">Kelola Harga &rarr;</a>
        </div>
      </div>

      <div class="admin-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.82rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Item Portofolio</div>
            <div style="font-size: 1.8rem; font-weight: 900; color: #FFFFFF; margin-top: 4px;"><?= $counts['portfolio'] ?> Proyek</div>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; color: #34D399; font-size: 1.3rem;">
            <i class="fa-solid fa-layer-group"></i>
          </div>
        </div>
        <div style="margin-top: 14px; border-top: 1px solid var(--admin-border); padding-top: 10px;">
          <a href="portfolio.php" style="color: var(--admin-accent); font-size: 0.82rem; font-weight: 700; text-decoration: none;">Kelola Portofolio &rarr;</a>
        </div>
      </div>

      <div class="admin-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.82rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Supplier &amp; Partner</div>
            <div style="font-size: 1.8rem; font-weight: 900; color: #FFFFFF; margin-top: 4px;"><?= $counts['partners'] ?> Brand</div>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #FBBF24; font-size: 1.3rem;">
            <i class="fa-solid fa-handshake"></i>
          </div>
        </div>
        <div style="margin-top: 14px; border-top: 1px solid var(--admin-border); padding-top: 10px;">
          <a href="partners.php" style="color: var(--admin-accent); font-size: 0.82rem; font-weight: 700; text-decoration: none;">Kelola Partner &rarr;</a>
        </div>
      </div>

      <div class="admin-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.82rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Pertanyaan (FAQ)</div>
            <div style="font-size: 1.8rem; font-weight: 900; color: #FFFFFF; margin-top: 4px;"><?= $counts['faqs'] ?> Tanya Jawab</div>
          </div>
          <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(139, 92, 246, 0.15); display: flex; align-items: center; justify-content: center; color: #A78BFA; font-size: 1.3rem;">
            <i class="fa-solid fa-circle-question"></i>
          </div>
        </div>
        <div style="margin-top: 14px; border-top: 1px solid var(--admin-border); padding-top: 10px;">
          <a href="faq.php" style="color: var(--admin-accent); font-size: 0.82rem; font-weight: 700; text-decoration: none;">Kelola FAQ &rarr;</a>
        </div>
      </div>
    </div>

    <!-- Quick Action Grid -->
    <div class="admin-card">
      <div class="admin-card-header">
        <div>
          <h2 class="admin-card-title">Pintasan Pengaturan Bagian Landing Page</h2>
          <p class="admin-card-subtitle">Pilih bagian yang ingin Anda edit teks, foto, maupun susunannya.</p>
        </div>
      </div>

      <div class="form-row">
        <a href="settings.php" class="admin-card" style="text-decoration: none; border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.05); transition: transform 0.2s ease;">
          <i class="fa-solid fa-palette" style="font-size: 1.6rem; color: #38BDF8; margin-bottom: 12px; display: block;"></i>
          <h4 style="color: #FFFFFF; font-size: 1.05rem; font-weight: 800; margin-bottom: 6px;">Palet Warna &amp; Logo</h4>
          <p style="color: #94A3B8; font-size: 0.85rem;">Ubah warna tombol, background dark, kartu, teks, serta upload logo &amp; favicon website.</p>
        </a>

        <a href="hero.php" class="admin-card" style="text-decoration: none; border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.05); transition: transform 0.2s ease;">
          <i class="fa-solid fa-star" style="font-size: 1.6rem; color: #38BDF8; margin-bottom: 12px; display: block;"></i>
          <h4 style="color: #FFFFFF; font-size: 1.05rem; font-weight: 800; margin-bottom: 6px;">Hero Banner &amp; Mockup</h4>
          <p style="color: #94A3B8; font-size: 0.85rem;">Edit judul utama "Your Business, Our Expertise", tombol CTA WhatsApp, dan upload gambar mockup laptop/HP.</p>
        </a>

        <a href="pricing.php" class="admin-card" style="text-decoration: none; border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.05); transition: transform 0.2s ease;">
          <i class="fa-solid fa-tags" style="font-size: 1.6rem; color: #38BDF8; margin-bottom: 12px; display: block;"></i>
          <h4 style="color: #FFFFFF; font-size: 1.05rem; font-weight: 800; margin-bottom: 6px;">Paket Harga &amp; Promo</h4>
          <p style="color: #94A3B8; font-size: 0.85rem;">Atur harga paket Silver, Gold, Platinum, VIP, diskon coret, badge popular, dan rincian fitur tiap paket.</p>
        </a>

        <a href="portfolio.php" class="admin-card" style="text-decoration: none; border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.05); transition: transform 0.2s ease;">
          <i class="fa-solid fa-layer-group" style="font-size: 1.6rem; color: #38BDF8; margin-bottom: 12px; display: block;"></i>
          <h4 style="color: #FFFFFF; font-size: 1.05rem; font-weight: 800; margin-bottom: 6px;">Galeri Portofolio</h4>
          <p style="color: #94A3B8; font-size: 0.85rem;">Upload screenshot website klien (Lukifays, Vouchershop, dll), kategori filter, dan tautan demo langsung.</p>
        </a>

        <a href="showcase.php" class="admin-card" style="text-decoration: none; border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.05); transition: transform 0.2s ease;">
          <i class="fa-solid fa-display" style="font-size: 1.6rem; color: #38BDF8; margin-bottom: 12px; display: block;"></i>
          <h4 style="color: #FFFFFF; font-size: 1.05rem; font-weight: 800; margin-bottom: 6px;">Showcase Admin Mockup</h4>
          <p style="color: #94A3B8; font-size: 0.85rem;">Ganti teks banner tengah "Website yang dirancang sesuai kebutuhan..." dan 3 angka statistik KPI.</p>
        </a>

        <a href="feature_directory.php" class="admin-card" style="text-decoration: none; border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.05); transition: transform 0.2s ease;">
          <i class="fa-solid fa-list-check" style="font-size: 1.6rem; color: #38BDF8; margin-bottom: 12px; display: block;"></i>
          <h4 style="color: #FFFFFF; font-size: 1.05rem; font-weight: 800; margin-bottom: 6px;">Direktori Fitur Lengkap</h4>
          <p style="color: #94A3B8; font-size: 0.85rem;">Kelola direktori fitur komprehensif berdasarkan kategori: Transaksi, Pembayaran, Reseller, Laporan, dll.</p>
        </a>
      </div>
    </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
