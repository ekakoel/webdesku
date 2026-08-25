# Sanur Kaja Statistics Data Mapping

Dokumen ini adalah hasil Phase 0 dan Phase 1 untuk roadmap statistik desa. File referensi yang dianalisis:

```text
docs/referances/SANUR_KAJA_STATISTIK.xlsx
```

Catatan path: brief menyebut `docs/references/`, tetapi lokasi aktual repository saat audit adalah `docs/referances/`.

Workbook ini adalah referensi coverage/output statistik, bukan database schema. Mapping menggunakan prioritas:

```text
USE EXISTING -> CALCULATE -> EXTEND EXISTING -> NEW DATA MODEL
```

---

## Phase 0 Baseline

### Baseline Terverifikasi

- Routes statistik tersedia: `statistik` dan `statistik.pdf` di `routes/web.php`.
- `StatisticsService` sudah menjadi sumber dataset untuk frontend statistik dan PDF.
- Validasi periode ada di `StatisticsPeriodRequest` dengan aturan `end_year >= start_year`.
- Frontend statistik ada di `resources/views/web/statistics/index.blade.php`.
- PDF statistik ada di `resources/views/pdf/statistics-report.blade.php`.
- CRUD admin tersedia untuk:
  - `VillagePopulation`
  - `VillagePopulationStat`
  - `VillageAsset`
  - `VillageLandUseArea`
  - `VillageInfographicItem`
  - APBDes item/document
  - transparency item/document
- Export Excel existing baru terdeteksi untuk laporan pengajuan layanan admin, belum untuk statistik publik.
- Identitas publik runtime memakai `VillageIdentity`, tetapi navbar masih memiliki fallback hardcoded lama dan sudah diperbaiki dalam fase ini.

### Baseline Test

Command yang sudah dijalankan:

```bash
php artisan test --filter=StatisticsReportTest
php artisan test --filter=VillageIdentityTest
```

Hasil:

- `StatisticsReportTest`: PASS, 8 tests, 71 assertions.
- `VillageIdentityTest`: PASS, 2 tests, 16 assertions.
- `VillageInfographicItemCrudTest`: PASS, 3 tests, 26 assertions.

---

## Workbook Structure

Workbook memiliki 10 worksheet:

1. `Data`
2. `I. Geografi dan Iklim`
3. `II. Kependudukan`
4. `III. Infrastruktur`
5. `IV. Ekonomi`
6. `V. Pemerintah Desa`
7. `VI. Kesehatan`
8. `SDGs Desa`
9. `Daftar Data full`
10. `Pembagian`

Worksheet utama yang berisi tabel statistik siap-mapping:

- `I. Geografi dan Iklim`
- `II. Kependudukan`
- `III. Infrastruktur`
- `IV. Ekonomi`
- `V. Pemerintah Desa`
- `VI. Kesehatan`

Worksheet `Data`, `Daftar Data full`, dan `Pembagian` berfungsi sebagai indeks/registry referensi data.

---

## Dataset Mapping Matrix

