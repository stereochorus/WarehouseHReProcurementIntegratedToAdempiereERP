<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
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
        $stats = [
            'total_items'    => 1247,
            'total_value'    => 'Rp 4.2 Miliar',
            'low_stock'      => 23,
            'pending_gr'     => 8,
            'today_in'       => 3,
            'today_out'      => 7,
        ];
        $movements = array_slice($this->getDummyMovements(), 0, 5);
        return view('warehouse.dashboard', compact('stats', 'movements'));
    }

    public function inventory(Request $request)
    {
        $inventory = $this->getDummyInventory();
        $search    = $request->get('search', '');
        $category  = $request->get('category', '');

        if ($search) {
            $inventory = array_filter($inventory, fn($i) =>
                stripos($i['name'], $search) !== false || stripos($i['id'], $search) !== false
            );
        }
        if ($category) {
            $inventory = array_filter($inventory, fn($i) => $i['category'] === $category);
        }

        $categories = array_unique(array_column($this->getDummyInventory(), 'category'));
        return view('warehouse.inventory', compact('inventory', 'search', 'category', 'categories'));
    }

    public function receiving()
    {
        $items = array_column($this->getDummyInventory(), 'name', 'id');
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

        $docNo = 'GR-' . date('Y') . '-' . str_pad(rand(90, 999), 4, '0', STR_PAD_LEFT);

        return redirect()->route('warehouse.receiving')
            ->with('success', "Penerimaan barang berhasil disimulasikan! No. Dokumen: {$docNo}. Data telah dicatat (simulasi).");
    }

    public function issuing()
    {
        $items       = array_column($this->getDummyInventory(), 'name', 'id');
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Procurement', 'Direksi'];
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

        $docNo = 'GI-' . date('Y') . '-' . str_pad(rand(46, 999), 4, '0', STR_PAD_LEFT);

        return redirect()->route('warehouse.issuing')
            ->with('success', "Pengeluaran barang berhasil disimulasikan! No. Dokumen: {$docNo}. Data telah dicatat (simulasi).");
    }

    public function stockMovement(Request $request)
    {
        $movements = $this->getDummyMovements();
        return view('warehouse.stock-movement', compact('movements'));
    }

    public function reports()
    {
        $inventory = $this->getDummyInventory();
        $lowStock  = array_filter($inventory, fn($i) => $i['status'] === 'Low Stock');
        return view('warehouse.reports', compact('inventory', 'lowStock'));
    }
}
