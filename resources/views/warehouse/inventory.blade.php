@extends('layouts.app')
@section('title', 'Inventory')
@section('page-title', 'Manajemen Inventory')

@section('content')
<div class="py-4">
    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ route('warehouse.inventory') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari Item</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama item atau ID..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="min-w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
            @if($search || $category)
            <a href="{{ route('warehouse.inventory') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-blue-700">{{ count($inventory) }}</p>
            <p class="text-xs text-blue-600 mt-1">Item Ditampilkan</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-red-700">{{ count(array_filter($inventory, fn($i)=>$i['status']==='Low Stock')) }}</p>
            <p class="text-xs text-red-600 mt-1">Stok Menipis</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-green-700">{{ count(array_filter($inventory, fn($i)=>$i['status']==='Normal')) }}</p>
            <p class="text-xs text-green-600 mt-1">Stok Normal</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Inventory</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($inventory) }} item</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID Item</th>
                        <th class="px-4 py-3 text-left">Nama Item</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Lokasi</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                        <th class="px-4 py-3 text-right">Min. Stok</th>
                        <th class="px-4 py-3 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50 {{ $item['status']==='Low Stock' ? 'bg-red-50/30' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs text-blue-600">{{ $item['id'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item['name'] }}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs">{{ $item['category'] }}</span></td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $item['location'] }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $item['stock'] <= $item['min_stock'] ? 'text-red-600' : 'text-gray-800' }}">
                            {{ number_format($item['stock']) }} {{ $item['unit'] }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ $item['min_stock'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item['price']) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item['status']==='Normal' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada item ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
