@extends('layouts.app')
@section('title', 'Pengeluaran Barang')
@section('page-title', 'Form Pengeluaran Barang (Good Issue)')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4M4 12l8-8 8 8"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Form Pengeluaran Barang (GI)</h3>
                    <p class="text-xs text-gray-500">Simulasi proses Good Issue — data tidak disimpan ke database</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('warehouse.issuing.store') }}" class="p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                    <input type="date" name="doc_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Departemen Penerima <span class="text-red-500">*</span></label>
                    <select name="department" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ old('department')===$dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Item Barang <span class="text-red-500">*</span></label>
                    <select name="item_id" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih Item --</option>
                        @foreach($items as $id => $name)
                            <option value="{{ $id }}" {{ old('item_id')===$id ? 'selected' : '' }}>{{ $id }} — {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" min="1" required value="{{ old('quantity', 1) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Penerima <span class="text-red-500">*</span></label>
                <input type="text" name="recipient" required placeholder="Nama karyawan penerima..."
                       value="{{ old('recipient') }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan / Keperluan <span class="text-red-500">*</span></label>
                <textarea name="purpose" required rows="3" placeholder="Jelaskan tujuan pengeluaran barang ini..."
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 resize-none">{{ old('purpose') }}</textarea>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-sm text-amber-800"><strong>Demo Mode:</strong> Simulasi pengeluaran barang. Stok tidak akan berkurang secara real.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors">
                    Proses Pengeluaran Barang
                </button>
                <a href="{{ route('warehouse.dashboard') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
