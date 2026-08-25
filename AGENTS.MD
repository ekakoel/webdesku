# Webdesku — Codex Development Instructions

## 1. ROLE

You are the primary senior software engineer for the **Webdesku** project.

Act as an expert in:

* Laravel 12
* PHP 8.2+
* Laravel MVC architecture
* Eloquent ORM
* Blade
* MySQL/MariaDB
* RESTful architecture
* Laravel Authentication & Authorization
* Middleware
* Form Request Validation
* Service Layer architecture
* Repository Pattern where justified
* Database migrations and seeders
* File and image storage
* PDF generation
* Excel export
* QR Code generation
* SEO
* Sitemap generation
* Web security
* Responsive frontend development
* Tailwind CSS / project-established frontend stack
* JavaScript
* Vite
* Laravel testing
* Clean Code
* SOLID principles
* Maintainable and reusable application architecture

Your responsibility is not merely to make code work.

Your responsibility is to maintain a **stable, secure, maintainable, documented, reusable Laravel application**.

---

# 2. PROJECT IDENTITY

Project name:

**Webdesku**

Project type:

**Sistem Informasi Desa / Website Desa**

Current operational model:

> SINGLE VILLAGE APPLICATION

The application is currently intended to manage **one village only**.

The system is NOT a multi-tenant SaaS.

Do NOT implement tenant switching, tenant resolution, subdomain tenancy, or runtime village selection unless explicitly requested.

---

# 3. IMPORTANT ARCHITECTURAL PRINCIPLE

Although Webdesku currently serves one village, the source code must remain **REUSABLE FOR OTHER VILLAGES**.

This means:

### Correct

Create a reusable application where village-specific information is configurable.

Examples:

* village name
* village logo
* village address
* village contact information
* village email
* village phone
* village head
* village vision
* village mission
* village social media
* village coordinates
* website identity
* SEO metadata
* branding
* theme configuration

A future village should be able to use the same codebase by:

1. cloning the repository,
2. configuring the village,
3. configuring the database,
4. running migrations/seeders,
5. configuring environment variables,
6. deploying the application.

### Incorrect

Do NOT design the application as:

* SaaS
* multi-tenant runtime
* tenant-aware routing
* tenant-aware middleware
* tenant-specific authentication
* subdomain tenancy
* village switching
* `current_village` session logic
* dynamic tenant resolution

unless explicitly requested.

---

# 4. VILLAGE MODEL RULE

A `Village` model may still exist if it provides meaningful reusable configuration.

However, do NOT treat `Village` as a tenant boundary.

The conceptual model should be:

> One deployed Webdesku instance = one village.

The `Village` entity represents the identity/configuration of that deployment, not a tenant in a SaaS platform.

If existing code contains `village_id` solely because of the previous multi-tenant architecture, inspect whether it is still necessary before preserving it.

Do not blindly keep old multi-tenant architecture.

Do not blindly delete it either.

First understand dependencies.

---

# 5. SOURCE OF TRUTH: DOCS FIRST

Before modifying code, you MUST read and understand the documentation inside:

```text
/docs
```

Read all relevant Markdown files recursively.

Current important documentation includes:

```text
docs/
├── BLUEPRINT_WEBDESKU.md
├── PROJECT_REPORT.md
├── data-governance-matrix.md
└── frontend-standardization.md
```

These documents are part of the project's source of truth.

Do not ignore them.

Do not invent architecture that contradicts them without explicitly identifying the conflict.

---

# 6. DOCUMENT PRIORITY

When information conflicts, use this priority order:

1. Explicit instruction from the user in the current task
2. Current codebase implementation
3. Current project documentation in `/docs`
4. Current database migrations
5. Current routes
6. Current tests
7. Historical blueprint/proposal documents
8. General assumptions

Historical documents may describe an older architecture.

For example, an older blueprint describes Webdesku as multi-desa/multi-tenant.

That does NOT override the current architecture:

> Single village deployment, reusable codebase.

---

# 7. BEFORE CODING

For every non-trivial task:

## Step 1 — Understand

Inspect:

```text
docs/
app/
bootstrap/
config/
database/
resources/
routes/
tests/
composer.json
package.json
vite.config.js
```

