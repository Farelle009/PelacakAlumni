<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\TrackingResult;
use Illuminate\Support\Str;

class IdentityScoringService
{
    public function evaluate(Alumni $alumni, array $candidate): array
    {
        $score = 0;

        $namaTerdeteksi = $candidate['nama_terdeteksi'] ?? '';
        $afiliasi = $candidate['afiliasi'] ?? '';
        $jabatan = $candidate['jabatan'] ?? '';
        $lokasi = $candidate['lokasi'] ?? '';
        $judul = $candidate['judul'] ?? '';
        $snippet = $candidate['snippet'] ?? '';
        $tahunAktivitas = $candidate['tahun_aktivitas'] ?? null;

        if ($this->isNameStrongMatch($alumni->nama_lengkap, $namaTerdeteksi)) {
            $score += 40;
        } elseif ($this->isNamePartialMatch($alumni->nama_lengkap, $namaTerdeteksi)) {
            $score += 25;
        }

        if ($this->containsText($afiliasi, $alumni->program_studi) || $this->containsText($snippet, $alumni->program_studi)) {
            $score += 20;
        }

        if (!empty($alumni->kota) && (
            $this->containsText($lokasi, $alumni->kota) ||
            $this->containsText($snippet, $alumni->kota) ||
            $this->containsText($judul, $alumni->kota)
        )) {
            $score += 15;
        }

        if (!empty($jabatan)) {
            $score += 10;
        }

        if (!empty($tahunAktivitas) && is_numeric($tahunAktivitas)) {
            if ((int) $tahunAktivitas >= (int) $alumni->tahun_lulus) {
                $score += 15;
            }
        }

        if ($score > 100) {
            $score = 100;
        }

        return [
            'confidence_score' => $score,
            'status_verifikasi' => $this->determineStatus($score),
        ];
    }

    public function applyToTrackingResult(Alumni $alumni, TrackingResult $trackingResult): TrackingResult
    {
        $result = $this->evaluate($alumni, [
            'nama_terdeteksi' => $trackingResult->nama_terdeteksi,
            'afiliasi' => $trackingResult->afiliasi,
            'jabatan' => $trackingResult->jabatan,
            'lokasi' => $trackingResult->lokasi,
            'judul' => $trackingResult->judul,
            'snippet' => $trackingResult->snippet,
        ]);

        $trackingResult->confidence_score = $result['confidence_score'];
        $trackingResult->status_verifikasi = $result['status_verifikasi'];

        return $trackingResult;
    }

    public function determineStatus(float $score): string
    {
        if ($score >= 70) {
            return TrackingResult::STATUS_KEMUNGKINAN_KUAT;
        }

        if ($score >= 40) {
            return TrackingResult::STATUS_PERLU_VERIFIKASI;
        }

        return TrackingResult::STATUS_TIDAK_COCOK;
    }

    protected function isNameStrongMatch(string $originalName, string $detectedName): bool
    {
        return $this->normalize($originalName) === $this->normalize($detectedName);
    }

    protected function isNamePartialMatch(string $originalName, string $detectedName): bool
    {
        $original = $this->normalize($originalName);
        $detected = $this->normalize($detectedName);

        if (empty($original) || empty($detected)) {
            return false;
        }

        return Str::contains($detected, $original) || Str::contains($original, $detected);
    }

    protected function containsText(?string $haystack, ?string $needle): bool
    {
        if (empty($haystack) || empty($needle)) {
            return false;
        }

        return Str::contains($this->normalize($haystack), $this->normalize($needle));
    }

    protected function normalize(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $text = Str::lower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}