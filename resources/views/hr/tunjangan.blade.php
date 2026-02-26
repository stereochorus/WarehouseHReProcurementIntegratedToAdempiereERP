@extends('layouts.app')

@section('title', 'Laporan Tunjangan')
@section('page-title', 'Laporan Tunjangan Perbulan')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Laporan Tunjangan Perbulan</h2>
            <p class="text-sm text-gray-500 mt-0.5">Rekap tunjangan karyawan termasuk take-home pay</p>
        </div>
        <form method="GET" action="{{ route('hr.tunjangan') }}" class="flex items-center gap-2">
            <label class="text-sm text-gray-600 font-medium">Periode:</label>
            <input type="month" name="bulan" value="{{ $bulan }}"
                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Tampilkan</button>
        </form>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalGajiPokok  = array_sum(array_column($tunjangan, 'gaji_pokok'));
        $totalTunjangan  = array_sum(array_column($tunjangan, 'total_tunjangan'));
        $totalTakeHome   = array_sum(array_column($tunjangan, 'take_home'));
        $totalTransport  = array_sum(array_column($tunjangan, 'tunjangan_transport'));
        $totalMakan      = array_sum(array_column($tunjangan, 'tunjangan_makan'));
        $totalJabatan    = array_sum(array_column($tunjangan, 'tunjangan_jabatan'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Gaji Pokok</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalGajiPokok, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ count($tunjangan) }} karyawan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Tunjangan</p>
            <p class="text-xl font-bold text-green-600 mt-1">Rp {{ number_format($totalTunjangan, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Transport + Makan + Jabatan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 lg:col-span-1">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Take-Home Pay</p>
            <p class="text-xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalTakeHome, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Gaji Pokok + Tunjangan</p>
        </div>
    </div>

    {{-- Breakdown Tunjangan --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
            <p class="text-xs text-green-600 font-semibold uppercase">Tunjangan Transport</p>
            <p class="text-lg font-bold text-green-700 mt-1">Rp {{ number_format($totalTransport, 0, ',', '.') }}</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
            <p class="text-xs text-amber-600 font-semibold uppercase">Tunjangan Makan</p>
            <p class="text-lg font-bold text-amber-700 mt-1">Rp {{ number_format($totalMakan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-xs text-blue-600 font-semibold uppercase">Tunjangan Jabatan</p>
            <p class="text-lg font-bold text-blue-700 mt-1">Rp {{ number_format($totalJabatan, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-700 text-sm mb-4">Distribusi Take-Home Pay per Karyawan</h3>
        <canvas id="tunjanganChart" height="80"></canvas>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Detail Tunjangan Karyawan — Periode {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h3>
            <span class="text-xs text-gray-400">{{ count($tunjangan) }} karyawan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Karyawan</th>
                        <th class="px-4 py-3 text-left font-semibold">Dept</th>
                        <th class="px-4 py-3 text-left font-semibold">Jabatan</th>
                        <th class="px-4 py-3 text-right font-semibold">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right font-semibold">Transport</th>
                        <th class="px-4 py-3 text-right font-semibold">Makan</th>
                        <th class="px-4 py-3 text-right font-semibold">Jabatan</th>
                        <th class="px-4 py-3 text-right font-semibold">Total Tunjangan</th>
                        <th class="px-4 py-3 text-right font-semibold">Take-Home Pay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tunjangan as $t)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $t['nama'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs">{{ $t['dept'] }}</span></td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $t['jabatan'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ number_format($t['gaji_pokok'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($t['tunjangan_transport'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($t['tunjangan_makan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($t['tunjangan_jabatan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium text-green-700">{{ number_format($t['total_tunjangan'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-700">{{ number_format($t['take_home'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-gray-700">Total:</td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ number_format($totalGajiPokok, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ number_format($totalTransport, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ number_format($totalMakan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ number_format($totalJabatan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalTunjangan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-blue-700">{{ number_format($totalTakeHome, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const labels = @json(array_column($tunjangan, 'nama'));
const gajiPokok = @json(array_column($tunjangan, 'gaji_pokok'));
const totalTunjangan = @json(array_column($tunjangan, 'total_tunjangan'));

new Chart(document.getElementById('tunjanganChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Gaji Pokok', data: gajiPokok, backgroundColor: '#3b82f6', borderRadius: 4 },
            { label: 'Total Tunjangan', data: totalTunjangan, backgroundColor: '#10b981', borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { stacked: false },
            y: {
                beginAtZero: true,
                ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }
            }
        }
    }
});
</script>
@endpush
