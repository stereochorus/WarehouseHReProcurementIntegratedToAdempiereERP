@extends('layouts.app')
@section('title', 'Buat Purchase Request')
@section('page-title', 'Form Purchase Request Baru')

@section('content')

<script>
const mrDataPR = @json($mrs);
const projectsDataPR = @json($projects);
const projectsMapPR = Object.fromEntries(projectsDataPR.map(function(p) { return [p.kode, p.nama]; }));
</script>

<div class="py-4 max-w-4xl"
     x-data="{
        items: [{nama: '', qty: 1, satuan: 'Unit', est_price: ''}],
        mr_ref: '',
        kode_proyek: '',
        proyek: '',
        get grandTotal() {
            return this.items.reduce(function(s, i) {
                return s + (parseInt(i.qty) || 0) * (parseFloat(i.est_price) || 0);
            }, 0);
        },
        addItem() { this.items.push({nama: '', qty: 1, satuan: 'Unit', est_price: ''}); },
        removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); },
        loadMR() {
            if (!this.mr_ref) return;
            const mr = mrDataPR.find(m => m.no === this.mr_ref);
            if (!mr) return;
            this.kode_proyek = mr.kode_proyek;
            this.proyek = mr.proyek;
            this.items = mr.items.map(function(i) {
                return {nama: i.nama, qty: i.qty, satuan: i.satuan, est_price: ''};
            });
        },
        formatRupiah(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
        }
     }">

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-purple-50 rounded-t-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Form Purchase Request (PR)</h3>
                    <p class="text-xs text-gray-500">Pengajuan PR — mendukung multi-item, data dapat ditarik dari MR yang sudah disetujui</p>
                </div>
            </div>
        </div>

        {{-- Workflow PR --}}
        <div class="px-6 pt-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Alur Persetujuan PR</p>
            <div class="overflow-x-auto pb-2">
                <div class="flex items-start min-w-max gap-0">
                    @php
                        $prSteps = [
                            ['label'=>'Pemohon',    'sub'=>'Pengaju',        'color'=>'bg-purple-600 text-white'],
                            ['label'=>'PPIC',       'sub'=>'Approve',        'color'=>'bg-gray-200 text-gray-500'],
                            ['label'=>'QC / QC Mgr','sub'=>'Review',         'color'=>'bg-gray-200 text-gray-500'],
                            ['label'=>'WH Manager', 'sub'=>'Review',         'color'=>'bg-gray-200 text-gray-500'],
                            ['label'=>'Site CM',    'sub'=>'Review',         'color'=>'bg-gray-200 text-gray-500'],
                            ['label'=>'Cost Ctrl',  'sub'=>'Review',         'color'=>'bg-gray-200 text-gray-500'],
                            ['label'=>'Project Mgr','sub'=>'Final Approve',  'color'=>'bg-gray-200 text-gray-500'],
                        ];
                    @endphp
                    @foreach($prSteps as $i => $step)
                    <div class="flex items-start">
                        <div class="flex flex-col items-center">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold {{ $step['color'] }}">
                                {{ $i + 1 }}
                            </div>
                            <span class="text-xs font-medium text-gray-700 mt-1 whitespace-nowrap">{{ $step['label'] }}</span>
                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $step['sub'] }}</span>
                        </div>
                        @if($i < count($prSteps)-1)
                        <div class="h-0.5 w-6 sm:w-8 bg-gray-200 mt-4 mx-0.5"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('procurement.purchase-requests.store') }}" class="p-6 space-y-5">
            @csrf

            {{-- Tanggal & Ref MR --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pengajuan</label>
                    <input type="date" name="pr_date" value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tarik dari MR (opsional)</label>
                    <select name="mr_ref" x-model="mr_ref" @change="loadMR()"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        <option value="">-- Pilih MR yang sudah disetujui --</option>
                        @foreach($mrs as $mr)
                        <option value="{{ $mr['no'] }}">{{ $mr['no'] }} — {{ $mr['proyek'] }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-0.5">Pilih MR untuk auto-isi item dan proyek.</p>
                </div>
            </div>

            {{-- Proyek --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Proyek <span class="text-red-500">*</span></label>
                    <select name="kode_proyek" x-model="kode_proyek" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                            @change="proyek = projectsMapPR[$event.target.value] || proyek">
                        <option value="">-- Pilih Kode Proyek --</option>
                        @foreach($projects as $p)
                        <option value="{{ $p['kode'] }}">{{ $p['kode'] }} — {{ $p['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Proyek <span class="text-red-500">*</span></label>
                    <input type="text" name="proyek" x-model="proyek" required
                           placeholder="Otomatis dari kode proyek / isi manual"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
            </div>

            {{-- Tabel Item Dinamis --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Daftar Item <span class="text-red-500">*</span></label>
                    <button type="button" @click="addItem()"
                            class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Item
                    </button>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold w-8">#</th>
                                <th class="px-3 py-2 text-left font-semibold">Nama Barang / Jasa</th>
                                <th class="px-3 py-2 text-left font-semibold w-20">Qty</th>
                                <th class="px-3 py-2 text-left font-semibold w-28">Satuan</th>
                                <th class="px-3 py-2 text-left font-semibold w-44">Est. Harga/Satuan <span class="font-normal text-gray-400">(opsional)</span></th>
                                <th class="px-3 py-2 text-right font-semibold w-36">Subtotal</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in items" :key="idx">
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-2 text-gray-400 text-xs" x-text="idx+1"></td>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="`items[${idx}][nama]`" x-model="item.nama" required
                                               placeholder="Nama barang atau jasa..."
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="`items[${idx}][qty]`" x-model="item.qty" min="1" required
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <select :name="`items[${idx}][satuan]`" x-model="item.satuan"
                                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                            <option>Unit</option>
                                            <option>Pcs</option>
                                            <option>Set</option>
                                            <option>Rim</option>
                                            <option>Lusin</option>
                                            <option>Meter</option>
                                            <option>Kg</option>
                                            <option>Liter</option>
                                            <option>Roll</option>
                                            <option>Lembar</option>
                                            <option>Pasang</option>
                                            <option>Box</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="`items[${idx}][est_price]`" x-model="item.est_price"
                                               min="0" placeholder="0"
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-3 py-2 text-right text-gray-600 text-xs whitespace-nowrap"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format((parseInt(item.qty)||0)*(parseFloat(item.est_price)||0))">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click="removeItem(idx)"
                                                x-show="items.length > 1"
                                                class="text-red-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Grand Total --}}
                <div class="mt-3 p-4 bg-purple-50 border border-purple-200 rounded-xl flex items-center justify-between">
                    <span class="text-sm font-medium text-purple-800">Estimasi Total Nilai PR:</span>
                    <span class="text-xl font-bold text-purple-700"
                          x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)">Rp 0</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Estimasi harga bersifat opsional — dapat diisi atau dikosongkan.</p>
            </div>

            {{-- Prioritas & Vendor --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Prioritas</label>
                    <select name="priority" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        <option>Normal</option>
                        <option>Tinggi</option>
                        <option>Rendah</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Vendor (opsional)</label>
                    <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="Nama vendor jika sudah ada..."
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
            </div>

            {{-- Alasan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan / Justifikasi Pembelian <span class="text-red-500">*</span></label>
                <textarea name="reason" required rows="3"
                          placeholder="Jelaskan kebutuhan dan alasan pengadaan ini..."
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm resize-none">{{ old('reason') }}</textarea>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex gap-3">
                <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-sm text-amber-800"><strong>Demo Mode:</strong> PR akan dikirim ke workflow approval simulasi (PPIC → QC → WH Manager → Site CM → Cost Control → Project Manager). No. PR digenerate otomatis.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors">
                    Ajukan Purchase Request
                </button>
                <a href="{{ route('procurement.purchase-requests') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- PR Progress Tracking (dummy data) --}}
    @php
        $dummyProgress = [
            ['id'=>'PR-2024-0159','proyek'=>'Infrastruktur IT Office','status'=>'Pending Finance',
             'steps'=>[
                ['step'=>'Pemohon','status'=>'done','ket'=>'Diajukan'],
                ['step'=>'PPIC','status'=>'done','ket'=>'Disetujui'],
                ['step'=>'QC / QC Mgr','status'=>'done','ket'=>'Disetujui'],
                ['step'=>'WH Manager','status'=>'done','ket'=>'Disetujui'],
                ['step'=>'Site CM','status'=>'active','ket'=>'Sedang Review'],
                ['step'=>'Cost Ctrl','status'=>'pending','ket'=>'Menunggu'],
                ['step'=>'Project Mgr','status'=>'pending','ket'=>'Menunggu'],
             ]],
            ['id'=>'PR-2024-0160','proyek'=>'Gudang Sentral Phase 2','status'=>'Pending Manager',
             'steps'=>[
                ['step'=>'Pemohon','status'=>'done','ket'=>'Diajukan'],
                ['step'=>'PPIC','status'=>'active','ket'=>'Sedang Review'],
                ['step'=>'QC / QC Mgr','status'=>'pending','ket'=>'Menunggu'],
                ['step'=>'WH Manager','status'=>'pending','ket'=>'Menunggu'],
                ['step'=>'Site CM','status'=>'pending','ket'=>'Menunggu'],
                ['step'=>'Cost Ctrl','status'=>'pending','ket'=>'Menunggu'],
                ['step'=>'Project Mgr','status'=>'pending','ket'=>'Menunggu'],
             ]],
        ];
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mt-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <h3 class="font-semibold text-gray-700 text-sm">Progress Tracking PR (In-Progress)</h3>
        </div>
        <div class="p-5 space-y-5">
            @foreach($dummyProgress as $dp)
            <div class="border border-gray-100 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="font-mono text-xs font-semibold text-purple-600">{{ $dp['id'] }}</span>
                        <span class="text-xs text-gray-500 ml-2">— {{ $dp['proyek'] }}</span>
                    </div>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">{{ $dp['status'] }}</span>
                </div>
                <div class="overflow-x-auto">
                    <div class="flex items-start min-w-max gap-0">
                        @foreach($dp['steps'] as $si => $s)
                        <div class="flex items-start">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $s['status'] === 'done'     ? 'bg-green-500 text-white' :
                                       ($s['status'] === 'active'   ? 'bg-purple-600 text-white' :
                                       ($s['status'] === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-400')) }}">
                                    @if($s['status'] === 'done')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($s['status'] === 'active')
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                    @else
                                    {{ $si + 1 }}
                                    @endif
                                </div>
                                <span class="text-xs font-medium text-gray-700 mt-1 whitespace-nowrap">{{ $s['step'] }}</span>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $s['ket'] }}</span>
                            </div>
                            @if($si < count($dp['steps'])-1)
                            <div class="h-0.5 w-6 sm:w-8 mt-4 mx-0.5
                                {{ $s['status'] === 'done' ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>


@endsection
