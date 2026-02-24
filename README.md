# WHR-ePIS — Warehouse HR eProcurement Integrated System

> ⚠ **DEMO MODE** — Simulasi UI/UX. Semua data adalah dummy. Belum ada integrasi real dengan Adempiere ERP.

---

## Cara Menjalankan Demo

### Opsi 1 — PHP langsung (tanpa Docker)
```bash
# Install dependencies
composer install

# Jalankan server (tidak perlu npm run dev)
php artisan serve

# Buka browser: http://localhost:8000
```

### Opsi 2 — Docker (direkomendasikan)
```bash
# Build image & jalankan container
docker compose up --build

# Atau jalankan di background
docker compose up --build -d

# Buka browser: http://localhost:8000

# Stop container
docker compose down
```

### Opsi 3 — Docker tanpa Compose
```bash
# Build image
docker build -t whr-epis .

# Jalankan container
docker run -p 8000:8000 --env-file .env whr-epis

# Buka browser: http://localhost:8000
```

## Akun Demo Login

| Email | Password | Role |
|---|---|---|
| `admin@demo.com` | `demo123` | Admin |
| `manager@demo.com` | `demo123` | Manager |
| `staff@demo.com` | `demo123` | Staff |

## Konfigurasi Nama Program (.env)

```env
APP_NAME="WHR-ePIS Demo"
APP_TITLE="Warehouse Human Resource eProcurement Integrated System"
APP_SHORT_TITLE="WHR-ePIS"
```

## Konfigurasi Database Supabase (.env)

```env
DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.your-project-ref
DB_PASSWORD=your-password
DB_SSLMODE=require
```

## Modul

- **Warehouse**: Dashboard, Inventory, GR, GI, Mutasi Stok, Laporan
- **HR**: Dashboard, Karyawan, Absensi, Payroll, Laporan HR, Pengajuan Cuti, Pengajuan Sakit, Pengajuan Lembur, Laporan Cuti/Sakit/Lembur
- **eProcurement**: Dashboard, PR, Approval Workflow, Laporan

## Slip Gaji

Modul Payroll dilengkapi dengan fitur **cetak slip gaji** per karyawan:

- Halaman slip gaji khusus dengan layout profesional (mirip slip resmi perusahaan)
- Informasi lengkap: header perusahaan, data karyawan (Nama, NIPEG, Jabatan, Grade, Status), periode gaji
- Rincian **Penerimaan**: Gaji Pokok, Tunjangan Pokok, Tunjangan Beras, Insentif Kinerja, Tunjangan Pajak, Uang Cuti Tahunan, Upah Lembur
- Rincian **Potongan**: Biaya Jabatan, Premi JHT, Premi JP, Premi JKK+JKM, PPh 21 atas Gaji, PPh 21 atas Bonus
- **Gaji Bersih** (Take Home Pay) = Total Penerimaan − Total Potongan
- Terbilang dan rekap ringkas
- Kolom tanda tangan (Payroll HR, HR Manager, Penerima)
- **Print-friendly**: tombol Cetak / Simpan PDF — sidebar dan toolbar disembunyikan otomatis saat dicetak

> Data slip gaji masih dummy/hardcoded, tetapi layout sudah siap untuk demo presentasi.

## Modul HR — Detail Fitur Tambahan

Modul HR dilengkapi dengan fitur simulasi pengajuan dan persetujuan untuk:

| Fitur | Deskripsi | Workflow Approval |
|---|---|---|
| **Pengajuan Cuti** | Form & daftar pengajuan cuti karyawan | Staff → Manager → HR |
| **Pengajuan Sakit** | Pencatatan ketidakhadiran sakit + keterangan medis | Staff/HR → Verifikasi HR |
| **Pengajuan Lembur** | Form & daftar pengajuan lembur + estimasi upah | Staff → Manager → HR (Payroll) |
| **Laporan Cuti/Sakit/Lembur** | Laporan terpadu dengan grafik & tabel detail (3 tab) | — |

> Semua modul HR tambahan ini masih berbasis simulasi UI/UX dengan data dummy. Belum ada integrasi real dengan sistem absensi atau Adempiere ERP.

## Teknologi

- Laravel 12 · PHP 8.2 · PostgreSQL (Supabase)
- TailwindCSS CDN · Alpine.js CDN · Chart.js CDN

---
*Simulasi UI/UX Demo — Belum terintegrasi dengan Adempiere ERP*

<!--original-laravel-readme-below-->
<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
