<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/auth.php';

require_login();

$settings = get_settings($pdo);
$current_admin = get_logged_in_admin($pdo);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($username)) {
        $error = 'Nama dan username tidak boleh kosong.';
    } else {
        // Check if username taken by another admin
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
        $stmt->execute([$username, $current_admin['id']]);
        if ($stmt->fetch()) {
            $error = 'Username sudah digunakan oleh akun lain.';
        } else {
            // Verify current password if user wants to change password OR update profile
            if (!empty($new_password)) {
                if (empty($current_password)) {
                    $error = 'Masukkan password saat ini untuk konfirmasi perubahan password.';
                } elseif (!password_verify($current_password, $current_admin['password'])) {
                    $error = 'Password saat ini salah.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'Password baru minimal 6 karakter.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'Konfirmasi password baru tidak cocok.';
                } else {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE admins SET name = ?, username = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $username, $hashed, $current_admin['id']]);
                    $_SESSION['admin_name'] = $name;
                    $_SESSION['admin_username'] = $username;
                    $success = 'Profil dan password berhasil diperbarui!';
                    $current_admin = get_logged_in_admin($pdo);
                }
            } else {
                // Just update name and username
                $stmt = $pdo->prepare("UPDATE admins SET name = ?, username = ? WHERE id = ?");
                $stmt->execute([$name, $username, $current_admin['id']]);
                $_SESSION['admin_name'] = $name;
                $_SESSION['admin_username'] = $username;
                $success = 'Profil admin berhasil diperbarui!';
                $current_admin = get_logged_in_admin($pdo);
            }
        }
    }
}

$page_title = 'Ubah Akun Admin';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<main class="admin-main">
  <header class="admin-topbar">
    <div class="page-heading">
      <h1>Ubah Akun Admin</h1>
      <p>Kelola nama profil, username login, dan ganti password administrator.</p>
    </div>
  </header>

  <div class="admin-body">
    <?php if ($success): ?>
      <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="admin-card" style="max-width: 680px;">
      <div class="admin-card-header">
        <div>
          <h2 class="admin-card-title"><i class="fa-solid fa-user-shield" style="color: var(--admin-accent);"></i> Data Akun Login</h2>
          <p class="admin-card-subtitle">Perbarui profil administrator sistem.</p>
        </div>
      </div>

      <form method="POST" action="profile.php" style="display: flex; flex-direction: column; gap: 20px;">
        <div class="form-group">
          <label class="form-label" for="name">Nama Lengkap / Panggilan</label>
          <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($current_admin['name'] ?? '') ?>" required>
          <div class="form-help">Nama yang tampil di pojok kiri bawah sidebar panel admin.</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="username">Username Login</label>
          <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($current_admin['username'] ?? '') ?>" required autocomplete="username">
          <div class="form-help">Gunakan username unik tanpa spasi untuk masuk ke halaman admin.</div>
        </div>

        <hr style="border: none; border-top: 1px solid var(--admin-border); margin: 8px 0;">

        <div style="font-weight: 700; color: #FFFFFF; font-size: 0.95rem;">
          <i class="fa-solid fa-key" style="margin-right: 8px; color: var(--admin-primary);"></i> Ganti Password (Opsional)
        </div>
        <div class="form-help" style="margin-top: -12px;">Kosongkan form password jika tidak ingin mengubah kata sandi saat ini.</div>

        <div class="form-group">
          <label class="form-label" for="current_password">Password Saat Ini</label>
          <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password" placeholder="Masukkan password sekarang jika ingin ganti baru">
        </div>

        <div class="form-group">
          <label class="form-label" for="new_password">Password Baru</label>
          <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password" placeholder="Minimal 6 karakter">
        </div>

        <div class="form-group">
          <label class="form-label" for="confirm_password">Konfirmasi Password Baru</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="new-password" placeholder="Ulangi password baru">
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Akun
          </button>
          <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
