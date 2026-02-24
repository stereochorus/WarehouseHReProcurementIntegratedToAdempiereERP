@extends('layouts.app')
@section('title', 'Warehouse Dashboard')
@section('page-title', 'Warehouse Dashboard')

@section('content')
<div class="py-4">

    {{-- ── Banner Status Adempiere (hanya ketika DEMO_MODE=false) ─────────── --}}
    @if(!$isDemo)
    <div class="mb-4">
        @if($adempiereConnected)
        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1 text-sm">
                <span class="font-medium text-green-800">Terkoneksi ke Adempiere ERP</span>
                <span class="text-green-600 ml-1.5">— Data ditampilkan secara real-time dari Adempiere.</span>
            </div>
            <a href="{{ route('adempiere.status') }}"
               class="text-xs text-green-700 hover:text-green-900 font-medium flex-shrink-0">
                Status ERP →
            </a>
        </div>
        @else
        <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-amber-800">Adempiere tidak dapat dihubungi — menampilkan data dummy.</p>
                @if($adempiereError)
                <p class="text-xs text-amber-700 mt-0.5 break-words">
                    {{ strlen($adempiereError) > 150 ? substr($adempiereError, 0, 150) . '…' : $adempiereError }}
                </p>
                @endif
            </div>
            <a href="{{ route('adempiere.status') }}"
               class="text-xs text-amber-700 hover:text-amber-900 font-medium flex-shrink-0">
                Diagnostik →
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @php
        $cards = [
            ['label'=>'Total Item',      'value'=>number_format($stats['total_items']), 'sub'=>'SKU aktif',           'c'=>'blue',   'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['label'=>'Total Nilai',     'value'=>$stats['total_value'],                 'sub'=>'Nilai inventori',    'c'=>'green',  'icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['label'=>'Stok Menipis',    'value'=>$stats['low_stock'],                   'sub'=>'Perlu reorder',      'c'=>'red',    'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            ['label'=>'GR Pending',      'value'=>$stats['pending_gr'],                  'sub'=>'Menunggu konfirmasi','c'=>'amber',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Masuk Hari Ini',  'value'=>$stats['today_in'],                    'sub'=>'Penerimaan',         'c'=>'teal',   'icon'=>'M12 4v16m8-8H4'],
            ['label'=>'Keluar Hari Ini', 'value'=>$stats['today_out'],                   'sub'=>'Pengeluaran',        'c'=>'orange', 'icon'=>'M12 20V4M4 12l8-8 8 8'],
        ];
        $ic = ['blue'=>'text-blue-600 bg-blue-100','green'=>'text-green-600 bg-green-100','red'=>'text-red-600 bg-red-100','amber'=>'text-amber-600 bg-amber-100','teal'=>'text-teal-600 bg-teal-100','orange'=>'text-orange-600 bg-orange-100'];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold mt-1 text-gray-800">{{ $card['value'] }}</p>
                    <p class="text-xs mt-1 text-gray-500">{{ $card['sub'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $ic[$card['c']] }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Stok per Kategori</h3>
            <canvas id="categoryChart" height="220"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="font-semibold text-gray-800 mb-4">Mutasi Barang (7 Hari Terakhir)</h3>
            <canvas id="movementChart" height="220"></canvas>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('warehouse.receiving') }}" class="flex items-center gap-4 p-4 bg-blue-600 hover:bg-blue-700 rounded-xl text-white transition-colors shadow-sm">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div><p class="font-semibold">Penerimaan Barang</p><p class="text-blue-200 text-sm">Buat Good Receipt baru</p></div>
        </a>
        <a href="{{ route('warehouse.issuing') }}" class="flex items-center gap-4 p-4 bg-green-600 hover:bg-green-700 rounded-xl text-white transition-colors shadow-sm">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4M4 12l8-8 8 8"/></svg>
            </div>
            <div><p class="font-semibold">Pengeluaran Barang</p><p class="text-green-200 text-sm">Buat Good Issue baru</p></div>
        </a>
        <a href="{{ route('warehouse.inventory') }}" class="flex items-center gap-4 p-4 bg-purple-600 hover:bg-purple-700 rounded-xl text-white transition-colors shadow-sm">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
            </div>
            <div><p class="font-semibold">Lihat Inventory</p><p class="text-purple-200 text-sm">Cek status stok</p></div>
        </a>
    </div>

    <!-- Recent Movements -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Mutasi Barang Terkini</h3>
            <a href="{{ route('warehouse.stock-movement') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">No. Dokumen</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-left">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($movements as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $m['date'] }}</td>
                        <td class="px-4 py-3 font-mono text-blue-600 text-xs">{{ $m['doc_no'] }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $m['type']==='Penerimaan' ? 'bg-green-100 text-green-700' : ($m['type']==='Pengeluaran' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $m['type'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $m['item'] }}</td>
                        <td class="px-4 py-3 text-center font-semibold {{ str_starts_with($m['qty'],'+') ? 'text-green-600' : 'text-red-600' }}">{{ $m['qty'] }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $m['by'] }}</td>
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
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: ['Elektronik', 'ATK', 'Furniture', 'IT Infra', 'Lainnya'],
        datasets: [{ label: 'Jumlah Item', data: [198, 312, 45, 12, 42],
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#8b5cf6','#6b7280'] }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
new Chart(document.getElementById('movementChart'), {
    type: 'line',
    data: {
        labels: ['18/2','19/2','20/2','21/2','22/2','23/2','24/2'],
        datasets: [
            { label: 'Masuk',  data: [12,8,15,6,20,9,3],  borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4 },
            { label: 'Keluar', data: [8,12,10,14,8,11,7], borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)',  fill: true, tension: 0.4 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
