# Webdesku Project Understanding Report

## 1. Report Scope

This report consolidates the project intent and implementation rules documented in:

- `docs/BLUEPRINT_WEBDESKU.md`
- `docs/data-governance-matrix.md`
- `docs/frontend-standardization.md`

The documentation was cross-checked against the current Laravel application structure, routes, controllers, models, migrations, Blade views, CSS, middleware, dependencies, and tests.

## 2. Executive Summary

Webdesku is a Laravel-based village government website designed to provide public information, digital public services, administrative transparency, and reusable multi-village deployment.

The current repository is no longer only a basic blueprint implementation. It already contains:

- A comprehensive public website.
- A role-protected administration area.
- Public service request and complaint workflows.
- Village profile, news, agenda, announcement, gallery, regulation, transparency, and infographic modules.
- Data governance helpers that centralize population and APBDes calculations.
- Subdomain-based village identification.
- Runtime module enable/disable controls.
- PDF, Excel, QR code, sitemap, map, and Instagram integration foundations.

The main architectural weakness is that public tenant selection and admin tenant selection are not yet aligned. Public pages resolve a village from the subdomain, while many admin controllers still use the first village in the database. Users also do not currently have a `village_id`, so the application is structurally multi-village on public content but not yet safely multi-tenant for administration.

## 3. Product Vision

The documented product goal is a modern, reusable village website that supports:

- Government transparency.
- Public information delivery.
- Digital village administration.
- Public service access.
- Multi-village deployment as a SaaS-ready platform.

The intended public navigation includes:

- Home.
- Village Profile.
- News.
- Agenda.
- Village Services.
- Transparency.
- Gallery.
- Announcements.
- Contact.

The implemented project extends that baseline with:

- Community complaints.
- Village regulations.
- Statistics.
- Infographics.
- Service request tracking and printable receipts.
- Data lineage.
- Configurable modules.
- Instagram synchronization.

## 4. Technology Stack

### Backend

- PHP `^8.2`.
- Laravel `^12.0`.
- Laravel Breeze authentication.
- Eloquent ORM and Laravel migrations.
- Database-backed sessions, cache, and queues by default.
- SQLite as the default example environment.

### Frontend

- Blade templates.
- Vite `^7`.
- Tailwind CSS.
- Alpine.js.
- Axios.
- A large custom public stylesheet in `resources/css/web.css`.

### Supporting Packages

- `barryvdh/laravel-dompdf` for PDF generation.
- `maatwebsite/excel` for spreadsheet exports.
- `simplesoftwareio/simple-qrcode` for QR codes.
- `spatie/laravel-sitemap` for sitemap support.

## 5. Current Repository Scale

At the time of review, the repository contains approximately:

- 38 controllers.
- 27 models.
- 49 migrations.
- 140 Blade views.
- 84 admin Blade views.
- 24 public website Blade views.
- 85 route declarations across web and authentication routes.
- 10 test files with 25 test methods.

These numbers show that the repository is a substantial application rather than a starter prototype.

## 6. Application Architecture

### Public Layer

Public routes are centered on `HomeController` and grouped under the `identifyVillage` middleware.

Main public capabilities:

- Homepage content aggregation.
- Village profile subpages.
- News list and detail.
- Agenda list and detail.
- Service catalogue, submissions, status checks, and receipts.
- Complaint submission and status checks.
- Village statistics.
- Transparency and APBDes.
- Infographics.
- Gallery.
- Announcements.
- Regulations and downloads.
- Contact information.

Most optional content areas are protected by module middleware, allowing disabled modules to return a 404 and disappear from the active experience.

### Administration Layer

The `/admin` area requires:

- Authentication.
- Verified email.
- The `aparat` or `super_admin` role.

It contains CRUD interfaces for nearly all public datasets, including:

- News, agendas, announcements, regulations, galleries, and sliders.
- Services and service requests.
- Complaints.
- Village assets and population data.
- Population category statistics.
- Land-use data.
- APBDes items and documents.
- Transparency items and documents.
- Profile pages, officials, and village head messages.
- Village settings and maps.
- Data lineage.

### Super Administrator Layer

The `/super-admin` area is restricted to `super_admin` and currently manages feature modules.

### Service Layer

Dedicated services support:

- BIG boundary import.
- Google Maps link resolution.
- Instagram feed synchronization.
- Village statistic synchronization.

