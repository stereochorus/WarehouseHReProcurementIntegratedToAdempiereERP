@extends('layouts.app')

@section('title', 'Daftar Aset IT')
@section('page-title', 'Daftar Aset IT')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Aset IT</h2>
            <p class="text-sm text-gray-500 mt-0.5">Inventaris lengkap aset IT perusahaan</p>
        </div>
        <a href="{{ route('aset-it.assets.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Daftarkan Aset
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('aset-it.assets') }}" class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari Aset</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama, No. Seri, Lokasi..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                    <option value="{{ $k }}" {{ ($kategori ?? '') === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    @foreach(['Aktif','Maintenance','Tidak Aktif'] as $s)
                    <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Filter</button>
            <a href="{{ route('aset-it.assets') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Reset</a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Inventaris Aset IT</h3>
            <span class="text-xs text-gray-400">{{ count($assets) }} aset ditemukan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">ID Aset</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama Aset</th>
                        <th class="px-4 py-3 text-left font-semibold">No. Seri</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Merek</th>
                        <th class="px-4 py-3 text-right font-semibold">Tahun</th>
                        <th class="px-4 py-3 text-right font-semibold">Nilai</th>
                        <th class="px-4 py-3 text-left font-semibold">Lokasi</th>
                        <th class="px-4 py-3 text-left font-semibold">PJ</th>
                        <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Garansi S/D</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $a['id'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $a['nama'] }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $a['no_seri'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs">{{ $a['kategori'] }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ $a['merek'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $a['thn_beli'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($a['nilai'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $a['lokasi'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $a['pj'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $kc = match($a['kondisi']) {
                                    'Baik'         => 'text-green-600',
                                    'Perlu Servis' => 'text-amber-600',
                                    'Rusak'        => 'text-red-600',
                                    default        => 'text-gray-600',
                                };
                            @endphp
                            <span class="text-xs font-medium {{ $kc }}">{{ $a['kondisi'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($a['status']) {
                                    'Aktif'       => 'bg-green-100 text-green-700',
                                    'Maintenance' => 'bg-amber-100 text-amber-700',
                                    'Tidak Aktif' => 'bg-red-100 text-red-700',
                                    default       => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $a['status'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $a['garansi_s/d'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-8 text-center text-gray-400 text-sm">Tidak ada aset ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
