<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Payroll — {{ $period }}</title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .report-wrapper { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; border-radius: 0 !important; }
            @page { margin: 1cm; size: A4 landscape; }
        }
    </style>
</head>
<body class="min-h-screen py-6 px-4">

    <!-- Toolbar (hidden on print) -->
    <div class="no-print max-w-5xl mx-auto mb-4 flex items-center justify-between">
        <a href="{{ route('hr.payroll') }}"
           class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Payroll
        </a>
        <button onclick="window.print()"
                class="flex items-center gap-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 px-5 py-2 rounded-lg shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- Report Card -->
    <div class="report-wrapper max-w-5xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden">

        <!-- ── HEADER PERUSAHAAN ── -->
        <div class="bg-gradient-to-r from-green-700 to-green-900 px-8 py-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center font-bold text-2xl border border-white/30">
                        {{ substr(config('app.short_title', 'W'), 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">{{ config('app.title', 'WHR-ePIS') }}</h1>
                        <p class="text-green-200 text-sm mt-0.5">{{ config('app.short_title', 'WHR-ePIS') }}</p>
                        <p class="text-green-300 text-xs mt-0.5">Jl. Sudirman No. 123, Jakarta Selatan 12190</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-green-300 uppercase tracking-widest font-semibold">Laporan Payroll</p>
                    <p class="text-2xl font-bold mt-0.5">{{ $period }}</p>
                    <p class="text-xs text-green-300 mt-1">Dicetak: {{ date('d F Y') }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 bg-amber-400/20 border border-amber-300/30 text-amber-200 text-xs px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                        DEMO — Data Simulasi
                    </div>
                </div>
            </div>
        </div>

        <!-- ── RINGKASAN EKSEKUTIF ── -->
        @php
        $totalGross  = array_sum(array_column($payroll, 'basic'))
                     + array_sum(array_column($payroll, 'allowance'))
                     + array_sum(array_column($payroll, 'overtime'));
        $totalDed    = array_sum(array_column($payroll, 'deduction'));
        $totalNet    = array_sum(array_column($payroll, 'net'));
        $countPaid   = count(array_filter($payroll, fn($p)=>$p['status']==='Dibayar'));
        $countProses = count(array_filter($payroll, fn($p)=>$p['status']==='Diproses'));
        $countPend   = count(array_filter($payroll, fn($p)=>$p['status']==='Pending'));
        @endphp
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Ringkasan Periode {{ $period }}</h2>
            <div class="grid grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ count($payroll) }}</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-xs text-gray-500">Total Bruto</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">Rp {{ number_format($totalGross, 0, ',', '.') }}</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-xs text-gray-500">Total Potongan</p>
                    <p class="text-sm font-bold text-red-600 mt-1">Rp {{ number_format($totalDed, 0, ',', '.') }}</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-xs text-gray-500">Total Neto</p>
                    <p class="text-sm font-bold text-green-700 mt-1">Rp {{ number_format($totalNet, 0, ',', '.') }}</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-xs text-gray-500">Sudah Dibayar</p>
                    <p class="text-2xl font-bold text-green-600 mt-0.5">{{ $countPaid }}</p>
                </div>
                <div class="text-center border-l border-gray-200">
                    <p class="text-xs text-gray-500">Pending / Proses</p>
                    <p class="text-2xl font-bold text-amber-600 mt-0.5">{{ $countPend + $countProses }}</p>
                </div>
            </div>
        </div>

        <!-- ── TABEL DETAIL PAYROLL ── -->
        <div class="px-8 pt-5 pb-2">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Detail Payroll Karyawan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 text-white text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">ID Karyawan</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Dept</th>
                        <th class="px-4 py-3 text-right">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right">Tunjangan</th>
                        <th class="px-4 py-3 text-right">Lembur</th>
                        <th class="px-4 py-3 text-right">Total Bruto</th>
                        <th class="px-4 py-3 text-right">Potongan</th>
                        <th class="px-4 py-3 text-right font-bold">Gaji Bersih</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($payroll as $i => $p)
                    @php $gross = $p['basic'] + $p['allowance'] + $p['overtime']; @endphp
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-green-700">{{ $p['emp_id'] }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $p['name'] }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $p['dept'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($p['basic'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-blue-700">Rp {{ number_format($p['allowance'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-green-700">
                            {{ $p['overtime'] > 0 ? 'Rp '.number_format($p['overtime'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-800 font-medium">Rp {{ number_format($gross, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-600">Rp {{ number_format($p['deduction'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">Rp {{ number_format($p['net'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($p['status']==='Dibayar') bg-green-100 text-green-700
                                @elseif($p['status']==='Diproses') bg-blue-100 text-blue-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $p['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-green-800 text-white font-bold text-sm">
                        <td colspan="4" class="px-4 py-3 text-left">TOTAL PAYROLL {{ strtoupper($period) }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format(array_sum(array_column($payroll, 'basic')), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format(array_sum(array_column($payroll, 'allowance')), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format(array_sum(array_column($payroll, 'overtime')), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($totalGross, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-300">Rp {{ number_format($totalDed, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-green-200 text-base">Rp {{ number_format($totalNet, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ── CATATAN ── -->
        <div class="px-8 py-4 bg-amber-50 border-t border-amber-100">
            <p class="text-xs text-amber-700">
                <strong>Catatan:</strong> Potongan meliputi BPJS Ketenagakerjaan (JHT 2% + JP 1%), BPJS Kesehatan (1%), dan PPh 21 sesuai peraturan perpajakan yang berlaku.
                Laporan ini merupakan simulasi UI/UX demo — data tidak mencerminkan kondisi nyata.
            </p>
        </div>

        <!-- ── TANDA TANGAN ── -->
        <div class="px-8 py-6 border-t border-gray-200">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div>
                    <p class="text-gray-500 mb-12">Disiapkan oleh,</p>
                    <div class="border-t border-gray-400 pt-1">
                        <p class="font-medium text-gray-700">Staff Payroll HR</p>
                        <p class="text-gray-500">Departemen HR</p>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 mb-12">Diperiksa oleh,</p>
                    <div class="border-t border-gray-400 pt-1">
                        <p class="font-medium text-gray-700">HR Manager</p>
                        <p class="text-gray-500">Departemen HR</p>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 mb-12">Disetujui oleh,</p>
                    <div class="border-t border-gray-400 pt-1">
                        <p class="font-medium text-gray-700">Direktur Keuangan</p>
                        <p class="text-gray-500">Finance & Accounting</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── FOOTER ── -->
        <div class="px-8 py-3 bg-gray-100 border-t border-gray-200 flex items-center justify-between text-xs text-gray-400">
            <p>{{ config('app.title') }} — Laporan Payroll {{ $period }}</p>
            <p class="no-print text-amber-600 font-medium">⚠ DEMO — Data Simulasi</p>
        </div>
    </div>

    <div class="no-print h-8"></div>

</body>
</html>