| Dataset | XLSX Worksheet | XLSX Field/Section | Existing Webdesku Source | Model/Table | Can Be Calculated? | Needs Extension? | Needs New Model? | Historical/Current | Unit | Public/Private | Export Requirement | Recommendation |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Luas wilayah | I. Geografi dan Iklim | Luas Wilayah 2022-2024 | Existing | `village_land_use_areas` or `villages.area_km2` | Partial | No | No | Historical if using land use rows | Ha/Km2 | Public | Web/PDF/Excel | Use `village_land_use_areas` for historical/label detail; use `villages.area_km2` for current summary. |
| Ketinggian tanah | I. Geografi dan Iklim | Ketinggian Tanah | No clean existing source | none | No | Yes | No | Historical/current | Mdpl | Public | Web/PDF/Excel | Store as aggregate indicator by extending `village_infographic_items` with year/source/notes. |
| Curah hujan | I. Geografi dan Iklim | Curah Hujan | No clean existing source | none | No | Yes | No | Historical | mm/tahun | Public | Web/PDF/Excel | Store as aggregate indicator in extended `village_infographic_items`; do not create climate table yet. |
| Suhu udara rata-rata | I. Geografi dan Iklim | Suhu Udara Rata-Rata | No clean existing source | none | No | Yes | No | Historical | derajat celcius | Public | Web/PDF/Excel | Store as aggregate indicator in extended `village_infographic_items`. |
| Jumlah keluarga | II. Kependudukan | Tabel 2.1 | Existing | `village_populations.households` | No | No | No | Historical | KK | Public | Web/PDF/Excel | Use `VillagePopulation`. |
| Penduduk laki-laki/perempuan/total | II. Kependudukan | Tabel 2.2 | Existing | `village_populations.male/female`, `total()` | Yes | No | No | Historical | jiwa | Public | Web/PDF/Excel | Use `VillagePopulation`; total calculated from male+female or fallback population summary. |
| Rasio jenis kelamin | II. Kependudukan | Tabel 2.3 | Calculated | `village_populations` | Yes | No | No | Historical | rasio | Public | Web/PDF/Excel | Calculate male/female*100 in `StatisticsService`; do not store duplicate aggregate. |
| Penduduk menurut dusun dan jenis kelamin | II. Kependudukan | Tabel 2.4 | No existing source | none | No | No | Yes | Historical | jiwa/KK | Public aggregate | Excel/PDF if available | Requires new domain model if roadmap demands dusun-level statistics. Do not force into `VillagePopulationStat` because it has no dusun dimension. |
| Agama/kepercayaan | II. Kependudukan | Tabel 2.5 | Existing | `village_population_stats` | No | No | No | Historical | jiwa | Public | Web/PDF/Excel | Use category `agama`. |
| Kewarganegaraan menurut jenis kelamin | II. Kependudukan | Tabel 2.6 | No clean existing source | none | No | No | Yes | Historical | jiwa | Public aggregate | Excel/PDF if available | Requires dataset with dimension citizenship+gender if required. |
| Pendidikan tertinggi | II. Kependudukan | Tabel 2.7 | Existing | `village_population_stats` | No | No | No | Historical | jiwa | Public | Web/PDF/Excel | Use category `pendidikan`. |
| Pekerjaan | II. Kependudukan | Tabel 2.8 | Existing | `village_population_stats` | No | No | No | Historical | jiwa | Public | Web/PDF/Excel | Use category `pekerjaan`. |
| Kelompok umur | II. Kependudukan | Tabel 2.9 | Existing | `village_population_stats` | No | No | No | Historical | jiwa | Public | Web/PDF/Excel | Use category `umur`. |
| Keluarga menurut jenis kelamin kepala keluarga | II. Kependudukan | Tabel 2.10 | No clean existing source | none | No | No | Yes | Historical | KK | Public aggregate | Excel/PDF if available | Requires family-head-gender aggregate model if required. |
| Penduduk menurut suku/etnis | II. Kependudukan | Tabel 2.11 | No clean existing source | none | No | No | Yes | Historical/current | jiwa | Public aggregate | Excel/PDF if available | Requires population category extension/new category if only label+value; if year needed can extend `VillagePopulationStat` category list. |
| Sarana pendidikan | III. Infrastruktur | Tabel 3.1 | Existing/calculated | `village_assets` | Yes, if assets entered per facility | Partial | No | Current; historical count may need extension | unit | Public | Web/PDF/Excel | Count published `VillageAsset` type `pendidikan`; for historical totals use extended `village_infographic_items` if assets are only current. |
| Sarana kesehatan | III. Infrastruktur | Tabel 3.2 | Existing/calculated | `village_assets` | Yes, if assets entered per facility | Partial | No | Current; historical count may need extension | unit | Public | Web/PDF/Excel | Count published `VillageAsset` type `kesehatan`; historical totals can use extended indicators. |
| Tempat ibadah | III. Infrastruktur | Tabel 3.3 | Existing/calculated | `village_assets` | Yes, if categorized | Partial | No | Current; historical count may need extension | unit | Public | Web/PDF/Excel | Existing `VillageAsset` lacks specific worship type; use `subcategory` for Pura/Masjid/Gereja/Vihara or extended indicators for historical. |
| Lembaga ekonomi | IV. Ekonomi | Tabel 4.1 | Partial | `village_assets` / `village_infographic_items` | Partial | Yes | No | Historical | unit | Public | Web/PDF/Excel | Use `VillageAsset` for current facilities; extended indicators for historical categories such as bank/BUMDes. |
| Penerima JPS | IV. Ekonomi | Tabel 4.2 | No clean existing source | none | No | Yes | No | Historical | KK | Public aggregate | Web/PDF/Excel | Store as aggregate social/economy indicator in extended `village_infographic_items`. |
| Koperasi menurut jenis | IV. Ekonomi | Tabel 4.3 | Partial | `village_assets` / `village_infographic_items` | Partial | Yes | No | Historical | unit | Public | Web/PDF/Excel | Current facilities may be assets; historical count should use extended indicators. |
| Perangkat desa | V. Pemerintah Desa | Tabel 5.1 | Existing/calculated | `village_officials` | Yes, current | Partial | No | Current; historical may need extension | jiwa | Public | Web/PDF/Excel | Count current officials by position/unit; historical count can use extended indicators if needed. |
| APBDes | V. Pemerintah Desa | Tabel 5.2 | Existing | `village_apbdes_items` | Yes | No | No | Historical | Rupiah | Public | Web/PDF/Excel | Use existing APBDes models. |
| Produk hukum | V. Pemerintah Desa | Tabel 5.3 | Existing/calculated | `announcements` type regulation | Partial | Yes | No | Historical | dokumen | Public | Web/PDF/Excel | Current detail via `Announcement`; yearly aggregate by type may be calculated if dates/types available, otherwise extended indicators. |
| Penyakit dan jumlah penderita | VI. Kesehatan | Tabel VI | No clean existing source | none | No | Yes | No | Current/year 2024 | orang | Public aggregate only | Excel/PDF if available | Store aggregate disease indicators in extended `village_infographic_items`; do not store individual health records. |
| Dirawat di fasilitas | VI. Kesehatan | Tabel VI | No clean existing source | none | No | Yes | No | Current/year 2024 | orang | Public aggregate only | Excel/PDF if available | Store aggregate health indicator notes/value if needed; keep public aggregate only. |
| SDGs Desa | SDGs Desa | empty/no sample rows detected | Not available | none | No | No | TBD | TBD | TBD | TBD | TBD | Keep TODO until workbook contains usable data. |

