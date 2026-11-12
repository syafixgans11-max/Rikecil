# DESIGN.md - Design Direction for UMKM Digital / Update Reseller

## 1. Identity & Soul
- **Product**: Platform Jasa Pembuatan Website Top Up Game, Voucher Digital, dan PPOB Otomatis untuk Reseller & UMKM di Indonesia.
- **Brand Personality**: Handal, presisi, transparan, dan berorientasi bisnis teknis nyata (Software House / Engineering Agency).
- **Tone**: Lugas, informatif, percaya diri, tanpa jargon AI atau janji berlebihan.
- **Audience**: Pemilik toko game, reseller voucher, komunitas gamer, dan pengusaha UMKM digital di Indonesia.

## 2. Design Dials
- **ENERGY**: 2 (Tenang, solid, fokus pada fungsionalitas dan kejelasan data).
- **RHYTHM**: 3 (Bervariasi: hero asimetris, perbandingan fitur berbobot, pricing terstruktur, tidak seragam/monoton).
- **MOTION**: 1 (Minimal & fungsional: transisi hover 0.2s untuk feedback interaktif, tanpa animasi mengambang/floating tanpa tujuan).

## 3. Color System (Strict Restraint - No Slop)
- **Background Base**: `#090D16` (Deep technical navy/slate, solid, tanpa radial orb / glow nebulas).
- **Surface / Card**: `#121724` (Permukaan solid matte dengan kontras jelas).
- **Surface Elevated**: `#192032` (Elevasi untuk kartu yang disorot).
- **Border / Stroke**: `#222B3D` (Garis pembatas 1px yang presisi dan konsisten).
- **Primary Accent**: `#2563EB` (Royal Blue untuk tombol aksi utama dan indikator aktif).
- **Secondary Accent**: `#0284C7` (Sky Blue yang tenang untuk aksen data kecil).
- **Text Primary**: `#F8FAFC` (Putih terang, kontras rasio > 14:1 terhadap background).
- **Text Secondary**: `#94A3B8` (Slate terang, kontras rasio > 5.5:1 terhadap background, lulus WCAG AA).
- **Text Muted**: `#64748B` (Untuk metadata kecil).

## 4. Typography & Hierarchy
- **Font**: `Plus Jakarta Sans` untuk seluruh antarmuka (huruf sans-serif modern buatan desainer Indonesia yang sangat cocok untuk produk teknologi lokal).
- **Headings**: Bobot 800-900, letter-spacing rapat (-0.02em), langsung menyampaikan solusi bisnis.
- **No Em Dashes**: Tidak ada tanda em dash (`-`) di seluruh copy teks.
- **No Eyebrow Capsule**: Tidak ada pill "UPDATE RESELLER 2026" dengan titik berkedip di atas H1.

## 5. Layout & Components
- **Border Radius**: Konsisten `6px` untuk kontrol kecil/tombol, `10px` untuk kartu, `14px` untuk container besar. Hindari bentuk pil (`9999px`) pada seluruh elemen.
- **No Fake Stats**: Hapus statistik tanpa sumber ("99.9% uptime", "5 detik", dsb). Ganti dengan penjelasan kapabilitas teknis nyata.
- **Real Feature Icons**: Gunakan ikon yang relevan secara teknis (server, code, database, shield, credit-card), bukan `wand-magic-sparkles` atau `robot`.
