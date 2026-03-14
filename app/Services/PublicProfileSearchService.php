<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\TrackingSource;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PublicProfileSearchService
{
    /**
     * Per-request timeouts (seconds).
     * Lower than Laravel's default 30s — fail fast, try fallback.
     */
    protected int $searchTimeout  = 10;
    protected int $metaTimeout    = 8;

    public function findBestProfile(Alumni $alumni, TrackingSource $source, array $queries): ?array
    {
        $domain = $this->getSourceDomain($source);

        if (empty($domain)) {
            return null;
        }

        foreach ($queries as $query) {
            $finalQuery = trim($query . ' site:' . $domain);

            $links = $this->searchLinks($finalQuery);

            foreach ($links as $link) {
                if (! $this->matchesSourceDomain($link, $source)) {
                    continue;
                }

                if (! $this->looksLikeProfileUrl($link, $source)) {
                    continue;
                }

                $meta = $this->fetchPageMetadata($link);

                return [
                    'query'           => $finalQuery,
                    'url'             => $link,
                    'judul'           => $meta['title'] ?: 'Profil ' . $source->nama_sumber,
                    'snippet'         => $meta['description'] ?: 'Profil publik ditemukan dari ' . $source->nama_sumber,
                    'nama_terdeteksi' => $this->extractLikelyName($meta['title']) ?: $alumni->nama_lengkap,
                    'afiliasi'        => $this->extractAffiliation($meta['description'], $alumni),
                    'jabatan'         => $this->extractJobTitle($meta['description']),
                    'lokasi'          => $this->extractLocation($meta['description'], $alumni),
                    'tahun_aktivitas' => now()->year,
                ];
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Search — DuckDuckGo then Bing fallback
    // -------------------------------------------------------------------------

    protected function searchLinks(string $query): array
    {
        $links = $this->searchDuckDuckGo($query);
        return ! empty($links) ? $links : $this->searchBing($query);
    }

    protected function searchDuckDuckGo(string $query): array
    {
        try {
            $response = Http::timeout($this->searchTimeout)
                ->withHeaders($this->defaultHeaders())
                ->get('https://html.duckduckgo.com/html/', ['q' => $query]);

            return $response->successful()
                ? $this->extractDuckDuckGoLinks($response->body())
                : [];
        } catch (\Exception $e) {
            Log::warning('DuckDuckGo search failed', ['query' => $query, 'error' => $e->getMessage()]);
            return [];
        }
    }

    protected function searchBing(string $query): array
    {
        try {
            $response = Http::timeout($this->searchTimeout)
                ->withHeaders($this->defaultHeaders())
                ->get('https://www.bing.com/search', ['q' => $query, 'form' => 'QBLH']);

            return $response->successful()
                ? $this->extractBingLinks($response->body())
                : [];
        } catch (\Exception $e) {
            Log::warning('Bing search failed', ['query' => $query, 'error' => $e->getMessage()]);
            return [];
        }
    }

    // -------------------------------------------------------------------------
    // Link extraction
    // -------------------------------------------------------------------------

    protected function extractDuckDuckGoLinks(string $html): array
    {
        if (blank($html)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//a[contains(@class, 'result__a')]");

        $links = [];
        foreach ($nodes as $node) {
            /** @var \DOMElement $node */
            $url = $this->normalizeSearchResultUrl($node->getAttribute('href'));
            if ($url) {
                $links[] = $url;
            }
        }

        libxml_clear_errors();
        return array_values(array_unique($links));
    }

    protected function extractBingLinks(string $html): array
    {
        if (blank($html)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//li[contains(@class,'b_algo')]//h2/a");

        $links = [];
        foreach ($nodes as $node) {
            /** @var \DOMElement $node */
            $url = $this->normalizeSearchResultUrl($node->getAttribute('href'));
            if ($url) {
                $links[] = $url;
            }
        }

        libxml_clear_errors();
        return array_values(array_unique($links));
    }

    protected function normalizeSearchResultUrl(?string $href): ?string
    {
        if (blank($href)) {
            return null;
        }

        if (Str::startsWith($href, '//')) {
            $href = 'https:' . $href;
        }

        if (Str::contains($href, 'duckduckgo.com/l/?')) {
            $query = parse_url($href, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $params);
                if (! empty($params['uddg'])) {
                    return urldecode($params['uddg']);
                }
            }
        }

        return filter_var($href, FILTER_VALIDATE_URL) ? $href : null;
    }

    // -------------------------------------------------------------------------
    // Metadata fetch — only parse the <head> section
    // -------------------------------------------------------------------------

    protected function fetchPageMetadata(string $url): array
    {
        $empty = ['title' => null, 'description' => null];

        try {
            $response = Http::timeout($this->metaTimeout)
                ->withHeaders(array_merge($this->defaultHeaders(), [
                    'Accept' => 'text/html,application/xhtml+xml',
                    'Range'  => 'bytes=0-16383',
                ]))
                ->get($url);

            if (! $response->successful()) {
                return $empty;
            }
        } catch (\Exception $e) {
            Log::warning('fetchPageMetadata failed', ['url' => $url, 'error' => $e->getMessage()]);
            return $empty;
        }

        return $this->parseMetaFromHtml($response->body());
    }

    protected function parseMetaFromHtml(string $html): array
    {
        // Only parse up to </head> — no need to load the full body into DOM
        $headEnd = stripos($html, '</head>');
        if ($headEnd !== false) {
            $html = substr($html, 0, $headEnd + 7);
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $titleNode       = $xpath->query('//title')->item(0);
        $metaDescription = $xpath->query("//meta[@name='description']/@content")->item(0);
        $ogDescription   = $xpath->query("//meta[@property='og:description']/@content")->item(0);

        libxml_clear_errors();

        return [
            'title'       => $titleNode?->nodeValue ? trim($titleNode->nodeValue) : null,
            'description' => $metaDescription?->nodeValue
                ? trim($metaDescription->nodeValue)
                : ($ogDescription?->nodeValue ? trim($ogDescription->nodeValue) : null),
        ];
    }

    // -------------------------------------------------------------------------
    // Domain helpers
    // -------------------------------------------------------------------------

    protected function getSourceDomain(TrackingSource $source): ?string
    {
        if (blank($source->base_url)) {
            return null;
        }

        $host = parse_url($source->base_url, PHP_URL_HOST);
        return $host ? Str::lower(Str::replaceFirst('www.', '', $host)) : null;
    }

    protected function matchesSourceDomain(string $url, TrackingSource $source): bool
    {
        $resultHost   = parse_url($url, PHP_URL_HOST);
        $sourceDomain = $this->getSourceDomain($source);

        if (! $resultHost || ! $sourceDomain) {
            return false;
        }

        $resultHost = Str::lower(Str::replaceFirst('www.', '', $resultHost));
        return $resultHost === $sourceDomain || Str::endsWith($resultHost, '.' . $sourceDomain);
    }

    protected function looksLikeProfileUrl(string $url, TrackingSource $source): bool
    {
        $path       = parse_url($url, PHP_URL_PATH) ?: '/';
        $sourceName = Str::lower($source->nama_sumber);

        if (Str::contains($sourceName, 'linkedin'))    return Str::contains($path, ['/in/', '/pub/']);
        if (Str::contains($sourceName, 'scholar'))     return Str::contains($path, '/citations');
        if (Str::contains($sourceName, 'researchgate'))return Str::contains($path, ['/profile/', '/scientific-contributions/']);
        if (Str::contains($sourceName, 'orcid'))       return (bool) preg_match('/\/\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $path);
        if (Str::contains($sourceName, 'github')) {
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            return count($segments) === 1;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Field extractors
    // -------------------------------------------------------------------------

    protected function extractLikelyName(?string $title): ?string
    {
        if (blank($title)) return null;
        $parts = preg_split('/\s[\|\-–]\s/', $title);
        return trim($parts[0] ?? $title);
    }

    protected function extractAffiliation(?string $description, Alumni $alumni): ?string
    {
        if (blank($description)) return $alumni->program_studi;
        if (Str::contains(Str::lower($description), Str::lower($alumni->program_studi))) return $alumni->program_studi;
        return Str::limit(trim($description), 150);
    }

    protected function extractJobTitle(?string $description): ?string
    {
        if (blank($description)) return null;
        $parts = preg_split('/[|,.-]/', $description);
        return isset($parts[0]) ? trim($parts[0]) : null;
    }

    protected function extractLocation(?string $description, Alumni $alumni): ?string
    {
        if (blank($description)) return $alumni->kota;
        if (! empty($alumni->kota) && Str::contains(Str::lower($description), Str::lower($alumni->kota))) return $alumni->kota;
        return $alumni->kota;
    }

    // -------------------------------------------------------------------------
    // Shared
    // -------------------------------------------------------------------------

    protected function defaultHeaders(): array
    {
        return [
            'User-Agent'      => 'Mozilla/5.0 (compatible; AlumniTrackerBot/1.0; +https://example.com/bot)',
            'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
        ];
    }
}