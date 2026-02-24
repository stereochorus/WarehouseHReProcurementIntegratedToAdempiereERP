@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('page-title', 'Form Data Karyawan Baru')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Form Pendataan Karyawan Baru</h3>
                    <p class="text-xs text-gray-500">Simulasi pendataan karyawan — data tidak disimpan ke database</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('hr.employees.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nama lengkap karyawan"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIK / No. KTP <span class="text-red-500">*</span></label>
                    <input type="text" name="nik" required value="{{ old('nik') }}" placeholder="3XXXXXXXXXXXXXXX"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="karyawan@company.com"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                    <select name="dept" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">-- Pilih --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d }}" {{ old('dept')===$d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Posisi/Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="position" required value="{{ old('position') }}" placeholder="Staff / Officer / Manager"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Karyawan</label>
                    <select name="status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option>Aktif</option>
                        <option>Probation</option>
                        <option>Kontrak</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Bergabung</label>
                    <input type="date" name="join_date" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Gaji Pokok (Rp)</label>
                    <input type="number" name="salary" min="0" value="{{ old('salary') }}" placeholder="8000000"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                <textarea name="address" rows="2" placeholder="Alamat lengkap karyawan..."
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 resize-none">{{ old('address') }}</textarea>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-sm text-amber-800"><strong>Demo Mode:</strong> ID karyawan baru akan digenerate secara otomatis. Data tidak disimpan permanen.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors">
                    Simpan Data Karyawan
                </button>
                <a href="{{ route('hr.employees') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
