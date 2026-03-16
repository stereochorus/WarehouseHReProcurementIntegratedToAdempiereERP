<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserManagerController extends Controller
{
    private function getDummyUsers(): array
    {
        return [
            ['id'=>1,'name'=>'Budi Santoso',   'email'=>'manager@demo.com','nip'=>'EMP-001','role'=>'manager','status'=>'Aktif',  'last_login'=>'16 Mar 2026 08:34'],
            ['id'=>2,'name'=>'Siti Rahayu',    'email'=>'staff@demo.com',  'nip'=>'EMP-003','role'=>'staff',  'status'=>'Aktif',  'last_login'=>'16 Mar 2026 09:10'],
            ['id'=>3,'name'=>'Ahmad Fauzi',    'email'=>'ahmad@demo.com',  'nip'=>'EMP-002','role'=>'staff',  'status'=>'Aktif',  'last_login'=>'15 Mar 2026 14:22'],
            ['id'=>4,'name'=>'Dewi Kusuma',    'email'=>'dewi@demo.com',   'nip'=>'EMP-004','role'=>'staff',  'status'=>'Aktif',  'last_login'=>'15 Mar 2026 11:05'],
            ['id'=>5,'name'=>'Eko Prasetyo',   'email'=>'eko@demo.com',    'nip'=>'EMP-005','role'=>'manager','status'=>'Aktif',  'last_login'=>'14 Mar 2026 16:48'],
            ['id'=>6,'name'=>'Fitri Handayani','email'=>'fitri@demo.com',  'nip'=>'EMP-006','role'=>'staff',  'status'=>'Aktif',  'last_login'=>'13 Mar 2026 10:30'],
            ['id'=>7,'name'=>'Gunawan Hadi',   'email'=>'gunawan@demo.com','nip'=>'EMP-007','role'=>'staff',  'status'=>'Nonaktif','last_login'=>'01 Mar 2026 08:00'],
            ['id'=>8,'name'=>'Hana Pertiwi',   'email'=>'hana@demo.com',   'nip'=>'EMP-008','role'=>'staff',  'status'=>'Aktif',  'last_login'=>'16 Mar 2026 07:55'],
            ['id'=>9,'name'=>'Irwan Saputra',  'email'=>'irwan@demo.com',  'nip'=>'EMP-009','role'=>'staff',  'status'=>'Nonaktif','last_login'=>'28 Feb 2026 13:15'],
            ['id'=>10,'name'=>'Joko Widodo',   'email'=>'joko@demo.com',   'nip'=>'EMP-010','role'=>'manager','status'=>'Aktif',  'last_login'=>'16 Mar 2026 08:01'],
        ];
    }

    private function getMenuMatrix(): array
    {
        return [
            'Warehouse' => [
                'Dashboard Warehouse'   => ['view'],
                'Inventory'             => ['view'],
                'Penerimaan Barang'     => ['view','tambah'],
                'Pengeluaran Barang'    => ['view','tambah'],
                'Mutasi Stok'           => ['view'],
                'Laporan Stok'          => ['view'],
                'Surat Jalan'           => ['view','tambah'],
                'Req ATK'               => ['view','tambah','edit','nonaktif'],
            ],
            'Human Resource' => [
                'Dashboard HR'          => ['view'],
                'Data Karyawan'         => ['view','tambah','edit','nonaktif'],
                'Absensi'               => ['view','tambah'],
                'Payroll'               => ['view'],
                'Pengajuan Cuti'        => ['view','tambah','approve'],
                'Pengajuan Sakit'       => ['view','tambah','approve'],
                'Pengajuan Lembur'      => ['view','tambah','approve'],
                'Form Izin'             => ['view','tambah'],
                'Pengajuan Dinas'       => ['view','tambah'],
                'Pengajuan SPJ'         => ['view','tambah'],
                'Laporan Tunjangan'     => ['view'],
            ],
            'eProcurement' => [
                'Dashboard Procurement' => ['view'],
                'Material Request (MR)' => ['view','tambah','approve'],
                'Purchase Request (PR)' => ['view','tambah','approve'],
                'Purchase Order (PO)'   => ['view','tambah'],
                'Approval PR'           => ['view','approve'],
                'Laporan Pengadaan'     => ['view'],
            ],
            'Aset IT' => [
                'Dashboard Aset IT'     => ['view'],
                'Daftar Aset IT'        => ['view'],
                'Daftarkan Aset'        => ['view','tambah'],
                'History Aset'          => ['view'],
                'Pengeluaran Aset'      => ['view','tambah','approve'],
            ],
            'E-Approval' => [
                'Dashboard E-Approval'  => ['view'],
                'Dokumen Approval'      => ['view','approve','tolak'],
                'Upload Dokumen'        => ['view','tambah'],
                'Workflow Approval'     => ['view','tambah'],
            ],
        ];
    }

    private function getDefaultPermissions(): array
    {
        // Default permissions per role
        return [
            'manager' => [
                'Penerimaan Barang'    => ['view','tambah'],
                'Pengeluaran Barang'   => ['view','tambah'],
                'Data Karyawan'        => ['view','tambah','edit'],
                'Pengajuan Cuti'       => ['view','tambah','approve'],
                'Pengajuan Sakit'      => ['view','tambah','approve'],
                'Material Request (MR)'=> ['view','tambah','approve'],
                'Purchase Request (PR)'=> ['view','tambah','approve'],
                'Approval PR'          => ['view','approve'],
                'Dokumen Approval'     => ['view','approve','tolak'],
            ],
            'staff' => [
                'Inventory'            => ['view'],
                'Penerimaan Barang'    => ['view','tambah'],
                'Pengeluaran Barang'   => ['view','tambah'],
                'Data Karyawan'        => ['view'],
                'Absensi'              => ['view','tambah'],
                'Pengajuan Cuti'       => ['view','tambah'],
                'Pengajuan Sakit'      => ['view','tambah'],
                'Material Request (MR)'=> ['view','tambah'],
                'Purchase Request (PR)'=> ['view','tambah'],
                'Upload Dokumen'       => ['view','tambah'],
            ],
        ];
    }

    public function index(Request $request)
    {
        $users = $this->getDummyUsers();

        if ($role = $request->get('role')) {
            $users = array_filter($users, fn($u) => $u['role'] === $role);
        }
        if ($status = $request->get('status')) {
            $users = array_filter($users, fn($u) => $u['status'] === $status);
        }
        if ($search = $request->get('search')) {
            $users = array_filter($users, fn($u) =>
                str_contains(strtolower($u['name']), strtolower($search)) ||
                str_contains(strtolower($u['email']), strtolower($search)) ||
                str_contains(strtolower($u['nip']), strtolower($search))
            );
        }

        $stats = [
            'total'    => count($this->getDummyUsers()),
            'aktif'    => count(array_filter($this->getDummyUsers(), fn($u) => $u['status'] === 'Aktif')),
            'nonaktif' => count(array_filter($this->getDummyUsers(), fn($u) => $u['status'] === 'Nonaktif')),
            'manager'  => count(array_filter($this->getDummyUsers(), fn($u) => $u['role'] === 'manager')),
        ];

        return view('admin.users.index', compact('users', 'stats', 'role', 'status', 'search'));
    }

    public function create()
    {
        $roles = ['manager', 'staff'];
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email',
            'nip'   => 'required|string|max:20',
            'role'  => 'required|in:manager,staff',
        ]);

        $id = 'EMP-' . str_pad(rand(11, 999), 3, '0', STR_PAD_LEFT);
        return redirect()->route('admin.users.index')
            ->with('success', "User baru berhasil ditambahkan! NIP: {$id} — {$request->name} ({$request->role}). Password default: demo123");
    }

    public function toggle($id)
    {
        $users  = $this->getDummyUsers();
        $user   = collect($users)->firstWhere('id', (int) $id);
        $newStatus = ($user && $user['status'] === 'Aktif') ? 'Nonaktif' : 'Aktif';
        $name  = $user['name'] ?? "User #{$id}";

        return redirect()->route('admin.users.index')
            ->with('success', "Status user {$name} berhasil diubah menjadi {$newStatus}.");
    }

    public function permissions($id)
    {
        $users = $this->getDummyUsers();
        $user  = collect($users)->firstWhere('id', (int) $id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('info', 'User tidak ditemukan.');
        }

        $menuMatrix  = $this->getMenuMatrix();
        $defaultPerms = $this->getDefaultPermissions()[$user['role']] ?? [];

        return view('admin.users.permissions', compact('user', 'menuMatrix', 'defaultPerms'));
    }

    public function savePermissions(Request $request, $id)
    {
        $users = $this->getDummyUsers();
        $user  = collect($users)->firstWhere('id', (int) $id);
        $name  = $user['name'] ?? "User #{$id}";

        return redirect()->route('admin.users.index')
            ->with('success', "Hak akses untuk {$name} berhasil disimpan. Perubahan akan aktif pada login berikutnya.");
    }
}
