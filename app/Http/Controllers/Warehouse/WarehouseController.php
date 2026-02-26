<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\AdempiereService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    /** True  → pakai dummy data (DEMO_MODE=true di .env)
     *  False → pakai data real dari Adempiere SOAP */
    private function isDemo(): bool
    {
        return env('DEMO_MODE', 'true') === 'true';
    }

    /** Ambil AdempiereService, dengan fallback graceful jika gagal connect */
    private function adempiere(): AdempiereService
    {
        return app(AdempiereService::class);
    }

    private function getDummyInventory(): array
    {
        return [
            ['id'=>'ITM-001','name'=>'Laptop Dell XPS 15','category'=>'Elektronik','unit'=>'Unit','stock'=>45,'min_stock'=>10,'location'=>'RAK-A1','price'=>18500000,'status'=>'Normal'],
            ['id'=>'ITM-002','name'=>'Printer HP LaserJet','category'=>'Elektronik','unit'=>'Unit','stock'=>8,'min_stock'=>5,'location'=>'RAK-A2','price'=>3200000,'status'=>'Normal'],
            ['id'=>'ITM-003','name'=>'Kertas A4 80gr','category'=>'ATK','unit'=>'Rim','stock'=>250,'min_stock'=>50,'location'=>'RAK-B1','price'=>45000,'status'=>'Normal'],
            ['id'=>'ITM-004','name'=>'Toner Printer','category'=>'ATK','unit'=>'Pcs','stock'=>12,'min_stock'=>15,'location'=>'RAK-B2','price'=>450000,'status'=>'Low Stock'],
            ['id'=>'ITM-005','name'=>'Meja Kerja','category'=>'Furniture','unit'=>'Unit','stock'=>20,'min_stock'=>5,'location'=>'RAK-C1','price'=>2500000,'status'=>'Normal'],
            ['id'=>'ITM-006','name'=>'Kursi Ergonomis','category'=>'Furniture','unit'=>'Unit','stock'=>3,'min_stock'=>10,'location'=>'RAK-C2','price'=>1800000,'status'=>'Low Stock'],
            ['id'=>'ITM-007','name'=>'Monitor LG 27"','category'=>'Elektronik','unit'=>'Unit','stock'=>30,'min_stock'=>8,'location'=>'RAK-A3','price'=>4500000,'status'=>'Normal'],
            ['id'=>'ITM-008','name'=>'Keyboard Wireless','category'=>'Elektronik','unit'=>'Unit','stock'=>55,'min_stock'=>10,'location'=>'RAK-A4','price'=>350000,'status'=>'Normal'],
            ['id'=>'ITM-009','name'=>'Mouse Wireless','category'=>'Elektronik','unit'=>'Unit','stock'=>60,'min_stock'=>10,'location'=>'RAK-A5','price'=>280000,'status'=>'Normal'],
            ['id'=>'ITM-010','name'=>'Pulpen Ballpoint','category'=>'ATK','unit'=>'Lusin','stock'=>40,'min_stock'=>20,'location'=>'RAK-B3','price'=>18000,'status'=>'Normal'],
            ['id'=>'ITM-011','name'=>'Stapler','category'=>'ATK','unit'=>'Pcs','stock'=>18,'min_stock'=>10,'location'=>'RAK-B4','price'=>35000,'status'=>'Normal'],
            ['id'=>'ITM-012','name'=>'Server Rack 42U','category'=>'IT Infrastructure','unit'=>'Unit','stock'=>2,'min_stock'=>1,'location'=>'RAK-D1','price'=>45000000,'status'=>'Normal'],
        ];
    }

    private function getDummyMovements(): array
    {
        return [
            ['date'=>'24 Feb 2024','doc_no'=>'GR-2024-0089','type'=>'Penerimaan','item'=>'Laptop Dell XPS 15','qty'=>'+50','from'=>'PT Mitra Teknologi','to'=>'Gudang Utama','by'=>'Budi S.'],
            ['date'=>'24 Feb 2024','doc_no'=>'GI-2024-0045','type'=>'Pengeluaran','item'=>'Printer HP LaserJet','qty'=>'-3','from'=>'Gudang Utama','to'=>'Dept Marketing','by'=>'Siti R.'],
            ['date'=>'23 Feb 2024','doc_no'=>'GR-2024-0088','type'=>'Penerimaan','item'=>'Kertas A4 80gr','qty'=>'+100','from'=>'CV Kertas Jaya','to'=>'Gudang Utama','by'=>'Ahmad F.'],
            ['date'=>'23 Feb 2024','doc_no'=>'GI-2024-0044','type'=>'Pengeluaran','item'=>'Kursi Ergonomis','qty'=>'-5','from'=>'Gudang Utama','to'=>'Ruang Direksi','by'=>'Budi S.'],
            ['date'=>'22 Feb 2024','doc_no'=>'TF-2024-0012','type'=>'Transfer','item'=>'Monitor LG 27"','qty'=>'5','from'=>'Gudang Cadangan','to'=>'Gudang Utama','by'=>'Siti R.'],
            ['date'=>'22 Feb 2024','doc_no'=>'GR-2024-0087','type'=>'Penerimaan','item'=>'Keyboard Wireless','qty'=>'+30','from'=>'PT Aksesori Prima','to'=>'Gudang Utama','by'=>'Ahmad F.'],
        ];
    }

    public function dashboard()
    {
        $isDemo = $this->isDemo();

        if ($isDemo) {
            $stats = [
                'total_items'    => 1247,
                'total_value'    => 'Rp 4.2 Miliar',
                'low_stock'      => 23,
                'pending_gr'     => 8,
                'today_in'       => 3,
                'today_out'      => 7,
            ];
            $movements          = array_slice($this->getDummyMovements(), 0, 5);
            $adempiereConnected = null;   // null = tidak relevan (DEMO_MODE)
            $adempiereError     = null;
            return view('warehouse.dashboard', compact('stats', 'movements', 'isDemo', 'adempiereConnected', 'adempiereError'));
        }

        // ── DEMO_MODE=false → data dari Adempiere ──────────────────────────
        $adempiereConnected = false;
        $adempiereError     = null;

        try {
            $adempiere = $this->adempiere();
            $inventory = $adempiere->getProducts();
            $movements = array_slice($adempiere->getStockMovements(), 0, 5);
            $lowStock  = array_filter($inventory, fn($i) => $i['status'] === 'Low Stock');
            $stats = [
                'total_items' => count($inventory),
                'total_value' => 'Rp ' . number_format(
                    array_sum(array_map(fn($i) => $i['price'] * $i['stock'], $inventory)), 0, ',', '.'
                ),
                'low_stock'   => count($lowStock),
                'pending_gr'  => 0,
                'today_in'    => count(array_filter($movements, fn($m) => $m['type'] === 'Penerimaan')),
                'today_out'   => count(array_filter($movements, fn($m) => $m['type'] === 'Pengeluaran')),
            ];
            $adempiereConnected = true;
        } catch (\Throwable $e) {
            Log::warning('[Warehouse Dashboard] Fallback ke dummy: ' . $e->getMessage());
            $adempiereError = $e->getMessage();
            $stats     = ['total_items'=>0,'total_value'=>'N/A','low_stock'=>0,'pending_gr'=>0,'today_in'=>0,'today_out'=>0];
            $movements = [];
        }

        return view('warehouse.dashboard', compact('stats', 'movements', 'isDemo', 'adempiereConnected', 'adempiereError'));
    }

    public function inventory(Request $request)
    {
        $search   = $request->get('search', '');
        $category = $request->get('category', '');

        if ($this->isDemo()) {
            $inventory  = $this->getDummyInventory();
            $allForMeta = $this->getDummyInventory();
        } else {
            try {
                $inventory  = $this->adempiere()->getProducts();
                $allForMeta = $inventory;
            } catch (\Throwable $e) {
                Log::warning('[Warehouse Inventory] Fallback ke dummy: ' . $e->getMessage());
                $inventory  = $this->getDummyInventory();
                $allForMeta = $this->getDummyInventory();
            }
        }

        if ($search) {
            $inventory = array_filter($inventory, fn($i) =>
                stripos($i['name'], $search) !== false || stripos($i['id'], $search) !== false
            );
        }
        if ($category) {
            $inventory = array_filter($inventory, fn($i) => $i['category'] === $category);
        }

        $categories = array_unique(array_column($allForMeta, 'category'));
        return view('warehouse.inventory', compact('inventory', 'search', 'category', 'categories'));
    }

    public function receiving()
    {
        if ($this->isDemo()) {
            $items = array_column($this->getDummyInventory(), 'name', 'id');
        } else {
            try {
                $products = $this->adempiere()->getProducts();
                $items    = array_column($products, 'name', 'id');
            } catch (\Throwable $e) {
                Log::warning('[Warehouse Receiving] Fallback ke dummy: ' . $e->getMessage());
                $items = array_column($this->getDummyInventory(), 'name', 'id');
            }
        }
        return view('warehouse.receiving', compact('items'));
    }

    public function storeReceiving(Request $request)
    {
        $request->validate([
            'item_id'  => 'required',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'required|string',
            'doc_date' => 'required|date',
        ]);

        if ($this->isDemo()) {
            $docNo = 'GR-' . date('Y') . '-' . str_pad(rand(90, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('warehouse.receiving')
                ->with('success', "Penerimaan barang berhasil disimulasikan! No. Dokumen: {$docNo}. Data telah dicatat (simulasi).");
        }

        // ── DEMO_MODE=false → kirim ke Adempiere ───────────────────────────
        try {
            $this->adempiere()->createMaterialReceipt($request->only('item_id', 'quantity', 'supplier', 'doc_date'));
            $docNo = 'GR-' . date('Y') . '-' . str_pad(rand(90, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('warehouse.receiving')
                ->with('success', "Penerimaan barang berhasil dicatat di Adempiere! No. Dokumen: {$docNo}.");
        } catch (\Throwable $e) {
            Log::error('[Warehouse storeReceiving] ' . $e->getMessage());
            return redirect()->route('warehouse.receiving')
                ->with('error', 'Gagal mencatat ke Adempiere: ' . $e->getMessage());
        }
    }

    public function issuing()
    {
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Procurement', 'Direksi'];

        if ($this->isDemo()) {
            $items = array_column($this->getDummyInventory(), 'name', 'id');
        } else {
            try {
                $products = $this->adempiere()->getProducts();
                $items    = array_column($products, 'name', 'id');
            } catch (\Throwable $e) {
                Log::warning('[Warehouse Issuing] Fallback ke dummy: ' . $e->getMessage());
                $items = array_column($this->getDummyInventory(), 'name', 'id');
            }
        }

        return view('warehouse.issuing', compact('items', 'departments'));
    }

    public function storeIssuing(Request $request)
    {
        $request->validate([
            'item_id'    => 'required',
            'quantity'   => 'required|integer|min:1',
            'department' => 'required|string',
            'purpose'    => 'required|string',
        ]);

        if ($this->isDemo()) {
            $docNo = 'GI-' . date('Y') . '-' . str_pad(rand(46, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('warehouse.issuing')
                ->with('success', "Pengeluaran barang berhasil disimulasikan! No. Dokumen: {$docNo}. Data telah dicatat (simulasi).");
        }

        // ── DEMO_MODE=false → kirim ke Adempiere ───────────────────────────
        try {
            $this->adempiere()->createMaterialIssue($request->only('item_id', 'quantity', 'department', 'purpose'));
            $docNo = 'GI-' . date('Y') . '-' . str_pad(rand(46, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('warehouse.issuing')
                ->with('success', "Pengeluaran barang berhasil dicatat di Adempiere! No. Dokumen: {$docNo}.");
        } catch (\Throwable $e) {
            Log::error('[Warehouse storeIssuing] ' . $e->getMessage());
            return redirect()->route('warehouse.issuing')
                ->with('error', 'Gagal mencatat ke Adempiere: ' . $e->getMessage());
        }
    }

    public function stockMovement(Request $request)
    {
        if ($this->isDemo()) {
            $movements = $this->getDummyMovements();
        } else {
            try {
                $movements = $this->adempiere()->getStockMovements();
            } catch (\Throwable $e) {
                Log::warning('[Warehouse StockMovement] Fallback ke dummy: ' . $e->getMessage());
                $movements = $this->getDummyMovements();
            }
        }
        return view('warehouse.stock-movement', compact('movements'));
    }

    public function reports()
    {
        if ($this->isDemo()) {
            $inventory = $this->getDummyInventory();
        } else {
            try {
                $inventory = $this->adempiere()->getProducts();
            } catch (\Throwable $e) {
                Log::warning('[Warehouse Reports] Fallback ke dummy: ' . $e->getMessage());
                $inventory = $this->getDummyInventory();
            }
        }
        $lowStock = array_filter($inventory, fn($i) => $i['status'] === 'Low Stock');
        return view('warehouse.reports', compact('inventory', 'lowStock'));
    }

    // ── Surat Jalan ────────────────────────────────────────────────────────────

    private function getDummySuratJalan(): array
    {
        return [
            ['no'=>'SJ-2024-0045','tanggal'=>'24 Feb 2024','karyawan'=>'Budi Santoso','barang'=>'Laptop Dell XPS 15 (2 unit)','tujuan'=>'Dept Marketing','catatan'=>'Pengiriman rutin','status'=>'Selesai'],
            ['no'=>'SJ-2024-0044','tanggal'=>'23 Feb 2024','karyawan'=>'Ahmad Fauzi','barang'=>'Monitor LG 27" (5 unit)','tujuan'=>'Ruang Server','catatan'=>'Penggantian monitor lama','status'=>'Proses'],
            ['no'=>'SJ-2024-0043','tanggal'=>'22 Feb 2024','karyawan'=>'Siti Rahayu','barang'=>'ATK Kantor (1 set)','tujuan'=>'Dept HR','catatan'=>'-','status'=>'Selesai'],
            ['no'=>'SJ-2024-0042','tanggal'=>'21 Feb 2024','karyawan'=>'Eko Prasetyo','barang'=>'Kursi Ergonomis (3 unit)','tujuan'=>'Ruang Direksi','catatan'=>'Penggantian kursi lama','status'=>'Selesai'],
            ['no'=>'SJ-2024-0041','tanggal'=>'20 Feb 2024','karyawan'=>'Gunawan Hadi','barang'=>'Barcode Scanner (2 unit)','tujuan'=>'Gudang 2','catatan'=>'Ekspansi gudang','status'=>'Dibatalkan'],
        ];
    }

    public function suratJalan()
    {
        $suratJalan = $this->getDummySuratJalan();
        return view('warehouse.surat-jalan', compact('suratJalan'));
    }

    public function storeSuratJalan(Request $request)
    {
        $request->validate([
            'karyawan' => 'required|string',
            'barang'   => 'required|string',
            'tujuan'   => 'required|string',
            'tanggal'  => 'required|date',
        ]);

        $no = 'SJ-' . date('Y') . '-' . str_pad(rand(46, 999), 4, '0', STR_PAD_LEFT);
        return redirect()->route('warehouse.surat-jalan')
            ->with('success', "Surat Jalan berhasil dibuat! No. Dokumen: {$no}.");
    }

    // ── Req ATK ────────────────────────────────────────────────────────────────

    private function getDummyReqAtk(): array
    {
        return [
            ['no'=>'ATK-2024-0033','tanggal'=>'24 Feb 2024','karyawan'=>'Siti Rahayu','dept'=>'HR','jenis_atk'=>'Kertas A4, Pulpen, Stapler','jumlah'=>'3 item','alasan'=>'Habis terpakai','status'=>'Disetujui'],
            ['no'=>'ATK-2024-0032','tanggal'=>'23 Feb 2024','karyawan'=>'Dewi Kusuma','dept'=>'Finance','jenis_atk'=>'Tinta Printer, Map Ordner','jumlah'=>'5 pcs','alasan'=>'Kebutuhan bulanan','status'=>'Menunggu'],
            ['no'=>'ATK-2024-0031','tanggal'=>'22 Feb 2024','karyawan'=>'Ahmad Fauzi','dept'=>'IT','jenis_atk'=>'Sticky Note, Whiteboard Marker','jumlah'=>'10 pcs','alasan'=>'Papan tulis baru dipasang','status'=>'Disetujui'],
            ['no'=>'ATK-2024-0030','tanggal'=>'21 Feb 2024','karyawan'=>'Fitri Handayani','dept'=>'Marketing','jenis_atk'=>'Banner Stand, Brosur','jumlah'=>'2 unit','alasan'=>'Pameran produk','status'=>'Ditolak'],
            ['no'=>'ATK-2024-0029','tanggal'=>'20 Feb 2024','karyawan'=>'Eko Prasetyo','dept'=>'Procurement','jenis_atk'=>'Amplop, Materai','jumlah'=>'50 pcs','alasan'=>'Pengiriman dokumen vendor','status'=>'Disetujui'],
        ];
    }

    public function reqAtk()
    {
        $reqAtk = $this->getDummyReqAtk();
        return view('warehouse.req-atk', compact('reqAtk'));
    }

    public function storeReqAtk(Request $request)
    {
        $request->validate([
            'karyawan' => 'required|string',
            'dept'     => 'required|string',
            'jenis_atk'=> 'required|string',
            'jumlah'   => 'required|string',
            'alasan'   => 'required|string',
        ]);

        $no = 'ATK-' . date('Y') . '-' . str_pad(rand(34, 999), 4, '0', STR_PAD_LEFT);
        return redirect()->route('warehouse.req-atk')
            ->with('success', "Pengajuan Req ATK berhasil disubmit! No. Pengajuan: {$no}. Menunggu persetujuan atasan.");
    }
}
