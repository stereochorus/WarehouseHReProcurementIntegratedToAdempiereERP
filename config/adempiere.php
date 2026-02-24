<?php

/*
|--------------------------------------------------------------------------
| Adempiere ERP – SOAP Integration Config
|--------------------------------------------------------------------------
|
| Konfigurasi koneksi ke Adempiere melalui ADInterface SOAP Web Services.
|
| SOAP Endpoints:
|   Login  → {base_url}/CompiereService
|   Data   → {base_url}/ModelADService
|
| WSDL (cek dulu via browser):
|   http://localhost:8080/ADInterface/services/CompiereService?wsdl
|   http://localhost:8080/ADInterface/services/ModelADService?wsdl
|
| Cara menemukan Client ID / Org ID / Role ID / Warehouse ID:
|   Masuk Adempiere → arahkan kursor ke field → lihat tooltip angka ID-nya.
|   Atau: Help > About Adempiere > System Info.
|
| Service Types HARUS dikonfigurasi di Adempiere terlebih dahulu:
|   System Admin > General Rules > Web Service > Web Service Type
|   Buat satu entry per service type di bawah.
|
*/

return [

    // ── Endpoint ──────────────────────────────────────────────────────────────
    'base_url'     => env('ADEMPIERE_BASE_URL', 'http://localhost:8080/ADInterface/services'),

    // ── Kredensial API User (buat user khusus di Adempiere, jangan pakai Admin) ─
    'username'     => env('ADEMPIERE_USERNAME', 'Admin'),
    'password'     => env('ADEMPIERE_PASSWORD', 'Admin'),

    // ── Parameter Sesi (sesuaikan dengan setup Adempiere Anda) ───────────────
    'client_id'    => (int) env('ADEMPIERE_CLIENT_ID', 11),
    'org_id'       => (int) env('ADEMPIERE_ORG_ID', 11),
    'role_id'      => (int) env('ADEMPIERE_ROLE_ID', 102),
    'warehouse_id' => (int) env('ADEMPIERE_WAREHOUSE_ID', 103),
    'language'     => env('ADEMPIERE_LANG', 'en_US'),

    // ── SoapClient options ────────────────────────────────────────────────────
    // CATATAN: konstanta WSDL_CACHE_* tidak didefinisikan di sini karena
    // config file di-load sebelum pengecekan extension soap.
    // Nilai cache_wsdl ditentukan di AdempiereService::makeSoapClient().
    'soap_timeout'  => 10,
    'soap_trace'    => env('APP_DEBUG', false),

    // ── Service Types ─────────────────────────────────────────────────────────
    //
    // Nama-nama berikut HARUS sama persis dengan "Service Type Name" yang
    // sudah Anda buat di Adempiere:
    //   System Admin → General Rules → Web Service → Web Service Type
    //
    // Cara buat di Adempiere:
    //   1. Buka menu Web Service Type
    //   2. Buat record baru, isi Name sesuai nilai di bawah
    //   3. Pilih Table Name yang sesuai
    //   4. Set Action = Read (untuk query) atau Create (untuk insert)
    //   5. Assign role yang punya akses ke tabel tersebut
    //
    'service_types' => [

        // WAREHOUSE ────────────────────────────────────────────────────────────
        // Table: M_Product | Action: Read
        'product_list'       => env('ADEMPIERE_ST_PRODUCT_LIST',    'GetProductList'),

        // Table: M_StorageOnHand | Action: Read  (view stok per lokasi)
        'stock_list'         => env('ADEMPIERE_ST_STOCK_LIST',      'GetStockList'),

        // Table: M_InOut | Action: Read  (filter MovementType = V+ = receipt)
        'receipt_list'       => env('ADEMPIERE_ST_RECEIPT_LIST',    'GetReceiptList'),

        // Table: M_InOut | Action: Read  (filter MovementType = C- = issue)
        'issue_list'         => env('ADEMPIERE_ST_ISSUE_LIST',      'GetIssueList'),

        // Table: M_InOut | Action: Read  (semua movement)
        'movement_list'      => env('ADEMPIERE_ST_MOVEMENT_LIST',   'GetMovementList'),

        // Table: M_InOut | Action: Create  (buat dokumen penerimaan)
        'create_receipt'     => env('ADEMPIERE_ST_CREATE_RECEIPT',  'CreateReceipt'),

        // Table: M_InOut | Action: Create  (buat dokumen pengeluaran)
        'create_issue'       => env('ADEMPIERE_ST_CREATE_ISSUE',    'CreateIssue'),

        // PROCUREMENT ──────────────────────────────────────────────────────────
        // Table: M_Requisition | Action: Read
        'requisition_list'   => env('ADEMPIERE_ST_REQ_LIST',        'GetRequisitionList'),

        // Table: M_Requisition | Action: Create
        'create_requisition' => env('ADEMPIERE_ST_CREATE_REQ',      'CreateRequisition'),

        // Table: C_Order | Action: Read  (Purchase Orders)
        'po_list'            => env('ADEMPIERE_ST_PO_LIST',         'GetPOList'),

        // HR ───────────────────────────────────────────────────────────────────
        // Table: C_BPartner (IsEmployee=Y) | Action: Read
        // Jika pakai modul HR Payroll: gunakan table HR_Employee
        'employee_list'      => env('ADEMPIERE_ST_EMPLOYEE_LIST',   'GetEmployeeList'),

        // Table: C_BPartner (IsVendor=Y) | Action: Read
        'vendor_list'        => env('ADEMPIERE_ST_VENDOR_LIST',     'GetVendorList'),
    ],
];
