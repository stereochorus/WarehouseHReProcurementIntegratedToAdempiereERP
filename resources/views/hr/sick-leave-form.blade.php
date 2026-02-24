@extends('layouts.app')
@section('title', 'Catat Sakit')
@section('page-title', 'Form Pengajuan Sakit')

@section('content')
<div class="py-4 max-w-2xl mx-auto">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('hr.sick-leaves') }}" class="hover:text-green-600">Pengajuan Sakit</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Form Pencatatan</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Form Pengajuan / Pencatatan Sakit</h3>
            <p class="text-xs text-gray-500 mt-0.5">Formulir ini digunakan untuk mencatat ketidakhadiran karena sakit beserta keterangan medis.</p>
        </div>

        <!-- Workflow Info -->
        <div class="px-6 py-3 bg-red-50 border-b border-red-100">
            <p class="text-xs text-red-700 font-medium">Alur Verifikasi:</p>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-medium">Staff / HR</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">Verifikasi HR</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Confirmed</span>
            </div>
        </div>

        <form method="POST" action="{{ route('hr.sick-leaves.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                <select name="emp_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('emp_id') border-red-400 @enderror">
                    <option value="">— Pilih Karyawan —</option>
                    @foreach($employees as $id => $name)
                        <option value="{{ $id }}" {{ old('emp_id') === $id ? 'selected' : '' }}>{{ $id }} — {{ $name }}</option>
                    @endforeach
                </select>
                @error('emp_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Sakit <span class="text-red-500">*</span></label>
                    <input type="date" name="start" value="{{ old('start') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('start') border-red-400 @enderror">
                    @error('start')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end" value="{{ old('end') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('end') border-red-400 @enderror">
                    @error('end')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Days counter display -->
            <div id="days-info" class="hidden px-3 py-2 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-xs text-red-700">Durasi sakit: <span id="days-count" class="font-bold">0</span> hari</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis / Keluhan <span class="text-red-500">*</span></label>
                <input type="text" name="diagnosis" value="{{ old('diagnosis') }}" required
                       placeholder="Contoh: Demam & Flu, Radang Tenggorokan..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('diagnosis') border-red-400 @enderror">
                @error('diagnosis')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dokter <span class="text-red-500">*</span></label>
                    <input type="text" name="doctor" value="{{ old('doctor') }}" required
                           placeholder="Dr. ..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('doctor') border-red-400 @enderror">
                    @error('doctor')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fasilitas Kesehatan</label>
                    <input type="text" name="hospital" value="{{ old('hospital') }}"
                           placeholder="Nama klinik / rumah sakit..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                <p class="text-xs text-amber-700">
                    <strong>Catatan:</strong> Sertakan surat keterangan sakit dari dokter. Pengajuan tanpa keterangan resmi akan diverifikasi oleh HR.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('hr.sick-leaves') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                    Catat Pengajuan Sakit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const startInput = document.querySelector('input[name="start"]');
const endInput   = document.querySelector('input[name="end"]');
const daysInfo   = document.getElementById('days-info');
const daysCount  = document.getElementById('days-count');

function updateDays() {
    if (startInput.value && endInput.value) {
        const start = new Date(startInput.value);
        const end   = new Date(endInput.value);
        if (end >= start) {
            const diff = Math.round((end - start) / (1000 * 60 * 60 * 24)) + 1;
            daysCount.textContent = diff;
            daysInfo.classList.remove('hidden');
        }
    }
}
startInput.addEventListener('change', updateDays);
endInput.addEventListener('change', updateDays);
</script>
@endpush
@endsection
