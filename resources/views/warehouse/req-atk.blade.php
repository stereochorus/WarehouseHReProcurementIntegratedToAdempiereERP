@extends('layouts.app')

@section('title', 'Pengajuan Req ATK')
@section('page-title', 'Pengajuan Req ATK')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Pengajuan Req ATK</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pengajuan kebutuhan Alat Tulis Kantor dari departemen</p>
        </div>
        <button onclick="document.getElementById('modal-atk').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Req ATK
        </button>
    </div>

    {{-- Stats --}}
    @php
        $total     = count($reqAtk);
        $disetujui = count(array_filter($reqAtk, fn($r) => $r['status'] === 'Disetujui'));
        $menunggu  = count(array_filter($reqAtk, fn($r) => $r['status'] === 'Menunggu'));
        $ditolak   = count(array_filter($reqAtk, fn($r) => $r['status'] === 'Ditolak'));
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
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Pengajuan ATK</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. ATK</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Karyawan</th>
                        <th class="px-4 py-3 text-left font-semibold">Dept</th>
                        <th class="px-4 py-3 text-left font-semibold">Jenis ATK</th>
                        <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold">Alasan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reqAtk as $atk)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-blue-600">{{ $atk['no'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $atk['tanggal'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $atk['karyawan'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs">{{ $atk['dept'] }}</span></td>
                        <td class="px-4 py-3 text-gray-600">{{ $atk['jenis_atk'] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $atk['jumlah'] }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $atk['alasan'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($atk['status']) {
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Menunggu'  => 'bg-amber-100 text-amber-700',
                                    'Ditolak'   => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $atk['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="modal-atk" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Ajukan Kebutuhan ATK</h3>
            <button onclick="document.getElementById('modal-atk').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('warehouse.req-atk.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan <span class="text-red-500">*</span></label>
                    <input type="text" name="karyawan" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-red-500">*</span></label>
                    <select name="dept" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih --</option>
                        @foreach(['IT','HR','Finance','Marketing','Operations','Procurement','Warehouse'] as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis ATK yang Dibutuhkan <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_atk" required placeholder="Contoh: Kertas A4, Pulpen, Stapler"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                <input type="text" name="jumlah" required placeholder="Contoh: 3 item / 10 pcs / 2 rim"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pengajuan <span class="text-red-500">*</span></label>
                <textarea name="alasan" required rows="2" placeholder="Jelaskan kebutuhan ATK..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-atk').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Ajukan</button>
            </div>
        </form>
    </div>
</div>
@endsection
