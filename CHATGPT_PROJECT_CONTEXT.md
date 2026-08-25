# ChatGPT Project Context - Webdesku

Dokumen ini adalah briefing teknis lengkap untuk membantu ChatGPT memahami project Webdesku sebelum memberikan saran, membuat kode, menulis dokumentasi, atau melakukan review. Gunakan dokumen ini bersama `AGENTS.MD`, `README.md`, dan dokumen lain di folder `docs/`.

Terakhir diperbarui berdasarkan inspeksi project pada 2026-08-15.

---

## 1. Ringkasan Identitas Project

- Nama repository/codebase: **Webdesku**.
- Jenis aplikasi: **Sistem Informasi Desa / Website Desa**.
- Framework utama: **Laravel 12**.
- Bahasa backend: **PHP 8.2+**.
- Frontend: **Blade**, **Vite**, **Tailwind CSS**, **Alpine.js**, custom CSS publik.
- Database target: MySQL/MariaDB atau database lain yang kompatibel dengan konfigurasi Laravel.
- Bentuk aplikasi: Laravel monolith, bukan microservice.
- Model operasional saat ini: **single-village application**.

Arsitektur konseptual:

```text
One Webdesku deployment
        ->
One village
        ->
Reusable codebase for other independent deployments
```

**Penting:** Webdesku adalah nama internal repository/codebase. Nama ini tidak boleh dipakai sebagai branding publik website desa. Identitas publik website harus berasal dari data/config desa, terutama model `Village`.

---

## 2. Prinsip Arsitektur Paling Penting

### 2.1 Single Village, Bukan Multi-Tenant

Project ini **bukan** SaaS multi-tenant.

Jangan menambahkan:

- tenant switching,
- tenant resolver runtime,
- subdomain tenancy,
- village switching UI,
- tenant-aware authentication,
- session-based current village switching,
- mekanisme satu aplikasi melayani banyak desa sekaligus.

Model yang benar:

```text
Satu deployment Webdesku = satu desa
```

Kode tetap harus reusable untuk desa lain melalui deployment terpisah.

### 2.2 Reusable Tidak Sama Dengan Multi-Tenant

Reusable berarti desa lain dapat:

1. clone repository,
2. konfigurasi `.env`,
3. migrasi database,
4. seed/config data desa,
5. konfigurasi storage/assets,
6. deploy sebagai instance mandiri.

Reusable tidak berarti satu runtime melayani banyak desa.

### 2.3 `Village` Adalah Konfigurasi Deployment

Model `Village` menyimpan identitas dan konfigurasi desa untuk deployment ini, seperti:

- nama desa,
- slug,
- alamat,
- kecamatan/kabupaten/provinsi,
- kepala desa,
- logo,
- kontak,
- visi/misi,
- sejarah,
- koordinat/peta,
- populasi snapshot,
- konfigurasi Instagram.

Walaupun banyak tabel masih memiliki `village_id`, perlakukan itu sebagai detail implementasi historis/legacy yang masih dipakai oleh query saat ini. Jangan mengubahnya menjadi desain multi-tenant baru.

---

## 3. Aturan Identitas Publik

Identitas publik harus dinamis dari data desa.

Implementasi terkait:

- `app/Support/VillageIdentity.php`
- `resources/views/web/web.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/layouts/partials/footer.blade.php`
- `resources/views/components/application-logo.blade.php`

Aturan:

- Homepage title: `[Nama Desa]`.
- Subhalaman title: `[Nama Halaman] | [Nama Desa]`.
- Fallback aman jika data desa belum ada: `Pemerintah Desa`.
- Footer publik memakai identitas Pemerintah Desa dari data `Village`.
- Logo memakai `Village::logo_url` jika tersedia, fallback ke `public/icons/icon_desa.png`.
- Jangan hardcode nama desa, alamat, kontak, logo, atau brand publik di Blade/PHP.
- Jangan tampilkan `Webdesku` sebagai title browser, footer, metadata PDF, atau brand publik.

---

## 4. Struktur Direktori Utama

```text
app/
|-- Console/Commands/
|-- Http/
│   |-- Controllers/
│   │   |-- Admin/
│   │   |-- Auth/
│   │   |-- SuperAdmin/
│   │   |-- HomeController.php
│   │   `-- ProfileController.php
│   |-- Middleware/
│   `-- Requests/
|-- Models/
|-- Providers/
|-- Services/
|-- Support/
`-- View/Components/

