-- ==========================================================
-- UMKM Digital: Database Schema & Default Seed Data (MySQL)
-- Version: 2.2 (Client Final Revision - Full Dynamic Controls)
-- ==========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: admins
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admins` (`id`, `username`, `password`, `name`) VALUES
(1, 'admin', '$2y$12$Nq1P7G0v0yS266jX761gMevq27Nn475jA8P648X8qUqF3sY76mG8i', 'Super Admin')
ON DUPLICATE KEY UPDATE `username`=VALUES(`username`);

-- --------------------------------------------------------
-- Table: settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'UMKM Digital: Solusi Pembuatan Website Top Up Game'),
('site_tagline', 'Jasa pembuatan website toko online top up game dan voucher otomatis terintegrasi multi-supplier H2H dan payment gateway.'),
('site_logo', 'assets/images/logo.svg'),
('site_favicon', 'assets/images/logo.svg'),
('nav_cta_text', 'Hubungi Kami'),
('nav_cta_link', 'https://wa.me/6281234567890'),
('primary_color', '#2563EB'),
('accent_color', '#0284C7'),
('bg_dark', '#090D16'),
('bg_card', '#121724'),
('border_color', '#222B3D'),
('text_primary', '#F8FAFC'),
('text_secondary', '#94A3B8'),
('whatsapp_number', '6281234567890'),
('footer_copyright', '2026 UMKM Digital. Hak Cipta Dilindungi.'),
('showcase_enabled', '1'),
('pricing_columns', '4'),
('features_tab_1', 'Website'),
('features_tab_2', 'Poster Design'),
('features_tab_3', 'Character'),
('showcase_title', 'Nikmati berbagai macam fitur yang telah kami sediakan disini'),
('showcase_subtitle', 'Layanan yang kami tawarkan memiliki banyak fitur yang bisa Anda dapatkan, berikut fiturnya:'),
('workflow_title', 'Dari ngobrol sampai toko tayang, empat langkah'),
('workflow_subtitle', 'Kamu tidak perlu paham teknis - bagian rumitnya kami yang kerjakan.')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

-- --------------------------------------------------------
-- Table: hero_section
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hero_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_text` varchar(100) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `btn_primary_text` varchar(50) DEFAULT NULL,
  `btn_primary_link` varchar(255) DEFAULT NULL,
  `btn_secondary_text` varchar(50) DEFAULT NULL,
  `btn_secondary_link` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hero_section` (`id`, `badge_text`, `title`, `subtitle`, `btn_primary_text`, `btn_primary_link`, `btn_secondary_text`, `btn_secondary_link`, `hero_image`) VALUES
(1, 'LAYANAN PEMBUATAN WEBSITE RESELLER', 'Your Business, Our Expertise. Let\'s Grow Together.', 'Solusi pembuatan website top-up game dan voucher digital terotomasi penuh untuk UMKM & Reseller. Terintegrasi penuh dengan multi-supplier, payment gateway QRIS/VA otomatis, dan sistem kelola keuntungan realtime.', 'Konsultasi Sekarang', 'https://wa.me/6281234567890', 'Lihat Portofolio', '#portofolio', 'assets/images/hero-slide-1-desktop.svg')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- --------------------------------------------------------
-- Table: hero_slides (Multi-Device Auto-Slider)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desktop_image` varchar(255) DEFAULT NULL,
  `mobile_image` varchar(255) DEFAULT NULL,
  `url_label` varchar(150) DEFAULT 'toko-anda.com',
  `caption` varchar(200) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hero_slides` (`id`, `desktop_image`, `mobile_image`, `url_label`, `caption`, `sort_order`) VALUES
(1, 'assets/images/hero-slide-1-desktop.svg', 'assets/images/hero-slide-1-mobile.svg', 'lukifays.id/store-demo', 'Tampilan Storefront Utama Responsif', 1),
(2, 'assets/images/hero-slide-2-desktop.svg', 'assets/images/hero-slide-2-mobile.svg', 'lukifays.id/checkout', 'Alur Checkout & QRIS Instan', 2),
(3, 'assets/images/hero-slide-3-desktop.svg', 'assets/images/hero-slide-3-mobile.svg', 'admin.lukifays.id', 'Panel Kendali & Laporan Laba', 3);

-- --------------------------------------------------------
-- Table: partners
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `logo_type` varchar(20) DEFAULT 'image',
  `display_mode` varchar(30) DEFAULT 'logo_text',
  `logo_image` varchar(255) DEFAULT NULL,
  `badge_color` varchar(20) DEFAULT '#2563EB',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `partners` (`id`, `name`, `logo_type`, `display_mode`, `logo_image`, `badge_color`, `sort_order`) VALUES
(1, 'Digiflazz', 'image', 'logo_text', 'assets/images/partners/digiflazz.svg', '#2563EB', 1),
(2, 'VIP Payment', 'image', 'logo_text', 'assets/images/partners/vippayment.svg', '#0284C7', 2),
(3, 'Tokovoucher', 'image', 'logo_text', 'assets/images/partners/tokovoucher.svg', '#D97706', 3),
(4, 'Apigames', 'image', 'logo_text', 'assets/images/partners/apigames.svg', '#16A34A', 4),
(5, 'UniPin', 'image', 'logo_text', 'assets/images/partners/unipin.svg', '#DC2626', 5),
(6, 'Bangjeff', 'image', 'logo_text', 'assets/images/partners/bangjeff.svg', '#4F46E5', 6);

-- --------------------------------------------------------
-- Table: why_us
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `why_us` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `why_us` (`id`, `icon`, `title`, `description`, `sort_order`) VALUES
(1, 'sliders', 'Full Customable', 'Tampilan website, struktur katalog, skema warna, dan logo toko disesuaikan sepenuhnya dengan identitas branding bisnismu.', 1),
(2, 'server', 'All-in-One Service', 'Solusi terpadu mencakup pembelian domain, cloud hosting kencang, integrasi supplier H2H, hingga payment gateway instan.', 2),
(3, 'gamepad', 'Complete Digital Product', 'Dukungan penuh untuk top-up 40+ game populer (MLBB, FF, Genshin), voucher streaming, pulsa, dan paket data.', 3),
(4, 'shield-halved', 'AI Business Assistant', 'Fitur asisten otomatisasi untuk memantau transaksi, alert stok supplier, hingga laporan rekap laba rugi otomatis.', 4);

-- --------------------------------------------------------
-- Table: services (3 Main Tabs)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step_number` varchar(10) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `features_list` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'code',
  `icon_image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `services` (`id`, `step_number`, `title`, `description`, `icon`, `icon_image`, `sort_order`) VALUES
(1, '01', 'Web Development', 'Pembuatan website toko online top up game dan voucher otomatis berkinerja tinggi, responsif, dan mudah dikelola dari panel admin.', 'code', 'assets/images/service-web.svg', 1),
(2, '02', 'Creative Studio', 'Desain visual branding eksklusif untuk meningkatkan kepercayaan pelanggan dan daya tarik toko di media sosial.', 'palette', 'assets/images/service-creative.svg', 2),
(3, '03', 'Commission Design', 'Pembuatan aset visual tematik khusus, maskot karakter toko, dan elemen UI kustom untuk identitas yang unik.', 'wand-magic-sparkles', 'assets/images/service-commission.svg', 3);

-- --------------------------------------------------------
-- Table: service_cards (6 Dynamic Cards per Tab)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT 'check',
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `service_cards` (`id`, `service_id`, `icon`, `title`, `description`, `sort_order`) VALUES
(1, 1, 'bolt', 'Transaksi Otomatis 24 Jam Nonstop', 'Pesanan diproses dalam hitungan detik tanpa campur tangan admin, terhubung langsung ke API multi-supplier dengan notifikasi status realtime.', 1),
(2, 1, 'credit-card', 'Multi Payment Gateway Sekaligus', 'Dukungan integrasi QRIS Dinamis (GoPay, OVO, Dana, ShopeePay) serta Virtual Account bank besar dengan auto-rekonsiliasi mutasi.', 2),
(3, 1, 'tags', 'Khusus Harga Khusus (Tiering Reseller)', 'Pengaturan harga jual bertingkat untuk member biasa, reseller terverifikasi, hingga agen distributor secara otomatis.', 3),
(4, 1, 'bell', 'Notifikasi Bot & Reseller Otomatis', 'Pengiriman invoice dan struk otomatis via bot WhatsApp ke pembeli, serta peringatan saldo supplier kritis ke Telegram admin.', 4),
(5, 1, 'id-badge', 'Cek Nickname Otomatis All - Game', 'Validasi User ID dan Zone ID akun pemain sebelum pembayaran untuk Mobile Legends, Free Fire, PUBG, Genshin, dan 40+ game.', 5),
(6, 1, 'wallet', 'Web Dinamis Semua E-Wallet', 'Tampilan website responsif, ultra-ringan saat dibuka lewat smartphone, dan mudah dikustomisasi dari dashboard admin.', 6),
(7, 2, 'crown', 'Desain Logo Vektor & Favicon Toko', 'Identitas visual profesional format SVG tajam tanpa pecah untuk logo website, favicon browser, dan header aplikasi.', 1),
(8, 2, 'image', 'Banner Promo & Event Games', 'Desain visual promosi event turnamen, flash sale diamond, dan paket bundling game format desktop dan mobile HD.', 2),
(9, 2, 'hashtag', 'Template Feed & Story Instagram', 'Paket template promosi media sosial seragam dan konsisten untuk membangun reputasi dan kredibilitas toko Anda.', 3),
(10, 2, 'receipt', 'Watermark & Struk Mockup Transaksi', 'Desain invoice berstempel resmi toko untuk dibagikan sebagai bukti transaksi dan testimoni terpercaya ke pelanggan.', 4),
(11, 2, 'video', 'Aset Animasi Transaksi Story', 'Template video vertikal singkat untuk update stok masuk dan bukti transaksi harian di status WhatsApp dan TikTok.', 5),
(12, 2, 'swatchbook', 'Panduan Brand & Skema Warna Toko', 'Dokumentasi kode warna identitas toko dan tipografi agar seluruh materi promosi terlihat solid dan rapi.', 6),
(13, 3, 'ghost', 'Karakter Maskot 2D/3D Eksklusif', 'Pembuatan maskot karakter unik bertema gaming atau futuristik sebagai duta visual brand toko online Anda.', 1),
(14, 3, 'panorama', 'Ilustrasi Vektor Hero Banner', 'Header website kustom dengan gambar vektor ilustrasi karakter game dan tipografi eksklusif non-template.', 2),
(15, 3, 'comments', 'Desain Stiker & Persona WhatsApp', 'Koleksi stiker WhatsApp ekspresif bertema maskot toko untuk interaksi customer care yang ramah dan akrab.', 3),
(16, 3, 'compass-drafting', 'UI/UX Custom Layout Sesuai Request', 'Penyesuaian tata letak kartu produk, form checkout, dan alur pembayaran sesuai alur bisnis spesifik Anda.', 4),
(17, 3, 'spinner', 'Animasi Loading Toko Kustom', 'Efek transisi halaman dan loading spinner bertema logo toko saat pengunjung berpindah halaman produk.', 5),
(18, 3, 'icons', 'Paket Ikon Game Vektor Kustom', 'Set ikon produk game bergaya seragam agar katalog toko terlihat rapi, bersih, dan memikat pembeli.', 6);

-- --------------------------------------------------------
-- Table: portfolio
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT '#',
  `action_type` varchar(20) DEFAULT 'modal',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `portfolio` (`id`, `title`, `category`, `description`, `tags`, `image`, `demo_url`, `action_type`, `sort_order`) VALUES
(1, 'Lukifays Store', 'Website', 'Website top up game dengan sistem pesanan instan dan integrasi multi-supplier Digiflazz dan VIP Payment.', 'Top Up Game, Reseller Otomatis, Payment Gateway', 'assets/images/portfolio-1.svg', 'https://lukifays.store', 'modal', 1),
(2, 'Vouchershop ID', 'Website', 'Platform penjualan voucher digital, token listrik PLN, dan paket data dengan sistem deposit saldo member.', 'PPOB, Voucher Streaming, Saldo Member', 'assets/images/portfolio-2.svg', 'https://vouchershop.id', 'modal', 2),
(3, 'Nexus Topup Esports', 'Website', 'Portal top up game turnamen dengan validasi User ID otomatis dan promo berkala.', 'Esports, Validasi ID, Multi-Supplier', 'assets/images/portfolio-3.svg', 'https://nexustopup.com', 'link', 3),
(4, 'Banner Promosi Mobile Legends', 'Poster Design', 'Desain materi promosi diamond Mobile Legends untuk posting media sosial toko.', 'Desain Grafis, Branding Toko, Promosi', 'assets/images/portfolio-1.svg', '#', 'modal', 4),
(5, 'Aset Maskot Toko Digital', 'Character', 'Desain karakter maskot 2D sebagai elemen penguat visual branding toko digital.', 'Maskot Toko, Ilustrasi Vektor, Branding', 'assets/images/portfolio-3.svg', '#', 'modal', 5);

-- --------------------------------------------------------
-- Table: showcase
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `showcase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stat1_num` varchar(50) DEFAULT NULL,
  `stat1_label` varchar(100) DEFAULT NULL,
  `stat2_num` varchar(50) DEFAULT NULL,
  `stat2_label` varchar(100) DEFAULT NULL,
  `stat3_num` varchar(50) DEFAULT NULL,
  `stat3_label` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `showcase` (`id`, `title`, `subtitle`, `image`, `stat1_num`, `stat1_label`, `stat2_num`, `stat2_label`, `stat3_num`, `stat3_label`) VALUES
(1, 'Nikmati berbagai macam fitur yang telah kami sediakan disini', 'Layanan yang kami tawarkan memiliki banyak fitur yang bisa Anda dapatkan, berikut fiturnya:', 'assets/images/admin-mockup.svg', '24 Jam', 'Pemrosesan Pesanan Otomatis', 'Multi-Supplier', 'Failover Otomatis Tanpa Gangguan', 'Semua Saluran', 'QRIS Dinamis dan Virtual Account');

-- --------------------------------------------------------
-- Table: showcase_tabs (Interactive Tabs: Customable, Checkout, Dashboard Admin)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `showcase_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tab_label` varchar(100) NOT NULL,
  `address_url` varchar(150) DEFAULT 'toko-anda.com',
  `image` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `showcase_tabs` (`id`, `tab_label`, `address_url`, `image`, `caption`, `sort_order`) VALUES
(1, 'Customable', 'lukifays-store.com', 'assets/images/showcase-customable.svg', 'Kustomisasi toko tanpa batas: atur banner, warna aksen, logo, dan susunan produk sesuai brand Anda.', 1),
(2, 'Checkout', 'lukifays-store.com/checkout', 'assets/images/showcase-checkout.svg', 'Alur checkout ringkas dan cepat: customer mengisi data, memilih payment, dan transaksi langsung terproses otomatis.', 2),
(3, 'Dashboard Admin', 'admin.lukifays-store.com', 'assets/images/admin-mockup.svg', 'Panel admin realtime: pendapatan harian, profit bersih, status transaksi, dan stok supplier dalam satu layar.', 3);

-- --------------------------------------------------------
-- Table: feature_highlights
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feature_highlights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT 'Website',
  `icon` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `feature_highlights` (`id`, `category`, `icon`, `title`, `description`, `sort_order`) VALUES
(1, 'Website', 'id-card', 'Validasi Nickname Otomatis', 'Pengecekan User ID dan Zone ID secara instan untuk 40+ game guna memastikan diamond terkirim ke akun yang tepat.', 1),
(2, 'Website', 'credit-card', 'Semua Saluran Pembayaran', 'Menerima QRIS dari semua e-wallet dan perbankan, Virtual Account bank besar, serta gerai retail.', 2),
(3, 'Website', 'network-wired', 'Multi-Supplier Failover', 'Tersambung ke beberapa supplier sekaligus: sistem otomatis mengalihkan pesanan jika salah satu supplier mengalami gangguan stok.', 3),
(4, 'Website', 'users-gear', 'Tiering Harga Khusus Reseller', 'Atur harga jual berbeda untuk pengguna umum, member terdaftar, reseller VIP, hingga level agen distributor.', 4),
(5, 'Website', 'tag', 'Promo Diskon & Kupon', 'Atur potongan harga berbatas waktu atau kupon promo untuk meningkatkan volume transaksi toko.', 5),
(6, 'Website', 'chart-line', 'Rekap Laba dan Mutasi', 'Laporan transaksi harian, kalkulasi margin bersih, dan rekapan mutasi saldo dapat dipantau dan diekspor ke Excel.', 6),
(7, 'Website', 'shield-halved', 'Keamanan Transaksi & Sesi', 'Enkripsi SSL aktif, proteksi brute force login, dan pencatatan audit log untuk setiap perubahan konfigurasi.', 7),
(8, 'Website', 'code', 'Koneksi REST API Reseller', 'Dokumentasi RESTful API terstandarisasi untuk menghubungkan website toko Anda ke platform reseller pihak ketiga.', 8),
(9, 'Poster Design', 'palette', 'Desain Banner Promo Event', 'Banner promosi turnamen dan flash sale diamond dengan resolusi HD siap posting untuk web maupun Instagram.', 1),
(10, 'Poster Design', 'layer-group', 'Template Feed & Story IG', 'Desain template katalog harga dan testimoni ber-watermark toko agar terlihat profesional dan seragam.', 2),
(11, 'Poster Design', 'file-image', 'Format Source Editable & PNG', 'Disediakan file master siap pakai dengan komposisi warna sesuai identitas brand toko digital Anda.', 3),
(12, 'Poster Design', 'mobile-screen', 'Optimasi Rasio Desktop & Mobile', 'Setiap banner disesuaikan ukurannya agar tajam di layar smartphone maupun monitor widescreen.', 4),
(13, 'Character', 'user-astronaut', 'Maskot Karakter 2D/3D Eksklusif', 'Pembuatan maskot karakter unik bertema gaming sebagai identitas pembeda toko Anda dibanding kompetitor.', 1),
(14, 'Character', 'wand-magic-sparkles', 'Beragam Pose & Ekspresi', 'Karakter dilengkapi variasi pose untuk diletakkan di halaman utama, banner promo, hingga stiker WhatsApp.', 2),
(15, 'Character', 'pen-nib', 'Artwork Original Vektor Tajam', 'Digambar secara kustom oleh ilustrator profesional bukan aset stok pasaran sehingga aman digunakan komersial.', 3),
(16, 'Character', 'shield-halved', 'Hak Kepemilikan Penuh Klien', 'File resolusi tinggi transparan diserahkan sepenuhnya untuk kebutuhan merchandise maupun branding toko.', 4);

-- --------------------------------------------------------
-- Table: pricing_plans
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pricing_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT 'website',
  `name` varchar(100) NOT NULL,
  `tier_subtitle` varchar(150) DEFAULT NULL,
  `old_price` varchar(50) DEFAULT NULL,
  `price` varchar(50) NOT NULL,
  `period` varchar(50) DEFAULT '/selamanya',
  `badge_text` varchar(50) DEFAULT NULL,
  `badge_color` varchar(20) DEFAULT '#2563EB',
  `is_popular` tinyint(1) DEFAULT 0,
  `features_list` text DEFAULT NULL,
  `btn_text` varchar(50) DEFAULT 'Pilih Paket',
  `btn_link` varchar(255) DEFAULT '#',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pricing_plans` (`id`, `category`, `name`, `tier_subtitle`, `old_price`, `price`, `period`, `badge_text`, `badge_color`, `is_popular`, `features_list`, `btn_text`, `btn_link`, `sort_order`) VALUES
