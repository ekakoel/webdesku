# Webdesku --- Roadmap Implementasi Statistik Desa

Status: `[ ] TODO` / `[~] IN PROGRESS` / `[x] DONE` / `[-] BLOCKED`

## Tujuan

Memperluas Webdesku agar data statistik desa dapat dikelola melalui CRUD
admin, ditampilkan pada frontend, difilter berdasarkan rentang tahun,
serta diekspor ke PDF dan Excel secara konsisten tanpa merusak sistem
existing.

## PHASE 0 --- Audit & Baseline

-   [x] Baca `AGENTS.MD`.
-   [x] Baca `CHATGPT_PROJECT_CONTEXT.md`.
-   [x] Baca docs relevan di `/docs`, terutama governance dan frontend
    standardization.
-   [x] Inspect routes statistik.
-   [x] Inspect `StatisticsService`.
-   [x] Inspect `VillagePopulation`.
-   [x] Inspect `VillagePopulationStat`.
-   [x] Inspect `VillageAsset`.
-   [x] Inspect `VillageLandUseArea`.
-   [x] Inspect `VillageInfographicItem`.
-   [x] Inspect CRUD admin statistik existing.
-   [x] Inspect frontend statistik.
-   [x] Inspect PDF statistik.
-   [x] Inspect tests statistik.
-   [x] Jalankan baseline test.
-   [x] Catat file/layer yang terdampak.

**Gate:** jangan membuat migration baru sebelum mapping source-of-truth
selesai.

## PHASE 1 --- Mapping Data Sanur Kaja

Untuk setiap dataset tentukan: `USE EXISTING` → `CALCULATE` →
`EXTEND EXISTING` → `NEW DATA MODEL`.

### Kependudukan

-   [x] Jumlah penduduk → `village_populations`.
-   [x] Laki-laki/perempuan/KK → `village_populations`.
-   [x] Umur → `village_population_stats`.
-   [x] Pendidikan → `village_population_stats`.
-   [x] Pekerjaan → `village_population_stats`.
-   [x] Dataset kependudukan lain → mapping dan dokumentasi.

### Geografi & Iklim

-   [x] Luas/penggunaan lahan → evaluasi `village_land_use_areas`.
-   [x] Peta/batas → existing village/map/boundary source.
-   [x] Ketinggian → evaluasi source.
-   [x] Curah hujan → kandidat dataset baru bila belum tersedia.
-   [x] Suhu → kandidat dataset baru bila belum tersedia.

### Infrastruktur

-   [x] Pendidikan → evaluasi `village_assets`.
-   [x] Kesehatan → evaluasi `village_assets`.
-   [x] Tempat ibadah → evaluasi `village_assets`.
-   [x] Statistik fasilitas dihitung dari aset jika memungkinkan.

### Ekonomi

-   [x] BUMDes.
-   [x] Koperasi.
-   [x] Lembaga ekonomi.
-   [x] Perbankan.
-   [x] UMKM/usaha.
-   [x] Mata pencaharian.
-   [x] Pengangguran.
-   [x] Pendapatan/per kapita.

### Pemerintahan

-   [x] Perangkat desa → `village_officials`.
-   [x] APBDes → existing APBDes models.
-   [x] Transparansi → existing transparency models.
-   [x] Produk hukum → existing announcements/regulations.

### Kesehatan/Sosial

-   [x] Penyakit.
-   [x] Posyandu.
-   [x] Imunisasi.
-   [x] Ibu hamil.
-   [x] Bayi.
-   [x] Gizi.
-   [x] KB.
-   [x] PHBS.
-   [x] Pastikan data public bersifat agregat, bukan identitas individu.

### Data Registry

-   [x] Buat matrix dataset.
-   [x] Status `AVAILABLE/PARTIAL/NOT_AVAILABLE`.
-   [x] Status `CURRENT_ONLY/HISTORICAL`.
-   [x] Source.
-   [x] Unit.
-   [x] Public/private.
-   [x] Exportable/not exportable.

## PHASE 2 --- Statistical Extension

-   [x] Finalisasi dataset yang benar-benar membutuhkan model baru.
-   [x] Gunakan `village_id` existing; jangan membuat multi-tenancy.
-   [x] Dukung `year`.
-   [x] Dukung category/code/label/dimension bila diperlukan.
-   [x] Dukung value/unit/source/notes.
-   [x] Dukung publication status jika public.
-   [x] Buat migration baru.
-   [x] Tambahkan foreign key dan index.
-   [x] Evaluasi unique constraint: tidak ditambahkan karena `year` bersifat nullable dan unique index biasa di MySQL tidak mencegah lebih dari satu nilai `NULL`; validasi business key `village_id + category + year + title` telah diuji, dan audit data tidak menemukan duplikat.
-   [x] Buat model dengan `$fillable`, casts, dan relation yang aman.
-   [x] Hindari duplicate aggregate.