---

## Phase 1 Implementation Decision

### Use Existing

- `VillagePopulation` for family count, male/female/total population.
- `VillagePopulationStat` for age, education, occupation, religion, marital status.
- `VillageLandUseArea` for land-use/area rows.
- `VillageAsset` for current facilities/assets/infrastructure.
- `VillageOfficial` for current village officials.
- APBDes models for APBDes.
- Announcement/regulation models for legal product details where available.

### Calculate

- Total population from male+female.
- Sex ratio from male/female.
- Facility totals from published `VillageAsset` when facility rows are entered as assets.
- Current official totals from `VillageOfficial`.
- Periodic legal product counts from `Announcement` if type/date data is available.

### Extend Existing

Extend `VillageInfographicItem` instead of creating many new tables for aggregate non-personal indicators that are not represented cleanly by existing models:

- geography/climate indicators,
- historical infrastructure aggregate counts,
- economic institution/JPS/cooperative indicators,
- health/social aggregate indicators,
- current/historical miscellaneous indicators.

Recommended minimal fields:

- `year` nullable integer,
- `source` nullable string,
- `notes` nullable text.

Recommended category additions:

- `geografi_iklim`
- `infrastruktur`
- `ekonomi`
- `pemerintahan`
- `kesehatan_sosial`

This follows the roadmap priority because it extends an existing aggregate infographic model and avoids table-per-sheet design.

### New Model/Table Required

Only create new domain tables if the project must support multi-dimensional historical data that cannot be represented by existing source or extended indicators:

- dusun-level population by year/gender/KK,
- citizenship by gender and year,
- family-head gender by year,
- complex SDGs datasets if later supplied.

These are not implemented in Phase 1 because the mapping alone does not prove the business requirement for full CRUD/detail dimensions.

---

## Recommended Next Implementation Step

Proceed to Phase 2 with the smallest safe extension:

