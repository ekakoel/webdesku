# Webdesku --- Codex Rules

## Sanur Kaja XLSX Reference Rule

The Sanur Kaja XLSX file is a reference dataset/report structure.

Codex MUST use it during Phase 1 to identify:
- dataset categories
- fields
- reporting structure
- historical/current data
- units
- expected report coverage

The XLSX MUST NOT be copied directly into the database schema.

For every dataset, Codex MUST determine:

1. Existing source-of-truth
2. Calculated from existing data
3. Existing model requires extension
4. New model/table genuinely required

Codex MUST NOT create a new table merely because a sheet or column exists in the XLSX.

The XLSX is a reference for DATA COVERAGE and REPORT STRUCTURE,
not a database schema.

## 1. Prioritas Sumber Kebenaran

Urutan wajib: 1. Instruksi developer/user saat ini. 2. Implementasi
codebase. 3. Dokumentasi current `/docs`. 4. `AGENTS.MD`. 5.
Migration/database aktual. 6. Routes aktual. 7. Tests. 8.
Blueprint/proposal historis. 9. Asumsi umum.

Jangan mengubah implementasi hanya karena blueprint lama berbeda.

## 2. Identitas Publik

`Webdesku` adalah nama internal repository/codebase.

Dilarang menampilkan Webdesku pada: - browser title, - header/footer, -
public navigation, - PDF, - Excel public report, - SEO metadata, - Open
Graph, - public branding.

Gunakan `Village`/`VillageIdentity` untuk: - nama desa, - alamat, -
kontak, - logo, - kepala desa, - wilayah.

Format: `[Nama Desa]` untuk homepage. `[Nama Halaman] | [Nama Desa]`
untuk subpage. Fallback: `Pemerintah Desa`.

Jangan hardcode identitas desa.

## 3. Single-Village Architecture

Satu deployment = satu desa.

Dilarang menambahkan: - tenant switching, - village switching, - tenant
resolver, - subdomain tenancy, - runtime multi-village, - session
current-village switching.

Reusable berarti deployment terpisah untuk desa lain.

Jika task membutuhkan multi-tenancy: STOP dan minta keputusan developer.

## 4. Workflow Sebelum Coding

Wajib: 1. Baca `AGENTS.MD`. 2. Baca `CHATGPT_PROJECT_CONTEXT.md`. 3.
Baca docs terkait. 4. Inspect route. 5. Inspect controller. 6. Inspect
model. 7. Inspect migration. 8. Inspect request validation. 9. Inspect
view. 10. Inspect service. 11. Inspect test. 12. Pahami flow end-to-end.

Sebelum implementasi tulis:

``` text
Files affected:
Database:
Model:
Controller:
Service:
View:
Route:
Test:
Docs:
```

## 5. Extend, Don't Replace

Urutan:
`REUSE EXISTING → EXTEND EXISTING → CREATE NEW ONLY IF NECESSARY`

Dilarang membuat duplicate: - model, - controller, - route, - service, -
query, - source data.

Jangan refactor besar tanpa alasan kuat.

## 6. Source of Truth

Contoh source: - Population → `VillagePopulation`. - Population
categories → `VillagePopulationStat`. - Assets → `VillageAsset`. - Land
use → `VillageLandUseArea`. - APBDes → APBDes models. - Transparency →
transparency models. - Officials → `VillageOfficial`.

Jika aggregate dapat dihitung dari source existing, hitung; jangan
simpan salinan angka.

## 7. Statistik

Bedakan: - current, - historical, - published, - unavailable.

`0` bukan `Data belum tersedia`.

Data historical harus memiliki tahun.

Data public harus berupa agregat. Jangan expose data pribadi/individual
yang tidak diperlukan.

## 8. Data Sanur Kaja

Workbook Sanur Kaja adalah referensi struktur/output, bukan schema
database.

Untuk setiap dataset:
`USE EXISTING → CALCULATE → EXTEND EXISTING → NEW DATA MODEL`.

Jangan membuat satu tabel per sheet Excel.

## 9. Database

-   Gunakan Laravel migration.
-   Jangan edit migration lama yang sudah digunakan production.
-   Jangan migration destructive tanpa instruksi.
-   Pertahankan `village_id` existing.
-   Tambahkan foreign key/index/unique constraint sesuai kebutuhan.
-   Jangan membuat generic/EAV table jika model domain yang jelas sudah
    cukup.

## 10. Model

-   Eloquent.
-   Relationship eksplisit.
-   `$fillable` aman.
-   Cast benar.
-   Logic berat di service, bukan model.