## PHASE 3 --- Admin CRUD

-   [x] Audit CRUD `VillagePopulation`.
-   [x] Audit CRUD `VillagePopulationStat`.
-   [x] Validation year/numeric/category.
-   [x] Publish/unpublish.
-   [x] Prevent duplicate.
-   [x] CRUD dataset baru: index/create/store/edit/update/delete.
-   [x] Authorization tetap `auth + verified + role`.
-   [x] Gunakan pola `admin/{module}/index/create/edit/_form`.
-   [x] Label bahasa Indonesia yang mudah dipahami.
-   [x] Unit dan tahun terlihat jelas.
-   [x] Bedakan `0` dengan `Data belum tersedia`.
-   [x] Confirmation delete.
-   [x] Flash message.

## PHASE 4 --- Unified StatisticsService

-   [x] Audit `StatisticsService`.
-   [x] Semua dataset statistik dikumpulkan melalui service.
-   [x] Population → `VillagePopulation`.
-   [x] Population categories → `VillagePopulationStat`.
-   [x] Assets → `VillageAsset`.
-   [x] Land use → `VillageLandUseArea`.
-   [x] APBDes/transparency → source existing.
-   [x] Dataset tambahan → extension model.
-   [x] Hindari query statistik yang tersebar di Blade.
-   [x] Web/PDF/Excel menggunakan dataset yang sama.

## PHASE 5 --- Frontend

-   [x] Audit posisi menu `Statistik`.
-   [x] Jangan redesign besar.
-   [x] Gunakan label `Statistik Desa`.
-   [x] Filter `start_year`.
-   [x] Filter `end_year`.
-   [x] Validasi `start_year <= end_year`.
-   [x] Reset filter.
-   [x] Tampilkan periode aktif.
-   [x] Tampilkan kategori secara jelas.
-   [x] Tampilkan unit dan tahun.
-   [x] Empty state informatif.
-   [x] Responsive/mobile.
-   [x] Tombol `Download PDF`.
-   [x] Tombol `Download Excel`.
-   [x] Export mengikuti filter aktif.

## PHASE 6 --- PDF Laporan Desa

-   [x] Header identitas desa dinamis.
-   [-] Logo desa dinamis menunggu verifikasi render pada environment dengan ekstensi PHP GD. Implementasi sudah mengambil logo `Village` secara kondisional dan aman tanpa logo bila GD tidak tersedia; PHP CLI saat release audit ini tidak memuat GD.
-   [x] Judul laporan.
-   [x] Periode.
-   [x] Tanggal generate.
-   [x] Ringkasan.
-   [x] Tabel statistik.
-   [x] Source data.
-   [x] Catatan/metodologi bila diperlukan.
-   [x] Footer/page number.
-   [x] A4 portrait diverifikasi untuk laporan statistik (`setPaper('a4')`); landscape tidak diperlukan karena seluruh tabel laporan dirancang dalam lebar portrait.
-   [x] Tidak menampilkan `Webdesku`.
-   [x] Tidak mengklaim status legal/ditandatangani tanpa kewenangan.
-   [x] Angka PDF sama dengan frontend.
-   [x] Gunakan `StatisticsService`.

## PHASE 7 --- Excel

-   [x] Nama file dinamis.
-   [x] Identitas desa.
-   [x] Geografi & iklim.
-   [x] Kependudukan.
-   [x] Infrastruktur.
-   [x] Ekonomi.
-   [x] Pemerintahan.
-   [x] Kesehatan/sosial jika tersedia.
-   [x] Data dictionary/daftar data.
-   [x] Historical data per tahun.
-   [x] Source/unit jelas.
-   [x] `Data belum tersedia` tidak menjadi `0`.
-   [x] Tidak ada data pribadi pada public export.
-   [-] Upgrade package Excel diperlukan sebelum gate ini dapat ditutup. Paket terkunci saat audit adalah `maatwebsite/excel` v1.1.5 (untuk Laravel 4/PHPExcel), tidak kompatibel atau terdaftar pada Laravel 12; upgrade ke rilis Laravel-kompatibel diblokir karena PHP CLI tidak memiliki ekstensi `zip`. Export `.xls` streaming saat ini tetap memakai payload `StatisticsService` dan telah diuji, tetapi belum dapat diklaim memakai package yang kompatibel.
-   [x] Excel menggunakan dataset yang sama dengan web/PDF.

