@extends('layouts.app')

@section('title', 'Purchase Order')
@section('page-title', 'Pembuatan Purchase Order (PO)')

@section('content')

<script>
const approvedPRsData = @json($approvedPRs);
const poProjectsMap = Object.fromEntries((@json($projects)).map(function(p) { return [p.kode, p.nama]; }));
</script>

<div class="pt-4 space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Purchase Order (PO)</h2>
            <p class="text-sm text-gray-500 mt-0.5">Surat pesanan pembelian kepada vendor/supplier — ditarik dari PR yang disetujui</p>
        </div>
        <button onclick="document.getElementById('modal-po').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat PO Baru
        </button>
    </div>

    {{-- Stats --}}
    @php
        $totalPO  = count($pos);
        $diterima = count(array_filter($pos, fn($p) => $p['status'] === 'Diterima'));
        $dikirim  = count(array_filter($pos, fn($p) => $p['status'] === 'Dikirim'));
        $draft    = count(array_filter($pos, fn($p) => $p['status'] === 'Draft'));
        $totalVal = array_sum(array_column($pos, 'total'));
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total PO</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPO }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Sudah Diterima</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $diterima }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Dalam Pengiriman</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $dikirim }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Nilai PO</p>
            <p class="text-lg font-bold text-purple-600 mt-1">Rp {{ number_format($totalVal, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700 text-sm">Daftar Purchase Order</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. PO</th>
                        <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold">Vendor</th>
                        <th class="px-4 py-3 text-left font-semibold">Proyek</th>
                        <th class="px-4 py-3 text-left font-semibold">Ref PR</th>
                        <th class="px-4 py-3 text-left font-semibold">Item(s)</th>
                        <th class="px-4 py-3 text-right font-semibold">Total</th>
                        <th class="px-4 py-3 text-left font-semibold">Est. Kirim</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pos as $po)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-purple-600">{{ $po['no'] }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $po['tanggal'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $po['vendor'] }}</td>
                        <td class="px-4 py-3">
                            <span class="block text-xs font-semibold text-purple-700">{{ $po['kode_proyek'] ?? '-' }}</span>
                            <span class="block text-xs text-gray-500">{{ $po['proyek'] ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if(!empty($po['pr_ref']))
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-mono">{{ $po['pr_ref'] }}</span>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php $poItems = $po['items'] ?? []; @endphp
                            @if(count($poItems))
                                <span class="block text-gray-800 text-xs">{{ $poItems[0]['nama'] }} ({{ $poItems[0]['qty'] }} {{ $poItems[0]['satuan'] }})</span>
                                @if(count($poItems) > 1)
                                <span class="text-xs text-purple-600">+{{ count($poItems)-1 }} item lainnya</span>
                                @endif
                            @else
                                <span class="text-gray-500 text-xs">{{ $po['barang'] ?? '-' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($po['total'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $po['tgl_kirim'] }}</td>
                        <td class="px-4 py-3">
                            @php
                                $cls = match($po['status']) {
                                    'Diterima'  => 'bg-green-100 text-green-700',
                                    'Dikirim'   => 'bg-blue-100 text-blue-700',
                                    'Diproses'  => 'bg-amber-100 text-amber-700',
                                    'Draft'     => 'bg-gray-100 text-gray-600',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">{{ $po['status'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Nilai PO:</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-purple-700">Rp {{ number_format($totalVal, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Modal PO dengan multi-item dari PR --}}
<div id="modal-po" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-4"
         x-data="{
            items: [{nama: '', qty: 1, satuan: 'unit', harga_satuan: 0}],
            pr_ref: '',
            kode_proyek: '',
            proyek: '',
            get grandTotal() {
                return this.items.reduce(function(s, i) {
                    return s + (parseInt(i.qty)||0) * (parseFloat(i.harga_satuan)||0);
                }, 0);
            },
            addItem() { this.items.push({nama: '', qty: 1, satuan: 'unit', harga_satuan: 0}); },
            removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); },
            loadPR() {
                if (!this.pr_ref) return;
                var pr = approvedPRsData.find(function(p) { return p.id === this.pr_ref; }.bind(this));
                if (!pr) return;
                this.kode_proyek = pr.kode_proyek;
                this.proyek = pr.proyek;
                this.items = pr.items.map(function(i) {
                    return {
                        nama: i.nama,
                        qty: i.qty,
                        satuan: i.satuan,
                        harga_satuan: i.est_price || 0
                    };
                });
            }
         }">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Buat Purchase Order Baru</h3>
            <button onclick="document.getElementById('modal-po').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('procurement.purchase-order.store') }}" class="p-6 space-y-4">
            @csrf

            {{-- Pilih PR --}}
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <label class="block text-sm font-medium text-blue-800 mb-1.5">Tarik dari PR yang Disetujui (opsional)</label>
                <select name="pr_ref" x-model="pr_ref" @change="loadPR()"
                        class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Pilih PR untuk auto-isi item --</option>
                    @foreach($approvedPRs as $apr)
                    <option value="{{ $apr['id'] }}">{{ $apr['id'] }} — {{ $apr['proyek'] }} ({{ count($apr['items']) }} item)</option>
                    @endforeach
                </select>
                <p class="text-xs text-blue-600 mt-1">Pilih PR untuk otomatis mengisi item dan proyek dari PR tersebut.</p>
            </div>

            {{-- Vendor --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor/Supplier <span class="text-red-500">*</span></label>
                <select name="vendor" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih Vendor --</option>
                    @foreach($vendors as $v)
                    <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Proyek --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Proyek <span class="text-red-500">*</span></label>
                    <select name="kode_proyek" x-model="kode_proyek" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            @change="proyek = poProjectsMap[$event.target.value] || proyek">
                        <option value="">-- Pilih Kode --</option>
                        @foreach($projects as $p)
                        <option value="{{ $p['kode'] }}">{{ $p['kode'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Proyek <span class="text-red-500">*</span></label>
                    <input type="text" name="proyek" x-model="proyek" required
                           placeholder="Otomatis dari PR / kode proyek"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            {{-- Tabel Item --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Daftar Item PO <span class="text-red-500">*</span></label>
                    <button type="button" @click="addItem()"
                            class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Item
                    </button>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-2 py-2 text-left font-semibold w-6">#</th>
                                <th class="px-2 py-2 text-left font-semibold">Nama Barang</th>
                                <th class="px-2 py-2 text-left font-semibold w-14">Qty</th>
                                <th class="px-2 py-2 text-left font-semibold w-20">Satuan</th>
                                <th class="px-2 py-2 text-left font-semibold w-32">Harga Satuan (Rp)</th>
                                <th class="px-2 py-2 text-right font-semibold w-28">Subtotal</th>
                                <th class="px-2 py-2 w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in items" :key="idx">
                                <tr class="border-t border-gray-100">
                                    <td class="px-2 py-1.5 text-gray-400" x-text="idx+1"></td>
                                    <td class="px-2 py-1.5">
                                        <input type="text" :name="`items[${idx}][nama]`" x-model="item.nama" required
                                               placeholder="Nama barang..."
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-2 py-1.5">
                                        <input type="number" :name="`items[${idx}][qty]`" x-model="item.qty" min="1" required
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-2 py-1.5">
                                        <select :name="`items[${idx}][satuan]`" x-model="item.satuan"
                                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                                            <option>unit</option>
                                            <option>pcs</option>
                                            <option>set</option>
                                            <option>rim</option>
                                            <option>meter</option>
                                            <option>kg</option>
                                            <option>liter</option>
                                            <option>roll</option>
                                            <option>lembar</option>
                                            <option>pasang</option>
                                            <option>box</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-1.5">
                                        <input type="number" :name="`items[${idx}][harga_satuan]`" x-model="item.harga_satuan"
                                               min="1" required placeholder="0"
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    </td>
                                    <td class="px-2 py-1.5 text-right text-gray-600 whitespace-nowrap"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format((parseInt(item.qty)||0)*(parseFloat(item.harga_satuan)||0))">
                                    </td>
                                    <td class="px-2 py-1.5 text-center">
                                        <button type="button" @click="removeItem(idx)"
                                                x-show="items.length > 1"
                                                class="text-red-400 hover:text-red-600 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                {{-- Grand Total --}}
                <div class="mt-2 flex items-center justify-between px-3 py-2 bg-purple-50 border border-purple-200 rounded-lg">
                    <span class="text-xs font-medium text-purple-800">Total Nilai PO:</span>
                    <span class="text-sm font-bold text-purple-700"
                          x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)">Rp 0</span>
                </div>
            </div>

            {{-- Tgl Kirim --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Tanggal Kirim <span class="text-red-500">*</span></label>
                <input type="date" name="tgl_kirim" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-po').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">Buat PO</button>
            </div>
        </form>
    </div>
</div>
@endsection