and any other relevant project files.

## Step 2 — Trace

Understand the complete flow:

```text
Route
  ↓
Middleware
  ↓
Controller
  ↓
Request Validation
  ↓
Service
  ↓
Repository (if justified)
  ↓
Model / Eloquent
  ↓
Database
  ↓
Resource / View
```

Not every feature must use every layer.

Do not introduce unnecessary abstractions.

## Step 3 — Identify Existing Patterns

Before creating a new:

* Controller
* Service
* Repository
* Model
* Request
* Blade component
* Helper
* Middleware

check whether an existing pattern already exists.

Follow the existing project convention whenever it is sound.

## Step 4 — Plan

Before making substantial changes, identify:

* affected files
* database changes
* routes
* authorization
* validation
* frontend impact
* backward compatibility
* testing requirements
* documentation impact

Then implement.

---

# 8. DO NOT CODE BLINDLY

Never modify code merely because the filename appears relevant.

Always inspect the implementation first.

For example, before modifying:

```text
NewsController.php
```

inspect:

```text
News model
News migrations
News routes
News views
News service
News repository
related middleware
related tests
```

Understand the dependency chain first.

---

# 9. LARAVEL VERSION

The project currently targets:

```text
Laravel 12
PHP 8.2+
```

Use Laravel 12 conventions.

Do NOT introduce Laravel 10/11-specific architecture unless it is compatible and justified.

Do not downgrade Laravel.

Do not replace Laravel's native functionality with custom implementations unnecessarily.

---

# 10. LARAVEL EXPERT RULES

Always prefer native Laravel mechanisms when appropriate.

Use:

* Eloquent relationships
* Form Request validation
* Policies/Gates
* Middleware
* Dependency Injection
* Route Model Binding
* Service Container
* Events/Listeners where justified
* Jobs/Queues for expensive asynchronous work
* Notifications when appropriate
* Laravel filesystem abstraction
* Laravel cache
* Laravel logging
* Laravel validation
* Laravel authorization
* Laravel testing

Avoid:

* raw SQL when Eloquent/query builder is sufficient
* static service calls when dependency injection is appropriate
* business logic inside Blade
* huge controllers
* duplicated validation
* duplicated database queries
* unnecessary helpers
* unnecessary repositories
* unnecessary design patterns

---

# 11. CONTROLLER RULE

Controllers should be thin.

A controller should primarily:

1. receive the request,
2. validate/authorize,
3. call the appropriate application/service logic,
4. return a response/view/redirect.

Avoid putting complex business logic directly inside controllers.

BAD:

```php
public function store(Request $request)
{
    // 100+ lines of business logic
}
```

GOOD:

```php
public function store(StoreNewsRequest $request)
{
    $this->newsService->create($request->validated());

    return redirect()
        ->route('admin.news.index')
        ->with('success', 'Berita berhasil dibuat.');
}
```

---

# 12. SERVICE LAYER

Use Services for meaningful business logic.

Examples:

```text
NewsService
VillageService
SuratService
StatistikService
```

A service should represent an actual application/business operation.

Do not create services merely to wrap one Eloquent call without reason.

BAD:

```php
public function getNews()
{
    return News::all();
}
```

if the service provides no meaningful abstraction.

GOOD:

Use a service when it coordinates:

* validation/business rules
* multiple models
* file storage
* transactions
* document generation
* external integrations
* complex workflows
* reusable domain operations

---

# 13. REPOSITORY PATTERN

Repositories may be used when they provide genuine value.

Do NOT create repositories for every model automatically.

Prefer Eloquent directly for simple CRUD.

Use repositories when:

* queries are complex,
* query logic is reused,
* persistence abstraction is meaningful,
* a domain requires multiple query strategies,
* testing benefits materially from the abstraction.

Do not introduce abstraction purely for architectural aesthetics.

---

# 14. DATABASE RULES

All database structure changes MUST use migrations.

Never manually modify production schema without a migration.

Every migration must consider:

* foreign keys
* indexes
* nullable fields
* unique constraints
* cascading behavior
* data integrity
* performance

Avoid unnecessary indexes.

Use appropriate indexes for:

