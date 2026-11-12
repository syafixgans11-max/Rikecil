<?php
/**
 * Database Connection & Initialization
 * Supports both MySQL and SQLite fallback secara otomatis
 */

if (!ob_get_level()) {
    ob_start();
}

define('DB_CONFIG_FILE', __DIR__ . '/db_config.php');

// Default database configuration (MySQL)
$db_driver = 'auto'; // 'auto', 'mysql', or 'sqlite'
$mysql_host = 'localhost';
$mysql_port = '3306';
$mysql_db   = 'landing_page_umkm';
$mysql_user = 'root';
$mysql_pass = '';

if (file_exists(DB_CONFIG_FILE)) {
    include DB_CONFIG_FILE;
}

$pdo = null;
$db_type_used = 'sqlite';

// Try MySQL first if driver is 'auto' or 'mysql'
if ($db_driver === 'auto' || $db_driver === 'mysql') {
    try {
        $tempPdo = new PDO(
            "mysql:host={$mysql_host};port={$mysql_port};charset=utf8mb4",
            $mysql_user,
            $mysql_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 2]
        );
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `{$mysql_db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $tempPdo = null;

        $pdo = new PDO(
            "mysql:host={$mysql_host};port={$mysql_port};dbname={$mysql_db};charset=utf8mb4",
            $mysql_user,
            $mysql_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        $db_type_used = 'mysql';
    } catch (Exception $e) {
        if ($db_driver === 'mysql') {
            die("Koneksi MySQL Gagal: " . $e->getMessage());
        }
        $pdo = null;
    }
}

// Fallback to SQLite
if (!$pdo) {
    try {
        $sqlite_dir = dirname(__DIR__) . '/data';
        if (!is_dir($sqlite_dir)) {
            mkdir($sqlite_dir, 0777, true);
        }
        $sqlite_path = $sqlite_dir . '/landing_page.db';
        $pdo = new PDO(
            "sqlite:" . $sqlite_path,
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        $db_type_used = 'sqlite';
    } catch (Exception $e) {
        die("Koneksi Database Gagal: " . $e->getMessage());
    }
}

// Auto Initialize tables if they do not exist
function init_database_tables($pdo, $db_type) {
    $primary_key = ($db_type === 'mysql') ? 'INT PRIMARY KEY AUTO_INCREMENT' : 'INTEGER PRIMARY KEY AUTOINCREMENT';

    // 1. Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT
    )");

    // 2. Admins Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id {$primary_key},
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Hero Section Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_section (
        id {$primary_key},
        badge_text VARCHAR(100),
        title TEXT,
        subtitle TEXT,
        btn_primary_text VARCHAR(100),
        btn_primary_link VARCHAR(255),
        btn_secondary_text VARCHAR(100),
        btn_secondary_link VARCHAR(255),
        hero_image VARCHAR(255),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Partner Logos
    $pdo->exec("CREATE TABLE IF NOT EXISTS partners (
        id {$primary_key},
        name VARCHAR(100),
        logo_type VARCHAR(20) DEFAULT 'badge',
        logo_image VARCHAR(255),
        badge_color VARCHAR(50) DEFAULT '#2563EB',
        sort_order INT DEFAULT 0
    )");

    // 5. Why Choose Us (Kenapa Harus Di Kami)
    $pdo->exec("CREATE TABLE IF NOT EXISTS why_us (
        id {$primary_key},
        icon VARCHAR(50),
        title VARCHAR(150),
        description TEXT,
        sort_order INT DEFAULT 0
    )");

    // 6. Services (Apa Saja yang Kami Kerjakan - 3 Tabs)
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id {$primary_key},
        step_number VARCHAR(10),
        title VARCHAR(150),
        description TEXT,
        features_list TEXT,
        icon VARCHAR(50) DEFAULT 'code',
        icon_image VARCHAR(255) DEFAULT '',
        sort_order INT DEFAULT 0
    )");

    // 6b. Service Cards (6 cards per tab)
    $pdo->exec("CREATE TABLE IF NOT EXISTS service_cards (
        id {$primary_key},
        service_id INT NOT NULL,
        icon VARCHAR(50) DEFAULT 'check',
        title VARCHAR(150) NOT NULL,
        description TEXT,
        sort_order INT DEFAULT 0
    )");

    // 7. Portfolio Items
    $pdo->exec("CREATE TABLE IF NOT EXISTS portfolio (
        id {$primary_key},
        title VARCHAR(150),
        category VARCHAR(50),
        description TEXT,
        tags VARCHAR(255),
        image VARCHAR(255),
        demo_url VARCHAR(255),
        sort_order INT DEFAULT 0
    )");

    // 8. Showcase Section
    $pdo->exec("CREATE TABLE IF NOT EXISTS showcase (
        id {$primary_key},
        title TEXT,
        subtitle TEXT,
        image VARCHAR(255),
        stat1_num VARCHAR(50),
        stat1_label VARCHAR(100),
        stat2_num VARCHAR(50),
        stat2_label VARCHAR(100),
        stat3_num VARCHAR(50),
        stat3_label VARCHAR(100)
    )");

    // 9. Feature Highlights (Apa yang Anda Dapatkan)
    $pdo->exec("CREATE TABLE IF NOT EXISTS feature_highlights (
        id {$primary_key},
        icon VARCHAR(50),
        title VARCHAR(150),
        description TEXT,
        sort_order INT DEFAULT 0
    )");

    // 10. Pricing Plans
    $pdo->exec("CREATE TABLE IF NOT EXISTS pricing_plans (
        id {$primary_key},
        category VARCHAR(50) DEFAULT 'website',
        name VARCHAR(100),
        tier_subtitle VARCHAR(255),
        old_price VARCHAR(50),
        price VARCHAR(50),
        period VARCHAR(50),
        badge_text VARCHAR(100),
        badge_color VARCHAR(50),
        is_popular INT DEFAULT 0,
        features_list TEXT,
        btn_text VARCHAR(100),
        btn_link VARCHAR(255),
        sort_order INT DEFAULT 0
    )");

    // 11. Workflow Steps (Cara Kerja)
    $pdo->exec("CREATE TABLE IF NOT EXISTS workflow_steps (
        id {$primary_key},
        step_number VARCHAR(10),
        title VARCHAR(150),
        description TEXT,
        sort_order INT DEFAULT 0
    )");

    // 12. FAQ Items
    $pdo->exec("CREATE TABLE IF NOT EXISTS faqs (
        id {$primary_key},
        question TEXT,
        answer TEXT,
        sort_order INT DEFAULT 0
    )");

    // 13. Expanded Features Directory
    $pdo->exec("CREATE TABLE IF NOT EXISTS expanded_features (
        id {$primary_key},
        category_code VARCHAR(50),
        category_name VARCHAR(100),
        title VARCHAR(150),
        description TEXT,
        icon VARCHAR(50),
        sort_order INT DEFAULT 0
    )");

    // 14. Hero Auto-Slides (Multi-device Browser + Mobile Showcase)
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
        id {$primary_key},
        desktop_image VARCHAR(255),
        mobile_image VARCHAR(255),
        url_label VARCHAR(150) DEFAULT 'toko-anda.com',
        caption VARCHAR(200),
        sort_order INT DEFAULT 0
    )");

    // 15. Showcase Tabs (Customable, Checkout, Dashboard Admin)
    $pdo->exec("CREATE TABLE IF NOT EXISTS showcase_tabs (
        id {$primary_key},
        tab_label VARCHAR(100) NOT NULL,
        address_url VARCHAR(150) DEFAULT 'toko-anda.com',
        image VARCHAR(255),
        caption TEXT,
        sort_order INT DEFAULT 0
    )");

    // Check if seeded
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        seed_default_data($pdo);
    }

    // Run idempotent upgrades on existing tables/rows
    migrate_missing_schema($pdo);
}

function migrate_missing_schema($pdo) {
    $alters = [
        "ALTER TABLE partners ADD COLUMN display_mode VARCHAR(30) DEFAULT 'logo_text'",
        "ALTER TABLE portfolio ADD COLUMN action_type VARCHAR(20) DEFAULT 'modal'",
        "ALTER TABLE portfolio ADD COLUMN logo_image VARCHAR(255) DEFAULT ''",
        "ALTER TABLE feature_highlights ADD COLUMN category VARCHAR(50) DEFAULT 'Website'"
    ];
    foreach ($alters as $sql) {
        try {
            $pdo->exec($sql);
        } catch (Exception $e) {
            // Column already exists
        }
    }

    // Ensure new default settings exist
    $defaultNewSettings = [
        'showcase_enabled' => '1',
        'pricing_columns' => '4',
        'features_tab_1' => 'Website',
        'features_tab_2' => 'Poster Design',
        'features_tab_3' => 'Character',
        'showcase_title' => 'Nikmati berbagai macam fitur yang telah kami sediakan disini',
        'showcase_subtitle' => 'Layanan yang kami tawarkan memiliki banyak fitur yang bisa Anda dapatkan, berikut fiturnya:',
        'workflow_title' => 'Dari ngobrol sampai toko tayang, empat langkah',
        'workflow_subtitle' => 'Kamu tidak perlu paham teknis - bagian rumitnya kami yang kerjakan.'
    ];
    $isMysql = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql');
    $sqlIgnore = $isMysql
        ? "INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)"
        : "INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)";
    $insertSetting = $pdo->prepare($sqlIgnore);
    foreach ($defaultNewSettings as $k => $v) {
        try {
            $insertSetting->execute([$k, $v]);
        } catch (Exception $e) {}
    }

    // Seed Hero Slides if empty
    $hsCount = $pdo->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
    if ($hsCount == 0) {
        $slides = [
            ['assets/images/hero-slide-1-desktop.svg', 'assets/images/hero-slide-1-mobile.svg', 'lukifays.id/store-demo', 'Tampilan Storefront Utama Responsif', 1],
            ['assets/images/hero-slide-2-desktop.svg', 'assets/images/hero-slide-2-mobile.svg', 'lukifays.id/checkout', 'Alur Checkout & QRIS Instan', 2],
            ['assets/images/hero-slide-3-desktop.svg', 'assets/images/hero-slide-3-mobile.svg', 'admin.lukifays.id', 'Panel Kendali & Laporan Laba', 3]
        ];
        $stmt = $pdo->prepare("INSERT INTO hero_slides (desktop_image, mobile_image, url_label, caption, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($slides as $s) {
            $stmt->execute($s);
        }
    }

    // Seed Showcase Tabs if empty
    $stCount = $pdo->query("SELECT COUNT(*) FROM showcase_tabs")->fetchColumn();
    if ($stCount == 0) {
        $tabs = [
            ['Customable', 'lukifays-store.com', 'assets/images/showcase-customable.svg', 'Kustomisasi toko tanpa batas: atur banner, warna aksen, logo, dan susunan produk sesuai brand Anda.', 1],
            ['Checkout', 'lukifays-store.com/checkout', 'assets/images/showcase-checkout.svg', 'Alur checkout ringkas dan cepat: customer mengisi data, memilih payment, dan transaksi langsung terproses otomatis.', 2],
            ['Dashboard Admin', 'admin.lukifays-store.com', 'assets/images/admin-mockup.svg', 'Panel admin realtime: pendapatan harian, profit bersih, status transaksi, dan stok supplier dalam satu layar.', 3]
        ];
        $stmt = $pdo->prepare("INSERT INTO showcase_tabs (tab_label, address_url, image, caption, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($tabs as $t) {
            $stmt->execute($t);
        }
    }

    // Seed sample items for Poster Design & Character tabs in feature_highlights if missing
    $extraFeatCount = $pdo->query("SELECT COUNT(*) FROM feature_highlights WHERE category IN ('Poster Design', 'Character')")->fetchColumn();
    if ($extraFeatCount == 0) {
        $extraFeats = [
            ['Poster Design', 'palette', 'Desain Banner Promo Event', 'Banner promosi turnamen dan flash sale diamond dengan resolusi HD siap posting untuk web maupun Instagram.', 1],
            ['Poster Design', 'layer-group', 'Template Feed & Story IG', 'Desain template katalog harga dan testimoni ber-watermark toko agar terlihat profesional dan seragam.', 2],
            ['Poster Design', 'file-image', 'Format Source Editable & PNG', 'Disediakan file master siap pakai dengan komposisi warna sesuai identitas brand toko digital Anda.', 3],
            ['Poster Design', 'mobile-screen', 'Optimasi Rasio Desktop & Mobile', 'Setiap banner disesuaikan ukurannya agar tajam di layar smartphone maupun monitor widescreen.', 4],
            ['Character', 'user-astronaut', 'Maskot Karakter 2D/3D Eksklusif', 'Pembuatan maskot karakter unik bertema gaming sebagai identitas pembeda toko Anda dibanding kompetitor.', 1],
            ['Character', 'wand-magic-sparkles', 'Beragam Pose & Ekspresi', 'Karakter dilengkapi variasi pose untuk diletakkan di halaman utama, banner promo, hingga stiker WhatsApp.', 2],
            ['Character', 'pen-nib', 'Artwork Original Vektor Tajam', 'Digambar secara kustom oleh ilustrator profesional bukan aset stok pasaran sehingga aman digunakan komersial.', 3],
            ['Character', 'shield-halved', 'Hak Kepemilikan Penuh Klien', 'File resolusi tinggi transparan diserahkan sepenuhnya untuk kebutuhan merchandise maupun branding toko.', 4]
        ];
        $stmt = $pdo->prepare("INSERT INTO feature_highlights (category, icon, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($extraFeats as $ef) {
            $stmt->execute($ef);
        }
    }
}

// Seed clean, human-crafted data with 100% NO AI SLOP
function seed_default_data($pdo) {
    // 1. Settings (Colors, Branding, Info)
    $settings = [
        'site_title' => 'UMKM Digital: Solusi Pembuatan Website Top Up Game & Reseller Otomatis',
        'site_tagline' => 'Jasa Pembuatan Website Top Up Game dan PPOB Terintegrasi H2H Multi-Supplier',
        'site_logo' => 'assets/images/logo.svg',
        'site_favicon' => 'assets/images/logo.svg',
        // GROUNDED THEME PALETTE (No neon slop, strict contrast)
        'primary_color' => '#2563EB',      // Royal Blue
        'accent_color' => '#0284C7',       // Calm Tech Sky Blue
        'bg_dark' => '#090D16',            // Solid Deep Technical Base
        'bg_card' => '#121724',            // Solid Matte Surface
        'border_color' => '#222B3D',        // Crisp Border
        'text_primary' => '#F8FAFC',       // High Contrast Crisp White
        'text_secondary' => '#94A3B8',     // Readable Cool Slate
        // Contact & Socials
        'contact_whatsapp' => '6281234567890',
        'contact_email' => 'halo@umkmdigital.id',
        'contact_telegram' => 'umkmdigital_support',
        'instagram_url' => 'https://instagram.com/umkmdigital',
        'footer_text' => 'Penyedia solusi rekayasa web untuk toko online top up game, voucher digital, dan PPOB otomatis di Indonesia.',
        'copyright_text' => '2026 UMKM Digital. Hak cipta dilindungi undang-undang.',
        'nav_cta_text' => 'Hubungi Kami',
        'nav_cta_link' => 'https://wa.me/6281234567890'
    ];

    $replaceStmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $k => $v) {
        $replaceStmt->execute([$k, $v]);
    }

    // 2. Default Admin Account (admin / admin123)
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $adminStmt = $pdo->prepare("INSERT INTO admins (username, password, name) VALUES (?, ?, ?)");
    $adminStmt->execute(['admin', $hashedPassword, 'Super Administrator']);

    // 3. Hero Section (Direct, honest, no AI slogan)
    $heroStmt = $pdo->prepare("INSERT INTO hero_section (badge_text, title, subtitle, btn_primary_text, btn_primary_link, btn_secondary_text, btn_secondary_link, hero_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $heroStmt->execute([
        'LAYANAN PEMBUATAN WEBSITE RESELLER',
        'Jasa Pembuatan Website Top Up Game & Voucher Otomatis',
        'Bangun platform toko digital Anda sendiri dengan sistem transaksi instan 24 jam nonstop. Terhubung langsung ke multi-supplier Digiflazz, VIP Payment, dan Tokovoucher dengan verifikasi pembayaran otomatis via QRIS dan Virtual Account.',
        'Konsultasi Spesifikasi',
        'https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20konsultasi%20pembuatan%20website%20topup',
        'Lihat Portofolio Karya',
        '#portofolio',
        'assets/images/hero-mockup.svg'
    ]);

    // 4. Partner Logos (Real integrated suppliers)
    $partners = [
        ['Digiflazz', 'badge', '', '#2563EB', 1],
        ['VIP Payment', 'badge', '', '#0284C7', 2],
        ['Tokovoucher', 'badge', '', '#D97706', 3],
        ['Apigames', 'badge', '', '#16A34A', 4],
        ['UniPin', 'badge', '', '#DC2626', 5],
        ['Bangjeff', 'badge', '', '#4F46E5', 6],
    ];
    $partnerStmt = $pdo->prepare("INSERT INTO partners (name, logo_type, logo_image, badge_color, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($partners as $p) {
        $partnerStmt->execute($p);
    }

    // 5. Why Choose Us (Authentic technical reasons, NO magic/sparkle/robot icons)
    $why = [
        ['sliders', 'Kustomisasi Desain Penuh', 'Tampilan website, struktur katalog, skema warna, dan logo toko disesuaikan sepenuhnya dengan identitas bisnis Anda.', 1],
        ['server', 'Infrastruktur Server Mandiri', 'Website di-deploy pada cloud server cepat dengan akses database mandiri dan sertifikat SSL aktif.', 2],
        ['gamepad', 'Dukungan 40+ Judul Game', 'Katalog terhubung langsung ke game populer seperti Mobile Legends, Free Fire, Valorant, Genshin Impact, dan produk PPOB.', 3],
        ['clock', 'Transaksi Otomatis 24 Jam', 'Sistem webhook instan memproses transaksi dari pembayaran pelanggan ke supplier dalam hitungan detik tanpa perlu admin standby.', 4]
    ];
    $whyStmt = $pdo->prepare("INSERT INTO why_us (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($why as $w) {
        $whyStmt->execute($w);
    }

    // 6. Services (3 Main Tabs from Figma)
    $services = [
        ['01', 'Web Development', 'Pembuatan website toko online top up game dan voucher otomatis berkinerja tinggi, responsif, dan mudah dikelola dari panel admin.', 'code', 'assets/images/service-web.svg', 1],
        ['02', 'Creative Studio', 'Desain visual branding eksklusif untuk meningkatkan kepercayaan pelanggan dan daya tarik toko di media sosial.', 'palette', 'assets/images/service-creative.svg', 2],
        ['03', 'Commission Design', 'Pembuatan aset visual tematik khusus, maskot karakter toko, dan elemen UI kustom untuk identitas yang unik.', 'wand-magic-sparkles', 'assets/images/service-commission.svg', 3]
    ];
    $srvStmt = $pdo->prepare("INSERT INTO services (step_number, title, description, icon, icon_image, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($services as $s) {
        $srvStmt->execute($s);
    }

    // 6b. Service Cards (6 dynamic cards per tab)
    $serviceCards = [
        [1, 'bolt', 'Transaksi Otomatis 24 Jam Nonstop', 'Pesanan diproses dalam hitungan detik tanpa campur tangan admin, terhubung langsung ke API multi-supplier dengan notifikasi status realtime.', 1],
        [1, 'credit-card', 'Multi Payment Gateway Sekaligus', 'Dukungan integrasi QRIS Dinamis (GoPay, OVO, Dana, ShopeePay) serta Virtual Account bank besar dengan auto-rekonsiliasi mutasi.', 2],
        [1, 'tags', 'Khusus Harga Khusus (Tiering Reseller)', 'Pengaturan harga jual bertingkat untuk member biasa, reseller terverifikasi, hingga agen distributor secara otomatis.', 3],
        [1, 'bell', 'Notifikasi Bot & Reseller Otomatis', 'Pengiriman invoice dan struk otomatis via bot WhatsApp ke pembeli, serta peringatan saldo supplier kritis ke Telegram admin.', 4],
        [1, 'id-badge', 'Cek Nickname Otomatis All - Game', 'Validasi User ID dan Zone ID akun pemain sebelum pembayaran untuk Mobile Legends, Free Fire, PUBG, Genshin, dan 40+ game.', 5],
        [1, 'wallet', 'Web Dinamis Semua E-Wallet', 'Tampilan website responsif, ultra-ringan saat dibuka lewat smartphone, dan mudah dikustomisasi dari dashboard admin.', 6],
        [2, 'crown', 'Desain Logo Vektor & Favicon Toko', 'Identitas visual profesional format SVG tajam tanpa pecah untuk logo website, favicon browser, dan header aplikasi.', 1],
        [2, 'image', 'Banner Promo & Event Games', 'Desain visual promosi event turnamen, flash sale diamond, dan paket bundling game format desktop dan mobile HD.', 2],
        [2, 'hashtag', 'Template Feed & Story Instagram', 'Paket template promosi media sosial seragam dan konsisten untuk membangun reputasi dan kredibilitas toko Anda.', 3],
        [2, 'receipt', 'Watermark & Struk Mockup Transaksi', 'Desain invoice berstempel resmi toko untuk dibagikan sebagai bukti transaksi dan testimoni terpercaya ke pelanggan.', 4],
        [2, 'video', 'Aset Animasi Transaksi Story', 'Template video vertikal singkat untuk update stok masuk dan bukti transaksi harian di status WhatsApp dan TikTok.', 5],
        [2, 'swatchbook', 'Panduan Brand & Skema Warna Toko', 'Dokumentasi kode warna identitas toko dan tipografi agar seluruh materi promosi terlihat solid dan rapi.', 6],
        [3, 'ghost', 'Karakter Maskot 2D/3D Eksklusif', 'Pembuatan maskot karakter unik bertema gaming atau futuristik sebagai duta visual brand toko online Anda.', 1],
        [3, 'panorama', 'Ilustrasi Vektor Hero Banner', 'Header website kustom dengan gambar vektor ilustrasi karakter game dan tipografi eksklusif non-template.', 2],
        [3, 'comments', 'Desain Stiker & Persona WhatsApp', 'Koleksi stiker WhatsApp ekspresif bertema maskot toko untuk interaksi customer care yang ramah dan akrab.', 3],
        [3, 'compass-drafting', 'UI/UX Custom Layout Sesuai Request', 'Penyesuaian tata letak kartu produk, form checkout, dan alur pembayaran sesuai alur bisnis spesifik Anda.', 4],
        [3, 'spinner', 'Animasi Loading Toko Kustom', 'Efek transisi halaman dan loading spinner bertema logo toko saat pengunjung berpindah halaman produk.', 5],
        [3, 'icons', 'Paket Ikon Game Vektor Kustom', 'Set ikon produk game bergaya seragam agar katalog toko terlihat rapi, bersih, dan memikat pembeli.', 6]
    ];
    $scStmt = $pdo->prepare("INSERT INTO service_cards (service_id, icon, title, description, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($serviceCards as $sc) {
        $scStmt->execute($sc);
    }

    // 7. Portfolio Items (Specific, realistic projects)
    $portfolio = [
        ['Lukifays Store', 'Website', 'Website top up game dengan sistem pesanan instan dan integrasi multi-supplier Digiflazz dan VIP Payment.', 'Top Up Game, Reseller Otomatis, Payment Gateway', 'assets/images/portfolio-1.svg', 'https://lukifays.store', 1],
        ['Vouchershop ID', 'Website', 'Platform penjualan voucher digital, token listrik PLN, dan paket data dengan sistem deposit saldo member.', 'PPOB, Voucher Streaming, Saldo Member', 'assets/images/portfolio-2.svg', 'https://vouchershop.id', 2],
        ['Nexus Topup Esports', 'Website', 'Portal top up game turnamen dengan validasi User ID otomatis dan promo berkala.', 'Esports, Validasi ID, Multi-Supplier', 'assets/images/portfolio-3.svg', 'https://nexustopup.com', 3],
        ['Banner Promosi Mobile Legends', 'Poster Design', 'Desain materi promosi diamond Mobile Legends untuk posting media sosial toko.', 'Desain Grafis, Branding Toko, Promosi', 'assets/images/portfolio-1.svg', '#', 4],
        ['Aset Maskot Toko Digital', 'Character', 'Desain karakter maskot 2D sebagai elemen penguat visual branding toko digital.', 'Maskot Toko, Ilustrasi Vektor, Branding', 'assets/images/portfolio-3.svg', '#', 5],
    ];
    $portStmt = $pdo->prepare("INSERT INTO portfolio (title, category, description, tags, image, demo_url, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($portfolio as $p) {
        $portStmt->execute($p);
    }

    // 8. Showcase Section (Technical control, NO fake numbers)
    $showcaseStmt = $pdo->prepare("INSERT INTO showcase (title, subtitle, image, stat1_num, stat1_label, stat2_num, stat2_label, stat3_num, stat3_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $showcaseStmt->execute([
        'Panel Kendali Lengkap untuk Mengelola Operasional Toko',
        'Pantau pesanan masuk secara realtime, atur margin keuntungan per level member, sinkronkan katalog supplier, dan kelola mutasi pembayaran langsung dari satu antarmuka yang bersih.',
        'assets/images/admin-mockup.svg',
        '24 Jam', 'Pemrosesan Pesanan Otomatis',
        'Multi-Supplier', 'Failover Otomatis Tanpa Gangguan',
        'Semua Saluran', 'QRIS Dinamis dan Virtual Account'
    ]);

    // 9. Feature Highlights (Real technical features, NO em dashes)
    $features = [
        ['id-card', 'Validasi Nickname Otomatis', 'Pengecekan User ID dan Zone ID secara instan untuk 40+ game guna memastikan diamond terkirim ke akun yang tepat.', 1],
        ['credit-card', 'Semua Saluran Pembayaran', 'Menerima QRIS dari semua e-wallet dan perbankan, Virtual Account bank besar, serta gerai retail.', 2],
        ['network-wired', 'Multi-Supplier Failover', 'Tersambung ke beberapa supplier sekaligus: sistem otomatis mengalihkan pesanan jika salah satu supplier mengalami gangguan stok.', 3],
        ['users-gear', 'Tiering Harga Khusus Reseller', 'Atur harga jual berbeda untuk pengguna umum, member terdaftar, reseller VIP, hingga level agen distributor.', 4],
        ['tag', 'Promo Diskon & Kupon', 'Atur potongan harga berbatas waktu atau kupon promo untuk meningkatkan volume transaksi toko.', 5],
        ['chart-line', 'Rekap Laba dan Mutasi', 'Laporan transaksi harian, kalkulasi margin bersih, dan rekapan mutasi saldo dapat dipantau dan diekspor ke Excel.', 6],
        ['shield-halved', 'Keamanan Transaksi & Sesi', 'Enkripsi SSL aktif, proteksi brute force login, dan pencatatan audit log untuk setiap perubahan konfigurasi.', 7],
        ['code', 'Koneksi REST API Reseller', 'Dokumentasi RESTful API terstandarisasi untuk menghubungkan website toko Anda ke platform reseller pihak ketiga.', 8],
    ];
    $featStmt = $pdo->prepare("INSERT INTO feature_highlights (icon, title, description, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($features as $f) {
        $featStmt->execute($f);
    }

    // 10. Pricing Plans (Matching Figma 4 Tiers)
    $plans = [
        [
            'website',
            'SILVER',
            'Cocok untuk pemula yang baru merintis usaha toko online top up',
            'Rp 2.000.000',
            'Rp 1.500.000',
            '/selamanya',
            'HEMAT 25%',
            '#64748B',
            0,
            "Domain .com / .my.id (1 tahun)\nCloud Hosting Cepat & SSL Aktif\n1 Koneksi API Supplier Otomatis\nIntegrasi QRIS Pembayaran Instan\nKatalog 20+ Game Populer\nGaransi Sistem 1 Bulan",
            'Pilih Paket',
            'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20SILVER',
            1
        ],
        [
            'website',
            'GOLD',
            'Cocok untuk kamu yang ingin toko profesional dengan multi-supplier',
            'Rp 7.000.000',
            'Rp 5.000.000',
            '/selamanya',
            'TERBAIK PEMULA',
            '#2563EB',
            0,
            "Domain .com / .id (1 tahun)\nHigh Speed Cloud Hosting SSD/NVMe\nMulti-Supplier H2H hingga 3 Supplier\nSemua Channel Pembayaran (QRIS, VA, E-Wallet)\nCek Nickname Otomatis 40+ Game\nSistem Tiering Harga Reseller\nGaransi & Support 3 Bulan",
            'Pilih Paket',
            'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20GOLD',
            2
        ],
        [
            'website',
            'PLATINUM',
            'Cocok untuk kamu yang ingin toko kompetitif, fitur premium & butuh branding sendiri',
            'Rp 10.000.000',
            'Rp 8.500.000',
            '/selamanya',
            'PALING POPULER',
            '#2563EB',
            1, // Highlighted
            "Semua Fitur Paket Gold\nDedicated Cloud Server (Anti-RTO)\nMulti-Supplier Unlimited + Failover Otomatis\nBot Notifikasi WhatsApp Otomatis ke Pembeli & Admin\nDesain Custom Fleksibel + Branding Identitas\nPaket Aset Banner Promosi & Feed IG (10 Desain)\nSupport Prioritas 6 Bulan",
            'Pilih Paket',
            'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20PLATINUM',
            3
        ],
        [
            'website',
            'VIP EXPERT',
            'Paket paling lengkap dan mendalam hingga ke skala UMKM Digital',
            '',
            'Rp 12.000.000',
            '/selamanya',
            'PALING CANGGIH',
            '#0284C7',
            0,
            "Semua Fitur Paket Platinum\nDedicated Private Server (VPS Enterprise)\nAplikasi Android APK Siap Upload Google Play Store\nSistem Manajemen Multi-Admin & Hak Akses\nKonsultasi Bisnis & Strategi Marketing 1-on-1\nIntegrasi WhatsApp Bot Multi-Device\nSupport VIP 1 Tahun",
            'Pilih Paket',
            'https://wa.me/6281234567890?text=Halo,%20saya%20tertarik%20dengan%20Paket%20VIP%20EXPERT',
            4
        ]
    ];
    $planStmt = $pdo->prepare("INSERT INTO pricing_plans (category, name, tier_subtitle, old_price, price, period, badge_text, badge_color, is_popular, features_list, btn_text, btn_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($plans as $pl) {
        $planStmt->execute($pl);
    }

    // 11. Workflow Steps (Structured, concrete phases)
    $steps = [
        ['01', 'Konsultasi Kebutuhan', 'Pembahasan spesifikasi toko, pemilihan domain, katalog game yang diinginkan, dan paket yang sesuai target bisnis.', 1],
        ['02', 'Konfigurasi Server & API', 'Pemasangan server hosting, pengaturan database, serta pengujian koneksi API multi-supplier dan payment gateway.', 2],
        ['03', 'Penerapan Desain & Katalog', 'Penyesuaian logo, penataan banner promosi, konfigurasi margin harga produk, dan pengaturan level reseller.', 3],
        ['04', 'Pengujian Alur & Peluncuran', 'Simulasi alur transaksi menyeluruh dari checkout pelanggan hingga pengiriman produk. Website siap online untuk menerima pesanan.', 4]
    ];
    $stepStmt = $pdo->prepare("INSERT INTO workflow_steps (step_number, title, description, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($steps as $st) {
        $stepStmt->execute($st);
    }

    // 12. FAQ Items (Real questions, NO em dashes, practical answers)
    $faqs = [
        [
            'Berapa lama estimasi pengerjaan website sampai siap online?',
            'Untuk paket standar pengerjaan memerlukan waktu 2 sampai 4 hari kerja. Untuk paket dengan kustomisasi desain khusus atau integrasi lanjutan membutuhkan waktu 5 sampai 7 hari kerja termasuk tahap pengujian koneksi payment dan supplier.',
            1
        ],
        [
            'Apakah saya bisa memakai nama domain yang sudah saya miliki?',
            'Bisa. Anda dapat mengarahkan domain yang sudah Anda beli ke server kami, atau menggunakan fasilitas domain baru yang sudah termasuk dalam paket layanan kami.',
            2
        ],
        [
            'Bagaimana cara kerja transaksi otomatis tanpa konfirmasi manual?',
            'Ketika pembeli menyelesaikan pembayaran via QRIS atau Virtual Account, penyedia payment gateway langsung mengirimkan data webhook ke sistem website. Sistem kemudian secara otomatis meneruskan pesanan ke API supplier dalam hitungan detik.',
            3
        ],
        [
            'Apa fungsi fitur multi-supplier failover?',
            'Fitur ini memastikan toko Anda tetap melayani pesanan meskipun salah satu supplier sedang mengalami kehabisan saldo atau pemeliharaan server. Sistem akan secara otomatis mengalihkan pesanan ke supplier alternatif yang aktif.',
            4
        ],
        [
            'Apakah ada biaya langganan bulanan dari tim pembuat website?',
            'Tidak ada biaya bulanan wajib dari kami. Sistem menggunakan skema lisensi mandiri. Anda hanya perlu memperpanjang biaya sewa hosting dan domain tahunan mulai tahun kedua.',
            5
        ],
        [
            'Apakah disediakan panduan untuk pengoperasian panel admin?',
            'Ya, kami menyediakan panduan langkah demi langkah serta sesi pendampingan awal untuk memastikan Anda memahami cara mengelola harga produk, mengecek transaksi, dan memantau saldo supplier.',
            6
        ]
    ];
    $faqStmt = $pdo->prepare("INSERT INTO faqs (question, answer, sort_order) VALUES (?, ?, ?)");
    foreach ($faqs as $fq) {
        $faqStmt->execute($fq);
    }

    // 13. Expanded Features Directory (NO em dashes)
    $expanded = [
        ['transaksi', 'Transaksi & Otomasi', 'Pemrosesan Pesanan 24 Jam Nonstop', 'Pesanan diteruskan ke server supplier secara instan tanpa memerlukan konfirmasi manual admin.', 'clock', 1],
        ['transaksi', 'Transaksi & Otomasi', 'Validasi User ID Otomatis', 'Menampilkan nama akun in-game pembeli untuk 40+ judul game sebelum pembayaran dilakukan.', 'id-card', 2],
        ['transaksi', 'Transaksi & Otomasi', 'Sinkronisasi Harga Modal Otomatis', 'Perubahan harga modal dari supplier tersinkronisasi otomatis sehingga margin keuntungan toko tetap terjaga.', 'arrows-rotate', 3],
        ['transaksi', 'Transaksi & Otomasi', 'Failover Multi-Supplier Cerdas', 'Pengalihan otomatis ke supplier alternatif jika supplier utama kehabisan kuota atau mengalami kendala jaringan.', 'server', 4],
        ['transaksi', 'Transaksi & Otomasi', 'Halaman Invoice Realtime', 'Halaman status pembayaran dinamis dengan kode QRIS dan pembaruan status pesanan tanpa perlu muat ulang halaman.', 'receipt', 5],
        ['transaksi', 'Transaksi & Otomasi', 'Pelacakan Pesanan Mandiri', 'Pelanggan dapat mengecek riwayat dan status transaksi kapan saja melalui nomor invoice atau nomor WhatsApp.', 'magnifying-glass', 6],

        ['pembayaran', 'Pembayaran', 'Dukungan Berbagai Payment Gateway', 'Tersedia integrasi untuk Duitku, LinkQu, Tripay, Flip, dan Midtrans dalam satu platform.', 'wallet', 1],
        ['pembayaran', 'Pembayaran', 'QRIS Dinamis Nasional', 'Menerima pembayaran QRIS dari semua aplikasi e-wallet dan perbankan di Indonesia.', 'qrcode', 2],
        ['pembayaran', 'Pembayaran', 'Virtual Account Semua Bank Utama', 'Verifikasi otomatis pembayaran Virtual Account bank BCA, Mandiri, BNI, dan BRI.', 'building-columns', 3],
        ['pembayaran', 'Pembayaran', 'Gerai Retail Alfamart & Indomaret', 'Pilihan pembayaran tunai melalui kasir gerai retail terdekat bagi pelanggan yang belum memiliki rekening.', 'store', 4],
        ['pembayaran', 'Pembayaran', 'Sistem Saldo Deposit Member', 'Pelanggan terdaftar dapat mengisi saldo akun untuk proses checkout kilat hanya dengan satu klik.', 'coins', 5],

        ['reseller', 'Member & Reseller', 'Pengaturan Level Harga Bertingkat', 'Bebas menentukan margin keuntungan berbeda untuk Pembeli Tamu, Member, Reseller, dan Agen.', 'users', 1],
        ['reseller', 'Member & Reseller', 'REST API Khusus Reseller', 'Menyediakan endpoint RESTful API terstandarisasi agar website Anda dapat menjadi penyedia stok bagi reseller lain.', 'code', 2],
        ['reseller', 'Member & Reseller', 'Dashboard Khusus Pengguna', 'Area member yang rapi untuk memantau riwayat pembelian, mutasi deposit, dan layanan tiket bantuan.', 'table-columns', 3],

        ['laporan', 'Laporan & Keuangan', 'Kalkulasi Margin Bersih Harian', 'Menghitung selisih harga jual dan harga modal supplier secara akurat untuk setiap transaksi sukses.', 'calculator', 1],
        ['laporan', 'Laporan & Keuangan', 'Statistik Produk Terlaris', 'Visualisasi data transaksi untuk memantau nominal dan jenis game yang paling diminati pembeli.', 'chart-column', 2],
        ['laporan', 'Laporan & Keuangan', 'Ekspor Laporan ke Format Excel', 'Unduh rekapan transaksi dan data keuangan toko untuk mempermudah pembukuan berkala.', 'file-excel', 3]
    ];
    $expStmt = $pdo->prepare("INSERT INTO expanded_features (category_code, category_name, title, description, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($expanded as $ex) {
        $expStmt->execute($ex);
    }
}

// Run initial schema & data check
init_database_tables($pdo, $db_type_used);

// Global Helper Functions
function get_settings($pdo) {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function update_setting($pdo, $key, $value) {
    $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    return $stmt->execute([$key, $value]);
}
