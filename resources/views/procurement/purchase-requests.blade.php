@extends('layouts.app')
@section('title', 'Purchase Request')
@section('page-title', 'Daftar Purchase Request')

@section('content')
<div class="py-4">
    <!-- Filter + Add -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ route('procurement.purchase-requests') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari PR</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="No. PR atau nama item..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="min-w-44">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" {{ $status===$s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">Filter</button>
            @if($search || $status)
            <a href="{{ route('procurement.purchase-requests') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Reset</a>
            @endif
            <a href="{{ route('procurement.purchase-requests.create') }}" class="ml-auto px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Buat PR Baru
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Purchase Request</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($prs) }} PR</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">No. PR</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Departemen</th>
                        <th class="px-4 py-3 text-left">Item</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Total Estimasi</th>
                        <th class="px-4 py-3 text-center">Prioritas</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($prs as $pr)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-purple-600">{{ $pr['id'] }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $pr['date'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $pr['dept'] }}</span></td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $pr['item'] }}</td>
                        <td class="px-4 py-3 text-center text-gray-600">{{ number_format($pr['qty']) }} {{ $pr['unit'] }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($pr['total']) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($pr['priority']==='Tinggi') bg-red-100 text-red-700
                                @elseif($pr['priority']==='Normal') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $pr['priority'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                                @if($pr['status']==='Approved') bg-green-100 text-green-700
                                @elseif($pr['status']==='Rejected') bg-red-100 text-red-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $pr['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada PR ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
