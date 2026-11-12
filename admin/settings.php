<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/auth.php';

require_login();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Update text & color settings
    $text_fields = [
        'site_title', 'site_tagline',
        'primary_color', 'accent_color', 'bg_dark', 'bg_card', 'border_color',
        'text_primary', 'text_secondary',
        'contact_whatsapp', 'contact_email', 'contact_telegram', 'instagram_url',
        'nav_cta_text', 'nav_cta_link',
        'footer_text', 'copyright_text'
    ];

    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_setting($pdo, $field, trim($_POST[$field]));
        }
    }

    // 2. Handle Logo Upload
    if (!empty($_FILES['site_logo_file']['name'])) {
        $uploaded = handle_image_upload('site_logo_file', 'uploads/');
        if ($uploaded) {
            update_setting($pdo, 'site_logo', $uploaded);
        }
    }

    // 3. Handle Favicon Upload
    if (!empty($_FILES['site_favicon_file']['name'])) {
        $uploaded = handle_image_upload('site_favicon_file', 'uploads/');
        if ($uploaded) {
            update_setting($pdo, 'site_favicon', $uploaded);
        }
    }

    set_flash('success', 'Pengaturan warna, logo, dan identitas berhasil disimpan!');
    header('Location: settings.php');
    exit;
}

$page_title = 'Pengaturan Warna, Logo & Identitas';
require_once __DIR__ . '/includes/header.php';
$settings = get_settings($pdo);
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Pengaturan Warna, Logo &amp; Identitas</h1>
      <p>Kustomisasi skema warna CSS, logo brand, dan informasi kontak landing page Anda.</p>
    </div>
    <div class="topbar-actions">
      <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-eye"></i> Pratinjau Website
      </a>
      <button type="submit" form="settings-form" class="btn btn-primary btn-sm">
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

    <form id="settings-form" method="POST" action="" enctype="multipart/form-data">

      <!-- THEME COLOR CUSTOMIZER -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-palette" style="color: var(--admin-accent); margin-right: 8px;"></i> Kustomisasi Palet Warna Tema (CSS Variables)</h2>
            <p class="admin-card-subtitle">Ubah kode warna hex di bawah ini, seluruh komponen landing page (tombol, glow, background, kartu) akan berubah seketika.</p>
          </div>
        </div>

        <div class="admin-grid-3">
          <!-- Primary Color -->
          <div class="form-group">
            <label class="form-label">Warna Utama (Primary / Tombol &amp; Glow)</label>
            <div class="color-picker-group">
              <input type="color" class="color-swatch-input" data-sync="primary_color" value="<?= htmlspecialchars($settings['primary_color'] ?? '#2563EB') ?>">
              <input type="text" id="primary_color" name="primary_color" class="form-control" value="<?= htmlspecialchars($settings['primary_color'] ?? '#2563EB') ?>" required>
            </div>
            <div class="form-help">Warna tombol utama, sorotan kartu, &amp; aksen border.</div>
          </div>

          <!-- Accent Color -->
          <div class="form-group">
            <label class="form-label">Warna Aksen (Neon Cyan / Icon / Badge)</label>
            <div class="color-picker-group">
              <input type="color" class="color-swatch-input" data-sync="accent_color" value="<?= htmlspecialchars($settings['accent_color'] ?? '#06B6D4') ?>">
              <input type="text" id="accent_color" name="accent_color" class="form-control" value="<?= htmlspecialchars($settings['accent_color'] ?? '#06B6D4') ?>" required>
            </div>
            <div class="form-help">Warna icon fitur, teks gradient, &amp; badge keunggulan.</div>
          </div>

          <!-- Background Dark -->
          <div class="form-group">
            <label class="form-label">Warna Background Utama (Dark Canvas)</label>
            <div class="color-picker-group">
              <input type="color" class="color-swatch-input" data-sync="bg_dark" value="<?= htmlspecialchars($settings['bg_dark'] ?? '#0B0E14') ?>">
              <input type="text" id="bg_dark" name="bg_dark" class="form-control" value="<?= htmlspecialchars($settings['bg_dark'] ?? '#0B0E14') ?>" required>
            </div>
            <div class="form-help">Warna latar belakang keseluruhan website.</div>
          </div>

          <!-- Background Card -->
          <div class="form-group">
            <label class="form-label">Warna Permukaan Kartu (Card Surface)</label>
            <div class="color-picker-group">
              <input type="color" class="color-swatch-input" data-sync="bg_card" value="<?= htmlspecialchars($settings['bg_card'] ?? '#161C26') ?>">
              <input type="text" id="bg_card" name="bg_card" class="form-control" value="<?= htmlspecialchars($settings['bg_card'] ?? '#161C26') ?>" required>
            </div>
            <div class="form-help">Warna latar belakang kartu fitur &amp; paket harga.</div>
          </div>

          <!-- Border Color -->
          <div class="form-group">
            <label class="form-label">Warna Garis Tepi (Card Border Stroke)</label>
            <div class="color-picker-group">
              <input type="color" class="color-swatch-input" data-sync="border_color" value="<?= htmlspecialchars($settings['border_color'] ?? '#242F42') ?>">
              <input type="text" id="border_color" name="border_color" class="form-control" value="<?= htmlspecialchars($settings['border_color'] ?? '#242F42') ?>" required>
            </div>
            <div class="form-help">Garis stroke halus di sekeliling kartu &amp; navbar.</div>
          </div>

          <!-- Text Secondary -->
          <div class="form-group">
            <label class="form-label">Warna Teks Sekunder (Subtitle &amp; Body)</label>
            <div class="color-picker-group">
              <input type="color" class="color-swatch-input" data-sync="text_secondary" value="<?= htmlspecialchars($settings['text_secondary'] ?? '#94A3B8') ?>">
              <input type="text" id="text_secondary" name="text_secondary" class="form-control" value="<?= htmlspecialchars($settings['text_secondary'] ?? '#94A3B8') ?>" required>
            </div>
            <div class="form-help">Warna deskripsi, paragraf, dan keterangan.</div>
          </div>
        </div>

        <!-- Live Color Preview Box -->
        <div style="margin-top: 20px; padding: 22px; border-radius: 12px; background: #0E131E; border: 1px solid var(--admin-border);">
          <div style="font-size: 0.82rem; font-weight: 700; color: #64748B; text-transform: uppercase; margin-bottom: 12px;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Pratinjau Komponen Warna Langsung
          </div>
          <div id="theme-preview-box" style="padding: 24px; border-radius: 14px; background: var(--preview-bg-card, #161C26); border: 1px solid var(--preview-border, #242F42); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
              <span style="font-size: 0.72rem; font-weight: 800; color: var(--preview-accent, #06B6D4); letter-spacing: 1px; text-transform: uppercase; display: block; margin-bottom: 4px;">CONTOH BADGE</span>
              <h4 style="font-size: 1.15rem; font-weight: 800; color: #FFFFFF;">Judul Kartu Berwarna Baru</h4>
              <p style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px;">Teks deskripsi akan tampak seperti ini di landing page pengunjung.</p>
            </div>
            <div>
              <button type="button" class="btn" style="background: var(--preview-primary, #2563EB); color: #FFFFFF; pointer-events: none; padding: 10px 20px; font-size: 0.88rem; border-radius: 8px;">
                Contoh Tombol CTA
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- BRANDING & LOGO UPLOAD -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-image" style="color: var(--admin-accent); margin-right: 8px;"></i> Logo &amp; Favicon Website</h2>
            <p class="admin-card-subtitle">Upload file logo dan ikon favicon (Format: SVG, PNG, WebP, JPG).</p>
          </div>
        </div>

        <div class="form-row">
          <!-- Logo File -->
          <div class="form-group">
            <label class="form-label">Logo Header Website</label>
            <div class="image-upload-box">
              <img id="logo-preview-img" class="preview-thumbnail" src="../<?= htmlspecialchars($settings['site_logo'] ?? 'assets/images/logo.svg') ?>" alt="Logo">
              <div style="flex-grow: 1;">
                <input type="file" name="site_logo_file" class="form-control" accept="image/*" data-preview="logo-preview-img">
                <div class="form-help">Pilih gambar logo baru untuk mengganti logo default.</div>
              </div>
            </div>
          </div>

          <!-- Favicon File -->
          <div class="form-group">
            <label class="form-label">Favicon Tab Browser</label>
            <div class="image-upload-box">
              <img id="favicon-preview-img" class="preview-thumbnail" style="width: 44px; height: 44px;" src="../<?= htmlspecialchars($settings['site_favicon'] ?? 'assets/images/logo.svg') ?>" alt="Favicon">
              <div style="flex-grow: 1;">
                <input type="file" name="site_favicon_file" class="form-control" accept="image/*" data-preview="favicon-preview-img">
                <div class="form-help">Ikon kecil yang muncul di tab browser pengguna.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Judul Website (Browser Meta Title)</label>
            <input type="text" name="site_title" class="form-control" value="<?= htmlspecialchars($settings['site_title'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Tagline Bisnis (Meta Description)</label>
            <input type="text" name="site_tagline" class="form-control" value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>" required>
          </div>
        </div>
      </div>

      <!-- NAVBAR & CONTACT INFORMATION -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-address-book" style="color: var(--admin-accent); margin-right: 8px;"></i> Kontak &amp; Tombol Aksi Navbar</h2>
            <p class="admin-card-subtitle">Kelola nomor WhatsApp, link media sosial, dan tujuan tombol aksi.</p>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol Navbar (Kanan Atas)</label>
            <input type="text" name="nav_cta_text" class="form-control" value="<?= htmlspecialchars($settings['nav_cta_text'] ?? 'Hubungi Kami') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Link Tujuan Tombol Navbar</label>
            <input type="text" name="nav_cta_link" class="form-control" value="<?= htmlspecialchars($settings['nav_cta_link'] ?? 'https://wa.me/6281234567890') ?>" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> Nomor WhatsApp Bisnis</label>
            <input type="text" name="contact_whatsapp" class="form-control" placeholder="Contoh: 6281234567890 (Gunakan kode negara 62)" value="<?= htmlspecialchars($settings['contact_whatsapp'] ?? '') ?>">
            <div class="form-help">Format angka tanpa tanda '+' atau spasi (contoh: 6281234567890).</div>
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-envelope" style="color: #38BDF8;"></i> Alamat Email</label>
            <input type="email" name="contact_email" class="form-control" placeholder="halo@umkmdigital.id" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-telegram" style="color: #229ED9;"></i> Akun Telegram Support</label>
            <input type="text" name="contact_telegram" class="form-control" placeholder="username_telegram" value="<?= htmlspecialchars($settings['contact_telegram'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-instagram" style="color: #E1306C;"></i> URL Instagram</label>
            <input type="url" name="instagram_url" class="form-control" placeholder="https://instagram.com/umkmdigital" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Singkat Footer</label>
          <textarea name="footer_text" class="form-control" rows="3"><?= htmlspecialchars($settings['footer_text'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Teks Hak Cipta (Copyright Footer)</label>
          <input type="text" name="copyright_text" class="form-control" value="<?= htmlspecialchars($settings['copyright_text'] ?? '') ?>">
        </div>
      </div>

      <div style="text-align: right; margin-top: 24px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 1rem;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan
        </button>
      </div>
    </form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