Console commands are available for BIG boundary import and Instagram synchronization.

## 7. Multi-Village Design

### Implemented Strengths

- Public requests pass through `IdentifyVillage`.
- The subdomain prefix is matched to `villages.slug`.
- Public content queries generally filter by `village_id`.
- Most village-owned tables include a foreign key to `villages`.
- Important uniqueness constraints are tenant-aware, such as village plus slug or village plus year.
- The `Village` model exposes relationships to most tenant-owned content.

### Current Limitations

- If a subdomain is not resolved, the middleware silently selects the first village.
- Many admin controllers also select `Village::query()->first()`.
- `User` has role information but no `village_id` ownership.
- The documented roles `Admin`, `Operator`, and `Kepala Desa` are represented only as the broader `aparat` and `super_admin` roles.
- Module settings are global rather than scoped by village.

### Consequence

The public layer demonstrates multi-village data separation, but the admin layer is effectively single-village. Adding multiple villages without first correcting admin scoping could cause operators to read or modify another village's records.

## 8. Data Governance

The data governance document defines a single source of truth for each public information component.

### Official Data Sources

| Public Component | Primary Table |
|---|---|
| Village assets | `village_assets` |
| Population totals and trends | `village_populations` |
| Religion, occupation, education, marital status, and age | `village_population_stats` |
| Other infographics | `village_infographic_items` |
| Land use | `village_land_use_areas` |
| APBDes details | `village_apbdes_items` |
| APBDes documents | `village_apbdes_documents` |
| General transparency documents | `village_transparency_documents` |

### Implemented Governance Practices

- Queries are filtered to published records.
- Population category data is resolved through a shared helper.
- APBDes year selection and summary calculations use a shared helper.
- Land-use data overwrites profile-page payload data so stored profile JSON cannot replace current database facts.
- Infographic pages expose source, update-time, period, and ownership metadata.
- Empty collections and placeholder displays are used when data is unavailable.

### Governance Conflict

`HomeController::normalizedVillagePopulation()` creates an estimated 50/50 male/female split when only a total population value exists. This conflicts with the documented rule that synthetic statistical figures must not be displayed. Unknown gender values should remain unavailable instead of being inferred.

## 9. Frontend Design System

### Documented Visual Direction

- Primary color: `#0B3D91`.
- Secondary color: `#1E88E5`.
- Accent color: `#FFC107`.
- Typography: Poppins or Inter.
- Government-oriented blue visual identity.

### Standard Public Page Structure

All public pages except the homepage should use:

- `section-wrap`.
- `container-grid`.
- `page-hero section-card`.
- `page-section-stack`.
- Narrow or compact container utilities when needed.

Standard page headers contain:

- A small label.
- An `h1` title.
- A short description.
- Optional actions in `page-hero__actions`.

### Reusable Controls and Tokens

The documented and implemented CSS system includes:

- `--radius-card`.
- `--radius-control`.
- `--text-control`.
- `.form-control`.
- `.form-control-button`.
- `.container-grid--narrow`.
- `.container-grid--compact`.

The homepage is intentionally excluded from the standard inner-page layout rules.

## 10. Functional Coverage

### Content Publishing

The project provides end-to-end publishing for:

- News with authors, images, publication state, and view count.
- Agendas with dates, posters, coordinates, and map links.
- Announcements with types, media, files, and map data.
- Regulations using the announcement infrastructure.
- Galleries with thumbnails.
- Sliders.
- Village officials and head messages.

### Digital Services

The service module supports:

- Published service definitions.
- Public applications.
- Unique ticket codes and public tokens.
- Status checking.
- Printable receipts.
- SLA target configuration.
- Admin processing.
- Excel and PDF reporting.

### Community Complaints

The complaint module supports:

- Public complaint submission.
- Ticket and public-token generation.
- Status checking.
- Admin review and updates.
- Complaint responses.

### Transparency and Statistics

The project supports:

- APBDes summaries and item details.
- APBDes documents.
- General transparency data and documents.
- Population trends.
- Demographic category statistics.
- Village assets.
- Land-use figures.
- Data source and update metadata.

### External and Geographic Features

- Village coordinates and GeoJSON boundaries.
- BIG boundary import.
- Google Maps link normalization.
- Instagram feed synchronization and local post storage.

## 11. Documentation-to-Code Alignment

### Strong Alignment

