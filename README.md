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

## Modul & Menu Navigasi

> ⚠ Semua data adalah **dummy/hardcoded**. Alur menu dan form sudah dapat disimulasikan sepenuhnya.

### Warehouse
| Menu | Deskripsi |
|---|---|
| Dashboard | Statistik stok, penerimaan, pengeluaran hari ini |
| Inventory | Tabel inventaris dengan filter kategori & status |
| Penerimaan Barang | Form Good Receipt (GR) — simulasi penerimaan dari vendor |
| **Pengeluaran Barang** | Form Good Issue (GI) — Nomor Dokumen auto-generate, Departemen Penerima (termasuk Warehouse), Status |
| Mutasi Stok | Log perpindahan barang antar lokasi/gudang |
| Laporan Stok | Ringkasan stok per kategori |
| **Surat Jalan** | Buat & kelola surat jalan pengiriman — relasi ke Nomor PO |
| **Req ATK** | Pengajuan kebutuhan Alat Tulis Kantor dari departemen |

### Human Resource (HRD)
| Menu | Deskripsi |
|---|---|
| Dashboard | KPI karyawan, chart absensi, rekap pengajuan |
| Data Karyawan | Tabel data karyawan + form tambah/edit |
| Absensi | Catat & lihat absensi harian |
| Payroll | Perhitungan gaji + cetak slip gaji per karyawan |
| Laporan HR | Laporan umum HRD |
| Pengajuan Cuti | Form + daftar cuti; alur: Staff → Manager → HR |
| **Pengajuan Sakit** | Form sakit + upload surat sakit (simulasi); alur: Staff → HR |
| Pengajuan Lembur | Form + estimasi upah lembur |
| Laporan Cuti/Sakit/Lembur | Laporan terpadu 3 tab dengan grafik |
| **Form Izin** | Izin tidak masuk, izin keluar, izin terlambat |
| **Pengajuan Dinas** | Perjalanan dinas — Kota/Lokasi dari Kode Proyek, tracking status |
| **Pengajuan SPJ** | Surat Perjalanan Dinas: transport, hotel, makan; grand total reimbursement |
| **Laporan Tunjangan** | Rekap tunjangan per karyawan + take-home pay + chart |

### eProcurement
| Menu | Deskripsi |
|---|---|
| Dashboard | Statistik PR, MR, PO |
| **Material Request (MR)** | Form MR → alur: Pemohon → Manager Dept (Review) → PPIC (Approve) → Gudang (Proses) |
| Purchase Request (PR) | Form PR — alur lengkap: Pemohon → PPIC → QC/QC Mgr → WH Manager → Site CM → Cost Control → Project Manager (Final Approve) + progress tracker |
| Approval | Daftar PR menunggu persetujuan |
| **Purchase Order (PO)** | Data PO dari PR yang sudah disetujui |
| Laporan Pengadaan | Ringkasan pengadaan |

### Aset IT
| Menu | Deskripsi |
|---|---|
| Dashboard | Statistik aset (total, aktif, maintenance, tidak aktif, total nilai) + chart per kategori |
| Daftar Aset IT | Inventaris aset IT dengan filter search |
| Daftarkan Aset | Form input aset baru |
| **History Aset** | Riwayat service & pemeliharaan aset (jenis, teknisi, vendor, biaya, status) |
| **Pengeluaran Aset** | Daftar aset write-off + form pengajuan pengeluaran aset tidak terpakai |

### E-Approval
| Menu | Deskripsi |
|---|---|
| Dashboard | Statistik dokumen + dokumen terbaru |
| Dokumen Approval | Daftar dokumen, filter status & jenis, riwayat approval per step, aksi Setujui/Tolak |
| **Upload Dokumen** | Form upload dokumen + pilih Approver & Reviewer per step (Tanda Tangan / Review Only) |
| **Workflow Approval** | Visualisasi alur approval lengkap per dokumen — siapa TTD, siapa review, status e-TTD digital simulasi |

---

## Relasi Antar Form

