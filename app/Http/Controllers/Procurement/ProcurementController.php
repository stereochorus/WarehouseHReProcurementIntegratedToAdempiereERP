<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Services\AdempiereService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ProcurementController extends Controller
{
    private function isDemo(): bool
    {
        return env('DEMO_MODE', 'true') === 'true';
    }

    private function adempiere(): AdempiereService
    {
        return app(AdempiereService::class);
    }

    private function getDummyPRs(): array
    {
        return [
            ['id'=>'PR-2024-0160','date'=>'24 Feb 2024','dept'=>'IT','requestor'=>'Ahmad Fauzi','item'=>'Server Rack 42U','qty'=>2,'unit'=>'Unit','est_price'=>85000000,'total'=>170000000,'status'=>'Pending Manager','priority'=>'Tinggi'],
            ['id'=>'PR-2024-0159','date'=>'23 Feb 2024','dept'=>'Operations','requestor'=>'Gunawan Hadi','item'=>'Forklift Electric','qty'=>1,'unit'=>'Unit','est_price'=>180000000,'total'=>180000000,'status'=>'Pending Finance','priority'=>'Normal'],
            ['id'=>'PR-2024-0158','date'=>'23 Feb 2024','dept'=>'Marketing','requestor'=>'Fitri Handayani','item'=>'Laptop MacBook Pro','qty'=>3,'unit'=>'Unit','est_price'=>28000000,'total'=>84000000,'status'=>'Pending Purchasing','priority'=>'Normal'],
            ['id'=>'PR-2024-0157','date'=>'22 Feb 2024','dept'=>'HR','requestor'=>'Siti Rahayu','item'=>'Seragam Karyawan','qty'=>200,'unit'=>'Pcs','est_price'=>350000,'total'=>70000000,'status'=>'Approved','priority'=>'Normal'],
            ['id'=>'PR-2024-0156','date'=>'22 Feb 2024','dept'=>'Finance','requestor'=>'Dewi Kusuma','item'=>'ATK Kantor','qty'=>50,'unit'=>'Set','est_price'=>90000,'total'=>4500000,'status'=>'Approved','priority'=>'Rendah'],
            ['id'=>'PR-2024-0155','date'=>'21 Feb 2024','dept'=>'Warehouse','requestor'=>'Budi Santoso','item'=>'Barcode Scanner','qty'=>5,'unit'=>'Unit','est_price'=>2500000,'total'=>12500000,'status'=>'Rejected','priority'=>'Normal'],
            ['id'=>'PR-2024-0154','date'=>'20 Feb 2024','dept'=>'IT','requestor'=>'Ahmad Fauzi','item'=>'Switch Cisco 24 Port','qty'=>3,'unit'=>'Unit','est_price'=>8500000,'total'=>25500000,'status'=>'Approved','priority'=>'Tinggi'],
        ];
    }

    public function dashboard()
    {
        if ($this->isDemo()) {
            $stats = [
                'total_pr'         => 160,
                'pending_approval' => 14,
                'approved'         => 38,
                'rejected'         => 8,
                'total_value'      => 'Rp 2.847.500.000',
                'this_month_value' => 'Rp 547.000.000',
            ];
            $recentPRs   = array_slice($this->getDummyPRs(), 0, 5);
            $approvalPRs = array_filter($this->getDummyPRs(), fn($p) => str_starts_with($p['status'], 'Pending'));
            return view('procurement.dashboard', compact('stats', 'recentPRs', 'approvalPRs'));
        }

        // ── DEMO_MODE=false → data dari Adempiere ──────────────────────────
        try {
            $allPRs      = $this->adempiere()->getRequisitions();
            $pending     = array_filter($allPRs, fn($p) => str_starts_with($p['status'] ?? '', 'Waiting') || $p['status'] === 'Draft');
            $approved    = array_filter($allPRs, fn($p) => $p['status'] === 'Approved' || $p['status'] === 'Completed');
            $totalValue  = array_sum(array_column($allPRs, 'total'));
            $stats = [
                'total_pr'         => count($allPRs),
                'pending_approval' => count($pending),
                'approved'         => count($approved),
                'rejected'         => 0,
                'total_value'      => 'Rp ' . number_format($totalValue, 0, ',', '.'),
                'this_month_value' => 'Rp -',
            ];
            $recentPRs   = array_slice($allPRs, 0, 5);
            $approvalPRs = $pending;
        } catch (\Throwable $e) {
            Log::warning('[Procurement Dashboard] Fallback ke dummy: ' . $e->getMessage());
            $stats = ['total_pr'=>0,'pending_approval'=>0,'approved'=>0,'rejected'=>0,'total_value'=>'N/A','this_month_value'=>'N/A'];
            $recentPRs = $approvalPRs = [];
        }

        return view('procurement.dashboard', compact('stats', 'recentPRs', 'approvalPRs'));
    }

    public function purchaseRequests(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        if ($this->isDemo()) {
            $prs    = $this->getDummyPRs();
            $allPRs = $this->getDummyPRs();
        } else {
            try {
                $prs    = $this->adempiere()->getRequisitions();
                $allPRs = $prs;
            } catch (\Throwable $e) {
                Log::warning('[Procurement PRs] Fallback ke dummy: ' . $e->getMessage());
                $prs    = $this->getDummyPRs();
                $allPRs = $this->getDummyPRs();
            }
        }

        if ($search) {
            $prs = array_filter($prs, fn($p) =>
                stripos($p['item'], $search) !== false || stripos($p['id'], $search) !== false
            );
        }
        if ($status) {
            $prs = array_filter($prs, fn($p) => $p['status'] === $status);
        }

        $statuses = array_unique(array_column($allPRs, 'status'));
        return view('procurement.purchase-requests', compact('prs', 'search', 'status', 'statuses'));
    }

    public function createPR()
    {
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Procurement', 'Warehouse'];
        return view('procurement.pr-form', compact('departments'));
    }

    public function storePR(Request $request)
    {
        $request->validate([
            'dept'      => 'required|string',
            'item'      => 'required|string',
            'qty'       => 'required|integer|min:1',
            'unit'      => 'required|string',
            'est_price' => 'required|numeric|min:1',
            'reason'    => 'required|string',
        ]);

        $total = number_format($request->qty * $request->est_price, 0, ',', '.');

        if ($this->isDemo()) {
            $prId = 'PR-' . date('Y') . '-' . str_pad(rand(161, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('procurement.purchase-requests')
                ->with('success', "Purchase Request berhasil dibuat! No. PR: {$prId}, Total: Rp {$total} (simulasi). Status: Pending Manager Approval.");
        }

        // ── DEMO_MODE=false → buat Requisition di Adempiere ───────────────
        try {
            $this->adempiere()->createRequisition($request->only('dept', 'item', 'qty', 'unit', 'est_price', 'reason'));
            $prId = 'PR-' . date('Y') . '-' . str_pad(rand(161, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('procurement.purchase-requests')
                ->with('success', "Purchase Request berhasil dibuat di Adempiere! Total: Rp {$total}. Status: Draft.");
        } catch (\Throwable $e) {
            Log::error('[Procurement storePR] ' . $e->getMessage());
            return redirect()->route('procurement.purchase-requests')
                ->with('error', 'Gagal membuat Requisition di Adempiere: ' . $e->getMessage());
        }
    }

    public function approvals()
    {
        $user = Session::get('demo_user');

        if ($this->isDemo()) {
            $pendingPRs = array_filter($this->getDummyPRs(), fn($p) => str_starts_with($p['status'], 'Pending'));
        } else {
            try {
                $all        = $this->adempiere()->getRequisitions();
                $pendingPRs = array_filter($all, fn($p) => in_array($p['status'] ?? '', ['Draft', 'Waiting Approval', 'In Progress']));
            } catch (\Throwable $e) {
                Log::warning('[Procurement Approvals] Fallback ke dummy: ' . $e->getMessage());
                $pendingPRs = array_filter($this->getDummyPRs(), fn($p) => str_starts_with($p['status'], 'Pending'));
            }
        }

        return view('procurement.approvals', compact('pendingPRs', 'user'));
    }

    public function processApproval(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'nullable|string',
        ]);

        $action  = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        $message = "PR {$id} berhasil {$action}";
        if ($request->notes) {
            $message .= ". Catatan: {$request->notes}";
        }

        return redirect()->route('procurement.approvals')
            ->with('success', $message . ' (simulasi).');
    }

    public function reports()
    {
        if ($this->isDemo()) {
            $prs = $this->getDummyPRs();
        } else {
            try {
                $prs = $this->adempiere()->getRequisitions();
            } catch (\Throwable $e) {
                Log::warning('[Procurement Reports] Fallback ke dummy: ' . $e->getMessage());
                $prs = $this->getDummyPRs();
            }
        }
        return view('procurement.reports', compact('prs'));
    }

    // ─── MATERIAL REQUEST (MR) ─────────────────────────────────────
    private function getDummyMR(): array
    {
        return [
            ['no'=>'MR-2024-0025','tanggal'=>'24 Feb 2024','pemohon'=>'Budi Santoso','dept'=>'Warehouse','barang'=>'Pallet Kayu','jumlah'=>'50 unit','alasan'=>'Stok gudang habis','status'=>'Disetujui','prioritas'=>'Tinggi'],
            ['no'=>'MR-2024-0024','tanggal'=>'23 Feb 2024','pemohon'=>'Ahmad Fauzi','dept'=>'IT','barang'=>'Kabel LAN Cat6','jumlah'=>'200 meter','alasan'=>'Perluasan jaringan ruang server','status'=>'Menunggu','prioritas'=>'Normal'],
            ['no'=>'MR-2024-0023','tanggal'=>'22 Feb 2024','pemohon'=>'Gunawan Hadi','dept'=>'Operations','barang'=>'Helm Safety & Rompi K3','jumlah'=>'30 pcs','alasan'=>'Standar keselamatan baru','status'=>'Disetujui','prioritas'=>'Tinggi'],
            ['no'=>'MR-2024-0022','tanggal'=>'21 Feb 2024','pemohon'=>'Siti Rahayu','dept'=>'HR','barang'=>'Formulir Rekrutmen (cetak)','jumlah'=>'500 lembar','alasan'=>'Rekrutmen batch Q1','status'=>'Ditolak','prioritas'=>'Rendah'],
            ['no'=>'MR-2024-0021','tanggal'=>'20 Feb 2024','pemohon'=>'Dewi Kusuma','dept'=>'Finance','barang'=>'Brankas Arsip A4','jumlah'=>'2 unit','alasan'=>'Penyimpanan dokumen keuangan','status'=>'Disetujui','prioritas'=>'Normal'],
        ];
    }

    public function materialRequest()
    {
        $mrs = $this->getDummyMR();
        return view('procurement.material-request', compact('mrs'));
    }

    public function storeMR(Request $request)
    {
        $request->validate([
            'pemohon'   => 'required|string',
            'dept'      => 'required|string',
            'barang'    => 'required|string',
            'jumlah'    => 'required|string',
            'alasan'    => 'required|string',
            'prioritas' => 'required|in:Rendah,Normal,Tinggi',
        ]);
        $no = 'MR-' . date('Y') . '-' . str_pad(rand(26, 999), 4, '0', STR_PAD_LEFT);
        return redirect()->route('procurement.material-request')
            ->with('success', "Material Request berhasil dibuat! No. MR: {$no}. Status: Menunggu persetujuan Manager.");
    }

    // ─── PURCHASE ORDER (PO) ───────────────────────────────────────
    private function getDummyPO(): array
    {
        return [
            ['no'=>'PO-2024-0078','tanggal'=>'24 Feb 2024','vendor'=>'PT Mitra Teknologi','barang'=>'Server HPE ProLiant DL380','jumlah'=>1,'satuan'=>'unit','harga_satuan'=>85000000,'total'=>85000000,'status'=>'Diterima','tgl_kirim'=>'28 Feb 2024'],
            ['no'=>'PO-2024-0077','tanggal'=>'22 Feb 2024','vendor'=>'CV Kertas Jaya','barang'=>'Kertas A4 80gr','jumlah'=>100,'satuan'=>'rim','harga_satuan'=>48000,'total'=>4800000,'status'=>'Dikirim','tgl_kirim'=>'25 Feb 2024'],
            ['no'=>'PO-2024-0076','tanggal'=>'21 Feb 2024','vendor'=>'PT Aksesori Prima','barang'=>'Keyboard & Mouse Wireless Set','jumlah'=>30,'satuan'=>'set','harga_satuan'=>650000,'total'=>19500000,'status'=>'Diproses','tgl_kirim'=>'01 Mar 2024'],
            ['no'=>'PO-2024-0075','tanggal'=>'20 Feb 2024','vendor'=>'UD Furniture Mandiri','barang'=>'Kursi Ergonomis','jumlah'=>10,'satuan'=>'unit','harga_satuan'=>1900000,'total'=>19000000,'status'=>'Draft','tgl_kirim'=>'-'],
            ['no'=>'PO-2024-0074','tanggal'=>'19 Feb 2024','vendor'=>'PT Safety Indonesia','barang'=>'Helm Safety & Rompi K3','jumlah'=>30,'satuan'=>'pcs','harga_satuan'=>185000,'total'=>5550000,'status'=>'Diterima','tgl_kirim'=>'22 Feb 2024'],
        ];
    }

    private function getDummyVendors(): array
    {
        return ['PT Mitra Teknologi','CV Kertas Jaya','PT Aksesori Prima','UD Furniture Mandiri','PT Safety Indonesia','CV Sumber Makmur','PT Global Supplier','UD Bintang Jaya'];
    }

    public function purchaseOrder()
    {
        $pos     = $this->getDummyPO();
        $vendors = $this->getDummyVendors();
        return view('procurement.purchase-order', compact('pos', 'vendors'));
    }

    public function storePO(Request $request)
    {
        $request->validate([
            'vendor'        => 'required|string',
            'barang'        => 'required|string',
            'jumlah'        => 'required|integer|min:1',
            'harga_satuan'  => 'required|numeric|min:1',
            'tgl_kirim'     => 'required|date',
        ]);
        $no    = 'PO-' . date('Y') . '-' . str_pad(rand(79, 999), 4, '0', STR_PAD_LEFT);
        $total = number_format($request->jumlah * $request->harga_satuan, 0, ',', '.');
        return redirect()->route('procurement.purchase-order')
            ->with('success', "Purchase Order berhasil dibuat! No. PO: {$no}. Total: Rp {$total}. Status: Draft.");
    }
}