1. Add nullable `year`, `source`, and `notes` to `village_infographic_items`.
2. Expand `VillageInfographicItem::CATEGORIES` for mapped aggregate categories.
3. Update admin CRUD form/list validation and labels.
4. Extend `StatisticsService` to expose available aggregate indicators grouped by category/year.
5. Add Excel export using the same `StatisticsService` report payload.

Do not create new tables during this step.

---

## Statistics Data Lineage Audit

Audit ini memverifikasi alur data statistik publik:

```text
Database -> StatisticsService::report() -> /statistik -> PDF -> Excel
```

### Snapshot Database Saat Audit

Command audit lokal menunjukkan:

- `village_populations`: 3 rows.
- `village_population_stats`: 23 rows.
- `village_assets`: 7 rows.
- `village_land_use_areas`: 0 rows.
- `village_infographic_items`: 3 rows.
- `complaints`: 0 rows.
- `service_requests`: 0 rows.
- `news`: 3 rows.
- `agendas`: 2 rows.
- Duplikat indikator `village_id + category + year + title`: tidak ditemukan.

### Source of Truth Statistik

Semua output statistik publik wajib memakai payload dari `App\Services\StatisticsService::report()`.

| Output | Field Service | Source DB | Filter | Catatan |
|---|---|---|---|---|
| KPI Total Penduduk | `population.total` | `village_populations.male/female` | `year` dalam rentang | Jika tidak ada snapshot pada periode, tampil `Tidak tersedia`, bukan `0`. |
| Komposisi Penduduk | `population.male/female/total` | `village_populations` | `year` dalam rentang | Chart hanya tampil saat `population.available = true`. |
| Pengajuan Layanan | `periodicCounts.service_requests` | `service_requests.submitted_at` | tanggal dalam rentang tahun | Tidak menampilkan NIK/telepon/nama pemohon pada public export. |
| Aduan | `periodicCounts.complaints` | `complaints.submitted_at` | tanggal dalam rentang tahun | Status/kategori dihitung agregat saja. |
| Berita | `periodicCounts.news` | `news.published_at` | tanggal dalam rentang tahun | Hanya published. |
| Agenda | `periodicCounts.agendas` | `agendas.start_at` | tanggal dalam rentang tahun | Hanya published. |
| Pengumuman | `periodicCounts.announcements` | `announcements.published_at` | tanggal dalam rentang tahun | Hanya published. |
| Galeri | `periodicCounts.galleries` | `galleries.published_at` | tanggal dalam rentang tahun | Hanya published. |
| Layanan Aktif | `masterCounts.services` | `services` | data terkini | Master count tidak dipaksa mengikuti periode. |
| Total Aset Desa | `masterCounts.assets` | `village_assets` | data terkini | Hanya published. |
| Komposisi Aset | `assetTypeStats` | `village_assets.type` | data terkini | Dikelompokkan berdasarkan `VillageAsset::typeOptions()`. |
| Tren Bulanan/Tahunan | `trend` | reuse `periodicCounts()` | bulan/tahun aktif | Tidak membuat query baru di Blade. |
| Indikator agregat | `infographicIndicators` | `village_infographic_items` | `year` null atau dalam rentang | Hanya published; menyertakan unit/source/notes. |

### CRUD Audit Indikator Agregat

- Admin CRUD `VillageInfographicItemController` membuat data ke `village_infographic_items`.
- Field yang boleh mass assignment dibatasi di `VillageInfographicItem::$fillable`.
- Store menetapkan `village_id` dari data desa aktif di backend, bukan dari request.
- Publish/unpublish mengatur `published_at`.
- Validasi mencegah duplikat `village_id + category + year/null + title`.
- Data draft/unpublished tidak masuk ke service, frontend, PDF, atau Excel.

### Regression Coverage

Test yang mengunci audit lineage:

- `StatisticsReportTest::test_statistics_data_lineage_matches_database_service_frontend_and_excel`
- `StatisticsReportTest::test_statistics_page_displays_empty_period_state`
- `StatisticsReportTest::test_statistics_excel_uses_selected_filter_and_public_identity`
- `VillageInfographicItemCrudTest::test_admin_indicator_crud_rejects_duplicate_category_year_and_title`