bootstrap/
config/
database/
|-- migrations/
|-- seeders/
`-- factories/

docs/
public/
resources/
|-- css/
|-- js/
`-- views/
routes/
storage/
tests/
vendor/
```

Dokumentasi utama:

- `AGENTS.MD`
- `README.md`
- `docs/BLUEPRINT_WEBDESKU.md`
- `docs/PROJECT_REPORT.md`
- `docs/data-governance-matrix.md`
- `docs/frontend-standardization.md`
- `CHATGPT_PROJECT_CONTEXT.md`
- `docs/SANUR_KAJA_DATA_MAPPING.md`

---

## 5. Stack dan Dependency

### Backend

- Laravel `^12.0`
- PHP `^8.2`
- Laravel Breeze authentication
- Eloquent ORM
- Laravel migrations
- Laravel validation
- Laravel filesystem/storage
- Laravel queues/cache/session sesuai konfigurasi environment

### Package PHP Penting

- `barryvdh/laravel-dompdf`: PDF generation.
- `maatwebsite/excel`: Excel export.
- `simplesoftwareio/simple-qrcode`: QR Code generation.
- `spatie/laravel-sitemap`: sitemap generation.
- `laravel/pint`: PHP formatter/style checker.
- `phpunit/phpunit`: automated testing.

### Frontend

- Blade templates.
- Vite.
- Tailwind CSS.
- Alpine.js.
- Axios.
- Custom public CSS: `resources/css/web.css`.
- Admin/auth layout memakai Tailwind/Breeze style.

Package JS utama:

- `vite`
- `laravel-vite-plugin`
- `tailwindcss`
- `@tailwindcss/forms`
- `alpinejs`
- `axios`
- `concurrently`

---

## 6. Routing dan Area Aplikasi

Routing utama ada di:

- `routes/web.php`
- `routes/auth.php`

Jumlah route terinspeksi: 185 route (`php artisan route:list --except-vendor`).

### 6.1 Public Website

Public routes berada dalam middleware `identifyVillage`.

Route publik utama:

- `/` → `HomeController@index` → `home`
- `/profil` → redirect internal ke gambaran umum
- `/profil/gambaran-umum-desa` → `profil.gambaran`
- `/profil/sejarah-desa` → `profil.sejarah`
- `/profil/visi-misi` → `profil.visimisi`
- `/profil/susunan-organisasi` → `profil.organisasi`
- `/berita` → `berita`
- `/berita/{slug}` → `berita.show`
- `/agenda` → `agenda`
- `/agenda/{agenda}` → `agenda.show`
- `/layanan` → `services`
- `/layanan/{slug}` → `services.show`
- `/layanan/{slug}/ajukan` → `services.apply`
- `/layanan/cek-status` → `services.status`
- `/layanan/pengajuan/{token}/cetak` → `services.receipt`
- `/pengaduan` → `complaints.index`
- `/pengaduan/cek-status` → `complaints.status`
- `/statistik` → `statistik`
- `/statistik/pdf` → `statistik.pdf`
- `/statistik/excel` → `statistik.excel`
- `/transparansi` → `transparansi`
- `/infografis` → `infografis`
- `/galeri` → `galeri`
- `/pengumuman` → `pengumuman`
- `/peraturan` → `regulations.index`
- `/peraturan/{announcement}/download` → `regulations.download`
- `/kontak` → `kontak`

Legacy redirects:

- `/news` → `/berita`
- `/news/{slug}` → `/berita/{slug}`
- `/services` → `/layanan`

### 6.2 Auth dan User Profile

Auth memakai Laravel Breeze.

Route auth penting:

- `/login`
- `/register`
- `/forgot-password`
- `/reset-password/{token}`
- `/verify-email`
- `/confirm-password`
- `/logout`

Profile user:

- `/profile` edit/update/delete.

### 6.3 Admin Panel

Prefix admin:

```text
/admin
```

Middleware admin:

```text
auth, verified, role:aparat,super_admin
```

Admin route memakai resource controller untuk modul CRUD.

Area admin utama:

- Dashboard
- Berita
- Agenda
- Pengumuman
- Peraturan
- Layanan Desa
- Pengajuan Layanan
- Pengaduan
- Galeri
- Aset Desa
- Data Penduduk
- Statistik Penduduk
- Luas Wilayah
- Transparansi
- APBDes
- Infografis
- Halaman Profil Desa
- Slider
- Sambutan Kepala Desa
- Perangkat Desa
- Pengaturan Desa
- Peta Desa
- Data Lineage

### 6.4 Super Admin

Prefix:

```text
/super-admin
```

Middleware:

```text
auth, verified, role:super_admin
```

Fungsi utama:

- mengelola `ModuleSetting` melalui `SuperAdmin\ModuleController`.

---

## 7. Middleware dan Role

Middleware custom:

- `IdentifyVillage`
- `EnsureModuleEnabled`
- `RoleMiddleware`

### 7.1 IdentifyVillage

Dipakai pada public route group.

Fungsi saat ini:

- mengikat data desa aktif untuk request publik,
- mendukung akses layout/helper terhadap `currentVillage`,
- bukan tenant resolver SaaS.

### 7.2 EnsureModuleEnabled

Dipakai untuk membatasi route berdasarkan module setting.

Sumber status modul:

- `App\Support\ModuleManager`
- tabel `module_settings`

Default modul dianggap aktif jika tabel/setting belum tersedia.

### 7.3 RoleMiddleware

Digunakan untuk membatasi akses berdasarkan role user.

Role yang terdeteksi:

- `aparat`
- `super_admin`

Helper role pada model `User`:

- `hasRole(string $role)`
- `isAparat()`
- `isSuperAdmin()`

---

## 8. Controller Utama dan Tanggung Jawab

### 8.1 Public Controller

File:

- `app/Http/Controllers/HomeController.php`

Tanggung jawab:

- menampilkan homepage,
- profil desa,
- berita list/detail,
- agenda list/detail,
- layanan desa dan pengajuan layanan,
- status layanan,
- pengaduan dan status pengaduan,
- statistik publik,
- export PDF statistik,
- transparansi dan APBDes,
- infografis,
- galeri,
- pengumuman,
- peraturan,
- kontak.

Catatan:

- Controller ini besar dan menjadi pusat query public website.
- Jangan refactor besar tanpa alasan kuat.
- Jika menambah fitur besar, pertimbangkan service hanya bila ada workflow bermakna.

### 8.2 Admin Controllers

Folder:

- `app/Http/Controllers/Admin/`

Controller admin memakai pola CRUD Laravel resource.

Daftar controller admin:

- `AgendaController`
- `AnnouncementController`
- `ComplaintController`
- `DashboardController`
- `DataLineageController`
- `GalleryController`
- `NewsController`
- `RegulationController`
- `ServiceController`
- `ServiceRequestController`
- `SliderController`
- `VillageApbdesDocumentController`
- `VillageApbdesItemController`
- `VillageAssetController`
- `VillageHeadMessageController`
- `VillageInfographicItemController`
- `VillageLandUseAreaController`
- `VillageMapController`
- `VillageOfficialController`
- `VillagePopulationController`
- `VillagePopulationStatController`
- `VillageProfilePageController`
- `VillageSettingController`
- `VillageTransparencyDocumentController`
- `VillageTransparencyItemController`

### 8.3 Auth Controllers

Folder:

- `app/Http/Controllers/Auth/`

Mengikuti Laravel Breeze:

- authenticated session,
- registration,
- password reset,
- email verification,
- password confirmation.

### 8.4 Super Admin Controller

File:

- `app/Http/Controllers/SuperAdmin/ModuleController.php`

Tanggung jawab:

- mengaktifkan/menonaktifkan modul.

---

## 9. Services dan Support Classes

### 9.1 Services

Folder:

- `app/Services/`

Service yang ada:

- `BigBoundaryService`: import/olah boundary desa dari file BIG.
- `GoogleMapsLinkResolver`: resolve link Google Maps menjadi koordinat/final URL.
- `InstagramFeedService`: integrasi/sinkronisasi feed Instagram.
- `StatisticsService`: menghitung statistik publik dan dataset PDF.
- `VillageStatisticSyncService`: sinkronisasi statistik desa.

### 9.2 Support Classes

Folder:

- `app/Support/`

Support class:

- `ModuleManager`: registry modul dan cache status aktif/nonaktif.
- `VillageIdentity`: helper identitas publik runtime.

`VillageIdentity` menyediakan:

- `village()`
- `name()`
- `governmentName()`
- `title()`
- `defaultPageTitle()`

---

## 10. Model dan Relasi Data

Folder model:

- `app/Models/`

### 10.1 Core Identity

#### `Village`

Pusat identitas/config deployment desa.

Relasi:

- `news()`
- `agendas()`
- `announcements()`
- `services()`
- `galleries()`
- `sliders()`
- `officials()`
- `headMessages()`
- `assets()`
- `populations()`
- `populationStats()`
- `landUseAreas()`
- `apbdesItems()`
- `apbdesDocuments()`
- `infographicItems()`
- `transparencyItems()`
- `transparencyDocuments()`
- `complaints()`
- `instagramPosts()`

Accessor:

- `logo_url`

### 10.2 Content Publishing

#### `News`

Berita desa.

Relasi:

- `village()`
- `author()`
- `images()`

Accessor/helper:

- `thumbnail_url`
- `hasLocalThumbnail()`

#### `NewsImage`

Gambar tambahan berita.

Relasi:

- `news()`

Accessor:

- `image_url`

#### `Agenda`

Agenda/kegiatan desa.

Relasi:

- `village()`

Accessor/helper:

- `poster_url`
- `hasLocalPoster()`

#### `Announcement`

Pengumuman dan peraturan desa.

Relasi:

- `village()`
- `images()`

Accessor/helper:

- `attachment_url`
- `hasLocalAttachment()`
- `typeOptions()`
- `typeMeta()`
- `typeLabel()`
- `typeColor()`
- `typeIcon()`

#### `AnnouncementImage`

Gambar pengumuman/peraturan.

Relasi:

- `announcement()`

Accessor:

- `image_url`

#### `Gallery`

Galeri publik.

Relasi:

- `village()`

Accessor/helper:

- `image_url`
- `thumbnail_url`
- `hasLocalImage()`

#### `Slider`

Slider homepage.

Relasi:

- `village()`

### 10.3 Layanan Publik

#### `VillageService`

Master layanan desa.

Relasi:

- `village()`
- `requests()`

Helper:

- `requirementsList()`
- `processList()`

#### `ServiceRequest`

Pengajuan layanan warga.

Relasi:

- `village()`
- `service()`

Accessor:

- `attachment_url`

### 10.4 Pengaduan

#### `Complaint`

Data pengaduan masyarakat.

Relasi:

- `village()`
- `responses()`

Accessor/helper:

- `attachment_url`
- `statusLabel()`

#### `ComplaintResponse`

Tanggapan admin/aparat terhadap pengaduan.

Relasi:

- `complaint()`
- `user()`

### 10.5 Profil dan Struktur Desa

#### `VillageOfficial`

Perangkat/pejabat desa.

Relasi:

- `village()`

#### `VillageHeadMessage`

Sambutan kepala desa.

Relasi:

- `village()`

#### `VillageProfilePage`

Konten halaman profil desa.

Relasi:

- `village()`

Slug profil penting:

- gambaran umum,
- sejarah,
- visi misi,
- organisasi.

### 10.6 Statistik, Infografis, dan Peta

#### `VillagePopulation`

Snapshot penduduk tahunan.

Relasi:

- `village()`

Helper:

- `total()`

#### `VillagePopulationStat`

Statistik penduduk per kategori.

Relasi:

- `village()`

Helper:

- `categoryOptions()`
- `categoryLabel()`

#### `VillageAsset`

Aset/fasilitas desa untuk infografis dan peta.

Relasi:

- `village()`

Helper:

- `typeOptions()`
- `typeLabel()`
- `typeColor()`
- `icon_url`
- `hasLocalIcon()`

#### `VillageLandUseArea`

Data luas wilayah menurut penggunaan.

Relasi:

- `village()`

#### `VillageInfographicItem`

Infografis tambahan.

Relasi:

- `village()`

Helper:

- `categoryOptions()`
- `categoryLabel()`

Catatan statistik:

- Dipakai juga sebagai sumber indikator agregat tambahan yang non-personal.
- Field `year` menandai data historis/tahunan.
- Field `source` dan `notes` menampung sumber/metodologi ringkas.
- Kategori statistik tambahan mencakup geografi & iklim, infrastruktur, ekonomi, pemerintahan, dan kesehatan & sosial.

### 10.7 Transparansi dan APBDes

#### `VillageApbdesItem`

Item APBDes.

Relasi:

- `village()`

Helper:

- `typeLabel()`

#### `VillageApbdesDocument`

Dokumen/laporan APBDes.

Relasi:

- `village()`

Helper:

- `documentLink()`

#### `VillageTransparencyItem`

Item transparansi umum.

Relasi:

- `village()`

Helper:

- `categoryOptions()`
- `categoryLabel()`

#### `VillageTransparencyDocument`

Dokumen transparansi umum.

Relasi:

- `village()`

Helper:

- `categoryOptions()`
- `categoryLabel()`
- `documentLink()`

### 10.8 Integrasi

#### `VillageInstagramPost`

Cache/snapshot post Instagram desa.

Relasi:

- `village()`

### 10.9 User dan Modul

#### `User`

User admin/aparat/super admin.

Helper:

- `hasRole()`
- `isAparat()`
- `isSuperAdmin()`

#### `ModuleSetting`

Status aktif/nonaktif modul.

Dipakai oleh:

- `ModuleManager`
- middleware module.

---

## 11. Database dan Migration

Migration sudah terdeteksi `Ran` pada environment saat inspeksi.

Tabel/fungsi data utama:

- `users`: user auth dan role.
- `cache`, `jobs`: tabel Laravel pendukung.
- `villages`: identitas dan konfigurasi desa.
- `news`: berita.
- `news_images`: gambar berita.
- `agendas`: agenda/kegiatan.
- `announcements`: pengumuman dan peraturan.
- `announcement_images`: media pengumuman.
- `services`: master layanan desa.
- `service_requests`: pengajuan layanan warga.
- `galleries`: galeri.
- `sliders`: slider homepage.
- `village_officials`: perangkat desa.
- `village_head_messages`: sambutan kepala desa.
- `village_assets`: aset/fasilitas desa.
- `village_populations`: snapshot penduduk tahunan.
- `village_population_stats`: statistik penduduk per kategori.
- `village_land_use_areas`: luas wilayah menurut penggunaan.
- `village_apbdes_items`: data APBDes.
- `village_apbdes_documents`: dokumen APBDes.
- `village_infographic_items`: infografis tambahan.
- `village_profile_pages`: konten halaman profil.
- `village_transparency_items`: item transparansi.
- `village_transparency_documents`: dokumen transparansi.
- `complaints`: pengaduan masyarakat.
- `complaint_responses`: tanggapan pengaduan.
- `module_settings`: status modul.
- `village_instagram_posts`: cache post Instagram.

Catatan schema:

- Banyak tabel konten memiliki `village_id`.
- Banyak tabel publikasi memiliki `is_published` dan/atau `published_at`.
- Beberapa tabel file/media menyimpan path lokal dan accessor URL.
- Beberapa migration menambahkan indeks/constraint untuk performa dan konsistensi.
- Jangan menjalankan `migrate:fresh`, `db:wipe`, `migrate:refresh`, atau operasi destruktif tanpa instruksi eksplisit.

---

## 12. Data Governance Publik

Dokumen terkait:

- `docs/data-governance-matrix.md`

Prinsip:

- Data publik harus berasal dari tabel resmi, bukan angka fallback sintetis.
- Jika data belum tersedia, tampilkan empty state informatif.
- Data lintas halaman memakai helper/query yang konsisten di `HomeController`.
- Statistik publik memakai `StatisticsService`.

Mapping utama:

- Infografis aset → `village_assets`
- Penduduk tahunan → `village_populations`
- Statistik kategori penduduk → `village_population_stats`
- Infografis tambahan → `village_infographic_items`
- Gambaran umum profil → `villages`, `village_profile_pages`, statistik terkait
- Luas wilayah → `village_land_use_areas`
- Transparansi APBDes → `village_apbdes_items`, `village_apbdes_documents`
- Transparansi dokumen umum → `village_transparency_documents`
- Statistik periodik → `news`, `agendas`, `announcements`, `galleries`, `complaints`, `service_requests`
- Statistik snapshot/master → `village_populations`, `services`, `village_assets`

---

## 13. Flow Public Website

### 13.1 Homepage

Flow:

```text
GET /
  → identifyVillage middleware
  → HomeController@index
  → currentVillage()
  → query module states
  → query data ringkas per modul
  → resources/views/web/home.blade.php
  → resources/views/web/web.blade.php
