<?php

namespace Database\Seeders;

use App\Models\TrackingSource;
use Illuminate\Database\Seeder;

class TrackingSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'nama_sumber' => 'LinkedIn',
                'base_url' => 'https://www.linkedin.com',
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'Google Scholar',
                'base_url' => 'https://scholar.google.com',
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'ResearchGate',
                'base_url' => 'https://www.researchgate.net',
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'ORCID',
                'base_url' => 'https://orcid.org',
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'Website Perusahaan',
                'base_url' => null,
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'Portal Berita',
                'base_url' => null,
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'GitHub',
                'base_url' => 'https://github.com',
                'is_active' => true,
            ],
            [
                'nama_sumber' => 'Kaggle',
                'base_url' => 'https://www.kaggle.com',
                'is_active' => false,
            ],
        ];

        foreach ($sources as $source) {
            TrackingSource::create($source);
        }
    }
}