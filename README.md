# Web Tracer Reporting System

Aplikasi web untuk mengolah data **Tracer Study** dari file Excel (format DIKTI) menjadi dashboard analitik interaktif dan laporan Word (.docx) otomatis.

Sistem membaca Excel apa adanya tanpa cleaning, menghitung statistik untuk **13 parameter analisis**, menghasilkan narasi otomatis, memvisualisasikan hasil dalam grafik Chart.js, dan mengekspor laporan formal dalam format Word.

## Features

- **Upload Excel** — Upload file Excel format DIKTI dengan meta info (institusi, prodi, jenjang, tahun). Drag & drop dengan validasi format dan duplikasi.
- **Dashboard** — 4 KPI cards + tabel data tracer study dengan aksi cepat (lihat analisis, generate laporan, hapus).
- **Analisis Otomatis** — 13 parameter analisis dengan 6 tipe statistik, narasi template, dan grafik Chart.js (pie, bar, grouped bar).
- **Report Generation** — Laporan Word (.docx) otomatis: cover page, summary table, 13 parameter sections dengan narasi & tabel statistik.
- **Cross-linked Navigation** — Semua halaman terhubung: Dashboard ↔ Analysis ↔ Reports.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 13 / PHP 8.3+ |
| Database | MySQL 8+ (SQLite for tests) |
| Frontend | Bootstrap 5.3 + AdminLTE 4.9 |
| Charts | Chart.js 4.4.7 |
| Excel Reader | PhpSpreadsheet 5.9 |
| Word Export | PHPWord 1.4 |
| Build Tool | Vite 8 |

## Architecture

Clean Architecture dengan 4 layer, Repository Pattern, dan Service Layer:

```
Presentation (Controllers, Views, Requests)
    → Application (Service & Repository Interfaces)
        → Domain (Models, Service Implementations)
            → Infrastructure (Eloquent Repos, Config, PhpSpreadsheet, PHPWord)
```

### Service Layer

| Service | Responsibility |
|---------|---------------|
| `ExcelReaderService` | Membaca data Excel per parameter dari file DIKTI |
| `StatisticsService` | Menghitung statistik (frequency, mean_median, mean, sum, display, index) |
| `NarrativeService` | Generate narasi otomatis dari template + data statistik |
| `ChartConfigService` | Membuat konfigurasi Chart.js (pie, bar, grouped_bar) |
| `TracerAnalysisService` | Orchestrator — menjalankan pipeline analisis lengkap |
| `ReportGeneratorService` | Generate laporan Word (.docx) dengan PHPWord |

### Processing Pipeline

```
Upload Excel
  → ExcelReaderService.read()         // extract raw data per parameter
  → StatisticsService.calculate()     // compute stats (6 types)
  → NarrativeService.generate()       // fill template narratives
  → ChartConfigService.generate()     // build Chart.js configs
  → Save 13 AnalysisResult records    // DB persistence
  → ReportGeneratorService.generate() // on-demand Word export
```

## 13 Parameter Analisis

| # | Parameter | Tipe Statistik | Chart |
|---|-----------|---------------|-------|
| 1 | Status Setelah Lulus | frequency | pie |
| 2 | Lama Mendapatkan Pekerjaan | mean_median | bar |
| 3 | Pendapatan | sum | bar |
| 4 | Kesesuaian Bidang Ilmu | frequency | pie |
| 5 | Tingkat Pendidikan untuk Pekerjaan | frequency | pie |
| 6 | Sumber Dana Pendidikan | frequency | bar |
| 7 | Jenis Perusahaan | frequency | bar |
| 8 | Tingkat Perusahaan | frequency | bar |
| 9 | Sumber Informasi Lowongan | frequency | bar |
| 10 | Metode Pencarian Kerja | frequency | bar |
| 11 | Rata-rata IPK | mean | — |
| 12 | Nama Tempat Kerja | display | — |
| 13 | Penilaian Kemampuan Diri | index | grouped_bar |

## Prerequisites

- PHP 8.3+ with extensions: mbstring, xml, zip, gd
- Composer 2.x
- Node.js 20+ & npm
- MySQL 8+

## Installation

```bash
git clone https://github.com/akbarwigunaa/tracer-reporting.git
cd tracer-reporting

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configure database in .env then run migrations
php artisan migrate

npm run build
```

## Development

```bash
php artisan serve        # Start Laravel dev server (port 8000)
npm run dev              # Start Vite dev server (separate terminal)
php artisan test         # Run full test suite
php artisan test --testsuite=Unit     # Unit tests only
php artisan test --testsuite=Feature  # Feature tests only
```

## Usage

1. Buka `http://localhost:8000` — langsung masuk Dashboard
2. Klik **Upload Data** — isi meta info + upload Excel format DIKTI
3. Sistem otomatis menganalisis 13 parameter & membuat grafik
4. Klik **Lihat Analisis** untuk melihat statistik, narasi, dan chart
5. Klik **Generate Laporan** untuk membuat file Word (.docx)
6. Download laporan dari halaman Laporan

## Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/dashboard` | Dashboard utama |
| GET | `/upload` | Form upload Excel |
| POST | `/upload` | Proses upload & analisis |
| GET | `/analysis` | Daftar hasil analisis |
| GET | `/analysis/{id}` | Detail analisis (13 parameter + charts) |
| GET | `/reports` | Daftar laporan |
| POST | `/reports/{id}/generate` | Generate laporan Word |
| GET | `/reports/{id}/download` | Download laporan .docx |
| DELETE | `/reports/{id}` | Hapus laporan |
| DELETE | `/tracer-studies/{id}` | Hapus tracer study |

## Testing

38 tests, 103 assertions — all passing.

| Suite | Tests | Coverage |
|-------|-------|----------|
| Feature / DashboardTest | 5 | Display, empty state, data, redirect, delete |
| Feature / UploadTest | 5 | Form, validation, duplicate, end-to-end Excel |
| Feature / AnalysisTest | 4 | Index, completed list, detail, Chart.js render |
| Feature / ReportTest | 5 | Index, generate, reject, download, delete |
| Unit / StatisticsServiceTest | 10 | All 6 stat types, edge cases, exceptions |
| Unit / NarrativeServiceTest | 3 | Template generation, unknown key, placeholder ordering |
| Unit / ChartConfigServiceTest | 5 | Pie, bar, grouped bar, null, unknown type |

## Project Structure

```
app/
├── Http/Controllers/       # 4 controllers (Dashboard, Upload, Analysis, Report)
├── Http/Requests/          # Form request validation
├── Models/                 # 3 Eloquent models
├── Repositories/
│   ├── Interfaces/         # 3 repository interfaces
│   └── Eloquent/           # 3 Eloquent implementations
├── Services/
│   ├── Interfaces/         # 6 service interfaces
│   └── *.php               # 6 service implementations
└── Providers/              # RepositoryServiceProvider (9 bindings)

config/
├── tracer.php              # 13 parameter definitions
└── narratives.php          # Narrative templates

resources/views/
├── layouts/app.blade.php   # AdminLTE layout
├── dashboard.blade.php     # Dashboard with KPI cards
├── upload/create.blade.php # Upload form with dropzone
├── analysis/               # Index + detail views with Chart.js
└── reports/index.blade.php # Report management

tests/
├── Feature/                # 5 files, 20 tests
├── Unit/                   # 4 files, 19 tests
└── Fixtures/               # Sample Excel for integration tests
```

## License

This project is for academic/personal use.
