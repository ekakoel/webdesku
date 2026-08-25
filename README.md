# Webdesku

Webdesku adalah aplikasi Laravel 12 untuk **Sistem Informasi Desa / Website Desa**.

Nama **Webdesku** hanya dipakai sebagai identitas internal repository/codebase. Identitas yang tampil ke publik harus selalu berasal dari konfigurasi/data desa aktif pada deployment, terutama data `Village`.

Arsitektur operasional saat ini adalah:

```text
One Webdesku deployment
        ↓
One village
        ↓
Reusable codebase for other deployments
```

Webdesku **bukan** multi-tenant SaaS. Jangan menambahkan tenant switching, subdomain tenancy, runtime village resolver, atau tenant-aware authentication kecuali diminta eksplisit.

## Tujuan

Webdesku membantu desa mengelola:

- website informasi publik,
- profil desa,
- berita,
- agenda,
- layanan desa,
- pengaduan masyarakat,
- transparansi/APBDes,
- galeri,
- pengumuman,
- peraturan desa,
- statistik dan infografis,
- administrasi konten melalui admin panel.

## Stack

- PHP `^8.2`
- Laravel `^12.0`
- Blade
- Vite
- Tailwind CSS
- Alpine.js
- MySQL/MariaDB atau database lain sesuai konfigurasi Laravel

Package pendukung:

- `barryvdh/laravel-dompdf`
- `maatwebsite/excel`
- `simplesoftwareio/simple-qrcode`
- `spatie/laravel-sitemap`

## Arsitektur

Webdesku adalah Laravel monolith dengan pemisahan utama:

- public website di `resources/views/web`
- admin panel di `resources/views/admin`
- routing utama di `routes/web.php`
- model Eloquent di `app/Models`
- controller public/admin di `app/Http/Controllers`
- service pendukung di `app/Services`
- support/helper class di `app/Support`

`Village` dapat digunakan sebagai konfigurasi identitas deployment desa, bukan sebagai tenant boundary.

## Identitas Publik Website

- Jangan menampilkan `Webdesku` sebagai nama website, judul browser, footer, metadata PDF, atau branding publik.
- Homepage menggunakan nama desa sebagai title utama.
- Subhalaman menggunakan pola title: `[Nama Halaman] | [Nama Desa]`.
- Fallback identitas publik adalah `Pemerintah Desa` jika data desa belum tersedia.
- Logo, nama, kontak, alamat, dan metadata publik harus diambil dari data/config desa, bukan hardcoded di Blade/PHP.

## Dokumentasi Utama

Baca dokumen ini sebelum melakukan perubahan:

- `AGENTS.MD`
- `docs/BLUEPRINT_WEBDESKU.md`
- `docs/PROJECT_REPORT.md`
- `docs/data-governance-matrix.md`
- `docs/frontend-standardization.md`

Jika dokumen lama menyebut multi-desa/SaaS, ikuti aturan terbaru: **single-village reusable deployment**.

## Instalasi Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Untuk menjalankan development server:

```bash
composer run dev
```

Atau jalankan proses Laravel/Vite secara terpisah sesuai kebutuhan:

```bash
php artisan serve
npm run dev
```

## Storage

Jika fitur upload file/gambar digunakan, pastikan storage link tersedia:

```bash
php artisan storage:link
```

## Verifikasi

Gunakan command berikut sesuai kebutuhan perubahan:

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

Jangan klaim command berhasil jika belum dijalankan.

## Prinsip Pengembangan

- Pahami dokumentasi dan implementasi sebelum coding.
- Jangan reintroduce multi-tenancy.
- Jangan hardcode identitas desa.
- Gunakan konfigurasi/data persisted untuk nilai spesifik desa.
- Jaga controller tetap thin.
- Gunakan Form Request untuk validasi non-trivial.
- Enforce authorization di server-side.
- Gunakan Laravel Storage untuk file upload.
- Hindari package baru jika Laravel atau package existing sudah cukup.
- Update dokumentasi jika mengubah arsitektur, database, routes, roles, modules, configuration, atau business rule.

## Catatan Legacy

Repository masih dapat mengandung legacy multi-tenant remnants seperti `village_id`, `IdentifyVillage`, atau `currentVillage`.

Jangan langsung hapus dan jangan langsung perluas. Inspeksi dependensi dulu, lalu refactor secara kecil dan aman jika memang dibutuhkan.
