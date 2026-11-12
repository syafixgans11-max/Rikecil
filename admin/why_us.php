<?php
$page_title = 'Kelola "Kenapa Harus Di Kami"';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $icon = trim($_POST['icon'] ?? 'star');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($title) {
            $stmt = $pdo->prepare("INSERT INTO why_us (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$icon, $title, $description, $sort_order]);
            set_flash('success', 'Kartu keunggulan baru berhasil ditambahkan!');
        }
    } elseif ($action === 'update_all') {
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $id => $data) {
                $stmt = $pdo->prepare("UPDATE why_us SET icon = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($data['icon']),
                    trim($data['title']),
                    trim($data['description']),
                    (int)$data['sort_order'],
                    (int)$id
                ]);
            }
            set_flash('success', 'Semua kartu "Kenapa Harus Di Kami" berhasil diperbarui!');
        }
    }

    header('Location: why_us.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM why_us WHERE id = ?")->execute([$id]);
    set_flash('success', 'Kartu berhasil dihapus!');
    header('Location: why_us.php');
    exit;
}

$items = $pdo->query("SELECT * FROM why_us ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola "Kenapa Harus Di Kami ?"</h1>
      <p>Edit kartu keunggulan utama website (tampilan 3 kolom agar lega dan rapi).</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addWhyModal')">
        <i class="fa-solid fa-plus"></i> Tambah Kartu
      </button>
      <button type="submit" form="why-us-form" class="btn btn-primary btn-sm">
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

    <form id="why-us-form" method="POST" action="">
      <input type="hidden" name="action" value="update_all">

      <div class="admin-grid-3">
        <?php foreach ($items as $item): ?>
          <div class="admin-card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
              <span style="font-weight: 800; font-size: 0.85rem; color: var(--admin-accent);">
                <i class="fa-solid fa-<?= htmlspecialchars($item['icon']) ?>"></i> KARTU #<?= $item['id'] ?>
              </span>
              <a href="why_us.php?delete=<?= $item['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus kartu ini?')">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Icon FontAwesome</label>
                <input type="text" name="items[<?= $item['id'] ?>][icon]" class="form-control" value="<?= htmlspecialchars($item['icon']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="items[<?= $item['id'] ?>][sort_order]" class="form-control" value="<?= $item['sort_order'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Judul Keunggulan</label>
              <input type="text" name="items[<?= $item['id'] ?>][title]" class="form-control" value="<?= htmlspecialchars($item['title']) ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Deskripsi Penjelasan</label>
              <textarea name="items[<?= $item['id'] ?>][description]" class="form-control" rows="4" required><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </form>

<!-- MODAL: TAMBAH KARTU KEUNGGULAN -->
<div class="admin-modal-overlay" id="addWhyModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Kartu Keunggulan Baru</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addWhyModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Icon FontAwesome</label>
            <input type="text" name="icon" class="form-control" placeholder="sliders, server, gamepad, shield-halved" value="shield-halved" required>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($items) + 1 ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Keunggulan</label>
          <input type="text" name="title" class="form-control" placeholder="Contoh: Dukungan Teknis 24 Jam" required>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Penjelasan</label>
          <textarea name="description" class="form-control" rows="4" placeholder="Tuliskan penjelasan lengkap..." required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addWhyModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambahkan Kartu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
