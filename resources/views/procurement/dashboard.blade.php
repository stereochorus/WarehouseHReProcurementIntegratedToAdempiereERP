@extends('layouts.app')
@section('title', 'Procurement Dashboard')
@section('page-title', 'eProcurement Dashboard')

@section('content')
<div class="py-4">
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total PR</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_pr'] }}</p>
                    <p class="text-xs text-purple-600 mt-1">Sepanjang tahun</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Pending Approval</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending_approval'] }}</p>
                    <p class="text-xs text-amber-500 mt-1">Perlu tindakan</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Approved</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved'] }}</p>
                    <p class="text-xs text-green-500 mt-1">Disetujui</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Ditolak</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                    <p class="text-xs text-red-500 mt-1">Perlu revisi</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Nilai Total PR</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">{{ $stats['total_value'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Tahun ini</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Nilai Bulan Ini</p>
                    <p class="text-lg font-bold text-purple-700 mt-1">{{ $stats['this_month_value'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts + Pending -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Status PR Overview</h3>
            <canvas id="statusChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Nilai PR per Bulan</h3>
            <canvas id="monthlyChart" height="220"></canvas>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Menunggu Approval</h3>
                <a href="{{ route('procurement.approvals') }}" class="text-xs text-purple-600 font-medium hover:text-purple-800">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-100 overflow-y-auto" style="max-height:280px">
                @foreach($approvalPRs as $pr)
                <div class="px-4 py-3 hover:bg-gray-50">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-mono text-xs text-purple-600">{{ $pr['id'] }}</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs">{{ $pr['status'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $pr['item'] }}</p>
                    <p class="text-xs text-gray-500">{{ $pr['dept'] }} | Rp {{ number_format($pr['total']) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Quick Actions + Recent PRs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('procurement.purchase-requests.create') }}" class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors">
                    <span class="text-xl">📋</span>
                    <div><p class="text-sm font-medium text-purple-800">Buat Purchase Request</p><p class="text-xs text-purple-600">Ajukan permintaan pembelian</p></div>
                </a>
                <a href="{{ route('procurement.approvals') }}" class="flex items-center gap-3 p-3 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors">
                    <span class="text-xl">✅</span>
                    <div><p class="text-sm font-medium text-amber-800">Proses Approval</p><p class="text-xs text-amber-600">{{ count($approvalPRs) }} PR menunggu</p></div>
                </a>
                <a href="{{ route('procurement.reports') }}" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                    <span class="text-xl">📊</span>
                    <div><p class="text-sm font-medium text-blue-800">Laporan Pengadaan</p><p class="text-xs text-blue-600">Lihat analitik lengkap</p></div>
                </a>
            </div>
        </div>

        <!-- Recent PRs -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">PR Terkini</h3>
                <a href="{{ route('procurement.purchase-requests') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">No. PR</th>
                            <th class="px-4 py-3 text-left">Item</th>
                            <th class="px-4 py-3 text-left">Dept</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentPRs as $pr)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs text-purple-600">{{ $pr['id'] }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 max-w-xs truncate">{{ $pr['item'] }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $pr['dept'] }}</span></td>
                            <td class="px-4 py-3 text-right font-medium text-gray-700">Rp {{ number_format($pr['total']) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($pr['status']==='Approved') bg-green-100 text-green-700
                                    @elseif($pr['status']==='Rejected') bg-red-100 text-red-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ $pr['status'] }}
                                </span>
                            </td>
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
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved','Pending','Rejected','In Process'],
        datasets: [{ data: [38,14,8,12], backgroundColor: ['#10b981','#f59e0b','#ef4444','#3b82f6'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: ['Sep','Okt','Nov','Des','Jan','Feb'],
        datasets: [{ label: 'Nilai PR (juta)', data: [380,420,310,850,290,547], backgroundColor: '#8b5cf6' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
