<?php
$page_title = 'Kelola Partner & Supplier H2H';
require_once __DIR__ . '/includes/header.php';

// Handle Add / Edit / Update Title
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_title') {
        $title = trim($_POST['partners_title'] ?? 'TERINTEGRASI DENGAN SUPPLIER TERNAMA');
        update_setting($pdo, 'partners_title', $title);
        set_flash('success', 'Judul section supplier berhasil diperbarui!');
    } elseif ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $display_mode = trim($_POST['display_mode'] ?? 'logo_text');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        $logo_image = '';
        if (!empty($_FILES['logo_file']['name'])) {
            $uploaded = handle_image_upload('logo_file', 'uploads/');
            if ($uploaded) {
                $logo_image = $uploaded;
            }
        }

        if ($name) {
            $stmt = $pdo->prepare("INSERT INTO partners (name, logo_type, display_mode, logo_image, sort_order) VALUES (?, 'image', ?, ?, ?)");
            $stmt->execute([$name, $display_mode, $logo_image, $sort_order]);
            set_flash('success', 'Partner baru berhasil ditambahkan!');
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $display_mode = trim($_POST['display_mode'] ?? 'logo_text');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($id && $name) {
            if (!empty($_FILES['logo_file']['name'])) {
                $uploaded = handle_image_upload('logo_file', 'uploads/');
                if ($uploaded) {
                    $stmt = $pdo->prepare("UPDATE partners SET name = ?, display_mode = ?, logo_image = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([$name, $display_mode, $uploaded, $sort_order, $id]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE partners SET name = ?, display_mode = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([$name, $display_mode, $sort_order, $id]);
            }
            set_flash('success', 'Partner berhasil diperbarui!');
        }
    }

    header('Location: partners.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM partners WHERE id = ?")->execute([$id]);
    set_flash('success', 'Partner berhasil dihapus!');
    header('Location: partners.php');
    exit;
}

$partners = $pdo->query("SELECT * FROM partners ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';

$modeLabels = [
    'logo_text' => 'Logo + Teks',
    'logo_only' => 'Hanya Logo',
    'text_only' => 'Hanya Teks'
];
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Partner &amp; Supplier H2H</h1>
      <p>Upload logo supplier dan pilih mode tampil: Logo + Teks, Hanya Logo, atau Hanya Teks.</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="openAdminModal('addPartnerModal')">
        <i class="fa-solid fa-plus"></i> Tambah Supplier
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Card: Atur Judul Section Supplier -->
    <div class="admin-card" style="margin-bottom: 24px;">
      <div class="admin-card-header">
        <div>
          <h2 class="admin-card-title"><i class="fa-solid fa-heading" style="color: var(--admin-accent);"></i> Judul Section Supplier</h2>
          <p class="admin-card-subtitle">Ubah teks judul kecil yang tampil di atas animasi berjalan logo supplier.</p>
        </div>
      </div>
      <form method="POST" action="" style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="action" value="update_title">
        <div class="form-group" style="flex: 1; min-width: 280px; margin-bottom: 0;">
          <label class="form-label">Teks Judul</label>
          <input type="text" name="partners_title" class="form-control" value="<?= htmlspecialchars($settings['partners_title'] ?? 'TERINTEGRASI DENGAN SUPPLIER TERNAMA') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Judul
        </button>
      </form>
    </div>

    <!-- Full Width Table Card -->
    <div class="admin-card">
      <div class="admin-card-header">
        <div>
          <h2 class="admin-card-title"><i class="fa-solid fa-handshake" style="color: var(--admin-accent);"></i> Daftar Brand &amp; Supplier Terpasang</h2>
          <p class="admin-card-subtitle">Total <?= count($partners) ?> partner aktif tampil di bawah section Hero.</p>
        </div>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th width="70">Urutan</th>
              <th>Pratinjau Tampilan</th>
              <th>Nama Brand</th>
              <th>Mode Tampilan</th>
              <th width="140">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($partners as $p): 
              $mode = $p['display_mode'] ?? 'logo_text';
            ?>
              <tr>
                <td><strong>#<?= $p['sort_order'] ?></strong></td>
                <td>
                  <div style="display: inline-flex; align-items: center; gap: 10px; padding: 6px 0;">
                    <?php if ($mode !== 'text_only' && !empty($p['logo_image'])): ?>
                      <img src="../<?= htmlspecialchars($p['logo_image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="height: 30px; width: auto; max-width: 140px; object-fit: contain;">
                    <?php endif; ?>
                    <?php if ($mode !== 'logo_only' || empty($p['logo_image'])): ?>
                      <strong style="color: #FFFFFF; font-size: 0.95rem;"><?= htmlspecialchars($p['name']) ?></strong>
                    <?php endif; ?>
                  </div>
                </td>
                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                <td>
                  <span class="sidebar-badge" style="border-radius: 6px; padding: 4px 10px;">
                    <?= htmlspecialchars($modeLabels[$mode] ?? 'Logo + Teks') ?>
                  </span>
                </td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button
                      type="button"
                      class="btn btn-secondary btn-sm edit-partner-btn"
                      data-id="<?= $p['id'] ?>"
                      data-name="<?= htmlspecialchars($p['name']) ?>"
                      data-mode="<?= htmlspecialchars($mode) ?>"
                      data-sort="<?= $p['sort_order'] ?>">
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <a href="partners.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus partner ini?')">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

<!-- MODAL: TAMBAH SUPPLIER -->
<div class="admin-modal-overlay" id="addPartnerModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Supplier / Partner Baru</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addPartnerModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">

        <div class="form-group">
          <label class="form-label">Nama Brand / Supplier</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Digiflazz / UniPin / Razer Gold" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Mode Tampilan di Website</label>
            <select name="display_mode" class="form-control">
              <option value="logo_text">Logo + Teks</option>
              <option value="logo_only">Hanya Logo Gambar</option>
              <option value="text_only">Hanya Teks Brand</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($partners) + 1 ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Upload Logo (PNG / SVG / WebP)</label>
          <input type="file" name="logo_file" class="form-control" accept="image/*">
          <div class="form-help">Disarankan logo transparan format PNG atau SVG agar menyatu dengan background gelap.</div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addPartnerModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Simpan Partner</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: EDIT SUPPLIER -->
<div class="admin-modal-overlay" id="editPartnerModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-pen-to-square" style="color: var(--admin-accent);"></i> Edit Supplier</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('editPartnerModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_partner_id">

        <div class="form-group">
          <label class="form-label">Nama Brand / Supplier</label>
          <input type="text" name="name" id="edit_partner_name" class="form-control" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Mode Tampilan di Website</label>
            <select name="display_mode" id="edit_partner_mode" class="form-control">
              <option value="logo_text">Logo + Teks</option>
              <option value="logo_only">Hanya Logo Gambar</option>
              <option value="text_only">Hanya Teks Brand</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" id="edit_partner_sort" class="form-control">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Ganti File Logo (Opsional)</label>
          <input type="file" name="logo_file" class="form-control" accept="image/*">
          <div class="form-help">Biarkan kosong jika tetap memakai logo yang sekarang terpasang.</div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('editPartnerModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.edit-partner-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit_partner_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_partner_name').value = btn.getAttribute('data-name');
    document.getElementById('edit_partner_mode').value = btn.getAttribute('data-mode');
    document.getElementById('edit_partner_sort').value = btn.getAttribute('data-sort');
    openAdminModal('editPartnerModal');
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
