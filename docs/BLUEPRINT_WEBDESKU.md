# Blueprint Webdesku (Acuan Implementasi Saat Ini)

Dokumen ini adalah acuan produk dan arsitektur Webdesku saat ini. Jika ada blueprint lama yang menyebut multi-desa, SaaS, tenant runtime, atau subdomain tenancy, bagian tersebut dianggap historis dan tidak menjadi arah implementasi kecuali diminta eksplisit.

## 1. Konsep Umum

- Webdesku adalah sistem informasi desa dan website desa modern berbasis Laravel.
- Model operasional saat ini adalah **single village application**.
- Satu deployment Webdesku digunakan untuk satu desa.
- Kode harus tetap reusable agar desa lain dapat memakai codebase yang sama lewat deployment terpisah.
- Tujuan utama: transparansi, pelayanan publik, digitalisasi administrasi desa, dan publikasi informasi desa.
- Nama **Webdesku** adalah identitas internal repository/codebase, bukan brand publik website desa.

## 2. Prinsip Reusability

Reusable berarti:

```text
Satu codebase Webdesku
        ↓
Deployment Desa A
Deployment Desa B
Deployment Desa C
```

Setiap deployment berdiri sendiri dengan database, konfigurasi, storage, dan environment masing-masing.

Reusable **bukan** berarti:

- satu aplikasi melayani banyak desa sekaligus,
- tenant switching,
- subdomain tenancy,
- tenant-aware routing,
- runtime village resolver,
- tenant-specific authentication,
- session-based current village switching.

## 3. Identitas Desa

Informasi spesifik desa harus dapat dikonfigurasi, bukan disebar sebagai hardcoded value di Blade/PHP.

Contoh data yang harus berasal dari konfigurasi atau data persisted:

- nama desa,
- logo,
- alamat,
- email dan nomor telepon,
- kepala desa,
- visi dan misi,
- sambutan kepala desa,
- media sosial,
- koordinat dan peta,
- SEO metadata,
- branding dan identitas website.

Model `Village` boleh tetap digunakan sebagai penyimpan konfigurasi identitas deployment, tetapi tidak boleh diperlakukan sebagai tenant boundary.

### Aturan Identitas Publik

- Website publik, layout auth, footer, metadata dokumen, dan title browser tidak boleh memakai `Webdesku` sebagai brand publik.
- Runtime identity harus berasal dari data/config desa pada deployment, terutama model `Village`.
- Homepage memakai title nama desa saja.
- Subhalaman memakai pola title `[Nama Halaman] | [Nama Desa]`.
- Jika data desa belum tersedia, fallback publik yang aman adalah `Pemerintah Desa`.
- Jangan hardcode nama desa tertentu di Blade/PHP; gunakan sumber data desa terpusat.

## 4. Struktur Menu Utama (Frontend Publik)

Core public modules:

- Home
- Profil Desa
  - Gambaran Umum Desa
  - Sejarah Desa
  - Visi Misi
  - Susunan Organisasi
- Berita
- Agenda
- Layanan Desa
- Transparansi
- Galeri
- Pengumuman
- Kontak

Modul tambahan yang sudah ada atau dapat dikembangkan mengikuti kebutuhan aplikasi:

- Pengaduan Masyarakat
- Peraturan Desa
- Statistik
- Infografis

Statistik publik mendukung filter rentang tahun dan export PDF laporan statistik. Statistik periodik harus mengikuti `start_year` dan `end_year`, sedangkan data snapshot/master seperti identitas desa, layanan aktif, aset desa, dan data penduduk snapshot harus diberi label sesuai sumbernya.

## 5. Struktur Halaman Home

Homepage menjadi pintu utama informasi desa dan dapat memuat:

- hero section,
- sambutan kepala desa,
- statistik desa,
- berita terbaru,
- layanan cepat,
- agenda desa,
- galeri kegiatan,
- pengumuman penting,
- tautan transparansi atau infografis.

