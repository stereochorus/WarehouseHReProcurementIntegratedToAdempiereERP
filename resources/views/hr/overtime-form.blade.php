@extends('layouts.app')
@section('title', 'Ajukan Lembur')
@section('page-title', 'Form Pengajuan Lembur')

@section('content')
<div class="py-4 max-w-2xl mx-auto">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('hr.overtime') }}" class="hover:text-green-600">Pengajuan Lembur</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Form Pengajuan</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Form Pengajuan Lembur</h3>
            <p class="text-xs text-gray-500 mt-0.5">Ajukan lembur minimal 1 hari sebelum pelaksanaan. Pengajuan mendadak membutuhkan persetujuan langsung dari Manager.</p>
        </div>

        <!-- Workflow Info -->
        <div class="px-6 py-3 bg-blue-50 border-b border-blue-100">
            <p class="text-xs text-blue-700 font-medium">Alur Persetujuan:</p>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-xs font-medium">Staff</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">Manager</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">HR (Payroll)</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">Approved</span>
            </div>
        </div>

        <form method="POST" action="{{ route('hr.overtime.store') }}" class="px-6 py-5 space-y-4">
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

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lembur <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ old('date') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('date') border-red-400 @enderror">
                @error('date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start" value="{{ old('start', '17:00') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('start') border-red-400 @enderror">
                    @error('start')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end" value="{{ old('end') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('end') border-red-400 @enderror">
                    @error('end')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Duration display -->
            <div id="duration-info" class="hidden px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-700">Durasi lembur: <span id="duration-count" class="font-bold">0</span> jam — Estimasi upah lembur: <span id="overtime-pay" class="font-bold text-green-700">Rp 0</span></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Pekerjaan Lembur <span class="text-red-500">*</span></label>
                <textarea name="desc" rows="3" required placeholder="Jelaskan pekerjaan yang akan / telah diselesaikan saat lembur..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 @error('desc') border-red-400 @enderror">{{ old('desc') }}</textarea>
                @error('desc')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                <p class="text-xs text-amber-700">
                    <strong>Kebijakan Lembur:</strong> Upah lembur dihitung 1.5× tarif normal untuk 1-2 jam pertama dan 2× untuk jam selanjutnya, sesuai aturan ketenagakerjaan.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('hr.overtime') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                    Ajukan Lembur
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const startInput    = document.querySelector('input[name="start"]');
const endInput      = document.querySelector('input[name="end"]');
const durationInfo  = document.getElementById('duration-info');
const durationCount = document.getElementById('duration-count');
const overtimePay   = document.getElementById('overtime-pay');

function updateDuration() {
    if (startInput.value && endInput.value) {
        const [sh, sm] = startInput.value.split(':').map(Number);
        const [eh, em] = endInput.value.split(':').map(Number);
        const totalMin = (eh * 60 + em) - (sh * 60 + sm);
        if (totalMin > 0) {
            const hours = (totalMin / 60).toFixed(1);
            durationCount.textContent = hours;
            // Simulate overtime pay at Rp 50,000/hour
            const pay = (totalMin / 60) * 50000;
            overtimePay.textContent = 'Rp ' + pay.toLocaleString('id-ID');
            durationInfo.classList.remove('hidden');
        }
    }
}
startInput.addEventListener('change', updateDuration);
endInput.addEventListener('change', updateDuration);
</script>
@endpush
@endsection
