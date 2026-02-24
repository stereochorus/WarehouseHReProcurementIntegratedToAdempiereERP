@extends('layouts.app')
@section('title', 'Absensi Karyawan')
@section('page-title', 'Modul Absensi Karyawan')

@section('content')
<div class="py-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Check-In Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
                <div class="px-5 py-4 border-b border-gray-200 bg-green-50 rounded-t-xl">
                    <h3 class="font-semibold text-gray-800">Catat Absensi</h3>
                    <p class="text-xs text-gray-500 mt-1">Simulasi check-in / check-out</p>
                </div>
                <form method="POST" action="{{ route('hr.attendance.store') }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ID Karyawan <span class="text-red-500">*</span></label>
                        <select name="emp_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach(['EMP-001'=>'Ahmad Fauzi','EMP-002'=>'Budi Santoso','EMP-003'=>'Siti Rahayu','EMP-004'=>'Dewi Kusuma','EMP-005'=>'Eko Prasetyo','EMP-006'=>'Fitri Handayani','EMP-007'=>'Gunawan Hadi'] as $id => $name)
                                <option value="{{ $id }}">{{ $id }} — {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Check-In <span class="text-red-500">*</span></label>
                        <input type="time" name="check_in" value="{{ date('H:i') }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Check-Out</label>
                        <input type="time" name="check_out"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika hanya check-in</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select name="attendance_status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm">
                            <option>Hadir</option>
                            <option>Terlambat</option>
                            <option>Izin</option>
                            <option>Sakit</option>
                            <option>Cuti</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors text-sm">
                        Simpan Absensi
                    </button>
                </form>
            </div>

            <!-- Summary Today -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h4 class="font-semibold text-gray-800 mb-3">Ringkasan Hari Ini</h4>
                @php
                $summary = ['Hadir'=>0,'Terlambat'=>0,'Izin'=>0,'Cuti'=>0,'Tidak Hadir'=>0];
                foreach($attendance as $a) {
                    if(isset($summary[$a['status']])) $summary[$a['status']]++;
                }
                $colors = ['Hadir'=>'green','Terlambat'=>'amber','Izin'=>'blue','Cuti'=>'purple','Tidak Hadir'=>'red'];
                @endphp
                <div class="space-y-2">
                    @foreach($summary as $s => $count)
                    <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">{{ $s }}</span>
                        <span class="font-bold text-{{ $colors[$s] }}-600">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Daftar Absensi — {{ now()->format('d F Y') }}</h3>
                    <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">{{ count($attendance) }} karyawan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-center">Check-In</th>
                                <th class="px-4 py-3 text-center">Check-Out</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Lembur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($attendance as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs text-green-600">{{ $a['emp_id'] }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $a['name'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($a['check_in'] !== '-')
                                        <span class="font-medium {{ $a['check_in'] > '08:05' ? 'text-amber-600' : 'text-green-600' }}">
                                            {{ $a['check_in'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $a['check_out'] !== '-' ? $a['check_out'] : '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($a['status']==='Hadir') bg-green-100 text-green-700
                                        @elseif($a['status']==='Terlambat') bg-amber-100 text-amber-700
                                        @elseif($a['status']==='Cuti') bg-blue-100 text-blue-700
                                        @elseif($a['status']==='Izin') bg-purple-100 text-purple-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $a['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $a['overtime'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
