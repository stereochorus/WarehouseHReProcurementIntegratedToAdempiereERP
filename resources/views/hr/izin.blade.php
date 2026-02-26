@extends('layouts.app')

@section('title', 'Form Izin')
@section('page-title', 'Form Izin')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Form Izin</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pengajuan izin tidak masuk / izin keluar / izin terlambat</p>
        </div>
        <button onclick="document.getElementById('modal-izin').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Izin
        </button>
    </div>

    {{-- Stats --}}
    @php
        $total     = count($izin);
        $disetujui = count(array_filter($izin, fn($i) => $i['status'] === 'Disetujui'));
        $menunggu  = count(array_filter($izin, fn($i) => $i['status'] === 'Menunggu'));
        $ditolak   = count(array_filter($izin, fn($i) => $i['status'] === 'Ditolak'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Pengajuan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $disetujui }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Menunggu</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $menunggu }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $ditolak }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Riwayat Pengajuan Izin</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. Izin</th>
                        <th class="px-4 py-3 text-left font-semibold">Tgl Ajuan</th>
                        <th class="px-4 py-3 text-left font-semibold">Karyawan</th>
                        <th class="px-4 py-3 text-left font-semibold">Dept</th>
                        <th class="px-4 py-3 text-left font-semibold">Jenis Izin</th>
                        <th class="px-4 py-3 text-left font-semibold">Tgl Izin</th>
                        <th class="px-4 py-3 text-left font-semibold">Alasan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($izin as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-green-600">{{ $item['no'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item['tanggal'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item['karyawan'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs">{{ $item['dept'] }}</span></td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['jenis'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item['tanggal_izin'] }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $item['alasan'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($item['status']) {
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Menunggu'  => 'bg-amber-100 text-amber-700',
                                    'Ditolak'   => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $item['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="modal-izin" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Form Pengajuan Izin</h3>
            <button onclick="document.getElementById('modal-izin').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('hr.izin.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan <span class="text-red-500">*</span></label>
                <input type="text" name="karyawan" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Izin <span class="text-red-500">*</span></label>
                <select name="jenis" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">-- Pilih Jenis Izin --</option>
                    @foreach(['Izin Sakit','Izin Keluar','Izin Terlambat','Izin Tidak Masuk','Izin Keperluan Pribadi'] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Izin <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_izin" required value="{{ date('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                <textarea name="alasan" required rows="3" placeholder="Jelaskan alasan izin..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-izin').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">Ajukan Izin</button>
            </div>
        </form>
    </div>
</div>
@endsection
