<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Services\AdempiereService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HRController extends Controller
{
    private function isDemo(): bool
    {
        return env('DEMO_MODE', 'true') === 'true';
    }

    private function adempiere(): AdempiereService
    {
        return app(AdempiereService::class);
    }

    private function getDummyEmployees(): array
    {
        return [
            ['id'=>'EMP-001','name'=>'Ahmad Fauzi','dept'=>'IT','position'=>'System Analyst','status'=>'Aktif','join_date'=>'2019-03-15','salary'=>12000000,'phone'=>'081234567890','email'=>'ahmad@company.com'],
            ['id'=>'EMP-002','name'=>'Budi Santoso','dept'=>'Warehouse','position'=>'Warehouse Supervisor','status'=>'Aktif','join_date'=>'2018-07-01','salary'=>9500000,'phone'=>'081234567891','email'=>'budi@company.com'],
            ['id'=>'EMP-003','name'=>'Siti Rahayu','dept'=>'HR','position'=>'HR Officer','status'=>'Aktif','join_date'=>'2020-01-10','salary'=>8500000,'phone'=>'081234567892','email'=>'siti@company.com'],
            ['id'=>'EMP-004','name'=>'Dewi Kusuma','dept'=>'Finance','position'=>'Finance Analyst','status'=>'Aktif','join_date'=>'2021-04-05','salary'=>10000000,'phone'=>'081234567893','email'=>'dewi@company.com'],
            ['id'=>'EMP-005','name'=>'Eko Prasetyo','dept'=>'Procurement','position'=>'Procurement Officer','status'=>'Aktif','join_date'=>'2020-08-20','salary'=>9000000,'phone'=>'081234567894','email'=>'eko@company.com'],
            ['id'=>'EMP-006','name'=>'Fitri Handayani','dept'=>'Marketing','position'=>'Marketing Executive','status'=>'Aktif','join_date'=>'2022-02-14','salary'=>8000000,'phone'=>'081234567895','email'=>'fitri@company.com'],
            ['id'=>'EMP-007','name'=>'Gunawan Hadi','dept'=>'Operations','position'=>'Operations Manager','status'=>'Aktif','join_date'=>'2017-05-30','salary'=>15000000,'phone'=>'081234567896','email'=>'gunawan@company.com'],
            ['id'=>'EMP-008','name'=>'Hana Pertiwi','dept'=>'IT','position'=>'Frontend Developer','status'=>'Cuti','join_date'=>'2021-09-12','salary'=>11000000,'phone'=>'081234567897','email'=>'hana@company.com'],
            ['id'=>'EMP-009','name'=>'Irwan Saputra','dept'=>'Finance','position'=>'Accounting Staff','status'=>'Aktif','join_date'=>'2020-11-25','salary'=>8200000,'phone'=>'081234567898','email'=>'irwan@company.com'],
            ['id'=>'EMP-010','name'=>'Joko Widodo','dept'=>'HR','position'=>'HR Manager','status'=>'Aktif','join_date'=>'2016-01-04','salary'=>16000000,'phone'=>'081234567899','email'=>'joko@company.com'],
        ];
    }

    private function getDummyAttendance(): array
    {
        return [
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-001','name'=>'Ahmad Fauzi','check_in'=>'07:58','check_out'=>'17:05','status'=>'Hadir','overtime'=>'1 jam'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-002','name'=>'Budi Santoso','check_in'=>'08:02','check_out'=>'17:00','status'=>'Hadir','overtime'=>'-'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-003','name'=>'Siti Rahayu','check_in'=>'08:15','check_out'=>'17:00','status'=>'Terlambat','overtime'=>'-'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-004','name'=>'Dewi Kusuma','check_in'=>'07:55','check_out'=>'18:30','status'=>'Hadir','overtime'=>'1.5 jam'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-005','name'=>'Eko Prasetyo','check_in'=>'08:00','check_out'=>'17:00','status'=>'Hadir','overtime'=>'-'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-006','name'=>'Fitri Handayani','check_in'=>'-','check_out'=>'-','status'=>'Izin','overtime'=>'-'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-007','name'=>'Gunawan Hadi','check_in'=>'07:45','check_out'=>'17:00','status'=>'Hadir','overtime'=>'-'],
            ['date'=>'24 Feb 2024','emp_id'=>'EMP-008','name'=>'Hana Pertiwi','check_in'=>'-','check_out'=>'-','status'=>'Cuti','overtime'=>'-'],
        ];
    }

    public function dashboard()
    {
        $stats = [
            'total_employees' => 156,
            'active'          => 148,
            'on_leave'        => 7,
            'resigned'        => 1,
            'present_today'   => 142,
            'late_today'      => 8,
            'absent_today'    => 6,
        ];
        $deptStats = [
            ['dept'=>'IT',          'count'=>25, 'percent'=>16],
            ['dept'=>'Warehouse',   'count'=>35, 'percent'=>22],
            ['dept'=>'HR',          'count'=>12, 'percent'=>8],
            ['dept'=>'Finance',     'count'=>18, 'percent'=>12],
            ['dept'=>'Procurement', 'count'=>15, 'percent'=>10],
            ['dept'=>'Marketing',   'count'=>20, 'percent'=>13],
            ['dept'=>'Operations',  'count'=>31, 'percent'=>20],
        ];
        $attendance = array_slice($this->getDummyAttendance(), 0, 5);
        return view('hr.dashboard', compact('stats', 'deptStats', 'attendance'));
    }

    public function employees(Request $request)
    {
        $search = $request->get('search', '');
        $dept   = $request->get('dept', '');

        if ($this->isDemo()) {
            $employees  = $this->getDummyEmployees();
            $allForMeta = $this->getDummyEmployees();
        } else {
            try {
                $employees  = $this->adempiere()->getEmployees();
                $allForMeta = $employees;
            } catch (\Throwable $e) {
                Log::warning('[HR Employees] Fallback ke dummy: ' . $e->getMessage());
                $employees  = $this->getDummyEmployees();
                $allForMeta = $this->getDummyEmployees();
            }
        }

        if ($search) {
            $employees = array_filter($employees, fn($e) =>
                stripos($e['name'], $search) !== false || stripos($e['id'], $search) !== false
            );
        }
        if ($dept) {
            $employees = array_filter($employees, fn($e) => $e['dept'] === $dept);
        }

        $depts = array_unique(array_column($allForMeta, 'dept'));
        return view('hr.employees', compact('employees', 'search', 'dept', 'depts'));
    }

    public function createEmployee()
    {
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Procurement', 'Warehouse'];
        return view('hr.employee-form', compact('departments'));
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'dept'     => 'required|string',
            'position' => 'required|string',
            'email'    => 'required|email',
            'phone'    => 'required|string',
        ]);

        $empId = 'EMP-' . str_pad(rand(156, 999), 3, '0', STR_PAD_LEFT);

        return redirect()->route('hr.employees')
            ->with('success', "Karyawan {$request->name} berhasil ditambahkan! ID: {$empId} (simulasi).");
    }

    public function attendance(Request $request)
    {
        $attendance = $this->getDummyAttendance();
        $date       = $request->get('date', '24 Feb 2024');
        return view('hr.attendance', compact('attendance', 'date'));
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'emp_id'    => 'required',
            'check_in'  => 'required',
            'date'      => 'required|date',
        ]);

        return redirect()->route('hr.attendance')
            ->with('success', "Absensi untuk {$request->emp_id} berhasil dicatat pada {$request->date} (simulasi).");
    }

    public function payroll()
    {
        $payroll = [
            ['emp_id'=>'EMP-001','name'=>'Ahmad Fauzi',  'dept'=>'IT',         'basic'=>12000000,'allowance'=>2500000,'overtime'=>800000, 'deduction'=>1200000,'net'=>14100000,'status'=>'Pending'],
            ['emp_id'=>'EMP-002','name'=>'Budi Santoso', 'dept'=>'Warehouse',  'basic'=>9500000, 'allowance'=>2000000,'overtime'=>0,      'deduction'=>950000, 'net'=>10550000,'status'=>'Diproses'],
            ['emp_id'=>'EMP-003','name'=>'Siti Rahayu',  'dept'=>'HR',         'basic'=>8500000, 'allowance'=>1800000,'overtime'=>0,      'deduction'=>850000, 'net'=>9450000, 'status'=>'Dibayar'],
            ['emp_id'=>'EMP-004','name'=>'Dewi Kusuma',  'dept'=>'Finance',    'basic'=>10000000,'allowance'=>2200000,'overtime'=>1200000,'deduction'=>1000000,'net'=>12400000,'status'=>'Dibayar'],
            ['emp_id'=>'EMP-005','name'=>'Eko Prasetyo', 'dept'=>'Procurement','basic'=>9000000, 'allowance'=>2000000,'overtime'=>0,      'deduction'=>900000, 'net'=>10100000,'status'=>'Pending'],
        ];
        $period = 'Februari 2024';
        return view('hr.payroll', compact('payroll', 'period'));
    }

    public function payrollReport()
    {
        $payroll = [
            ['emp_id'=>'EMP-001','name'=>'Ahmad Fauzi',  'dept'=>'IT',         'basic'=>12000000,'allowance'=>2500000,'overtime'=>800000, 'deduction'=>1200000,'net'=>14100000,'status'=>'Pending'],
            ['emp_id'=>'EMP-002','name'=>'Budi Santoso', 'dept'=>'Warehouse',  'basic'=>9500000, 'allowance'=>2000000,'overtime'=>0,      'deduction'=>950000, 'net'=>10550000,'status'=>'Diproses'],
            ['emp_id'=>'EMP-003','name'=>'Siti Rahayu',  'dept'=>'HR',         'basic'=>8500000, 'allowance'=>1800000,'overtime'=>0,      'deduction'=>850000, 'net'=>9450000, 'status'=>'Dibayar'],
            ['emp_id'=>'EMP-004','name'=>'Dewi Kusuma',  'dept'=>'Finance',    'basic'=>10000000,'allowance'=>2200000,'overtime'=>1200000,'deduction'=>1000000,'net'=>12400000,'status'=>'Dibayar'],
            ['emp_id'=>'EMP-005','name'=>'Eko Prasetyo', 'dept'=>'Procurement','basic'=>9000000, 'allowance'=>2000000,'overtime'=>0,      'deduction'=>900000, 'net'=>10100000,'status'=>'Pending'],
        ];
        $period = 'Februari 2024';
        return view('hr.laporan-payroll', compact('payroll', 'period'));
    }

    public function slipGaji($empId)
    {
        $slipData = [
            'EMP-001' => [
                'emp' => ['id'=>'EMP-001','name'=>'Ahmad Fauzi','nipeg'=>'NIPEG-2019-IT-001','dept'=>'IT','position'=>'System Analyst','grade'=>'III-B','status'=>'Karyawan Tetap','join_date'=>'15 Maret 2019'],
                'penerimaan' => [
                    ['label'=>'Gaji Pokok',         'amount'=>12000000],
                    ['label'=>'Tunjangan Pokok',     'amount'=>1000000],
                    ['label'=>'Tunjangan Beras',     'amount'=>300000],
                    ['label'=>'Insentif Kinerja',    'amount'=>700000],
                    ['label'=>'Tunjangan Pajak',     'amount'=>200000],
                    ['label'=>'Uang Cuti Tahunan',   'amount'=>0],
                    ['label'=>'Upah Lembur',         'amount'=>800000],
                ],
                'potongan' => [
                    ['label'=>'Biaya Jabatan (5%)',  'amount'=>500000],
                    ['label'=>'Premi JHT Karyawan (2%)', 'amount'=>240000],
                    ['label'=>'Premi JP Karyawan (1%)',  'amount'=>120000],
                    ['label'=>'Premi JKK + JKM',    'amount'=>60000],
                    ['label'=>'PPh 21 atas Gaji',   'amount'=>245000],
                    ['label'=>'PPh 21 atas Bonus',  'amount'=>35000],
                ],
            ],
            'EMP-002' => [
                'emp' => ['id'=>'EMP-002','name'=>'Budi Santoso','nipeg'=>'NIPEG-2018-WH-002','dept'=>'Warehouse','position'=>'Warehouse Supervisor','grade'=>'II-A','status'=>'Karyawan Tetap','join_date'=>'01 Juli 2018'],
                'penerimaan' => [
                    ['label'=>'Gaji Pokok',         'amount'=>9500000],
                    ['label'=>'Tunjangan Pokok',     'amount'=>800000],
                    ['label'=>'Tunjangan Beras',     'amount'=>300000],
                    ['label'=>'Insentif Kinerja',    'amount'=>600000],
                    ['label'=>'Tunjangan Pajak',     'amount'=>150000],
                    ['label'=>'Uang Cuti Tahunan',   'amount'=>150000],
                    ['label'=>'Upah Lembur',         'amount'=>0],
                ],
                'potongan' => [
                    ['label'=>'Biaya Jabatan (5%)',  'amount'=>475000],
                    ['label'=>'Premi JHT Karyawan (2%)', 'amount'=>190000],
                    ['label'=>'Premi JP Karyawan (1%)',  'amount'=>95000],
                    ['label'=>'Premi JKK + JKM',    'amount'=>48000],
                    ['label'=>'PPh 21 atas Gaji',   'amount'=>107000],
                    ['label'=>'PPh 21 atas Bonus',  'amount'=>35000],
                ],
            ],
            'EMP-003' => [
                'emp' => ['id'=>'EMP-003','name'=>'Siti Rahayu','nipeg'=>'NIPEG-2020-HR-003','dept'=>'HR','position'=>'HR Officer','grade'=>'II-B','status'=>'Karyawan Tetap','join_date'=>'10 Januari 2020'],
                'penerimaan' => [
                    ['label'=>'Gaji Pokok',         'amount'=>8500000],
                    ['label'=>'Tunjangan Pokok',     'amount'=>700000],
                    ['label'=>'Tunjangan Beras',     'amount'=>300000],
                    ['label'=>'Insentif Kinerja',    'amount'=>500000],
                    ['label'=>'Tunjangan Pajak',     'amount'=>150000],
                    ['label'=>'Uang Cuti Tahunan',   'amount'=>150000],
                    ['label'=>'Upah Lembur',         'amount'=>0],
                ],
                'potongan' => [
                    ['label'=>'Biaya Jabatan (5%)',  'amount'=>425000],
                    ['label'=>'Premi JHT Karyawan (2%)', 'amount'=>170000],
                    ['label'=>'Premi JP Karyawan (1%)',  'amount'=>85000],
                    ['label'=>'Premi JKK + JKM',    'amount'=>43000],
                    ['label'=>'PPh 21 atas Gaji',   'amount'=>98000],
                    ['label'=>'PPh 21 atas Bonus',  'amount'=>29000],
                ],
            ],
            'EMP-004' => [
                'emp' => ['id'=>'EMP-004','name'=>'Dewi Kusuma','nipeg'=>'NIPEG-2021-FIN-004','dept'=>'Finance','position'=>'Finance Analyst','grade'=>'III-A','status'=>'Karyawan Tetap','join_date'=>'05 April 2021'],
                'penerimaan' => [
                    ['label'=>'Gaji Pokok',         'amount'=>10000000],
                    ['label'=>'Tunjangan Pokok',     'amount'=>900000],
                    ['label'=>'Tunjangan Beras',     'amount'=>300000],
                    ['label'=>'Insentif Kinerja',    'amount'=>600000],
                    ['label'=>'Tunjangan Pajak',     'amount'=>200000],
                    ['label'=>'Uang Cuti Tahunan',   'amount'=>200000],
                    ['label'=>'Upah Lembur',         'amount'=>1200000],
                ],
                'potongan' => [
                    ['label'=>'Biaya Jabatan (5%)',  'amount'=>500000],
                    ['label'=>'Premi JHT Karyawan (2%)', 'amount'=>200000],
                    ['label'=>'Premi JP Karyawan (1%)',  'amount'=>100000],
                    ['label'=>'Premi JKK + JKM',    'amount'=>50000],
                    ['label'=>'PPh 21 atas Gaji',   'amount'=>110000],
                    ['label'=>'PPh 21 atas Bonus',  'amount'=>40000],
                ],
            ],
            'EMP-005' => [
                'emp' => ['id'=>'EMP-005','name'=>'Eko Prasetyo','nipeg'=>'NIPEG-2020-PRO-005','dept'=>'Procurement','position'=>'Procurement Officer','grade'=>'II-B','status'=>'Karyawan Tetap','join_date'=>'20 Agustus 2020'],
                'penerimaan' => [
                    ['label'=>'Gaji Pokok',         'amount'=>9000000],
                    ['label'=>'Tunjangan Pokok',     'amount'=>750000],
                    ['label'=>'Tunjangan Beras',     'amount'=>300000],
                    ['label'=>'Insentif Kinerja',    'amount'=>600000],
                    ['label'=>'Tunjangan Pajak',     'amount'=>150000],
                    ['label'=>'Uang Cuti Tahunan',   'amount'=>200000],
                    ['label'=>'Upah Lembur',         'amount'=>0],
                ],
                'potongan' => [
                    ['label'=>'Biaya Jabatan (5%)',  'amount'=>450000],
                    ['label'=>'Premi JHT Karyawan (2%)', 'amount'=>180000],
                    ['label'=>'Premi JP Karyawan (1%)',  'amount'=>90000],
                    ['label'=>'Premi JKK + JKM',    'amount'=>45000],
                    ['label'=>'PPh 21 atas Gaji',   'amount'=>100000],
                    ['label'=>'PPh 21 atas Bonus',  'amount'=>35000],
                ],
            ],
        ];

        if (!isset($slipData[$empId])) {
            return redirect()->route('hr.payroll')->with('info', 'Data slip gaji tidak ditemukan untuk karyawan tersebut.');
        }

        $slip              = $slipData[$empId];
        $slip['period']    = 'Februari 2024';
        $slip['issued']    = '29 Februari 2024';
        $slip['total_penerimaan'] = array_sum(array_column($slip['penerimaan'], 'amount'));
        $slip['total_potongan']   = array_sum(array_column($slip['potongan'],   'amount'));
        $slip['gaji_bersih']      = $slip['total_penerimaan'] - $slip['total_potongan'];

        return view('hr.slip-gaji', compact('slip'));
    }

    public function reports()
    {
        $employees  = $this->getDummyEmployees();
        $attendance = $this->getDummyAttendance();
        return view('hr.reports', compact('employees', 'attendance'));
    }

    // ─── PENGAJUAN CUTI ────────────────────────────────────────────
    private function getDummyLeaves(): array
    {
        return [
            ['id'=>'LV-2024-001','emp_id'=>'EMP-003','name'=>'Siti Rahayu',    'dept'=>'HR',        'type'=>'Cuti Tahunan', 'start'=>'25 Feb 2024','end'=>'27 Feb 2024','days'=>3,'reason'=>'Keperluan keluarga','status'=>'Pending Manager','applied'=>'20 Feb 2024'],
            ['id'=>'LV-2024-002','emp_id'=>'EMP-006','name'=>'Fitri Handayani','dept'=>'Marketing', 'type'=>'Cuti Tahunan', 'start'=>'01 Mar 2024','end'=>'05 Mar 2024','days'=>5,'reason'=>'Liburan keluarga','status'=>'Approved','applied'=>'18 Feb 2024'],
            ['id'=>'LV-2024-003','emp_id'=>'EMP-001','name'=>'Ahmad Fauzi',    'dept'=>'IT',         'type'=>'Cuti Menikah','start'=>'10 Mar 2024','end'=>'13 Mar 2024','days'=>4,'reason'=>'Pernikahan','status'=>'Approved','applied'=>'15 Feb 2024'],
            ['id'=>'LV-2024-004','emp_id'=>'EMP-009','name'=>'Irwan Saputra',  'dept'=>'Finance',   'type'=>'Cuti Tahunan', 'start'=>'15 Mar 2024','end'=>'15 Mar 2024','days'=>1,'reason'=>'Urusan pribadi','status'=>'Rejected','applied'=>'22 Feb 2024'],
            ['id'=>'LV-2024-005','emp_id'=>'EMP-004','name'=>'Dewi Kusuma',    'dept'=>'Finance',   'type'=>'Cuti Tahunan', 'start'=>'20 Mar 2024','end'=>'22 Mar 2024','days'=>3,'reason'=>'Acara keluarga','status'=>'Pending HR','applied'=>'23 Feb 2024'],
        ];
    }

    private function getDummySickLeaves(): array
    {
        return [
            ['id'=>'SK-2024-001','emp_id'=>'EMP-002','name'=>'Budi Santoso',   'dept'=>'Warehouse', 'start'=>'20 Feb 2024','end'=>'21 Feb 2024','days'=>2,'diagnosis'=>'Demam & Flu','doctor'=>'Dr. Hendra','hospital'=>'Klinik Sehat','status'=>'Approved','applied'=>'20 Feb 2024'],
            ['id'=>'SK-2024-002','emp_id'=>'EMP-008','name'=>'Hana Pertiwi',   'dept'=>'IT',         'start'=>'22 Feb 2024','end'=>'24 Feb 2024','days'=>3,'diagnosis'=>'Radang Tenggorokan','doctor'=>'Dr. Sari','hospital'=>'RS Medika','status'=>'Approved','applied'=>'22 Feb 2024'],
            ['id'=>'SK-2024-003','emp_id'=>'EMP-005','name'=>'Eko Prasetyo',   'dept'=>'Procurement','start'=>'24 Feb 2024','end'=>'24 Feb 2024','days'=>1,'diagnosis'=>'Sakit Kepala Migrain','doctor'=>'Dr. Rudi','hospital'=>'Puskesmas','status'=>'Pending HR','applied'=>'24 Feb 2024'],
            ['id'=>'SK-2024-004','emp_id'=>'EMP-007','name'=>'Gunawan Hadi',   'dept'=>'Operations', 'start'=>'26 Feb 2024','end'=>'27 Feb 2024','days'=>2,'diagnosis'=>'Penyakit Lambung','doctor'=>'Dr. Andi','hospital'=>'RS Umum','status'=>'Pending Manager','applied'=>'24 Feb 2024'],
        ];
    }

    private function getDummyOvertime(): array
    {
        return [
            ['id'=>'OT-2024-001','emp_id'=>'EMP-001','name'=>'Ahmad Fauzi',    'dept'=>'IT',         'date'=>'20 Feb 2024','start'=>'17:00','end'=>'20:00','hours'=>3,'desc'=>'Maintenance server & update sistem','status'=>'Approved','applied'=>'19 Feb 2024'],
            ['id'=>'OT-2024-002','emp_id'=>'EMP-004','name'=>'Dewi Kusuma',    'dept'=>'Finance',    'date'=>'21 Feb 2024','start'=>'17:00','end'=>'21:00','hours'=>4,'desc'=>'Closing laporan keuangan bulanan','status'=>'Approved','applied'=>'20 Feb 2024'],
            ['id'=>'OT-2024-003','emp_id'=>'EMP-002','name'=>'Budi Santoso',   'dept'=>'Warehouse',  'date'=>'22 Feb 2024','start'=>'17:00','end'=>'19:00','hours'=>2,'desc'=>'Stock opname barang masuk','status'=>'Pending Manager','applied'=>'22 Feb 2024'],
            ['id'=>'OT-2024-004','emp_id'=>'EMP-005','name'=>'Eko Prasetyo',   'dept'=>'Procurement','date'=>'23 Feb 2024','start'=>'17:00','end'=>'20:30','hours'=>3.5,'desc'=>'Proses tender pengadaan mendesak','status'=>'Approved','applied'=>'22 Feb 2024'],
            ['id'=>'OT-2024-005','emp_id'=>'EMP-003','name'=>'Siti Rahayu',    'dept'=>'HR',          'date'=>'24 Feb 2024','start'=>'17:00','end'=>'19:00','hours'=>2,'desc'=>'Input data rekrutmen karyawan baru','status'=>'Pending HR','applied'=>'23 Feb 2024'],
        ];
    }

    public function leaves(Request $request)
    {
        $leaves = $this->getDummyLeaves();
        $status = $request->get('status', '');
        if ($status) {
            $leaves = array_filter($leaves, fn($l) => $l['status'] === $status);
        }
        $statuses = array_unique(array_column($this->getDummyLeaves(), 'status'));
        return view('hr.leaves', compact('leaves', 'status', 'statuses'));
    }

    public function createLeave()
    {
        $employees = array_column($this->getDummyEmployees(), 'name', 'id');
        $types     = ['Cuti Tahunan','Cuti Menikah','Cuti Melahirkan','Cuti Duka','Cuti Besar'];
        return view('hr.leave-form', compact('employees', 'types'));
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'emp_id'   => 'required',
            'type'     => 'required',
            'start'    => 'required|date',
            'end'      => 'required|date|after_or_equal:start',
            'reason'   => 'required|string',
        ]);
        $id   = 'LV-' . date('Y') . '-' . str_pad(rand(6, 999), 3, '0', STR_PAD_LEFT);
        $days = (new \DateTime($request->start))->diff(new \DateTime($request->end))->days + 1;
        return redirect()->route('hr.leaves')
            ->with('success', "Pengajuan cuti berhasil diajukan! No: {$id}, {$days} hari kerja. Status: Pending Manager (simulasi).");
    }

    public function approveLeave(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);
        $act = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return redirect()->route('hr.leaves')->with('success', "Cuti {$id} berhasil {$act} (simulasi).");
    }

    // ─── PENGAJUAN SAKIT ───────────────────────────────────────────
    public function sickLeaves(Request $request)
    {
        $sickLeaves = $this->getDummySickLeaves();
        $status     = $request->get('status', '');
        if ($status) {
            $sickLeaves = array_filter($sickLeaves, fn($s) => $s['status'] === $status);
        }
        $statuses = array_unique(array_column($this->getDummySickLeaves(), 'status'));
        return view('hr.sick-leaves', compact('sickLeaves', 'status', 'statuses'));
    }

    public function createSickLeave()
    {
        $employees = array_column($this->getDummyEmployees(), 'name', 'id');
        return view('hr.sick-leave-form', compact('employees'));
    }

    public function storeSickLeave(Request $request)
    {
        $request->validate([
            'emp_id'    => 'required',
            'start'     => 'required|date',
            'end'       => 'required|date|after_or_equal:start',
            'diagnosis' => 'required|string',
            'doctor'    => 'required|string',
        ]);
        $id   = 'SK-' . date('Y') . '-' . str_pad(rand(5, 999), 3, '0', STR_PAD_LEFT);
        $days = (new \DateTime($request->start))->diff(new \DateTime($request->end))->days + 1;
        return redirect()->route('hr.sick-leaves')
            ->with('success', "Pengajuan sakit berhasil dicatat! No: {$id}, {$days} hari. Status: Pending HR (simulasi).");
    }

    public function approveSickLeave(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);
        $act = $request->action === 'approve' ? 'dikonfirmasi' : 'ditolak';
        return redirect()->route('hr.sick-leaves')->with('success', "Sakit {$id} berhasil {$act} (simulasi).");
    }

    // ─── PENGAJUAN LEMBUR ──────────────────────────────────────────
    public function overtime(Request $request)
    {
        $overtime = $this->getDummyOvertime();
        $status   = $request->get('status', '');
        if ($status) {
            $overtime = array_filter($overtime, fn($o) => $o['status'] === $status);
        }
        $statuses = array_unique(array_column($this->getDummyOvertime(), 'status'));
        return view('hr.overtime', compact('overtime', 'status', 'statuses'));
    }

    public function createOvertime()
    {
        $employees = array_column($this->getDummyEmployees(), 'name', 'id');
        return view('hr.overtime-form', compact('employees'));
    }

    public function storeOvertime(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
            'date'   => 'required|date',
            'start'  => 'required',
            'end'    => 'required',
            'desc'   => 'required|string',
        ]);
        $id = 'OT-' . date('Y') . '-' . str_pad(rand(6, 999), 3, '0', STR_PAD_LEFT);
        return redirect()->route('hr.overtime')
            ->with('success', "Pengajuan lembur berhasil diajukan! No: {$id}. Status: Pending Manager (simulasi).");
    }

    public function approveOvertime(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);
        $act = $request->action === 'approve' ? 'disetujui' : 'ditolak';
        return redirect()->route('hr.overtime')->with('success', "Lembur {$id} berhasil {$act} (simulasi).");
    }

    // ─── LAPORAN CUTI, SAKIT & LEMBUR ─────────────────────────────
    public function leaveReports()
    {
        $leaves     = $this->getDummyLeaves();
        $sickLeaves = $this->getDummySickLeaves();
        $overtime   = $this->getDummyOvertime();
        return view('hr.leave-reports', compact('leaves', 'sickLeaves', 'overtime'));
    }

    // ─── FORM IZIN ─────────────────────────────────────────────────
    private function getDummyIzin(): array
    {
        return [
            ['no'=>'IZN-2024-011','tanggal'=>'24 Feb 2024','karyawan'=>'Fitri Handayani','dept'=>'Marketing','jenis'=>'Izin Sakit','tanggal_izin'=>'24 Feb 2024','alasan'=>'Tidak enak badan','status'=>'Disetujui'],
            ['no'=>'IZN-2024-010','tanggal'=>'23 Feb 2024','karyawan'=>'Ahmad Fauzi','dept'=>'IT','jenis'=>'Izin Keluar','tanggal_izin'=>'23 Feb 2024','alasan'=>'Urusan bank','status'=>'Disetujui'],
            ['no'=>'IZN-2024-009','tanggal'=>'22 Feb 2024','karyawan'=>'Dewi Kusuma','dept'=>'Finance','jenis'=>'Izin Terlambat','tanggal_izin'=>'22 Feb 2024','alasan'=>'Kemacetan parah','status'=>'Disetujui'],
            ['no'=>'IZN-2024-008','tanggal'=>'21 Feb 2024','karyawan'=>'Irwan Saputra','dept'=>'Finance','jenis'=>'Izin Keluar','tanggal_izin'=>'21 Feb 2024','alasan'=>'Dokter spesialis','status'=>'Ditolak'],
            ['no'=>'IZN-2024-007','tanggal'=>'20 Feb 2024','karyawan'=>'Budi Santoso','dept'=>'Warehouse','jenis'=>'Izin Sakit','tanggal_izin'=>'20 Feb 2024','alasan'=>'Demam tinggi','status'=>'Menunggu'],
        ];
    }

    public function formIzin()
    {
        $izin = $this->getDummyIzin();
        return view('hr.izin', compact('izin'));
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'karyawan'    => 'required|string',
            'jenis'       => 'required|string',
            'tanggal_izin'=> 'required|date',
            'alasan'      => 'required|string',
        ]);
        $no = 'IZN-' . date('Y') . '-' . str_pad(rand(12, 999), 3, '0', STR_PAD_LEFT);
        return redirect()->route('hr.izin')
            ->with('success', "Form izin berhasil diajukan! No: {$no}. Menunggu persetujuan atasan.");
    }

    // ─── PENGAJUAN DINAS ───────────────────────────────────────────
    private function getDummyDinas(): array
    {
        return [
            ['no'=>'DNS-2024-015','tanggal'=>'24 Feb 2024','karyawan'=>'Gunawan Hadi','dept'=>'Operations','lokasi'=>'Surabaya','tgl_mulai'=>'27 Feb 2024','tgl_selesai'=>'29 Feb 2024','tujuan'=>'Audit operasional cabang','status'=>'Disetujui'],
            ['no'=>'DNS-2024-014','tanggal'=>'22 Feb 2024','karyawan'=>'Eko Prasetyo','dept'=>'Procurement','lokasi'=>'Bandung','tgl_mulai'=>'25 Feb 2024','tgl_selesai'=>'26 Feb 2024','tujuan'=>'Negosiasi kontrak vendor','status'=>'Menunggu'],
            ['no'=>'DNS-2024-013','tanggal'=>'21 Feb 2024','karyawan'=>'Ahmad Fauzi','dept'=>'IT','lokasi'=>'Bali','tgl_mulai'=>'01 Mar 2024','tgl_selesai'=>'03 Mar 2024','tujuan'=>'Seminar teknologi','status'=>'Disetujui'],
            ['no'=>'DNS-2024-012','tanggal'=>'20 Feb 2024','karyawan'=>'Dewi Kusuma','dept'=>'Finance','lokasi'=>'Medan','tgl_mulai'=>'22 Feb 2024','tgl_selesai'=>'23 Feb 2024','tujuan'=>'Laporan keuangan regional','status'=>'Disetujui'],
            ['no'=>'DNS-2024-011','tanggal'=>'18 Feb 2024','karyawan'=>'Siti Rahayu','dept'=>'HR','lokasi'=>'Yogyakarta','tgl_mulai'=>'19 Feb 2024','tgl_selesai'=>'19 Feb 2024','tujuan'=>'Rekrutmen karyawan baru','status'=>'Ditolak'],
        ];
    }

    public function pengajuanDinas()
    {
        $dinas = $this->getDummyDinas();
        return view('hr.pengajuan-dinas', compact('dinas'));
    }

    public function storePengajuanDinas(Request $request)
    {
        $request->validate([
            'karyawan'    => 'required|string',
            'lokasi'      => 'required|string',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'tujuan'      => 'required|string',
        ]);
        $no = 'DNS-' . date('Y') . '-' . str_pad(rand(16, 999), 3, '0', STR_PAD_LEFT);
        return redirect()->route('hr.pengajuan-dinas')
            ->with('success', "Pengajuan dinas berhasil diajukan! No: {$no}. Menunggu persetujuan manager.");
    }

    // ─── PENGAJUAN SPJ ─────────────────────────────────────────────
    private function getDummySpj(): array
    {
        return [
            ['no'=>'SPJ-2024-009','tanggal'=>'24 Feb 2024','karyawan'=>'Gunawan Hadi','lokasi'=>'Surabaya','tgl_dinas'=>'20-22 Feb 2024','biaya_transport'=>1200000,'biaya_hotel'=>900000,'biaya_makan'=>450000,'total'=>2550000,'status'=>'Disetujui'],
            ['no'=>'SPJ-2024-008','tanggal'=>'20 Feb 2024','karyawan'=>'Eko Prasetyo','lokasi'=>'Bandung','tgl_dinas'=>'15-16 Feb 2024','biaya_transport'=>350000,'biaya_hotel'=>600000,'biaya_makan'=>200000,'total'=>1150000,'status'=>'Menunggu'],
            ['no'=>'SPJ-2024-007','tanggal'=>'18 Feb 2024','karyawan'=>'Ahmad Fauzi','lokasi'=>'Jakarta','tgl_dinas'=>'14 Feb 2024','biaya_transport'=>150000,'biaya_hotel'=>0,'biaya_makan'=>100000,'total'=>250000,'status'=>'Disetujui'],
            ['no'=>'SPJ-2024-006','tanggal'=>'15 Feb 2024','karyawan'=>'Dewi Kusuma','lokasi'=>'Medan','tgl_dinas'=>'10-12 Feb 2024','biaya_transport'=>2100000,'biaya_hotel'=>1200000,'biaya_makan'=>600000,'total'=>3900000,'status'=>'Disetujui'],
        ];
    }

    public function pengajuanSpj()
    {
        $spj = $this->getDummySpj();
        return view('hr.pengajuan-spj', compact('spj'));
    }

    public function storePengajuanSpj(Request $request)
    {
        $request->validate([
            'karyawan'         => 'required|string',
            'lokasi'           => 'required|string',
            'tgl_dinas'        => 'required|string',
            'biaya_transport'  => 'required|numeric|min:0',
            'biaya_hotel'      => 'required|numeric|min:0',
            'biaya_makan'      => 'required|numeric|min:0',
        ]);
        $no    = 'SPJ-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT);
        $total = $request->biaya_transport + $request->biaya_hotel + $request->biaya_makan;
        return redirect()->route('hr.pengajuan-spj')
            ->with('success', "Pengajuan SPJ berhasil diajukan! No: {$no}. Total: Rp " . number_format($total, 0, ',', '.') . ". Menunggu verifikasi keuangan.");
    }

    // ─── LAPORAN TUNJANGAN PERBULAN ────────────────────────────────
    private function getDummyTunjangan(): array
    {
        return [
            ['nama'=>'Ahmad Fauzi',    'dept'=>'IT',         'jabatan'=>'System Analyst',        'gaji_pokok'=>12000000,'tunjangan_transport'=>500000,'tunjangan_makan'=>400000,'tunjangan_jabatan'=>1500000,'total_tunjangan'=>2400000,'take_home'=>14400000],
            ['nama'=>'Budi Santoso',   'dept'=>'Warehouse',  'jabatan'=>'Warehouse Supervisor',  'gaji_pokok'=>9500000, 'tunjangan_transport'=>400000,'tunjangan_makan'=>350000,'tunjangan_jabatan'=>1000000,'total_tunjangan'=>1750000,'take_home'=>11250000],
            ['nama'=>'Siti Rahayu',    'dept'=>'HR',         'jabatan'=>'HR Officer',            'gaji_pokok'=>8500000, 'tunjangan_transport'=>350000,'tunjangan_makan'=>300000,'tunjangan_jabatan'=>750000, 'total_tunjangan'=>1400000,'take_home'=>9900000],
            ['nama'=>'Dewi Kusuma',    'dept'=>'Finance',    'jabatan'=>'Finance Analyst',       'gaji_pokok'=>10000000,'tunjangan_transport'=>450000,'tunjangan_makan'=>350000,'tunjangan_jabatan'=>1200000,'total_tunjangan'=>2000000,'take_home'=>12000000],
            ['nama'=>'Eko Prasetyo',   'dept'=>'Procurement','jabatan'=>'Procurement Officer',   'gaji_pokok'=>9000000, 'tunjangan_transport'=>400000,'tunjangan_makan'=>300000,'tunjangan_jabatan'=>900000, 'total_tunjangan'=>1600000,'take_home'=>10600000],
            ['nama'=>'Fitri Handayani','dept'=>'Marketing',  'jabatan'=>'Marketing Executive',   'gaji_pokok'=>8000000, 'tunjangan_transport'=>350000,'tunjangan_makan'=>300000,'tunjangan_jabatan'=>750000, 'total_tunjangan'=>1400000,'take_home'=>9400000],
            ['nama'=>'Gunawan Hadi',   'dept'=>'Operations', 'jabatan'=>'Operations Manager',    'gaji_pokok'=>15000000,'tunjangan_transport'=>600000,'tunjangan_makan'=>500000,'tunjangan_jabatan'=>2500000,'total_tunjangan'=>3600000,'take_home'=>18600000],
            ['nama'=>'Hana Pertiwi',   'dept'=>'IT',         'jabatan'=>'Frontend Developer',    'gaji_pokok'=>11000000,'tunjangan_transport'=>450000,'tunjangan_makan'=>350000,'tunjangan_jabatan'=>1000000,'total_tunjangan'=>1800000,'take_home'=>12800000],
            ['nama'=>'Irwan Saputra',  'dept'=>'Finance',    'jabatan'=>'Accounting Staff',      'gaji_pokok'=>8200000, 'tunjangan_transport'=>350000,'tunjangan_makan'=>300000,'tunjangan_jabatan'=>600000, 'total_tunjangan'=>1250000,'take_home'=>9450000],
            ['nama'=>'Joko Widodo',    'dept'=>'HR',         'jabatan'=>'HR Manager',            'gaji_pokok'=>16000000,'tunjangan_transport'=>600000,'tunjangan_makan'=>500000,'tunjangan_jabatan'=>3000000,'total_tunjangan'=>4100000,'take_home'=>20100000],
        ];
    }

    public function tunjanganReport()
    {
        $tunjangan = $this->getDummyTunjangan();
        $bulan     = request('bulan', date('Y-m'));
        return view('hr.tunjangan', compact('tunjangan', 'bulan'));
    }
}