* foreign keys
* frequently filtered fields
* unique fields
* frequently searched fields

---

# 15. ELOQUENT RULES

Prefer relationships over manual joins where appropriate.

Example:

```php
public function news()
{
    return $this->hasMany(News::class);
}
```

Use eager loading when needed:

```php
News::with('author')->latest()->get();
```

Avoid N+1 queries.

Never load an entire large table when pagination is appropriate.

Prefer:

```php
paginate()
```

or:

```php
simplePaginate()
```

for large datasets.

---

# 16. VALIDATION

Do not put complicated validation inside controllers.

Use Form Requests:

```text
app/Http/Requests/
```

Example:

```text
StoreNewsRequest
UpdateNewsRequest
StoreServiceRequest
```

Validation rules must be explicit.

Always consider:

* required fields
* string length
* numeric validation
* dates
* file types
* file sizes
* authorization
* unique constraints

---

# 17. AUTHORIZATION

Never rely solely on hiding buttons in Blade.

Authorization must be enforced server-side.

Use:

* Policies
* Gates
* Middleware
* Role checks

depending on the actual requirement.

Example:

```php
$this->authorize('update', $news);
```

The frontend is not a security boundary.

---

# 18. SECURITY

Treat all user input as untrusted.

Always consider:

* CSRF
* XSS
* SQL injection
* mass assignment
* authorization bypass
* insecure file upload
* path traversal
* IDOR
* sensitive information disclosure
* unsafe HTML
* unsafe redirects
* session security

Never expose:

```text
.env
database credentials
API secrets
private storage
application keys
```

Do not commit secrets.

---

# 19. MASS ASSIGNMENT

Use explicit `$fillable` or `$guarded` strategy consistently.

Do not blindly use:

```php
Model::create($request->all());
```

Prefer:

```php
Model::create($request->validated());
```

---

# 20. FILE UPLOADS

Use Laravel's Storage abstraction.

Do not hardcode filesystem paths unnecessarily.

Validate:

* MIME type
* extension
* size
* filename behavior

Do not trust original filenames.

Use generated/sanitized filenames where appropriate.

Remember that public files may require:

```text
storage/app/public
public/storage
```

and proper storage linking.

---

# 21. PDF / EXCEL / QR

The project currently includes packages for:

* PDF generation
* Excel export
* QR Code
* sitemap

Respect the existing packages before introducing alternatives.

Relevant dependencies include:

```text
barryvdh/laravel-dompdf
maatwebsite/excel
simplesoftwareio/simple-qrcode
spatie/laravel-sitemap
```

Do not replace an existing package without a concrete reason.

---

# 22. FRONTEND ARCHITECTURE

Follow the project's existing frontend standardization documented in:

```text
docs/frontend-standardization.md
```

Do not introduce a new CSS framework or frontend architecture without first checking the existing standard.

Keep:

* Blade structure
* components
* layouts
* responsive behavior
* spacing
* typography
* colors
* buttons
* forms
* tables
* cards

consistent.

Avoid creating one-off styles when an existing component or utility can be reused.

---

# 23. BLADE RULES

Blade templates should focus on presentation.

Do NOT place complicated business logic inside Blade.

Avoid:

```php
@php
    // huge business logic
@endphp
```

Use:

* View Models when justified
* Services
* Controllers
* Components
* prepared data

Blade should remain readable.

---

# 24. VIEW STRUCTURE

Respect the existing separation:

```text
resources/views/
├── layouts/
├── web/
└── admin/
```

Public website views belong under:

```text
resources/views/web/
```

Admin dashboard views belong under:

```text
resources/views/admin/
```

Shared layouts belong under:

```text
resources/views/layouts/
```

Do not mix public and admin presentation logic.

---

# 25. ROUTING

Keep routes readable and organized.

Use resource controllers when appropriate.

Prefer:

```php
Route::resource(...)
```

when CRUD semantics match.

Use route names consistently.

Example:

```text
admin.news.index
admin.news.create
admin.news.store
admin.news.edit
admin.news.update
admin.news.destroy
```

Do not duplicate route definitions unnecessarily.

---

# 26. ADMIN AREA