(1, 'website', 'SILVER', 'Cocok untuk pemula yang baru merintis usaha toko online top up', 'Rp 2.000.000', 'Rp 1.500.000', '/selamanya', 'HEMAT 25%', '#64748B', 0, "Domain .com / .my.id (1 tahun)\nCloud Hosting Cepat & SSL Aktif\n1 Koneksi API Supplier Otomatis\nIntegrasi QRIS Pembayaran Instan\nKatalog 20+ Game Populer\nGaransi Sistem 1 Bulan", 'Pilih Paket', 'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20SILVER', 1),
(2, 'website', 'GOLD', 'Cocok untuk kamu yang ingin toko profesional dengan multi-supplier', 'Rp 7.000.000', 'Rp 5.000.000', '/selamanya', 'TERBAIK PEMULA', '#2563EB', 0, "Domain .com / .id (1 tahun)\nHigh Speed Cloud Hosting SSD/NVMe\nMulti-Supplier H2H hingga 3 Supplier\nSemua Channel Pembayaran (QRIS, VA, E-Wallet)\nCek Nickname Otomatis 40+ Game\nSistem Tiering Harga Reseller\nGaransi & Support 3 Bulan", 'Pilih Paket', 'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20GOLD', 2),
(3, 'website', 'PLATINUM', 'Cocok untuk kamu yang ingin toko kompetitif, fitur premium & butuh branding sendiri', 'Rp 10.000.000', 'Rp 8.500.000', '/selamanya', 'PALING POPULER', '#2563EB', 1, "Semua Fitur Paket Gold\nDedicated Cloud Server (Anti-RTO)\nMulti-Supplier Unlimited + Failover Otomatis\nBot Notifikasi WhatsApp Otomatis ke Pembeli & Admin\nDesain Custom Fleksibel + Branding Identitas\nPaket Aset Banner Promosi & Feed IG (10 Desain)\nSupport Prioritas 6 Bulan", 'Pilih Paket', 'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20PLATINUM', 3),
(4, 'website', 'VIP EXPERT', 'Paket paling lengkap dan mendalam hingga ke skala UMKM Digital', '', 'Rp 12.000.000', '/selamanya', 'PALING CANGGIH', '#0284C7', 0, "Semua Fitur Paket Platinum\nDedicated Private Server (VPS Enterprise)\nAplikasi Android APK Siap Upload Google Play Store\nSistem Manajemen Multi-Admin & Hak Akses\nKonsultasi Bisnis & Strategi Marketing 1-on-1\nIntegrasi WhatsApp Bot Multi-Device\nSupport VIP 1 Tahun", 'Pilih Paket', 'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20VIP%20EXPERT', 4);

