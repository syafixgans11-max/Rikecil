<?php
$page_title = 'Kelola Direktori Fitur Lengkap';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $category_code = trim($_POST['category_code'] ?? 'transaksi');
        $category_name = trim($_POST['category_name'] ?? 'Transaksi & Otomasi');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'circle-check');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($title) {
            $stmt = $pdo->prepare("INSERT INTO expanded_features (category_code, category_name, title, description, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category_code, $category_name, $title, $description, $icon, $sort_order]);
            set_flash('success', 'Fitur direktori baru berhasil ditambahkan!');
        }
    } elseif ($action === 'update_all') {
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $id => $data) {
                $stmt = $pdo->prepare("UPDATE expanded_features SET category_code = ?, category_name = ?, title = ?, description = ?, icon = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($data['category_code']),
                    trim($data['category_name']),
                    trim($data['title']),
                    trim($data['description']),
                    trim($data['icon']),
                    (int)$data['sort_order'],
                    (int)$id
                ]);
            }
            set_flash('success', 'Semua fitur direktori berhasil diperbarui!');
        }
    }

    header('Location: feature_directory.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM expanded_features WHERE id = ?")->execute([$id]);
    set_flash('success', 'Fitur berhasil dihapus!');
    header('Location: feature_directory.php');
    exit;
}

$items = $pdo->query("SELECT * FROM expanded_features ORDER BY category_code ASC, sort_order ASC, id ASC")->fetchAll();
$cats = [];
foreach ($items as $it) {
    $cats[$it['category_code']] = $it['category_name'];
}

require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Direktori Fitur Lengkap (fitur.php)</h1>
      <p>Katalog fitur mendalam halaman fitur.php (maksimal 4 kartu per baris dengan filter kategori di atas).</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addDirModal')">
        <i class="fa-solid fa-plus"></i> Tambah Fitur
      </button>
      <button type="submit" form="dir-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Fitur
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Category Filter Tabs -->
    <div class="admin-filter-tabs">
      <button type="button" class="admin-tab-btn active" data-dir-filter="all">Semua Kategori (<?= count($items) ?>)</button>
      <?php foreach ($cats as $code => $name): ?>
        <button type="button" class="admin-tab-btn" data-dir-filter="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($name) ?></button>
      <?php endforeach; ?>
    </div>

    <form id="dir-form" method="POST" action="">
      <input type="hidden" name="action" value="update_all">

      <!-- Strictly Max 4 Columns Grid -->
      <div class="admin-grid-4">
        <?php foreach ($items as $item): ?>
          <div class="admin-card admin-dir-card" data-dir-cat="<?= htmlspecialchars($item['category_code']) ?>" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span class="sidebar-badge" style="font-size: 0.78rem; border-radius: 6px; padding: 4px 10px;">
                <?= htmlspecialchars($item['category_name']) ?>
              </span>
              <a href="feature_directory.php?delete=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus fitur ini?')">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Kode Tab</label>
                <input type="text" name="items[<?= $item['id'] ?>][category_code]" class="form-control" value="<?= htmlspecialchars($item['category_code']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="items[<?= $item['id'] ?>][category_name]" class="form-control" value="<?= htmlspecialchars($item['category_name']) ?>" required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Judul Fitur</label>
              <input type="text" name="items[<?= $item['id'] ?>][title]" class="form-control" value="<?= htmlspecialchars($item['title']) ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Penjelasan</label>
              <textarea name="items[<?= $item['id'] ?>][description]" class="form-control" rows="3" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Icon FontAwesome</label>
                <input type="text" name="items[<?= $item['id'] ?>][icon]" class="form-control" value="<?= htmlspecialchars($item['icon']) ?>" required>
              </div>
              <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Urutan</label>
                <input type="number" name="items[<?= $item['id'] ?>][sort_order]" class="form-control" value="<?= $item['sort_order'] ?>">
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </form>

<!-- MODAL: TAMBAH FITUR DIREKTORI -->
<div class="admin-modal-overlay" id="addDirModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Fitur ke Halaman Katalog (fitur.php)</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addDirModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kategori</label>
            <select name="category_code" class="form-control" onchange="this.form.category_name.value = this.options[this.selectedIndex].text;">
              <option value="transaksi">Transaksi &amp; Otomasi</option>
              <option value="pembayaran">Pembayaran</option>
              <option value="supplier">Produk &amp; Supplier</option>
              <option value="marketing">Toko &amp; Marketing</option>
              <option value="reseller">Member &amp; Reseller</option>
              <option value="laporan">Laporan &amp; Analitik</option>
            </select>
            <input type="hidden" name="category_name" value="Transaksi &amp; Otomasi">
          </div>

          <div class="form-group">
            <label class="form-label">Icon FontAwesome</label>
            <input type="text" name="icon" class="form-control" value="circle-check" required>
          </div>

          <div class="form-group">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" class="form-control" value="1">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Fitur</label>
          <input type="text" name="title" class="form-control" placeholder="Contoh: Webhook Realtime Callback" required>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan keterangan fitur..." required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addDirModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambahkan Fitur</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.admin-tab-btn[data-dir-filter]').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.admin-tab-btn[data-dir-filter]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.getAttribute('data-dir-filter');
    document.querySelectorAll('.admin-dir-card').forEach(card => {
      card.style.display = (filter === 'all' || card.getAttribute('data-dir-cat') === filter) ? 'block' : 'none';
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
