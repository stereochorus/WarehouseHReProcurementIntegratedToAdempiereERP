@extends('layouts.app')

@section('title', 'Surat Jalan')
@section('page-title', 'Pembuatan Surat Jalan')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pembuatan Surat Jalan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola surat jalan pengiriman/pengeluaran barang dari gudang</p>
        </div>
        <button onclick="document.getElementById('modal-sj').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Surat Jalan
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $total    = count($suratJalan);
            $selesai  = count(array_filter($suratJalan, fn($s) => $s['status'] === 'Selesai'));
            $proses   = count(array_filter($suratJalan, fn($s) => $s['status'] === 'Proses'));
            $batal    = count(array_filter($suratJalan, fn($s) => $s['status'] === 'Dibatalkan'));
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total SJ</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</p>
            <p class="text-xs text-blue-600 mt-1">Bulan ini</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $selesai }}</p>
            <p class="text-xs text-gray-400 mt-1">Terkirim</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Dalam Proses</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $proses }}</p>
            <p class="text-xs text-gray-400 mt-1">Sedang dikirim</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Dibatalkan</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $batal }}</p>
            <p class="text-xs text-gray-400 mt-1">Tidak jadi dikirim</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Surat Jalan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. SJ</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Karyawan</th>
                        <th class="px-4 py-3 text-left font-semibold">Barang</th>
                        <th class="px-4 py-3 text-left font-semibold">Tujuan</th>
                        <th class="px-4 py-3 text-left font-semibold">Catatan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($suratJalan as $sj)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-blue-600">{{ $sj['no'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sj['tanggal'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $sj['karyawan'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sj['barang'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sj['tujuan'] }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $sj['catatan'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($sj['status']) {
                                    'Selesai'    => 'bg-green-100 text-green-700',
                                    'Proses'     => 'bg-amber-100 text-amber-700',
                                    'Dibatalkan' => 'bg-red-100 text-red-700',
                                    default      => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $sj['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Form --}}
<div id="modal-sj" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Buat Surat Jalan Baru</h3>
            <button onclick="document.getElementById('modal-sj').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('warehouse.surat-jalan.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan <span class="text-red-500">*</span></label>
                <input type="text" name="karyawan" required placeholder="Nama karyawan pengaju"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barang yang Dikirim <span class="text-red-500">*</span></label>
                <input type="text" name="barang" required placeholder="Nama barang dan jumlah (contoh: Laptop Dell XPS 15 (2 unit))"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Pengiriman <span class="text-red-500">*</span></label>
                <input type="text" name="tujuan" required placeholder="Departemen/lokasi tujuan"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengiriman <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan" rows="2" placeholder="Catatan tambahan (opsional)"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-sj').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Buat Surat Jalan</button>
            </div>
        </form>
    </div>
</div>
@endsection
