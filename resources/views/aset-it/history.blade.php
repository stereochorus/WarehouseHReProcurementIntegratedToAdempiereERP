@extends('layouts.app')
@section('title', 'History Aset IT')
@section('page-title', 'Riwayat Service & Pemeliharaan Aset IT')

@section('content')
<div class="py-4 fade-in">

    {{-- Header Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Total Record</p>
            <p class="text-2xl font-bold text-indigo-600">{{ count($history) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Total Biaya Service</p>
            <p class="text-lg font-bold text-red-600">Rp {{ number_format($total_biaya, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Perbaikan</p>
            <p class="text-2xl font-bold text-orange-600">{{ count(array_filter($history, fn($h) => $h['jenis'] === 'Perbaikan')) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Pemeliharaan</p>
            <p class="text-2xl font-bold text-green-600">{{ count(array_filter($history, fn($h) => $h['jenis'] === 'Pemeliharaan')) }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari Aset</label>
                <input type="text" name="aset_id" value="{{ $aset_id }}" placeholder="Nama aset..."
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 w-48">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jenis</label>
                <select name="jenis" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Jenis</option>
                    @foreach($jenises as $j)
                    <option value="{{ $j }}" {{ $jenis === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            <a href="{{ route('aset-it.history') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Riwayat Service Aset IT</h3>
            <span class="text-sm text-gray-500">{{ count($history) }} record</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">No. Service</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Aset</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Jenis</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Tanggal</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Teknisi / Vendor</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-gray-600">Biaya</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Keterangan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($history as $h)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600">{{ $h['id'] }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $h['aset'] }}</div>
                            <div class="text-xs text-gray-400">{{ $h['aset_id'] }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $jenisBadge = match($h['jenis']) {
                                    'Perbaikan' => 'bg-red-100 text-red-700',
                                    'Pemeliharaan' => 'bg-green-100 text-green-700',
                                    'Penggantian Baterai' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $jenisBadge }}">{{ $h['jenis'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $h['tanggal'] }}</td>
                        <td class="px-4 py-3">
                            <div class="text-gray-800">{{ $h['teknisi'] }}</div>
                            <div class="text-xs text-gray-400">{{ $h['vendor'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium {{ $h['biaya'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $h['biaya'] > 0 ? 'Rp ' . number_format($h['biaya'], 0, ',', '.') : 'Gratis' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs">
                            <div class="truncate" title="{{ $h['keterangan'] }}">{{ $h['keterangan'] }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusBadge = match($h['status']) {
                                    'Selesai' => 'bg-green-100 text-green-700',
                                    'Tidak Dapat Diperbaiki' => 'bg-red-100 text-red-700',
                                    default => 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">{{ $h['status'] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">Tidak ada riwayat service ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
            Data dummy — simulasi riwayat service & pemeliharaan aset IT
        </div>
    </div>
</div>
@endsection
