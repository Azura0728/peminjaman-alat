<?php

namespace App\Http\Controllers\Admin;

use App\Models\LogAktivitas;

trait DashboardActions
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('logs'));
    }
}
