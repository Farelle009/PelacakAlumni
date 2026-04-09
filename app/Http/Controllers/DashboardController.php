<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\TrackingResult;
use App\Models\TrackingSource;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Single query — group all status counts in one round-trip
        $statusCounts = Alumni::query()
            ->select('status_pelacakan', DB::raw('count(*) as total'))
            ->groupBy('status_pelacakan')
            ->pluck('total', 'status_pelacakan');

        $totalAlumni          = $statusCounts->sum();
        $totalBelumDilacak    = (int) ($statusCounts[Alumni::STATUS_BELUM_DILACAK]    ?? 0);
        $totalSedangDilacak   = (int) ($statusCounts[Alumni::STATUS_SEDANG_DILACAK]   ?? 0);
        $totalTeridentifikasi = (int) ($statusCounts[Alumni::STATUS_TERIDENTIFIKASI]  ?? 0);
        $totalPerluVerifikasi = (int) ($statusCounts[Alumni::STATUS_PERLU_VERIFIKASI] ?? 0);
        $totalTidakDitemukan  = (int) ($statusCounts[Alumni::STATUS_TIDAK_DITEMUKAN]  ?? 0);

        // PDDIKTI Stats
        $totalPddiktiTerverifikasi = Alumni::whereExists(function ($query) {
            $query->selectRaw('1')
                  ->from('alumni_details')
                  ->whereRaw('LOWER(TRIM(alumni_details.nim)) = LOWER(TRIM(alumni.nim))')
                  ->whereRaw('LOWER(TRIM(alumni_details.nama)) = LOWER(TRIM(alumni.nama_lengkap))');
        })->count();
        $totalPddiktiBelum = $totalAlumni - $totalPddiktiTerverifikasi;

        $totalSumberAktif   = TrackingSource::where('is_active', true)->count();
        $totalHasilTracking = TrackingResult::count();
        $rataRataConfidence = round((float) TrackingResult::avg('confidence_score'), 2);

        $hasilTerbaru = TrackingResult::with(['alumni', 'trackingSource'])
            ->latest()
            ->take(5)
            ->get();

        // Include is_pddikti_verified for the dashboard table
        $alumniTerbaru = Alumni::query()
            ->select('alumni.*')
            ->selectSub(function ($query) {
                $query->selectRaw('1')
                      ->from('alumni_details')
                      ->whereRaw('LOWER(TRIM(alumni_details.nim)) = LOWER(TRIM(alumni.nim))')
                      ->whereRaw('LOWER(TRIM(alumni_details.nama)) = LOWER(TRIM(alumni.nama_lengkap))')
                      ->limit(1);
            }, 'is_pddikti_verified')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalAlumni',
            'totalBelumDilacak',
            'totalSedangDilacak',
            'totalTeridentifikasi',
            'totalPerluVerifikasi',
            'totalTidakDitemukan',
            'totalPddiktiTerverifikasi',
            'totalPddiktiBelum',
            'totalSumberAktif',
            'totalHasilTracking',
            'hasilTerbaru',
            'alumniTerbaru',
            'rataRataConfidence'
        ));
    }
}