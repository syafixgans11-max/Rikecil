<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/auth.php';

require_login();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. CTA Banner Fields
    $cta_fields = [
        'cta_badge_text', 'cta_title', 'cta_subtitle',
        'cta_btn_primary_text', 'cta_btn_primary_link',
        'cta_btn_secondary_text', 'cta_btn_secondary_link'
    ];
    foreach ($cta_fields as $f) {
        if (isset($_POST[$f])) {
            update_setting($pdo, $f, trim($_POST[$f]));
        }
    }

    // 2. Footer Bio & Socials
    $footer_fields = [
        'footer_bio', 'footer_wa_link', 'footer_instagram_link', 'footer_telegram_link',
        'footer_col2_title', 'footer_col3_title', 'footer_col4_title',
        'footer_col4_desc', 'footer_col4_btn_text', 'footer_col4_btn_link',
        'footer_copyright'
    ];
    foreach ($footer_fields as $f) {
        if (isset($_POST[$f])) {
            update_setting($pdo, $f, trim($_POST[$f]));
        }
    }

    set_flash('success', 'Pengaturan Banner CTA & Footer berhasil disimpan!');
    header('Location: footer.php');
    exit;
}

$page_title = 'Kelola Banner CTA & Footer';
require_once __DIR__ . '/includes/header.php';
$settings = get_settings($pdo);
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Banner CTA &amp; Footer</h1>
      <p>Kustomisasi teks banner ajakan bertindak (CTA), deskripsi brand, link sosial media, dan teks hak cipta footer.</p>
    </div>
    <div class="topbar-actions">
      <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-eye"></i> Pratinjau Website
      </a>
      <button type="submit" form="footer-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="footer-form" method="POST" action="">

      <!-- SECTION 1: CALL TO ACTION (CTA) BANNER -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-bullhorn" style="color: var(--admin-accent); margin-right: 8px;"></i> Banner Ajakan Bertindak (CTA Banner)</h2>
            <p class="admin-card-subtitle">Banner konversi utama yang tampil sebelum footer di bagian bawah landing page.</p>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="flex: 2;">
            <label class="form-label">Judul Besar Banner CTA</label>
            <input type="text" name="cta_title" class="form-control" value="<?= htmlspecialchars($settings['cta_title'] ?? 'Siap Memulai Website Bisnis Anda?') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi / Penjelasan Banner CTA</label>
          <textarea name="cta_subtitle" class="form-control" rows="3" required><?= htmlspecialchars($settings['cta_subtitle'] ?? 'Bergabunglah dengan 23+ pemilik toko yang sudah berjualan otomatis setiap hari. Konsultasi gratis, tanpa komitmen.') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol Utama (WhatsApp)</label>
            <input type="text" name="cta_btn_primary_text" class="form-control" value="<?= htmlspecialchars($settings['cta_btn_primary_text'] ?? 'Hubungi Kami Sekarang') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Link Tujuan Tombol Utama</label>
            <input type="text" name="cta_btn_primary_link" class="form-control" value="<?= htmlspecialchars($settings['cta_btn_primary_link'] ?? $settings['hero_btn_wa_link'] ?? 'https://wa.me/6281234567890') ?>" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol Kedua (Outline)</label>
            <input type="text" name="cta_btn_secondary_text" class="form-control" value="<?= htmlspecialchars($settings['cta_btn_secondary_text'] ?? 'Pelajari Fiturnya') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Link Tujuan Tombol Kedua</label>
            <input type="text" name="cta_btn_secondary_link" class="form-control" value="<?= htmlspecialchars($settings['cta_btn_secondary_link'] ?? 'fitur.php') ?>">
          </div>
        </div>
      </div>

      <!-- SECTION 2: FOOTER BIO & SOCIAL MEDIA -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-shoe-prints" style="color: var(--admin-accent); margin-right: 8px;"></i> Konten 4-Kolom Footer</h2>
            <p class="admin-card-subtitle">Atur teks bio bisnis pada kolom 1 serta tautan sosial media.</p>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi / Bio Brand (Kolom 1)</label>
          <textarea name="footer_bio" class="form-control" rows="3"><?= htmlspecialchars($settings['footer_bio'] ?? 'Penyedia solusi pembuatan website digital, topup game otomatis, desain poster, dan maskot visual dengan standar teknologi modern.') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> Link WhatsApp Footer</label>
            <input type="text" name="footer_wa_link" class="form-control" value="<?= htmlspecialchars($settings['footer_wa_link'] ?? $settings['hero_btn_wa_link'] ?? 'https://wa.me/6281234567890') ?>">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-instagram" style="color: #E1306C;"></i> Link Instagram Footer</label>
            <input type="text" name="footer_instagram_link" class="form-control" value="<?= htmlspecialchars($settings['footer_instagram_link'] ?? $settings['instagram_url'] ?? 'https://instagram.com') ?>">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-telegram" style="color: #229ED9;"></i> Link Telegram Footer</label>
            <input type="text" name="footer_telegram_link" class="form-control" value="<?= htmlspecialchars($settings['footer_telegram_link'] ?? 'https://t.me') ?>">
          </div>
        </div>
      </div>

      <!-- SECTION 3: FOOTER CONTACT & COPYRIGHT -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-headset" style="color: var(--admin-accent); margin-right: 8px;"></i> Kontak Support &amp; Hak Cipta (Kolom 4)</h2>
            <p class="admin-card-subtitle">Atur deskripsi tim support serta baris teks copyright paling bawah.</p>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Teks Penjelasan Support Teknis (Kolom 4)</label>
          <textarea name="footer_col4_desc" class="form-control" rows="2"><?= htmlspecialchars($settings['footer_col4_desc'] ?? 'Hubungi tim support teknis kami untuk tanya jawab atau demo platform live:') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol Support</label>
            <input type="text" name="footer_col4_btn_text" class="form-control" value="<?= htmlspecialchars($settings['footer_col4_btn_text'] ?? 'WhatsApp Support') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Link Tombol Support</label>
            <input type="text" name="footer_col4_btn_link" class="form-control" value="<?= htmlspecialchars($settings['footer_col4_btn_link'] ?? $settings['hero_btn_wa_link'] ?? 'https://wa.me/6281234567890') ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Teks Hak Cipta (Copyright Paling Bawah)</label>
          <input type="text" name="footer_copyright" class="form-control" value="<?= htmlspecialchars($settings['footer_copyright'] ?? $settings['copyright_text'] ?? '2026 UMKM Digital. Hak Cipta Dilindungi.') ?>" required>
        </div>
      </div>

      <div style="text-align: right; margin-top: 24px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 1rem;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Footer &amp; CTA
        </button>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
