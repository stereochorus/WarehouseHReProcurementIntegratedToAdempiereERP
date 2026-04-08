@extends('layouts.app')

@section('title', 'Material Request')
@section('page-title', 'Pembuatan Material Request (MR)')

@section('content')
<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Material Request (MR)</h2>
            <p class="text-sm text-gray-500 mt-0.5">Permintaan material/barang dari proyek ke gudang</p>
        </div>
        <button onclick="document.getElementById('modal-mr').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat MR Baru
        </button>
    </div>

    {{-- Stats --}}
    @php
        $totalMR   = count($mrs);
        $disetujui = count(array_filter($mrs, fn($m) => $m['status'] === 'Disetujui'));
        $menunggu  = count(array_filter($mrs, fn($m) => $m['status'] === 'Menunggu'));
        $ditolak   = count(array_filter($mrs, fn($m) => $m['status'] === 'Ditolak'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total MR</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalMR }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $disetujui }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Menunggu</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $menunggu }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Ditolak</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $ditolak }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Material Request</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. MR</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Pemohon</th>
                        <th class="px-4 py-3 text-left font-semibold">Proyek</th>
                        <th class="px-4 py-3 text-left font-semibold">Item(s)</th>
                        <th class="px-4 py-3 text-left font-semibold">Alasan</th>
                        <th class="px-4 py-3 text-left font-semibold">Prioritas</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mrs as $mr)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-purple-600">{{ $mr['no'] }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $mr['tanggal'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $mr['pemohon'] }}</td>
                        <td class="px-4 py-3">
                            <span class="block text-xs font-semibold text-purple-700">{{ $mr['kode_proyek'] }}</span>
                            <span class="block text-xs text-gray-500">{{ $mr['proyek'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php $items = $mr['items']; @endphp
                            <span class="block text-gray-800">{{ $items[0]['nama'] }} — {{ $items[0]['qty'] }} {{ $items[0]['satuan'] }}</span>
                            @if(count($items) > 1)
                                <span class="text-xs text-purple-600">+{{ count($items)-1 }} item lainnya</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $mr['alasan'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $pc = match($mr['prioritas']) {
                                    'Tinggi' => 'bg-red-100 text-red-700',
                                    'Normal' => 'bg-blue-100 text-blue-700',
                                    'Rendah' => 'bg-gray-100 text-gray-600',
                                    default  => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $pc }}">{{ $mr['prioritas'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($mr['status']) {
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Menunggu'  => 'bg-amber-100 text-amber-700',
                                    'Ditolak'   => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $mr['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal MR dengan multi-item --}}
<div id="modal-mr" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4"
         x-data="{
            items: [{nama: '', qty: 1, satuan: 'unit'}],
            addItem() { this.items.push({nama: '', qty: 1, satuan: 'unit'}); },
            removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); }
         }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Buat Material Request</h3>
            <button onclick="document.getElementById('modal-mr').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- MR Approval Flow --}}
        <div class="px-6 py-3 bg-purple-50 border-b border-purple-100">
            <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider mb-2">Alur Review & Approval MR</p>
            <div class="flex items-center gap-1 overflow-x-auto pb-1">
                @foreach([['Pemohon','Pengaju'],['Manager Dept','Review'],['PPIC','Approve'],['Gudang','Proses']] as $i => $s)
                <div class="flex items-center flex-shrink-0">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0 ? 'bg-purple-600 text-white' : 'bg-white border-2 border-purple-300 text-purple-500' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="text-xs font-medium text-purple-800 mt-0.5 whitespace-nowrap">{{ $s[0] }}</span>
                        <span class="text-xs text-purple-500 whitespace-nowrap">{{ $s[1] }}</span>
                    </div>
                    @if($i < 3)
                    <div class="w-6 h-0.5 bg-purple-200 mx-0.5 mb-5"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('procurement.material-request.store') }}" class="p-6 space-y-4">
            @csrf

            {{-- Pemohon & Prioritas --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemohon <span class="text-red-500">*</span></label>
                    <input type="text" name="pemohon" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas <span class="text-red-500">*</span></label>
                    <select name="prioritas" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="Normal">Normal</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
            </div>

            {{-- Proyek --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Proyek <span class="text-red-500">*</span></label>
                    <select name="kode_proyek" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            onchange="(function(sel){var opt=sel.options[sel.selectedIndex];document.getElementById('mr-proyek-nama').value=opt.dataset.nama||'';})(this)">
                        <option value="">-- Pilih Kode --</option>
                        @foreach($projects as $p)
                        <option value="{{ $p['kode'] }}" data-nama="{{ $p['nama'] }}">{{ $p['kode'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek <span class="text-red-500">*</span></label>
                    <input type="text" name="proyek" id="mr-proyek-nama" required placeholder="Otomatis dari kode proyek"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            {{-- Tabel Item Dinamis --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Daftar Item <span class="text-red-500">*</span></label>
                    <button type="button" @click="addItem()"
                            class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Baris
                    </button>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold w-8">#</th>
                                <th class="px-3 py-2 text-left font-semibold">Nama Barang / Material</th>
                                <th class="px-3 py-2 text-left font-semibold w-20">Qty</th>
                                <th class="px-3 py-2 text-left font-semibold w-28">Satuan</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in items" :key="idx">
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-2 text-gray-400 text-xs" x-text="idx+1"></td>
                                    <td class="px-3 py-2">
                                        <input type="text" :name="`items[${idx}][nama]`" x-model="item.nama" required
                                               placeholder="Nama barang..."
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="`items[${idx}][qty]`" x-model="item.qty" min="1" required
                                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <select :name="`items[${idx}][satuan]`" x-model="item.satuan"
                                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                                            <option>unit</option>
                                            <option>pcs</option>
                                            <option>set</option>
                                            <option>meter</option>
                                            <option>roll</option>
                                            <option>rim</option>
                                            <option>lembar</option>
                                            <option>kg</option>
                                            <option>liter</option>
                                            <option>pasang</option>
                                            <option>lusin</option>
                                            <option>box</option>
                                        </select>
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
                <p class="text-xs text-gray-400 mt-1">Klik "Tambah Baris" untuk menambah item lebih dari 1.</p>
            </div>

            {{-- Alasan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Kebutuhan <span class="text-red-500">*</span></label>
                <textarea name="alasan" required rows="2" placeholder="Jelaskan alasan kebutuhan material..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-mr').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">Buat MR</button>
            </div>
        </form>
    </div>
</div>
@endsection
