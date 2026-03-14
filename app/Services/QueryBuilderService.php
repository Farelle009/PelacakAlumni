<?php

namespace App\Services;

use App\Models\Alumni;
use Illuminate\Support\Str;

class QueryBuilderService
{
    public function buildNameVariations(Alumni $alumni): array
    {
        $fullName = trim($alumni->nama_lengkap);
        $normalized = preg_replace('/\s+/', ' ', $fullName);

        $variations = [$normalized];

        $parts = explode(' ', $normalized);

        if (count($parts) >= 2) {
            $firstName = $parts[0];
            $lastName = end($parts);

            $variations[] = $firstName;
            $variations[] = $firstName . ' ' . $lastName;
        }

        return array_values(array_unique(array_filter($variations)));
    }

    public function buildContextKeywords(Alumni $alumni): array
    {
        $keywords = [
            $alumni->program_studi,
            $alumni->kota,
            $alumni->tahun_lulus,
        ];

        return array_values(array_unique(array_filter($keywords)));
    }

    public function buildQueries(Alumni $alumni, array $affiliationKeywords = []): array
    {
        $nameVariations = $this->buildNameVariations($alumni);
        $contextKeywords = $this->buildContextKeywords($alumni);

        $defaultAffiliations = [
            'universitas',
            'alumni',
            'lulusan',
        ];

        $affiliations = array_values(array_unique(array_filter(array_merge(
            $defaultAffiliations,
            $affiliationKeywords
        ))));

        $queries = [];

        foreach ($nameVariations as $name) {
            $queries[] = "\"{$name}\"";
            $queries[] = "\"{$name}\" \"{$alumni->program_studi}\"";
            $queries[] = "\"{$name}\" site:linkedin.com/in";
            $queries[] = "\"{$name}\" site:scholar.google.com";
            $queries[] = "\"{$name}\" ORCID";
            $queries[] = "\"{$name}\" ResearchGate";

            foreach ($affiliations as $affiliation) {
                $queries[] = "\"{$name}\" \"{$affiliation}\"";
            }

            foreach ($contextKeywords as $keyword) {
                $queries[] = "\"{$name}\" \"{$keyword}\"";
            }

            if (!empty($alumni->kota)) {
                $queries[] = "\"{$name}\" pekerjaan \"{$alumni->kota}\"";
            }
        }

        return array_values(array_unique(array_filter($queries)));
    }

    public function buildSearchProfile(Alumni $alumni, array $affiliationKeywords = []): array
    {
        return [
            'nama_asli' => $alumni->nama_lengkap,
            'variasi_nama' => $this->buildNameVariations($alumni),
            'kata_kunci_konteks' => $this->buildContextKeywords($alumni),
            'queries' => $this->buildQueries($alumni, $affiliationKeywords),
        ];
    }
}