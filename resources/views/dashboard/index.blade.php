@extends('layouts.app')
@section('title', 'Dashboard Utama')
@section('page-title', 'Dashboard Utama')

@section('content')
<div class="py-4">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-5 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Selamat Datang, {{ session('demo_user.name') }}! 👋</h2>
                <p class="text-blue-200 text-sm mt-1">
                    Login sebagai <span class="font-semibold uppercase bg-white/20 px-2 py-0.5 rounded">{{ session('demo_user.role') }}</span>
                    &nbsp;|&nbsp; {{ now()->format('l, d F Y') }}
                </p>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-blue-200 text-xs">{{ config('app.title') }}</p>
                <p class="text-white font-bold">{{ config('app.short_title') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- Warehouse Stats -->
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Item</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_items']) }}</p>
                    <p class="text-xs text-blue-600 mt-1">Warehouse</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Stok Menipis</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['low_stock_items'] }}</p>
                    <p class="text-xs text-red-500 mt-1">Perlu reorder</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Penerimaan Pending</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending_receiving'] }}</p>
                    <p class="text-xs text-amber-500 mt-1">Menunggu proses</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Karyawan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_employees'] }}</p>
                    <p class="text-xs text-green-600 mt-1">HR Module</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Hadir Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['present_today'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['on_leave'] }} cuti/izin</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">PR Pending</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['pending_pr'] }}</p>
                    <p class="text-xs text-purple-500 mt-1">Total: {{ $stats['total_pr_value'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts + Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Chart: Stock Overview -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                Distribusi Stok per Kategori
            </h3>
            <canvas id="stockChart" height="200"></canvas>
        </div>

        <!-- Chart: Attendance -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                Kehadiran Minggu Ini
            </h3>
            <canvas id="attendanceChart" height="200"></canvas>
        </div>

        <!-- Chart: PR Status -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                Status Purchase Request
            </h3>
            <canvas id="prChart" height="200"></canvas>
        </div>
    </div>

    <!-- Quick Access + Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Quick Access -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Akses Cepat</h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['route'=>'warehouse.receiving',          'icon'=>'📥', 'label'=>'Terima Barang',  'color'=>'bg-blue-50 hover:bg-blue-100 text-blue-700'],
                    ['route'=>'warehouse.issuing',            'icon'=>'📤', 'label'=>'Keluarkan Barang','color'=>'bg-blue-50 hover:bg-blue-100 text-blue-700'],
                    ['route'=>'hr.attendance',                'icon'=>'⏰', 'label'=>'Catat Absensi',  'color'=>'bg-green-50 hover:bg-green-100 text-green-700'],
                    ['route'=>'hr.employees.create',          'icon'=>'👤', 'label'=>'Tambah Karyawan','color'=>'bg-green-50 hover:bg-green-100 text-green-700'],
                    ['route'=>'procurement.purchase-requests.create','icon'=>'📋','label'=>'Buat PR', 'color'=>'bg-purple-50 hover:bg-purple-100 text-purple-700'],
                    ['route'=>'procurement.approvals',        'icon'=>'✅', 'label'=>'Approval PR',    'color'=>'bg-purple-50 hover:bg-purple-100 text-purple-700'],
                ] as $q)
                <a href="{{ route($q['route']) }}"
                   class="flex flex-col items-center p-3 rounded-xl {{ $q['color'] }} transition-colors text-center">
                    <span class="text-2xl mb-1">{{ $q['icon'] }}</span>
                    <span class="text-xs font-medium leading-tight">{{ $q['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Aktivitas Terkini</h3>
                <span class="text-xs text-gray-500">{{ now()->format('d F Y') }}</span>
            </div>
            <div class="space-y-3">
                @foreach($recentActivities as $act)
                <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($act['color'] === 'blue')
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                            </div>
                        @elseif($act['color'] === 'green')
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800">{{ $act['action'] }}</span>
                            <span class="text-xs text-gray-400">{{ $act['time'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $act['desc'] }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        @if($act['color']==='blue') bg-blue-100 text-blue-700
                        @elseif($act['color']==='green') bg-green-100 text-green-700
                        @else bg-purple-100 text-purple-700 @endif
                        font-medium flex-shrink-0">
                        {{ $act['module'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Stock Chart
new Chart(document.getElementById('stockChart'), {
    type: 'doughnut',
    data: {
        labels: ['Elektronik', 'ATK', 'Furniture', 'IT Infra', 'Lainnya'],
        datasets: [{
            data: [458, 312, 145, 89, 243],
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#6b7280'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});

// Attendance Chart
new Chart(document.getElementById('attendanceChart'), {
    type: 'bar',
    data: {
        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        datasets: [
            { label: 'Hadir', data: [145, 148, 143, 150, 142, 90], backgroundColor: '#10b981' },
            { label: 'Absen', data: [11, 8, 13, 6, 14, 66], backgroundColor: '#ef4444' },
        ]
    },
    options: {
        responsive: true,
        scales: { x: { stacked: false }, y: { beginAtZero: true, max: 160 } },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});

// PR Status Chart
new Chart(document.getElementById('prChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending', 'Rejected', 'In Process'],
        datasets: [{
            data: [38, 14, 8, 12],
            backgroundColor: ['#10b981','#f59e0b','#ef4444','#3b82f6'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});
</script>
@endpush
