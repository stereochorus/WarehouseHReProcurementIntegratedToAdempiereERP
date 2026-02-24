<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji — {{ $slip['emp']['name'] }} — {{ $slip['period'] }}</title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }

        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .slip-wrapper { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; }
            .print-break { page-break-inside: avoid; }
            @page { margin: 1cm; size: A4; }
        }
    </style>
</head>
<body class="min-h-screen py-6 px-4">

    <!-- Toolbar (hidden on print) -->
    <div class="no-print max-w-3xl mx-auto mb-4 flex items-center justify-between">
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

    <!-- Slip Card -->
    <div class="slip-wrapper max-w-3xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden print-break">

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
                    <p class="text-xs text-green-300 uppercase tracking-widest font-semibold">Slip Gaji</p>
                    <p class="text-lg font-bold mt-0.5">{{ $slip['period'] }}</p>
                    <p class="text-xs text-green-300 mt-1">Diterbitkan: {{ $slip['issued'] }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 bg-amber-400/20 border border-amber-300/30 text-amber-200 text-xs px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                        DEMO — Data Simulasi
                    </div>
                </div>
            </div>
        </div>

        <!-- ── DATA KARYAWAN ── -->
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Data Karyawan</h2>
            <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Nama</span>
                    <span class="font-semibold text-gray-800">{{ $slip['emp']['name'] }}</span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Jabatan</span>
                    <span class="font-medium text-gray-800">{{ $slip['emp']['position'] }}</span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">NIPEG</span>
                    <span class="font-mono text-gray-700 text-xs">{{ $slip['emp']['nipeg'] }}</span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Departemen</span>
                    <span class="font-medium text-gray-800">{{ $slip['emp']['dept'] }}</span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Grade</span>
                    <span class="font-medium text-gray-800">{{ $slip['emp']['grade'] }}</span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                        {{ $slip['emp']['status'] }}
                    </span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Tgl Bergabung</span>
                    <span class="text-gray-700">{{ $slip['emp']['join_date'] }}</span>
                </div>
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-500 w-28 flex-shrink-0">Periode Gaji</span>
                    <span class="font-medium text-gray-800">{{ $slip['period'] }}</span>
                </div>
            </div>
        </div>

        <!-- ── PENERIMAAN & POTONGAN ── -->
        <div class="grid grid-cols-2 divide-x divide-gray-200 border-b border-gray-200">

            <!-- Penerimaan -->
            <div class="px-6 py-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                    Penerimaan
                </h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($slip['penerimaan'] as $item)
                        <tr>
                            <td class="py-1.5 text-gray-600 pr-3">{{ $item['label'] }}</td>
                            <td class="py-1.5 text-right font-medium {{ $item['amount'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">
                                @if($item['amount'] > 0)
                                    Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3 pt-3 border-t-2 border-green-300 flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-700">Total Penerimaan</span>
                    <span class="text-sm font-bold text-green-700">Rp {{ number_format($slip['total_penerimaan'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Potongan -->
            <div class="px-6 py-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
                    Potongan
                </h3>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($slip['potongan'] as $item)
                        <tr>
                            <td class="py-1.5 text-gray-600 pr-3">{{ $item['label'] }}</td>
                            <td class="py-1.5 text-right font-medium text-gray-800">
                                Rp {{ number_format($item['amount'], 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3 pt-3 border-t-2 border-red-300 flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-700">Total Potongan</span>
                    <span class="text-sm font-bold text-red-600">Rp {{ number_format($slip['total_potongan'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- ── GAJI BERSIH ── -->
        <div class="px-8 py-6 bg-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-200 text-sm">Gaji Bersih (Take Home Pay)</p>
                    <p class="text-xs text-green-300 mt-0.5">Total Penerimaan − Total Potongan</p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-white">
                        Rp {{ number_format($slip['gaji_bersih'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ── TERBILANG ── -->
        <div class="px-8 py-3 bg-green-50 border-b border-green-100">
            <p class="text-xs text-green-700">
                <span class="font-medium">Terbilang:</span>
                <span class="italic">
                    @php
                    // Simple terbilang for display purposes
                    $n = $slip['gaji_bersih'];
                    $jutaan = intdiv($n, 1000000);
                    $ribuan = intdiv($n % 1000000, 1000);
                    $satuan = $n % 1000;
                    $parts = [];
                    if ($jutaan > 0) $parts[] = $jutaan . ' juta';
                    if ($ribuan > 0) $parts[] = number_format($ribuan) . ' ribu';
                    if ($satuan > 0) $parts[] = $satuan;
                    echo implode(' ', $parts) . ' rupiah';
                    @endphp
                </span>
            </p>
        </div>

        <!-- ── REKAP SINGKAT ── -->
        <div class="px-8 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-3 gap-4 text-center text-xs">
                <div>
                    <p class="text-gray-500">Total Penerimaan</p>
                    <p class="font-bold text-green-700 text-sm mt-0.5">Rp {{ number_format($slip['total_penerimaan'], 0, ',', '.') }}</p>
                </div>
                <div class="border-x border-gray-200">
                    <p class="text-gray-500">Total Potongan</p>
                    <p class="font-bold text-red-600 text-sm mt-0.5">Rp {{ number_format($slip['total_potongan'], 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Gaji Bersih</p>
                    <p class="font-bold text-gray-900 text-sm mt-0.5">Rp {{ number_format($slip['gaji_bersih'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- ── TANDA TANGAN ── -->
        <div class="px-8 py-6">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div>
                    <p class="text-gray-500 mb-12">Disiapkan oleh,</p>
                    <div class="border-t border-gray-400 pt-1">
                        <p class="font-medium text-gray-700">Staff Payroll HR</p>
                        <p class="text-gray-500">Departemen HR</p>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 mb-12">Disetujui oleh,</p>
                    <div class="border-t border-gray-400 pt-1">
                        <p class="font-medium text-gray-700">HR Manager</p>
                        <p class="text-gray-500">Departemen HR</p>
                    </div>
                </div>
                <div>
                    <p class="text-gray-500 mb-12">Penerima,</p>
                    <div class="border-t border-gray-400 pt-1">
                        <p class="font-medium text-gray-700">{{ $slip['emp']['name'] }}</p>
                        <p class="text-gray-500">{{ $slip['emp']['nipeg'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── FOOTER ── -->
        <div class="px-8 py-3 bg-gray-100 border-t border-gray-200 flex items-center justify-between">
            <p class="text-xs text-gray-400">Slip gaji ini dicetak secara otomatis oleh sistem dan sah tanpa tanda tangan basah.</p>
            <p class="text-xs text-amber-600 font-medium no-print">⚠ DEMO — Data Simulasi</p>
        </div>
    </div>

    <!-- Bottom spacer (hidden on print) -->
    <div class="no-print h-8"></div>

</body>
</html>
