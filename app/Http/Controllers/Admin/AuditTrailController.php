<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditTrailController extends Controller
{
    public function index()
    {
        // Kunci Pintu: HANYA Super Admin yang boleh masuk ruangan ini
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Akses Ditolak! Hanya Super Admin yang dapat melihat Log Aktivitas.');
        }

        // Ambil data log terbaru, beserta data user yang melakukannya (causer)
        $logs = Activity::with('causer')->latest()->paginate(20);

        return view('admin.audit.index', compact('logs'));
    }
}
