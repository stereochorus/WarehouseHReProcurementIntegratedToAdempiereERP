@extends('layouts.app')

@section('title', 'Daftarkan Aset IT')
@section('page-title', 'Daftarkan Aset IT Baru')

@section('content')
<div class="pt-4 fade-in">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('aset-it.dashboard') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Form Pendaftaran Aset IT</h2>
            <p class="text-sm text-gray-500">Daftarkan aset IT baru ke sistem inventaris</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('aset-it.assets.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aset <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required value="{{ old('nama') }}"
                           placeholder="Contoh: Laptop Dell XPS 15"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('nama') border-red-400 @enderror">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Seri <span class="text-red-500">*</span></label>
                    <input type="text" name="no_seri" required value="{{ old('no_seri') }}"
                           placeholder="Nomor seri perangkat"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merek <span class="text-red-500">*</span></label>
                    <input type="text" name="merek" required value="{{ old('merek') }}"
                           placeholder="Dell, HP, Lenovo, Cisco..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                        <option value="{{ $k }}" {{ old('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pembelian <span class="text-red-500">*</span></label>
                    <input type="number" name="thn_beli" required min="2000" max="{{ date('Y') }}" value="{{ old('thn_beli', date('Y')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Perolehan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="nilai" required min="0" value="{{ old('nilai', 0) }}"
                           placeholder="Harga perolehan aset"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi / Penempatan <span class="text-red-500">*</span></label>
                    <select name="lokasi" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach($lokasis as $l)
                        <option value="{{ $l }}" {{ old('lokasi') === $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab <span class="text-red-500">*</span></label>
                    <input type="text" name="pj" required value="{{ old('pj') }}"
                           placeholder="Nama karyawan PJ aset"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi <span class="text-red-500">*</span></label>
                    <select name="kondisi" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Baik" {{ old('kondisi','Baik') === 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Perlu Servis" {{ old('kondisi') === 'Perlu Servis' ? 'selected' : '' }}>Perlu Servis</option>
                        <option value="Rusak" {{ old('kondisi') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Garansi Sampai</label>
                    <input type="date" name="garansi" value="{{ old('garansi') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="2" placeholder="Spesifikasi, catatan, dll (opsional)"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('aset-it.assets') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">Daftarkan Aset</button>
            </div>
        </form>
    </div>
</div>
@endsection
