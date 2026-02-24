@extends('layouts.app')
@section('title', 'Approval PR')
@section('page-title', 'Workflow Approval Purchase Request')

@section('content')
<div class="py-4">
    <!-- Workflow Explanation -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm">
        <h3 class="font-semibold text-gray-800 mb-4">Alur Approval Purchase Request</h3>
        <div class="flex items-center gap-0 overflow-x-auto pb-2">
            @foreach([
                ['step'=>'1','label'=>'PR Dibuat','sub'=>'Pemohon','color'=>'bg-purple-500'],
                ['step'=>'2','label'=>'Manager','sub'=>'Persetujuan Awal','color'=>'bg-amber-500'],
                ['step'=>'3','label'=>'Finance','sub'=>'Cek Anggaran','color'=>'bg-blue-500'],
                ['step'=>'4','label'=>'Purchasing','sub'=>'Proses Pembelian','color'=>'bg-green-500'],
                ['step'=>'5','label'=>'Selesai','sub'=>'PO Diterbitkan','color'=>'bg-gray-400'],
            ] as $i => $s)
            <div class="flex items-center {{ $i < 4 ? 'flex-1 min-w-0' : '' }}">
                <div class="flex flex-col items-center min-w-[70px]">
                    <div class="w-10 h-10 {{ $s['color'] }} rounded-full flex items-center justify-center text-white font-bold text-sm">{{ $s['step'] }}</div>
                    <p class="text-xs font-medium text-gray-700 mt-1 text-center">{{ $s['label'] }}</p>
                    <p class="text-xs text-gray-400 text-center">{{ $s['sub'] }}</p>
                </div>
                @if($i < 4)
                <div class="flex-1 h-0.5 bg-gray-200 mx-1 mb-5"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    @php $role = session('demo_user.role', 'staff'); @endphp

    @if(count($pendingPRs) === 0)
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-sm">
        <div class="text-6xl mb-4">✅</div>
        <h3 class="text-lg font-semibold text-gray-700">Tidak Ada PR yang Perlu Diapprove</h3>
        <p class="text-gray-500 mt-2">Semua purchase request sudah diproses.</p>
    </div>
    @else
    <!-- Pending PRs -->
    <div class="space-y-4">
        @foreach($pendingPRs as $pr)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-amber-50 border-b border-amber-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $pr['id'] }}</p>
                        <p class="text-xs text-gray-500">{{ $pr['date'] }} | Pemohon: {{ $pr['requestor'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">{{ $pr['status'] }}</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $pr['priority']==='Tinggi' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $pr['priority'] }}
                    </span>
                </div>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Item</p>
                        <p class="font-medium text-gray-800">{{ $pr['item'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Departemen</p>
                        <p class="font-medium text-gray-800">{{ $pr['dept'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jumlah</p>
                        <p class="font-medium text-gray-800">{{ number_format($pr['qty']) }} {{ $pr['unit'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Estimasi</p>
                        <p class="font-bold text-purple-700">Rp {{ number_format($pr['total']) }}</p>
                    </div>
                </div>

                @if($role === 'admin' || $role === 'manager')
                <form method="POST" action="{{ route('procurement.approvals.process', $pr['id']) }}"
                      x-data="{ action: '' }" class="flex flex-wrap gap-3 items-end border-t border-gray-100 pt-4">
                    @csrf
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan Approval</label>
                        <input type="text" name="notes" placeholder="Catatan tambahan (opsional)..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" name="action" value="approve"
                                onclick="return confirm('Setujui PR {{ $pr['id'] }}?')"
                                class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Approve
                        </button>
                        <button type="submit" name="action" value="reject"
                                onclick="return confirm('Tolak PR {{ $pr['id'] }}?')"
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reject
                        </button>
                    </div>
                </form>
                @else
                <div class="border-t border-gray-100 pt-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-gray-500">Anda login sebagai <strong>Staff</strong>. Hanya Manager/Admin yang dapat melakukan approval.</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