-- --------------------------------------------------------
-- Table: workflow_steps
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `workflow_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step_number` varchar(10) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `workflow_steps` (`id`, `step_number`, `title`, `description`, `sort_order`) VALUES
(1, '1', 'Konsultasi', 'Ceritakan kebutuhan tokomu lewat WhatsApp, kami bantu menentukan domain, supplier, dan channel pembayaran yang pas.', 1),
(2, '2', 'Setup & Integrasi', 'Kami siapkan website dan panel admin, lalu hubungkan payment gateway dan supplier pilihanmu sampai transaksi berjalan.', 2),
(3, '3', 'Branding Toko', 'Nama, logo, warna, dan banner diatur sesuai identitas tokomu hingga website tampil sepenuhnya atas nama brandmu.', 3),
(4, '4', 'Go Live', 'Toko tayang di domainmu sendiri. Kamu fokus jualan; pembaruan fitur platform datang berkala.', 4);

-- --------------------------------------------------------
-- Table: faqs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `faqs` (`id`, `question`, `answer`, `sort_order`) VALUES
(1, 'Apakah website ini menggunakan sistem sewa bulanan?', 'Tidak. Sistem ini menggunakan lisensi kepemilikan mandiri. Anda hanya membayar biaya perpanjangan domain dan server hosting tahunan tanpa potongan bagi hasil transaksi.', 1),
(2, 'Bagaimana jika salah satu supplier top-up mengalami gangguan?', 'Sistem kami telah dilengkapi fitur auto-failover: jika supplier utama mengalami gangguan stok atau maintenance, pesanan secara otomatis dialihkan ke supplier cadangan Anda.', 2),
(3, 'Apakah saya bisa menentukan margin keuntungan sendiri?', 'Bisa. Di panel admin tersedia menu pengaturan harga di mana Anda dapat menentukan markup keuntungan nominal tetap (misal Rp 2.000 per transaksi) atau persentase untuk setiap level member.', 3),
(4, 'Metode pembayaran apa saja yang bisa digunakan pembeli?', 'Website mendukung QRIS instan dari semua e-wallet (GoPay, OVO, Dana, ShopeePay) dan mobile banking, Virtual Account bank besar, serta pembayaran kasir retail Alfamart dan Indomaret.', 4),
(5, 'Apakah saya mendapatkan akses source code dan database penuh?', 'Ya. Semua source code PHP dan struktur database diserahkan penuh kepada Anda tanpa proteksi ionCube atau enkripsi pihak ketiga.', 5),
(6, 'Bagaimana bantuan teknis jika terjadi kendala operasional?', 'Kami memberikan garansi pemeliharaan sistem dan bantuan teknis melalui grup WhatsApp khusus sesuai durasi masa garansi paket yang Anda pilih.', 6);

