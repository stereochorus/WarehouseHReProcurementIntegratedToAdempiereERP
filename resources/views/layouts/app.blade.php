<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.short_title', 'WHR-ePIS') }}</title>

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a', 950: '#172554',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-transition { transition: width 0.3s ease; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .nav-item:hover { background-color: rgba(255,255,255,0.1); }
        .nav-item.active { background-color: rgba(255,255,255,0.15); border-right: 3px solid #60a5fa; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Mobile overlay -->
<div x-show="mobileSidebarOpen" @click="mobileSidebarOpen=false"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden" style="display:none;"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'w-64' : 'w-16'"
       class="sidebar-transition fixed inset-y-0 left-0 bg-gradient-to-b from-slate-800 to-slate-900 text-white z-30 flex flex-col
              lg:translate-x-0"
       :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    <!-- Logo -->
    <div class="flex items-center px-4 py-4 border-b border-slate-700 min-h-[65px]">
        <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center font-bold text-sm">
            {{ substr(config('app.short_title', 'W'), 0, 1) }}
        </div>
        <div x-show="sidebarOpen" class="ml-3 overflow-hidden">
            <div class="font-bold text-sm leading-tight">{{ config('app.short_title', 'WHR-ePIS') }}</div>
            <div class="text-xs text-slate-400 leading-tight">Demo v1.0</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-2">

        <!-- Main Dashboard -->
        <div x-show="sidebarOpen" class="px-2 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Main</div>
        <a href="{{ route('dashboard') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Dashboard</span>
        </a>

        <!-- Warehouse Module -->
        <div x-show="sidebarOpen" class="px-2 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider mt-3 mb-1">Warehouse</div>
        <a href="{{ route('warehouse.dashboard') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('warehouse.dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Dashboard</span>
        </a>
        <a href="{{ route('warehouse.inventory') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('warehouse.inventory') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Inventory</span>
        </a>
        <a href="{{ route('warehouse.receiving') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('warehouse.receiving') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Penerimaan Barang</span>
        </a>
        <a href="{{ route('warehouse.issuing') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('warehouse.issuing') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4M4 12l8-8 8 8"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Pengeluaran Barang</span>
        </a>
        <a href="{{ route('warehouse.stock-movement') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('warehouse.stock-movement') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Mutasi Stok</span>
        </a>
        <a href="{{ route('warehouse.reports') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('warehouse.reports') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Laporan Stok</span>
        </a>

        <!-- HR Module -->
        <div x-show="sidebarOpen" class="px-2 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider mt-3 mb-1">Human Resource</div>
        <a href="{{ route('hr.dashboard') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Dashboard</span>
        </a>
        <a href="{{ route('hr.employees') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.employees*') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Data Karyawan</span>
        </a>
        <a href="{{ route('hr.attendance') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.attendance') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Absensi</span>
        </a>
        <a href="{{ route('hr.payroll') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.payroll') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Payroll</span>
        </a>
        <a href="{{ route('hr.reports') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.reports') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Laporan HR</span>
        </a>
        <a href="{{ route('hr.leaves') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.leaves*') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Pengajuan Cuti</span>
        </a>
        <a href="{{ route('hr.sick-leaves') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.sick-leaves*') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Pengajuan Sakit</span>
        </a>
        <a href="{{ route('hr.overtime') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.overtime*') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Pengajuan Lembur</span>
        </a>
        <a href="{{ route('hr.leave-reports') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('hr.leave-reports') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Laporan Cuti/Sakit/Lembur</span>
        </a>

        <!-- Procurement Module -->
        <div x-show="sidebarOpen" class="px-2 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider mt-3 mb-1">eProcurement</div>
        <a href="{{ route('procurement.dashboard') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('procurement.dashboard') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Dashboard</span>
        </a>
        <a href="{{ route('procurement.purchase-requests') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('procurement.purchase-requests*') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Purchase Request</span>
        </a>
        <a href="{{ route('procurement.approvals') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('procurement.approvals') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Approval</span>
        </a>
        <a href="{{ route('procurement.reports') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('procurement.reports') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Laporan Pengadaan</span>
        </a>

        {{-- ERP System link (hanya ketika DEMO_MODE=false) --}}
        @if(env('DEMO_MODE', 'true') !== 'true')
        <div x-show="sidebarOpen" class="px-2 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider mt-3 mb-1">System</div>
        <a href="{{ route('adempiere.status') }}"
           class="nav-item flex items-center px-3 py-2.5 rounded-lg mb-1 text-slate-200 hover:text-white transition-colors {{ request()->routeIs('adempiere.*') ? 'active' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
            </svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm">Status ERP</span>
        </a>
        @endif

    </nav>

    <!-- Sidebar toggle (desktop) -->
    <div class="px-4 py-3 border-t border-slate-700">
        <button @click="sidebarOpen=!sidebarOpen" class="w-full flex items-center justify-center p-2 rounded-lg hover:bg-slate-700 transition-colors text-slate-400 hover:text-white">
            <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
        </button>
    </div>
</aside>

<!-- Main Content -->
<div :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'" class="sidebar-transition min-h-screen flex flex-col">

    <!-- Top Header -->
    <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center gap-3">
            <!-- Mobile hamburger -->
            <button @click="mobileSidebarOpen=!mobileSidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <!-- Page title -->
            <div>
                <h1 class="text-base font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-500">{{ config('app.title', 'WHR-ePIS') }}</p>
            </div>
        </div>

        <!-- Right side: user info -->
        <div class="flex items-center gap-3">
            <!-- Demo/ERP Badge -->
            @if(env('DEMO_MODE', 'true') === 'true')
            <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5 animate-pulse"></span>
                DEMO MODE
            </span>
            @else
            <a href="{{ route('adempiere.status') }}"
               class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                Adempiere ERP
            </a>
            @endif

            <!-- Notification bell -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open=!open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        {{ substr(session('demo_user.name', 'U'), 0, 1) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-medium text-gray-700 leading-tight">{{ session('demo_user.name', 'User') }}</p>
                        <p class="text-xs text-gray-500 capitalize leading-tight">{{ session('demo_user.role', 'staff') }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" @click.away="open=false"
                     class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50"
                     style="display:none;">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-800">{{ session('demo_user.name') }}</p>
                        <p class="text-xs text-gray-500">{{ session('demo_user.email') }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 mt-1 capitalize">
                            {{ session('demo_user.role') }}
                        </span>
                    </div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/></svg>
                        Dashboard
                    </a>
                    <div class="border-t border-gray-100 mt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Alert Messages -->
    <div class="px-6 pt-4">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show=false, 5000)"
                 class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl mb-2 fade-in">
                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <div>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        @endif
        @if(session('info'))
            <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl mb-2 fade-in">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <p class="text-sm text-blue-800">{{ session('info') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl mb-2 fade-in">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <ul class="text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="flex-1 px-6 pb-6 fade-in">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 px-6 py-3 bg-white">
        <p class="text-xs text-gray-400 text-center">
            {{ config('app.title') }} &copy; {{ date('Y') }} —
            @if(env('DEMO_MODE', 'true') === 'true')
                <span class="text-amber-600 font-medium">DEMO MODE - Simulasi UI/UX</span> —
                Belum terintegrasi dengan Adempiere ERP
            @else
                <span class="text-green-600 font-medium">Mode Produksi</span> —
                Terintegrasi dengan <a href="{{ route('adempiere.status') }}" class="text-blue-500 hover:underline">Adempiere ERP</a>
            @endif
        </p>
    </footer>
</div>

@stack('scripts')
</body>
</html>
