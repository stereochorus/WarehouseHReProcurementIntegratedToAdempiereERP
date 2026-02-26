@extends('layouts.app')

@section('title', 'Pengajuan SPJ')
@section('page-title', 'Pengajuan SPJ')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pengajuan SPJ</h2>
            <p class="text-sm text-gray-500 mt-0.5">Surat Pertanggungjawaban biaya perjalanan dinas</p>
        </div>
        <button onclick="document.getElementById('modal-spj').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan SPJ
        </button>
    </div>

    {{-- Stats --}}
    @php
        $total        = count($spj);
        $disetujui    = count(array_filter($spj, fn($s) => $s['status'] === 'Disetujui'));
        $menunggu     = count(array_filter($spj, fn($s) => $s['status'] === 'Menunggu'));
        $totalBiaya   = array_sum(array_column($spj, 'total'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total SPJ</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $disetujui }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Menunggu Verifikasi</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $menunggu }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Reimbursement</p>
            <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Pengajuan SPJ</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. SPJ</th>
                        <th class="px-4 py-3 text-left font-semibold">Karyawan</th>
                        <th class="px-4 py-3 text-left font-semibold">Lokasi</th>
                        <th class="px-4 py-3 text-left font-semibold">Tgl Dinas</th>
                        <th class="px-4 py-3 text-right font-semibold">Transport</th>
                        <th class="px-4 py-3 text-right font-semibold">Hotel</th>
                        <th class="px-4 py-3 text-right font-semibold">Makan</th>
                        <th class="px-4 py-3 text-right font-semibold">Total</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($spj as $s)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-green-600">{{ $s['no'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $s['karyawan'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $s['lokasi'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $s['tgl_dinas'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($s['biaya_transport'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($s['biaya_hotel'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($s['biaya_makan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($s['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($s['status']) {
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Menunggu'  => 'bg-amber-100 text-amber-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $s['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Grand Total Reimbursement:</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-blue-700">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="modal-spj" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
            <h3 class="font-bold text-gray-800">Form Pengajuan SPJ</h3>
            <button onclick="document.getElementById('modal-spj').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('hr.pengajuan-spj.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan <span class="text-red-500">*</span></label>
                <input type="text" name="karyawan" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Dinas <span class="text-red-500">*</span></label>
                <input type="text" name="lokasi" required placeholder="Kota/lokasi perjalanan dinas"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Dinas <span class="text-red-500">*</span></label>
                <input type="text" name="tgl_dinas" required placeholder="Contoh: 20-22 Feb 2024"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Transport <span class="text-red-500">*</span></label>
                    <input type="number" name="biaya_transport" required min="0" value="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Hotel <span class="text-red-500">*</span></label>
                    <input type="number" name="biaya_hotel" required min="0" value="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Makan <span class="text-red-500">*</span></label>
                    <input type="number" name="biaya_makan" required min="0" value="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-spj').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">Ajukan SPJ</button>
            </div>
        </form>
    </div>
</div>
@endsection
