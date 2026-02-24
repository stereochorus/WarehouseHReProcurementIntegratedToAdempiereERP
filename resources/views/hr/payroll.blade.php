@extends('layouts.app')
@section('title', 'Payroll')
@section('page-title', 'Manajemen Payroll')

@section('content')
<div class="py-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-700 to-green-900 rounded-xl p-5 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold">Laporan Payroll — {{ $period }}</h2>
                <p class="text-green-200 text-sm mt-1">Status: <span class="font-semibold bg-white/20 px-2 py-0.5 rounded">Dalam Proses</span></p>
            </div>
            <a href="{{ route('hr.payroll.report') }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan Payroll
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
        $totalGross = array_sum(array_column($payroll, 'basic')) + array_sum(array_column($payroll, 'allowance')) + array_sum(array_column($payroll, 'overtime'));
        $totalNet   = array_sum(array_column($payroll, 'net'));
        $totalDed   = array_sum(array_column($payroll, 'deduction'));
        @endphp
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Karyawan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($payroll) }}</p>
            <p class="text-xs text-green-600 mt-1">Yang diproses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Bruto</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalGross) }}</p>
            <p class="text-xs text-gray-500 mt-1">Sebelum potongan</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Potongan</p>
            <p class="text-xl font-bold text-red-600 mt-1">Rp {{ number_format($totalDed) }}</p>
            <p class="text-xs text-red-500 mt-1">BPJS + PPh</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Neto</p>
            <p class="text-xl font-bold text-green-600 mt-1">Rp {{ number_format($totalNet) }}</p>
            <p class="text-xs text-green-500 mt-1">Take home pay</p>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Detail Payroll {{ $period }}</h3>
            <div class="flex gap-2">
                <span class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">● Pending</span>
                <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">● Diproses</span>
                <span class="px-3 py-1.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">● Dibayar</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Dept</th>
                        <th class="px-4 py-3 text-right">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right">Tunjangan</th>
                        <th class="px-4 py-3 text-right">Lembur</th>
                        <th class="px-4 py-3 text-right">Potongan</th>
                        <th class="px-4 py-3 text-right font-bold">Take Home</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Slip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($payroll as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-green-600">{{ $p['emp_id'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $p['name'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $p['dept'] }}</span></td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($p['basic']) }}</td>
                        <td class="px-4 py-3 text-right text-blue-600">Rp {{ number_format($p['allowance']) }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ $p['overtime'] > 0 ? 'Rp '.number_format($p['overtime']) : '—' }}</td>
                        <td class="px-4 py-3 text-right text-red-600">Rp {{ number_format($p['deduction']) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800">Rp {{ number_format($p['net']) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($p['status']==='Dibayar') bg-green-100 text-green-700
                                @elseif($p['status']==='Diproses') bg-blue-100 text-blue-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $p['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('hr.payroll.slip', $p['emp_id']) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Lihat Slip
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-green-50">
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-right font-bold text-gray-700">Total Pengeluaran Payroll:</td>
                        <td class="px-4 py-3 text-right font-bold text-green-700 text-base">Rp {{ number_format($totalNet) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Demo Notice -->
    <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl flex gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <p class="text-sm text-amber-800"><strong>Demo Mode:</strong> Data payroll ini adalah simulasi. Angka tidak mencerminkan data real. Integrasi dengan sistem akuntansi Adempiere belum dilakukan.</p>
    </div>
</div>
@endsection
