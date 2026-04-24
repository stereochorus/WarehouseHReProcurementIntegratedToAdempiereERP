@extends('layouts.app')

@section('title', 'Ajukan Dokumen')
@section('page-title', 'Ajukan Dokumen untuk Approval')

@section('content')
<div class="pt-4 fade-in"
     x-data="{
         steps: [{ user: '', jabatan: '', peran: 'Approve', actionStatus: 'Prepare', paraf: false }],
         jenis: '',
         jenisWithNomor: {{ json_encode($jenisWithNomor) }},
         showPreview: false,
         judul: '',
         noDokumen: '',
         dept: '',
         deadline: '',
         approvers: {{ json_encode($approvers) }},
         dragSigs: [],
         dragging: null,
         get needsNomor() { return this.jenisWithNomor.includes(this.jenis); },
         getJabatan(nama) {
             const found = this.approvers.find(a => a.nama === nama);
             return found ? found.jabatan : '';
         },
         onUserChange(step, nama) {
             step.user = nama;
             step.jabatan = this.getJabatan(nama);
         },
         handleDragStart(event, idx) {
             this.dragging = idx;
             event.dataTransfer.effectAllowed = 'move';
         },
         handleDrop(event) {
             if (this.dragging === null) return;
             const rect = event.currentTarget.getBoundingClientRect();
             const x = Math.max(0, Math.min(event.clientX - rect.left - 50, rect.width - 110));
             const y = Math.max(0, Math.min(event.clientY - rect.top - 18, rect.height - 44));
             const existing = this.dragSigs.findIndex(s => s.idx === this.dragging);
             const label = this.steps[this.dragging] ? (this.steps[this.dragging].user || 'Step ' + (this.dragging+1)) : 'Step ' + (this.dragging+1);
             if (existing >= 0) {
                 this.dragSigs[existing] = { idx: this.dragging, x, y, label };
             } else {
                 this.dragSigs.push({ idx: this.dragging, x, y, label });
             }
             this.dragging = null;
         }
     }">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('e-approval.dashboard') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Form Pengajuan Dokumen</h2>
            <p class="text-sm text-gray-500">Ajukan dokumen untuk proses persetujuan digital</p>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div x-show="showPreview" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showPreview=false">
        <div class="bg-white rounded-2xl w-full max-w-xl max-h-[85vh] overflow-y-auto shadow-2xl" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-teal-50 rounded-t-2xl">
                <div>
                    <p class="text-xs text-teal-600 font-semibold uppercase">Preview Dokumen (Simulasi)</p>
                    <h3 class="text-sm font-bold text-gray-800 mt-0.5" x-text="judul || '(Judul belum diisi)'"></h3>
                </div>
                <button @click="showPreview=false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-teal-200 text-teal-600 text-xl font-bold">&times;</button>
            </div>
            <div class="px-6 py-5 space-y-4 text-sm">
                <template x-if="noDokumen">
                    <div class="p-3 bg-teal-50 border border-teal-200 rounded-lg">
                        <span class="text-xs text-teal-600 font-semibold uppercase">Nomor Dokumen</span>
                        <p class="font-mono text-teal-800 font-bold mt-0.5" x-text="noDokumen"></p>
                    </div>
                </template>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div><span class="text-gray-400">Jenis</span><p class="text-gray-800 font-medium mt-0.5" x-text="jenis || '—'"></p></div>
                    <div><span class="text-gray-400">Departemen</span><p class="text-gray-800 font-medium mt-0.5" x-text="dept || '—'"></p></div>
                    <div><span class="text-gray-400">Deadline</span><p class="text-amber-600 font-medium mt-0.5" x-text="deadline || '—'"></p></div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Rantai Persetujuan</p>
                    <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-3 py-2 text-gray-500">No</th>
                                <th class="text-left px-3 py-2 text-gray-500">Nama & Jabatan</th>
                                <th class="text-left px-3 py-2 text-gray-500">Peran</th>
                                <th class="text-left px-3 py-2 text-gray-500">Status</th>
                                <th class="text-center px-3 py-2 text-gray-500">Paraf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(step, idx) in steps" :key="idx">
                                <tr>
                                    <td class="px-3 py-2 text-gray-500" x-text="idx+1"></td>
                                    <td class="px-3 py-2">
                                        <p class="font-medium text-gray-800" x-text="step.user || '(belum dipilih)'"></p>
                                        <p class="text-gray-400 text-xs mt-0.5" x-text="step.jabatan"></p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-medium"
                                              :class="step.peran === 'Approve' ? 'bg-teal-100 text-teal-700' : 'bg-indigo-100 text-indigo-700'"
                                              x-text="step.peran"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                              :class="step.actionStatus === 'Approve' ? 'bg-green-100 text-green-700' : step.actionStatus === 'Checked' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'"
                                              x-text="step.actionStatus"></span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <template x-if="step.paraf">
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-teal-50 border-2 border-teal-300 rounded text-teal-700 text-xs font-bold italic"
                                                  x-text="step.user ? step.user.substring(0,2) : '?'"></span>
                                        </template>
                                        <template x-if="!step.paraf">
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-50 border border-dashed border-gray-300 rounded text-gray-300 text-xs">—</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end pt-2">
                    <button @click="showPreview=false" class="px-5 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Tutup Preview</button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form method="POST" action="{{ route('e-approval.documents.store') }}" class="space-y-5">
                    @csrf

                    {{-- Nomor Dokumen (conditional) --}}
                    <div x-show="needsNomor">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Dokumen
                            <span class="text-gray-400 text-xs font-normal">(untuk jenis Proposal, Kontrak, SK, dll.)</span>
                        </label>
                        <input type="text" name="no_dokumen" x-model="noDokumen"
                               placeholder="Contoh: PRO/IT/2024/0033"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-mono">
                        <p class="text-xs text-gray-400 mt-1">Format: JENIS/DEPT/TAHUN/URUTAN</p>
                    </div>

                    {{-- Judul --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" required x-model="judul" value="{{ old('judul') }}"
                               placeholder="Contoh: Proposal Pengadaan Server 2024"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('judul') border-red-400 @enderror">
                        @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Dokumen <span class="text-red-500">*</span></label>
                            <select name="jenis" required x-model="jenis"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenises as $j)
                                <option value="{{ $j }}" {{ old('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span class="text-red-500">*</span></label>
                            <select name="dept" required x-model="dept"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Pilih Dept --</option>
                                @foreach(['IT','HR','Finance','Marketing','Operations','Procurement','Warehouse'] as $d)
                                <option value="{{ $d }}" {{ old('dept') === $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline Persetujuan <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_deadline" required x-model="deadline" value="{{ old('tgl_deadline') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                            @error('tgl_deadline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Interval Reminder Email</label>
                            <select name="reminder_interval"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Pilih Interval --</option>
                                <option value="1" {{ old('reminder_interval') == '1' ? 'selected' : '' }}>Setiap 1 Hari</option>
                                <option value="3" {{ old('reminder_interval') == '3' ? 'selected' : '' }}>Setiap 3 Hari</option>
                                <option value="7" {{ old('reminder_interval') == '7' ? 'selected' : '' }}>Setiap 7 Hari</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Notifikasi email dikirim ke approver sesuai interval</p>
                        </div>
                    </div>

                    {{-- Upload area --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Dokumen</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-teal-400 transition-colors">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-sm text-gray-500">Drag & drop atau klik untuk upload</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, DOCX, XLSX — Maks. 10MB</p>
                            <p class="text-xs text-amber-600 mt-2">(Simulasi — file tidak benar-benar disimpan)</p>
                        </div>
                    </div>

                    {{-- Approver & Reviewer Steps --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Approver & Reviewer <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400 mb-3">Tambahkan langkah persetujuan. Isi nama, jabatan, peran, status aksi, dan paraf.</p>
                        <div class="space-y-2">
                            <template x-for="(step, idx) in steps" :key="idx">
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 bg-teal-600 text-white text-xs rounded-full flex items-center justify-center font-bold flex-shrink-0" x-text="idx+1"></span>
                                        <select @change="onUserChange(step, $event.target.value)"
                                                :name="'step_user[]'" required
                                                class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-teal-500">
                                            <option value="">-- Pilih User --</option>
                                            @foreach($approvers as $a)
                                            <option value="{{ $a['nama'] }}">{{ $a['nama'] }} — {{ $a['jabatan'] }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" @click="steps.splice(idx,1)" x-show="steps.length > 1"
                                                class="text-red-400 hover:text-red-600 flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2 pl-8">
                                        {{-- Jabatan (auto-filled) --}}
                                        <div class="flex-1">
                                            <input type="text" :name="'step_jabatan[]'" x-model="step.jabatan" placeholder="Jabatan (auto)"
                                                   class="w-full border border-gray-200 bg-gray-100 rounded-lg px-2 py-1.5 text-xs text-gray-600 focus:ring-1 focus:ring-teal-400">
                                        </div>
                                        {{-- Peran --}}
                                        <select x-model="step.peran" :name="'step_peran[]'"
                                                class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-teal-500">
                                            <option value="Approve">Tanda Tangan</option>
                                            <option value="Review">Review Only</option>
                                        </select>
                                        {{-- Status Aksi --}}
                                        <select x-model="step.actionStatus" :name="'step_action_status[]'"
                                                class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-teal-500">
                                            <option value="Prepare">Prepare</option>
                                            <option value="Checked">Checked</option>
                                            <option value="Approve">Approve</option>
                                        </select>
                                        {{-- Paraf --}}
                                        <label class="flex items-center gap-1 text-xs text-gray-600 cursor-pointer whitespace-nowrap">
                                            <input type="checkbox" :name="'step_paraf[]'" x-model="step.paraf"
                                                   class="w-3.5 h-3.5 text-teal-600 rounded">
                                            Paraf
                                        </label>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="steps.push({ user: '', jabatan: '', peran: 'Approve', actionStatus: 'Prepare', paraf: false })"
                                    class="flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800 font-medium mt-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Step Approval
                            </button>
                        </div>
                    </div>

                    {{-- TTD Digital --}}
                    <div class="flex items-center gap-3 p-3 bg-teal-50 border border-teal-200 rounded-lg">
                        <input type="checkbox" name="ttd_digital" id="ttd_digital" value="1" {{ old('ttd_digital') ? 'checked' : '' }}
                               class="w-4 h-4 text-teal-600 rounded border-gray-300 focus:ring-teal-500">
                        <label for="ttd_digital" class="text-sm text-teal-800">
                            <span class="font-medium">Butuh Tanda Tangan Digital</span>
                            <span class="text-teal-600"> — Dokumen akan disimulasikan dengan e-signature setelah disetujui</span>
                        </label>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Latar Belakang</label>
                        <textarea name="keterangan" rows="3" placeholder="Jelaskan isi dokumen, urgensi, dan informasi relevan lainnya..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('e-approval.documents') }}"
                           class="px-6 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">Batal</a>
                        <button type="button" @click="showPreview=true"
                                class="px-5 py-2 border border-teal-400 text-teal-600 text-sm rounded-lg hover:bg-teal-50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview Dokumen
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">Upload & Ajukan Approval</button>
                    </div>
                </form>
            </div>

            {{-- Drag-and-Drop Signature Placement --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <h4 class="font-semibold text-gray-700 text-sm">Penempatan Tanda Tangan (Drag & Drop)</h4>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full">Simulasi</span>
                </div>
                <p class="text-xs text-gray-500 mb-4">Drag kotak tanda tangan di bawah ke area dokumen untuk menentukan posisi tanda tangan. Posisi disimulasikan — tidak tersimpan ke server.</p>

                {{-- Draggable signature boxes --}}
                <div class="flex flex-wrap gap-2 mb-4 min-h-[36px]">
                    <template x-for="(step, idx) in steps" :key="idx">
                        <div draggable="true"
                             @dragstart="handleDragStart($event, idx)"
                             class="cursor-grab active:cursor-grabbing flex items-center gap-1.5 px-3 py-1.5 bg-teal-100 border border-teal-300 rounded-lg text-xs text-teal-800 font-medium select-none hover:bg-teal-200 transition-colors">
                            <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span x-text="(step.user || 'Step ' + (idx+1)) + ' (' + step.peran + ')'"></span>
                        </div>
                    </template>
                    <template x-if="steps.length === 0">
                        <p class="text-xs text-gray-400 italic">Tambahkan approver terlebih dahulu</p>
                    </template>
                </div>

                {{-- Drop zone (simulated document) --}}
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 overflow-hidden"
                     style="height: 200px;"
                     @dragover.prevent
                     @drop="handleDrop($event)">
                    {{-- Background lines to simulate document --}}
                    <div class="absolute inset-0 p-4 pointer-events-none">
                        <div class="space-y-2 opacity-20">
                            <div class="h-3 bg-gray-400 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-400 rounded w-full"></div>
                            <div class="h-3 bg-gray-400 rounded w-5/6"></div>
                            <div class="h-3 bg-gray-400 rounded w-2/3"></div>
                            <div class="h-3 bg-gray-400 rounded w-4/5"></div>
                        </div>
                    </div>
                    <p class="absolute inset-0 flex items-center justify-center text-xs text-gray-400 text-center px-4 pointer-events-none"
                       x-show="dragSigs.length === 0">
                        Area Dokumen — Drag tanda tangan ke area ini untuk meletakkan posisi
                    </p>
                    {{-- Placed signatures --}}
                    <template x-for="sig in dragSigs" :key="sig.idx">
                        <div class="absolute border-2 border-teal-500 bg-white rounded-lg px-2 py-1 text-xs text-teal-700 font-medium shadow-md cursor-pointer"
                             :style="'left:' + sig.x + 'px; top:' + sig.y + 'px; min-width:110px'"
                             @click="dragSigs = dragSigs.filter(s => s.idx !== sig.idx)">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span x-text="sig.label"></span>
                            </div>
                            <span class="text-teal-400 text-xs">klik untuk hapus</span>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-400 mt-2">Klik tanda tangan yang sudah diletakkan untuk menghapusnya dari area dokumen.</p>
            </div>
        </div>

        {{-- Info panel --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h4 class="font-semibold text-gray-700 text-sm mb-3">Alur Approval Default</h4>
                <div class="space-y-3">
                    @foreach(['Manager Departemen','Legal / Compliance','Direktur Keuangan','Direktur Utama'] as $i => $step)
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-teal-600 text-white text-xs rounded-full flex items-center justify-center font-bold flex-shrink-0">{{ $i+1 }}</span>
                        <span class="text-sm text-gray-700">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h4 class="font-semibold text-gray-700 text-sm mb-3">Status Aksi</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">Prepare</span>
                        <span class="text-xs text-gray-500">Belum diproses</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Checked</span>
                        <span class="text-xs text-gray-500">Sudah diperiksa</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Approve</span>
                        <span class="text-xs text-gray-500">Disetujui & ditandatangani</span>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">Mode Simulasi</p>
                        <p class="text-xs text-amber-700 mt-1">Upload file, penempatan TTD, dan pengiriman reminder berjalan sebagai simulasi. Di lingkungan produksi, semua akan terintegrasi secara nyata.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak]{display:none!important}</style>
@endsection
