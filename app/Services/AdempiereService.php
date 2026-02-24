<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AdempiereService
 * ─────────────────────────────────────────────────────────────────────────────
 * Wrapper SOAP ke Adempiere ADInterface Web Services (versi klasik).
 *
 * SOAP Endpoints yang digunakan:
 *   - {base_url}/CompiereService  → login / verifikasi koneksi
 *   - {base_url}/ModelADService   → query & create data
 *
 * Sebelum pakai service ini, pastikan:
 *   1. PHP extension "soap" sudah aktif (php -m | grep soap)
 *   2. WSDL bisa diakses: http://localhost:8080/ADInterface/services/CompiereService?wsdl
 *   3. Service Types sudah dikonfigurasi di Adempiere (lihat config/adempiere.php)
 *   4. DEMO_MODE=false di .env
 * ─────────────────────────────────────────────────────────────────────────────
 */
class AdempiereService
{
    /** Parameter login yang dikirim ke setiap SOAP request */
    private array $loginParams;

    /** Base URL ADInterface (tanpa trailing slash) */
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('adempiere.base_url'), '/');

        $this->loginParams = [
            'user'        => config('adempiere.username'),
            'pass'        => config('adempiere.password'),
            'lang'        => config('adempiere.language'),
            'ClientID'    => (int) config('adempiere.client_id'),
            'RoleID'      => (int) config('adempiere.role_id'),
            'OrgID'       => (int) config('adempiere.org_id'),
            'WarehouseID' => (int) config('adempiere.warehouse_id'),
            'stage'       => 0,   // required by ADLoginRequest WSDL (xsd:int)
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // KONEKSI & TEST
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Cek apakah koneksi ke Adempiere berhasil.
     * Hasilnya di-cache 5 menit agar tidak hit SOAP setiap request.
     *
     * Versi lama Adempiere (XFire) menggunakan "ADService" untuk login,
     * bukan "CompiereService".
     */
    public function isConnected(): bool
    {
        return Cache::remember('adempiere_connected', 300, function () {
            try {
                $client   = $this->makeSoapClient('ADService');
                // WSDL: ADLoginResponse login(ADLoginRequest $ADLoginRequest)
                // → nama parameter adalah 'ADLoginRequest' (bukan 'in0' atau 'LoginRequest').
                $response = $client->login(['ADLoginRequest' => $this->loginParams]);

                // WSDL mendefinisikan ADLoginResponse dengan field 'status' (xsd:int),
                // BUKAN 'result'. XFire membungkus return value dalam property 'return'.
                // status = 0  → login berhasil
                // status < 0  → login gagal / kredensial salah
                $status = $response->return->status
                       ?? $response->out->status
                       ?? $response->LoginResponse->status
                       ?? $response->status
                       ?? -1;

                return ((int) $status) >= 0;
            } catch (\Throwable $e) {
                Log::warning('[Adempiere] Koneksi gagal: ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Hapus cache status koneksi (panggil saat ganti credential).
     */
    public function clearConnectionCache(): void
    {
        Cache::forget('adempiere_connected');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // WAREHOUSE — PRODUK & STOK
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Ambil daftar produk aktif dari M_Product.
     *
     * Adempiere setup yang diperlukan:
     *   Web Service Type Name : GetProductList   (sesuai .env ADEMPIERE_ST_PRODUCT_LIST)
     *   Table                 : M_Product
     *   Action                : Read
     *
     * @return array<int, array{id:string, name:string, category:string, unit:string, stock:int, min_stock:int, location:string, price:float, status:string}>
     */
    public function getProducts(): array
    {
        $rows = $this->queryData(
            config('adempiere.service_types.product_list'),
            'M_Product'
        );

        return array_map(function (array $r) {
            $stock    = (int) ($r['QtyOnHand']       ?? 0);
            $minStock = (int) ($r['M_Product_ID']    ?? 0); // ganti dengan field min stock sesuai setup Anda
            return [
                'id'        => $r['Value']         ?? $r['M_Product_ID'] ?? '',
                'name'      => $r['Name']           ?? '',
                'category'  => $r['M_Product_Category_ID'] ?? '',
                'unit'      => $r['C_UOM_ID']       ?? 'Pcs',
                'stock'     => $stock,
                'min_stock' => $minStock,
                'location'  => $r['M_Locator_ID']  ?? '-',
                'price'     => (float) ($r['PriceStd'] ?? 0),
                'status'    => $stock <= $minStock ? 'Low Stock' : 'Normal',
            ];
        }, $rows);
    }

    /**
     * Ambil stok on-hand dari M_StorageOnHand.
     *
     * Adempiere setup:
     *   Web Service Type Name : GetStockList   (ADEMPIERE_ST_STOCK_LIST)
     *   Table                 : M_StorageOnHand
     *   Action                : Read
     */
    public function getStockOnHand(): array
    {
        return $this->queryData(
            config('adempiere.service_types.stock_list'),
            'M_StorageOnHand'
        );
    }

    /**
     * Ambil riwayat pergerakan stok (Material Receipts + Issues).
     *
     * Adempiere setup:
     *   Web Service Type Name : GetMovementList   (ADEMPIERE_ST_MOVEMENT_LIST)
     *   Table                 : M_InOut
     *   Action                : Read
     *
     * @return array<int, array{date:string, doc_no:string, type:string, item:string, qty:string, from:string, to:string, by:string}>
     */
    public function getStockMovements(): array
    {
        $rows = $this->queryData(
            config('adempiere.service_types.movement_list'),
            'M_InOut'
        );

        return array_map(function (array $r) {
            $mvType = $r['MovementType'] ?? '';
            $type   = match (true) {
                str_starts_with($mvType, 'V') => 'Penerimaan',
                str_starts_with($mvType, 'C') => 'Pengeluaran',
                default                        => 'Transfer',
            };
            $qty = str_starts_with($mvType, 'V') ? '+' . ($r['MovementQty'] ?? 0) : '-' . ($r['MovementQty'] ?? 0);

            return [
                'date'   => $r['MovementDate'] ?? '',
                'doc_no' => $r['DocumentNo']   ?? '',
                'type'   => $type,
                'item'   => $r['M_Product_ID'] ?? '',
                'qty'    => $qty,
                'from'   => $r['C_BPartner_ID'] ?? '-',
                'to'     => $r['M_Warehouse_ID'] ?? '-',
                'by'     => $r['CreatedBy']      ?? '',
            ];
        }, $rows);
    }

    /**
     * Buat dokumen Material Receipt (penerimaan barang) di Adempiere.
     *
     * Adempiere setup:
     *   Web Service Type Name : CreateReceipt   (ADEMPIERE_ST_CREATE_RECEIPT)
     *   Table                 : M_InOut
     *   Action                : Create
     *
     * @param  array{item_id:string, quantity:int, supplier:string, doc_date:string}  $data
     * @return array  Response dari Adempiere (berisi RecordID dokumen baru)
     */
    public function createMaterialReceipt(array $data): array
    {
        return $this->createData(
            config('adempiere.service_types.create_receipt'),
            'M_InOut',
            [
                'MovementType' => 'V+',
                'MovementDate' => $data['doc_date'],
                'M_Product_ID' => $data['item_id'],
                'MovementQty'  => $data['quantity'],
                'C_BPartner_ID'=> $data['supplier'],
                'DocStatus'    => 'DR', // Draft
            ]
        );
    }

    /**
     * Buat dokumen Material Issue (pengeluaran barang) di Adempiere.
     *
     * Adempiere setup:
     *   Web Service Type Name : CreateIssue   (ADEMPIERE_ST_CREATE_ISSUE)
     *   Table                 : M_InOut
     *   Action                : Create
     *
     * @param  array{item_id:string, quantity:int, department:string, purpose:string}  $data
     */
    public function createMaterialIssue(array $data): array
    {
        return $this->createData(
            config('adempiere.service_types.create_issue'),
            'M_InOut',
            [
                'MovementType'  => 'C-',
                'MovementDate'  => now()->format('Y-m-d'),
                'M_Product_ID'  => $data['item_id'],
                'MovementQty'   => $data['quantity'],
                'Description'   => $data['department'] . ' - ' . $data['purpose'],
                'DocStatus'     => 'DR',
            ]
        );
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PROCUREMENT — REQUISITION & PURCHASE ORDER
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Ambil daftar Purchase Requisition dari M_Requisition.
     *
     * Adempiere setup:
     *   Web Service Type Name : GetRequisitionList   (ADEMPIERE_ST_REQ_LIST)
     *   Table                 : M_Requisition
     *   Action                : Read
     *
     * @return array<int, array{id:string, date:string, dept:string, requestor:string, item:string, qty:int, unit:string, est_price:float, total:float, status:string, priority:string}>
     */
    public function getRequisitions(): array
    {
        $rows = $this->queryData(
            config('adempiere.service_types.requisition_list'),
            'M_Requisition'
        );

        return array_map(function (array $r) {
            $qty   = (int)   ($r['Qty']           ?? 0);
            $price = (float) ($r['PriceActual']   ?? 0);
            return [
                'id'        => $r['DocumentNo']    ?? '',
                'date'      => $r['DateRequired']  ?? '',
                'dept'      => $r['AD_Org_ID']     ?? '',
                'requestor' => $r['CreatedBy']     ?? '',
                'item'      => $r['M_Product_ID']  ?? '',
                'qty'       => $qty,
                'unit'      => $r['C_UOM_ID']      ?? 'Pcs',
                'est_price' => $price,
                'total'     => $qty * $price,
                'status'    => $this->mapDocStatus($r['DocStatus'] ?? 'DR'),
                'priority'  => $r['PriorityRule']  ?? 'Normal',
            ];
        }, $rows);
    }

    /**
     * Buat Purchase Requisition baru di Adempiere.
     *
     * Adempiere setup:
     *   Web Service Type Name : CreateRequisition   (ADEMPIERE_ST_CREATE_REQ)
     *   Table                 : M_Requisition
     *   Action                : Create
     *
     * @param  array{dept:string, item:string, qty:int, unit:string, est_price:float, reason:string}  $data
     */
    public function createRequisition(array $data): array
    {
        return $this->createData(
            config('adempiere.service_types.create_requisition'),
            'M_Requisition',
            [
                'AD_Org_ID'     => $data['dept'],
                'M_Product_ID'  => $data['item'],
                'Qty'           => $data['qty'],
                'C_UOM_ID'      => $data['unit'],
                'PriceActual'   => $data['est_price'],
                'Description'   => $data['reason'],
                'DateRequired'  => now()->addDays(7)->format('Y-m-d'),
                'DocStatus'     => 'DR',
            ]
        );
    }

    /**
     * Ambil daftar Purchase Order dari C_Order.
     *
     * Adempiere setup:
     *   Web Service Type Name : GetPOList   (ADEMPIERE_ST_PO_LIST)
     *   Table                 : C_Order
     *   Action                : Read
     */
    public function getPurchaseOrders(): array
    {
        return $this->queryData(
            config('adempiere.service_types.po_list'),
            'C_Order'
        );
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HR — KARYAWAN & VENDOR
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Ambil daftar karyawan dari C_BPartner (IsEmployee = 'Y').
     *
     * Adempiere setup:
     *   Web Service Type Name : GetEmployeeList   (ADEMPIERE_ST_EMPLOYEE_LIST)
     *   Table                 : C_BPartner
     *   Action                : Read
     *   Filter (atur di Web Service Type): IsEmployee = 'Y'
     *
     * Catatan: Jika Adempiere Anda menggunakan modul HR Payroll,
     *          table-nya adalah HR_Employee, sesuaikan service type-nya.
     *
     * @return array<int, array{id:string, name:string, dept:string, position:string, status:string, join_date:string, salary:float, phone:string, email:string}>
     */
    public function getEmployees(): array
    {
        $rows = $this->queryData(
            config('adempiere.service_types.employee_list'),
            'C_BPartner'
        );

        return array_map(fn(array $r) => [
            'id'        => $r['Value']         ?? $r['C_BPartner_ID'] ?? '',
            'name'      => $r['Name']           ?? '',
            'dept'      => $r['AD_Org_ID']      ?? '',
            'position'  => $r['JobTitle']        ?? '',
            'status'    => ($r['IsActive'] ?? 'Y') === 'Y' ? 'Aktif' : 'Non-Aktif',
            'join_date' => $r['FirstSaleDate']  ?? '',
            'salary'    => 0, // payroll data biasanya di tabel terpisah
            'phone'     => $r['Phone']           ?? '',
            'email'     => $r['EMail']           ?? '',
        ], $rows);
    }

    /**
     * Ambil daftar vendor dari C_BPartner (IsVendor = 'Y').
     *
     * Adempiere setup:
     *   Web Service Type Name : GetVendorList   (ADEMPIERE_ST_VENDOR_LIST)
     *   Table                 : C_BPartner
     *   Action                : Read
     *   Filter: IsVendor = 'Y'
     */
    public function getVendors(): array
    {
        return $this->queryData(
            config('adempiere.service_types.vendor_list'),
            'C_BPartner'
        );
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SOAP CORE — internal
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Query (Read) data dari Adempiere melalui ModelADService.queryData.
     *
     * @param  string  $serviceType  Harus sesuai Web Service Type Name di Adempiere
     * @param  string  $tableName    Nama tabel Adempiere
     * @param  array   $filters      ['NamaKolom' => 'nilai'] — opsional
     * @return array<int, array<string, mixed>>
     * @throws \RuntimeException jika SOAP call gagal
     */
    public function queryData(string $serviceType, string $tableName, array $filters = []): array
    {
        try {
            $client = $this->makeSoapClient('ModelADService');

            // Build filter fields
            $fieldList = [];
            foreach ($filters as $col => $val) {
                $fieldList[] = ['column' => $col, 'val' => (string) $val];
            }

            // WSDL: WindowTabData queryData(ModelCRUDRequest $ModelCRUDRequest)
            // → nama parameter adalah 'ModelCRUDRequest'.
            // ModelCRUD memerlukan: serviceType, TableName, RecordID, Filter,
            //   RetriveResultAs (Attribute|Element), Action (Create|Read|Update|Delete),
            //   PageNo, DataRow (opsional).
            $request = [
                'ModelCRUDRequest' => [
                    'ModelCRUD'      => [
                        'serviceType'      => $serviceType,
                        'TableName'        => $tableName,
                        'RecordID'         => 0,
                        'Filter'           => '',
                        'RetriveResultAs'  => 'Element',
                        'Action'           => 'Read',
                        'PageNo'           => 0,
                        'DataRow'          => ['field' => $fieldList],
                    ],
                    'ADLoginRequest' => $this->loginParams,
                ],
            ];

            $response = $client->queryData($request);
            return $this->parseDataSet($response);

        } catch (\Throwable $e) {
            Log::error("[Adempiere] queryData [{$tableName}] gagal: " . $e->getMessage());
            throw new \RuntimeException("Adempiere queryData gagal: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create data baru di Adempiere melalui ModelADService.createData.
     *
     * @param  string  $serviceType  Harus sesuai Web Service Type Name di Adempiere
     * @param  string  $tableName    Nama tabel Adempiere
     * @param  array   $fields       ['NamaKolom' => 'nilai']
     * @return array   Response mentah dari Adempiere (berisi RecordID baru jika sukses)
     * @throws \RuntimeException jika SOAP call gagal
     */
    public function createData(string $serviceType, string $tableName, array $fields): array
    {
        try {
            $client = $this->makeSoapClient('ModelADService');

            $fieldList = array_map(
                fn($col, $val) => ['column' => $col, 'val' => (string) $val],
                array_keys($fields),
                array_values($fields)
            );

            // WSDL: StandardResponse createData(ModelCRUDRequest $ModelCRUDRequest)
            // → nama parameter adalah 'ModelCRUDRequest'.
            $request = [
                'ModelCRUDRequest' => [
                    'ModelCRUD'      => [
                        'serviceType'      => $serviceType,
                        'TableName'        => $tableName,
                        'RecordID'         => 0,
                        'Filter'           => '',
                        'RetriveResultAs'  => 'Element',
                        'Action'           => 'Create',
                        'PageNo'           => 0,
                        'DataRow'          => ['field' => $fieldList],
                    ],
                    'ADLoginRequest' => $this->loginParams,
                ],
            ];

            $response = $client->createData($request);
            return json_decode(json_encode($response), true) ?? [];

        } catch (\Throwable $e) {
            Log::error("[Adempiere] createData [{$tableName}] gagal: " . $e->getMessage());
            throw new \RuntimeException("Adempiere createData gagal: " . $e->getMessage(), 0, $e);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HELPER — internal
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Buat SoapClient untuk service tertentu (CompiereService atau ModelADService).
     */
    private function makeSoapClient(string $service): \SoapClient
    {
        if (!extension_loaded('soap')) {
            throw new \RuntimeException(
                'PHP extension "soap" belum aktif. ' .
                'Aktifkan dengan menambahkan "extension=soap" di php.ini, lalu restart server.'
            );
        }

        $wsdl = "{$this->baseUrl}/{$service}?wsdl";

        // WSDL_CACHE_DISK = 1, WSDL_CACHE_NONE = 0
        // Konstanta ini hanya tersedia jika extension soap aktif,
        // maka tidak boleh dipakai di luar method ini.
        $cacheMode = defined('WSDL_CACHE_DISK') ? WSDL_CACHE_DISK : 1;

        return new \SoapClient($wsdl, [
            'trace'           => config('adempiere.soap_trace', false),
            'exceptions'      => true,
            'cache_wsdl'      => $cacheMode,
            'connection_timeout' => config('adempiere.soap_timeout', 10),
        ]);
    }

    /**
     * Parse response SOAP dari Adempiere menjadi array of rows.
     *
     * Adempiere mengembalikan struktur:
     *   ModelCRUDResponse → DataSet → DataRow[] → field[]
     * Setiap field memiliki 'column' dan 'val'.
     */
    private function parseDataSet(mixed $response): array
    {
        if (!$response) {
            return [];
        }

        // Konversi object SOAP → array
        $data = json_decode(json_encode($response), true);

        // XFire membungkus return value dalam 'return'.
        // Coba beberapa path: return > ModelCRUDResponse > DataSet > DataRow
        $dataRows = $data['return']['ModelCRUDResponse']['DataSet']['DataRow']
                 ?? $data['out']['ModelCRUDResponse']['DataSet']['DataRow']
                 ?? $data['ModelCRUDResponse']['DataSet']['DataRow']
                 ?? [];

        if (empty($dataRows)) {
            return [];
        }

        // Normalisasi: single row vs multiple rows
        // Single row: array asosiatif dengan key 'field'
        // Multiple rows: array numerik, tiap elemen punya 'field'
        if (isset($dataRows['field'])) {
            $dataRows = [$dataRows];
        }

        return array_map(function (array $row) {
            $result = [];
            $fields = $row['field'] ?? [];

            // Normalisasi single field vs multiple fields
            if (isset($fields['column'])) {
                $fields = [$fields];
            }

            foreach ($fields as $field) {
                $result[$field['column']] = $field['val'] ?? null;
            }

            return $result;
        }, $dataRows);
    }

    /**
     * Map Adempiere DocStatus code ke label Indonesia.
     */
    private function mapDocStatus(string $docStatus): string
    {
        return match ($docStatus) {
            'DR'    => 'Draft',
            'IP'    => 'In Progress',
            'WA'    => 'Waiting Approval',
            'AP'    => 'Approved',
            'CO'    => 'Completed',
            'CL'    => 'Closed',
            'VO'    => 'Voided',
            'RE'    => 'Reversed',
            'IN'    => 'In Progress',
            default => $docStatus,
        };
    }
}
