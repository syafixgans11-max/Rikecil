<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <img src="../<?= htmlspecialchars($settings['site_logo'] ?? 'assets/images/logo.svg') ?>" alt="Logo">
    <button type="button" class="admin-sidebar-close" id="adminSidebarClose" aria-label="Tutup Menu">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <ul class="sidebar-menu">
    <li class="menu-category">Utama</li>
    <li class="menu-item <?= is_active_nav('index.php') ?>">
      <a href="index.php"><i class="fa-solid fa-gauge icon"></i> Dashboard</a>
    </li>
    <li class="menu-item <?= is_active_nav('settings.php') ?>">
      <a href="settings.php"><i class="fa-solid fa-palette icon"></i> Warna &amp; Identitas</a>
    </li>

    <li class="menu-category">Konten Landing Page</li>
    <li class="menu-item <?= is_active_nav('hero.php') ?>">
      <a href="hero.php"><i class="fa-solid fa-star icon"></i> Hero &amp; Banner</a>
    </li>
    <li class="menu-item <?= is_active_nav('partners.php') ?>">
      <a href="partners.php"><i class="fa-solid fa-handshake icon"></i> Supplier &amp; Partner</a>
    </li>
    <li class="menu-item <?= is_active_nav('why_us.php') ?>">
      <a href="why_us.php"><i class="fa-solid fa-thumbs-up icon"></i> Kenapa Harus Di Kami</a>
    </li>
    <li class="menu-item <?= is_active_nav('services.php') ?>">
      <a href="services.php"><i class="fa-solid fa-laptop-code icon"></i> Layanan Kami</a>
    </li>
    <li class="menu-item <?= is_active_nav('portfolio.php') ?>">
      <a href="portfolio.php"><i class="fa-solid fa-layer-group icon"></i> Portofolio</a>
    </li>
    <li class="menu-item <?= is_active_nav('showcase.php') ?>">
      <a href="showcase.php"><i class="fa-solid fa-display icon"></i> Showcase Mockup</a>
    </li>
    <li class="menu-item <?= is_active_nav('features.php') ?>">
      <a href="features.php"><i class="fa-solid fa-bolt icon"></i> Apa yang Didapatkan</a>
    </li>
    <li class="menu-item <?= is_active_nav('pricing.php') ?>">
      <a href="pricing.php"><i class="fa-solid fa-tags icon"></i> Paket Harga</a>
    </li>
    <li class="menu-item <?= is_active_nav('workflow.php') ?>">
      <a href="workflow.php"><i class="fa-solid fa-diagram-project icon"></i> Alur Kerja (Cara Kerja)</a>
    </li>
    <li class="menu-item <?= is_active_nav('faq.php') ?>">
      <a href="faq.php"><i class="fa-solid fa-circle-question icon"></i> Pertanyaan (FAQ)</a>
    </li>
    <li class="menu-item <?= is_active_nav('footer.php') ?>">
      <a href="footer.php"><i class="fa-solid fa-shoe-prints icon"></i> Banner CTA &amp; Footer</a>
    </li>
    <li class="menu-item <?= is_active_nav('feature_directory.php') ?>">
      <a href="feature_directory.php"><i class="fa-solid fa-list-check icon"></i> Direktori Fitur Lengkap</a>
    </li>

    <li class="menu-category">Pratinjau &amp; Akun</li>
    <li class="menu-item <?= is_active_nav('profile.php') ?>">
      <a href="profile.php"><i class="fa-solid fa-user-gear icon"></i> Akun Admin</a>
    </li>
    <li class="menu-item">
      <a href="../index.php" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square icon"></i> Lihat Website Asli</a>
    </li>
    <li class="menu-item">
      <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')"><i class="fa-solid fa-right-from-bracket icon" style="color: #F87171;"></i> Logout</a>
    </li>
  </ul>

  <div class="sidebar-footer">
    <a href="profile.php" class="admin-profile" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; width: 100%;" title="Klik untuk Ubah Akun Admin">
      <div class="admin-avatar"><?= strtoupper(substr($current_admin['name'], 0, 1)) ?></div>
      <div style="flex-grow: 1;">
        <div class="admin-name"><?= htmlspecialchars($current_admin['name']) ?></div>
        <div class="admin-role">Super Admin &middot; Edit Akun</div>
      </div>
      <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem; color: var(--admin-muted);"></i>
    </a>
  </div>
</aside>
