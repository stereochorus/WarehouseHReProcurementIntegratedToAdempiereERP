@extends('layouts.app')
@section('title', 'Penerimaan Barang')
@section('page-title', 'Form Penerimaan Barang (Good Receipt)')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Form Penerimaan Barang (GR)</h3>
                    <p class="text-xs text-gray-500">Simulasi proses Good Receipt — data tidak disimpan ke database</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('warehouse.receiving.store') }}" class="p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Penerimaan <span class="text-red-500">*</span></label>
                    <input type="date" name="doc_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. PO Referensi</label>
                    <input type="text" name="po_ref" placeholder="PO-2024-0XXX (opsional)"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Supplier / Pemasok <span class="text-red-500">*</span></label>
                <input type="text" name="supplier" required placeholder="Nama perusahaan supplier..."
                       value="{{ old('supplier') }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Item Barang <span class="text-red-500">*</span></label>
                    <select name="item_id" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Item --</option>
                        @foreach($items as $id => $name)
                            <option value="{{ $id }}" {{ old('item_id')===$id ? 'selected' : '' }}>{{ $id }} — {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Diterima <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" min="1" required value="{{ old('quantity', 1) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Penyimpanan</label>
                    <input type="text" name="location" placeholder="RAK-A1" value="{{ old('location') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kondisi Barang</label>
                    <select name="condition" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option>Baik</option>
                        <option>Perlu Pengecekan</option>
                        <option>Rusak</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3" placeholder="Catatan tambahan penerimaan..."
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-sm text-amber-800"><strong>Demo Mode:</strong> No. Dokumen GR akan digenerate otomatis. Data tidak disimpan ke database real.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                    Proses Penerimaan Barang
                </button>
                <a href="{{ route('warehouse.dashboard') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