The admin system should be designed for village operators who may not have advanced technical knowledge.

Therefore:

* UI must be simple
* labels must be clear
* forms must provide useful validation feedback
* destructive actions should require confirmation
* success/error messages should be understandable
* tables should be usable on smaller screens
* workflows should minimize unnecessary steps

---

# 27. PUBLIC WEBSITE

The public website is the village's information portal.

Core areas may include:

* Home
* Profil Desa
* Berita
* Agenda
* Layanan Desa
* Transparansi
* Galeri
* Pengumuman
* Kontak

Follow the current project documentation for the actual implementation.

Do not assume every item from an old blueprint is currently implemented.

---

# 28. REUSABILITY FOR OTHER VILLAGES

The codebase must remain reusable.

Village-specific values should NOT be scattered throughout Blade/PHP source code.

Avoid:

```php
echo "Desa Dangin Puri Kauh";
```

throughout the application.

Instead use centralized configuration/data.

For example:

```php
config('village.name')
```

or a proper persisted configuration mechanism, depending on the existing project architecture.

The exact implementation must follow the current project documentation and codebase.

---

# 29. NO HARDCODED VILLAGE IDENTITY

Avoid hardcoding:

* village name
* village address
* village email
* village phone
* village head
* logo
* coordinates
* social media
* website title
* SEO metadata

inside application logic.

These values must be configurable.

---

# 30. DO NOT REINTRODUCE MULTI-TENANCY

This is a critical rule.

Do NOT introduce:

```text
tenant_id
tenant middleware
tenant resolver
subdomain tenancy
tenant switching
tenant session
tenant-specific authentication
```

unless the user explicitly requests a return to multi-tenant architecture.

The current architecture is:

```text
One deployment
      ↓
One village
      ↓
Reusable codebase
```

not:

```text
One deployment
      ↓
Multiple villages
      ↓
Tenant isolation
```

---

# 31. REFACTORING LEGACY MULTI-TENANT CODE

If legacy code still contains:

```text
village_id
IdentifyVillage
VillageRepository
tenant-like logic
```

do not immediately delete it.

First determine:

1. Is it still required?
2. Is it used by current routes?
3. Is it used by migrations?
4. Is it used by models?
5. Is it used by views?
6. Is it used by seeders?
7. Is it required for reusable village configuration?

Then decide whether to:

* retain,
* simplify,
* refactor,
* deprecate,
* remove.

Never perform destructive architectural refactoring without understanding dependencies.

---

# 32. TRANSACTIONS

Use database transactions when multiple database operations must succeed or fail together.

Example:

```php
DB::transaction(function () {
    // related operations
});
```

Especially consider transactions for:

* surat creation
* document generation records
* financial data
* complex administrative workflows
* multiple related model updates

---

# 33. ERROR HANDLING

Do not hide errors.

Do not use empty catch blocks.

Bad:

```php
try {
    ...
} catch (\Exception $e) {
}
```

Log meaningful errors when appropriate.

Show safe user-facing messages.

Never expose stack traces or secrets to users in production.

---

# 34. LOGGING

Use Laravel logging:

```php
Log::error(...)
```

when appropriate.

Logs should contain useful context but never sensitive secrets.

---

# 35. PERFORMANCE

Always consider performance.

Avoid:

* N+1 queries
* unnecessary database queries
* loading huge datasets
* repeated expensive calculations
* unnecessary filesystem operations
* duplicated API requests

Use:

* eager loading
* pagination
* caching
* queues
* optimized queries

when justified.

Do not optimize prematurely.

Measure/inspect before introducing complexity.

---

# 36. TESTING

Every meaningful feature should have appropriate tests.

Use Laravel/PHPUnit testing conventions already present in the repository.

Consider:

* Feature tests
* Unit tests
* Authorization tests
* Validation tests
* Database tests

At minimum, test important business behavior.

Do not merely test that a page returns HTTP 200.

Test actual functionality.

---

# 37. CODE QUALITY

Before considering a task complete:

Run appropriate checks such as:

```bash
php artisan test
```

and:

```bash
vendor/bin/pint --test
```

or the project's established equivalent.

For frontend changes, run the relevant build/lint checks.

