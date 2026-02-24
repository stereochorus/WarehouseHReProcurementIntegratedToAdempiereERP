@extends('layouts.app')
@section('title', 'Buat Purchase Request')
@section('page-title', 'Form Purchase Request Baru')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-purple-50 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Form Purchase Request (PR)</h3>
                    <p class="text-xs text-gray-500">Simulasi pengajuan PR — akan masuk ke workflow approval</p>
                </div>
            </div>
        </div>

        <!-- Workflow Info -->
        <div class="px-6 pt-4">
            <div class="flex items-center gap-0">
                @foreach(['Pemohon','Manager','Finance','Purchasing','Selesai'] as $i => $step)
                <div class="flex items-center {{ $i < 4 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0 ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="text-xs text-gray-500 mt-1 whitespace-nowrap">{{ $step }}</span>
                    </div>
                    @if($i < 4)
                    <div class="flex-1 h-0.5 bg-gray-200 mx-1 mb-4"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('procurement.purchase-requests.store') }}" class="p-6 space-y-5"
              x-data="{ qty: 1, price: 0, get total() { return this.qty * this.price; } }">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pengajuan</label>
                    <input type="date" name="pr_date" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                    <select name="dept" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d }}" {{ old('dept')===$d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Item / Barang <span class="text-red-500">*</span></label>
                <input type="text" name="item" required value="{{ old('item') }}" placeholder="Nama barang atau jasa yang diminta..."
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="qty" min="1" required x-model="qty" value="{{ old('qty', 1) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                    <select name="unit" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        @foreach(['Unit','Pcs','Set','Rim','Lusin','Meter','Kg','Liter'] as $u)
                            <option>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estimasi Harga/Satuan <span class="text-red-500">*</span></label>
                    <input type="number" name="est_price" min="1" required x-model="price" value="{{ old('est_price', 0) }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                           placeholder="0">
                </div>
            </div>

            <!-- Total Display -->
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-purple-800">Estimasi Total Nilai PR:</span>
                    <span class="text-xl font-bold text-purple-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)">Rp 0</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Prioritas</label>
                    <select name="priority" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        <option>Normal</option>
                        <option>Tinggi</option>
                        <option>Rendah</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Vendor (opsional)</label>
                    <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="Nama vendor jika sudah ada..."
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan / Justifikasi Pembelian <span class="text-red-500">*</span></label>
                <textarea name="reason" required rows="3" value="{{ old('reason') }}"
                          placeholder="Jelaskan kebutuhan dan alasan pengadaan ini..."
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm resize-none">{{ old('reason') }}</textarea>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-sm text-amber-800"><strong>Demo Mode:</strong> PR akan dikirim ke workflow approval simulasi. No. PR digenerate otomatis.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors">
                    Ajukan Purchase Request
                </button>
                <a href="{{ route('procurement.purchase-requests') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
