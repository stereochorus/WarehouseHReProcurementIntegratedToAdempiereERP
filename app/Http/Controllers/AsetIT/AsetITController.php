<?php

namespace App\Http\Controllers\AsetIT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AsetITController extends Controller
{
    private function getDummyAssets(): array
    {
        return [
            ['id'=>'AST-IT-001','nama'=>'Laptop Dell XPS 15','no_seri'=>'DELL-XPS-2023-001','kategori'=>'Laptop','merek'=>'Dell','thn_beli'=>2023,'nilai'=>18500000,'lokasi'=>'Ruang IT Lt.2','pj'=>'Ahmad Fauzi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Des 2025'],
            ['id'=>'AST-IT-002','nama'=>'Server HPE ProLiant DL380','no_seri'=>'HPE-DL380-2022-001','kategori'=>'Server','merek'=>'HPE','thn_beli'=>2022,'nilai'=>85000000,'lokasi'=>'Ruang Server B1','pj'=>'Ahmad Fauzi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Jan 2027'],
            ['id'=>'AST-IT-003','nama'=>'Switch Cisco Catalyst 24-Port','no_seri'=>'CSC-CAT-2022-003','kategori'=>'Jaringan','merek'=>'Cisco','thn_beli'=>2022,'nilai'=>8500000,'lokasi'=>'Ruang Server B1','pj'=>'Hana Pertiwi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Jun 2024'],
            ['id'=>'AST-IT-004','nama'=>'UPS APC 1500VA','no_seri'=>'APC-UPS-2021-005','kategori'=>'Power','merek'=>'APC','thn_beli'=>2021,'nilai'=>4200000,'lokasi'=>'Ruang Server B1','pj'=>'Ahmad Fauzi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Des 2023'],
            ['id'=>'AST-IT-005','nama'=>'Printer HP LaserJet Pro M428','no_seri'=>'HP-LJ-2023-002','kategori'=>'Printer','merek'=>'HP','thn_beli'=>2023,'nilai'=>3200000,'lokasi'=>'Ruang Admin Lt.1','pj'=>'Siti Rahayu','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Mar 2026'],
            ['id'=>'AST-IT-006','nama'=>'Monitor LG UltraWide 34"','no_seri'=>'LG-UW34-2023-008','kategori'=>'Monitor','merek'=>'LG','thn_beli'=>2023,'nilai'=>6500000,'lokasi'=>'Ruang Direksi','pj'=>'Gunawan Hadi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Jan 2026'],
            ['id'=>'AST-IT-007','nama'=>'Laptop Lenovo ThinkPad E15','no_seri'=>'LNV-TPE-2022-004','kategori'=>'Laptop','merek'=>'Lenovo','thn_beli'=>2022,'nilai'=>12000000,'lokasi'=>'Ruang Finance','pj'=>'Dewi Kusuma','kondisi'=>'Perlu Servis','status'=>'Maintenance','garansi_s/d'=>'Sep 2024'],
            ['id'=>'AST-IT-008','nama'=>'CCTV IP Camera Hikvision','no_seri'=>'HKV-IP-2021-012','kategori'=>'Keamanan','merek'=>'Hikvision','thn_beli'=>2021,'nilai'=>2800000,'lokasi'=>'Area Gudang','pj'=>'Budi Santoso','kondisi'=>'Rusak','status'=>'Tidak Aktif','garansi_s/d'=>'Jun 2023'],
            ['id'=>'AST-IT-009','nama'=>'Access Point Ubiquiti UAP-AC-Pro','no_seri'=>'UBI-UAP-2023-006','kategori'=>'Jaringan','merek'=>'Ubiquiti','thn_beli'=>2023,'nilai'=>1900000,'lokasi'=>'Lobby Lt.1','pj'=>'Hana Pertiwi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Feb 2026'],
            ['id'=>'AST-IT-010','nama'=>'NAS Synology DS923+','no_seri'=>'SYN-NAS-2023-001','kategori'=>'Storage','merek'=>'Synology','thn_beli'=>2023,'nilai'=>14500000,'lokasi'=>'Ruang Server B1','pj'=>'Ahmad Fauzi','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Jan 2028'],
            ['id'=>'AST-IT-011','nama'=>'Tablet iPad Air 5th Gen','no_seri'=>'APL-IPA-2022-007','kategori'=>'Tablet','merek'=>'Apple','thn_beli'=>2022,'nilai'=>9500000,'lokasi'=>'Ruang Meeting','pj'=>'Joko Widodo','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Agt 2024'],
            ['id'=>'AST-IT-012','nama'=>'Proyektor Epson EB-X51','no_seri'=>'EPS-PRJ-2021-002','kategori'=>'Presentasi','merek'=>'Epson','thn_beli'=>2021,'nilai'=>6800000,'lokasi'=>'Ruang Meeting','pj'=>'Joko Widodo','kondisi'=>'Baik','status'=>'Aktif','garansi_s/d'=>'Mar 2024'],
        ];
    }