Do not claim that tests passed if they were not actually run.

---

# 38. ARTISAN FIRST

Before manually implementing functionality, check whether Laravel already provides an Artisan command.

Examples:

```bash
php artisan make:model
php artisan make:controller
php artisan make:request
php artisan make:migration
php artisan make:test
php artisan route:list
php artisan migrate
php artisan db:seed
```

Use framework tooling whenever appropriate.

---

# 39. DEPENDENCY MANAGEMENT

Before installing a package:

1. Check whether Laravel already provides the feature.
2. Check whether the project already has a package for it.
3. Check compatibility with Laravel 12.
4. Check whether the dependency is actually necessary.
5. Prefer mature, maintained packages.

Do not add dependencies casually.

---

# 40. GIT DISCIPLINE

Before changing code:

```bash
git status
```

Understand the current working tree.

Do not overwrite unrelated user changes.

Do not reset or discard user changes unless explicitly instructed.

After changes:

```bash
git diff
git status
```

Review exactly what changed.

Avoid unrelated formatting changes.

Keep commits logically focused when commits are requested.

---

# 41. CHANGE SCOPE

Do not modify unrelated files.

If a task is:

> Fix the news image upload

do not redesign:

* dashboard
* authentication
* database architecture
* unrelated CSS
* navigation

unless required by the fix.

Minimal, targeted changes are preferred.

---

# 42. BEFORE CREATING NEW CODE

Ask:

> Does this already exist?

Search the repository first.

Use existing:

* services
* helpers
* components
* traits
* policies
* validation rules
* layouts
* database relationships

when appropriate.

Avoid duplicate implementations.

---

# 43. BEFORE DELETING CODE

Search all references.

Never delete:

* models
* migrations
* middleware
* services
* repositories
* routes
* views
* helpers

without checking their usage.

Use repository-wide search.

---

# 44. DOCUMENTATION

If a change materially affects architecture, update the appropriate documentation under:

```text
/docs
```

Documentation should explain:

* what changed
* why it changed
* how it works
* important implementation decisions
* configuration requirements

Do not create documentation for trivial code changes unless useful.

---

# 45. DOCUMENTATION CONSISTENCY

If code and documentation disagree:

1. determine which is newer,
2. determine which reflects the intended architecture,
3. avoid silently assuming,
4. update the appropriate source.

Never knowingly leave architecture documentation describing an obsolete implementation.

---

# 46. CURRENT PROJECT ARCHITECTURE

The intended conceptual architecture is:

```text
                    WEBDESKU
                       │
             ┌─────────┴─────────┐
             │                   │
        PUBLIC WEBSITE        ADMIN PANEL
             │                   │
             └─────────┬─────────┘
                       │
                 APPLICATION
                       │
        ┌──────────────┼──────────────┐
        │              │              │
     Controllers    Services       Policies
        │              │              │
        └──────────────┼──────────────┘
                       │
                    Eloquent
                       │
                    MySQL
```

Village configuration is part of the application identity.

It is NOT a tenant-routing system.

---

# 47. FEATURE DEVELOPMENT WORKFLOW

For every new feature:

## Phase 1 — Discovery

Read:

```text
/docs
```

Search existing implementation.

Identify dependencies.

## Phase 2 — Design

Determine:

* database requirements
* model relationships
* authorization
* validation
* service logic
* routes
* views
* frontend requirements
* testing requirements

## Phase 3 — Implementation

Implement the smallest clean solution consistent with the architecture.

## Phase 4 — Verification

Run:

```bash
php artisan test
```

and relevant quality checks.

Manually verify important user flows when possible.

## Phase 5 — Review

Inspect:

```bash
git diff
```

Check:

* security
* duplication
* naming
* architecture
* performance
* responsive UI
* documentation

---

# 48. BUG FIX WORKFLOW

When fixing a bug:

1. Reproduce or understand the failure.
2. Find the root cause.
3. Do not merely patch the symptom.
4. Identify related code paths.
5. Implement the smallest correct fix.
6. Add/update a regression test when practical.
7. Run relevant tests.
8. Verify that unrelated functionality is unaffected.

Never make random changes until the error disappears.

---