## 11. Controller

Controller:
`request → validation → authorization → model/service → response`.

Jangan menaruh query/business logic kompleks yang berulang di
controller.

## 12. Validation

Semua input divalidasi: - type, - required/nullable, - numeric, -
range, - year, - enum/category, - relationship, - file MIME/size.

Filter tahun wajib: `start_year <= end_year`.

## 13. Authorization

Admin CRUD tetap memakai middleware existing: `auth + verified + role`.

Jangan bypass authorization atau menggunakan hidden field sebagai
security.

## 14. Frontend

Public UI: - bahasa Indonesia sederhana, - responsive, - accessible, -
empty/error state, - tidak expose internal implementation.

Jangan gunakan istilah teknis seperti `dataset_id`, `EAV`, `tenant` pada
public UI.

## 15. UI/UX

Jangan redesign besar.

Prioritas:
`existing design → navigation → information hierarchy → usability`.

Dilarang tanpa instruksi: - mengganti theme, - mengganti color system, -
mengganti typography, - mengganti layout admin, - mengganti design
system.

Ikuti `docs/frontend-standardization.md`: - `section-wrap`, -
`container-grid`, - `page-hero`, - `section-card`, -
`page-section-stack`, - `form-control`, - `form-control-button`.

Hindari inline spacing/max-width acak.

## 16. StatisticsService

Pipeline wajib: `Database → StatisticsService → Web/PDF/Excel`.

Jangan membuat query berbeda untuk Web, PDF, dan Excel.

## 17. PDF

PDF harus: - memakai identitas desa, - memiliki judul/periode/tanggal, -
memiliki source, - memiliki tabel yang terbaca, - memiliki footer/page
number, - tidak menampilkan Webdesku, - menggunakan dataset
`StatisticsService`.

Jangan mengklaim status legal/ditandatangani tanpa kewenangan resmi.

## 18. Excel

Excel: - mengikuti filter aktif, - menggunakan dataset yang sama, -
memiliki unit/tahun/source, - membedakan zero vs unavailable, - tidak
memuat data pribadi pada public export, - menggunakan package existing.

## 19. Security

Periksa: - CSRF, - XSS, - SQL injection, - mass assignment, - IDOR, -
authorization, - file upload, - path traversal, - sensitive data
exposure, - unsafe redirect, - session security.

## 20. Testing

Jangan pernah mengatakan test passed jika belum dijalankan.

Urutan: 1. test paling spesifik, 2. feature test, 3. full suite jika
perubahan luas, 4. review failure, 5. `git diff`, 6. `git status`.

Contoh:

``` bash
php artisan test --filter=StatisticsReportTest
php artisan test --filter=VillageIdentityTest
php artisan test
```

Pint boleh dibatasi pada file terkait jika repository memiliki legacy
style issues.

## 21. Documentation

Jika menyentuh database/architecture/route/module/business
rule/statistics/export/security/UI convention: - update docs terkait, -
update project context bila perlu, - dokumentasikan source-of-truth.

Jangan mengubah docs agar fitur terlihat selesai padahal belum.

## 22. Git Safety

Sebelum:

``` bash
git status
```

Sesudah:

``` bash
git diff
git status
```

Jangan: - `git reset --hard`, - `git clean -fd`, - destructive database
command, tanpa instruksi eksplisit.

## 23. Stop Conditions

STOP dan minta keputusan developer jika: - membutuhkan multi-tenancy, -
membutuhkan penghapusan data, - source-of-truth bertentangan, -
migration destructive, - redesign besar, - data pribadi sensitif, -
credential/API secret, - business rule pemerintahan tidak jelas, -
status legal/resmi laporan tidak jelas.

## 24. Definition of Done

Task hanya DONE jika:
`implementation + validation + authorization + UI + testing + documentation + security review`.

Kode yang hanya "jalan" tetapi belum diverifikasi bukan DONE.

## 25. Prinsip Akhir

``` text
Stability > feature speed
Existing source > duplicate data
Minimal change > large refactor
Consistent UX > new visual design
Verified result > assumption
```

Instruksi inti: \> Webdesku adalah Laravel 12 single-village application
yang reusable melalui deployment terpisah. Gunakan source-of-truth
existing terlebih dahulu. Jangan ubah menjadi multi-tenant. Pertahankan
UI/UX existing. Semua statistik, PDF, dan Excel harus berasal dari
dataset yang konsisten. Validation, authorization, security, testing,
documentation, dan regression check adalah bagian dari implementasi.
