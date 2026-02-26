@extends('layouts.app')

@section('title', 'E-Approval')
@section('page-title', 'E-Approval & Tanda Tangan Digital')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Dashboard E-Approval</h2>
            <p class="text-sm text-gray-500 mt-0.5">Persetujuan dokumen digital dengan simulasi tanda tangan elektronik</p>
        </div>
        <a href="{{ route('e-approval.documents.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Dokumen
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Dokumen</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Menunggu</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['menunggu'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Perlu tindakan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['selesai'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Disetujui semua</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['ditolak'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Perlu revisi</p>
        </div>
        <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
            <p class="text-xs text-teal-600 uppercase font-semibold">TTD Digital</p>
            <p class="text-2xl font-bold text-teal-700 mt-1">{{ $stats['ttd_digital'] }}</p>
            <p class="text-xs text-teal-500 mt-1">Tanda tangan elektronik</p>
        </div>
    </div>

    {{-- Alur Approval (banner info) --}}
    <div class="bg-teal-50 border border-teal-200 rounded-xl p-4">
        <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5 text-teal-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h4 class="font-semibold text-teal-800 text-sm">Alur Persetujuan Dokumen</h4>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @foreach(['Upload Dokumen','Review Manager','Persetujuan Legal','Persetujuan Direktur','TTD Digital','Arsip'] as $i => $step)
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-teal-200 rounded-lg">
                    <span class="w-5 h-5 bg-teal-600 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ $i+1 }}</span>
                    <span class="text-xs font-medium text-teal-800">{{ $step }}</span>
                </div>
                @if($i < 5)<svg class="w-4 h-4 text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>@endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent documents --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Dokumen Terbaru</h3>
            <a href="{{ route('e-approval.documents') }}" class="text-xs text-teal-600 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recent as $doc)
            <div class="px-5 py-4 hover:bg-gray-50">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs text-teal-600 font-semibold">{{ $doc['id'] }}</span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $doc['jenis'] }}</span>
                            @if($doc['ttd_digital'])
                            <span class="px-2 py-0.5 bg-teal-100 text-teal-700 rounded text-xs flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                TTD Digital
                            </span>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-gray-800">{{ $doc['judul'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $doc['pemohon'] }} — {{ $doc['dept'] }} | Upload: {{ $doc['tgl_upload'] }}</p>
                        {{-- Progress bar --}}
                        @php $done = count(array_filter($doc['history'], fn($h) => $h['status'] === 'Disetujui')); $total = count($doc['history']); @endphp
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-teal-500 h-1.5 rounded-full" style="width: {{ $total > 0 ? ($done/$total)*100 : 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $done }}/{{ $total }} step</span>
                        </div>
                    </div>
                    @php
                        $cls = match(true) {
                            $doc['status'] === 'Selesai'              => 'bg-green-100 text-green-700',
                            $doc['status'] === 'Ditolak'              => 'bg-red-100 text-red-700',
                            str_starts_with($doc['status'],'Menunggu')=> 'bg-amber-100 text-amber-700',
                            default                                   => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $cls }}">{{ $doc['status'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('e-approval.documents.create') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow group">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-teal-200 transition-colors">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Upload Dokumen</p>
            <p class="text-xs text-gray-500 mt-0.5">Ajukan dokumen untuk approval</p>
        </a>
        <a href="{{ route('e-approval.documents') }}" class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow group">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-amber-200 transition-colors">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">Proses Approval</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $stats['menunggu'] }} dokumen menunggu</p>
        </a>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-800">TTD Digital</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $stats['ttd_digital'] }} dokumen ber-TTD digital</p>
        </div>
    </div>
</div>
@endsection