    public function dashboard()
    {
        $assets = $this->getDummyAssets();
        $stats  = [
            'total'       => count($assets),
            'aktif'       => count(array_filter($assets, fn($a) => $a['status'] === 'Aktif')),
            'maintenance' => count(array_filter($assets, fn($a) => $a['status'] === 'Maintenance')),
            'tidak_aktif' => count(array_filter($assets, fn($a) => $a['status'] === 'Tidak Aktif')),
            'total_nilai' => array_sum(array_column($assets, 'nilai')),
        ];

        $byKategori = [];
        foreach ($assets as $a) {
            $byKategori[$a['kategori']] = ($byKategori[$a['kategori']] ?? 0) + 1;
        }

        return view('aset-it.dashboard', compact('stats', 'byKategori', 'assets'));
    }

    public function assets(Request $request)
    {
        $assets = $this->getDummyAssets();

        if ($search = $request->get('search')) {
            $assets = array_filter($assets, fn($a) =>
                str_contains(strtolower($a['nama']), strtolower($search)) ||
                str_contains(strtolower($a['no_seri']), strtolower($search)) ||
                str_contains(strtolower($a['lokasi']), strtolower($search))
            );
        }
        if ($kategori = $request->get('kategori')) {
            $assets = array_filter($assets, fn($a) => $a['kategori'] === $kategori);
        }
        if ($status = $request->get('status')) {
            $assets = array_filter($assets, fn($a) => $a['status'] === $status);
        }

        $kategoris = array_unique(array_column($this->getDummyAssets(), 'kategori'));
        sort($kategoris);

        return view('aset-it.assets', compact('assets', 'search', 'kategori', 'status', 'kategoris'));
    }

    public function create()
    {
        $kategoris = ['Laptop','Desktop','Server','Monitor','Printer','Jaringan','Storage','Power','Keamanan','Tablet','Presentasi','Lainnya'];
        $lokasis   = ['Ruang IT Lt.2','Ruang Server B1','Ruang Admin Lt.1','Ruang Direksi','Ruang Finance','Ruang HR','Ruang Marketing','Area Gudang','Lobby Lt.1','Ruang Meeting'];
        return view('aset-it.create', compact('kategoris', 'lokasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'no_seri'   => 'required|string|max:50',
            'kategori'  => 'required|string',
            'merek'     => 'required|string|max:50',
            'thn_beli'  => 'required|integer|min:2000|max:' . date('Y'),
            'nilai'     => 'required|numeric|min:0',
            'lokasi'    => 'required|string',
            'pj'        => 'required|string|max:100',
            'kondisi'   => 'required|in:Baik,Perlu Servis,Rusak',
        ]);

        $prefix = strtoupper(substr(preg_replace('/[^A-Z]/i', '', $request->kategori), 0, 3));
        $id     = 'AST-' . $prefix . '-' . str_pad(rand(13, 999), 3, '0', STR_PAD_LEFT);

        return redirect()->route('aset-it.assets')
            ->with('success', "Aset IT berhasil didaftarkan! ID Aset: {$id} — {$request->nama}.");
    }
}