```

Data homepage dapat mencakup:

- slider,
- berita terbaru,
- layanan unggulan,
- sambutan kepala desa,
- perangkat desa,
- agenda,
- pengumuman,
- galeri,
- post Instagram,
- statistik penduduk,
- state modul aktif/nonaktif.

### 13.2 Profil Desa

Flow:

```text
GET /profil/*
  → identifyVillage
  → HomeController profil method
  → profileContext()
  → merge data Village + VillageProfilePage + statistik terkait
  → Blade profile view
```

Halaman:

- gambaran umum,
- sejarah,
- visi misi,
- susunan organisasi.

### 13.3 Berita

List:

```text
GET /berita
  → HomeController@news
  → publishedNewsQuery()
  → paginate
  → web.news.index
```

Detail:

```text
GET /berita/{slug}
  → HomeController@newsShow
  → publishedNewsQuery()
  → slug lookup
  → optional increment view_count
  → related news
  → web.news.show
```

### 13.4 Agenda

List:

```text
GET /agenda?status=&q=&month=&day=
  → HomeController@agenda
  → filter keyword/status/month/day
  → map agenda items
  → calendar data
  → paginate
  → web.agenda.index
```

Detail:

```text
GET /agenda/{agenda}
  → route model binding
  → publication check
  → village_id consistency check if village exists
  → related agenda
  → web.agenda.show
```

### 13.5 Layanan Desa

List:

```text
GET /layanan
  → HomeController@services
  → publishedServicesQuery()
  → paginate
  → web.services.index
```

Detail:

```text
GET /layanan/{slug}
  → HomeController@serviceShow
  → lookup service by slug
  → web.services.show
```

Pengajuan:

```text
POST /layanan/{slug}/ajukan
  → validate request
  → optional attachment upload to public disk
  → generate ticket_code
  → generate public_token
  → create ServiceRequest
  → redirect with status, ticket, receipt URL
```

Cek status:

```text
GET /layanan/cek-status?ticket=
  → lookup ServiceRequest by ticket_code
  → web.services.status
```

Cetak bukti:

```text
GET /layanan/pengajuan/{token}/cetak
  → lookup ServiceRequest by public_token
  → generate QR SVG
  → render PDF receipt
```

### 13.6 Pengaduan

Form:

```text
GET /pengaduan
  → HomeController@complaints
  → complaintCategories()
  → web.complaints.index
```

Submit:

```text
POST /pengaduan
  → validate input
  → optional attachment upload to public disk
  → generate ticket_code
  → generate public_token
  → create Complaint
  → redirect with ticket and lookup URL
```

Cek status:

```text
GET /pengaduan/cek-status?ticket=
  → lookup complaint by ticket
  → include responses if table exists
  → web.complaints.status
```

### 13.7 Statistik, PDF, dan Excel

Halaman:

```text
GET /statistik?start_year=&end_year=
  → StatisticsPeriodRequest
  → StatisticsService::report()
  → web.statistics.index
```

PDF:

```text
GET /statistik/pdf?start_year=&end_year=
  → StatisticsPeriodRequest
  → StatisticsService::report()
  → pdf.statistics-report
  → DomPDF download
```

Excel:

```text
GET /statistik/excel?start_year=&end_year=
  -> StatisticsPeriodRequest
  -> StatisticsService::report()
  -> streamed HTML table .xls response
```

Aturan:

- `start_year` dan `end_year` memfilter statistik periodik.
- Data snapshot/master diberi label sesuai sumber.
- Web, PDF, dan Excel memakai dataset service yang sama.
- Jika snapshot penduduk tidak tersedia pada periode terpilih, tampilkan `Tidak tersedia` / empty-state, bukan angka `0`.
- Indikator agregat tambahan berasal dari `village_infographic_items` yang published, non-personal, dan difilter menurut `year` jika tersedia.

### 13.8 Transparansi

Flow:

```text
GET /transparansi?tab=apbdes|dokumen&year=
  → module:transparency
  → HomeController@transparansi
  → resolve APBDes dataset
  → query documents/items
  → web.transparency.index
```

### 13.9 Infografis

Flow:

```text
GET /infografis?tab=aset|penduduk|lainnya&type=&q=&year=
  → module:infographics
  → HomeController@infografis
  → query assets/population/APBDes/other infographic
  → build governance metadata
  → web.infographics.index
```

### 13.10 Galeri, Pengumuman, Peraturan, Kontak

Galeri:

```text
GET /galeri
  → published galleries
  → web.gallery.index
```

Pengumuman:

```text
GET /pengumuman?q=&type=
  → published announcements
  → web.announcements.index
```

Peraturan:

```text
GET /peraturan?q=
  → announcements filtered as regulations
  → web.regulations.index
```

Download peraturan:

```text
GET /peraturan/{announcement}/download
  → type check
  → publication check
  → local attachment check
  → Storage download
```

Kontak:

```text
GET /kontak
  → web.page
  → address/phone/email from Village
```

---

## 14. Flow Admin Panel

Admin panel menggunakan Blade layout:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `resources/views/layouts/partials/admin-breadcrumbs.blade.php`

Flow umum CRUD:

```text
Admin route
  → auth + verified + role middleware
  → optional module middleware
  → Admin Controller
  → validate request
  → create/update/delete Eloquent model
  → redirect to index/edit with flash message
```

Pola view admin:

```text
resources/views/admin/{module}/
|-- index.blade.php
|-- create.blade.php
|-- edit.blade.php
`-- _form.blade.php
```

Modul admin yang mengikuti pola ini:

- news,
- agendas,
- announcements,
- regulations,
- services,
- galleries,
- sliders,
- officials,
- head messages,
- profile pages,
- village assets,
- village populations,
- village population stats,
- village land use areas,
- village transparency items,
- village transparency documents,
- village APBDes items,
- village APBDes documents,
- village infographic items.

Fitur admin khusus:

- `service-requests`: index, show, update, destroy, export Excel, export PDF.
- `complaints`: index/update dan redirect show.
- `village-settings`: edit/update identitas desa, sync Instagram.
- `village-map`: edit/update/import BIG.
- `data-lineage`: melihat sumber/jejak data.

---

## 15. Module System

Class:

- `App\Support\ModuleManager`

Tabel:

- `module_settings`

Modul yang terdaftar:

- `services` → Layanan Desa
- `complaints` → Pengaduan Masyarakat
- `news` → Berita
- `agendas` → Agenda
- `announcements` → Pengumuman
- `regulations` → Peraturan Desa
- `galleries` → Galeri
- `transparency` → Transparansi
- `infographics` → Infografis
- `profile` → Profil Desa

Perilaku:

- default modul aktif jika setting belum tersedia,
- status modul dicache,
- `setEnabled()` update DB dan clear cache,
- public/admin route tertentu dibungkus middleware `module:{key}`.

---

## 16. View dan Frontend

### 16.1 Public Layout

Layout publik:

- `resources/views/web/web.blade.php`

Partial:

- `resources/views/layouts/partials/navbar.blade.php`
- `resources/views/layouts/partials/footer.blade.php`

Public page views:

- `resources/views/web/home.blade.php`
- `resources/views/web/page.blade.php`
- `resources/views/web/news/*`
- `resources/views/web/agenda/*`
- `resources/views/web/services/*`
- `resources/views/web/complaints/*`
- `resources/views/web/statistics/*`
- `resources/views/web/transparency/*`
- `resources/views/web/infographics/*`
- `resources/views/web/gallery/*`
- `resources/views/web/announcements/*`
- `resources/views/web/regulations/*`
- `resources/views/web/profile/*`

### 16.2 Frontend Standardization

Dokumen:

- `docs/frontend-standardization.md`

Aturan halaman publik selain homepage:

- wrapper dasar: `section-wrap` + `container-grid`,
- hero header: `page-hero section-card`,
- gunakan `page-section-stack` untuk spacing,
- gunakan `form-control` dan `form-control-button` untuk kontrol form,
- hindari inline max-width dan inline spacing acak.

Homepage boleh punya struktur khusus.

### 16.3 Admin/Auth Layout

Admin dan auth memakai layout Breeze/Tailwind:

- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/components/*`

---

## 17. File Upload, Storage, dan Media

Prinsip:

- gunakan Laravel Storage,
- validasi MIME/type,
- validasi ukuran file,
- jangan percaya original filename,
- simpan path di database,
- akses URL melalui accessor/helper model bila tersedia.

Contoh field/media:

- berita: thumbnail dan `news_images`,
- agenda: poster,
- pengumuman/peraturan: attachment dan images,
- galeri: image/thumbnail,
- layanan/pengaduan: attachment warga,
- village asset: icon,
- APBDes/transparency document: document path/link.

Command penting:

```bash
php artisan storage:link
```

---

## 18. PDF, Excel, QR, dan Integrasi

### PDF

Dipakai untuk:

- receipt pengajuan layanan,
- laporan statistik publik,
- export laporan pengajuan layanan admin.

Package:

- `barryvdh/laravel-dompdf`

Template PDF:

- `resources/views/web/services/receipt.blade.php`
- `resources/views/pdf/statistics-report.blade.php`
- `resources/views/admin/service-requests/report-pdf.blade.php`

### Excel

Dipakai untuk export service request admin dan export statistik publik.

Package tersedia:

- `maatwebsite/excel`

Catatan implementasi:

- Export service request admin memakai package existing.
- Export statistik publik saat ini memakai streamed HTML table `.xls` dari payload `StatisticsService`.
- Roadmap masih menyisakan TODO bila export statistik publik perlu dipindahkan ke package Excel existing.

Route statistik publik:

- `statistik.excel`

### QR Code

Dipakai pada receipt layanan.

Package:

- `simplesoftwareio/simple-qrcode`

### Maps

Komponen terkait:

- field koordinat pada `villages`, `agendas`, `village_assets`,
- `GoogleMapsLinkResolver`,
- reverse geocoding pada `VillageAssetController`,
- `BigBoundaryService`,
- `ImportVillageBoundaryFromBig`,
- `VillageMapController`.

### Instagram

Komponen terkait:

- field Instagram pada `villages`,
- `VillageInstagramPost`,
- `InstagramFeedService`,
- `InstagramSyncPostsCommand`,
- `VillageSettingController@syncInstagram`.

---

## 19. Testing

Folder:

- `tests/Feature`
- `tests/Unit`

Test yang ada:

- auth tests dari Breeze,
- profile tests,
- example feature/unit tests,
- `StatisticsReportTest`,
- `VillageIdentityTest`.

Perilaku yang sudah diuji:

- auth login/logout/register/reset/verification,
- profile update/delete,
- halaman utama/transparency basic response,
- filter statistik rentang tahun,
- validasi rentang tahun statistik,
- PDF statistik memakai dataset service yang sama,
- identitas publik dinamis dari data desa,
- title homepage/subhalaman/auth tidak memakai brand internal.

Command:

```bash
php artisan test
php artisan test --filter=VillageIdentityTest
php artisan test --filter=StatisticsReportTest
```

Catatan style:

- `vendor/bin/pint --test` secara global masih dapat gagal karena style legacy di banyak file.
- Untuk file baru/terkait, jalankan Pint terbatas agar tidak memformat massal file yang tidak terkait.

---

## 20. Development Workflow Wajib

Sebelum mengubah kode:

1. Baca `AGENTS.MD`.
2. Baca dokumen relevan di `docs/`.
3. Inspect route, controller, model, migration, view, dan test terkait.
4. Pahami flow lengkap.
5. Buat perubahan paling kecil dan aman.
6. Jalankan test relevan.
7. Update docs jika perubahan menyentuh arsitektur/data/route/module/business rule.
8. Review `git diff` dan `git status`.

Jangan:

- refactor besar tanpa alasan kuat,
- mengubah arsitektur single-village menjadi multi-tenant,
- menghapus legacy `village_id` tanpa analisis dependency,
- hardcode identitas desa,
- bypass authorization,
- bypass validasi,
- mengklaim test lulus jika belum dijalankan,
- menjalankan command database destruktif tanpa instruksi eksplisit.

---

## 21. Security Rules

Selalu pertimbangkan:

- CSRF,
- XSS,
- SQL injection,
- mass assignment,
- authorization bypass,
- insecure file upload,
- path traversal,
- IDOR,
- sensitive information exposure,
- unsafe redirects,
- session security.

Aturan praktis:

- gunakan `$request->validate()` atau Form Request,
- gunakan `$fillable`/mass assignment strategy eksplisit,
- validasi ownership/konsistensi data saat akses record,
- jangan expose `.env`, token, credential, private storage,
- jangan log password/token/API secret.

---

## 22. Dokumentasi dan Sumber Kebenaran

Priority order saat terjadi konflik:

1. Instruksi eksplisit user saat ini.
2. Implementasi codebase saat ini.
3. Dokumentasi current di `/docs`.
4. Migration/database saat ini.
5. Routes saat ini.
6. Tests saat ini.
7. Blueprint/proposal historis.
8. Asumsi umum.

Jika docs menyebut multi-desa/SaaS tetapi `AGENTS.MD` dan docs terbaru menyebut single-village, ikuti single-village.

---

## 23. Cara Memberi Prompt ke ChatGPT Menggunakan Dokumen Ini

Contoh instruksi yang disarankan:

```text
Kamu membantu project Laravel bernama Webdesku. Baca dan ikuti dokumen
CHATGPT_PROJECT_CONTEXT.md ini. Pahami bahwa Webdesku adalah single-village
application, bukan multi-tenant SaaS. Nama Webdesku hanya internal codebase,
sedangkan identitas publik harus berasal dari data Village. Jangan mengubah
arsitektur tanpa alasan jelas. Sebelum memberi solusi, jelaskan file/layer
yang akan terdampak dan cara verifikasinya.
```

Jika ChatGPT akan diminta membuat kode, tambahkan:

```text
Ikuti existing Laravel conventions, jaga controller tetap tipis, gunakan
validasi server-side, pertimbangkan security, dan update dokumentasi jika
perubahan menyentuh route/database/module/business rule. Jangan mengklaim test
lulus kecuali test benar-benar dijalankan.
```

---

## 24. Checklist Untuk ChatGPT Sebelum Menjawab Task Coding

Gunakan checklist ini sebelum memberi saran implementasi:

- [ ] Apakah task menyentuh public, admin, auth, database, atau docs?
- [ ] Apakah ada route/controller/model/view yang sudah ada?
- [ ] Apakah perlu validasi Form Request?
- [ ] Apakah perlu authorization/role check?
- [ ] Apakah perubahan bisa merusak single-village architecture?
- [ ] Apakah ada hardcoded identity desa?
- [ ] Apakah query sudah memakai data source resmi?
- [ ] Apakah file upload aman?
- [ ] Apakah PDF/Excel/QR memakai package existing?
- [ ] Apakah perlu test feature/unit?
- [ ] Apakah docs perlu diperbarui?
- [ ] Command apa yang perlu dijalankan untuk verifikasi?

---

## 25. Ringkasan Mental Model

Pahami Webdesku sebagai:

```text
Laravel 12 monolith
  |-- Public website untuk warga
  |-- Admin panel untuk aparat desa
  |-- Super admin untuk module settings
  |-- Village sebagai konfigurasi identitas deployment
  |-- Content modules dengan CRUD admin dan tampilan publik
  |-- Service request dan complaint workflow
  |-- Statistik/infografis/transparansi berbasis data DB
  |-- PDF/Excel/QR/Map/Instagram support
  `-- Reusable codebase untuk deployment desa lain
```

Target pengembangan:

```text
Stable single-village deployment
        +
Reusable codebase
        -
Runtime multi-tenancy
        -
Hardcoded public identity
```
