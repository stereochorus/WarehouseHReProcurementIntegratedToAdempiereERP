@extends('layouts.app')

@section('title', 'Status Upload Dokumen')
@section('page-title', 'Status Dokumen yang Diupload')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Status Upload Dokumen</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pantau status seluruh dokumen yang sudah diajukan untuk approval</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('e-approval.documents.export') }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
            <a href="{{ route('e-approval.documents.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Dokumen Baru
            </a>
        </div>
    </div>

    {{-- Stats cards --}}
    @php
        $total     = count($documents);
        $selesai   = count(array_filter($documents, fn($d) => $d['status'] === 'Selesai'));
        $menunggu  = count(array_filter($documents, fn($d) => str_starts_with($d['status'], 'Menunggu')));
        $ditolak   = count(array_filter($documents, fn($d) => $d['status'] === 'Ditolak'));
        $overdue   = count(array_filter($documents, fn($d) => $d['overdue'] ?? false));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-green-600 uppercase font-semibold">Selesai</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $selesai }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-amber-600 uppercase font-semibold">Menunggu</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $menunggu }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-red-500 uppercase font-semibold">Ditolak</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ $ditolak }}</p>
        </div>
        <div class="{{ $overdue > 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }} rounded-xl border shadow-sm p-4">
            <p class="text-xs {{ $overdue > 0 ? 'text-red-600' : 'text-gray-500' }} uppercase font-semibold">Overdue</p>
            <p class="text-2xl font-bold {{ $overdue > 0 ? 'text-red-600' : 'text-gray-400' }} mt-1">{{ $overdue }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Dokumen</h3>
            <span class="text-xs text-gray-400">{{ $total }} dokumen terdaftar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Nomor Dokumen</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Nama Dokumen</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Jenis</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Pemohon</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Reviewer</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Approver</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Deadline</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">TTD</th>
                        <th class="text-left px-4 py-3 text-xs text-gray-500 font-semibold uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($documents as $doc)
                    @php
                        $reviewers = array_filter($doc['history'], fn($h) => $h['peran'] === 'Review');
                        $approvers = array_filter($doc['history'], fn($h) => $h['peran'] === 'Approve');
                        $isOverdue = $doc['overdue'] ?? false;
                        $statusLabel = $isOverdue ? 'Overdue' : $doc['status'];
                        $statusCls = match(true) {
                            $doc['status'] === 'Selesai'              => 'bg-green-100 text-green-700',
                            $doc['status'] === 'Ditolak'              => 'bg-red-100 text-red-700',
                            $isOverdue                                => 'bg-red-100 text-red-700',
                            str_starts_with($doc['status'],'Menunggu')=> 'bg-amber-100 text-amber-700',
                            default                                   => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $isOverdue ? 'bg-red-50/30' : '' }}">
                        <td class="px-4 py-3">
                            @if($doc['no_dokumen'])
                            <span class="font-mono text-xs text-teal-600 font-semibold">{{ $doc['no_dokumen'] }}</span>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 text-sm">{{ $doc['judul'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $doc['tgl_upload_full'] }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $doc['jenis'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-800">{{ $doc['pemohon'] }}</p>
                            <p class="text-xs text-gray-400">{{ $doc['jabatan_pemohon'] }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusCls }}">
                                {{ $statusLabel }}
                            </span>
                            @if($isOverdue)
                            <p class="text-xs text-red-500 mt-0.5">Melewati deadline</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if(count($reviewers) > 0)
                            <div class="space-y-0.5">
                                @foreach($reviewers as $r)
                                <div>
                                    <p class="text-xs text-gray-700 font-medium">{{ $r['nama'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $r['jabatan'] }}</p>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if(count($approvers) > 0)
                            <div class="space-y-0.5">
                                @foreach($approvers as $a)
                                <div>
                                    <p class="text-xs text-gray-700 font-medium">{{ $a['nama'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $a['jabatan'] }}</p>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm {{ $isOverdue ? 'text-red-600 font-bold' : 'text-amber-600' }}">
                                {{ $doc['tgl_deadline_display'] }}
                            </p>
                            @if($doc['reminder_interval'])
                            <p class="text-xs text-gray-400 mt-0.5">Reminder: {{ $doc['reminder_interval'] }} hari</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($doc['ttd_digital'])
                            <span class="flex items-center gap-1 text-xs text-teal-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Digital
                            </span>
                            @if(isset($doc['tgl_ttd_full']) && $doc['status'] === 'Selesai')
                            <p class="text-xs text-teal-500 mt-0.5">{{ $doc['tgl_ttd_full'] }}</p>
                            @endif
                            @else
                            <span class="text-xs text-gray-400">Manual</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('e-approval.documents') }}"
                               class="text-xs text-teal-600 hover:underline font-medium">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($overdue > 0)
        <div class="px-5 py-3 border-t border-red-100 bg-red-50 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="text-xs text-red-700 font-medium">{{ $overdue }} dokumen melewati deadline — segera tindaklanjuti atau perbarui deadline.</span>
        </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Keterangan Status</p>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center gap-2"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">Selesai</span><span class="text-xs text-gray-500">Semua step disetujui</span></div>
            <div class="flex items-center gap-2"><span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">Menunggu</span><span class="text-xs text-gray-500">Dalam proses approval</span></div>
            <div class="flex items-center gap-2"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Ditolak</span><span class="text-xs text-gray-500">Perlu revisi</span></div>
            <div class="flex items-center gap-2"><span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">Overdue</span><span class="text-xs text-gray-500">Melewati tanggal deadline, masih menunggu</span></div>
        </div>
    </div>
</div>
@endsection
