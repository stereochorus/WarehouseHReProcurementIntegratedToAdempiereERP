<?php

namespace App\Http\Controllers\EApproval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EApprovalController extends Controller
{
    private function getDummyDocuments(): array
    {
        $today = date('Y-m-d');

        $documents = [
            [
                'id'                   => 'DOC-2024-0031',
                'no_dokumen'           => 'PRO/IT/2024/0031',
                'judul'                => 'Proposal Pengadaan Server 2024',
                'jenis'                => 'Proposal',
                'pemohon'              => 'Ahmad Fauzi',
                'jabatan_pemohon'      => 'Staff IT',
                'dept'                 => 'IT',
                'tgl_upload'           => '24 Feb 2024',
                'tgl_upload_full'      => '24 Februari 2024, 09:15 WIB',
                'tgl_deadline'         => '2024-02-28',
                'tgl_deadline_display' => '28 Feb 2024',
                'ttd_digital'          => true,
                'reminder_interval'    => 1,
                'status'               => 'Menunggu Direktur',
                'komentar'             => [
                    ['user' => 'Ahmad Fauzi',  'jabatan' => 'Staff IT',   'tgl' => '24 Feb 2024, 09:20 WIB', 'text' => 'Dokumen sudah lengkap, mohon segera direview.',               'bagian' => 'Umum'],
                    ['user' => 'Budi Manager', 'jabatan' => 'Manager IT', 'tgl' => '24 Feb 2024, 14:05 WIB', 'text' => 'Spesifikasi server perlu disesuaikan dengan kebutuhan 2025.', 'bagian' => 'Hal. 3'],
                ],
                'history'              => [
                    ['step' => 1, 'nama' => 'Budi Manager',  'jabatan' => 'Manager IT',      'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '24 Feb 2024, 14:05 WIB', 'catatan' => 'Kebutuhan mendesak', 'paraf' => true],
                    ['step' => 2, 'nama' => 'Siti Rahayu',   'jabatan' => 'Manager Finance', 'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '25 Feb 2024, 10:30 WIB', 'catatan' => 'Anggaran tersedia',  'paraf' => true],
                    ['step' => 3, 'nama' => 'Hendra Wijaya', 'jabatan' => 'Direktur Utama',  'peran' => 'Approve', 'action_status' => 'Prepare', 'status' => 'Menunggu',  'tgl' => '-',                     'catatan' => '-',                  'paraf' => false],
                ],
            ],
            [
                'id'                   => 'DOC-2024-0030',
                'no_dokumen'           => 'KTR/PCM/2024/0030',
                'judul'                => 'Surat Perjanjian Vendor CV Kertas Jaya',
                'jenis'                => 'Kontrak',
                'pemohon'              => 'Eko Prasetyo',
                'jabatan_pemohon'      => 'Staff Procurement',
                'dept'                 => 'Procurement',
                'tgl_upload'           => '23 Feb 2024',
                'tgl_upload_full'      => '23 Februari 2024, 08:45 WIB',
                'tgl_deadline'         => '2024-02-27',
                'tgl_deadline_display' => '27 Feb 2024',
                'ttd_digital'          => true,
                'reminder_interval'    => 3,
                'status'               => 'Selesai',
                'tgl_ttd_full'         => '25 Februari 2024, 16:42:10 WIB',
                'komentar'             => [
                    ['user' => 'Eko Prasetyo', 'jabatan' => 'Staff Procurement', 'tgl' => '23 Feb 2024, 09:00 WIB', 'text' => 'Kontrak sudah direvisi sesuai masukan legal.', 'bagian' => 'Umum'],
                ],
                'history'              => [
                    ['step' => 1, 'nama' => 'Dewi Kusuma',   'jabatan' => 'Manager Procurement', 'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '23 Feb 2024, 11:20 WIB', 'catatan' => 'Sudah sesuai SOP', 'paraf' => true],
                    ['step' => 2, 'nama' => 'Andi Hukum',    'jabatan' => 'Legal / Compliance',  'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '24 Feb 2024, 09:15 WIB', 'catatan' => 'Klausul OK',       'paraf' => true],
                    ['step' => 3, 'nama' => 'Hendra Wijaya', 'jabatan' => 'Direktur Utama',      'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '25 Feb 2024, 16:42 WIB', 'catatan' => 'Setuju',           'paraf' => true],
                ],
            ],
            [
                'id'                   => 'DOC-2024-0029',
                'no_dokumen'           => null,
                'judul'                => 'Laporan Audit Internal Q4 2023',
                'jenis'                => 'Laporan',
                'pemohon'              => 'Gunawan Hadi',
                'jabatan_pemohon'      => 'Staff Operations',
                'dept'                 => 'Operations',
                'tgl_upload'           => '22 Feb 2024',
                'tgl_upload_full'      => '22 Februari 2024, 13:00 WIB',
                'tgl_deadline'         => '2024-02-26',
                'tgl_deadline_display' => '26 Feb 2024',
                'ttd_digital'          => false,
                'reminder_interval'    => 7,
                'status'               => 'Ditolak',
                'komentar'             => [
                    ['user' => 'Fitri Direktur', 'jabatan' => 'Direktur Keuangan', 'tgl' => '23 Feb 2024, 15:30 WIB', 'text' => 'Data Q3 belum dimasukkan, perlu dilengkapi dulu.', 'bagian' => 'Hal. 5'],
                ],
                'history'              => [
                    ['step' => 1, 'nama' => 'Rudi Ops',       'jabatan' => 'Manager Operations', 'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '22 Feb 2024, 15:00 WIB', 'catatan' => '',                    'paraf' => true],
                    ['step' => 2, 'nama' => 'Fitri Direktur', 'jabatan' => 'Direktur Keuangan',  'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Ditolak',  'tgl' => '23 Feb 2024, 15:30 WIB', 'catatan' => 'Perlu revisi data Q3', 'paraf' => false],
                ],
            ],
            [
                'id'                   => 'DOC-2024-0028',
                'no_dokumen'           => 'SK/HR/2024/0028',
                'judul'                => 'SK Kenaikan Gaji Karyawan 2024',
                'jenis'                => 'Surat Keputusan',
                'pemohon'              => 'Joko Widodo',
                'jabatan_pemohon'      => 'HR Manager',
                'dept'                 => 'HR',
                'tgl_upload'           => '21 Feb 2024',
                'tgl_upload_full'      => '21 Februari 2024, 08:00 WIB',
                'tgl_deadline'         => '2024-02-25',
                'tgl_deadline_display' => '25 Feb 2024',
                'ttd_digital'          => true,
                'reminder_interval'    => 1,
                'status'               => 'Selesai',
                'tgl_ttd_full'         => '23 Februari 2024, 11:30:45 WIB',
                'komentar'             => [],
                'history'              => [
                    ['step' => 1, 'nama' => 'Joko Widodo',   'jabatan' => 'HR Manager',       'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '21 Feb 2024, 10:00 WIB', 'catatan' => 'Sesuai kebijakan', 'paraf' => true],
                    ['step' => 2, 'nama' => 'Siti Rahayu',   'jabatan' => 'Manager Finance',  'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '22 Feb 2024, 09:45 WIB', 'catatan' => 'Anggaran OK',      'paraf' => true],
                    ['step' => 3, 'nama' => 'Hendra Wijaya', 'jabatan' => 'Direktur Utama',   'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '23 Feb 2024, 11:30 WIB', 'catatan' => 'Approved',         'paraf' => true],
                ],
            ],
            [
                'id'                   => 'DOC-2024-0027',
                'no_dokumen'           => null,
                'judul'                => 'Memo Perubahan SOP Gudang',
                'jenis'                => 'Memo Internal',
                'pemohon'              => 'Budi Santoso',
                'jabatan_pemohon'      => 'Supervisor Warehouse',
                'dept'                 => 'Warehouse',
                'tgl_upload'           => '20 Feb 2024',
                'tgl_upload_full'      => '20 Februari 2024, 07:30 WIB',
                'tgl_deadline'         => '2024-02-24',
                'tgl_deadline_display' => '24 Feb 2024',
                'ttd_digital'          => false,
                'reminder_interval'    => 3,
                'status'               => 'Menunggu Manager',
                'komentar'             => [
                    ['user' => 'Budi Santoso', 'jabatan' => 'Supervisor Warehouse', 'tgl' => '20 Feb 2024, 08:00 WIB', 'text' => 'SOP baru sudah siap untuk direview.', 'bagian' => 'Umum'],
                ],
                'history'              => [
                    ['step' => 1, 'nama' => 'Agus Warehouse', 'jabatan' => 'Supervisor Warehouse', 'peran' => 'Review',  'action_status' => 'Prepare', 'status' => 'Menunggu', 'tgl' => '-', 'catatan' => '-', 'paraf' => false],
                    ['step' => 2, 'nama' => 'Rina Manager',   'jabatan' => 'Manager Warehouse',    'peran' => 'Approve', 'action_status' => 'Prepare', 'status' => 'Menunggu', 'tgl' => '-', 'catatan' => '-', 'paraf' => false],
                ],
            ],
            [
                'id'                   => 'DOC-2024-0032',
                'no_dokumen'           => 'PR/OPS/2024/0032',
                'judul'                => 'Purchase Request Mesin Fotokopi Baru',
                'jenis'                => 'Purchase Request',
                'pemohon'              => 'Linda Sekretaris',
                'jabatan_pemohon'      => 'Staff Admin',
                'dept'                 => 'Operations',
                'tgl_upload'           => '26 Feb 2024',
                'tgl_upload_full'      => '26 Februari 2024, 13:00 WIB',
                'tgl_deadline'         => '2024-03-01',
                'tgl_deadline_display' => '01 Mar 2024',
                'ttd_digital'          => true,
                'reminder_interval'    => 1,
                'status'               => 'Menunggu Finance',
                'komentar'             => [],
                'history'              => [
                    ['step' => 1, 'nama' => 'Rudi Ops',    'jabatan' => 'Manager Operations', 'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '26 Feb 2024, 15:00 WIB', 'catatan' => 'Perlu untuk kantor', 'paraf' => true],
                    ['step' => 2, 'nama' => 'Siti Rahayu', 'jabatan' => 'Manager Finance',    'peran' => 'Approve', 'action_status' => 'Prepare', 'status' => 'Menunggu',  'tgl' => '-',                     'catatan' => '-',                  'paraf' => false],
                ],
            ],
        ];

        foreach ($documents as &$doc) {
            $doc['overdue'] = str_starts_with($doc['status'], 'Menunggu') && $doc['tgl_deadline'] < $today;
        }
        unset($doc);

        return $documents;
    }

    private function jenisWithNomorDokumen(): array
    {
        return ['Proposal', 'Kontrak', 'Surat Keputusan', 'Surat Keterangan', 'Purchase Request', 'Material Request'];
    }

    public function dashboard()
    {
        $documents = $this->getDummyDocuments();
        $stats = [
            'total'       => count($documents),
            'menunggu'    => count(array_filter($documents, fn($d) => str_starts_with($d['status'], 'Menunggu'))),
            'selesai'     => count(array_filter($documents, fn($d) => $d['status'] === 'Selesai')),
            'ditolak'     => count(array_filter($documents, fn($d) => $d['status'] === 'Ditolak')),
            'ttd_digital' => count(array_filter($documents, fn($d) => $d['ttd_digital'])),
            'overdue'     => count(array_filter($documents, fn($d) => $d['overdue'] ?? false)),
        ];
        $recent = array_slice($documents, 0, 5);
        return view('e-approval.dashboard', compact('stats', 'recent'));
    }

    public function documents(Request $request)
    {
        $all = $this->getDummyDocuments();

        $documents = $all;
        if ($status = $request->get('status')) {
            $documents = array_filter($documents, fn($d) => $d['status'] === $status);
        }
        if ($jenis = $request->get('jenis')) {
            $documents = array_filter($documents, fn($d) => $d['jenis'] === $jenis);
        }

        $statuses    = array_unique(array_column($all, 'status'));
        $jenises     = array_unique(array_column($all, 'jenis'));
        $totalDocs   = count($all);
        $overdueDocs = count(array_filter($all, fn($d) => $d['overdue'] ?? false));
        $selesaiDocs = count(array_filter($all, fn($d) => $d['status'] === 'Selesai'));

        return view('e-approval.documents', compact(
            'documents', 'status', 'jenis', 'statuses', 'jenises',
            'totalDocs', 'overdueDocs', 'selesaiDocs', 'all'
        ));
    }

    public function create()
    {
        $jenises = ['Proposal', 'Kontrak', 'Laporan', 'Surat Keputusan', 'Memo Internal', 'Surat Keterangan', 'Purchase Request', 'Material Request', 'Lainnya'];
        $approvers = [
            ['nama' => 'Budi Manager',   'jabatan' => 'Manager IT'],
            ['nama' => 'Siti Rahayu',    'jabatan' => 'Manager Finance'],
            ['nama' => 'Joko Widodo',    'jabatan' => 'HR Manager'],
            ['nama' => 'Dewi Kusuma',    'jabatan' => 'Manager Procurement'],
            ['nama' => 'Rina Manager',   'jabatan' => 'Manager Warehouse'],
            ['nama' => 'Rudi Ops',       'jabatan' => 'Manager Operations'],
            ['nama' => 'Andi Hukum',     'jabatan' => 'Legal / Compliance'],
            ['nama' => 'Fitri Direktur', 'jabatan' => 'Direktur Keuangan'],
            ['nama' => 'Hendra Wijaya',  'jabatan' => 'Direktur Utama'],
            ['nama' => 'Agus Warehouse', 'jabatan' => 'Supervisor Warehouse'],
            ['nama' => 'Yuni PPIC',      'jabatan' => 'PPIC'],
            ['nama' => 'Dian QC',        'jabatan' => 'QC Manager'],
        ];
        $jenisWithNomor = $this->jenisWithNomorDokumen();
        return view('e-approval.create', compact('jenises', 'approvers', 'jenisWithNomor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'        => 'required|string|max:150',
            'jenis'        => 'required|string',
            'dept'         => 'required|string',
            'tgl_deadline' => 'required|date|after:today',
            'ttd_digital'  => 'nullable',
            'keterangan'   => 'nullable|string',
            'dokumen'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $id = 'DOC-' . date('Y') . '-' . str_pad(rand(32, 9999), 4, '0', STR_PAD_LEFT);

        $fileMsg = '';
        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            $file     = $request->file('dokumen');
            $origName = $file->getClientOriginalName();
            $ext      = $file->getClientOriginalExtension();
            $safeName = $id . '_' . time() . '.' . $ext;

            // Store to public/uploads/e-approval/ — accessible without storage:link
            $destDir = public_path('uploads/e-approval');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $safeName);

            $fileMsg = ' File "<strong>' . e($origName) . '</strong>" berhasil disimpan.';
        }

        $msg = 'Dokumen berhasil diajukan! No. Dokumen: <strong>' . $id . '</strong> — "' . e($request->judul) . '". Status: Menunggu persetujuan.' . $fileMsg;

        return redirect()->route('e-approval.documents')->with('success', $msg);
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string']);
        return redirect()->route('e-approval.documents')
            ->with('success', "Dokumen {$id} berhasil disetujui. TTD digital disimulasikan. Proses lanjut ke tahap berikutnya.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string|min:5']);
        return redirect()->route('e-approval.documents')
            ->with('success', "Dokumen {$id} dikembalikan untuk revisi. Catatan: {$request->catatan}.");
    }

    public function export()
    {
        $documents = $this->getDummyDocuments();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="e-approval-summary-' . date('Ymd-His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($documents) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($file, ['No. Dokumen', 'Judul Dokumen', 'Jenis', 'Pemohon', 'Jabatan', 'Departemen', 'Tgl Upload', 'Deadline', 'Status', 'Overdue', 'Reviewer', 'Approver', 'TTD Digital', 'Reminder (hari)']);
            foreach ($documents as $doc) {
                $reviewers = array_filter($doc['history'], fn($h) => $h['peran'] === 'Review');
                $approvers = array_filter($doc['history'], fn($h) => $h['peran'] === 'Approve');
                fputcsv($file, [
                    $doc['no_dokumen'] ?? '-',
                    $doc['judul'],
                    $doc['jenis'],
                    $doc['pemohon'],
                    $doc['jabatan_pemohon'],
                    $doc['dept'],
                    $doc['tgl_upload_full'],
                    $doc['tgl_deadline_display'],
                    $doc['status'],
                    ($doc['overdue'] ?? false) ? 'Ya' : 'Tidak',
                    implode('; ', array_map(fn($h) => $h['nama'] . ' (' . $h['jabatan'] . ')', $reviewers)),
                    implode('; ', array_map(fn($h) => $h['nama'] . ' (' . $h['jabatan'] . ')', $approvers)),
                    $doc['ttd_digital'] ? 'Ya' : 'Tidak',
                    $doc['reminder_interval'] ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function uploadStatus()
    {
        $documents = $this->getDummyDocuments();
        return view('e-approval.upload-status', compact('documents'));
    }

    public function workflow()
    {
        $workflows = [
            ['id' => 'WF-2024-0031', 'judul' => 'Proposal Pengadaan Server 2024', 'pemohon' => 'Ahmad Fauzi', 'dept' => 'IT', 'tgl' => '24 Feb 2024',
             'steps' => [
                 ['urutan' => 1, 'nama' => 'Budi Manager',   'jabatan' => 'Manager IT',      'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '24 Feb 2024, 14:05 WIB', 'catatan' => 'Kebutuhan mendesak', 'ttd' => true],
                 ['urutan' => 2, 'nama' => 'Siti Rahayu',    'jabatan' => 'Manager Finance', 'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '25 Feb 2024, 10:30 WIB', 'catatan' => 'Anggaran tersedia',  'ttd' => true],
                 ['urutan' => 3, 'nama' => 'Hendra Wijaya',  'jabatan' => 'Direktur Utama',  'peran' => 'Approve', 'action_status' => 'Prepare', 'status' => 'Menunggu',  'tgl' => '-',                     'catatan' => '-',                  'ttd' => false],
             ]],
            ['id' => 'WF-2024-0030', 'judul' => 'Surat Perjanjian Vendor CV Kertas Jaya', 'pemohon' => 'Eko Prasetyo', 'dept' => 'Procurement', 'tgl' => '23 Feb 2024',
             'steps' => [
                 ['urutan' => 1, 'nama' => 'Dewi Kusuma',  'jabatan' => 'Manager Procurement', 'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '23 Feb 2024, 11:20 WIB', 'catatan' => 'Sudah sesuai SOP', 'ttd' => true],
                 ['urutan' => 2, 'nama' => 'Andi Hukum',   'jabatan' => 'Legal / Compliance',  'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '24 Feb 2024, 09:15 WIB', 'catatan' => 'Klausul OK',       'ttd' => true],
                 ['urutan' => 3, 'nama' => 'Hendra Wijaya', 'jabatan' => 'Direktur Utama',     'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '25 Feb 2024, 16:42 WIB', 'catatan' => 'Setuju',           'ttd' => true],
             ]],
            ['id' => 'WF-2024-0028', 'judul' => 'SK Kenaikan Gaji Karyawan 2024', 'pemohon' => 'Joko Widodo', 'dept' => 'HR', 'tgl' => '21 Feb 2024',
             'steps' => [
                 ['urutan' => 1, 'nama' => 'Joko Widodo',   'jabatan' => 'HR Manager',       'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '21 Feb 2024, 10:00 WIB', 'catatan' => 'Sesuai kebijakan', 'ttd' => true],
                 ['urutan' => 2, 'nama' => 'Siti Rahayu',   'jabatan' => 'Manager Finance',  'peran' => 'Review',  'action_status' => 'Checked', 'status' => 'Disetujui', 'tgl' => '22 Feb 2024, 09:45 WIB', 'catatan' => 'Anggaran OK',      'ttd' => true],
                 ['urutan' => 3, 'nama' => 'Hendra Wijaya', 'jabatan' => 'Direktur Utama',   'peran' => 'Approve', 'action_status' => 'Approve', 'status' => 'Disetujui', 'tgl' => '23 Feb 2024, 11:30 WIB', 'catatan' => 'Approved',         'ttd' => true],
             ]],
            ['id' => 'WF-2024-0027', 'judul' => 'Memo Perubahan SOP Gudang', 'pemohon' => 'Budi Santoso', 'dept' => 'Warehouse', 'tgl' => '20 Feb 2024',
             'steps' => [
                 ['urutan' => 1, 'nama' => 'Agus Warehouse', 'jabatan' => 'Supervisor Warehouse', 'peran' => 'Review',  'action_status' => 'Prepare', 'status' => 'Menunggu', 'tgl' => '-', 'catatan' => '-', 'ttd' => false],
                 ['urutan' => 2, 'nama' => 'Rina Manager',   'jabatan' => 'Manager Warehouse',    'peran' => 'Approve', 'action_status' => 'Prepare', 'status' => 'Menunggu', 'tgl' => '-', 'catatan' => '-', 'ttd' => false],
             ]],
        ];

        $users = [
            ['nama' => 'Budi Manager',   'jabatan' => 'Manager IT'],
            ['nama' => 'Siti Rahayu',    'jabatan' => 'Manager Finance'],
            ['nama' => 'Joko Widodo',    'jabatan' => 'HR Manager'],
            ['nama' => 'Dewi Kusuma',    'jabatan' => 'Manager Procurement'],
            ['nama' => 'Rina Manager',   'jabatan' => 'Manager Warehouse'],
            ['nama' => 'Rudi Ops',       'jabatan' => 'Manager Operations'],
            ['nama' => 'Andi Hukum',     'jabatan' => 'Legal / Compliance'],
            ['nama' => 'Fitri Direktur', 'jabatan' => 'Direktur Keuangan'],
            ['nama' => 'Hendra Wijaya',  'jabatan' => 'Direktur Utama'],
            ['nama' => 'Agus Warehouse', 'jabatan' => 'Supervisor Warehouse'],
            ['nama' => 'Yuni PPIC',      'jabatan' => 'PPIC'],
            ['nama' => 'Dian QC',        'jabatan' => 'QC Manager'],
        ];

        return view('e-approval.workflow', compact('workflows', 'users'));
    }
}
