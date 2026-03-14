<?php

namespace App\Jobs;

use App\Models\Alumni;
use App\Models\TrackingResult;
use App\Models\TrackingSource;
use App\Services\IdentityScoringService;
use App\Services\PublicProfileSearchService;
use App\Services\QueryBuilderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunAlumniTracking implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Allow up to 10 minutes for the job (network-heavy).
     */
    public int $timeout = 600;

    /**
     * Retry twice before giving up.
     */
    public int $tries = 2;

    public function __construct(
        public readonly Alumni $alumni
    ) {}

    public function handle(
        QueryBuilderService $queryBuilderService,
        IdentityScoringService $identityScoringService,
        PublicProfileSearchService $publicProfileSearchService
    ): void {
        $sources = TrackingSource::query()
            ->where('is_active', true)
            ->whereNotNull('base_url')
            ->orderBy('nama_sumber')
            ->get();

        if ($sources->isEmpty()) {
            Log::info("RunAlumniTracking: no active sources for alumni #{$this->alumni->id}");
            return;
        }

        $searchProfile = $queryBuilderService->buildSearchProfile($this->alumni);

        foreach ($sources as $source) {
            try {
                $candidate = $publicProfileSearchService->findBestProfile(
                    $this->alumni,
                    $source,
                    $searchProfile['queries']
                );

                if (! $candidate) {
                    continue;
                }

                $score = $identityScoringService->evaluate($this->alumni, $candidate);

                TrackingResult::updateOrCreate(
                    [
                        'alumni_id'          => $this->alumni->id,
                        'tracking_source_id' => $source->id,
                        'url'                => $candidate['url'],
                    ],
                    [
                        'query'              => $candidate['query'] ?? null,
                        'judul'              => $candidate['judul'] ?? null,
                        'snippet'            => $candidate['snippet'] ?? null,
                        'nama_terdeteksi'    => $candidate['nama_terdeteksi'] ?? null,
                        'afiliasi'           => $candidate['afiliasi'] ?? null,
                        'jabatan'            => $candidate['jabatan'] ?? null,
                        'lokasi'             => $candidate['lokasi'] ?? null,
                        'confidence_score'   => $score['confidence_score'],
                        'status_verifikasi'  => $score['status_verifikasi'],
                        'tanggal_ditemukan'  => now(),
                    ]
                );
            } catch (\Throwable $e) {
                // Log and continue — one failing source should not abort the rest
                Log::warning("RunAlumniTracking: source [{$source->nama_sumber}] failed", [
                    'alumni_id' => $this->alumni->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        // Refresh model so syncStatus sees the fresh results
        $this->syncAlumniTrackingStatus($this->alumni->fresh());
    }

    protected function syncAlumniTrackingStatus(Alumni $alumni): void
    {
        $results = $alumni->trackingResults()->get();

        if ($results->isEmpty()) {
            $alumni->update(['status_pelacakan' => Alumni::STATUS_TIDAK_DITEMUKAN]);
            return;
        }

        $maxScore = (float) $results->max('confidence_score');

        $alumni->update([
            'status_pelacakan' => match (true) {
                $maxScore >= 70 => Alumni::STATUS_TERIDENTIFIKASI,
                $maxScore >= 40 => Alumni::STATUS_PERLU_VERIFIKASI,
                default         => Alumni::STATUS_TIDAK_DITEMUKAN,
            },
        ]);
    }
}