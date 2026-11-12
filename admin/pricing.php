<?php
$page_title = 'Kelola Paket Harga & Layanan';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $category = trim($_POST['category'] ?? 'website');
        $name = trim($_POST['name'] ?? '');
        $tier_subtitle = trim($_POST['tier_subtitle'] ?? '');
        $old_price = trim($_POST['old_price'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $period = trim($_POST['period'] ?? '/selamanya');
        $badge_text = trim($_POST['badge_text'] ?? '');
        $badge_color = trim($_POST['badge_color'] ?? '#2563EB');
        $is_popular = isset($_POST['is_popular']) ? 1 : 0;
        $features_list = trim($_POST['features_list'] ?? '');
        $btn_text = trim($_POST['btn_text'] ?? 'Pilih Paket');
        $btn_link = trim($_POST['btn_link'] ?? '#');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($name && $price) {
            $stmt = $pdo->prepare("INSERT INTO pricing_plans (category, name, tier_subtitle, old_price, price, period, badge_text, badge_color, is_popular, features_list, btn_text, btn_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category, $name, $tier_subtitle, $old_price, $price, $period, $badge_text, $badge_color, $is_popular, $features_list, $btn_text, $btn_link, $sort_order]);
            set_flash('success', 'Paket harga baru berhasil ditambahkan!');
        }
    } elseif ($action === 'update_all') {
        if (isset($_POST['pricing_columns'])) {
            update_setting($pdo, 'pricing_columns', $_POST['pricing_columns'] === '3' ? '3' : '4');
        }
        if (!empty($_POST['plans'])) {
            foreach ($_POST['plans'] as $id => $data) {
                $is_popular = isset($data['is_popular']) ? 1 : 0;
                $stmt = $pdo->prepare("UPDATE pricing_plans SET 
                    category = ?, name = ?, tier_subtitle = ?, 
                    old_price = ?, price = ?, period = ?, 
                    badge_text = ?, badge_color = ?, is_popular = ?, 
                    features_list = ?, btn_text = ?, btn_link = ?, 
                    sort_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($data['category']),
                    trim($data['name']),
                    trim($data['tier_subtitle']),
                    trim($data['old_price']),
                    trim($data['price']),
                    trim($data['period']),
                    trim($data['badge_text']),
                    trim($data['badge_color']),
                    $is_popular,
                    trim($data['features_list']),
                    trim($data['btn_text']),
                    trim($data['btn_link']),
                    (int)$data['sort_order'],
                    (int)$id
                ]);
            }
        }
        set_flash('success', 'Semua paket harga & pengaturan kolom berhasil diperbarui!');
    }

    header('Location: pricing.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM pricing_plans WHERE id = ?")->execute([$id]);
    set_flash('success', 'Paket berhasil dihapus!');
    header('Location: pricing.php');
    exit;
}

$settings = get_settings($pdo);
$pricing_columns = $settings['pricing_columns'] ?? '4';
$plans = $pdo->query("SELECT * FROM pricing_plans ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Paket Harga &amp; Layanan</h1>
      <p>Filter berdasarkan kategori di atas, atur 3 atau 4 kartu per baris, dan tambah paket baru via pojok kanan atas.</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addPricingModal')">
        <i class="fa-solid fa-plus"></i> Tambah Paket Baru
      </button>
      <button type="submit" form="pricing-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Paket
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="pricing-form" method="POST" action="">
      <input type="hidden" name="action" value="update_all">

      <!-- Top Bar: Column Layout Setting (3/4 Card Toggle) + Category Filter Tabs -->
      <div class="admin-card" style="padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div class="admin-filter-tabs" style="margin-bottom: 0;">
          <button type="button" class="admin-tab-btn active" data-price-cat="all">Semua Paket (<?= count($plans) ?>)</button>
          <button type="button" class="admin-tab-btn" data-price-cat="website">Website</button>
          <button type="button" class="admin-tab-btn" data-price-cat="resseler">Resseler</button>
          <button type="button" class="admin-tab-btn" data-price-cat="ads-on">Ads On</button>
        </div>

        <div style="display: flex; align-items: center; gap: 12px;">
          <label class="form-label" style="margin: 0; white-space: nowrap;">Jumlah Kartu / Baris di Landing:</label>
          <select name="pricing_columns" class="form-control" style="width: auto; padding: 7px 14px;">
            <option value="4" <?= $pricing_columns === '4' ? 'selected' : '' ?>>4 Kartu per Baris (4 Kolom)</option>
            <option value="3" <?= $pricing_columns === '3' ? 'selected' : '' ?>>3 Kartu per Baris (3 Kolom)</option>
          </select>
        </div>
      </div>

      <!-- Max 3 Columns inside Admin for Spacious Editing -->
      <div class="admin-grid-3">
        <?php foreach ($plans as $p): ?>
          <div class="admin-card admin-price-item" data-price-item-cat="<?= htmlspecialchars($p['category']) ?>" style="margin-bottom: 0; <?= $p['is_popular'] ? 'border-color: #2563EB;' : '' ?>">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <strong style="font-size: 1rem; color: #FFFFFF;"><?= htmlspecialchars($p['name']) ?></strong>
              <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-size: 0.78rem; color: #38BDF8; font-weight: 700; cursor: pointer;">
                  <input type="checkbox" name="plans[<?= $p['id'] ?>][is_popular]" value="1" <?= $p['is_popular'] ? 'checked' : '' ?>>
                  Popular
                </label>
                <a href="pricing.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus paket ini?')">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Nama Paket</label>
                <input type="text" name="plans[<?= $p['id'] ?>][name]" class="form-control" value="<?= htmlspecialchars($p['name']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Kategori Tab</label>
                <select name="plans[<?= $p['id'] ?>][category]" class="form-control">
                  <option value="website" <?= $p['category'] === 'website' ? 'selected' : '' ?>>Website</option>
                  <option value="resseler" <?= $p['category'] === 'resseler' ? 'selected' : '' ?>>Resseler</option>
                  <option value="ads-on" <?= $p['category'] === 'ads-on' ? 'selected' : '' ?>>Ads On</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Sub-Judul Target Pengguna</label>
              <input type="text" name="plans[<?= $p['id'] ?>][tier_subtitle]" class="form-control" value="<?= htmlspecialchars($p['tier_subtitle']) ?>">
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Harga Coret (Diskon)</label>
                <input type="text" name="plans[<?= $p['id'] ?>][old_price]" class="form-control" value="<?= htmlspecialchars($p['old_price']) ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Harga Aktif</label>
                <input type="text" name="plans[<?= $p['id'] ?>][price]" class="form-control" value="<?= htmlspecialchars($p['price']) ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Periode</label>
                <input type="text" name="plans[<?= $p['id'] ?>][period]" class="form-control" value="<?= htmlspecialchars($p['period']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Badge Promo</label>
                <input type="text" name="plans[<?= $p['id'] ?>][badge_text]" class="form-control" value="<?= htmlspecialchars($p['badge_text']) ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Warna Badge</label>
              <div class="color-picker-group">
                <input type="color" class="color-swatch-input" data-sync="badge_color_<?= $p['id'] ?>" value="<?= htmlspecialchars($p['badge_color'] ?: '#2563EB') ?>">
                <input type="text" id="badge_color_<?= $p['id'] ?>" name="plans[<?= $p['id'] ?>][badge_color]" class="form-control" value="<?= htmlspecialchars($p['badge_color'] ?: '#2563EB') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Daftar Fitur (1 Poin per Baris)</label>
              <textarea name="plans[<?= $p['id'] ?>][features_list]" class="form-control" rows="5"><?= htmlspecialchars($p['features_list']) ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Teks Tombol</label>
                <input type="text" name="plans[<?= $p['id'] ?>][btn_text]" class="form-control" value="<?= htmlspecialchars($p['btn_text']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="plans[<?= $p['id'] ?>][sort_order]" class="form-control" value="<?= $p['sort_order'] ?>">
              </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Link Tujuan (WhatsApp)</label>
              <input type="text" name="plans[<?= $p['id'] ?>][btn_link]" class="form-control" value="<?= htmlspecialchars($p['btn_link']) ?>" required>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </form>

<!-- MODAL: TAMBAH PAKET HARGA -->
<div class="admin-modal-overlay" id="addPricingModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Paket Harga Baru</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addPricingModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nama Paket</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: RESELLER PRO" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kategori Tab</label>
            <select name="category" class="form-control">
              <option value="website">Website</option>
              <option value="resseler">Resseler</option>
              <option value="ads-on">Ads On</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Sub-Judul Paket</label>
          <input type="text" name="tier_subtitle" class="form-control" placeholder="Cocok untuk...">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Harga Coret (Opsional)</label>
            <input type="text" name="old_price" class="form-control" placeholder="Rp 3.000.000">
          </div>
          <div class="form-group">
            <label class="form-label">Harga Aktif</label>
            <input type="text" name="price" class="form-control" placeholder="Rp 2.000.000" required>
          </div>
          <div class="form-group">
            <label class="form-label">Periode</label>
            <input type="text" name="period" class="form-control" value="/selamanya" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Badge Atas (Opsional)</label>
            <input type="text" name="badge_text" class="form-control" placeholder="PALING POPULER">
          </div>
          <div class="form-group">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($plans) + 1 ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Daftar Fitur (1 per baris)</label>
          <textarea name="features_list" class="form-control" rows="4" placeholder="Domain .com&#10;Hosting SSD&#10;Integrasi QRIS"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Teks Tombol CTA</label>
            <input type="text" name="btn_text" class="form-control" value="Pilih Paket" required>
          </div>
          <div class="form-group">
            <label class="form-label">Link Tujuan WhatsApp</label>
            <input type="text" name="btn_link" class="form-control" value="https://wa.me/6281234567890" required>
          </div>
        </div>

        <div class="form-group">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 700; color: #38BDF8;">
            <input type="checkbox" name="is_popular" value="1"> Sorot Paket Ini Sebagai Paling Populer
          </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addPricingModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Simpan Paket</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.admin-tab-btn[data-price-cat]').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.admin-tab-btn[data-price-cat]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.getAttribute('data-price-cat');
    document.querySelectorAll('.admin-price-item').forEach(card => {
      card.style.display = (filter === 'all' || card.getAttribute('data-price-item-cat') === filter) ? 'block' : 'none';
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
