@extends('layouts.app')
@section('title', 'Laporan Cuti, Sakit & Lembur')
@section('page-title', 'Laporan Cuti, Sakit & Lembur')

@section('content')
<div class="py-4">

    @php
    $totalLeaves     = count($leaves);
    $totalSick       = count($sickLeaves);
    $totalOT         = count($overtime);
    $totalLeaveDays  = array_sum(array_column($leaves, 'days'));
    $totalSickDays   = array_sum(array_column($sickLeaves, 'days'));
    $totalOTHours    = array_sum(array_column($overtime, 'hours'));
    $approvedLeaves  = count(array_filter($leaves,     fn($l)=>$l['status']==='Approved'));
    $approvedSick    = count(array_filter($sickLeaves, fn($s)=>$s['status']==='Approved'));
    $approvedOT      = count(array_filter($overtime,   fn($o)=>$o['status']==='Approved'));
    @endphp

    <!-- Summary Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase font-medium">Pengajuan Cuti</p>
                <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalLeaves }}</p>
            <p class="text-xs text-gray-500 mt-1">Total {{ $totalLeaveDays }} hari · {{ $approvedLeaves }} disetujui</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase font-medium">Pengajuan Sakit</p>
                <span class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalSick }}</p>
            <p class="text-xs text-gray-500 mt-1">Total {{ $totalSickDays }} hari · {{ $approvedSick }} dikonfirmasi</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-gray-500 uppercase font-medium">Pengajuan Lembur</p>
                <span class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalOT }}</p>
            <p class="text-xs text-gray-500 mt-1">Total {{ $totalOTHours }} jam · {{ $approvedOT }} disetujui</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        <!-- Distribusi Status Chart -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-1">Distribusi Status (Cuti)</h3>
            <p class="text-xs text-gray-500 mb-4">Persentase status pengajuan cuti</p>
            <div class="flex justify-center">
                <canvas id="leaveStatusChart" height="200" style="max-width:280px;"></canvas>
            </div>
        </div>

        <!-- Rekap per Departemen Chart -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-1">Jam Lembur per Departemen</h3>
            <p class="text-xs text-gray-500 mb-4">Total jam lembur yang diajukan</p>
            <canvas id="overtimeDeptChart" height="200"></canvas>
        </div>
    </div>

    <!-- Tabs untuk Tabel Detail -->
    <div x-data="{ activeTab: 'leaves' }" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <!-- Tab headers -->
        <div class="flex border-b border-gray-200 bg-gray-50">
            <button @click="activeTab='leaves'"
                    :class="activeTab==='leaves' ? 'border-b-2 border-green-500 text-green-700 bg-white font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-3 text-sm transition-colors">
                Cuti ({{ $totalLeaves }})
            </button>
            <button @click="activeTab='sick'"
                    :class="activeTab==='sick' ? 'border-b-2 border-red-500 text-red-700 bg-white font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-3 text-sm transition-colors">
                Sakit ({{ $totalSick }})
            </button>
            <button @click="activeTab='overtime'"
                    :class="activeTab==='overtime' ? 'border-b-2 border-blue-500 text-blue-700 bg-white font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-5 py-3 text-sm transition-colors">
                Lembur ({{ $totalOT }})
            </button>
        </div>

        <!-- Tab: Cuti -->
        <div x-show="activeTab==='leaves'" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No.</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-center">Hari</th>
                        <th class="px-4 py-3 text-left">Alasan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($leaves as $lv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-green-600">{{ $lv['id'] }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $lv['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $lv['dept'] }}</p>
                        </td>
                        <td class="px-4 py-3"><span class="px-2 py-1 bg-green-50 text-green-700 rounded text-xs">{{ $lv['type'] }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">{{ $lv['start'] }} – {{ $lv['end'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800">{{ $lv['days'] }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate text-xs">{{ $lv['reason'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($lv['status']==='Approved') bg-green-100 text-green-700
                                @elseif($lv['status']==='Rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $lv['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $lv['applied'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tab: Sakit -->
        <div x-show="activeTab==='sick'" style="display:none;" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No.</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Periode</th>
                        <th class="px-4 py-3 text-center">Hari</th>
                        <th class="px-4 py-3 text-left">Diagnosis</th>
                        <th class="px-4 py-3 text-left">Dokter / Faskes</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sickLeaves as $sk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-red-600">{{ $sk['id'] }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $sk['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $sk['dept'] }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">{{ $sk['start'] }} – {{ $sk['end'] }}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800">{{ $sk['days'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 bg-red-50 text-red-700 rounded text-xs">{{ $sk['diagnosis'] }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            <p>{{ $sk['doctor'] }}</p>
                            <p class="text-gray-400">{{ $sk['hospital'] }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($sk['status']==='Approved') bg-green-100 text-green-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $sk['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $sk['applied'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tab: Lembur -->
        <div x-show="activeTab==='overtime'" style="display:none;" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No.</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Mulai–Selesai</th>
                        <th class="px-4 py-3 text-center">Jam</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($overtime as $ot)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-blue-600">{{ $ot['id'] }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $ot['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $ot['dept'] }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">{{ $ot['date'] }}</td>
                        <td class="px-4 py-3 text-center text-xs text-gray-600 whitespace-nowrap">{{ $ot['start'] }} – {{ $ot['end'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold text-blue-700">{{ $ot['hours'] }}</span>
                            <span class="text-xs text-gray-400"> jam</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate text-xs">{{ $ot['desc'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($ot['status']==='Approved') bg-green-100 text-green-700
                                @elseif($ot['status']==='Rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $ot['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $ot['applied'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div><!-- end tabs -->

</div>

@push('scripts')
<script>
// Leave status doughnut chart
(function() {
    const approved = {{ $approvedLeaves }};
    const pending  = {{ $totalLeaves - $approvedLeaves }};
    const ctx = document.getElementById('leaveStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disetujui', 'Pending / Ditolak'],
            datasets: [{
                data: [approved, pending],
                backgroundColor: ['#16a34a', '#f59e0b'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
            }
        }
    });
})();

// Overtime per department bar chart
(function() {
    const deptData = {
        @php
        $otByDept = [];
        foreach ($overtime as $o) {
            $otByDept[$o['dept']] = ($otByDept[$o['dept']] ?? 0) + $o['hours'];
        }
        @endphp
        @foreach($otByDept as $dept => $hours)
        '{{ $dept }}': {{ $hours }},
        @endforeach
    };
    const ctx = document.getElementById('overtimeDeptChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(deptData),
            datasets: [{
                label: 'Total Jam Lembur',
                data: Object.values(deptData),
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 11 } } },
                x: { ticks: { font: { size: 11 } } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
})();
</script>
@endpush
@endsection
