<?php
$page_title = 'Kelola Layanan & Tab Fitur';
require_once __DIR__ . '/includes/header.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Update Services (The 3 Tabs - Clean Text Tabs Without Icons)
    if ($action === 'update_services') {
        foreach ($_POST['services'] as $id => $data) {
            $stmt = $pdo->prepare("UPDATE services SET step_number = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([
                trim($data['step_number']),
                trim($data['title']),
                trim($data['description']),
                (int)$data['sort_order'],
                (int)$id
            ]);
        }
        set_flash('success', '3 Tab Layanan Utama berhasil diperbarui!');
        header('Location: services.php');
        exit;
    }

    // 2. Add New Card to a Tab
    if ($action === 'add_card') {
        $service_id = (int)$_POST['service_id'];
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($title) {
            $stmt = $pdo->prepare("INSERT INTO service_cards (service_id, icon, title, description, sort_order) VALUES (?, 'check', ?, ?, ?)");
            $stmt->execute([$service_id, $title, $description, $sort_order]);
            set_flash('success', 'Kartu layanan baru berhasil ditambahkan!');
        }
        header('Location: services.php');
        exit;
    }

    // 3. Update Existing Cards
    if ($action === 'update_cards') {
        if (!empty($_POST['cards'])) {
            foreach ($_POST['cards'] as $id => $cData) {
                $stmt = $pdo->prepare("UPDATE service_cards SET title = ?, description = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($cData['title']),
                    trim($cData['description']),
                    (int)$cData['sort_order'],
                    (int)$id
                ]);
            }
            set_flash('success', 'Daftar kartu layanan berhasil diperbarui!');
        }
        header('Location: services.php');
        exit;
    }
}

