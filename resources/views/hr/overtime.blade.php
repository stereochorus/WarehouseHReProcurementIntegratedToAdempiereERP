@extends('layouts.app')
@section('title', 'Pengajuan Lembur')
@section('page-title', 'Manajemen Pengajuan Lembur')

@section('content')
<div class="py-4">

    <!-- Stats -->
    @php
    $totalOT    = count($overtime);
    $approvedOT = count(array_filter($overtime, fn($o)=>$o['status']==='Approved'));
    $pendingOT  = count(array_filter($overtime, fn($o)=>str_starts_with($o['status'],'Pending')));
    $totalHours = array_sum(array_column($overtime, 'hours'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Pengajuan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalOT }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedOT }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Pending</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingOT }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Jam Lembur</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalHours }}</p>
        </div>
    </div>

    <!-- Filter + Tombol -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ route('hr.overtime') }}" class="flex flex-wrap gap-3 items-end">
            <div class="min-w-44">
                <label class="block text-xs font-medium text-gray-500 mb-1">Filter Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $status===$s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Filter</button>
            @if($status)
                <a href="{{ route('hr.overtime') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Reset</a>
            @endif
            <a href="{{ route('hr.overtime.create') }}" class="ml-auto px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Ajukan Lembur
            </a>
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Pengajuan Lembur</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($overtime) }} pengajuan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No.</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Mulai</th>
                        <th class="px-4 py-3 text-center">Selesai</th>
                        <th class="px-4 py-3 text-center">Jam</th>
                        <th class="px-4 py-3 text-left">Deskripsi Pekerjaan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        @if(in_array(session('demo_user.role'),['admin','manager']))
                        <th class="px-4 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($overtime as $ot)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-blue-600">{{ $ot['id'] }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $ot['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $ot['dept'] }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $ot['date'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $ot['start'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-700">{{ $ot['end'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold text-blue-700">{{ $ot['hours'] }}</span>
                            <span class="text-xs text-gray-400"> jam</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $ot['desc'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                @if($ot['status']==='Approved') bg-green-100 text-green-700
                                @elseif($ot['status']==='Rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $ot['status'] }}
                            </span>
                        </td>
                        @if(in_array(session('demo_user.role'),['admin','manager']))
                        <td class="px-4 py-3 text-center">
                            @if(str_starts_with($ot['status'],'Pending'))
                            <div class="flex gap-1 justify-center">
                                <form method="POST" action="{{ route('hr.overtime.approve', $ot['id']) }}" class="inline">
                                    @csrf
                                    <button name="action" value="approve" class="px-2 py-1 bg-green-100 text-green-700 hover:bg-green-200 rounded text-xs font-medium">✓ Setuju</button>
                                </form>
                                <form method="POST" action="{{ route('hr.overtime.approve', $ot['id']) }}" class="inline">
                                    @csrf
                                    <button name="action" value="reject" class="px-2 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded text-xs font-medium">✗ Tolak</button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Tidak ada pengajuan lembur</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
