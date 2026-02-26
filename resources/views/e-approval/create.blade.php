@extends('layouts.app')

@section('title', 'Ajukan Dokumen')
@section('page-title', 'Ajukan Dokumen untuk Approval')

@section('content')
<div class="pt-4 fade-in">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('e-approval.dashboard') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Form Pengajuan Dokumen</h2>
            <p class="text-sm text-gray-500">Ajukan dokumen untuk proses persetujuan digital</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <form method="POST" action="{{ route('e-approval.documents.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" required value="{{ old('judul') }}"
                           placeholder="Contoh: Proposal Pengadaan Server 2024"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('judul') border-red-400 @enderror">
                    @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen <span class="text-red-500">*</span></label>
                        <select name="jenis" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($jenises as $j)
                            <option value="{{ $j }}" {{ old('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-red-500">*</span></label>
                        <select name="dept" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">-- Pilih Dept --</option>
                            @foreach(['IT','HR','Finance','Marketing','Operations','Procurement','Warehouse'] as $d)
                            <option value="{{ $d }}" {{ old('dept') === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deadline Persetujuan <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_deadline" required value="{{ old('tgl_deadline') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    @error('tgl_deadline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Dokumen</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-teal-400 transition-colors">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-sm text-gray-500">Drag & drop atau klik untuk upload</p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOCX, XLSX — Maks. 10MB</p>
                        <p class="text-xs text-amber-600 mt-2">(Simulasi — file tidak benar-benar disimpan)</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                    <input type="checkbox" name="ttd_digital" id="ttd_digital" value="1" {{ old('ttd_digital') ? 'checked' : '' }}
                           class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
                    <label for="ttd_digital" class="text-sm text-teal-800">
                        <span class="font-medium">Butuh Tanda Tangan Digital</span>
                        <span class="text-teal-600"> — Dokumen akan disimulasikan dengan e-signature setelah disetujui</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Latar Belakang</label>
                    <textarea name="keterangan" rows="4" placeholder="Jelaskan isi dokumen, urgensi, dan informasi relevan lainnya..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('keterangan') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('e-approval.documents') }}"
                       class="px-6 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">Batal</a>
                    <button type="submit"
                            class="px-6 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">Ajukan untuk Approval</button>
                </div>
            </form>
        </div>

        {{-- Info panel --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h4 class="font-semibold text-gray-700 text-sm mb-3">Alur Approval</h4>
                <div class="space-y-3">
                    @foreach(['Manager Departemen','Legal / Compliance','Direktur Keuangan','Direktur Utama'] as $i => $step)
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-teal-600 text-white text-xs rounded-full flex items-center justify-center font-bold flex-shrink-0">{{ $i+1 }}</span>
                        <span class="text-sm text-gray-700">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">Mode Simulasi</p>
                        <p class="text-xs text-amber-700 mt-1">Fitur upload dan TTD digital berjalan sebagai simulasi. Di lingkungan produksi, akan terintegrasi dengan sistem tanda tangan elektronik yang sah secara hukum.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
