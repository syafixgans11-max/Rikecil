<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/auth.php';

require_login();

// Handle Hero Text / Slider Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update_hero';

    if ($action === 'update_hero') {
        $badge_text = trim($_POST['badge_text'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $btn_primary_text = trim($_POST['btn_primary_text'] ?? '');
        $btn_primary_link = trim($_POST['btn_primary_link'] ?? '');
        $btn_secondary_text = trim($_POST['btn_secondary_text'] ?? '');
        $btn_secondary_link = trim($_POST['btn_secondary_link'] ?? '');

        $stmt = $pdo->prepare("UPDATE hero_section SET 
            badge_text = ?, title = ?, subtitle = ?, 
            btn_primary_text = ?, btn_primary_link = ?, 
            btn_secondary_text = ?, btn_secondary_link = ? 
            WHERE id = 1");
        $stmt->execute([$badge_text, $title, $subtitle, $btn_primary_text, $btn_primary_link, $btn_secondary_text, $btn_secondary_link]);

        // Also allow bulk update of slide metadata (url_label, caption, sort_order)
        if (!empty($_POST['slides'])) {
            $uStmt = $pdo->prepare("UPDATE hero_slides SET url_label = ?, caption = ?, sort_order = ? WHERE id = ?");
            foreach ($_POST['slides'] as $sid => $sdata) {
                $uStmt->execute([
                    trim($sdata['url_label'] ?? ''),
                    trim($sdata['caption'] ?? ''),
                    (int)($sdata['sort_order'] ?? 0),
                    (int)$sid
                ]);
            }
        }

        set_flash('success', 'Konten Hero & konfigurasi slide berhasil disimpan!');
    } elseif ($action === 'add_slide') {
        $url_label = trim($_POST['url_label'] ?? 'toko-anda.com');
        $caption = trim($_POST['caption'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        $desktop_img = '';
        if (!empty($_FILES['desktop_image_file']['name'])) {
            $uploaded = handle_image_upload('desktop_image_file', 'uploads/');
            if ($uploaded) {
                $desktop_img = $uploaded;
            }
        }

        $mobile_img = '';
        if (!empty($_FILES['mobile_image_file']['name'])) {
            $uploadedMob = handle_image_upload('mobile_image_file', 'uploads/');
            if ($uploadedMob) {
                $mobile_img = $uploadedMob;
            }
        }

        if ($desktop_img) {
            if (!$mobile_img) {
                $mobile_img = $desktop_img;
            }
            $stmt = $pdo->prepare("INSERT INTO hero_slides (desktop_image, mobile_image, url_label, caption, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$desktop_img, $mobile_img, $url_label, $caption, $sort_order]);
            set_flash('success', 'Slide Hero baru berhasil ditambahkan ke auto-slider!');
        } else {
            set_flash('danger', 'Harap upload gambar tampilan Desktop terlebih dahulu.');
        }
    }

    header('Location: hero.php');
    exit;
}

if (isset($_GET['delete_slide'])) {
    $sid = (int)$_GET['delete_slide'];
    $pdo->prepare("DELETE FROM hero_slides WHERE id = ?")->execute([$sid]);
    set_flash('success', 'Slide Hero berhasil dihapus!');
    header('Location: hero.php');
    exit;
}

$page_title = 'Kelola Hero & Auto-Slider Showcase';
require_once __DIR__ . '/includes/header.php';
$hero = $pdo->query("SELECT * FROM hero_section WHERE id = 1")->fetch();
$hero_slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Hero &amp; Auto-Slider Showcase</h1>
      <p>Edit teks headline utama dan kelola beberapa gambar slide mockup (Browser Desktop + Mobile) agar auto-slide.</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addHeroSlideModal')">
        <i class="fa-solid fa-plus"></i> Tambah Slide Mockup
      </button>
      <button type="submit" form="hero-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Hero
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="hero-form" method="POST" action="">
      <input type="hidden" name="action" value="update_hero">

      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-pen-nib" style="color: var(--admin-accent); margin-right: 8px;"></i> Teks Headline &amp; Tombol Aksi</h2>
            <p class="admin-card-subtitle">Semua teks pembuka di layar utama landing page.</p>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Label Kecil (Atas Headline)</label>
          <input type="text" name="badge_text" class="form-control" value="<?= htmlspecialchars($hero['badge_text'] ?? '') ?>" required>
          <div class="form-help">Teks label bersih di atas judul utama (Contoh: LAYANAN PEMBUATAN WEBSITE RESELLER).</div>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Utama (Headline Besar)</label>
          <input type="text" name="title" class="form-control" style="font-size: 1.1rem; font-weight: 700;" value="<?= htmlspecialchars($hero['title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Sub-Headline (Penjelasan Singkat)</label>
          <textarea name="subtitle" class="form-control" rows="4" required><?= htmlspecialchars($hero['subtitle'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol Utama (Primary Button)</label>
            <input type="text" name="btn_primary_text" class="form-control" value="<?= htmlspecialchars($hero['btn_primary_text'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Link Tujuan Tombol Utama</label>
            <input type="text" name="btn_primary_link" class="form-control" value="<?= htmlspecialchars($hero['btn_primary_link'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol Kedua (Secondary Button)</label>
            <input type="text" name="btn_secondary_text" class="form-control" value="<?= htmlspecialchars($hero['btn_secondary_text'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Link Tujuan Tombol Kedua</label>
            <input type="text" name="btn_secondary_link" class="form-control" value="<?= htmlspecialchars($hero['btn_secondary_link'] ?? '') ?>" required>
          </div>
        </div>
      </div>

      <!-- MULTI-IMAGE AUTO SLIDER MANAGER -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-images" style="color: var(--admin-accent); margin-right: 8px;"></i> Daftar Slide Mockup Hero (Auto-Slide)</h2>
            <p class="admin-card-subtitle">Upload beberapa screenshot agar showcase browser &amp; smartphone di samping hero berganti otomatis.</p>
          </div>
          <button type="button" class="btn btn-primary btn-sm" onclick="openAdminModal('addHeroSlideModal')">
            <i class="fa-solid fa-plus"></i> Tambah Slide
          </button>
        </div>

        <div class="admin-grid-3">
          <?php foreach ($hero_slides as $idx => $s): ?>
            <div style="background: #0F141F; border: 1px solid var(--admin-border); border-radius: 10px; padding: 16px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="sidebar-badge">SLIDE #<?= $idx + 1 ?></span>
                <a href="hero.php?delete_slide=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus slide ini?')">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>

              <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 10px; margin-bottom: 14px;">
                <div>
                  <div style="font-size: 0.72rem; color: var(--admin-muted); margin-bottom: 4px;">Preview Desktop</div>
                  <img src="../<?= htmlspecialchars($s['desktop_image']) ?>" alt="Desktop Slide" style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);">
                </div>
                <div>
                  <div style="font-size: 0.72rem; color: var(--admin-muted); margin-bottom: 4px;">Preview Mobile</div>
                  <img src="../<?= htmlspecialchars(!empty($s['mobile_image']) ? $s['mobile_image'] : $s['desktop_image']) ?>" alt="Mobile Slide" style="width: 100%; aspect-ratio: 9/16; max-height: 92px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);">
                </div>
              </div>

              <div class="form-group" style="margin-bottom: 10px;">
                <label class="form-label" style="font-size: 0.78rem;">URL Bar Browser Mockup</label>
                <input type="text" name="slides[<?= $s['id'] ?>][url_label]" class="form-control" value="<?= htmlspecialchars($s['url_label'] ?? '') ?>">
              </div>

              <div class="form-group" style="margin-bottom: 10px;">
                <label class="form-label" style="font-size: 0.78rem;">Judul / Keterangan Slide</label>
                <input type="text" name="slides[<?= $s['id'] ?>][caption]" class="form-control" value="<?= htmlspecialchars($s['caption'] ?? '') ?>">
              </div>

              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" style="font-size: 0.78rem;">Urutan</label>
                <input type="number" name="slides[<?= $s['id'] ?>][sort_order]" class="form-control" value="<?= $s['sort_order'] ?>">
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div style="text-align: right; margin-top: 24px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan Hero
        </button>
      </div>
    </form>

<!-- MODAL: TAMBAH SLIDE MOCKUP HERO -->
<div class="admin-modal-overlay" id="addHeroSlideModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Slide Auto-Slider Hero</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addHeroSlideModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_slide">

        <div class="form-group">
          <label class="form-label">Gambar Tampilan Desktop (Wajib)</label>
          <input type="file" name="desktop_image_file" class="form-control" accept="image/*" required>
          <div class="form-help">Tampil di jendela browser utama (Rasio landscape 16:10, format PNG/JPG/WebP/SVG).</div>
        </div>

        <div class="form-group">
          <label class="form-label">Gambar Tampilan Mobile (Opsional)</label>
          <input type="file" name="mobile_image_file" class="form-control" accept="image/*">
          <div class="form-help">Tampil di bingkai HP pojok kanan bawah. Jika kosong, otomatis memakai gambar desktop.</div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks URL di Address Bar</label>
            <input type="text" name="url_label" class="form-control" value="lukifays.id/store-demo" required>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($hero_slides) + 1 ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Keterangan / Alt Gambar</label>
          <input type="text" name="caption" class="form-control" placeholder="Contoh: Tampilan Katalog Mobile Legends">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addHeroSlideModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i> Upload Slide</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
