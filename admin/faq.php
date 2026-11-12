<?php
$page_title = 'Kelola Tanya Jawab (FAQ)';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($question && $answer) {
            $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$question, $answer, $sort_order]);
            set_flash('success', 'Pertanyaan FAQ baru berhasil ditambahkan!');
        }
    } elseif ($action === 'update_all') {
        if (!empty($_POST['faqs'])) {
            foreach ($_POST['faqs'] as $id => $data) {
                $stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ?, sort_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($data['question']),
                    trim($data['answer']),
                    (int)$data['sort_order'],
                    (int)$id
                ]);
            }
            set_flash('success', 'Semua FAQ berhasil diperbarui!');
        }
    }

    header('Location: faq.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM faqs WHERE id = ?")->execute([$id]);
    set_flash('success', 'FAQ berhasil dihapus!');
    header('Location: faq.php');
    exit;
}

$faqs = $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC")->fetchAll();
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Kelola Tanya Jawab (FAQ)</h1>
      <p>Edit pertanyaan dan jawaban seputar layanan (maksimal 4 kartu per baris agar lega dibaca).</p>
    </div>
    <div class="topbar-actions">
      <button type="button" class="btn btn-secondary btn-sm" onclick="openAdminModal('addFaqModal')">
        <i class="fa-solid fa-plus"></i> Tambah FAQ
      </button>
      <button type="submit" form="faq-form" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua FAQ
      </button>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash['message']) ?>
      </div>
    <?php endif; ?>

    <form id="faq-form" method="POST" action="">
      <input type="hidden" name="action" value="update_all">

      <!-- Strictly Max 4 Columns Grid -->
      <div class="admin-grid-4">
        <?php foreach ($faqs as $f): ?>
          <div class="admin-card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <span style="font-weight: 800; font-size: 0.85rem; color: var(--admin-accent);">
                <i class="fa-solid fa-circle-question"></i> FAQ #<?= $f['id'] ?>
              </span>
              <a href="faq.php?delete=<?= $f['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pertanyaan ini?')">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>

            <div class="form-group">
              <label class="form-label">Pertanyaan</label>
              <input type="text" name="faqs[<?= $f['id'] ?>][question]" class="form-control" value="<?= htmlspecialchars($f['question']) ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Jawaban Penjelasan</label>
              <textarea name="faqs[<?= $f['id'] ?>][answer]" class="form-control" rows="4" required><?= htmlspecialchars($f['answer']) ?></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label">Urutan Tampil (Sort)</label>
              <input type="number" name="faqs[<?= $f['id'] ?>][sort_order]" class="form-control" value="<?= $f['sort_order'] ?>">
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </form>

<!-- MODAL: TAMBAH PERTANYAAN FAQ -->
<div class="admin-modal-overlay" id="addFaqModal">
  <div class="admin-modal">
    <div class="admin-modal-header">
      <h3><i class="fa-solid fa-plus-circle" style="color: var(--admin-accent);"></i> Tambah Pertanyaan FAQ Baru</h3>
      <button type="button" class="admin-modal-close" onclick="closeAdminModal('addFaqModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="admin-modal-body">
      <form method="POST" action="">
        <input type="hidden" name="action" value="add">

        <div class="form-row">
          <div class="form-group" style="flex: 3;">
            <label class="form-label">Pertanyaan</label>
            <input type="text" name="question" class="form-control" placeholder="Tuliskan pertanyaan pelanggan..." required>
          </div>
          <div class="form-group">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" class="form-control" value="<?= count($faqs) + 1 ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Jawaban Penjelasan</label>
          <textarea name="answer" class="form-control" rows="4" placeholder="Tuliskan jawaban informatif dan jelas..." required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAdminModal('addFaqModal')">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Simpan FAQ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
