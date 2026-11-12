<?php
$page_title = 'Kelola Alur Kerja (Cara Kerja)';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['workflow_title'])) update_setting($pdo, 'workflow_title', trim($_POST['workflow_title']));
    if (isset($_POST['workflow_subtitle'])) update_setting($pdo, 'workflow_subtitle', trim($_POST['workflow_subtitle']));

    if (isset($_POST['steps'])) {
        foreach ($_POST['steps'] as $id => $data) {
            $stmt = $pdo->prepare("UPDATE workflow_steps SET step_number = ?, title = ?, description = ?, sort_order = ? WHERE id = ?");
            $stmt->execute([
                trim($data['step_number']),
                trim($data['title']),
                trim($data['description']),
                (int)$data['sort_order'],
                (int)$id
            ]);
        }
    }
    set_flash('success', 'Alur kerja & judul berhasil disimpan!');
    header('Location: workflow.php');
    exit;
}

$settings = get_settings($pdo);
$steps = $pdo->query("SELECT * FROM workflow_steps ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Alur Kerja (Cara Kerja)</h1>
      <p>Edit judul section dan 4 tahap alur timeline horizontal pengerjaan website toko.</p>
    </div>
    <div class="topbar-actions">
      <button type="submit" form="workflow-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Alur Kerja
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="workflow-form" method="POST" action="">
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="fa-solid fa-heading" style="color: var(--admin-accent);"></i> Judul &amp; Sub-Judul Section Cara Kerja</h2>
        </div>
        <div class="form-row">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Judul Utama</label>
            <input type="text" name="workflow_title" class="form-control" value="<?= htmlspecialchars($settings['workflow_title'] ?? 'Dari ngobrol sampai toko tayang, empat langkah') ?>" required>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Sub-Judul Penjelasan</label>
            <input type="text" name="workflow_subtitle" class="form-control" value="<?= htmlspecialchars($settings['workflow_subtitle'] ?? 'Kamu tidak perlu paham teknis - bagian rumitnya kami yang kerjakan.') ?>" required>
          </div>
        </div>
      </div>

      <div class="admin-grid-4">
        <?php foreach ($steps as $st): ?>
          <div class="admin-card" style="margin-bottom: 0;">
            <div class="admin-card-header" style="margin-bottom: 14px; padding-bottom: 10px;">
              <span class="sidebar-badge" style="font-size: 0.9rem; border-radius: 6px; padding: 4px 10px;">
                LANGKAH <?= htmlspecialchars($st['step_number']) ?>
              </span>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Angka Bulat</label>
                <input type="text" name="steps[<?= $st['id'] ?>][step_number]" class="form-control" value="<?= htmlspecialchars($st['step_number']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Urutan</label>
                <input type="number" name="steps[<?= $st['id'] ?>][sort_order]" class="form-control" value="<?= $st['sort_order'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Judul Tahap</label>
              <input type="text" name="steps[<?= $st['id'] ?>][title]" class="form-control" value="<?= htmlspecialchars($st['title']) ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Penjelasan Tahap</label>
              <textarea name="steps[<?= $st['id'] ?>][description]" class="form-control" rows="4" required><?= htmlspecialchars($st['description']) ?></textarea>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="text-align: right; margin-top: 24px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Alur Kerja
        </button>
      </div>
    </form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
