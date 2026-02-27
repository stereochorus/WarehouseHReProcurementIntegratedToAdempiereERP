@extends('layouts.app')

@section('title', 'Purchase Order')
@section('page-title', 'Pembuatan Purchase Order (PO)')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Purchase Order (PO)</h2>
            <p class="text-sm text-gray-500 mt-0.5">Surat pesanan pembelian kepada vendor/supplier</p>
        </div>
        <button onclick="document.getElementById('modal-po').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat PO Baru
        </button>
    </div>

    {{-- Stats --}}
    @php
        $total    = count($pos);
        $diterima = count(array_filter($pos, fn($p) => $p['status'] === 'Diterima'));
        $dikirim  = count(array_filter($pos, fn($p) => $p['status'] === 'Dikirim'));
        $draft    = count(array_filter($pos, fn($p) => $p['status'] === 'Draft'));
        $totalVal = array_sum(array_column($pos, 'total'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total PO</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Sudah Diterima</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $diterima }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Dalam Pengiriman</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $dikirim }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Nilai PO</p>
            <p class="text-lg font-bold text-purple-600 mt-1">Rp {{ number_format($totalVal, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Purchase Order</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. PO</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left font-semibold">Barang</th>
                        <th class="px-4 py-3 text-right font-semibold">Qty</th>
                        <th class="px-4 py-3 text-right font-semibold">Harga Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold">Total</th>
                        <th class="px-4 py-3 text-left font-semibold">Est. Kirim</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pos as $po)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-purple-600">{{ $po['no'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $po['tanggal'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $po['vendor'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $po['barang'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($po['jumlah'], 0, ',', '.') }} {{ $po['satuan'] ?? '' }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($po['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($po['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $po['tgl_kirim'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($po['status']) {
                                    'Diterima'  => 'bg-green-100 text-green-700',
                                    'Dikirim'   => 'bg-blue-100 text-blue-700',
                                    'Diproses'  => 'bg-amber-100 text-amber-700',
                                    'Draft'     => 'bg-gray-100 text-gray-600',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $po['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Nilai PO:</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-purple-700">Rp {{ number_format($totalVal, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="modal-po" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Buat Purchase Order Baru</h3>
            <button onclick="document.getElementById('modal-po').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('procurement.purchase-order.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor/Supplier <span class="text-red-500">*</span></label>
                <select name="vendor" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih Vendor --</option>
                    @foreach($vendors as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                <input type="text" name="barang" required placeholder="Nama barang yang dipesan"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" required min="1" value="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_satuan" required min="1" placeholder="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Tanggal Kirim <span class="text-red-500">*</span></label>
                <input type="date" name="tgl_kirim" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-po').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">Buat PO</button>
            </div>
        </form>
    </div>
</div>
@endsection