-- --------------------------------------------------------
-- Table: expanded_features
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expanded_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) DEFAULT 'check',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `expanded_features` (`id`, `category_code`, `category_name`, `title`, `description`, `icon`, `sort_order`) VALUES
(1, 'transaksi', 'Transaksi & Otomasi', 'Pemrosesan Pesanan 24 Jam Nonstop', 'Pesanan diteruskan ke server supplier secara instan tanpa memerlukan konfirmasi manual admin.', 'clock', 1),
(2, 'transaksi', 'Transaksi & Otomasi', 'Validasi User ID Otomatis', 'Menampilkan nama akun in-game pembeli untuk 40+ judul game sebelum pembayaran dilakukan.', 'id-card', 2),
(3, 'transaksi', 'Transaksi & Otomasi', 'Sinkronisasi Harga Modal Otomatis', 'Perubahan harga modal dari supplier tersinkronisasi otomatis sehingga margin keuntungan toko tetap terjaga.', 'arrows-rotate', 3),
(4, 'transaksi', 'Transaksi & Otomasi', 'Failover Multi-Supplier Cerdas', 'Pengalihan otomatis ke supplier alternatif jika supplier utama kehabisan kuota atau mengalami kendala jaringan.', 'server', 4),
(5, 'transaksi', 'Transaksi & Otomasi', 'Halaman Invoice Realtime', 'Halaman status pembayaran dinamis dengan kode QRIS dan pembaruan status pesanan tanpa perlu muat ulang halaman.', 'receipt', 5),
(6, 'transaksi', 'Transaksi & Otomasi', 'Pelacakan Pesanan Mandiri', 'Pelanggan dapat mengecek riwayat dan status transaksi kapan saja melalui nomor invoice atau nomor WhatsApp.', 'magnifying-glass', 6),
(7, 'pembayaran', 'Pembayaran', 'Dukungan Berbagai Payment Gateway', 'Tersedia integrasi untuk Duitku, LinkQu, Tripay, Flip, dan Midtrans dalam satu platform.', 'wallet', 1),
(8, 'pembayaran', 'Pembayaran', 'QRIS Dinamis Nasional', 'Menerima pembayaran QRIS dari semua aplikasi e-wallet dan perbankan di Indonesia.', 'qrcode', 2),
(9, 'pembayaran', 'Pembayaran', 'Virtual Account Semua Bank Utama', 'Verifikasi otomatis pembayaran Virtual Account bank BCA, Mandiri, BNI, dan BRI.', 'building-columns', 3),
(10, 'pembayaran', 'Pembayaran', 'Gerai Retail Alfamart & Indomaret', 'Pilihan pembayaran tunai melalui kasir gerai retail terdekat bagi pelanggan yang belum memiliki rekening.', 'store', 4),
(11, 'pembayaran', 'Pembayaran', 'Sistem Saldo Deposit Member', 'Pelanggan terdaftar dapat mengisi saldo akun untuk proses checkout kilat hanya dengan satu klik.', 'coins', 5),
(12, 'reseller', 'Member & Reseller', 'Pengaturan Level Harga Bertingkat', 'Bebas menentukan margin keuntungan berbeda untuk Pembeli Tamu, Member, Reseller, dan Agen.', 'users', 1),
(13, 'reseller', 'Member & Reseller', 'REST API Khusus Reseller', 'Menyediakan endpoint RESTful API terstandarisasi agar website Anda dapat menjadi penyedia stok bagi reseller lain.', 'code', 2),
(14, 'reseller', 'Member & Reseller', 'Dashboard Khusus Pengguna', 'Area member yang rapi untuk memantau riwayat pembelian, mutasi deposit, dan layanan tiket bantuan.', 'table-columns', 3),
(15, 'laporan', 'Laporan & Keuangan', 'Kalkulasi Margin Bersih Harian', 'Menghitung selisih harga jual dan harga modal supplier secara akurat untuk setiap transaksi sukses.', 'calculator', 1),
(16, 'laporan', 'Laporan & Keuangan', 'Statistik Produk Terlaris', 'Visualisasi data transaksi untuk memantau nominal dan jenis game yang paling diminati pembeli.', 'chart-column', 2),
(17, 'laporan', 'Laporan & Keuangan', 'Ekspor Laporan ke Format Excel', 'Unduh rekapan transaksi dan data keuangan toko untuk mempermudah pembukuan berkala.', 'file-excel', 3);

COMMIT;