Data pada homepage harus berasal dari database/configuration yang relevan. Jangan menampilkan angka sintetis atau hardcoded yang dapat menyesatkan.

## 6. Struktur Database

Tabel utama yang saat ini relevan antara lain:

- `villages`
- `users`
- `news`
- `agendas`
- `announcements`
- `services`
- `service_requests`
- `complaints`
- `galleries`
- `sliders`
- `village_assets`
- `village_populations`
- `village_population_stats`
- `village_apbdes_items`
- `village_apbdes_documents`
- `village_transparency_items`
- `village_transparency_documents`
- `village_land_use_areas`
- `module_settings`

Catatan:

- Legacy field seperti `village_id` dapat tetap ada karena dependensi implementasi lama.
- Jangan menambahkan field tenant baru untuk kebutuhan multi-tenancy.
- Jika ingin menyederhanakan legacy `village_id`, inspeksi dulu model, migration, controller, view, seeder, dan data existing.
- Perubahan schema harus memakai migration dan mempertimbangkan integritas data.

## 7. Struktur Folder Laravel

Ikuti struktur Laravel monolith yang sudah ada:

- `app/Models`
- `app/Http/Controllers`
- `app/Http/Controllers/Admin`
- `app/Http/Middleware`
- `app/Http/Requests`
- `app/Services`
- `app/Support`
- `resources/views/layouts`
- `resources/views/web`
- `resources/views/admin`
- `routes`
- `database/migrations`
- `database/seeders`
- `tests`

Gunakan service layer hanya jika ada business logic bermakna. Jangan membuat repository/service/helper hanya demi pola arsitektur.

## 8. Design System

Patokan visual:

- Primary: `#0B3D91`
- Secondary: `#1E88E5`
- Accent: `#FFC107`
- Font: Poppins / Inter
- Icon: Font Awesome jika sudah digunakan oleh implementasi

Untuk halaman frontend publik selain home, ikuti `docs/frontend-standardization.md`.

## 9. Role dan Admin Area

Admin area ditujukan untuk operator/aparat desa pada satu deployment desa.

Prinsip:

- authorization tetap server-side,
- role check tetap enforced oleh middleware/policy/gate sesuai kebutuhan,
- UI admin harus sederhana dan jelas,
- destructive action perlu konfirmasi,
- validasi wajib jelas dan aman,
- jangan menganggap hidden button sebagai boundary keamanan.

## 10. Arah Pengembangan

Saat membuat fitur baru:

1. Selaraskan dengan kebutuhan single-village deployment.
2. Pastikan data spesifik desa configurable, bukan hardcoded.
3. Jangan memperkenalkan multi-tenancy kecuali diminta eksplisit.
4. Pisahkan public website dan admin panel.
5. Ikuti Laravel 12 conventions.
6. Pertahankan konsistensi design system biru pemerintah.
7. Perbarui dokumentasi bila perubahan menyentuh arsitektur, route, database, role, module, konfigurasi, atau business rule.

Untuk laporan PDF statistik:

- gunakan data dari `App\Services\StatisticsService`,
- jangan mengarang angka, nama pejabat, tanda tangan, atau regulasi,
- tampilkan periode laporan,
- posisikan PDF sebagai rekapitulasi sistem yang memerlukan verifikasi/pengesahan Pemerintah Desa bila digunakan sebagai dokumen administrasi resmi.

## 11. Legacy Multi-Tenant Code

Jika menemukan legacy code seperti:

- `IdentifyVillage`,
- `currentVillage`,
- `village_id`,
- query berdasarkan slug subdomain,
- istilah tenant,

jangan langsung dihapus.

Langkah aman:

1. Cari semua referensi.
2. Pahami apakah masih dibutuhkan untuk konfigurasi deployment.
3. Pastikan tidak merusak data existing.
4. Refactor secara bertahap bila memang dibutuhkan.
5. Tambahkan/update test untuk perilaku penting.

Target akhir bukan SaaS multi-tenant, melainkan aplikasi satu desa yang reusable, aman, dan mudah dikonfigurasi.
