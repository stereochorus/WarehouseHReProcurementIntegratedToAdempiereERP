# Adempiere SOAP Integration Documentation

Dokumentasi integrasi antara **WHR-ePIS (Laravel)** dengan **Adempiere ERP** menggunakan SOAP Web Services (ADInterface — XFire).

---

## Daftar Isi

1. [Arsitektur Integrasi](#1-arsitektur-integrasi)
2. [Prasyarat](#2-prasyarat)
3. [Konfigurasi Environment](#3-konfigurasi-environment)
4. [File-File yang Terlibat](#4-file-file-yang-terlibat)
5. [Cara Kerja DEMO_MODE](#5-cara-kerja-demo_mode)
6. [Setup Adempiere (Sisi Server)](#6-setup-adempiere-sisi-server)
7. [Setup Laravel (Sisi Aplikasi)](#7-setup-laravel-sisi-aplikasi)
8. [Test Koneksi](#8-test-koneksi)
9. [Pemetaan Modul ke Tabel Adempiere](#9-pemetaan-modul-ke-tabel-adempiere)
10. [Referensi Error & Solusi](#10-referensi-error--solusi)

---

## 1. Arsitektur Integrasi

```
┌─────────────────────────┐         SOAP/XFire          ┌──────────────────────┐
│   WHR-ePIS (Laravel)    │ ──────────────────────────► │   Adempiere ERP      │
│                         │                              │   localhost:8080     │
│  AdempiereService.php   │ ◄────────────────────────── │   ADInterface        │
│  (SOAP Client)          │        Response XML          │   (XFire)            │
└─────────────────────────┘                              └──────────────────────┘
         │
         │ (hanya jika DEMO_MODE=false)
         ▼
┌─────────────────────────┐
│   Supabase PostgreSQL   │
│   (data app lokal)      │
└─────────────────────────┘
```

**Prinsip utama:**
- Data ERP (produk, inventory, purchase order, dll.) **selalu** melalui SOAP Adempiere
- Tidak ada akses langsung ke database Adempiere dari Laravel
- Adempiere tetap berfungsi normal sebagai sistem utama

---

## 2. Prasyarat

### Sisi Server (Adempiere)
- Adempiere berjalan di `http://localhost:8080/webui`
- ADInterface aktif dan dapat diakses:
  ```
  http://localhost:8080/ADInterface/services/ADService?wsdl
  http://localhost:8080/ADInterface/services/ModelADService?wsdl
  ```
- Web Service Types sudah dikonfigurasi (lihat [bagian 6](#6-setup-adempiere-sisi-server))

### Sisi Aplikasi (Laravel)
- PHP 8.2+ dengan extension `soap` aktif
- Verifikasi: `php -m | grep soap` → harus tampil `soap`
- Laravel 12.x

### Aktifkan PHP Soap Extension (Windows/XAMPP)

Edit file `php.ini` (cek lokasinya dengan `php -r "echo php_ini_loaded_file();"`):

```ini
; Ubah baris ini:
;extension=soap

; Menjadi:
extension=soap
```

Lalu restart Apache (XAMPP Control Panel → Apache → Stop → Start).

---

## 3. Konfigurasi Environment

Tambahkan variabel berikut di file `.env`:

```env
# ─── Adempiere SOAP Integration ───────────────────────────────────────────────
# Aktif hanya jika DEMO_MODE=false

# Switch utama
DEMO_MODE=false  # true = dummy data, false = data real dari Adempiere

# Endpoint ADInterface (XFire)
ADEMPIERE_BASE_URL=http://localhost:8080/ADInterface/services

# User API Adempiere (disarankan buat user khusus, bukan Admin)
ADEMPIERE_USERNAME=Admin
ADEMPIERE_PASSWORD=Admin

# Parameter sesi — sesuaikan dengan nilai di Adempiere Anda
# (hover field di Adempiere untuk lihat ID-nya, atau lihat Help > System Info)
ADEMPIERE_CLIENT_ID=11
ADEMPIERE_ORG_ID=11
ADEMPIERE_ROLE_ID=102
ADEMPIERE_WAREHOUSE_ID=103
ADEMPIERE_LANG=en_US

# Service Types — harus sama persis dengan yang dikonfigurasi di Adempiere
# (lihat bagian 6 untuk cara konfigurasi)
ADEMPIERE_ST_PRODUCT_LIST=GetProductList
ADEMPIERE_ST_STOCK_LIST=GetStockList
ADEMPIERE_ST_RECEIPT_LIST=GetReceiptList
ADEMPIERE_ST_ISSUE_LIST=GetIssueList
ADEMPIERE_ST_MOVEMENT_LIST=GetMovementList
ADEMPIERE_ST_CREATE_RECEIPT=CreateReceipt
ADEMPIERE_ST_CREATE_ISSUE=CreateIssue
ADEMPIERE_ST_REQ_LIST=GetRequisitionList
ADEMPIERE_ST_CREATE_REQ=CreateRequisition
ADEMPIERE_ST_PO_LIST=GetPOList
ADEMPIERE_ST_EMPLOYEE_LIST=GetEmployeeList
ADEMPIERE_ST_VENDOR_LIST=GetVendorList
```

### Cara Menemukan Client ID / Org ID / Role ID

Di Adempiere desktop client:
- Buka menu manapun → arahkan kursor ke field ID → lihat tooltip yang muncul
- Atau: **Help → About Adempiere → System Info**
- Atau: Login sebagai System Administrator → cek di **System Admin → Client**

---

## 4. File-File yang Terlibat

```
project/
├── .env                                        ← Konfigurasi environment
├── config/
│   └── adempiere.php                           ← Config SOAP & service types
├── app/
│   ├── Services/
│   │   └── AdempiereService.php                ← SOAP client utama
│   ├── Console/
│   │   └── Commands/
│   │       └── TestAdempiereConnection.php     ← Artisan command test koneksi
│   └── Http/
│       └── Controllers/
│           ├── Warehouse/
│           │   └── WarehouseController.php     ← Inventory, Receiving, Issuing
│           ├── Procurement/
│           │   └── ProcurementController.php   ← Purchase Request
│           └── HR/
│               └── HRController.php            ← Employee list
└── Dockerfile                                  ← Sudah include extension soap
```

### `config/adempiere.php`
Menyimpan semua konfigurasi koneksi. Konstanta PHP SOAP (`WSDL_CACHE_DISK`, dll.) **tidak** diletakkan di sini karena config file di-load sebelum pengecekan extension, yang dapat menyebabkan fatal error jika extension belum aktif.

### `app/Services/AdempiereService.php`
Kelas utama yang menangani semua komunikasi SOAP dengan Adempiere. Menyediakan method:

| Method | Fungsi | Tabel Adempiere |
|---|---|---|
| `isConnected()` | Test koneksi & login | ADService |
| `getProducts()` | Ambil daftar produk | M_Product |
| `getStockOnHand()` | Ambil stok per lokasi | M_StorageOnHand |
| `getStockMovements()` | Riwayat pergerakan stok | M_InOut |
| `createMaterialReceipt()` | Buat dokumen penerimaan | M_InOut (V+) |
| `createMaterialIssue()` | Buat dokumen pengeluaran | M_InOut (C-) |
| `getRequisitions()` | Ambil daftar PR | M_Requisition |
| `createRequisition()` | Buat Purchase Request | M_Requisition |
| `getPurchaseOrders()` | Ambil Purchase Orders | C_Order |
| `getEmployees()` | Ambil data karyawan | C_BPartner |
| `getVendors()` | Ambil data vendor | C_BPartner |
| `queryData()` | Query umum (low-level) | Any table |
| `createData()` | Create umum (low-level) | Any table |

---

## 5. Cara Kerja DEMO_MODE

```
DEMO_MODE=true  (default)
    └── Semua controller menggunakan data dummy (hardcoded array)
    └── Tidak ada koneksi ke Adempiere
    └── Cocok untuk demo/presentasi tanpa Adempiere

DEMO_MODE=false
    └── Controller memanggil AdempiereService
    └── Data diambil real-time dari Adempiere via SOAP
    └── Jika Adempiere tidak dapat diakses → fallback ke dummy data
    └── Error dicatat di Laravel log (storage/logs/laravel.log)
```

### Logika di setiap Controller

```php
// Contoh pola yang dipakai di semua controller:

private function isDemo(): bool
{
    return env('DEMO_MODE', 'true') === 'true';
}

public function inventory(Request $request)
{
    if ($this->isDemo()) {
        $inventory = $this->getDummyInventory(); // data hardcoded
    } else {
        try {
            $inventory = $this->adempiere()->getProducts(); // dari Adempiere
        } catch (\Throwable $e) {
            Log::warning('Fallback ke dummy: ' . $e->getMessage());
            $inventory = $this->getDummyInventory(); // fallback
        }
    }
    // ...
}
```

---

## 6. Setup Adempiere (Sisi Server)

### 6.1 Verifikasi ADInterface Aktif

Buka browser dan akses URL berikut. Jika berhasil, akan tampil daftar operasi WSDL:

```
http://localhost:8080/ADInterface/services/ADService?wsdl
http://localhost:8080/ADInterface/services/ModelADService?wsdl
http://localhost:8080/ADInterface/services/WebService?wsdl
```

Di versi Adempiere lama (XFire), listing service terlihat seperti:
```
Available Services:
  ADService [wsdl]
  ModelADService [wsdl]
  WebService [wsdl]
  ExternalSales [wsdl]
Generated by XFire (http://xfire.codehaus.org)
```

### 6.2 Konfigurasi Web Service Types

Web Service Type mendefinisikan operasi apa yang diizinkan via SOAP. Konfigurasi dilakukan melalui **Adempiere Application Dictionary** (bukan direct DB access).

**Cara akses:**

1. Login ke Adempiere sebagai **System Administrator**
2. Buka menu: **Application Dictionary → Window, Tab & Field**
3. Di search box, ketik: `Web Service Type`
4. Atau cari di: **System Admin → General Rules → Web Service**

**Daftar Web Service Type yang harus dibuat:**

| Name | Table Name | Action | Keterangan |
|---|---|---|---|
| `GetProductList` | `M_Product` | Read | Daftar produk aktif |
| `GetStockList` | `M_StorageOnHand` | Read | Stok per lokasi |
| `GetMovementList` | `M_InOut` | Read | Semua pergerakan barang |
| `CreateReceipt` | `M_InOut` | Create | Buat dokumen penerimaan |
| `CreateIssue` | `M_InOut` | Create | Buat dokumen pengeluaran |
| `GetRequisitionList` | `M_Requisition` | Read | Daftar Purchase Request |
| `CreateRequisition` | `M_Requisition` | Create | Buat Purchase Request |
| `GetPOList` | `C_Order` | Read | Daftar Purchase Order |
| `GetEmployeeList` | `C_BPartner` | Read | Daftar karyawan (IsEmployee=Y) |
| `GetVendorList` | `C_BPartner` | Read | Daftar vendor (IsVendor=Y) |

> **Catatan:** Nama di kolom "Name" harus **sama persis** (case-sensitive) dengan nilai di `.env` (variabel `ADEMPIERE_ST_*`).

### 6.3 Buat API User (Opsional tapi Disarankan)

Disarankan membuat user Adempiere khusus untuk integrasi API (bukan menggunakan Admin):

1. **System Admin → Security → User**
2. Buat user baru: misal `api_laravel`
3. Assign role yang punya akses ke menu: Inventory, Purchasing, HR
4. Gunakan credential user ini di `.env` (`ADEMPIERE_USERNAME` / `ADEMPIERE_PASSWORD`)

---

## 7. Setup Laravel (Sisi Aplikasi)

### 7.1 Pastikan soap Extension Aktif

```bash
php -m | grep soap
# Output yang diharapkan: soap
```

Jika tidak muncul, edit `php.ini` dan aktifkan `extension=soap`, lalu restart web server.

### 7.2 Clear Config Cache

Setelah mengubah `.env` atau `config/adempiere.php`:

```bash
php artisan config:clear
php artisan cache:clear
```

### 7.3 Set DEMO_MODE

```bash
# Di .env:
DEMO_MODE=false   # aktifkan koneksi ke Adempiere
DEMO_MODE=true    # kembali ke mode demo (dummy data)
```

---

## 8. Test Koneksi

Artisan command tersedia untuk memverifikasi koneksi tanpa harus mengubah `DEMO_MODE`:

```bash
# Test dasar: cek WSDL endpoint + login
php artisan adempiere:test

# Tampilkan semua operasi yang tersedia di setiap WSDL
php artisan adempiere:test --wsdl

# Test lengkap termasuk query data M_Product
php artisan adempiere:test --query
```

**Contoh output sukses:**
```
┌─────────────────────────────────────────────────────────┐
│        Adempiere SOAP Connection Test                   │
└─────────────────────────────────────────────────────────┘

📋 Konfigurasi:
+-------------+------------------------------------------+
| Key         | Value                                    |
+-------------+------------------------------------------+
| base_url    | http://localhost:8080/ADInterface/services |
| username    | Admin                                    |
| ...         | ...                                      |

🔌 Mengecek WSDL endpoint...
  ✓ ADService       — http://localhost:8080/ADInterface/services/ADService?wsdl
  ✓ ModelADService  — http://localhost:8080/ADInterface/services/ModelADService?wsdl
  ✓ WebService      — http://localhost:8080/ADInterface/services/WebService?wsdl

🔑 Mencoba login ke ADService...
  ✓ Login BERHASIL — result: Success

┌─────────────────────────────────────────────────────────┐
│  ✅  Koneksi ke Adempiere BERHASIL                      │
└─────────────────────────────────────────────────────────┘
```

---

## 9. Pemetaan Modul ke Tabel Adempiere

### Modul Warehouse

| Fitur Laravel | SOAP Method | Tabel Adempiere | MovementType |
|---|---|---|---|
| Daftar Inventory | `getProducts()` | `M_Product` | - |
| Stok On-Hand | `getStockOnHand()` | `M_StorageOnHand` | - |
| Riwayat Pergerakan | `getStockMovements()` | `M_InOut` | All |
| Penerimaan Barang | `createMaterialReceipt()` | `M_InOut` | `V+` (Vendor Receipt) |
| Pengeluaran Barang | `createMaterialIssue()` | `M_InOut` | `C-` (Customer Shipment) |

### Modul eProcurement

| Fitur Laravel | SOAP Method | Tabel Adempiere | Status Adempiere |
|---|---|---|---|
| Daftar Purchase Request | `getRequisitions()` | `M_Requisition` | DR/IP/AP/CO |
| Buat Purchase Request | `createRequisition()` | `M_Requisition` | DR (Draft) |
| Daftar Purchase Order | `getPurchaseOrders()` | `C_Order` | - |

### Modul HR

| Fitur Laravel | SOAP Method | Tabel Adempiere | Filter |
|---|---|---|---|
| Daftar Karyawan | `getEmployees()` | `C_BPartner` | `IsEmployee='Y'` |
| Daftar Vendor | `getVendors()` | `C_BPartner` | `IsVendor='Y'` |

> **Catatan HR:** Jika Adempiere menggunakan modul **HR & Payroll**, tabel karyawan berada di `HR_Employee`, bukan `C_BPartner`. Sesuaikan `ADEMPIERE_ST_EMPLOYEE_LIST` service type dengan tabel yang benar.

### DocStatus Mapping (Adempiere → Label Indonesia)

| Kode Adempiere | Label |
|---|---|
| `DR` | Draft |
| `IP` | In Progress |
| `WA` | Waiting Approval |
| `AP` | Approved |
| `CO` | Completed |
| `CL` | Closed |
| `VO` | Voided |
| `RE` | Reversed |

---

## 10. Referensi Error & Solusi

### `A facade root has not been set`
**Penyebab:** Fatal error terjadi sebelum Laravel selesai bootstrap, biasanya karena `config/adempiere.php` menggunakan konstanta SOAP (`WSDL_CACHE_DISK`) saat extension soap belum aktif.

**Solusi:**
1. Aktifkan `extension=soap` di `php.ini`
2. Restart web server (Apache/Nginx)
3. Konstanta SOAP hanya boleh dipakai di dalam method (tidak di config file)

---

### `PHP extension "soap" belum aktif`
**Penyebab:** Extension soap tidak terload.

**Solusi:**
```bash
# Cek php.ini yang dipakai:
php -r "echo php_ini_loaded_file();"

# Edit file tersebut, cari dan ubah:
;extension=soap   →   extension=soap

# Restart Apache/Nginx
```

---

### `SoapFault: Could not connect to host`
**Penyebab:** Adempiere tidak berjalan atau URL salah.

**Solusi:**
1. Pastikan Adempiere berjalan: buka `http://localhost:8080/webui`
2. Pastikan `ADEMPIERE_BASE_URL` di `.env` benar
3. Cek firewall/port `8080` tidak diblokir

---

### `SoapFault: Login Failed` / `result: Error`
**Penyebab:** Credential atau parameter sesi salah.

**Solusi:**
1. Verifikasi `ADEMPIERE_USERNAME` dan `ADEMPIERE_PASSWORD`
2. Cek `ADEMPIERE_CLIENT_ID`, `ADEMPIERE_ROLE_ID`, `ADEMPIERE_ORG_ID`, `ADEMPIERE_WAREHOUSE_ID`
3. Pastikan user tersebut aktif dan role-nya punya akses

---

### `Service type not found` / Data kosong
**Penyebab:** Web Service Type belum dikonfigurasi di Adempiere.

**Solusi:**
1. Masuk Adempiere sebagai System Administrator
2. Buka **Application Dictionary → Web Service Type**
3. Buat entry sesuai tabel di [bagian 6.2](#62-konfigurasi-web-service-types)
4. Pastikan nama persis sama dengan nilai `ADEMPIERE_ST_*` di `.env`

---

### WSDL fetch sangat lambat
**Penyebab:** WSDL di-download setiap request karena cache dinonaktifkan.

**Solusi:**
WSDL di-cache otomatis di disk (`WSDL_CACHE_DISK = 1`). Jika ingin reset cache WSDL:
```bash
# Hapus cache WSDL (lokasi default di php.ini: soap.wsdl_cache_dir)
# Windows XAMPP biasanya di: C:/Windows/Temp atau folder tmp

php artisan cache:clear   # clear Laravel cache
```

---

*Dokumentasi ini dibuat pada sesi development integrasi Adempiere SOAP untuk WHR-ePIS.*
*Versi Adempiere: Classic (XFire-based ADInterface)*
*Versi Laravel: 12.x | PHP: 8.2+*