if (isset($_GET['delete_card'])) {
    $cardId = (int)$_GET['delete_card'];
    $pdo->prepare("DELETE FROM service_cards WHERE id = ?")->execute([$cardId]);
    set_flash('success', 'Kartu layanan berhasil dihapus!');
    header('Location: services.php');
    exit;
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC, id ASC")->fetchAll();
$service_cards = $pdo->query("SELECT * FROM service_cards ORDER BY service_id ASC, sort_order ASC, id ASC")->fetchAll();
$cards_by_service = [];
foreach ($service_cards as $sc) {
    $cards_by_service[$sc['service_id']][] = $sc;
}

require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola "Apa Saja yang kami kerjakan"</h1>
      <p>Kustomisasi 3 Tab Layanan Utama dan isi kartu penjelasannya (polosan bersih tanpa ikon).</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-primary btn-sm" onclick="openAdminModal('addServiceCardModal')">
        <i class="fa-solid fa-plus"></i> Tambah Kartu Fitur
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <!-- Section 1: Edit 3 Main Tabs (Clean, No Icon Uploads) -->
    <div class="admin-card" style="margin-bottom: 32px;">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="fa-solid fa-layer-group" style="color: var(--admin-primary);"></i> 3 Tab Layanan Utama</h2>
      </div>

      <form method="POST" action="">
        <input type="hidden" name="action" value="update_services">

        <div class="admin-grid-3">
          <?php foreach ($services as $srv): ?>
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--admin-border); border-radius: 10px; padding: 18px;">
              <div style="margin-bottom: 14px;">
                <strong style="color: #FFFFFF; font-size: 1rem;"><?= htmlspecialchars($srv['title']) ?></strong>
                <div style="font-size: 0.78rem; color: var(--admin-muted);">Tab #<?= $srv['id'] ?></div>
              </div>

              <div class="form-group">
                <label class="form-label">Judul Tab</label>
                <input type="text" name="services[<?= $srv['id'] ?>][title]" class="form-control" value="<?= htmlspecialchars($srv['title']) ?>" required>
              </div>

              <div class="form-group">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="services[<?= $srv['id'] ?>][description]" class="form-control" rows="2"><?= htmlspecialchars($srv['description']) ?></textarea>
              </div>

              <div class="form-row">
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label">No. Langkah</label>
                  <input type="text" name="services[<?= $srv['id'] ?>][step_number]" class="form-control" value="<?= htmlspecialchars($srv['step_number']) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label">Urutan</label>
                  <input type="number" name="services[<?= $srv['id'] ?>][sort_order]" class="form-control" value="<?= $srv['sort_order'] ?>">
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="margin-top: 18px; text-align: right;">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan 3 Tab
          </button>
        </div>
      </form>
    </div>

    <!-- Section 2: Cards for each Tab (Max 3 columns grid) -->
    <?php foreach ($services as $srv): ?>
      <div class="admin-card" style="margin-bottom: 28px;">
        <div class="admin-card-header">
          <div>
            <h3 style="font-size: 1.15rem; color: #FFFFFF; font-weight: 800;">
              Kartu Isi: <?= htmlspecialchars($srv['title']) ?>
            </h3>
            <span style="font-size: 0.8rem; color: var(--admin-muted);">
              <?= count($cards_by_service[$srv['id']] ?? []) ?> kartu aktif
            </span>
          </div>
          <button type="button" class="btn btn-secondary btn-sm" onclick="openServiceCardModal(<?= $srv['id'] ?>)">
            <i class="fa-solid fa-plus"></i> Tambah ke <?= htmlspecialchars($srv['title']) ?>
          </button>
        </div>

        <form method="POST" action="">
          <input type="hidden" name="action" value="update_cards">
          <div class="admin-grid-3">
            <?php if (!empty($cards_by_service[$srv['id']])): ?>
              <?php foreach ($cards_by_service[$srv['id']] as $c): ?>
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--admin-border); border-radius: 10px; padding: 16px;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 0.78rem; color: var(--admin-accent); font-weight: 700;">KARTU #<?= $c['id'] ?></span>
                    <a href="services.php?delete_card=<?= $c['id'] ?>" class="btn btn-danger btn-sm" style="padding: 2px 8px; font-size: 0.75rem;" onclick="return confirm('Hapus kartu ini?')">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </div>

                  <div class="form-group" style="margin-bottom: 10px;">
                    <label class="form-label" style="font-size: 0.78rem;">Judul Kartu</label>
                    <input type="text" name="cards[<?= $c['id'] ?>][title]" class="form-control" value="<?= htmlspecialchars($c['title']) ?>" required>
                  </div>

                  <div class="form-group" style="margin-bottom: 10px;">
                    <label class="form-label" style="font-size: 0.78rem;">Deskripsi</label>
                    <textarea name="cards[<?= $c['id'] ?>][description]" class="form-control" rows="3" style="font-size: 0.84rem;"><?= htmlspecialchars($c['description']) ?></textarea>
                  </div>

                  <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="font-size: 0.78rem;">Urutan</label>
                    <input type="number" name="cards[<?= $c['id'] ?>][sort_order]" class="form-control" value="<?= $c['sort_order'] ?>">
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p style="color: var(--admin-muted); font-size: 0.88rem;">Belum ada kartu untuk tab ini.</p>
            <?php endif; ?>
          </div>

          <?php if (!empty($cards_by_service[$srv['id']])): ?>
            <div style="margin-top: 16px; text-align: right;">
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Kartu <?= htmlspecialchars($srv['title']) ?>
              </button>
            </div>
          <?php endif; ?>
        </form>
      </div>
    <?php endforeach; ?>

<!-- MODAL: TAMBAH KARTU FITUR LAYANAN -->
<div class="admin-modal-overlay" id="addServiceCardModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Kartu Isi Layanan</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addServiceCardModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="">
        <input type="hidden" name="action" value="add_card">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Pilih Tab Layanan</label>
            <select name="service_id" id="modal_service_id" class="form-control">
              <?php foreach ($services as $srv): ?>
                <option value="<?= $srv['id'] ?>"><?= htmlspecialchars($srv['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-control" value="7">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Kartu</label>
          <input type="text" name="title" class="form-control" placeholder="Contoh: Transaksi Otomatis 24 Jam" required>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Penjelasan</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan detail layanan..." required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addServiceCardModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Simpan Kartu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openServiceCardModal(serviceId) {
  document.getElementById('modal_service_id').value = serviceId;
  openAdminModal('addServiceCardModal');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
