<?php

namespace App\Http\Controllers;

use App\Jobs\RunAlumniTracking;
use App\Models\Alumni;
use App\Models\TrackingResult;
use App\Models\TrackingSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    // No more injected services — the job handles all of that.

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $alumni = Alumni::query()
            ->withCount('trackingResults')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('program_studi', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status_pelacakan', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusOptions = [
            Alumni::STATUS_BELUM_DILACAK,
            Alumni::STATUS_TERIDENTIFIKASI,
            Alumni::STATUS_PERLU_VERIFIKASI,
            Alumni::STATUS_TIDAK_DITEMUKAN,
        ];

        $activeSources = TrackingSource::query()
            ->where('is_active', true)
            ->orderBy('nama_sumber')
            ->get();

        return view('tracking.index', compact(
            'alumni',
            'search',
            'status',
            'statusOptions',
            'activeSources'
        ));
    }

    public function run(Alumni $alumni): RedirectResponse
    {
        $hasSources = TrackingSource::query()
            ->where('is_active', true)
            ->whereNotNull('base_url')
            ->exists();

        if (! $hasSources) {
            return redirect()
                ->route('tracking.index')
                ->with('error', 'Tidak ada sumber tracking aktif yang memiliki domain valid.');
        }

        // Mark as in-progress so the UI can show a spinner / badge
        $alumni->update(['status_pelacakan' => Alumni::STATUS_SEDANG_DILACAK]);

        // Dispatch to the queue — returns immediately to the browser
        RunAlumniTracking::dispatch($alumni);

        return redirect()
            ->route('tracking.result', $alumni)
            ->with('info', 'Tracking sedang diproses di background. Refresh halaman ini setelah beberapa menit untuk melihat hasilnya.');
    }

    public function result(Alumni $alumni): View
    {
        $alumni->load([
            'trackingResults' => function ($query) {
                $query->with('trackingSource')->latest();
            },
        ]);

        $results = $alumni->trackingResults;

        $summary = [
            'total'              => $results->count(),
            'kemungkinan_kuat'   => $results->where('status_verifikasi', TrackingResult::STATUS_KEMUNGKINAN_KUAT)->count(),
            'perlu_verifikasi'   => $results->where('status_verifikasi', TrackingResult::STATUS_PERLU_VERIFIKASI)->count(),
            'tidak_cocok'        => $results->where('status_verifikasi', TrackingResult::STATUS_TIDAK_COCOK)->count(),
            'confidence_tertinggi' => $results->max('confidence_score') ?? 0,
        ];

        return view('tracking.result', compact('alumni', 'results', 'summary'));
    }
}