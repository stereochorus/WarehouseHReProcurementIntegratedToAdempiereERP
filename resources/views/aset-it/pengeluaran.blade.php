@extends('layouts.app')
@section('title', 'Pengeluaran Aset IT')
@section('page-title', 'Pengeluaran / Write-Off Aset IT')

@section('content')
<div class="py-4 fade-in" x-data="{ showForm: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">Daftar aset yang sudah tidak terpakai atau diajukan untuk dihapus dari inventaris</p>
        </div>
        <button @click="showForm = !showForm"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Pengeluaran Aset
        </button>
    </div>

    {{-- Form (toggle) --}}
    <div x-show="showForm" x-transition class="bg-white rounded-xl border border-indigo-200 shadow-sm p-6 mb-6" style="display:none;">
        <h3 class="font-semibold text-gray-800 mb-4">Form Pengajuan Pengeluaran Aset</h3>
        <form method="POST" action="{{ route('aset-it.pengeluaran.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Aset <span class="text-red-500">*</span></label>
                    <select name="aset_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Aset --</option>
                        @foreach($assets as $a)
                        <option value="{{ $a['id'] }}">{{ $a['id'] }} — {{ $a['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan Pengeluaran <span class="text-red-500">*</span></label>
                <textarea name="alasan" required rows="3" placeholder="Jelaskan alasan aset ini dikeluarkan dari inventaris..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('alasan') }}</textarea>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                <strong>Perhatian:</strong> Pengajuan ini akan diteruskan ke Manager IT untuk persetujuan. Aset tidak akan langsung dihapus dari sistem.
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">Submit Pengajuan</button>
                <button type="button" @click="showForm=false" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Batal</button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Aset Dikeluarkan / Write-Off</h3>
            <span class="text-sm text-gray-500">{{ count($pengeluaran) }} aset</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">No. Dokumen</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Aset</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Kategori</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Thn Beli</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Alasan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Disetujui Oleh</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Tgl Pengeluaran</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pengeluaran as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600">{{ $p['id'] }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $p['aset'] }}</div>
                            <div class="text-xs text-gray-400">{{ $p['aset_id'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $p['kategori'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $p['thn_beli'] }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs">
                            <div class="truncate" title="{{ $p['alasan'] }}">{{ $p['alasan'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $p['disetujui_oleh'] }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $p['tgl_pengeluaran'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($p['status']) {
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Menunggu Persetujuan' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-red-100 text-red-700',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">{{ $p['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
            Data dummy — simulasi pengeluaran / write-off aset IT
        </div>
    </div>
</div>
@endsection
