<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/auth.php';

$settings = get_settings($pdo);
$error = '';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['name'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau Password salah!';
        }
    } else {
        $error = 'Harap isi username dan password!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Administrator - <?= htmlspecialchars($settings['site_title'] ?? 'UMKM Digital') ?></title>
  <link rel="icon" type="image/svg+xml" href="../<?= htmlspecialchars($settings['site_favicon'] ?? 'assets/images/logo.svg') ?>">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">
  <div class="login-card">
    <div class="login-logo">
      <img src="../<?= htmlspecialchars($settings['site_logo'] ?? 'assets/images/logo.svg') ?>" alt="Logo">
      <h2 style="font-size: 1.3rem; font-weight: 800;">Panel Administrator</h2>
      <p style="font-size: 0.85rem; color: #94A3B8; margin-top: 4px;">Kelola Semua Konten &amp; Tampilan Landing Page</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger" style="font-size: 0.85rem;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-user"></i> Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="" autocomplete="off" required autofocus>
      </div>

      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" value="" autocomplete="new-password" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Dashboard
      </button>

      <div style="text-align: center; margin-top: 24px;">
        <a href="../index.php" style="color: #06B6D4; font-size: 0.85rem; text-decoration: none;">
          <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Utama
        </a>
      </div>
    </form>
  </div>
</body>
</html>
