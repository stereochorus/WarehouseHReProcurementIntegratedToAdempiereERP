<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.short_title', 'WHR-ePIS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-image: radial-gradient(circle at 25px 25px, rgba(255,255,255,.1) 2px, transparent 0),
                              radial-gradient(circle at 75px 75px, rgba(255,255,255,.1) 2px, transparent 0);
            background-size: 100px 100px;
        }
        .input-focus:focus { box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
        .fade-up { animation: fadeUp 0.5s ease-out; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900">

<div class="min-h-screen flex">

    <!-- Left Panel -->
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-center px-16 bg-pattern">
        <div class="max-w-md">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center font-bold text-xl text-white shadow-lg">
                    W
                </div>
                <div>
                    <h1 class="text-white font-bold text-xl leading-tight">{{ config('app.short_title', 'WHR-ePIS') }}</h1>
                    <p class="text-blue-300 text-sm">Demo v1.0</p>
                </div>
            </div>

            <h2 class="text-white text-3xl font-bold mb-4 leading-tight">
                Sistem Manajemen<br>Terintegrasi
            </h2>
            <p class="text-slate-400 text-base mb-8 leading-relaxed">
                {{ config('app.title', 'Warehouse Human Resource eProcurement Integrated System') }}
            </p>

            <!-- Features list -->
            <div class="space-y-4">
                @foreach([
                    ['icon'=>'📦', 'color'=>'blue',   'title'=>'Warehouse Management',   'desc'=>'Kelola inventory, penerimaan & pengeluaran barang'],
                    ['icon'=>'👥', 'color'=>'green',  'title'=>'Human Resource',          'desc'=>'Data karyawan, absensi, dan payroll'],
                    ['icon'=>'🛒', 'color'=>'purple', 'title'=>'eProcurement',            'desc'=>'Purchase request, approval workflow, laporan pengadaan'],
                ] as $f)
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-lg flex-shrink-0">{{ $f['icon'] }}</div>
                    <div>
                        <p class="text-white font-medium text-sm">{{ $f['title'] }}</p>
                        <p class="text-slate-400 text-xs">{{ $f['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10 p-4 bg-amber-500/20 border border-amber-500/30 rounded-xl">
                <p class="text-amber-300 text-sm font-medium">⚠ Demo Mode</p>
                <p class="text-amber-200/70 text-xs mt-1">Ini adalah simulasi UI/UX. Semua data adalah contoh. Belum terintegrasi dengan Adempiere ERP.</p>
            </div>
        </div>
    </div>

    <!-- Right Panel (Login Form) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md fade-up">

            <!-- Mobile logo -->
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center font-bold text-white">W</div>
                <div>
                    <h1 class="text-white font-bold">{{ config('app.short_title', 'WHR-ePIS') }}</h1>
                    <p class="text-blue-300 text-xs">{{ config('app.title') }}</p>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8 shadow-2xl">
                <h2 class="text-2xl font-bold text-white mb-1">Masuk ke Sistem</h2>
                <p class="text-slate-400 text-sm mb-6">Gunakan akun demo di bawah untuk masuk</p>

                @if(session('success'))
                    <div class="p-3 bg-green-500/20 border border-green-500/30 rounded-lg mb-4">
                        <p class="text-green-300 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" x-data="{ loading: false }" @submit="loading=true">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input-focus w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-blue-400 transition-colors"
                               placeholder="admin@demo.com" required>
                        @error('email')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6" x-data="{ show: false }">
                        <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password"
                                   class="input-focus w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-blue-400 transition-colors pr-12"
                                   placeholder="••••••••" required>
                            <button type="button" @click="show=!show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            :disabled="loading"
                            class="w-full py-3 bg-blue-600 hover:bg-blue-500 disabled:bg-blue-800 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="loading ? 'Memproses...' : 'Masuk ke Sistem'"></span>
                    </button>
                </form>

                <!-- Demo Accounts -->
                <div class="mt-6 p-4 bg-white/5 border border-white/10 rounded-xl" x-data>
                    <p class="text-slate-400 text-xs font-medium mb-3 uppercase tracking-wider">Akun Demo Tersedia</p>
                    <div class="space-y-2">
                        @foreach([
                            ['email'=>'admin@demo.com',   'role'=>'Admin',   'color'=>'blue',  'badge'=>'bg-blue-500/20 text-blue-300'],
                            ['email'=>'manager@demo.com', 'role'=>'Manager', 'color'=>'green', 'badge'=>'bg-green-500/20 text-green-300'],
                            ['email'=>'staff@demo.com',   'role'=>'Staff',   'color'=>'gray',  'badge'=>'bg-gray-500/20 text-gray-300'],
                        ] as $acc)
                        <button type="button"
                                @click="document.querySelector('[name=email]').value='{{ $acc['email'] }}'; document.querySelector('[name=password]').value='demo123'"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/10 transition-colors cursor-pointer">
                            <span class="text-slate-300 text-xs">{{ $acc['email'] }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $acc['badge'] }} font-medium">{{ $acc['role'] }}</span>
                        </button>
                        @endforeach
                    </div>
                    <p class="text-slate-500 text-xs mt-2 text-center">Password: <code class="text-slate-300">demo123</code> | Klik untuk autofill</p>
                </div>
            </div>

            <p class="text-center text-slate-500 text-xs mt-4">
                {{ config('app.title') }} &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</div>

</body>
</html>
