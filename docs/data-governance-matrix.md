# Data Governance Matrix (Frontend Publik)

Dokumen ini menjadi acuan sumber data resmi (single source of truth) untuk halaman publik.  
Semua komponen di bawah wajib menampilkan data dari tabel database yang disebutkan, dan dikelola lewat CRUD backend terkait.

## Konteks Arsitektur Saat Ini
- Webdesku saat ini adalah single-village application.
- Nama Webdesku adalah identitas internal codebase, bukan identitas publik yang ditampilkan kepada warga.
- Data governance di dokumen ini mengatur konsistensi sumber data dalam satu deployment desa.
- Jika tabel masih memiliki `village_id`, perlakukan sebagai legacy implementation detail sampai ada refactor eksplisit.
- Jangan menafsirkan matrix ini sebagai arahan untuk membangun multi-tenancy atau tenant isolation runtime.

## Prinsip Umum
- Satu komponen publik = satu sumber data utama.
- Tidak menggunakan angka/manual fallback sintetis untuk menampilkan data statistik.
- Jika data belum tersedia di DB, UI menampilkan empty-state informatif.
- Data lintas halaman memakai helper/query yang sama di `HomeController` untuk mencegah inkonsistensi.
- Identitas publik website memakai data/config desa terpusat; fallback publik yang aman adalah `Pemerintah Desa`.

## Matriks Sumber Data

1. **Infografis > Tab Aset Desa**
- Sumber data: `village_assets`
- Backend CRUD: `admin.village-assets.*`
- Halaman publik: `web.infographics` tab `aset`

2. **Infografis > Tab Penduduk (total/laki/perempuan/KK/tren)**
- Sumber data: `village_populations`
- Backend CRUD: `admin.village-populations.*`
- Halaman publik: `web.infographics` tab `penduduk`

3. **Infografis > Tab Penduduk (agama, pekerjaan, pendidikan, status kawin, umur)**
- Sumber data: `village_population_stats`
- Backend CRUD: `admin.village-population-stats.*`
- Halaman publik: `web.infographics` tab `penduduk`

4. **Infografis > Tab Lainnya**
- Sumber data: `village_infographic_items`
- Backend CRUD: `admin.village-infographic-items.*`
- Halaman publik: `web.infographics` tab `lainnya`

5. **Gambaran Umum Desa > Statistik Penduduk**
- Sumber data: `village_populations` + `village_population_stats`
- Backend CRUD: `admin.village-populations.*`, `admin.village-population-stats.*`
- Halaman publik: `web.profile.gambaran`

6. **Gambaran Umum Desa > Luas Wilayah Menurut Penggunaan**
- Sumber data: `village_land_use_areas`
- Backend CRUD: `admin.village-land-use-areas.*`
- Halaman publik: `web.profile.gambaran`

7. **Transparansi > APBDes Ringkasan dan Rincian**
- Sumber data: `village_apbdes_items`
- Backend CRUD: `admin.village-apbdes-items.*`
- Halaman publik: `web.transparency` tab `apbdes`

8. **Transparansi > Dokumen/Laporan APBDes**
- Sumber data: `village_apbdes_documents`
- Backend CRUD: `admin.village-apbdes-documents.*`
- Halaman publik: `web.transparency` tab `apbdes`

9. **Transparansi > Dokumen Transparansi Umum**
- Sumber data: `village_transparency_documents`
- Backend CRUD: `admin.village-transparency-documents.*`
- Halaman publik: `web.transparency` tab `dokumen`

10. **Statistik Publik > Statistik Periodik**
- Sumber data:
  - Berita: `news.published_at`
  - Agenda: `agendas.start_at`
  - Pengumuman: `announcements.published_at`
  - Galeri: `galleries.published_at`
  - Pengaduan: `complaints.submitted_at`
  - Pengajuan layanan: `service_requests.submitted_at`
- Service: `App\Services\StatisticsService`
- Halaman publik: `web.statistics.index`
- Endpoint PDF: `statistik.pdf`

11. **Statistik Publik > Statistik Snapshot/Master**
- Sumber data:
  - Penduduk: `village_populations.year` sebagai snapshot tahunan
  - Layanan aktif: `services`
  - Aset desa: `village_assets`
- Service: `App\Services\StatisticsService`
- Catatan: data snapshot/master tidak boleh dipaksa memakai field tanggal yang tidak mewakili periode data.

12. **Statistik Publik > Indikator Agregat Tambahan**
- Sumber data: `village_infographic_items`
- Backend CRUD: `admin.village-infographic-items.*`
- Halaman publik: `web.statistics.index` dan `web.infographics` tab `lainnya`
- Kategori: umum, layanan, kelembagaan, geografi & iklim, infrastruktur, ekonomi, pemerintahan, sosial, kesehatan & sosial, lingkungan.
- Catatan: `year` dipakai untuk data historis; data tanpa tahun dianggap data terkini. Data ini hanya untuk agregat non-personal dan tidak boleh memuat data individu.

## Catatan Implementasi Teknis
- `HomeController::resolveApbdesDataset()` dipakai bersama oleh Transparansi dan Infografis agar perhitungan tahun aktif dan summary APBDes konsisten.
- `HomeController::resolveLatestPopulationStatsByCategory()` dipakai bersama oleh Gambaran dan Infografis Penduduk.
- Data `luas` pada Gambaran di-override dari query `village_land_use_areas` setelah merge payload profile, untuk mencegah payload statis menimpa data DB aktual.
- Filter statistik publik memakai query parameter `start_year` dan `end_year`.
- Laporan PDF statistik memakai dataset yang sama dari `App\Services\StatisticsService`, sehingga angka halaman web dan PDF konsisten.
- Export Excel statistik tersedia pada route `statistik.excel` dan memakai dataset yang sama dari `App\Services\StatisticsService`.
- Metadata dan label sumber data pada PDF statistik memakai identitas Sistem Informasi Desa dari desa deployment, bukan brand internal codebase.
- PDF statistik adalah hasil rekapitulasi sistem untuk bahan informasi/administrasi; status sebagai dokumen resmi tetap membutuhkan verifikasi dan pengesahan Pemerintah Desa.
- Mapping dataset Sanur Kaja terdokumentasi di `docs/SANUR_KAJA_DATA_MAPPING.md`.
