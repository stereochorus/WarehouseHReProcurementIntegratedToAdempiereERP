<?php

namespace App\Http\Controllers\EApproval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EApprovalController extends Controller
{
    private function getDummyDocuments(): array
    {
        return [
            ['id'=>'DOC-2024-0031','judul'=>'Proposal Pengadaan Server 2024','jenis'=>'Proposal','pemohon'=>'Ahmad Fauzi','dept'=>'IT','tgl_upload'=>'24 Feb 2024','tgl_deadline'=>'28 Feb 2024','ttd_digital'=>true,'status'=>'Menunggu Direktur','history'=>[
                ['step'=>1,'nama'=>'Manager IT','status'=>'Disetujui','tgl'=>'24 Feb 2024','catatan'=>'Kebutuhan mendesak'],
                ['step'=>2,'nama'=>'Manajer Keuangan','status'=>'Disetujui','tgl'=>'25 Feb 2024','catatan'=>'Anggaran tersedia'],
                ['step'=>3,'nama'=>'Direktur Utama','status'=>'Menunggu','tgl'=>'-','catatan'=>'-'],
            ]],
            ['id'=>'DOC-2024-0030','judul'=>'Surat Perjanjian Vendor CV Kertas Jaya','jenis'=>'Kontrak','pemohon'=>'Eko Prasetyo','dept'=>'Procurement','tgl_upload'=>'23 Feb 2024','tgl_deadline'=>'27 Feb 2024','ttd_digital'=>true,'status'=>'Selesai','history'=>[
                ['step'=>1,'nama'=>'Manager Procurement','status'=>'Disetujui','tgl'=>'23 Feb 2024','catatan'=>'Sudah sesuai SOP'],
                ['step'=>2,'nama'=>'Legal','status'=>'Disetujui','tgl'=>'24 Feb 2024','catatan'=>'Klausul OK'],
                ['step'=>3,'nama'=>'Direktur Utama','status'=>'Disetujui','tgl'=>'25 Feb 2024','catatan'=>'Setuju'],
            ]],
            ['id'=>'DOC-2024-0029','judul'=>'Laporan Audit Internal Q4 2023','jenis'=>'Laporan','pemohon'=>'Gunawan Hadi','dept'=>'Operations','tgl_upload'=>'22 Feb 2024','tgl_deadline'=>'26 Feb 2024','ttd_digital'=>false,'status'=>'Ditolak','history'=>[
                ['step'=>1,'nama'=>'Manager Ops','status'=>'Disetujui','tgl'=>'22 Feb 2024','catatan'=>''],
                ['step'=>2,'nama'=>'Direktur Keuangan','status'=>'Ditolak','tgl'=>'23 Feb 2024','catatan'=>'Perlu revisi data Q3'],
            ]],
            ['id'=>'DOC-2024-0028','judul'=>'SK Kenaikan Gaji Karyawan 2024','jenis'=>'Surat Keputusan','pemohon'=>'Joko Widodo','dept'=>'HR','tgl_upload'=>'21 Feb 2024','tgl_deadline'=>'25 Feb 2024','ttd_digital'=>true,'status'=>'Selesai','history'=>[
                ['step'=>1,'nama'=>'HR Manager','status'=>'Disetujui','tgl'=>'21 Feb 2024','catatan'=>'Sesuai kebijakan'],
                ['step'=>2,'nama'=>'Manager Keuangan','status'=>'Disetujui','tgl'=>'22 Feb 2024','catatan'=>'Anggaran OK'],
                ['step'=>3,'nama'=>'Direktur Utama','status'=>'Disetujui','tgl'=>'23 Feb 2024','catatan'=>'Approved'],
            ]],
            ['id'=>'DOC-2024-0027','judul'=>'Memo Perubahan SOP Gudang','jenis'=>'Memo Internal','pemohon'=>'Budi Santoso','dept'=>'Warehouse','tgl_upload'=>'20 Feb 2024','tgl_deadline'=>'24 Feb 2024','ttd_digital'=>false,'status'=>'Menunggu Manager','history'=>[
                ['step'=>1,'nama'=>'Supervisor Warehouse','status'=>'Menunggu','tgl'=>'-','catatan'=>'-'],
            ]],
        ];
    }

    public function dashboard()
    {
        $documents = $this->getDummyDocuments();
        $stats = [
            'total'     => count($documents),
            'menunggu'  => count(array_filter($documents, fn($d) => str_starts_with($d['status'], 'Menunggu'))),
            'selesai'   => count(array_filter($documents, fn($d) => $d['status'] === 'Selesai')),
            'ditolak'   => count(array_filter($documents, fn($d) => $d['status'] === 'Ditolak')),
            'ttd_digital'=> count(array_filter($documents, fn($d) => $d['ttd_digital'])),
        ];
        $recent = array_slice($documents, 0, 5);
        return view('e-approval.dashboard', compact('stats', 'recent'));
    }

    public function documents(Request $request)
    {
        $documents = $this->getDummyDocuments();

        if ($status = $request->get('status')) {
            $documents = array_filter($documents, fn($d) => $d['status'] === $status);
        }
        if ($jenis = $request->get('jenis')) {
            $documents = array_filter($documents, fn($d) => $d['jenis'] === $jenis);
        }

        $statuses = array_unique(array_column($this->getDummyDocuments(), 'status'));
        $jenises  = array_unique(array_column($this->getDummyDocuments(), 'jenis'));

        return view('e-approval.documents', compact('documents', 'status', 'jenis', 'statuses', 'jenises'));
    }

    public function create()
    {
        $jenises    = ['Proposal','Kontrak','Laporan','Surat Keputusan','Memo Internal','Surat Keterangan','Lainnya'];
        $approvers  = ['Manager Departemen','Manager Keuangan','Legal','Direktur Keuangan','Direktur Utama'];
        return view('e-approval.create', compact('jenises', 'approvers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:150',
            'jenis'       => 'required|string',
            'dept'        => 'required|string',
            'tgl_deadline'=> 'required|date|after:today',
            'ttd_digital' => 'nullable',
            'keterangan'  => 'nullable|string',
        ]);

        $id = 'DOC-' . date('Y') . '-' . str_pad(rand(32, 999), 4, '0', STR_PAD_LEFT);
        return redirect()->route('e-approval.documents')
            ->with('success', 'Dokumen berhasil diajukan untuk approval! No. Dokumen: ' . $id . ' — "' . $request->judul . '". Status: Menunggu persetujuan.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string',
        ]);
        return redirect()->route('e-approval.documents')
            ->with('success', "Dokumen {$id} berhasil disetujui dan TTD digital disimulasikan. Proses lanjut ke tahap berikutnya.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|min:5',
        ]);
        return redirect()->route('e-approval.documents')
            ->with('success', "Dokumen {$id} dikembalikan untuk revisi. Catatan: {$request->catatan}.");
    }
}