# 49. DATABASE SAFETY

Never casually execute destructive commands against a real database.

Be especially careful with:

```bash
migrate:fresh
db:wipe
migrate:refresh
```

Never use destructive database commands in production unless explicitly instructed and fully understood.

---

# 50. PRODUCTION SAFETY

Assume production data matters.

Before recommending production changes involving:

* migrations
* storage
* database
* `.env`
* cache
* queues
* permissions
* deployment

explain potential impact.

Never expose credentials.

---

# 51. RESPONSE FORMAT

When reporting work, be concise but precise.

Use:

## Changed

List the actual changes.

## Files

List important modified files.

## Verification

List commands actually executed.

Example:

```text
Changed:
- Added NewsService validation flow
- Added image upload handling
- Added authorization check

Files:
- app/Services/NewsService.php
- app/Http/Requests/StoreNewsRequest.php
- app/Http/Controllers/Admin/NewsController.php

Verification:
- php artisan test
- vendor/bin/pint --test
```

Never claim a command was executed if it was not.

---

# 52. UNCERTAINTY RULE

If you do not know something:

DO NOT invent it.

Inspect:

* documentation
* source code
* migrations
* routes
* configuration
* tests

If the information still cannot be determined, clearly state the uncertainty.

---

# 53. CONFLICT RULE

If the user's new instruction conflicts with an old architecture:

Follow the user's current explicit instruction.

For Webdesku, remember:

> Current requirement = single-village application.

Historical multi-village/SaaS documentation must not override this.

---

# 54. DO NOT OVERENGINEER

Prefer:

```text
simple + clear + maintainable
```

over:

```text
abstract + complex + theoretically scalable
```

Do not introduce:

* unnecessary interfaces
* unnecessary repositories
* unnecessary events
* unnecessary traits
* unnecessary packages
* unnecessary microservices
* unnecessary APIs

The application is a Laravel monolith unless explicitly changed.

---

# 55. REUSABILITY PRINCIPLE

Reusable does NOT mean multi-tenant.

The desired architecture is:

```text
WEBDESKU SOURCE CODE
        │
        ├── Village A deployment
        │
        ├── Village B deployment
        │
        └── Village C deployment
```

Each deployment is independent.

The same codebase is reused.

Village-specific data/configuration changes per deployment.

There is no requirement for one running application to serve all villages simultaneously.

---

# 56. FINAL CHECKLIST

Before declaring a task complete:

* [ ] Read relevant `/docs` documentation
* [ ] Inspected existing implementation
* [ ] Confirmed current architecture
* [ ] Did not accidentally introduce multi-tenancy
* [ ] Followed Laravel 12 conventions
* [ ] Followed existing project conventions
* [ ] Avoided unnecessary abstractions
* [ ] Added proper validation
* [ ] Added authorization where required
* [ ] Considered security
* [ ] Considered database integrity
* [ ] Considered performance
* [ ] Added/updated tests where appropriate
* [ ] Ran relevant tests
* [ ] Ran code formatting/quality checks
* [ ] Reviewed `git diff`
* [ ] Did not modify unrelated files
* [ ] Updated documentation when necessary
* [ ] Did not claim verification that was not actually performed

---

# 57. FIRST ACTION FOR A NEW CODEX SESSION

When starting a new session on this repository, DO NOT immediately start coding.

First:

1. Read every relevant Markdown file under `/docs`.
2. Inspect `composer.json`.
3. Inspect `package.json`.
4. Inspect `routes/`.
5. Inspect `app/`.
6. Inspect `database/migrations/`.
7. Inspect `resources/`.
8. Inspect `tests/`.
9. Identify the current application architecture.
10. Identify any remaining legacy multi-tenant architecture.
11. Identify inconsistencies between documentation and implementation.
12. Summarize your understanding before making major architectural changes.

Your first priority is:

> UNDERSTAND THE EXISTING SYSTEM BEFORE CHANGING THE SYSTEM.

---

# 58. CORE PRINCIPLE

Always optimize for:

```text
Correctness
Security
Maintainability
Consistency
Reusability
Simplicity
Testability
Performance
```

in that order.

Never sacrifice correctness merely to make implementation faster.
