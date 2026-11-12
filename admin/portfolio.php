<?php
$page_title = 'Kelola Portofolio Proyek';
require_once __DIR__ . '/includes/header.php';

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'Website');
        $description = trim($_POST['description'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $demo_url = trim($_POST['demo_url'] ?? '#');
        $action_type = trim($_POST['action_type'] ?? 'modal');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        $image_path = 'assets/images/portfolio-1.svg';
        if (!empty($_FILES['image_file']['name'])) {
            $uploaded = handle_image_upload('image_file', 'uploads/');
            if ($uploaded) {
                $image_path = $uploaded;
            }
        }

        $logo_path = '';
        if (!empty($_FILES['logo_file']['name'])) {
            $upLogo = handle_image_upload('logo_file', 'uploads/');
            if ($upLogo) {
                $logo_path = $upLogo;
            }
        }

        if ($title) {
            $stmt = $pdo->prepare("INSERT INTO portfolio (title, category, description, tags, image, demo_url, action_type, sort_order, logo_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $description, $tags, $image_path, $demo_url, $action_type, $sort_order, $logo_path]);
            set_flash('success', 'Item portofolio baru berhasil ditambahkan!');
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'Website');
        $description = trim($_POST['description'] ?? '');
        $tags = trim($_POST['tags'] ?? '');
        $demo_url = trim($_POST['demo_url'] ?? '#');
        $action_type = trim($_POST['action_type'] ?? 'modal');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($id && $title) {
            $updates = [
                'title' => $title,
                'category' => $category,
                'description' => $description,
                'tags' => $tags,
                'demo_url' => $demo_url,
                'action_type' => $action_type,
                'sort_order' => $sort_order
            ];

            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = handle_image_upload('image_file', 'uploads/');
                if ($uploaded) {
                    $updates['image'] = $uploaded;
                }
            }
            if (!empty($_FILES['logo_file']['name'])) {
                $upLogo = handle_image_upload('logo_file', 'uploads/');
                if ($upLogo) {
                    $updates['logo_image'] = $upLogo;
                }
            }

            $setParts = [];
            $params = [];
            foreach ($updates as $col => $val) {
                $setParts[] = "{$col} = ?";
                $params[] = $val;
            }
            $params[] = $id;

            $stmt = $pdo->prepare("UPDATE portfolio SET " . implode(', ', $setParts) . " WHERE id = ?");
            $stmt->execute($params);
            set_flash('success', 'Portofolio berhasil diperbarui!');
        }
    }

    header('Location: portfolio.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM portfolio WHERE id = ?")->execute([$id]);
    set_flash('success', 'Portofolio berhasil dihapus!');
    header('Location: portfolio.php');
    exit;
}

$portfolio = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Portofolio Proyek</h1>
      <p>Kelola website klien, desain poster, atau karakter maskot serta pilih aksi klik (Pop-up Modal atau Buka Link).</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="openAdminModal('addPortfolioModal')">
        <i class="fa-solid fa-plus"></i> Tambah Portofolio
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Full Width Table -->
    <div class="admin-card">
      <div class="admin-card-header">
        <div>
          <h2 class="admin-card-title"><i class="fa-solid fa-layer-group" style="color: var(--admin-accent);"></i> Daftar Portofolio Aktif</h2>
          <p class="admin-card-subtitle">Total <?= count($portfolio) ?> proyek terpasang pada slider portofolio landing page.</p>
        </div>
      </div>

      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th width="70">Urutan</th>
              <th width="110">Thumbnail</th>
              <th>Judul &amp; Deskripsi</th>
              <th>Kategori</th>
              <th>Aksi Preview Website</th>
              <th width="130">Kelola</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($portfolio as $p): 
              $actType = $p['action_type'] ?? 'modal';
            ?>
              <tr>
                <td><strong>#<?= $p['sort_order'] ?></strong></td>
                <td>
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <?php if (!empty($p['logo_image'])): ?>
                      <img src="../<?= htmlspecialchars($p['logo_image']) ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);" title="Logo Brand">
                    <?php endif; ?>
                    <img src="../<?= htmlspecialchars($p['image']) ?>" alt="Thumb" style="width: 80px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);">
                  </div>
                </td>
                <td>
                  <strong style="color: #FFFFFF; font-size: 0.96rem;"><?= htmlspecialchars($p['title']) ?></strong>
                  <div style="font-size: 0.82rem; color: var(--admin-muted); margin-top: 4px; max-width: 380px;">
                    <?= htmlspecialchars(substr($p['description'], 0, 95)) ?>...
                  </div>
                  <?php if (!empty($p['tags'])): ?>
                    <div style="font-size: 0.74rem; color: #64748B; margin-top: 4px;"><?= htmlspecialchars($p['tags']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="sidebar-badge" style="border-radius: 6px; padding: 4px 10px;"><?= htmlspecialchars($p['category']) ?></span>
                </td>
                <td>
                  <?php if ($actType === 'link'): ?>
                    <span style="color: #34D399; font-size: 0.82rem; font-weight: 700;"><i class="fa-solid fa-link"></i> Buka Link Langsung</span>
                  <?php else: ?>
                    <span style="color: #60A5FA; font-size: 0.82rem; font-weight: 700;"><i class="fa-solid fa-window-restore"></i> Pop-up Preview Modal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button
                      type="button"
                      class="btn btn-secondary btn-sm edit-port-btn"
                      data-id="<?= $p['id'] ?>"
                      data-title="<?= htmlspecialchars($p['title']) ?>"
                      data-category="<?= htmlspecialchars($p['category']) ?>"
                      data-desc="<?= htmlspecialchars($p['description']) ?>"
                      data-tags="<?= htmlspecialchars($p['tags']) ?>"
                      data-url="<?= htmlspecialchars($p['demo_url']) ?>"
                      data-action-type="<?= htmlspecialchars($actType) ?>"
                      data-sort="<?= $p['sort_order'] ?>">
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <a href="portfolio.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus portofolio ini?')">
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

<!-- MODAL: TAMBAH PORTOFOLIO -->
<div class="admin-modal-overlay" id="addPortfolioModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Portofolio Baru</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addPortfolioModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Judul Proyek / Nama Toko</label>
            <input type="text" name="title" class="form-control" placeholder="Contoh: Lukifays Store" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kategori Tab</label>
            <select name="category" class="form-control" required>
              <option value="Website">Website</option>
              <option value="Poster Design">Poster Design</option>
              <option value="Character">Character</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Aksi Saat "Preview Website" Diklik</label>
            <select name="action_type" class="form-control">
              <option value="modal">Buka Pop-up Detail Proyek (Modal)</option>
              <option value="link">Langsung Buka Link Demo Website</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($portfolio) + 1 ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Upload Screenshot / Banner (16:10)</label>
            <input type="file" name="image_file" class="form-control" accept="image/*" required>
          </div>
          <div class="form-group">
            <label class="form-label">Upload Logo Klien (Opsional, 1:1)</label>
            <input type="file" name="logo_file" class="form-control" accept="image/*">
            <div class="form-help">Ikon logo kecil yang muncul di sebelah judul proyek.</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">URL Demo / Link Tujuan</label>
          <input type="text" name="demo_url" class="form-control" placeholder="https://lukifays.store" value="#">
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Proyek (Tampil di Pop-up)</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan fitur atau hasil kerja proyek ini..." required></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Tags / Teknologi (Pisahkan Koma)</label>
          <input type="text" name="tags" class="form-control" placeholder="Top Up Game, Payment Gateway, Multi-Supplier">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addPortfolioModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Simpan Portofolio</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: EDIT PORTOFOLIO -->
<div class="admin-modal-overlay" id="editPortfolioModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-pen-to-square" style="color: var(--admin-accent);"></i> Edit Portofolio</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('editPortfolioModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_port_id">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Judul Proyek / Nama Toko</label>
            <input type="text" name="title" id="edit_port_title" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kategori Tab</label>
            <select name="category" id="edit_port_category" class="form-control">
              <option value="Website">Website</option>
              <option value="Poster Design">Poster Design</option>
              <option value="Character">Character</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Aksi Saat "Preview Website" Diklik</label>
            <select name="action_type" id="edit_port_action_type" class="form-control">
              <option value="modal">Buka Pop-up Detail Proyek (Modal)</option>
              <option value="link">Langsung Buka Link Demo Website</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" id="edit_port_sort" class="form-control">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Ganti Screenshot / Banner (Opsional)</label>
            <input type="file" name="image_file" class="form-control" accept="image/*">
            <div class="form-help">Biarkan kosong jika tetap menggunakan gambar saat ini.</div>
          </div>
          <div class="form-group">
            <label class="form-label">Ganti Logo Klien (Opsional)</label>
            <input type="file" name="logo_file" class="form-control" accept="image/*">
            <div class="form-help">Biarkan kosong jika tidak ingin mengganti logo.</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">URL Demo / Link Tujuan</label>
          <input type="text" name="demo_url" id="edit_port_url" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Proyek (Tampil di Pop-up)</label>
          <textarea name="description" id="edit_port_desc" class="form-control" rows="3" required></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Tags / Teknologi (Pisahkan Koma)</label>
          <input type="text" name="tags" id="edit_port_tags" class="form-control">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('editPortfolioModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.edit-port-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit_port_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_port_title').value = btn.getAttribute('data-title');
    document.getElementById('edit_port_category').value = btn.getAttribute('data-category');
    document.getElementById('edit_port_desc').value = btn.getAttribute('data-desc');
    document.getElementById('edit_port_tags').value = btn.getAttribute('data-tags');
    document.getElementById('edit_port_url').value = btn.getAttribute('data-url');
    document.getElementById('edit_port_action_type').value = btn.getAttribute('data-action-type');
    document.getElementById('edit_port_sort').value = btn.getAttribute('data-sort');
    openAdminModal('editPortfolioModal');
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
