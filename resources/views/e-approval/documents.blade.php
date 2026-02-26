@extends('layouts.app')

@section('title', 'Dokumen Approval')
@section('page-title', 'Daftar Dokumen Approval')

@section('content')
<div class="pt-4 space-y-6 fade-in" x-data="{}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Dokumen Approval</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola dan proses persetujuan dokumen digital</p>
        </div>
        <a href="{{ route('e-approval.documents.create') }}"
           class="flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Dokumen Baru
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('e-approval.documents') }}" class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Dokumen</label>
                <select name="jenis" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Semua Jenis</option>
                    @foreach($jenises as $j)
                    <option value="{{ $j }}" {{ ($jenis ?? '') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-sm rounded-lg hover:bg-teal-700">Filter</button>
            <a href="{{ route('e-approval.documents') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Reset</a>
        </div>
    </form>

    {{-- Document cards --}}
    <div class="space-y-4">
        @forelse($documents as $doc)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ open: false }">
            <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="font-mono text-xs text-teal-600 font-semibold">{{ $doc['id'] }}</span>
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $doc['jenis'] }}</span>
                            @if($doc['ttd_digital'])
                            <span class="px-2 py-0.5 bg-teal-100 text-teal-700 rounded text-xs">TTD Digital</span>
                            @endif
                        </div>
                        <p class="text-base font-semibold text-gray-800">{{ $doc['judul'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pemohon: <span class="font-medium text-gray-700">{{ $doc['pemohon'] }}</span>
                            ({{ $doc['dept'] }}) •
                            Upload: {{ $doc['tgl_upload'] }} •
                            Deadline: <span class="text-amber-600 font-medium">{{ $doc['tgl_deadline'] }}</span>
                        </p>

                        {{-- Progress --}}
                        @php
                            $done  = count(array_filter($doc['history'], fn($h) => $h['status'] === 'Disetujui'));
                            $total = count($doc['history']);
                        @endphp
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-1.5 max-w-xs">
                                <div class="h-1.5 rounded-full {{ $doc['status'] === 'Ditolak' ? 'bg-red-500' : 'bg-teal-500' }}"
                                     style="width: {{ $total > 0 ? ($done/$total)*100 : 0 }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $done }}/{{ $total }} disetujui</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        @php
                            $cls = match(true) {
                                $doc['status'] === 'Selesai'              => 'bg-green-100 text-green-700',
                                $doc['status'] === 'Ditolak'              => 'bg-red-100 text-red-700',
                                str_starts_with($doc['status'],'Menunggu')=> 'bg-amber-100 text-amber-700',
                                default                                   => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $doc['status'] }}</span>

                        @if(str_starts_with($doc['status'], 'Menunggu'))
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('e-approval.documents.approve', $doc['id']) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('Setujui dokumen ini?')"
                                        class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('e-approval.documents.reject', $doc['id']) }}">
                                @csrf
                                <input type="hidden" name="catatan" value="Perlu revisi dokumen">
                                <button type="submit" onclick="return confirm('Tolak dokumen ini?')"
                                        class="px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @endif

                        <button @click="open=!open" class="text-xs text-teal-600 hover:underline">
                            <span x-show="!open">Lihat detail ▾</span>
                            <span x-show="open">Tutup detail ▴</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- History accordion --}}
            <div x-show="open" x-collapse class="border-t border-gray-100 bg-gray-50 px-5 py-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Riwayat Approval</p>
                <div class="space-y-2">
                    @foreach($doc['history'] as $h)
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $h['status'] === 'Disetujui' ? 'bg-green-100' : ($h['status'] === 'Ditolak' ? 'bg-red-100' : 'bg-gray-100') }}">
                            @if($h['status'] === 'Disetujui')
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($h['status'] === 'Ditolak')
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @else
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-800">Step {{ $h['step'] }}: {{ $h['nama'] }}</span>
                                <span class="text-xs {{ $h['status'] === 'Disetujui' ? 'text-green-600' : ($h['status'] === 'Ditolak' ? 'text-red-600' : 'text-amber-600') }} font-medium">{{ $h['status'] }}</span>
                            </div>
                            @if($h['catatan'] && $h['catatan'] !== '-')
                            <p class="text-xs text-gray-500">Catatan: {{ $h['catatan'] }}</p>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400">{{ $h['tgl'] }}</span>
                    </div>
                    @endforeach
                </div>
                @if($doc['status'] === 'Selesai' && $doc['ttd_digital'])
                <div class="mt-4 p-3 bg-teal-50 border border-teal-200 rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span class="text-xs text-teal-700 font-medium">Dokumen telah ditandatangani secara digital — Simulasi TTD Elektronik</span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p>Tidak ada dokumen ditemukan</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
