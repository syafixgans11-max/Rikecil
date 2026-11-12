# Landing Page UMKM Digital (Update Reseller 2026)
### Berdasarkan Desain Figma: SaaS / Digital Store / Agency Top-Up Game & Voucher

Landing Page modern berbasis **PHP** dan **SQL** dengan tema dark cyber aesthetic yang terintegrasi langsung dengan **Admin Panel CMS**. Seluruh elemen pada landing page dapat diatur secara dinamis melalui Admin Panel:
1. **Palet Warna Tema** (Warna Utama/Primary, Aksen Neon Cyan, Background Canvas, Card Surface, Garis Tepi/Border, Teks).
2. **Semua Gambar & Mockup** (Logo Brand, Favicon Browser, Mockup Hero Laptop/HP, Mockup Showcase Admin, Screenshot Portofolio).
3. **Semua Konten Teks** (Headline, Sub-headline, Badge, Tombol CTA, Layanan, Portofolio, Fitur, Harga Paket, Alur Kerja, FAQ).

---

## 🚀 Fitur Utama

- **100% Dynamic Content (PHP & SQL)**: Semua teks, gambar, dan warna disimpan di database.
- **Dual Database Support (Plug & Play)**:
  - Otomatis mendukung **MySQL / MariaDB** (melalui Laragon / XAMPP).
  - Dilengkapi **SQLite Fallback Otomatis** sehingga dapat langsung dijalankan tanpa perlu setup database terlebih dahulu (`php -S localhost:8000`).
  - File SQL standar disertakan: `database.sql`.
- **Admin Panel Komprehensif**:
  - **Login Aman**: Enkripsi password `password_hash()` BCRYPT & proteksi sesi.
  - **Warna & Identitas**: Live Color Picker untuk mengubah CSS Variables tema dan upload Logo/Favicon.
  - **Hero & Banner**: Atur judul headline, tombol WhatsApp, dan upload mockup gadget toko.
  - **Supplier & Partner**: Tambah/edit badge partner (Apigames, Digiflazz, VIP Payment, Tokovoucher, UniPin, dll).
  - **Kenapa Harus Di Kami**: Edit 4 kartu keunggulan utama.
  - **Layanan Kami**: Edit 3 kolom pilar layanan & checklist fitur.
  - **Portofolio**: Upload screenshot karya website, kategori filter (Website, Poster, Character), dan link demo.
  - **Showcase Mockup**: Edit banner tengah dan 3 statistik pencapaian (Uptime, Kecepatan, Pilihan Game).
  - **Apa yang Anda Dapatkan**: Edit 8 kartu keunggulan fitur teknis.
  - **Paket Harga**: Atur harga Silver, Gold, Platinum (Paling Popular), VIP (Paling Canggih), diskon coret, dan rincian fitur.
  - **Alur Kerja**: Edit 4 tahapan pengerjaan (Konsultasi -> Setup -> Kustomisasi -> Rilis).
  - **Pertanyaan (FAQ)**: Kelola accordion tanya jawab.
  - **Direktori Fitur Lengkap**: Katalog fitur mendalam berdasarkan kategori (Transaksi, Pembayaran, Reseller, Laporan).

---

## 🔑 Akses Administrator

- **URL Admin**: `http://localhost:8008/admin/login.php` (atau `http://localhost/landing-page-umkm/admin/login.php`)
- **Username**: `admin`
- **Password**: `admin123`

---

## 🛠️ Cara Menjalankan

### Cara 1: Menggunakan PHP Built-in Server (Tercepat)
Buka terminal / PowerShell di direktori proyek ini, lalu jalankan:
```bash
php -S localhost:8000
```
Buka browser:
- Halaman Pengunjung: `http://localhost:8000/`
- Panel Admin: `http://localhost:8000/admin/login.php`

*(Sistem otomatis mendeteksi database dan mengisi data bawaan sesuai Figma tanpa perlu konfigurasi tambahan).*

### Cara 2: Menggunakan Laragon / XAMPP (Apache + MySQL)
1. Salin folder `landing-page-umkm` ke dalam folder web server:
   - Laragon: `C:\laragon\www\landing-page-umkm`
   - XAMPP: `C:\xampp\htdocs\landing-page-umkm`
2. Jalankan Apache dan MySQL pada Laragon / XAMPP.
3. Buat database baru bernama `landing_page_umkm` di phpMyAdmin (atau biarkan script `config/database.php` membuatnya otomatis).
4. (Opsional) Import file `database.sql` ke database `landing_page_umkm`.
5. Buka browser:
   - `http://localhost/landing-page-umkm/`
   - `http://localhost/landing-page-umkm/admin/login.php`

---

## 📁 Struktur Direktori

```
landing-page-umkm/
├── admin/                     # Panel Administrator CMS
│   ├── includes/              # Template Header, Sidebar, Footer Admin
│   ├── index.php              # Dashboard Utama & Ringkasan Statistik
│   ├── settings.php           # Pengatur Warna Tema, Logo, Kontak
│   ├── hero.php               # Pengatur Hero Banner & Mockup
│   ├── partners.php           # Pengatur Supplier & Partner
│   ├── why_us.php             # Pengatur Kenapa Harus Di Kami
│   ├── services.php           # Pengatur Layanan Kami
│   ├── portfolio.php          # Pengatur Portofolio & Upload Gambar
│   ├── showcase.php           # Pengatur Showcase & Statistik
│   ├── features.php           # Pengatur Apa yang Didapatkan (8 Fitur)
│   ├── pricing.php            # Pengatur Paket Harga & Diskon
│   ├── workflow.php           # Pengatur Alur Kerja (Cara Kerja)
│   ├── faq.php                # Pengatur FAQ Accordion
│   ├── feature_directory.php  # Pengatur Direktori Fitur Lengkap
│   ├── login.php              # Halaman Login Admin
│   └── logout.php             # Halaman Logout
├── assets/
│   ├── css/
│   │   ├── style.css          # Styling Utama (Dark Theme & Responsive)
│   │   └── admin.css          # Styling Panel Admin
│   ├── js/
│   │   ├── main.js            # Interaktivitas Frontend (Filter & Accordion)
│   │   └── admin.js           # Live Color Preview & Image Preview Admin
│   └── images/                # Mockup & Asset Grafis Bawaan
├── config/
│   ├── database.php           # Koneksi PDO (MySQL & SQLite Auto-Init)
│   └── auth.php               # Sistem Sesi, Upload Gambar & CSRF
├── data/
│   └── landing_page.db        # SQLite Database (Fallback Otomatis)
├── uploads/                   # Folder Penyimpanan Gambar Upload Admin
├── database.sql               # Skrip SQL Lengkap untuk MySQL/MariaDB
├── index.php                  # Halaman Depan Landing Page Pengunjung
└── README.md                  # Dokumentasi Proyek
```
