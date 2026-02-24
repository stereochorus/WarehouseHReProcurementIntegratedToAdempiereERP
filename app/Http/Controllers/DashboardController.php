<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_items'       => 1247,
            'low_stock_items'   => 23,
            'pending_receiving' => 8,
            'total_employees'   => 156,
            'present_today'     => 142,
            'on_leave'          => 7,
            'pending_pr'        => 14,
            'approved_pr'       => 38,
            'total_pr_value'    => 'Rp 2.847.500.000',
        ];

        $recentActivities = [
            ['time' => '09:15', 'module' => 'Warehouse', 'color' => 'blue',   'action' => 'Penerimaan Barang', 'desc' => 'GR-2024-0089 - 500 unit Laptop Dell XPS dari PT Mitra Teknologi'],
            ['time' => '09:42', 'module' => 'HR',        'color' => 'green',  'action' => 'Check-In Karyawan',  'desc' => '142 dari 149 karyawan telah melakukan check-in'],
            ['time' => '10:00', 'module' => 'Procurement','color'=> 'purple', 'action' => 'PR Disetujui',       'desc' => 'PR-2024-0156 disetujui oleh Manager - ATK Kantor Rp 4.500.000'],
            ['time' => '10:30', 'module' => 'Warehouse', 'color' => 'blue',   'action' => 'Pengeluaran Barang', 'desc' => 'GI-2024-0045 - 20 unit Printer HP ke Dept Marketing'],
            ['time' => '11:15', 'module' => 'Procurement','color'=> 'purple', 'action' => 'PR Baru Dibuat',     'desc' => 'PR-2024-0160 - Pengadaan Server Rack Rp 85.000.000'],
            ['time' => '13:00', 'module' => 'HR',        'color' => 'green',  'action' => 'Pengajuan Cuti',     'desc' => 'Ahmad Fauzi mengajukan cuti 3 hari (25-27 Feb 2024)'],
        ];

        return view('dashboard.index', compact('stats', 'recentActivities'));
    }
}
