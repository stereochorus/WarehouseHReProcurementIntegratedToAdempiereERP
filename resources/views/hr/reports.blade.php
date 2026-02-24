@extends('layouts.app')
@section('title', 'Laporan HR')
@section('page-title', 'Laporan Human Resource')

@section('content')
<div class="py-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-700 to-teal-800 rounded-xl p-5 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold">Laporan HR Komprehensif</h2>
                <p class="text-green-200 text-sm">Periode: {{ now()->format('F Y') }} | Simulasi Data</p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium">
                🖨 Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Distribusi Karyawan per Departemen</h3>
            <canvas id="deptChart" height="250"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Trend Kehadiran Bulanan</h3>
            <canvas id="trendChart" height="250"></canvas>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label'=>'Total Karyawan','value'=>count($employees),'color'=>'blue'],
            ['label'=>'Aktif',         'value'=>count(array_filter($employees,fn($e)=>$e['status']==='Aktif')), 'color'=>'green'],
            ['label'=>'Cuti/Izin',     'value'=>count(array_filter($employees,fn($e)=>$e['status']==='Cuti')),  'color'=>'amber'],
            ['label'=>'Rata-rata Gaji','value'=>'Rp '.number_format(array_sum(array_column($employees,'salary'))/count($employees)),  'color'=>'purple'],
        ] as $s)
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">{{ $s['label'] }}</p>
            <p class="text-xl font-bold text-{{ $s['color'] }}-600 mt-1">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Attendance Summary Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Rekap Absensi Hari Ini</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-center">Check-In</th>
                        <th class="px-4 py-3 text-center">Check-Out</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Lembur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($attendance as $a)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-mono text-xs text-green-600">{{ $a['emp_id'] }}</td>
                        <td class="px-4 py-2.5 font-medium text-gray-800">{{ $a['name'] }}</td>
                        <td class="px-4 py-2.5 text-center font-medium {{ $a['check_in']!=='-'&&$a['check_in']>'08:05' ? 'text-amber-600' : 'text-green-600' }}">
                            {{ $a['check_in'] !== '-' ? $a['check_in'] : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-center text-gray-600">{{ $a['check_out'] !== '-' ? $a['check_out'] : '—' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($a['status']==='Hadir') bg-green-100 text-green-700
                                @elseif($a['status']==='Terlambat') bg-amber-100 text-amber-700
                                @elseif($a['status']==='Cuti') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $a['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center text-xs text-gray-500">{{ $a['overtime'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('deptChart'), {
    type: 'doughnut',
    data: {
        labels: ['IT','Warehouse','HR','Finance','Procurement','Marketing','Operations'],
        datasets: [{ data: [25,35,12,18,15,20,31],
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#f97316'],
            borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
});
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: ['Okt','Nov','Des','Jan','Feb'],
        datasets: [
            { label: 'Hadir',    data: [145,148,140,147,142], borderColor: '#10b981', fill: false, tension: 0.4 },
            { label: 'Terlambat',data: [8,6,12,5,8],          borderColor: '#f59e0b', fill: false, tension: 0.4 },
            { label: 'Absen',    data: [3,2,4,4,6],           borderColor: '#ef4444', fill: false, tension: 0.4 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
