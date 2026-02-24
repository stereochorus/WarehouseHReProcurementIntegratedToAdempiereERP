@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('page-title', 'Manajemen Data Karyawan')

@section('content')
<div class="py-4">
    <!-- Filter + Add Button -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ route('hr.employees') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Karyawan</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau ID karyawan..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="min-w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Departemen</label>
                <select name="dept" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Departemen</option>
                    @foreach($depts as $d)
                        <option value="{{ $d }}" {{ $dept===$d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Filter</button>
            @if($search || $dept)
            <a href="{{ route('hr.employees') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300">Reset</a>
            @endif
            <a href="{{ route('hr.employees.create') }}" class="ml-auto px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Karyawan
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Karyawan</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($employees) }} karyawan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nama Karyawan</th>
                        <th class="px-4 py-3 text-left">Departemen</th>
                        <th class="px-4 py-3 text-left">Posisi</th>
                        <th class="px-4 py-3 text-left">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-right">Gaji Pokok</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-green-600">{{ $emp['id'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-700 font-bold text-xs">
                                    {{ substr($emp['name'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $emp['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $emp['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs">{{ $emp['dept'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $emp['position'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ date('d M Y', strtotime($emp['join_date'])) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-700">Rp {{ number_format($emp['salary']) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $emp['status']==='Aktif' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $emp['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="alert('Fitur edit karyawan (simulasi)\nID: {{ $emp['id'] }}\nNama: {{ $emp['name'] }}')"
                                    class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-medium transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada karyawan ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
