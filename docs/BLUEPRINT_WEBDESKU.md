# Blueprint Webdesku (Acuan Implementasi)

Dokumen ini merangkum isi `Blueprint_Webdesku_Reusable_Desa.pdf` dan menjadi acuan default pengembangan website ini ke depan.

## 1. Konsep Umum
- Webdesku adalah website desa modern, reusable, dan mendukung multi-desa.
- Tujuan utama: transparansi, pelayanan publik, digitalisasi administrasi desa.
- Referensi UI: website desa modern seperti `loaduriilir.digitaldesa.id`.

## 2. Struktur Menu Utama (Frontend Publik)
- Home
- Profil Desa (Sejarah, Visi Misi, Struktur Organisasi, Demografi)
- Berita
- Agenda
- Layanan Desa
- Transparansi (APBDes, Laporan)
- Galeri
- Pengumuman
- Kontak

## 3. Struktur Halaman Home
- Hero section (banner + slogan desa)
- Sambutan kepala desa
- Statistik desa (penduduk, KK, RT/RW, luas wilayah)
- Berita terbaru
- Layanan cepat
- Agenda desa
- Galeri kegiatan

## 4. Struktur Database (Laravel)
Tabel utama:
- `villages`
- `users`
- `news`
- `agendas`
- `announcements`
- `services`
- `galleries`

Aturan penting:
- Setiap tabel konten harus memiliki `village_id` untuk mendukung multi-desa.

## 5. Struktur Folder Laravel
- `app/Models`
- `app/Http/Controllers`
- Controller admin terpisah (`Admin/*Controller`)
- `resources/views/layouts`
- `resources/views/home.blade.php`
- `resources/views/news/*`
- `resources/views/agenda/*`
- `resources/views/services/*`
- `resources/views/admin/*`

## 6. Design System
- Primary: `#0B3D91`
- Secondary: `#1E88E5`
- Accent: `#FFC107`
- Font: Poppins / Inter
- Icon: Font Awesome

## 7. Fitur Multi-Desa (SaaS Ready)
- Subdomain per desa (`desa-a.webdesku.id`)
- Role management (Admin, Operator, Kepala Desa)
- Dashboard statistik
- Generate surat otomatis
- QR code verifikasi
- SEO optimized
- Auto sitemap

## 8. Arah Pengembangan
Saat membuat fitur baru, gunakan urutan prioritas berikut:
1. Selaraskan dengan menu dan struktur halaman blueprint.
2. Pastikan data model mendukung multi-desa (`village_id`).
3. Pisahkan area publik vs admin (backend aparat desa).
4. Pertahankan konsistensi design system biru pemerintah.
5. Aktifkan kebutuhan SEO dan sitemap ketika fitur konten bertambah.
