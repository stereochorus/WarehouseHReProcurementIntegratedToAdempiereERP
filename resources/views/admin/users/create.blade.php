@extends('layouts.app')
@section('title', 'Tambah User Baru')
@section('page-title', 'Tambah User Baru')

@section('content')
<div class="py-4 max-w-2xl fade-in">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Form Tambah User</h2>
            <p class="text-sm text-gray-500">Tambahkan pengguna baru ke dalam sistem</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Data User Baru</h3>
                    <p class="text-xs text-gray-500">Password default: <code class="bg-gray-200 px-1 rounded">demo123</code> — user dapat menggantinya setelah login pertama</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           placeholder="Nama lengkap karyawan..."
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           placeholder="email@company.com"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIP / ID Karyawan <span class="text-red-500">*</span></label>
                    <input type="text" name="nip" required value="{{ old('nip') }}"
                           placeholder="EMP-011"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm @error('nip') border-red-400 @enderror">
                    @error('nip')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r)
                        <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Departemen</label>
                <select name="dept" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach(['IT','HR','Finance','Marketing','Operations','Procurement','Warehouse','PPIC','QC','Project'] as $d)
                    <option value="{{ $d }}" {{ old('dept') === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Awal</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="Aktif" checked class="text-blue-600"> <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="Nonaktif" class="text-blue-600"> <span class="text-sm text-gray-700">Nonaktif</span>
                    </label>
                </div>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                <strong>Simulasi:</strong> User baru akan ditambahkan ke daftar dengan password default <code>demo123</code>. Setelah disimpan, Admin dapat mengatur hak akses melalui menu <em>Atur Hak Akses</em>.
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors text-sm">
                    Simpan User Baru
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
