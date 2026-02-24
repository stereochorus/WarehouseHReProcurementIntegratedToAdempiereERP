@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Inventory & Stok')

@section('content')
<div class="py-4">
    <!-- Report Header -->
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-xl p-5 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold">Laporan Inventori Warehouse</h2>
                <p class="text-blue-200 text-sm">Periode: {{ now()->format('F Y') }} | Data Simulasi</p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium transition-colors">
                🖨 Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Nilai Inventori per Kategori</h3>
            <canvas id="valueChart" height="250"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Trend Mutasi Barang (30 Hari Terakhir)</h3>
            <canvas id="trendChart" height="250"></canvas>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if(count($lowStock) > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <h3 class="font-semibold text-red-800">Peringatan: {{ count($lowStock) }} Item Stok Menipis</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($lowStock as $item)
            <div class="bg-white border border-red-200 rounded-lg p-3">
                <p class="font-medium text-gray-800 text-sm">{{ $item['name'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['id'] }} | {{ $item['location'] }}</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-red-600 font-bold text-sm">Stok: {{ $item['stock'] }}</span>
                    <span class="text-gray-500 text-xs">Min: {{ $item['min_stock'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Full Inventory Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Rekap Inventori Lengkap</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nama Item</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                        <th class="px-4 py-3 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-right">Total Nilai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $grandTotal = 0; @endphp
                    @foreach($inventory as $item)
                    @php $total = $item['stock'] * $item['price']; $grandTotal += $total; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-mono text-xs text-blue-600">{{ $item['id'] }}</td>
                        <td class="px-4 py-2.5 font-medium text-gray-800">{{ $item['name'] }}</td>
                        <td class="px-4 py-2.5"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $item['category'] }}</span></td>
                        <td class="px-4 py-2.5 text-right text-gray-700">{{ number_format($item['stock']) }} {{ $item['unit'] }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-600">Rp {{ number_format($item['price']) }}</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp {{ number_format($total) }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $item['status']==='Normal' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">Total Nilai Inventori:</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-700 text-base">Rp {{ number_format($grandTotal) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('valueChart'), {
    type: 'pie',
    data: {
        labels: ['Elektronik', 'ATK', 'Furniture', 'IT Infrastructure'],
        datasets: [{ data: [3200000000, 180000000, 210000000, 90000000],
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: Array.from({length:15},(_,i)=>`${i*2+1}/2`),
        datasets: [
            { label: 'Masuk',  data: [12,8,15,6,20,9,3,14,10,8,12,15,7,9,11], borderColor: '#10b981', fill: false, tension: 0.4 },
            { label: 'Keluar', data: [8,12,10,14,8,11,7,9,13,10,8,12,14,10,9], borderColor: '#ef4444', fill: false, tension: 0.4 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
