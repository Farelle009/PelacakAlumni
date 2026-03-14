<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\TrackingResult;
use App\Models\TrackingSource;
use App\Services\IdentityScoringService;
use App\Services\QueryBuilderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function __construct(
        protected QueryBuilderService $queryBuilderService,
        protected IdentityScoringService $identityScoringService
    ) {
    }

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
        $sources = TrackingSource::query()
            ->where('is_active', true)
            ->orderBy('nama_sumber')
            ->get();

        if ($sources->isEmpty()) {
            return redirect()
                ->route('tracking.index')
                ->with('error', 'Tidak ada sumber tracking yang aktif.');
        }

        $searchProfile = $this->queryBuilderService->buildSearchProfile($alumni);

        foreach ($sources as $source) {
            $query = $this->selectQueryForSource($source, $searchProfile['queries']);

            $candidate = $this->buildSimpleCandidate($alumni, $source, $query);
            $score = $this->identityScoringService->evaluate($alumni, $candidate);

            TrackingResult::create([
                'alumni_id' => $alumni->id,
                'tracking_source_id' => $source->id,
                'query' => $query,
                'judul' => $candidate['judul'] ?? null,
                'snippet' => $candidate['snippet'] ?? null,
                'url' => $candidate['url'] ?? null,
                'nama_terdeteksi' => $candidate['nama_terdeteksi'] ?? null,
                'afiliasi' => $candidate['afiliasi'] ?? null,
                'jabatan' => $candidate['jabatan'] ?? null,
                'lokasi' => $candidate['lokasi'] ?? null,
                'confidence_score' => $score['confidence_score'],
                'status_verifikasi' => $score['status_verifikasi'],
                'tanggal_ditemukan' => now(),
            ]);
        }

        $this->syncAlumniTrackingStatus($alumni->fresh());

        return redirect()
            ->route('tracking.result', $alumni)
            ->with('success', 'Proses tracking alumni berhasil dijalankan.');
    }

    public function result(Alumni $alumni): View
    {
        $alumni->load([
            'trackingResults' => function ($query) {
                $query->with('trackingSource')
                    ->latest();
            }
        ]);

        $results = $alumni->trackingResults;

        $summary = [
            'total' => $results->count(),
            'kemungkinan_kuat' => $results->where('status_verifikasi', TrackingResult::STATUS_KEMUNGKINAN_KUAT)->count(),
            'perlu_verifikasi' => $results->where('status_verifikasi', TrackingResult::STATUS_PERLU_VERIFIKASI)->count(),
            'tidak_cocok' => $results->where('status_verifikasi', TrackingResult::STATUS_TIDAK_COCOK)->count(),
            'confidence_tertinggi' => $results->max('confidence_score') ?? 0,
        ];

        return view('tracking.result', compact('alumni', 'results', 'summary'));
    }

    protected function selectQueryForSource(TrackingSource $source, array $queries): ?string
    {
        $sourceName = strtolower($source->nama_sumber);

        foreach ($queries as $query) {
            $normalizedQuery = strtolower($query);

            if (str_contains($sourceName, 'linkedin') && str_contains($normalizedQuery, 'linkedin.com/in')) {
                return $query;
            }

            if (str_contains($sourceName, 'scholar') && str_contains($normalizedQuery, 'scholar.google.com')) {
                return $query;
            }

            if (str_contains($sourceName, 'orcid') && str_contains($normalizedQuery, 'orcid')) {
                return $query;
            }

            if (str_contains($sourceName, 'researchgate') && str_contains($normalizedQuery, 'researchgate')) {
                return $query;
            }
        }

        return $queries[0] ?? null;
    }

    protected function buildSimpleCandidate(Alumni $alumni, TrackingSource $source, ?string $query): array
    {
        $sourceName = strtolower($source->nama_sumber);

        return [
            'nama_terdeteksi' => $alumni->nama_lengkap,
            'afiliasi' => $alumni->program_studi,
            'jabatan' => str_contains($sourceName, 'linkedin') ? 'Profil profesional terdeteksi' : null,
            'lokasi' => $alumni->kota,
            'judul' => 'Hasil pelacakan ' . $source->nama_sumber . ' - ' . $alumni->nama_lengkap,
            'snippet' => 'Query pencarian disiapkan untuk alumni ' . $alumni->nama_lengkap .
                ($query ? ' dengan kata kunci: ' . $query : ''),
            'url' => $source->base_url,
            'tahun_aktivitas' => now()->year,
        ];
    }

    protected function syncAlumniTrackingStatus(Alumni $alumni): void
    {
        $results = $alumni->trackingResults()->get();

        if ($results->isEmpty()) {
            $alumni->update([
                'status_pelacakan' => Alumni::STATUS_TIDAK_DITEMUKAN,
            ]);

            return;
        }

        $maxScore = (float) $results->max('confidence_score');

        if ($maxScore >= 70) {
            $status = Alumni::STATUS_TERIDENTIFIKASI;
        } elseif ($maxScore >= 40) {
            $status = Alumni::STATUS_PERLU_VERIFIKASI;
        } else {
            $status = Alumni::STATUS_TIDAK_DITEMUKAN;
        }

        $alumni->update([
            'status_pelacakan' => $status,
        ]);
    }
}