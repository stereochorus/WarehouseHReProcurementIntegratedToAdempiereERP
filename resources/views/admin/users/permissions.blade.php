@extends('layouts.app')
@section('title', 'Hak Akses — ' . $user['name'])
@section('page-title', 'Atur Hak Akses: ' . $user['name'])

@section('content')
<div class="py-4 fade-in">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Hak Akses: {{ $user['name'] }}</h2>
            <p class="text-sm text-gray-500">
                {{ $user['email'] }} · NIP: {{ $user['nip'] }} ·
                <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $user['role'] === 'manager' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($user['role']) }}
                </span>
            </p>
        </div>
    </div>

    {{-- Legend --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5">
        <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wider">Keterangan Aksi</p>
        <div class="flex flex-wrap gap-3">
            @foreach(['view'=>['bg-blue-100 text-blue-700','Lihat Data'],'tambah'=>['bg-green-100 text-green-700','Tambah Data'],'edit'=>['bg-yellow-100 text-yellow-700','Edit Data'],'nonaktif'=>['bg-red-100 text-red-600','Nonaktifkan Data'],'approve'=>['bg-teal-100 text-teal-700','Approve'],'tolak'=>['bg-orange-100 text-orange-700','Tolak']] as $key => $info)
            <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $info[0] }}">
                <span class="w-2 h-2 rounded-full bg-current opacity-60"></span>{{ $info[1] }}
            </span>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.permissions.save', $user['id']) }}">
        @csrf

        <div class="space-y-5">
            @foreach($menuMatrix as $modul => $menus)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Module Header --}}
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700 text-sm flex items-center gap-2">
                        @php
                            $modulIcon = match($modul) {
                                'Warehouse'      => 'text-blue-500',
                                'Human Resource' => 'text-green-500',
                                'eProcurement'   => 'text-purple-500',
                                'Aset IT'        => 'text-indigo-500',
                                'E-Approval'     => 'text-teal-500',
                                default          => 'text-gray-500',
                            };
                        @endphp
                        <span class="w-2.5 h-2.5 rounded-full bg-current {{ $modulIcon }}"></span>
                        {{ $modul }}
                    </h3>
                    {{-- Select All for module --}}
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs text-gray-500 hover:text-gray-700"
                           x-data
                           @click.prevent="
                               $el.closest('.bg-white').querySelectorAll('input[type=checkbox]')
                                  .forEach(cb => cb.checked = !$el.querySelector('input').checked)
                           ">
                        <input type="checkbox" class="w-3.5 h-3.5 text-blue-600 rounded">
                        <span>Pilih Semua</span>
                    </label>
                </div>

                {{-- Menu rows --}}
                <div class="divide-y divide-gray-50">
                    @foreach($menus as $menu => $actions)
                    @php
                        $slug = \Illuminate\Support\Str::slug($menu);
                        $isDefaultGranted = isset($defaultPerms[$menu]);
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                        <div class="w-48 flex-shrink-0">
                            <span class="text-sm text-gray-700">{{ $menu }}</span>
                            @if($isDefaultGranted)
                            <span class="ml-1 text-xs text-green-600">(default)</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @foreach($actions as $action)
                            @php
                                $isChecked = $isDefaultGranted && in_array($action, $defaultPerms[$menu] ?? []);
                                $badgeColor = match($action) {
                                    'view'    => 'text-blue-600',
                                    'tambah'  => 'text-green-600',
                                    'edit'    => 'text-yellow-600',
                                    'nonaktif'=> 'text-red-500',
                                    'approve' => 'text-teal-600',
                                    'tolak'   => 'text-orange-600',
                                    default   => 'text-gray-500',
                                };
                            @endphp
                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                <input type="checkbox"
                                       name="perms[{{ $slug }}][{{ $action }}]"
                                       value="1"
                                       {{ $isChecked ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-xs font-medium {{ $badgeColor }} group-hover:underline capitalize">{{ $action }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                Simpan Hak Akses
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-colors">
                Batal
            </a>
            <p class="text-xs text-gray-400 ml-2">Perubahan hak akses aktif pada login berikutnya (simulasi)</p>
        </div>
    </form>
</div>
@endsection
