# Data Governance Matrix (Frontend Publik)

Dokumen ini menjadi acuan sumber data resmi (single source of truth) untuk halaman publik.  
Semua komponen di bawah wajib menampilkan data dari tabel database yang disebutkan, dan dikelola lewat CRUD backend terkait.

## Prinsip Umum
- Satu komponen publik = satu sumber data utama.
- Tidak menggunakan angka/manual fallback sintetis untuk menampilkan data statistik.
- Jika data belum tersedia di DB, UI menampilkan empty-state informatif.
- Data lintas halaman memakai helper/query yang sama di `HomeController` untuk mencegah inkonsistensi.

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

## Catatan Implementasi Teknis
- `HomeController::resolveApbdesDataset()` dipakai bersama oleh Transparansi dan Infografis agar perhitungan tahun aktif dan summary APBDes konsisten.
- `HomeController::resolveLatestPopulationStatsByCategory()` dipakai bersama oleh Gambaran dan Infografis Penduduk.
- Data `luas` pada Gambaran di-override dari query `village_land_use_areas` setelah merge payload profile, untuk mencegah payload statis menimpa data DB aktual.
