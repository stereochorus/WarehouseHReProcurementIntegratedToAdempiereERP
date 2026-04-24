@extends('layouts.app')

@section('title', 'Dokumen Approval')
@section('page-title', 'Daftar Dokumen Approval')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Dokumen Approval</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola dan proses persetujuan dokumen digital</p>
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
                Ajukan Dokumen Baru
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {!! session('success') !!}
    </div>
    @endif

    {{-- Summary Section --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="{ showTable: false }">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h3 class="font-semibold text-gray-700 text-sm">Ringkasan Dokumen</h3>
                <div class="flex gap-3">
                    <span class="text-xs text-gray-500">Total: <strong class="text-gray-800">{{ $totalDocs }}</strong></span>
                    <span class="text-xs text-green-600">Selesai: <strong>{{ $selesaiDocs }}</strong></span>
                    @if($overdueDocs > 0)
                    <span class="text-xs text-red-600">Overdue: <strong>{{ $overdueDocs }}</strong></span>
                    @endif
                </div>
            </div>
            <button @click="showTable=!showTable" class="flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800 font-medium">
                <span x-show="!showTable">Tampilkan tabel ringkasan ▾</span>
                <span x-show="showTable">Sembunyikan ▴</span>
            </button>
        </div>
        <div x-show="showTable" x-collapse>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-2.5 text-gray-500 font-semibold">No. Dokumen</th>
                            <th class="text-left px-4 py-2.5 text-gray-500 font-semibold">Nama Dokumen</th>
                            <th class="text-left px-4 py-2.5 text-gray-500 font-semibold">Status</th>
                            <th class="text-left px-4 py-2.5 text-gray-500 font-semibold">Reviewer</th>
                            <th class="text-left px-4 py-2.5 text-gray-500 font-semibold">Approver</th>
                            <th class="text-left px-4 py-2.5 text-gray-500 font-semibold">Deadline</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($all as $row)
                        @php
                            $reviewers = array_filter($row['history'], fn($h) => $h['peran'] === 'Review');
                            $approvers = array_filter($row['history'], fn($h) => $h['peran'] === 'Approve');
                            $sCls = match(true) {
                                $row['status'] === 'Selesai'              => 'bg-green-100 text-green-700',
                                $row['status'] === 'Ditolak'              => 'bg-red-100 text-red-700',
                                ($row['overdue'] ?? false)                => 'bg-red-100 text-red-700',
                                str_starts_with($row['status'],'Menunggu')=> 'bg-amber-100 text-amber-700',
                                default                                   => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2.5 font-mono text-teal-600">{{ $row['no_dokumen'] ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-800 font-medium">{{ $row['judul'] }}</td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sCls }}">
                                    {{ ($row['overdue'] ?? false) ? 'Overdue' : $row['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">
                                @if(count($reviewers)) {{ implode(', ', array_column($reviewers, 'nama')) }} @else — @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-600">
                                @if(count($approvers)) {{ implode(', ', array_column($approvers, 'nama')) }} @else — @endif
                            </td>
                            <td class="px-4 py-2.5 {{ ($row['overdue'] ?? false) ? 'text-red-600 font-semibold' : 'text-amber-600' }}">
                                {{ $row['tgl_deadline_display'] }}
                                @if($row['overdue'] ?? false)<span class="ml-1 text-red-500">⚠</span>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-100 flex justify-end">
                <a href="{{ route('e-approval.documents.export') }}"
                   class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export to Excel (.csv)
                </a>
            </div>
        </div>
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
        <div class="bg-white rounded-xl border {{ ($doc['overdue'] ?? false) ? 'border-red-300' : 'border-gray-200' }} shadow-sm overflow-hidden"
             x-data="{ open: false, showPreview: false }">

            {{-- Preview Modal --}}
            <div x-show="showPreview" x-cloak
                 class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
                 @keydown.escape.window="showPreview=false">
                <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl" @click.stop>
                    {{-- Modal header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-teal-50 rounded-t-2xl">
                        <div>
                            <p class="text-xs text-teal-600 font-semibold uppercase tracking-wide">Preview Dokumen</p>
                            <h3 class="text-base font-bold text-gray-800 mt-0.5">{{ $doc['judul'] }}</h3>
                        </div>
                        <button @click="showPreview=false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-teal-200 text-teal-600 text-xl font-bold transition-colors">&times;</button>
                    </div>
                    <div class="px-6 py-5 space-y-5">
                        {{-- Document number & metadata --}}
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($doc['no_dokumen'])
                            <div class="col-span-2 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                                <span class="text-xs text-teal-600 font-semibold uppercase">Nomor Dokumen</span>
                                <p class="font-mono text-teal-800 font-bold mt-0.5">{{ $doc['no_dokumen'] }}</p>
                            </div>
                            @endif
                            <div><span class="text-xs text-gray-500">Judul</span><p class="font-semibold text-gray-800 mt-0.5">{{ $doc['judul'] }}</p></div>
                            <div><span class="text-xs text-gray-500">Jenis</span><p class="text-gray-700 mt-0.5">{{ $doc['jenis'] }}</p></div>
                            <div><span class="text-xs text-gray-500">Pemohon</span><p class="text-gray-700 mt-0.5">{{ $doc['pemohon'] }} <span class="text-gray-400">({{ $doc['jabatan_pemohon'] }})</span></p></div>
                            <div><span class="text-xs text-gray-500">Departemen</span><p class="text-gray-700 mt-0.5">{{ $doc['dept'] }}</p></div>
                            <div><span class="text-xs text-gray-500">Tgl Upload</span><p class="text-gray-700 mt-0.5">{{ $doc['tgl_upload_full'] }}</p></div>
                            <div><span class="text-xs text-gray-500">Deadline</span><p class="text-amber-600 font-medium mt-0.5">{{ $doc['tgl_deadline_display'] }}</p></div>
                        </div>

                        {{-- Approval chain table --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Rantai Persetujuan</p>
                            <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-3 py-2 text-gray-500">No</th>
                                        <th class="text-left px-3 py-2 text-gray-500">Nama & Jabatan</th>
                                        <th class="text-left px-3 py-2 text-gray-500">Peran</th>
                                        <th class="text-left px-3 py-2 text-gray-500">Status</th>
                                        <th class="text-left px-3 py-2 text-gray-500">Tanda Tangan</th>
                                        <th class="text-left px-3 py-2 text-gray-500">Tanggal & Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($doc['history'] as $h)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2.5 text-gray-600">{{ $h['step'] }}</td>
                                        <td class="px-3 py-2.5">
                                            <p class="font-medium text-gray-800">{{ $h['nama'] }}</p>
                                            <p class="text-gray-400 text-xs">{{ $h['jabatan'] }}</p>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @php
                                                $peranCls = $h['peran'] === 'Approve' ? 'bg-teal-100 text-teal-700' : 'bg-indigo-100 text-indigo-700';
                                            @endphp
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $peranCls }}">{{ $h['peran'] }}</span>
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @php
                                                $asCls = match($h['action_status']) {
                                                    'Approve' => 'bg-green-100 text-green-700',
                                                    'Checked' => 'bg-blue-100 text-blue-700',
                                                    default   => 'bg-gray-100 text-gray-500',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $asCls }}">{{ $h['action_status'] }}</span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center">
                                            @if($h['paraf'])
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-teal-50 border border-teal-300 rounded text-teal-700 text-xs font-bold italic">
                                                {{ mb_substr($h['nama'], 0, 2) }}
                                            </span>
                                            @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 border border-dashed border-gray-300 rounded text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-gray-500">{{ $h['tgl'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Signed timestamp --}}
                        @if($doc['status'] === 'Selesai' && $doc['ttd_digital'])
                        <div class="p-3 bg-teal-50 border border-teal-200 rounded-lg flex items-start gap-2">
                            <svg class="w-4 h-4 text-teal-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <div>
                                <p class="text-xs font-semibold text-teal-800">Dokumen Telah Ditandatangani Secara Digital</p>
                                <p class="text-xs text-teal-700 mt-0.5">Waktu TTD: <strong>{{ $doc['tgl_ttd_full'] ?? $doc['tgl_upload_full'] }}</strong></p>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-end pt-2">
                            <button @click="showPreview=false"
                                    class="px-5 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card body --}}
            <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        {{-- Badges row --}}
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="font-mono text-xs text-teal-600 font-semibold">{{ $doc['id'] }}</span>
                            @if($doc['no_dokumen'])
                            <span class="font-mono text-xs text-indigo-600 font-semibold bg-indigo-50 px-1.5 py-0.5 rounded">{{ $doc['no_dokumen'] }}</span>
                            @endif
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">{{ $doc['jenis'] }}</span>
                            @if($doc['ttd_digital'])
                            <span class="px-2 py-0.5 bg-teal-100 text-teal-700 rounded text-xs">TTD Digital</span>
                            @endif
                            @if($doc['overdue'] ?? false)
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-semibold animate-pulse">⚠ Overdue</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <p class="text-base font-semibold text-gray-800">{{ $doc['judul'] }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pemohon: <span class="font-medium text-gray-700">{{ $doc['pemohon'] }}</span>
                            <span class="text-gray-400">({{ $doc['jabatan_pemohon'] }})</span> •
                            Upload: {{ $doc['tgl_upload'] }} •
                            Deadline: <span class="{{ ($doc['overdue'] ?? false) ? 'text-red-600 font-bold' : 'text-amber-600 font-medium' }}">{{ $doc['tgl_deadline_display'] }}</span>
                            @if($doc['reminder_interval'])
                            • <span class="text-gray-400">Reminder setiap {{ $doc['reminder_interval'] }} hari</span>
                            @endif
                        </p>

                        {{-- Progress --}}
                        @php
                            $done  = count(array_filter($doc['history'], fn($h) => $h['status'] === 'Disetujui'));
                            $total = count($doc['history']);
                        @endphp
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-1.5 max-w-xs">
                                <div class="h-1.5 rounded-full {{ $doc['status'] === 'Ditolak' ? 'bg-red-500' : (($doc['overdue'] ?? false) ? 'bg-red-400' : 'bg-teal-500') }}"
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
                                ($doc['overdue'] ?? false)                => 'bg-red-100 text-red-700',
                                str_starts_with($doc['status'],'Menunggu')=> 'bg-amber-100 text-amber-700',
                                default                                   => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">
                            {{ ($doc['overdue'] ?? false) ? 'Overdue' : $doc['status'] }}
                        </span>

                        <button @click="showPreview=true"
                                class="flex items-center gap-1 px-3 py-1 border border-teal-300 text-teal-600 text-xs rounded-lg hover:bg-teal-50 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview
                        </button>

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

            {{-- Detail accordion --}}
            <div x-show="open" x-collapse class="border-t border-gray-100 bg-gray-50">

                {{-- Approval History (enhanced) --}}
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Riwayat Approval — Approver & Reviewer</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-white border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold w-8">No</th>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold">Nama & Jabatan</th>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold">Peran</th>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold">Status Aksi</th>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold">Approval</th>
                                    <th class="text-center px-3 py-2 text-gray-500 font-semibold">Paraf</th>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold">Tanggal & Waktu</th>
                                    <th class="text-left px-3 py-2 text-gray-500 font-semibold">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($doc['history'] as $h)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2.5 text-gray-500">{{ $h['step'] }}</td>
                                    <td class="px-3 py-2.5">
                                        <p class="font-medium text-gray-800">{{ $h['nama'] }}</p>
                                        <p class="text-gray-400 text-xs mt-0.5">{{ $h['jabatan'] }}</p>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        @php $peranCls = $h['peran'] === 'Approve' ? 'bg-teal-100 text-teal-700' : 'bg-indigo-100 text-indigo-700'; @endphp
                                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $peranCls }}">{{ $h['peran'] }}</span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        @php
                                            $asCls = match($h['action_status']) {
                                                'Approve' => 'bg-green-100 text-green-700',
                                                'Checked' => 'bg-blue-100 text-blue-700',
                                                default   => 'bg-gray-100 text-gray-500',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $asCls }}">{{ $h['action_status'] }}</span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        @php
                                            $stCls = match($h['status']) {
                                                'Disetujui' => 'text-green-600',
                                                'Ditolak'   => 'text-red-600',
                                                default     => 'text-amber-600',
                                            };
                                        @endphp
                                        <span class="font-medium text-xs {{ $stCls }}">{{ $h['status'] }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        @if($h['paraf'])
                                        <span class="inline-flex items-center justify-center w-9 h-9 bg-teal-50 border-2 border-teal-300 rounded text-teal-700 text-xs font-bold italic shadow-sm"
                                              title="Paraf: {{ $h['nama'] }}">
                                            {{ mb_substr($h['nama'], 0, 2) }}
                                        </span>
                                        @else
                                        <span class="inline-flex items-center justify-center w-9 h-9 bg-gray-50 border border-dashed border-gray-300 rounded text-gray-300 text-xs" title="Belum paraf">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-gray-500 whitespace-nowrap">{{ $h['tgl'] }}</td>
                                    <td class="px-3 py-2.5 text-gray-500">
                                        @if($h['catatan'] && $h['catatan'] !== '-') {{ $h['catatan'] }} @else <span class="text-gray-300">—</span> @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Signed document timestamp --}}
                @if($doc['status'] === 'Selesai' && $doc['ttd_digital'])
                <div class="mx-5 mb-4 p-3 bg-teal-50 border border-teal-200 rounded-lg flex items-start gap-2">
                    <svg class="w-4 h-4 text-teal-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-teal-800">Dokumen Telah Ditandatangani Secara Digital</p>
                        <p class="text-xs text-teal-600 mt-0.5">
                            Waktu tanda tangan: <strong>{{ $doc['tgl_ttd_full'] ?? '—' }}</strong>
                        </p>
                    </div>
                </div>
                @endif

                {{-- Bubble Comments --}}
                @if(count($doc['komentar']) > 0)
                <div class="px-5 pb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Komentar Dokumen</p>
                    <div class="space-y-3">
                        @foreach($doc['komentar'] as $kom)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 text-indigo-600 text-xs font-bold">
                                {{ mb_substr($kom['user'], 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-none px-4 py-2.5 shadow-sm relative">
                                    <div class="flex items-center justify-between gap-3 mb-1">
                                        <div>
                                            <span class="text-xs font-semibold text-gray-800">{{ $kom['user'] }}</span>
                                            <span class="text-xs text-gray-400 ml-1">{{ $kom['jabatan'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-medium">{{ $kom['bagian'] }}</span>
                                            <span class="text-xs text-gray-400">{{ $kom['tgl'] }}</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ $kom['text'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
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

<style>[x-cloak]{display:none!important}</style>
@endsection
