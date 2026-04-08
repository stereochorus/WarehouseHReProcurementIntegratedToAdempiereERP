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

    private function getDummyProjects(): array
    {
        return [
            ['kode' => 'KP-001', 'nama' => 'Gudang Sentral Phase 2'],
            ['kode' => 'KP-002', 'nama' => 'Infrastruktur IT Office'],
            ['kode' => 'KP-003', 'nama' => 'K3 & Safety Site'],
            ['kode' => 'KP-004', 'nama' => 'SDM Rekrutmen Q1'],
            ['kode' => 'KP-005', 'nama' => 'Arsip & Dokumen Keuangan'],
            ['kode' => 'KP-006', 'nama' => 'Pengembangan Produk Baru'],
        ];
    }

    private function getDummyPRs(): array
    {
        return [
            [
                'id' => 'PR-2024-0160', 'date' => '24 Feb 2024',
                'kode_proyek' => 'KP-001', 'proyek' => 'Gudang Sentral Phase 2',
                'mr_ref' => 'MR-2024-0025', 'requestor' => 'Ahmad Fauzi',
                // flat compat fields (for search/listing)
                'item' => 'Server Rack 42U', 'qty' => 2, 'unit' => 'Unit', 'est_price' => 85000000,
                'items' => [
                    ['nama' => 'Server Rack 42U', 'qty' => 2, 'satuan' => 'Unit', 'est_price' => 85000000, 'total' => 170000000],
                    ['nama' => 'UPS 3000VA',       'qty' => 1, 'satuan' => 'Unit', 'est_price' => 25000000, 'total' =>  25000000],
                ],
                'total' => 195000000, 'status' => 'Pending Manager', 'priority' => 'Tinggi',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done',   'ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'active', 'ket' => 'Sedang Review'],
                    ['step' => 'QC / QC Mgr','status' => 'pending','ket' => 'Menunggu'],
                    ['step' => 'WH Manager', 'status' => 'pending','ket' => 'Menunggu'],
                    ['step' => 'Site CM',    'status' => 'pending','ket' => 'Menunggu'],
                    ['step' => 'Cost Ctrl',  'status' => 'pending','ket' => 'Menunggu'],
                    ['step' => 'Project Mgr','status' => 'pending','ket' => 'Menunggu'],
                ],
            ],
            [
                'id' => 'PR-2024-0159', 'date' => '23 Feb 2024',
                'kode_proyek' => 'KP-002', 'proyek' => 'Infrastruktur IT Office',
                'mr_ref' => 'MR-2024-0024', 'requestor' => 'Gunawan Hadi',
                'item' => 'Forklift Electric', 'qty' => 1, 'unit' => 'Unit', 'est_price' => 180000000,
                'items' => [
                    ['nama' => 'Forklift Electric', 'qty' => 1, 'satuan' => 'Unit', 'est_price' => 180000000, 'total' => 180000000],
                ],
                'total' => 180000000, 'status' => 'Pending Finance', 'priority' => 'Normal',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done',   'ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'QC / QC Mgr','status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'WH Manager', 'status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'Site CM',    'status' => 'active', 'ket' => 'Sedang Review'],
                    ['step' => 'Cost Ctrl',  'status' => 'pending','ket' => 'Menunggu'],
                    ['step' => 'Project Mgr','status' => 'pending','ket' => 'Menunggu'],
                ],
            ],
            [
                'id' => 'PR-2024-0158', 'date' => '23 Feb 2024',
                'kode_proyek' => 'KP-003', 'proyek' => 'K3 & Safety Site',
                'mr_ref' => 'MR-2024-0023', 'requestor' => 'Fitri Handayani',
                'item' => 'Laptop MacBook Pro', 'qty' => 3, 'unit' => 'Unit', 'est_price' => 28000000,
                'items' => [
                    ['nama' => 'Laptop MacBook Pro', 'qty' => 3, 'satuan' => 'Unit', 'est_price' => 28000000, 'total' => 84000000],
                ],
                'total' => 84000000, 'status' => 'Pending Purchasing', 'priority' => 'Normal',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done',   'ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'QC / QC Mgr','status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'WH Manager', 'status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'Site CM',    'status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'Cost Ctrl',  'status' => 'done',   'ket' => 'Disetujui'],
                    ['step' => 'Project Mgr','status' => 'active', 'ket' => 'Sedang Review'],
                ],
            ],
            [
                'id' => 'PR-2024-0157', 'date' => '22 Feb 2024',
                'kode_proyek' => 'KP-004', 'proyek' => 'SDM Rekrutmen Q1',
                'mr_ref' => null, 'requestor' => 'Siti Rahayu',
                'item' => 'Seragam Karyawan', 'qty' => 200, 'unit' => 'Pcs', 'est_price' => 350000,
                'items' => [
                    ['nama' => 'Seragam Karyawan', 'qty' => 200, 'satuan' => 'Pcs', 'est_price' => 350000, 'total' => 70000000],
                    ['nama' => 'ID Card & Holder', 'qty' => 200, 'satuan' => 'Pcs', 'est_price' =>  15000, 'total' =>  3000000],
                ],
                'total' => 73000000, 'status' => 'Approved', 'priority' => 'Normal',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done','ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'QC / QC Mgr','status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'WH Manager', 'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Site CM',    'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Cost Ctrl',  'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Project Mgr','status' => 'done','ket' => 'Disetujui'],
                ],
            ],
            [
                'id' => 'PR-2024-0156', 'date' => '22 Feb 2024',
                'kode_proyek' => 'KP-005', 'proyek' => 'Arsip & Dokumen Keuangan',
                'mr_ref' => null, 'requestor' => 'Dewi Kusuma',
                'item' => 'ATK Kantor', 'qty' => 50, 'unit' => 'Set', 'est_price' => 90000,
                'items' => [
                    ['nama' => 'ATK Kantor', 'qty' => 50, 'satuan' => 'Set', 'est_price' => 90000, 'total' => 4500000],
                ],
                'total' => 4500000, 'status' => 'Approved', 'priority' => 'Rendah',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done','ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'QC / QC Mgr','status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'WH Manager', 'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Site CM',    'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Cost Ctrl',  'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Project Mgr','status' => 'done','ket' => 'Disetujui'],
                ],
            ],
            [
                'id' => 'PR-2024-0155', 'date' => '21 Feb 2024',
                'kode_proyek' => 'KP-001', 'proyek' => 'Gudang Sentral Phase 2',
                'mr_ref' => null, 'requestor' => 'Budi Santoso',
                'item' => 'Barcode Scanner', 'qty' => 5, 'unit' => 'Unit', 'est_price' => 2500000,
                'items' => [
                    ['nama' => 'Barcode Scanner', 'qty' => 5, 'satuan' => 'Unit', 'est_price' => 2500000, 'total' => 12500000],
                ],
                'total' => 12500000, 'status' => 'Rejected', 'priority' => 'Normal',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done',    'ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'rejected','ket' => 'Ditolak'],
                    ['step' => 'QC / QC Mgr','status' => 'pending', 'ket' => '-'],
                    ['step' => 'WH Manager', 'status' => 'pending', 'ket' => '-'],
                    ['step' => 'Site CM',    'status' => 'pending', 'ket' => '-'],
                    ['step' => 'Cost Ctrl',  'status' => 'pending', 'ket' => '-'],
                    ['step' => 'Project Mgr','status' => 'pending', 'ket' => '-'],
                ],
            ],
            [
                'id' => 'PR-2024-0154', 'date' => '20 Feb 2024',
                'kode_proyek' => 'KP-002', 'proyek' => 'Infrastruktur IT Office',
                'mr_ref' => 'MR-2024-0024', 'requestor' => 'Ahmad Fauzi',
                'item' => 'Switch Cisco 24 Port', 'qty' => 3, 'unit' => 'Unit', 'est_price' => 8500000,
                'items' => [
                    ['nama' => 'Switch Cisco 24 Port', 'qty' =>  3, 'satuan' => 'Unit', 'est_price' => 8500000, 'total' => 25500000],
                    ['nama' => 'Patch Cord Cat6 3m',   'qty' => 48, 'satuan' => 'pcs',  'est_price' =>   45000, 'total' =>  2160000],
                ],
                'total' => 27660000, 'status' => 'Approved', 'priority' => 'Tinggi',
                'progress' => [
                    ['step' => 'Pemohon',     'status' => 'done','ket' => 'Diajukan'],
                    ['step' => 'PPIC',        'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'QC / QC Mgr','status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'WH Manager', 'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Site CM',    'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Cost Ctrl',  'status' => 'done','ket' => 'Disetujui'],
                    ['step' => 'Project Mgr','status' => 'done','ket' => 'Disetujui'],
                ],
            ],
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

        try {
            $allPRs     = $this->adempiere()->getRequisitions();
            $pending    = array_filter($allPRs, fn($p) => str_starts_with($p['status'] ?? '', 'Waiting') || $p['status'] === 'Draft');
            $approved   = array_filter($allPRs, fn($p) => in_array($p['status'], ['Approved', 'Completed']));
            $totalValue = array_sum(array_column($allPRs, 'total'));
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
                stripos($p['item'] ?? '', $search) !== false ||
                stripos($p['id'], $search) !== false ||
                stripos($p['proyek'] ?? '', $search) !== false
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
        $projects = $this->getDummyProjects();
        $mrs      = array_values(array_filter($this->getDummyMR(), fn($m) => $m['status'] === 'Disetujui'));
        return view('procurement.pr-form', compact('projects', 'mrs'));
    }

    public function storePR(Request $request)
    {
        $request->validate([
            'kode_proyek'          => 'required|string',
            'proyek'               => 'required|string',
            'items'                => 'required|array|min:1',
            'items.*.nama'         => 'required|string',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.satuan'       => 'required|string',
            'items.*.est_price'    => 'nullable|numeric|min:0',
            'reason'               => 'required|string',
        ]);

        $grandTotal = 0;
        foreach ($request->items as $item) {
            $grandTotal += (int)($item['qty'] ?? 0) * (float)($item['est_price'] ?? 0);
        }
        $total = number_format($grandTotal, 0, ',', '.');

        if ($this->isDemo()) {
            $prId = 'PR-' . date('Y') . '-' . str_pad(rand(161, 999), 4, '0', STR_PAD_LEFT);
            return redirect()->route('procurement.purchase-requests')
                ->with('success', "Purchase Request berhasil dibuat! No. PR: {$prId}, Total Estimasi: Rp {$total} (simulasi). Status: Pending PPIC Approval.");
        }

        try {
            $this->adempiere()->createRequisition($request->only('kode_proyek', 'proyek', 'items', 'reason'));
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
            [
                'no' => 'MR-2024-0025', 'tanggal' => '24 Feb 2024', 'pemohon' => 'Budi Santoso',
                'kode_proyek' => 'KP-001', 'proyek' => 'Gudang Sentral Phase 2',
                'items' => [
                    ['nama' => 'Pallet Kayu',     'qty' => 50, 'satuan' => 'unit'],
                    ['nama' => 'Shrink Wrap Roll', 'qty' => 10, 'satuan' => 'roll'],
                ],
                'alasan' => 'Stok gudang habis', 'status' => 'Disetujui', 'prioritas' => 'Tinggi',
            ],
            [
                'no' => 'MR-2024-0024', 'tanggal' => '23 Feb 2024', 'pemohon' => 'Ahmad Fauzi',
                'kode_proyek' => 'KP-002', 'proyek' => 'Infrastruktur IT Office',
                'items' => [
                    ['nama' => 'Kabel LAN Cat6',     'qty' => 200, 'satuan' => 'meter'],
                    ['nama' => 'Patch Panel 24 Port', 'qty' =>   2, 'satuan' => 'unit'],
                ],
                'alasan' => 'Perluasan jaringan ruang server', 'status' => 'Menunggu', 'prioritas' => 'Normal',
            ],
            [
                'no' => 'MR-2024-0023', 'tanggal' => '22 Feb 2024', 'pemohon' => 'Gunawan Hadi',
                'kode_proyek' => 'KP-003', 'proyek' => 'K3 & Safety Site',
                'items' => [
                    ['nama' => 'Helm Safety',   'qty' => 30, 'satuan' => 'pcs'],
                    ['nama' => 'Rompi K3',      'qty' => 30, 'satuan' => 'pcs'],
                    ['nama' => 'Sepatu Safety', 'qty' => 15, 'satuan' => 'pasang'],
                ],
                'alasan' => 'Standar keselamatan baru', 'status' => 'Disetujui', 'prioritas' => 'Tinggi',
            ],
            [
                'no' => 'MR-2024-0022', 'tanggal' => '21 Feb 2024', 'pemohon' => 'Siti Rahayu',
                'kode_proyek' => 'KP-004', 'proyek' => 'SDM Rekrutmen Q1',
                'items' => [
                    ['nama' => 'Formulir Rekrutmen (cetak)', 'qty' => 500, 'satuan' => 'lembar'],
                ],
                'alasan' => 'Rekrutmen batch Q1', 'status' => 'Ditolak', 'prioritas' => 'Rendah',
            ],
            [
                'no' => 'MR-2024-0021', 'tanggal' => '20 Feb 2024', 'pemohon' => 'Dewi Kusuma',
                'kode_proyek' => 'KP-005', 'proyek' => 'Arsip & Dokumen Keuangan',
                'items' => [
                    ['nama' => 'Brankas Arsip A4',  'qty' => 2, 'satuan' => 'unit'],
                    ['nama' => 'Lemari Arsip Besi', 'qty' => 1, 'satuan' => 'unit'],
                ],
                'alasan' => 'Penyimpanan dokumen keuangan', 'status' => 'Disetujui', 'prioritas' => 'Normal',
            ],
        ];
    }

    public function materialRequest()
    {
        $mrs      = $this->getDummyMR();
        $projects = $this->getDummyProjects();
        return view('procurement.material-request', compact('mrs', 'projects'));
    }

    public function storeMR(Request $request)
    {
        $request->validate([
            'pemohon'          => 'required|string',
            'kode_proyek'      => 'required|string',
            'proyek'           => 'required|string',
            'items'            => 'required|array|min:1',
            'items.*.nama'     => 'required|string',
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.satuan'   => 'required|string',
            'alasan'           => 'required|string',
            'prioritas'        => 'required|in:Rendah,Normal,Tinggi',
        ]);
        $no        = 'MR-' . date('Y') . '-' . str_pad(rand(26, 999), 4, '0', STR_PAD_LEFT);
        $itemCount = count($request->items);
        return redirect()->route('procurement.material-request')
            ->with('success', "Material Request berhasil dibuat! No. MR: {$no} ({$itemCount} item). Proyek: {$request->proyek}. Status: Menunggu persetujuan Manager.");
    }

    // ─── PURCHASE ORDER (PO) ───────────────────────────────────────
    private function getDummyPO(): array
    {
        return [
            [
                'no' => 'PO-2024-0078', 'tanggal' => '24 Feb 2024', 'vendor' => 'PT Mitra Teknologi',
                'kode_proyek' => 'KP-001', 'proyek' => 'Gudang Sentral Phase 2', 'pr_ref' => 'PR-2024-0157',
                'barang' => 'Server HPE ProLiant DL380', 'jumlah' => 1, 'satuan' => 'unit', 'harga_satuan' => 85000000,
                'items' => [
                    ['nama' => 'Server HPE ProLiant DL380', 'qty' => 1, 'satuan' => 'unit', 'harga_satuan' => 85000000, 'total' => 85000000],
                ],
                'total' => 85000000, 'status' => 'Diterima', 'tgl_kirim' => '28 Feb 2024',
            ],
            [
                'no' => 'PO-2024-0077', 'tanggal' => '22 Feb 2024', 'vendor' => 'CV Kertas Jaya',
                'kode_proyek' => 'KP-005', 'proyek' => 'Arsip & Dokumen Keuangan', 'pr_ref' => 'PR-2024-0156',
                'barang' => 'Kertas A4 80gr', 'jumlah' => 100, 'satuan' => 'rim', 'harga_satuan' => 48000,
                'items' => [
                    ['nama' => 'Kertas A4 80gr', 'qty' => 100, 'satuan' => 'rim', 'harga_satuan' => 48000, 'total' => 4800000],
                    ['nama' => 'Map Plastik A4',  'qty' => 200, 'satuan' => 'pcs', 'harga_satuan' =>  3500, 'total' =>  700000],
                ],
                'total' => 5500000, 'status' => 'Dikirim', 'tgl_kirim' => '25 Feb 2024',
            ],
            [
                'no' => 'PO-2024-0076', 'tanggal' => '21 Feb 2024', 'vendor' => 'PT Aksesori Prima',
                'kode_proyek' => 'KP-002', 'proyek' => 'Infrastruktur IT Office', 'pr_ref' => 'PR-2024-0154',
                'barang' => 'Keyboard & Mouse Wireless Set', 'jumlah' => 30, 'satuan' => 'set', 'harga_satuan' => 650000,
                'items' => [
                    ['nama' => 'Keyboard & Mouse Wireless Set', 'qty' => 30, 'satuan' => 'set', 'harga_satuan' => 650000, 'total' => 19500000],
                ],
                'total' => 19500000, 'status' => 'Diproses', 'tgl_kirim' => '01 Mar 2024',
            ],
            [
                'no' => 'PO-2024-0075', 'tanggal' => '20 Feb 2024', 'vendor' => 'UD Furniture Mandiri',
                'kode_proyek' => 'KP-004', 'proyek' => 'SDM Rekrutmen Q1', 'pr_ref' => 'PR-2024-0157',
                'barang' => 'Kursi Ergonomis', 'jumlah' => 10, 'satuan' => 'unit', 'harga_satuan' => 1900000,
                'items' => [
                    ['nama' => 'Kursi Ergonomis',  'qty' => 10, 'satuan' => 'unit', 'harga_satuan' => 1900000, 'total' => 19000000],
                    ['nama' => 'Meja Kerja 120cm', 'qty' =>  5, 'satuan' => 'unit', 'harga_satuan' => 2500000, 'total' => 12500000],
                ],
                'total' => 31500000, 'status' => 'Draft', 'tgl_kirim' => '-',
            ],
            [
                'no' => 'PO-2024-0074', 'tanggal' => '19 Feb 2024', 'vendor' => 'PT Safety Indonesia',
                'kode_proyek' => 'KP-003', 'proyek' => 'K3 & Safety Site', 'pr_ref' => 'PR-2024-0156',
                'barang' => 'Helm Safety & Rompi K3', 'jumlah' => 30, 'satuan' => 'pcs', 'harga_satuan' => 185000,
                'items' => [
                    ['nama' => 'Helm Safety', 'qty' => 30, 'satuan' => 'pcs', 'harga_satuan' =>  85000, 'total' => 2550000],
                    ['nama' => 'Rompi K3',    'qty' => 30, 'satuan' => 'pcs', 'harga_satuan' => 100000, 'total' => 3000000],
                ],
                'total' => 5550000, 'status' => 'Diterima', 'tgl_kirim' => '22 Feb 2024',
            ],
        ];
    }

    private function getDummyVendors(): array
    {
        return ['PT Mitra Teknologi','CV Kertas Jaya','PT Aksesori Prima','UD Furniture Mandiri','PT Safety Indonesia','CV Sumber Makmur','PT Global Supplier','UD Bintang Jaya'];
    }

    public function purchaseOrder()
    {
        $pos         = $this->getDummyPO();
        $vendors     = $this->getDummyVendors();
        $projects    = $this->getDummyProjects();
        $approvedPRs = array_values(array_filter($this->getDummyPRs(), fn($p) => $p['status'] === 'Approved'));
        return view('procurement.purchase-order', compact('pos', 'vendors', 'projects', 'approvedPRs'));
    }

    public function storePO(Request $request)
    {
        $request->validate([
            'vendor'                  => 'required|string',
            'kode_proyek'             => 'required|string',
            'proyek'                  => 'required|string',
            'pr_ref'                  => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.nama'            => 'required|string',
            'items.*.qty'             => 'required|integer|min:1',
            'items.*.satuan'          => 'required|string',
            'items.*.harga_satuan'    => 'required|numeric|min:1',
            'tgl_kirim'               => 'required|date',
        ]);
        $grandTotal = 0;
        foreach ($request->items as $item) {
            $grandTotal += (int)($item['qty'] ?? 0) * (float)($item['harga_satuan'] ?? 0);
        }
        $no    = 'PO-' . date('Y') . '-' . str_pad(rand(79, 999), 4, '0', STR_PAD_LEFT);
        $total = number_format($grandTotal, 0, ',', '.');
        return redirect()->route('procurement.purchase-order')
            ->with('success', "Purchase Order berhasil dibuat! No. PO: {$no}. Total: Rp {$total}. Status: Draft.");
    }
}
