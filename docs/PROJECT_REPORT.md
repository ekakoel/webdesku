# Webdesku Project Understanding Report

## 1. Report Scope

Dokumen ini merangkum pemahaman arsitektur dan aturan pengembangan Webdesku saat ini berdasarkan:

- `AGENTS.MD`
- `docs/BLUEPRINT_WEBDESKU.md`
- `docs/data-governance-matrix.md`
- `docs/frontend-standardization.md`
- struktur Laravel application saat ini

Dokumen ini menggantikan interpretasi lama yang mengarahkan Webdesku sebagai multi-tenant SaaS.

## 2. Executive Summary

Webdesku adalah aplikasi Laravel 12 untuk sistem informasi desa dan website desa.

Nama **Webdesku** dipertahankan sebagai identitas internal repository/codebase. Pada runtime publik, identitas aplikasi harus tampil sebagai identitas desa dari data/config deployment, bukan sebagai brand Webdesku.

Arsitektur operasional saat ini:

```text
One Webdesku deployment
        ↓
One village
        ↓
Reusable codebase for other deployments
```

Webdesku **bukan** multi-tenant SaaS. Aplikasi tidak boleh dikembangkan menuju tenant switching, subdomain tenancy, runtime village selection, tenant-specific authentication, atau tenant-aware routing kecuali ada instruksi eksplisit.

Kode tetap harus reusable. Desa lain dapat memakai codebase yang sama melalui deployment independen dengan konfigurasi, database, storage, dan environment masing-masing.

## 3. Product Vision

Webdesku bertujuan menyediakan:

- website informasi desa,
- kanal transparansi dan publikasi APBDes,
- berita, agenda, pengumuman, galeri, dan peraturan desa,
- layanan publik digital,
- pengaduan masyarakat,
- statistik dan infografis desa,
- admin panel yang mudah digunakan aparat desa.

Fokus produk adalah stabilitas, keamanan, maintainability, konfigurasi identitas desa, dan kemudahan reuse untuk deployment desa lain.

## 3.1 Runtime Public Identity

- `Webdesku` tidak boleh muncul sebagai nama website publik, title browser, footer, metadata PDF, atau label sumber data yang dilihat warga.
- Nama website publik berasal dari data desa, terutama `Village::name`.
- Homepage menggunakan title nama desa saja.
- Subhalaman menggunakan pola `[Nama Halaman] | [Nama Desa]`.
- Fallback publik ketika data desa belum tersedia adalah `Pemerintah Desa`.
- Implementasi title/branding harus terpusat agar reusable untuk deployment desa lain dan tidak menyebar hardcoded identity di Blade/PHP.

## 4. Technology Stack

### Backend

- PHP `^8.2`
- Laravel `^12.0`
- Laravel Breeze authentication
- Eloquent ORM
- Laravel migrations
- Database-backed sessions/cache/queues sesuai konfigurasi environment

### Frontend

- Blade templates
- Vite
- Tailwind CSS
- Alpine.js
- Axios
- Custom public stylesheet di `resources/css/web.css`

### Supporting Packages

- `barryvdh/laravel-dompdf` untuk PDF
- `maatwebsite/excel` untuk Excel export
- `simplesoftwareio/simple-qrcode` untuk QR Code
- `spatie/laravel-sitemap` untuk sitemap

Gunakan package yang sudah ada sebelum menambah alternatif baru.

## 5. Current Application Shape

Aplikasi memiliki:

- public website routes,
- admin routes,
- super-admin module settings,
- content CRUD,
- service request workflow,
- complaint workflow,
- transparency and APBDes data,
- infographic and statistic views,
- annual public statistics filtering and PDF report export,
- map, QR, PDF, Excel, sitemap, and Instagram integration foundations.

Root `README.md` sekarang harus diperlakukan sebagai dokumentasi onboarding project, bukan README Laravel generik.

## 6. Current Architecture

Konsep arsitektur:

```text
                    WEBDESKU
                       │
          ┌────────────┴────────────┐
          │                         │
     PUBLIC WEBSITE             ADMIN PANEL
          │                         │
          └────────────┬────────────┘
                       │
                 APPLICATION
                       │
        ┌──────────────┼──────────────┐
        │              │              │
   Controllers      Services       Policies
        │              │              │
        └──────────────┼──────────────┘
                       │
                    Eloquent
                       │
                    Database
```

Not every feature must use every layer. Controllers should stay thin; services are justified only for meaningful business workflows.

## 7. Public Layer

Public routes are centered on `HomeController` and Blade views under `resources/views/web`.

Core public capabilities include:

- homepage,
- profile pages,
- news list and detail,
- agenda list and detail,
- services, submissions, status checks, and receipts,
- complaints and status checks,
- statistics,
- annual statistics period filtering and PDF download,
- transparency/APBDes,
- infographics,
- gallery,
- announcements,
- regulations,
- contact.

Optional public modules may be hidden/disabled through module settings. Module settings are application-level, not tenant-level.

## 8. Administration Layer

The `/admin` area is for authorized village operators in the current single-village deployment.

Admin capabilities include CRUD and management flows for:

- news,
- agendas,
- announcements,
- regulations,
- galleries,
- sliders,
- services,
- service requests,
- complaints,
- village assets,
- population data,
- population category statistics,
- land-use areas,
- APBDes items and documents,
- transparency items and documents,
- profile pages,
- officials,
- village head messages,
- village settings,
- village map,
- data lineage.

Admin UI must remain simple, clear, responsive, and safe for non-technical operators.

## 9. Village Configuration Model

The `Village` model may be used to store the identity/configuration of the current deployment.

It may contain:

