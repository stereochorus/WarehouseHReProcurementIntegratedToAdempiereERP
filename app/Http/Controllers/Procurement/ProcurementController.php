<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProcurementController extends Controller
{
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
        $stats = [
            'total_pr'         => 160,
            'pending_approval' => 14,
            'approved'         => 38,
            'rejected'         => 8,
            'total_value'      => 'Rp 2.847.500.000',
            'this_month_value' => 'Rp 547.000.000',
        ];
        $recentPRs      = array_slice($this->getDummyPRs(), 0, 5);
        $approvalPRs    = array_filter($this->getDummyPRs(), fn($p) => str_starts_with($p['status'], 'Pending'));
        return view('procurement.dashboard', compact('stats', 'recentPRs', 'approvalPRs'));
    }

    public function purchaseRequests(Request $request)
    {
        $prs    = $this->getDummyPRs();
        $search = $request->get('search', '');
        $status = $request->get('status', '');

        if ($search) {
            $prs = array_filter($prs, fn($p) =>
                stripos($p['item'], $search) !== false || stripos($p['id'], $search) !== false
            );
        }
        if ($status) {
            $prs = array_filter($prs, fn($p) => $p['status'] === $status);
        }

        $statuses = array_unique(array_column($this->getDummyPRs(), 'status'));
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

        $prId    = 'PR-' . date('Y') . '-' . str_pad(rand(161, 999), 4, '0', STR_PAD_LEFT);
        $total   = number_format($request->qty * $request->est_price, 0, ',', '.');

        return redirect()->route('procurement.purchase-requests')
            ->with('success', "Purchase Request berhasil dibuat! No. PR: {$prId}, Total: Rp {$total} (simulasi). Status: Pending Manager Approval.");
    }

    public function approvals()
    {
        $pendingPRs = array_filter($this->getDummyPRs(), fn($p) => str_starts_with($p['status'], 'Pending'));
        $user       = Session::get('demo_user');
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
        $prs = $this->getDummyPRs();
        return view('procurement.reports', compact('prs'));
    }
}
