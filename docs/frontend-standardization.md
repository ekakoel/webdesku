# Frontend Standardization Guide

## Scope
- Berlaku untuk seluruh halaman frontend publik.
- Tidak berlaku untuk halaman beranda (`resources/views/web/home.blade.php`).
- Tidak berlaku untuk seluruh halaman backend/admin.
- Standar ini berlaku untuk single-village deployment Webdesku, bukan untuk tenant-aware UI atau village-switching UI.
- `Webdesku` adalah nama internal codebase; tampilan publik harus memakai identitas desa dari data/config deployment.

## Identity and Title Standards
- Layout publik wajib menggunakan identitas desa terpusat, bukan fallback brand `Webdesku`.
- Homepage memakai title nama desa saja.
- Halaman selain homepage memakai pola `[Nama Halaman] | [Nama Desa]`.
- Footer publik memakai nama/identitas Pemerintah Desa dari data `Village`.
- Jangan hardcode nama desa, alamat, kontak, logo, atau brand publik di Blade.

## Baseline Style
- Patokan visual utama adalah header halaman Transparansi.
- Header standar frontend menggunakan class:
  - `page-hero section-card`
- Struktur dasar section:
  - `section-wrap` -> `container-grid` -> konten halaman.

## Layout Standards
- Gunakan `container-grid` sebagai wrapper utama.
- Untuk halaman dengan lebar konten lebih sempit:
  - `container-grid--narrow` (max-width 900px)
  - `container-grid--compact` (max-width 860px)
- Untuk konsistensi jarak vertikal antar blok:
  - `page-section-stack`

## Header Standards
- Header utama halaman harus berbentuk:
  - Label kecil (`small`)
  - Judul utama (`h1`)
  - Deskripsi ringkas (`p`)
- Aksi tambahan (misalnya tombol/link kembali atau cek status) diletakkan di:
  - `page-hero__actions`

## Implemented in This Refactor
- Halaman Pengaduan telah distandarisasi struktur container, spacing, dan hero.
- Halaman frontend lain (selain beranda) telah diarahkan ke pola header dan container yang sama.
- Style global ditambahkan di `resources/css/web.css`:
  - `.page-hero`
  - `.page-hero__actions`
  - `.container-grid--narrow`
  - `.container-grid--compact`
  - `.page-section-stack`

## Implementation Rule for Next Changes
- Setiap halaman frontend baru wajib:
  1. Memakai `section-wrap` + `container-grid`.
  2. Memakai `page-hero section-card` untuk header utama.
  3. Tidak memakai inline `max-width`; gunakan class utilitas container.
  4. Menjaga konsistensi spacing menggunakan class utilitas, bukan inline margin acak.

## Additional Notes (Phase 2)
- Halaman detail (`show`) juga mengikuti pola hero yang sama agar konsisten dengan halaman index.
- Untuk viewport mobile, `page-hero` otomatis menjadi 1 kolom dan action pindah ke kiri.
- Empty state card sebaiknya tidak menambahkan `margin-top` inline jika sudah berada dalam alur `section-wrap` standar.

## Component Standards (Phase 3)
- Gunakan token global:
  - `--radius-card`
  - `--radius-control`
  - `--text-control`
- Gunakan class reusable untuk kontrol form:
  - `form-control` untuk `input/select/textarea`
  - `form-control-button` untuk tombol aksi form utama
- Hindari styling input/button dengan inline style kecuali benar-benar kasus khusus.
- Untuk halaman status/form publik (layanan/pengaduan/statistik), gunakan komponen kontrol yang sama agar visual konsisten.