- Public and admin areas are clearly separated.
- Most content tables use `village_id`.
- The blueprint public menu is implemented and extended.
- Homepage sections match the blueprint.
- APBDes and population helpers match the governance document.
- Land-use override behavior matches the documented implementation note.
- Standard frontend CSS classes and tokens exist.
- Module controls improve the blueprint's reusability goal.

### Partial Alignment

- Subdomain tenancy exists, but admin ownership is not tenant-safe.
- Role management exists, but the role model is less detailed than the blueprint.
- Sitemap support is installed, but sitemap behavior was not documented in the reviewed files.
- QR code support is installed, but the reviewed documentation does not define its complete verification flow.
- SEO is a stated direction, but no dedicated SEO governance document exists.

### Documentation Gaps

- The root README is still the generic Laravel README.
- There is no project-specific installation or deployment guide.
- There is no documented tenant provisioning process.
- There is no role and permission matrix.
- There is no API or integration configuration guide.
- There is no testing strategy for village isolation, governance, services, or complaints.
- There is no production operations guide for queues, scheduler, storage links, backups, or Instagram token rotation.

## 12. Test Coverage Assessment

The existing automated tests are primarily Laravel Breeze authentication and profile tests, plus a basic homepage response test.

Critical business behavior currently lacks visible dedicated tests:

- Subdomain village resolution.
- Cross-village data isolation.
- Admin authorization and tenant scoping.
- Module enable/disable behavior.
- News and content publication filters.
- Population data governance.
- APBDes calculations.
- Service application and status flows.
- Complaint submission and status flows.
- File exports and document downloads.

This is the largest maintainability risk after tenant scoping.

## 13. Priority Recommendations

### Priority 1: Complete Tenant Isolation

1. Add explicit village ownership for non-super-admin users.
2. Resolve the active admin village from the authenticated user.
3. Replace all `Village::query()->first()` usage in admin controllers.
4. Add tenant-aware route model binding or authorization policies.
5. Scope admin indexes, updates, and deletes by active village.
6. Decide whether module settings are global or per village.

### Priority 2: Enforce Data Governance

1. Remove synthetic male/female population estimation.
2. Display a clear unavailable state when source fields are missing.
3. Add tests for official source-table precedence.
4. Add validation for conflicting totals and category sums.
5. Record data period and update metadata consistently across public pages.

### Priority 3: Add Business-Critical Tests

1. Test subdomain identification and unknown subdomains.
2. Test cross-village access denial.
3. Test role and module middleware.
4. Test service and complaint lifecycle workflows.
5. Test APBDes and population helper calculations.
6. Test published versus unpublished visibility.

### Priority 4: Improve Project Documentation

1. Replace the generic README with project setup instructions.
2. Document environment variables and external integrations.
3. Document scheduler, queue worker, storage, and deployment requirements.
4. Add a tenant onboarding checklist.
5. Add a role-permission matrix.
6. Add a data backup and retention policy.

### Priority 5: Refine Architecture

1. Split the large `HomeController` into domain-focused public controllers or query services.
2. Centralize active-village resolution in one reusable service.
3. Introduce policies for tenant-owned models.
4. Standardize repeated admin CRUD village-scoping logic.
5. Add monitoring for failed Instagram synchronization and imports.

## 14. Suggested Delivery Roadmap

### Phase A: Safety

- Fix user-to-village ownership.
- Enforce tenant-scoped admin access.
- Remove synthetic demographic values.
- Add isolation and governance tests.

### Phase B: Reliability

- Test service, complaint, and transparency workflows.
- Document deployment and background jobs.
- Add integration error monitoring.

### Phase C: Maintainability

- Refactor the public controller.
- Introduce shared tenant-aware query patterns.
- Expand project-specific documentation.

### Phase D: SaaS Readiness

- Add village provisioning.
- Make module configuration tenant-aware if required.
- Expand role granularity.
- Define domain, subscription, audit, backup, and operational policies.

## 15. Overall Assessment

Webdesku has a strong functional base and demonstrates good alignment with its documented public-service, transparency, and reusable-village goals. The database and public query structure already anticipate multiple villages, while the module system and broad admin CRUD coverage make the application adaptable.

The project should currently be treated as a feature-rich single-village deployment with partial multi-village foundations, not yet as a fully isolated multi-tenant SaaS. Completing authenticated village ownership, removing synthetic statistics, and adding business-focused automated tests would provide the largest improvement in safety and readiness.
