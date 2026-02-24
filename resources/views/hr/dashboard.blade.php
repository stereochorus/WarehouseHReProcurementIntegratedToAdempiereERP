@extends('layouts.app')
@section('title', 'HR Dashboard')
@section('page-title', 'Human Resource Dashboard')

@section('content')
<div class="py-4">

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_employees'] }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $stats['active'] }} aktif</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['present_today'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">dari {{ $stats['total_employees'] }} karyawan</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Terlambat</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['late_today'] }}</p>
                    <p class="text-xs text-amber-500 mt-1">Hari ini</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Cuti/Izin</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['on_leave'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['absent_today'] }} tidak hadir</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Rate Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-800">Tingkat Kehadiran Hari Ini</h3>
            @php $rate = round(($stats['present_today'] / $stats['total_employees']) * 100); @endphp
            <span class="text-2xl font-bold text-green-600">{{ $rate }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="h-4 rounded-full transition-all duration-1000
                {{ $rate >= 90 ? 'bg-green-500' : ($rate >= 75 ? 'bg-amber-500' : 'bg-red-500') }}"
                style="width: {{ $rate }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-2">
            <span>0%</span>
            <span class="{{ $rate >= 90 ? 'text-green-600' : 'text-amber-600' }} font-medium">
                {{ $stats['present_today'] }} hadir dari {{ $stats['total_employees'] }} karyawan
            </span>
            <span>100%</span>
        </div>
    </div>

    <!-- Charts + Dept Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Attendance Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Kehadiran Minggu Ini</h3>
            <canvas id="weeklyAttendance" height="220"></canvas>
        </div>

        <!-- Department Distribution -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Distribusi Karyawan per Departemen</h3>
            <div class="space-y-3">
                @foreach($deptStats as $d)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 font-medium">{{ $d['dept'] }}</span>
                        <span class="text-gray-500">{{ $d['count'] }} karyawan</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full bg-green-500" style="width: {{ $d['percent'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions + Recent Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Aksi Cepat HR</h3>
            <div class="space-y-2">
                <a href="{{ route('hr.employees.create') }}" class="flex items-center gap-3 p-3 bg-green-50 hover:bg-green-100 rounded-xl transition-colors">
                    <span class="text-xl">👤</span>
                    <div>
                        <p class="text-sm font-medium text-green-800">Tambah Karyawan</p>
                        <p class="text-xs text-green-600">Input data karyawan baru</p>
                    </div>
                </a>
                <a href="{{ route('hr.attendance') }}" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                    <span class="text-xl">⏰</span>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Catat Absensi</p>
                        <p class="text-xs text-blue-600">Check-in / check-out</p>
                    </div>
                </a>
                <a href="{{ route('hr.payroll') }}" class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors">
                    <span class="text-xl">💰</span>
                    <div>
                        <p class="text-sm font-medium text-purple-800">Proses Payroll</p>
                        <p class="text-xs text-purple-600">Hitung gaji karyawan</p>
                    </div>
                </a>
                <a href="{{ route('hr.reports') }}" class="flex items-center gap-3 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors">
                    <span class="text-xl">📊</span>
                    <div>
                        <p class="text-sm font-medium text-amber-800">Laporan HR</p>
                        <p class="text-xs text-amber-600">Lihat semua laporan</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Absensi Terkini - {{ now()->format('d F Y') }}</h3>
                <a href="{{ route('hr.attendance') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Check-In</th>
                            <th class="px-4 py-3 text-left">Check-Out</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Lembur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($attendance as $a)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $a['name'] }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                @if($a['check_in'] !== '-')
                                    <span class="{{ $a['check_in'] > '08:00' ? 'text-amber-600' : 'text-green-600' }} font-medium">
                                        {{ $a['check_in'] }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $a['check_out'] !== '-' ? $a['check_out'] : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($a['status']==='Hadir') bg-green-100 text-green-700
                                    @elseif($a['status']==='Terlambat') bg-amber-100 text-amber-700
                                    @elseif($a['status']==='Cuti') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ $a['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $a['overtime'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('weeklyAttendance'), {
    type: 'bar',
    data: {
        labels: ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
        datasets: [
            { label: 'Hadir',    data: [145,148,143,150,142,90], backgroundColor: '#10b981' },
            { label: 'Terlambat',data: [8,5,10,4,8,3],           backgroundColor: '#f59e0b' },
            { label: 'Absen',    data: [3,3,3,2,6,63],           backgroundColor: '#ef4444' },
        ]
    },
    options: {
        responsive: true,
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, max: 160 } },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});
</script>
@endpush
