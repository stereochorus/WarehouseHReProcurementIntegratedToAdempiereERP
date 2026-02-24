@extends('layouts.app')
@section('title', 'Laporan Pengadaan')
@section('page-title', 'Laporan eProcurement')

@section('content')
<div class="py-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-700 to-purple-900 rounded-xl p-5 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold">Laporan Pengadaan (eProcurement)</h2>
                <p class="text-purple-200 text-sm">Periode: {{ now()->format('F Y') }} | Simulasi Data</p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium">
                🖨 Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    @php
    $totalApproved = array_filter($prs, fn($p)=>$p['status']==='Approved');
    $totalPending  = array_filter($prs, fn($p)=>str_starts_with($p['status'],'Pending'));
    $totalRejected = array_filter($prs, fn($p)=>$p['status']==='Rejected');
    $totalValue    = array_sum(array_column(array_values($totalApproved), 'total'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Total PR</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($prs) }}</p>
            <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ count($totalApproved) }}</p>
            <p class="text-xs text-green-500 mt-1">Approved</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Pending</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ count($totalPending) }}</p>
            <p class="text-xs text-amber-500 mt-1">Proses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Nilai Approved</p>
            <p class="text-lg font-bold text-purple-600 mt-1">Rp {{ number_format($totalValue) }}</p>
            <p class="text-xs text-gray-500 mt-1">Disetujui</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">PR per Departemen</h3>
            <canvas id="deptChart" height="250"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Nilai Pengadaan per Bulan (Rp Juta)</h3>
            <canvas id="valueChart" height="250"></canvas>
        </div>
    </div>

    <!-- PR Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Rekap Semua Purchase Request</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No. PR</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Departemen</th>
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-left">Pemohon</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Prioritas</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($prs as $pr)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-mono text-xs text-purple-600">{{ $pr['id'] }}</td>
                        <td class="px-4 py-2.5 text-gray-600 whitespace-nowrap">{{ $pr['date'] }}</td>
                        <td class="px-4 py-2.5"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $pr['dept'] }}</span></td>
                        <td class="px-4 py-2.5 font-medium text-gray-800">{{ $pr['item'] }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $pr['requestor'] }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp {{ number_format($pr['total']) }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $pr['priority']==='Tinggi' ? 'bg-red-100 text-red-700' : ($pr['priority']==='Rendah' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700') }}">
                                {{ $pr['priority'] }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center">
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
                <tfoot class="bg-purple-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">Total Nilai Semua PR:</td>
                        <td class="px-4 py-3 text-right font-bold text-purple-700">Rp {{ number_format(array_sum(array_column(array_values($prs),'total'))) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: ['IT','Operations','Marketing','HR','Finance','Warehouse'],
        datasets: [{ label: 'Jumlah PR', data: [45,22,18,15,28,32], backgroundColor: '#8b5cf6' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
new Chart(document.getElementById('valueChart'), {
    type: 'line',
    data: {
        labels: ['Sep','Okt','Nov','Des','Jan','Feb'],
        datasets: [
            { label: 'Nilai PR (Juta)', data: [380,420,310,850,290,547], borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.1)', fill: true, tension: 0.4 },
            { label: 'Nilai Approved',  data: [310,380,280,720,260,480], borderColor: '#10b981', fill: false, tension: 0.4 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
