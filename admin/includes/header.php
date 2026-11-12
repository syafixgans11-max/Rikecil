<?php
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/config/auth.php';

require_login();

$current_admin = get_current_admin();
$settings = get_settings($pdo);
$flash = get_flash();

// Active nav highlight helper
if (!function_exists('is_active_nav')) {
    function is_active_nav($page_name) {
        $current = basename($_SERVER['PHP_SELF']);
        return ($current === $page_name) ? 'active' : '';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Admin Dashboard') ?> - Panel Admin UMKM Digital</title>
  <link rel="icon" type="image/svg+xml" href="../<?= htmlspecialchars($settings['site_favicon'] ?? 'assets/images/logo.svg') ?>">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- Mobile Admin Top Bar with Menu Hamburger Toggle -->
  <div class="admin-mobile-bar">
    <button type="button" class="admin-mobile-toggle" id="adminSidebarToggle" aria-label="Buka Menu Admin">
      <i class="fa-solid fa-bars"></i>
    </button>
    <img src="../<?= htmlspecialchars($settings['site_logo'] ?? 'assets/images/logo.svg') ?>" alt="Logo" style="height: 28px; width: auto;">
    <a href="logout.php" class="admin-mobile-logout" title="Logout" onclick="return confirm('Keluar dari admin?')">
      <i class="fa-solid fa-right-from-bracket"></i>
    </a>
  </div>
  <div class="admin-backdrop" id="adminBackdrop"></div>