```
MR (Material Request)
  └─ diajukan oleh pemohon
  └─ direview Manager Dept → disetujui PPIC → diproses Gudang

PR (Purchase Request)
  └─ bisa dibuat dari MR yang sudah disetujui
  └─ alur: Pemohon → PPIC → QC/QC Mgr → WH Manager → Site CM → Cost Control → Project Manager

PO (Purchase Order)
  └─ dibuat dari PR yang sudah Final Approve

Surat Jalan
  └─ relasi ke Nomor PO — data surat jalan ditarik berdasarkan PO

SPJ (Surat Perjalanan Dinas)
  └─ relasi ke Kode Proyek → menentukan Kota/Lokasi tujuan dinas
  └─ pemohon mengisi biaya transport, hotel, makan → sistem hitung grand total
```

---

## Alur Approval Detail

### Material Request (MR)
```
[Pemohon] → [Manager Dept: Review] → [PPIC: Approve] → [Gudang: Proses]
```

### Purchase Request (PR)
```
[Pemohon] → [PPIC: Approve] → [QC / QC Mgr: Review] → [WH Manager: Review]
          → [Site CM: Review] → [Cost Control: Review] → [Project Manager: Final Approve]
```

### E-Approval (Konfigurasi Bebas)
- User memilih sendiri siapa yang **Tanda Tangan (Approve)** dan siapa yang **Review Only**
- Bisa menambah/mengurangi step secara dinamis
- Simulasi tanda tangan digital (e-TTD) per step yang disetujui

---

## Cara Menambahkan Data Dummy Baru

Semua data dummy berada di method private dalam masing-masing Controller:

| Controller | Method | Keterangan |
|---|---|---|
| `WarehouseController` | `getDummyInventory()` | Data item inventaris |
| `WarehouseController` | `getDummyMovements()` | Data mutasi stok |
| `HRController` | `getDummyEmployees()` | Data karyawan |
| `HRController` | `getDummyAttendance()` | Data absensi |
| `ProcurementController` | `getDummyPRs()` | Data Purchase Request |
| `AsetITController` | `getDummyAssets()` | Data aset IT |
| `EApprovalController` | `getDummyDocuments()` | Data dokumen approval |

**Cara menambah data:** Cukup tambahkan array baru ke dalam method `getDummyXxx()` di controller yang bersangkutan. Format mengikuti array yang sudah ada.

---

## Struktur Modul (File Penting)

```
app/Http/Controllers/
  Warehouse/WarehouseController.php
  HR/HRController.php
  Procurement/ProcurementController.php
  AsetIT/AsetITController.php
  EApproval/EApprovalController.php

resources/views/
  warehouse/      — issuing, receiving, surat-jalan, req-atk, ...
  hr/             — employees, attendance, sick-leaves, pengajuan-dinas, pengajuan-spj, tunjangan, ...
  procurement/    — material-request, pr-form, purchase-requests, purchase-order, ...
  aset-it/        — assets, history, pengeluaran, ...
  e-approval/     — documents, create (upload), workflow, ...
  layouts/app.blade.php  — sidebar navigasi utama

routes/web.php    — semua route terdaftar di sini
```

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

## Modul HR — Detail Fitur

| Fitur | Deskripsi | Workflow Approval |
|---|---|---|
| **Pengajuan Cuti** | Form & daftar pengajuan cuti karyawan | Staff → Manager → HR |
| **Pengajuan Sakit** | Form sakit + upload surat sakit (simulasi) | Staff/HR → Verifikasi HR |
| **Pengajuan Lembur** | Form & daftar lembur + estimasi upah | Staff → Manager → HR (Payroll) |
| **Form Izin** | Izin tidak masuk, izin keluar, izin terlambat | Staff → Atasan → HR |
| **Pengajuan Dinas** | Dinas luar kota, Kota dari Kode Proyek | Staff → Manager → HR |
| **Pengajuan SPJ** | Reimbursement biaya dinas (transport, hotel, makan) | Staff → Manager → Finance |
| **Laporan Tunjangan** | Rekap tunjangan bulanan per karyawan | — |
| **Laporan Cuti/Sakit/Lembur** | Laporan terpadu 3 tab dengan grafik | — |

> Semua modul HR berbasis simulasi UI/UX dengan data dummy. Belum ada integrasi real dengan sistem absensi atau Adempiere ERP.

## Teknologi

- Laravel 12 · PHP 8.4 · PostgreSQL (Supabase)
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
