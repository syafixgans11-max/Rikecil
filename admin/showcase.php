<?php
$page_title = 'Kelola Showcase Fitur Interaktif';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update_all';

    if ($action === 'update_all') {
        $showcase_enabled = isset($_POST['showcase_enabled']) ? '1' : '0';
        update_setting($pdo, 'showcase_enabled', $showcase_enabled);
        update_setting($pdo, 'showcase_title', trim($_POST['showcase_title'] ?? ''));
        update_setting($pdo, 'showcase_subtitle', trim($_POST['showcase_subtitle'] ?? ''));

        if (!empty($_POST['tabs'])) {
            foreach ($_POST['tabs'] as $tid => $tdata) {
                $tab_label = trim($tdata['tab_label'] ?? '');
                $address_url = trim($tdata['address_url'] ?? '');
                $caption = trim($tdata['caption'] ?? '');
                $sort_order = (int)($tdata['sort_order'] ?? 0);

                if (!empty($_FILES['tab_image_' . $tid]['name'])) {
                    $uploaded = handle_image_upload('tab_image_' . $tid, 'uploads/');
                    if ($uploaded) {
                        $stmt = $pdo->prepare("UPDATE showcase_tabs SET tab_label = ?, address_url = ?, image = ?, caption = ?, sort_order = ? WHERE id = ?");
                        $stmt->execute([$tab_label, $address_url, $uploaded, $caption, $sort_order, (int)$tid]);
                    }
                } else {
                    $stmt = $pdo->prepare("UPDATE showcase_tabs SET tab_label = ?, address_url = ?, caption = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([$tab_label, $address_url, $caption, $sort_order, (int)$tid]);
                }
            }
        }
        set_flash('success', 'Pengaturan Showcase & seluruh tab berhasil disimpan!');
    } elseif ($action === 'add_tab') {
        $tab_label = trim($_POST['tab_label'] ?? '');
        $address_url = trim($_POST['address_url'] ?? 'toko-anda.com');
        $caption = trim($_POST['caption'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $image = 'assets/images/showcase-customable.svg';

        if (!empty($_FILES['image_file']['name'])) {
            $uploaded = handle_image_upload('image_file', 'uploads/');
            if ($uploaded) $image = $uploaded;
        }

        if ($tab_label) {
            $stmt = $pdo->prepare("INSERT INTO showcase_tabs (tab_label, address_url, image, caption, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tab_label, $address_url, $image, $caption, $sort_order]);
            set_flash('success', 'Tab Showcase baru berhasil ditambahkan!');
        }
    }

    header('Location: showcase.php');
    exit;
}

if (isset($_GET['delete_tab'])) {
    $tid = (int)$_GET['delete_tab'];
    $pdo->prepare("DELETE FROM showcase_tabs WHERE id = ?")->execute([$tid]);
    set_flash('success', 'Tab Showcase berhasil dihapus!');
    header('Location: showcase.php');
    exit;
}

$settings = get_settings($pdo);
$showcase_tabs = $pdo->query("SELECT * FROM showcase_tabs ORDER BY sort_order ASC, id ASC")->fetchAll();
$showcase_enabled = ($settings['showcase_enabled'] ?? '1') === '1';

require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Showcase Fitur (Customable / Checkout / Dashboard)</h1>
      <p>Aktif/nonaktifkan section showcase di landing page serta edit gambar tab pratinjau browser.</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addShowcaseTabModal')">
        <i class="fa-solid fa-plus"></i> Tambah Tab Preview
      </button>
      <button type="submit" form="showcase-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Showcase
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="showcase-form" method="POST" action="" enctype="multipart/form-data">
      <input type="hidden" name="action" value="update_all">

      <!-- Visibility ON/OFF & Headline -->
      <div class="admin-card">
        <div class="admin-card-header">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-toggle-on" style="color: var(--admin-accent); margin-right: 8px;"></i> Status Tampil (ON / OFF) &amp; Judul</h2>
            <p class="admin-card-subtitle">Anda dapat menyembunyikan section ini sewaktu-waktu jika belum diperlukan.</p>
          </div>
          <label class="admin-switch" style="background: #0F141F; padding: 10px 16px; border: 1px solid var(--admin-border); border-radius: 8px;">
            <input type="checkbox" name="showcase_enabled" value="1" <?= $showcase_enabled ? 'checked' : '' ?>>
            <strong style="color: #FFFFFF; font-size: 0.9rem;">Tampilkan Section Showcase (ON)</strong>
          </label>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Utama Showcase</label>
          <input type="text" name="showcase_title" class="form-control" style="font-weight: 700;" value="<?= htmlspecialchars($settings['showcase_title'] ?? 'Nikmati berbagai macam fitur yang telah kami sediakan disini') ?>" required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Sub-Judul Showcase</label>
          <textarea name="showcase_subtitle" class="form-control" rows="2" required><?= htmlspecialchars($settings['showcase_subtitle'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Interactive Browser Tabs List (3 columns grid) -->
      <div class="admin-grid-3">
        <?php foreach ($showcase_tabs as $tab): ?>
          <div class="admin-card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <span class="sidebar-badge">TAB #<?= $tab['sort_order'] ?></span>
              <a href="showcase.php?delete_tab=<?= $tab['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tab preview ini?')">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>

            <div style="margin-bottom: 14px;">
              <img id="tab-preview-<?= $tab['id'] ?>" src="../<?= htmlspecialchars($tab['image']) ?>" alt="Preview" style="width: 100%; aspect-ratio: 16/9; object-fit: cover; border-radius: 8px; border: 1px solid var(--admin-border);">
            </div>

            <div class="form-group">
              <label class="form-label">Nama Tab (Contoh: Customable / Checkout)</label>
              <input type="text" name="tabs[<?= $tab['id'] ?>][tab_label]" class="form-control" value="<?= htmlspecialchars($tab['tab_label']) ?>" required>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">URL Address Bar</label>
                <input type="text" name="tabs[<?= $tab['id'] ?>][address_url]" class="form-control" value="<?= htmlspecialchars($tab['address_url']) ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="tabs[<?= $tab['id'] ?>][sort_order]" class="form-control" value="<?= $tab['sort_order'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Ganti Screenshot Preview Tab</label>
              <input type="file" name="tab_image_<?= $tab['id'] ?>" class="form-control" accept="image/*" data-preview="tab-preview-<?= $tab['id'] ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Caption Penjelasan (Bawah Browser)</label>
              <textarea name="tabs[<?= $tab['id'] ?>][caption]" class="form-control" rows="2"><?= htmlspecialchars($tab['caption']) ?></textarea>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="text-align: right; margin-top: 24px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan Showcase
        </button>
      </div>
    </form>

<!-- MODAL: TAMBAH TAB SHOWCASE -->
<div class="admin-modal-overlay" id="addShowcaseTabModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Tab Pratinjau Browser</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addShowcaseTabModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_tab">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Tab</label>
            <input type="text" name="tab_label" class="form-control" placeholder="Contoh: Laporan Profit" required>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($showcase_tabs) + 1 ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">URL Address Bar Dummy</label>
          <input type="text" name="address_url" class="form-control" placeholder="admin.lukifays-store.com/laporan">
        </div>

        <div class="form-group">
          <label class="form-label">Upload Screenshot Fitur</label>
          <input type="file" name="image_file" class="form-control" accept="image/*" required>
        </div>

        <div class="form-group">
          <label class="form-label">Caption Penjelasan Singkat</label>
          <textarea name="caption" class="form-control" rows="2" placeholder="Penjelasan ringkas mengenai tab fitur ini..."></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addShowcaseTabModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Tab</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