- name,
- slug if still needed by legacy code,
- logo,
- address,
- phone,
- email,
- website,
- profile fields,
- head data,
- statistics summary,
- coordinates,
- boundary,
- social media and integration settings.

Important rule:

> `Village` is not a tenant boundary.

If legacy relationships still use `village_id`, treat them as existing implementation details. Do not introduce new tenant semantics around them unless explicitly requested.

## 10. Legacy Multi-Tenant Remnants

The repository may still contain legacy code that originated from older multi-village assumptions, for example:

- `IdentifyVillage` middleware,
- `currentVillage` container binding,
- `village_id` columns,
- fallback to the first `Village`,
- slug/subdomain-oriented village lookup,
- documentation references to SaaS readiness.

These are not automatically target architecture. They must be inspected before any refactor.

Safe handling rule:

1. Do not blindly preserve legacy multi-tenant architecture.
2. Do not blindly delete legacy code.
3. Search all references first.
4. Understand database and view dependencies.
5. Simplify only when the scope is clear and tests/verification can cover the behavior.

## 11. Data Governance

`docs/data-governance-matrix.md` remains the source of truth for public data sources.

Key rules:

- public data must come from the official source tables,
- do not use synthetic/manual fallback values for statistics,
- show informative empty states when data is unavailable,
- shared calculations should stay centralized to avoid inconsistent public pages.

Important helpers currently documented:

- `HomeController::resolveApbdesDataset()`
- `HomeController::resolveLatestPopulationStatsByCategory()`
- `App\Services\StatisticsService` for public statistics and PDF report aggregation

Any refactor must preserve their data-governance behavior or update this documentation deliberately.

Public statistics use `start_year` and `end_year` query parameters. Periodic statistics are filtered by the semantically correct date field for each table, while snapshot/master statistics remain clearly labeled as current/snapshot data. The PDF endpoint reuses the same service result as the frontend and must not claim automatic official/legal status without village verification and approval.

## 12. Frontend Standards

`docs/frontend-standardization.md` defines public frontend layout rules.

For public pages except the homepage, use:

- `section-wrap`,
- `container-grid`,
- `page-hero section-card`,
- `page-section-stack`,
- `container-grid--narrow` or `container-grid--compact` when needed.

Use reusable controls:

- `form-control`,
- `form-control-button`,
- global radius/text tokens.

Avoid one-off inline layout and styling unless truly justified.

## 13. Documentation-to-Code Alignment

Current alignment:

- Laravel 12/PHP 8.2 stack is confirmed.
- Public and admin layers are separated.
- Many modules are already implemented.
- Data governance docs align with public transparency/statistic goals.
- Frontend standardization docs provide current UI rules.
- `AGENTS.MD` now establishes single-village reusable deployment as the current architecture.

Known documentation history:

- Older blueprint/report language described multi-village SaaS readiness.
- That direction has been superseded.
- Future documentation should not present SaaS/multi-tenancy as the default goal.

## 14. Current Risks

Primary risks to manage:

- legacy multi-tenant code may confuse future development,
- data-governance violations can happen if fallback/synthetic statistics are reintroduced,
- admin CRUD can accumulate duplicated validation/business logic,
- documentation can drift from implementation,
- tests are still likely lighter than the business-critical workflows require.

These risks should be addressed incrementally and without broad unrelated refactors.

## 15. Priority Recommendations

### Priority 1: Clarify Single-Village Architecture

1. Keep documentation consistent with single-village deployment.
2. Mark legacy multi-tenant code as implementation debt, not target architecture.
3. Avoid adding tenant resolver/session/subdomain behavior.
4. Centralize village identity/configuration access where practical.

### Priority 2: Preserve Data Governance

1. Avoid synthetic statistic values.
2. Keep APBDes and population calculations consistent across pages.
3. Add tests for important data-source behavior when modifying those modules.
4. Maintain clear empty states when official data is missing.

### Priority 3: Improve Maintainability

1. Keep controllers thin.
2. Extract services only for meaningful workflows.
3. Use Form Requests for non-trivial validation.
4. Avoid duplicate query and validation logic.
5. Keep Blade templates presentational.

### Priority 4: Expand Business Tests

Recommended tests:

- service request lifecycle,
- complaint submission/status flow,
- publication visibility,
- admin authorization,
- APBDes calculations,
- population/statistic source precedence,
- module enable/disable behavior,
- file upload validation.

### Priority 5: Project Onboarding and Operations

Improve documentation for:

- installation,
- environment variables,
- storage link,
- queue/scheduler,
- seeding village identity,
- backups,
- external integrations,
- deployment checklist.

## 16. Suggested Delivery Roadmap

### Phase A: Documentation and Safety

- Keep docs synchronized with single-village architecture.
- Update README onboarding.
- Identify remaining legacy multi-tenant references.

### Phase B: Reliability

- Add tests for service, complaint, transparency, and statistic workflows.
- Strengthen validation and authorization where gaps are discovered.

### Phase C: Maintainability

- Refactor large controllers carefully only when there is clear value.
- Centralize repeated village configuration access if it reduces confusion.

### Phase D: Reusable Deployment Readiness

- Document deployment for another village.
- Ensure village identity/config values are not hardcoded.
- Keep configuration and seed data easy to adapt per deployment.

## 17. Overall Assessment

Webdesku is a feature-rich Laravel monolith for a single village deployment, with reusable foundations for other independent village deployments.

The correct direction is not to complete multi-tenancy. The correct direction is to:

- simplify legacy assumptions safely,
- preserve current business features,
- keep village identity configurable,
- strengthen validation, authorization, and tests,
- maintain clear documentation.

Target state:

```text
Stable single-village Webdesku deployment
        +
Reusable codebase for independent deployments
        -
Runtime multi-tenancy
```
