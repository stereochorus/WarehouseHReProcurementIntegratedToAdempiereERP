@extends('layouts.app')
@section('title', 'Status Adempiere ERP')
@section('page-title', 'Status Koneksi Adempiere ERP')

@section('content')
<div class="py-4 max-w-4xl">

    {{-- ── Mode Banner ──────────────────────────────────────────────────────── --}}
    @if($isDemo)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="font-semibold text-amber-800">DEMO_MODE = true — Adempiere Tidak Aktif</p>
            <p class="text-sm text-amber-700 mt-1">
                Koneksi ke Adempiere dinonaktifkan karena <code class="bg-amber-100 px-1 rounded font-mono text-xs">DEMO_MODE=true</code> di <code class="bg-amber-100 px-1 rounded font-mono text-xs">.env</code>.
                Ubah ke <strong>DEMO_MODE=false</strong> untuk mengaktifkan integrasi ERP, lalu restart server.
            </p>
        </div>
    </div>
    @else
        {{-- Summary ──────────────────────────────────────────────────────────── --}}
        @php
            $allOk   = !empty($checks) && collect($checks)->every(fn($c) => $c['ok'] === true);
            $anyFail = collect($checks)->contains(fn($c) => $c['ok'] === false);
        @endphp
        <div class="{{ $allOk ? 'bg-green-50 border-green-200' : ($anyFail ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200') }} border rounded-xl p-4 mb-6 flex items-start gap-3">
            @if($allOk)
            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold text-green-800">Koneksi Adempiere OK</p>
                <p class="text-sm text-green-700 mt-1">
                    Semua tes berhasil. Adempiere dapat dihubungi dan login berhasil.
                    Langkah selanjutnya: pastikan <strong>Service Types</strong> sudah dikonfigurasi di Adempiere agar query data dapat berjalan.
                </p>
            </div>
            @elseif($anyFail)
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold text-red-800">Koneksi Adempiere Bermasalah</p>
                <p class="text-sm text-red-700 mt-1">
                    Satu atau lebih tes gagal. Sistem akan menggunakan data dummy sebagai fallback.
                    Periksa hasil diagnostik di bawah dan ikuti panduan setup.
                </p>
            </div>
            @else
            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold text-blue-800">Diagnostik Sedang Dimuat</p>
                <p class="text-sm text-blue-700 mt-1">Lihat hasil tes di bawah.</p>
            </div>
            @endif
        </div>
    @endif

    {{-- ── Diagnostic Checks ────────────────────────────────────────────────── --}}
    @if(!$isDemo && !empty($checks))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">Hasil Diagnostik</h3>
                <p class="text-xs text-gray-500 mt-0.5">Tes dijalankan langsung (fresh, tidak pakai cache) saat halaman ini dibuka.</p>
            </div>
            <form method="POST" action="{{ route('adempiere.clear-cache') }}">
                @csrf
                <button type="submit"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium px-3 py-1.5 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    Reset Cache &amp; Reload
                </button>
            </form>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($checks as $key => $check)
            <div class="px-5 py-3.5 flex items-start gap-3">
                {{-- Status icon --}}
                @if($check['ok'] === true)
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @elseif($check['ok'] === false)
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800">{{ $check['label'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5 break-words">{{ $check['msg'] }}</p>
                    @if(isset($check['url']))
                    <a href="{{ $check['url'] }}" target="_blank"
                       class="text-xs text-blue-500 hover:text-blue-700 mt-0.5 inline-block break-all">
                        {{ $check['url'] }}
                    </a>
                    @endif
                    {{-- Tampilkan WSDL function signatures untuk debug --}}
                    @if(!empty($check['functions']))
                    <div class="mt-1.5">
                        <p class="text-xs font-medium text-gray-600 mb-0.5">Fungsi tersedia di WSDL:</p>
                        <div class="bg-gray-900 rounded p-2 space-y-0.5">
                            @foreach(array_slice($check['functions'], 0, 10) as $fn)
                            <p class="text-xs font-mono text-green-400">{{ $fn }}</p>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Konfigurasi Aktif ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Konfigurasi Aktif (.env)</h3>
            <p class="text-xs text-gray-500 mt-0.5">Nilai yang sedang digunakan sistem. Password disembunyikan.</p>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                @php
                $cfgRows = [
                    ['ADEMPIERE_BASE_URL',      $config['base_url']],
                    ['ADEMPIERE_USERNAME',      $config['username']],
                    ['ADEMPIERE_PASSWORD',      '••••••••'],
                    ['ADEMPIERE_CLIENT_ID',     $config['client_id']],
                    ['ADEMPIERE_ORG_ID',        $config['org_id']],
                    ['ADEMPIERE_ROLE_ID',       $config['role_id']],
                    ['ADEMPIERE_WAREHOUSE_ID',  $config['warehouse_id']],
                    ['ADEMPIERE_LANG',          $config['language']],
                ];
                @endphp
                @foreach($cfgRows as [$key, $val])
                <div class="flex items-center justify-between py-2 border-b border-gray-100 text-sm">
                    <span class="text-gray-500 text-xs">{{ $key }}</span>
                    <span class="font-mono text-gray-800 text-xs ml-2">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Service Types yang Diperlukan ───────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Service Types yang Diperlukan di Adempiere</h3>
            <p class="text-xs text-gray-500 mt-1">
                Buat semua record ini di Adempiere:
                <strong>System Admin → Application Dictionary → Web Service → Web Service Type</strong>
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Service Type Name</th>
                        <th class="px-4 py-3 text-left">Tabel Adempiere</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Modul</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                    $serviceInfo = [
                        'product_list'       => ['table' => 'M_Product',       'action' => 'Read',   'module' => 'Warehouse'],
                        'stock_list'         => ['table' => 'M_StorageOnHand', 'action' => 'Read',   'module' => 'Warehouse'],
                        'receipt_list'       => ['table' => 'M_InOut',         'action' => 'Read',   'module' => 'Warehouse'],
                        'issue_list'         => ['table' => 'M_InOut',         'action' => 'Read',   'module' => 'Warehouse'],
                        'movement_list'      => ['table' => 'M_InOut',         'action' => 'Read',   'module' => 'Warehouse'],
                        'create_receipt'     => ['table' => 'M_InOut',         'action' => 'Create', 'module' => 'Warehouse'],
                        'create_issue'       => ['table' => 'M_InOut',         'action' => 'Create', 'module' => 'Warehouse'],
                        'requisition_list'   => ['table' => 'M_Requisition',   'action' => 'Read',   'module' => 'Procurement'],
                        'create_requisition' => ['table' => 'M_Requisition',   'action' => 'Create', 'module' => 'Procurement'],
                        'po_list'            => ['table' => 'C_Order',         'action' => 'Read',   'module' => 'Procurement'],
                        'employee_list'      => ['table' => 'C_BPartner',      'action' => 'Read',   'module' => 'HR'],
                        'vendor_list'        => ['table' => 'C_BPartner',      'action' => 'Read',   'module' => 'HR / Procurement'],
                    ];
                    @endphp
                    @foreach($config['service_types'] as $key => $typeName)
                    @php $info = $serviceInfo[$key] ?? ['table' => '-', 'action' => '-', 'module' => '-']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-mono text-blue-700 text-xs font-semibold">{{ $typeName }}</td>
                        <td class="px-4 py-2.5 font-mono text-gray-600 text-xs">{{ $info['table'] }}</td>
                        <td class="px-4 py-2.5">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $info['action'] === 'Create' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $info['action'] }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-xs text-gray-500">{{ $info['module'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Panduan Setup ────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Panduan Setup Adempiere</h3>
        </div>
        <div class="p-5 space-y-5 text-sm text-gray-700">

            <div>
                <p class="font-semibold text-gray-800 mb-1">1. Pastikan Adempiere berjalan & WSDL dapat diakses</p>
                <p class="text-gray-600 text-xs">Buka di browser (harus menampilkan XML WSDL):</p>
                <div class="mt-1 space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
                        <a href="{{ $config['base_url'] }}/ADService?wsdl" target="_blank"
                           class="text-xs font-mono text-blue-600 hover:underline break-all">
                           {{ $config['base_url'] }}/ADService?wsdl
                        </a>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
                        <a href="{{ $config['base_url'] }}/ModelADService?wsdl" target="_blank"
                           class="text-xs font-mono text-blue-600 hover:underline break-all">
                           {{ $config['base_url'] }}/ModelADService?wsdl
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-1">2. Buat Web Service Types di Adempiere</p>
                <ol class="list-decimal list-inside space-y-1 text-gray-600 text-xs ml-2">
                    <li>Buka Adempiere → <strong>System Admin → Application Dictionary → Web Service → Web Service Type</strong></li>
                    <li>Klik "New" untuk membuat record baru</li>
                    <li>Isi <strong>Name</strong> persis sama dengan kolom "Service Type Name" di tabel di atas (contoh: <code class="bg-gray-100 px-1 rounded font-mono">GetProductList</code>)</li>
                    <li>Pilih <strong>Table Name</strong> yang sesuai (contoh: <code class="bg-gray-100 px-1 rounded font-mono">M_Product</code>)</li>
                    <li>Set <strong>Action</strong> = Read atau Create sesuai tabel</li>
                    <li>Assign <strong>Role</strong> yang punya akses ke tabel tersebut</li>
                    <li>Simpan dan ulangi untuk setiap service type</li>
                </ol>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-1">3. Verifikasi dengan Artisan command</p>
                <div class="bg-gray-900 rounded-lg p-3 font-mono text-xs text-green-400">
                    <p># Test koneksi dasar</p>
                    <p>php artisan adempiere:test</p>
                    <p class="mt-1"># Test koneksi + query</p>
                    <p>php artisan adempiere:test --query</p>
                </div>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-1">4. Test ulang di halaman ini</p>
                <p class="text-gray-600 text-xs">
                    Setelah setup selesai, klik <strong>"Reset Cache &amp; Reload"</strong> di bagian Diagnostik di atas
                    untuk memperbarui status koneksi.
                </p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs text-blue-800">
                    <strong>Catatan:</strong> Jika menu "Web Service Type" tidak terlihat, pastikan Anda login sebagai
                    <strong>System Administrator</strong> (bukan role biasa). Cari juga via <em>Application Dictionary → Window/Tab/Field</em>.
                    Nilai ID Client/Org/Role/Warehouse bisa dilihat di <strong>Help → About → System Info</strong> di Adempiere.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
