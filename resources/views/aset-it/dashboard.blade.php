@extends('layouts.app')

@section('title', 'Aset Inventaris IT')
@section('page-title', 'Aset Inventaris IT')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Dashboard Aset Inventaris IT</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pantau dan kelola seluruh aset IT perusahaan</p>
        </div>
        <a href="{{ route('aset-it.assets.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Daftarkan Aset
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Aset</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
            <p class="text-xs text-indigo-600 mt-1">Unit terdaftar</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Aktif</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['aktif'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Operasional</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Maintenance</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['maintenance'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Sedang diperbaiki</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Tidak Aktif</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['tidak_aktif'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Rusak / pensiun</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
            <p class="text-xs text-indigo-600 uppercase font-semibold">Total Nilai Aset</p>
            <p class="text-lg font-bold text-indigo-700 mt-1">Rp {{ number_format($stats['total_nilai']/1000000, 1) }}jt</p>
            <p class="text-xs text-indigo-500 mt-1">Nilai perolehan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart by kategori --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 text-sm mb-4">Distribusi Aset per Kategori</h3>
            <canvas id="kategoriChart" height="200"></canvas>
        </div>

        {{-- Recent assets --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700 text-sm">Aset Terbaru</h3>
                <a href="{{ route('aset-it.assets') }}" class="text-xs text-indigo-600 hover:underline">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach(array_slice($assets, 0, 6) as $a)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $a['nama'] }}</p>
                        <p class="text-xs text-gray-500">{{ $a['no_seri'] }} — {{ $a['lokasi'] }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $cls = match($a['status']) {
                                'Aktif'       => 'bg-green-100 text-green-700',
                                'Maintenance' => 'bg-amber-100 text-amber-700',
                                'Tidak Aktif' => 'bg-red-100 text-red-700',
                                default       => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $cls }}">{{ $a['status'] }}</span>
                        <p class="text-xs text-gray-400 mt-1">{{ $a['kondisi'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('aset-it.assets') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow group">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-indigo-200 transition-colors">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Daftar Aset</p>
            <p class="text-xs text-gray-500 mt-0.5">Lihat semua aset IT</p>
        </a>
        <a href="{{ route('aset-it.assets.create') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow group">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-200 transition-colors">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Daftarkan Aset</p>
            <p class="text-xs text-gray-500 mt-0.5">Input aset baru ke sistem</p>
        </a>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Maintenance</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $stats['maintenance'] }} aset perlu perhatian</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const labels = @json(array_keys($byKategori));
const data   = @json(array_values($byKategori));
const colors = ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#14b8a6','#f97316','#ec4899','#84cc16'];

new Chart(document.getElementById('kategoriChart'), {
    type: 'doughnut',
    data: {
        labels,
        datasets: [{ data, backgroundColor: colors.slice(0, labels.length), borderWidth: 2 }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'right', labels: { font: { size: 11 } } }
        }
    }
});
</script>
@endpush
