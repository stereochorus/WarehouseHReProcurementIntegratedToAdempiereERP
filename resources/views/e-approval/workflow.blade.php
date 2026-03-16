@extends('layouts.app')
@section('title', 'Workflow Approval')
@section('page-title', 'Workflow Approval — Simulasi Tanda Tangan Digital')

@section('content')
<div class="py-4 fade-in" x-data="{ showForm: false, selectedRoles: [] }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Kelola alur approval, pilih siapa yang tanda tangan dan siapa yang hanya review</p>
        <button @click="showForm = !showForm"
                class="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Workflow Baru
        </button>
    </div>

    {{-- Create Workflow Form --}}
    <div x-show="showForm" x-transition class="bg-white rounded-xl border border-teal-200 shadow-sm p-6 mb-6" style="display:none;">
        <h3 class="font-semibold text-gray-800 mb-4">Konfigurasi Workflow Approval Baru</h3>
        <form method="POST" action="{{ route('e-approval.documents.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Dokumen / Workflow <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" required placeholder="Contoh: PR Pengadaan Peralatan Proyek"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Dokumen <span class="text-red-500">*</span></label>
                    <select name="jenis" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach(['Proposal','Kontrak','Laporan','Surat Keputusan','Purchase Request','Material Request','Memo Internal','Surat Keterangan','Lainnya'] as $j)
                        <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Departemen <span class="text-red-500">*</span></label>
                    <select name="dept" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pilih Dept --</option>
                        @foreach(['IT','HR','Finance','Marketing','Operations','Procurement','Warehouse','PPIC','QC','Project'] as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Deadline Persetujuan <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_deadline" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            {{-- Approver/Reviewer Configuration --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Konfigurasi Approver & Reviewer</label>
                <p class="text-xs text-gray-500 mb-3">Tentukan urutan, siapa yang <strong>Tanda Tangan (Approve)</strong> dan siapa yang hanya <strong>Review</strong>.</p>
                <div class="space-y-3" x-data="{ steps: [{ urutan: 1, user: '', peran: 'Approve' }] }">
                    <template x-for="(step, idx) in steps" :key="idx">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="w-7 h-7 bg-teal-600 text-white text-xs rounded-full flex items-center justify-center font-bold flex-shrink-0" x-text="idx+1"></span>
                            <select x-model="step.user" :name="'step_user[]'" required
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Pilih User / Jabatan --</option>
                                @foreach($users as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                                @endforeach
                            </select>
                            <select x-model="step.peran" :name="'step_peran[]'"
                                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="Approve">Tanda Tangan (Approve)</option>
                                <option value="Review">Review Only</option>
                            </select>
                            <button type="button" @click="steps.splice(idx,1)" x-show="steps.length > 1"
                                    class="text-red-400 hover:text-red-600 p-1 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="steps.push({ urutan: steps.length+1, user: '', peran: 'Approve' })"
                            class="flex items-center gap-2 text-sm text-teal-600 hover:text-teal-800 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Step
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                <input type="checkbox" name="ttd_digital" id="ttd_wf" value="1" checked class="w-4 h-4 text-teal-600 rounded">
                <label for="ttd_wf" class="text-sm text-teal-800 font-medium">Aktifkan Tanda Tangan Digital Simulasi</label>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors">Buat Workflow</button>
                <button type="button" @click="showForm=false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Batal</button>
            </div>
        </form>
    </div>

    {{-- Workflow List --}}
    <div class="space-y-4">
        @foreach($workflows as $wf)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs text-teal-600">{{ $wf['id'] }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $wf['dept'] }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-800 mt-0.5">{{ $wf['judul'] }}</h3>
                    <p class="text-xs text-gray-400">Pemohon: {{ $wf['pemohon'] }} · {{ $wf['tgl'] }}</p>
                </div>
                @php
                    $allDone = collect($wf['steps'])->every(fn($s) => $s['status'] === 'Disetujui');
                    $anyWait = collect($wf['steps'])->contains(fn($s) => $s['status'] === 'Menunggu');
                    $wfStatus = $allDone ? 'Selesai' : ($anyWait ? 'Berjalan' : 'Selesai');
                    $wfBadge = $allDone ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $wfBadge }}">{{ $wfStatus }}</span>
            </div>

            <div class="px-5 py-4">
                <div class="flex items-start gap-0 overflow-x-auto">
                    @foreach($wf['steps'] as $i => $step)
                    <div class="flex items-start {{ $i < count($wf['steps'])-1 ? 'flex-1' : '' }} min-w-0">
                        <div class="flex flex-col items-center min-w-[100px]">
                            {{-- Circle --}}
                            @php
                                $circleClass = match($step['status']) {
                                    'Disetujui' => 'bg-green-500 text-white',
                                    'Menunggu'  => 'bg-gray-200 text-gray-500 border-2 border-dashed border-gray-400',
                                    default     => 'bg-red-500 text-white',
                                };
                                $icon = match($step['status']) {
                                    'Disetujui' => '✓',
                                    'Menunggu'  => $step['urutan'],
                                    default     => '✕',
                                };
                            @endphp
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold {{ $circleClass }}">
                                {{ $icon }}
                            </div>
                            <div class="text-center mt-1.5 px-1">
                                <p class="text-xs font-semibold text-gray-700 leading-tight">{{ $step['nama'] }}</p>
                                <span class="inline-block text-xs px-1.5 py-0.5 rounded mt-0.5
                                    {{ $step['peran'] === 'Approve' ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $step['peran'] === 'Approve' ? 'TTD' : 'Review' }}
                                </span>
                                @if($step['status'] === 'Disetujui' && $step['ttd'])
                                <div class="flex items-center justify-center gap-1 mt-1">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    <span class="text-xs text-green-600">e-TTD</span>
                                </div>
                                @endif
                                @if($step['catatan'] !== '-')
                                <p class="text-xs text-gray-400 mt-0.5 italic truncate max-w-[90px]" title="{{ $step['catatan'] }}">"{{ $step['catatan'] }}"</p>
                                @endif
                            </div>
                        </div>
                        @if($i < count($wf['steps'])-1)
                        <div class="flex-1 h-0.5 mt-4 mx-1
                            {{ $step['status'] === 'Disetujui' ? 'bg-green-300' : 'bg-gray-200' }}">
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <p class="text-xs text-gray-400 text-center mt-6">Data dummy — simulasi workflow approval & tanda tangan digital</p>
</div>
@endsection
