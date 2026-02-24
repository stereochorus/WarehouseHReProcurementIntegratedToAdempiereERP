@extends('layouts.app')
@section('title', 'Mutasi Stok')
@section('page-title', 'Riwayat Mutasi Stok')

@section('content')
<div class="py-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-semibold text-gray-800">Riwayat Mutasi Stok Barang</h3>
            <div class="flex gap-2">
                <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium">● Penerimaan</span>
                <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-xs font-medium">● Pengeluaran</span>
                <span class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">● Transfer</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">No. Dokumen</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-left">Dari</th>
                        <th class="px-4 py-3 text-left">Ke</th>
                        <th class="px-4 py-3 text-left">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($movements as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $m['date'] }}</td>
                        <td class="px-4 py-3 font-mono text-blue-600 text-xs whitespace-nowrap">{{ $m['doc_no'] }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap
                                {{ $m['type']==='Penerimaan' ? 'bg-green-100 text-green-700' : ($m['type']==='Pengeluaran' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $m['type'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $m['item'] }}</td>
                        <td class="px-4 py-3 text-center font-bold
                            {{ str_starts_with($m['qty'],'+') ? 'text-green-600' : (str_starts_with($m['qty'],'-') ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $m['qty'] }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $m['from'] }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $m['to'] }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $m['by'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
