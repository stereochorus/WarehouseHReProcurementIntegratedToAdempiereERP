@extends('layouts.app')
@section('title', 'User Manager')
@section('page-title', 'User Manager — Kelola Pengguna & Hak Akses')

@section('content')
<div class="py-4 fade-in">

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-blue-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Total User</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Aktif</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['aktif'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-red-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Nonaktif</p>
            <p class="text-2xl font-bold text-red-500">{{ $stats['nonaktif'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-purple-200 p-4 shadow-sm">
            <p class="text-xs text-gray-500 mb-1">Manager</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['manager'] }}</p>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl mb-5 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        <div>
            <p class="text-sm font-semibold text-blue-800">Portal Admin — User Manager</p>
            <p class="text-xs text-blue-700 mt-0.5">Anda login sebagai <strong>Administrator</strong>. Anda hanya dapat mengelola user dan hak akses. Untuk mengakses modul transaksi, gunakan akun Manager atau Staff.</p>
        </div>
    </div>

    {{-- Filter & Add Button --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end justify-between">
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama, email, NIP..."
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-48">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Role</label>
                    <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Role</option>
                        <option value="manager" {{ $role === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="staff"   {{ $role === 'staff'   ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="Aktif"    {{ $status === 'Aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ $status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah User Baru
            </a>
        </form>
    </div>

    {{-- User Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Pengguna Sistem</h3>
            <span class="text-sm text-gray-500">{{ count($users) }} user</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">NIP</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-600">Last Login</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-600">Hak Akses</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors {{ $user['status'] === 'Nonaktif' ? 'opacity-60' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0
                                    {{ $user['role'] === 'manager' ? 'bg-purple-500' : 'bg-blue-400' }}">
                                    {{ strtoupper(substr($user['name'], 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $user['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $user['email'] }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $user['nip'] }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $user['role'] === 'manager' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($user['role']) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $user['status'] === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $user['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $user['last_login'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.users.permissions', $user['id']) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                Atur Hak Akses
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.users.toggle', $user['id']) }}"
                                  onsubmit="return confirm('{{ $user['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }} user {{ $user['name'] }}?')">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                        {{ $user['status'] === 'Aktif'
                                            ? 'bg-red-50 hover:bg-red-100 text-red-600'
                                            : 'bg-green-50 hover:bg-green-100 text-green-600' }}">
                                    {{ $user['status'] === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">Tidak ada user ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
            Data dummy — simulasi User Manager. Password default semua user: <code class="bg-gray-200 px-1 rounded">demo123</code>
        </div>
    </div>
</div>
@endsection
