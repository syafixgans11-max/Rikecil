<?php
$page_title = 'Kelola "Apa yang Anda Dapatkan"';
require_once __DIR__ . '/includes/header.php';

$settings = get_settings($pdo);
$tab1 = $settings['features_tab_1'] ?? 'Website';
$tab2 = $settings['features_tab_2'] ?? 'Poster Design';
$tab3 = $settings['features_tab_3'] ?? 'Character';

// Handle Add / Edit / Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $category = trim($_POST['category'] ?? $tab1);
        $icon = trim($_POST['icon'] ?? 'check');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($title) {
            $stmt = $pdo->prepare("INSERT INTO feature_highlights (category, icon, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$category, $icon, $title, $description, $sort_order]);
            set_flash('success', 'Fitur baru berhasil ditambahkan!');
        }
    } elseif ($action === 'update_all') {
        // Save customizable 3 category labels if modified
        if (isset($_POST['features_tab_1'])) update_setting($pdo, 'features_tab_1', trim($_POST['features_tab_1']));
        if (isset($_POST['features_tab_2'])) update_setting($pdo, 'features_tab_2', trim($_POST['features_tab_2']));
        if (isset($_POST['features_tab_3'])) update_setting($pdo, 'features_tab_3', trim($_POST['features_tab_3']));

        if (!empty($_POST['features'])) {
            foreach ($_POST['features'] as $id => $data) {
                $stmt = $pdo->prepare("UPDATE feature_highlights SET category = ?, icon = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($data['category'] ?? $tab1),
                    trim($data['icon']),
                    trim($data['title']),
                    trim($data['description']),
                    (int)$data['sort_order'],
                    (int)$id
                ]);
            }
        }
        set_flash('success', 'Semua fitur & nama kategori tab berhasil diperbarui!');
    }

    header('Location: features.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM feature_highlights WHERE id = ?")->execute([$id]);
    set_flash('success', 'Fitur berhasil dihapus!');
    header('Location: features.php');
    exit;
}

$settings = get_settings($pdo);
$tab1 = $settings['features_tab_1'] ?? 'Website';
$tab2 = $settings['features_tab_2'] ?? 'Poster Design';
$tab3 = $settings['features_tab_3'] ?? 'Character';
$allTabs = [$tab1, $tab2, $tab3];

$features = $pdo->query("SELECT * FROM feature_highlights ORDER BY category ASC, sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola "Apa yang Anda Dapatkan"</h1>
      <p>Kelola 3 opsi tab kategori (maks. 8 fitur per tab; tombol "Lihat Semua Fitur" khusus Tab 1).</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addHighlightModal')">
        <i class="fa-solid fa-plus"></i> Tambah Fitur
      </button>
      <button type="submit" form="features-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="features-form" method="POST" action="">
      <input type="hidden" name="action" value="update_all">

      <!-- Customize the 3 Tab Names -->
      <div class="admin-card" style="margin-bottom: 24px;">
        <div class="admin-card-header" style="margin-bottom: 14px; padding-bottom: 10px;">
          <div>
            <h2 class="admin-card-title"><i class="fa-solid fa-sliders" style="color: var(--admin-accent);"></i> Nama 3 Opsi Tab Kategori</h2>
            <p class="admin-card-subtitle">Pilihan 1 (kiri) otomatis menampilkan tombol "Lihat Semua Fitur" di bawah gridnya.</p>
          </div>
        </div>
        <div class="admin-grid-3">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tab 1 (Utama - Ada Lihat Semua Fitur)</label>
            <input type="text" name="features_tab_1" class="form-control" value="<?= htmlspecialchars($tab1) ?>" required>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tab 2 (Tanpa Tombol Lihat Semua)</label>
            <input type="text" name="features_tab_2" class="form-control" value="<?= htmlspecialchars($tab2) ?>" required>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tab 3 (Tanpa Tombol Lihat Semua)</label>
            <input type="text" name="features_tab_3" class="form-control" value="<?= htmlspecialchars($tab3) ?>" required>
          </div>
        </div>
      </div>

      <!-- Filter Tabs inside Admin -->
      <div class="admin-filter-tabs">
        <button type="button" class="admin-tab-btn active" data-cat-filter="all">Semua Tab (<?= count($features) ?>)</button>
        <?php foreach ($allTabs as $tName): ?>
          <button type="button" class="admin-tab-btn" data-cat-filter="<?= htmlspecialchars($tName) ?>"><?= htmlspecialchars($tName) ?></button>
        <?php endforeach; ?>
      </div>

      <!-- Strictly Max 4 Columns Grid -->
      <div class="admin-grid-4">
        <?php foreach ($features as $f): 
          $fCat = !empty($f['category']) ? $f['category'] : $tab1;
        ?>
          <div class="admin-card admin-feat-item" data-item-cat="<?= htmlspecialchars($fCat) ?>" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span class="sidebar-badge" style="border-radius: 6px; padding: 3px 9px;">
                <?= htmlspecialchars($fCat) ?>
              </span>
              <a href="features.php?delete=<?= $f['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus fitur ini?')">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>

            <div class="form-group">
              <label class="form-label">Kategori Tab</label>
              <select name="features[<?= $f['id'] ?>][category]" class="form-control">
                <?php foreach ($allTabs as $tName): ?>
                  <option value="<?= htmlspecialchars($tName) ?>" <?= $fCat === $tName ? 'selected' : '' ?>><?= htmlspecialchars($tName) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Icon FA</label>
                <input type="text" name="features[<?= $f['id'] ?>][icon]" class="form-control" value="<?= htmlspecialchars($f['icon']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="features[<?= $f['id'] ?>][sort_order]" class="form-control" value="<?= $f['sort_order'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Judul Fitur</label>
              <input type="text" name="features[<?= $f['id'] ?>][title]" class="form-control" value="<?= htmlspecialchars($f['title']) ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Deskripsi</label>
              <textarea name="features[<?= $f['id'] ?>][description]" class="form-control" rows="3" required><?= htmlspecialchars($f['description']) ?></textarea>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </form>

<!-- MODAL: TAMBAH FITUR BARU -->
<div class="admin-modal-overlay" id="addHighlightModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Kartu "Apa yang Anda Dapatkan"</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addHighlightModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kategori Tab</label>
            <select name="category" class="form-control">
              <?php foreach ($allTabs as $tName): ?>
                <option value="<?= htmlspecialchars($tName) ?>"><?= htmlspecialchars($tName) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="8">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Icon FontAwesome</label>
            <input type="text" name="icon" class="form-control" placeholder="bolt, shield-halved, tag" value="circle-check" required>
          </div>
          <div class="form-group">
            <label class="form-label">Judul Fitur</label>
            <input type="text" name="title" class="form-control" placeholder="Contoh: Validasi ID Instan" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Singkat</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan manfaat fitur..." required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addHighlightModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Fitur</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.admin-tab-btn[data-cat-filter]').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.admin-tab-btn[data-cat-filter]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.getAttribute('data-cat-filter');
    document.querySelectorAll('.admin-feat-item').forEach(card => {
      card.style.display = (filter === 'all' || card.getAttribute('data-item-cat') === filter) ? 'block' : 'none';
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
