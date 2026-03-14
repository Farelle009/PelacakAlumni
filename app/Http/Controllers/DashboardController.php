<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\TrackingResult;
use App\Models\TrackingSource;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalAlumni = Alumni::count();

        $totalBelumDilacak = Alumni::where('status_pelacakan', Alumni::STATUS_BELUM_DILACAK)->count();
        $totalTeridentifikasi = Alumni::where('status_pelacakan', Alumni::STATUS_TERIDENTIFIKASI)->count();
        $totalPerluVerifikasi = Alumni::where('status_pelacakan', Alumni::STATUS_PERLU_VERIFIKASI)->count();
        $totalTidakDitemukan = Alumni::where('status_pelacakan', Alumni::STATUS_TIDAK_DITEMUKAN)->count();

        $totalSumberAktif = TrackingSource::where('is_active', true)->count();
        $totalHasilTracking = TrackingResult::count();

        $hasilTerbaru = TrackingResult::with(['alumni', 'trackingSource'])
            ->latest()
            ->take(5)
            ->get();

        $alumniTerbaru = Alumni::latest()
            ->take(5)
            ->get();

        $rataRataConfidence = round(
            (float) TrackingResult::avg('confidence_score'),
            2
        );

        return view('dashboard.index', compact(
            'totalAlumni',
            'totalBelumDilacak',
            'totalTeridentifikasi',
            'totalPerluVerifikasi',
            'totalTidakDitemukan',
            'totalSumberAktif',
            'totalHasilTracking',
            'hasilTerbaru',
            'alumniTerbaru',
            'rataRataConfidence'
        ));
    }
}