## PHASE 8 --- UI/UX

-   [x] Pertahankan visual identity existing.
-   [x] Sesuaikan posisi menu/navigasi secara minimal.
-   [x] Ikuti `docs/frontend-standardization.md`.
-   [x] Gunakan existing classes/components.
-   [x] Jangan mengganti typography/color system tanpa instruksi.
-   [x] Public UI mudah dipahami warga.
-   [x] Admin UI mudah dipahami operator.
-   [x] Form tidak terlalu padat.
-   [x] Validation error jelas.
-   [x] Mobile/tablet/desktop usable.
-   [x] Hindari istilah teknis pada public UI.

## PHASE 9 --- Security & Governance

-   [x] CSRF/XSS/SQL injection check.
-   [x] Mass assignment check.
-   [x] Authorization/IDOR check.
-   [x] File/export access check.
-   [x] Tidak ada data pribadi pada statistik publik.
-   [x] Tidak ada credential/token pada export.
-   [x] Source data tercatat.
-   [x] Published data saja yang public.

## PHASE 10 --- Testing

-   [x] Unit test aggregation.
-   [x] Unit test year filter.
-   [x] Unit test invalid period.
-   [x] Feature test frontend.
-   [x] Feature test PDF.
-   [x] Feature test Excel.
-   [x] CRUD create/update/delete.
-   [x] Authorization.
-   [x] Unpublished data tidak tampil.
-   [x] Regression test modul existing.
-   [x] Jalankan test relevan dan full suite bila perubahan luas.

## PHASE 11 --- Documentation

-   [x] Update project context bila arsitektur berubah.
-   [x] Update data governance.
-   [x] Dokumentasikan dataset baru.
-   [x] Dokumentasikan source-of-truth.
-   [x] Dokumentasikan migration/route/export.
-   [x] Dokumentasikan testing/deployment.

## PHASE 12 --- Final Release

-   [-] Semua task kritis belum dapat ditutup karena verifikasi logo PDF (GD), package Excel kompatibel (ZIP), dan backup deployment memerlukan environment/otoritas deployment.
-   [x] Tidak ada duplicate source-of-truth.
-   [x] CRUD/frontend/PDF/Excel/filter berfungsi.
-   [x] Desktop/mobile tested.
-   [x] Security reviewed.
-   [x] `git diff` reviewed.
-   [x] `git status` reviewed.
-   [x] Tidak ada debug code/credential.
-   [x] Tidak ada hardcoded identity.
-   [x] Tidak ada `Webdesku` pada public output.
-   [-] Database backup sebelum deployment menunggu akses database target. Sebelum menjalankan migrasi di production, operator wajib membuat dan memverifikasi backup yang dapat direstore, menyimpan lokasinya di catatan rilis, lalu baru melanjutkan deployment. Tidak ada backup yang diklaim dilakukan dari environment ini.
-   [x] Migration dan rollback plan reviewed: migration `2026_08_15_000100_extend_village_infographic_items_for_statistics` sudah berstatus `Ran`, hanya menambah kolom nullable serta index pada `up()`, dan `down()` menghapus index/kolom tersebut. Rollback production tidak boleh dijalankan otomatis karena akan menghapus nilai statistik yang telah diisi setelah migrasi.

## Definition of Done

Fitur dianggap selesai jika database/model, validation, authorization,
CRUD, frontend, filter, PDF, Excel, UI/UX, test, security,
documentation, dan regression check sudah diverifikasi.

> **Prinsip:** Extend, don't replace. Gunakan source-of-truth existing
> sebelum membuat model baru.

## Phase Notes

- Phase 0 baseline evidence is documented in `docs/SANUR_KAJA_DATA_MAPPING.md`.
- Phase 1 Sanur Kaja dataset mapping is documented in `docs/SANUR_KAJA_DATA_MAPPING.md`.
- Phase 2 recommended direction: extend `village_infographic_items` minimally for aggregate non-personal indicators before considering any new model/table.

## Environment Notes

- PDF logo desa dinamis sudah disiapkan secara conditional; render logo membutuhkan ekstensi PHP GD agar DomPDF dapat memproses image raster.
- Release audit 25 Agustus 2026: `StatisticsReportTest` (8 test/71 assertion), `VillageInfographicItemCrudTest` (3 test/26 assertion), dan `VillageIdentityTest` (2 test/16 assertion) lulus. Route statistik dan status migration telah diverifikasi. Release production masih diblokir oleh ekstensi PHP GD/ZIP dan prosedur backup database target.
