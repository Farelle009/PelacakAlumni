@extends('layouts.app')

@section('title', 'Hasil Tracking')
@section('page-title', 'Hasil Tracking Alumni')
@section('page-description', 'Lihat hasil pelacakan, confidence score, dan ringkasan verifikasi')

@section('content')
    <div class="space-y-6">

        {{-- ── Processing Banner (only shown when status is sedang_dilacak) ──── --}}
        @if ($alumni->status_pelacakan === \App\Models\Alumni::STATUS_SEDANG_DILACAK)
            <div
                x-data="autoRefresh()"
                x-init="start()"
                class="flex items-start gap-4 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4"
            >
                {{-- Animated ring spinner --}}
                <div class="mt-0.5 flex-shrink-0">
                    <svg class="h-5 w-5 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                </div>

                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-800">Tracking sedang diproses di background</p>
                    <p class="mt-1 text-sm text-blue-700">
                        Halaman ini akan otomatis diperbarui setiap
                        <span class="font-medium" x-text="interval + ' detik'"></span>.
                        Refresh manual jika ingin melihat lebih cepat.
                    </p>

                    {{-- Countdown bar --}}
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-blue-200">
                        <div
                            class="h-full rounded-full bg-blue-500 transition-none"
                            :style="'width:' + progressPercent + '%'"
                        ></div>
                    </div>
                    <p class="mt-1.5 text-xs text-blue-500">
                        Refresh dalam <span x-text="countdown"></span> detik…
                    </p>
                </div>

                {{-- Manual refresh button --}}
                <a
                    href="{{ route('tracking.result', $alumni) }}"
                    class="flex-shrink-0 self-start rounded-xl border border-blue-300 bg-white px-3 py-2 text-xs font-medium text-blue-700 transition hover:bg-blue-50"
                >
                    Refresh
                </a>
            </div>

            @push('scripts')
            <script>
                function autoRefresh() {
                    return {
                        interval: 15,      // seconds between auto-refreshes
                        countdown: 15,
                        progressPercent: 100,
                        timer: null,
                        ticker: null,

                        start() {
                            this.countdown = this.interval;
                            this.progressPercent = 100;

                            // Countdown tick every second
                            this.ticker = setInterval(() => {
                                this.countdown--;
                                this.progressPercent = (this.countdown / this.interval) * 100;

                                if (this.countdown <= 0) {
                                    clearInterval(this.ticker);
                                    window.location.reload();
                                }
                            }, 1000);
                        },
                    };
                }
            </script>
            @endpush
        @endif

        {{-- ── Alumni Info Card ─────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm text-slate-500">Alumni</p>
                    <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $alumni->nama_lengkap }}</h3>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-slate-500">NIM</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $alumni->nim }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Program Studi</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $alumni->program_studi }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Tahun Lulus</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $alumni->tahun_lulus }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Status Pelacakan</p>
                            <div class="mt-1">
                                @php
                                    $statusMap = [
                                        \App\Models\Alumni::STATUS_TERIDENTIFIKASI  => 'bg-emerald-50 text-emerald-700',
                                        \App\Models\Alumni::STATUS_PERLU_VERIFIKASI => 'bg-amber-50 text-amber-700',
                                        \App\Models\Alumni::STATUS_TIDAK_DITEMUKAN  => 'bg-rose-50 text-rose-700',
                                        \App\Models\Alumni::STATUS_SEDANG_DILACAK   => 'bg-blue-50 text-blue-700',
                                    ];
                                    $badgeClass = $statusMap[$alumni->status_pelacakan]
                                        ?? 'bg-slate-100 text-slate-700';
                                @endphp

                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $badgeClass }}">
                                    @if ($alumni->status_pelacakan === \App\Models\Alumni::STATUS_SEDANG_DILACAK)
                                        <span class="relative flex h-2 w-2">
                                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                        </span>
                                    @endif
                                    {{ $alumni->status_pelacakan }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    {{-- Disable "Jalankan Ulang" while processing to prevent double-dispatch --}}
                    @if ($alumni->status_pelacakan === \App\Models\Alumni::STATUS_SEDANG_DILACAK)
                        <button
                            disabled
                            title="Tracking sedang berjalan"
                            class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-slate-300 px-5 py-3 text-sm font-medium text-slate-500"
                        >
                            <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            Sedang Diproses…
                        </button>
                    @else
                        <div x-data="{ loading: false }">
                            <form action="{{ route('tracking.run', $alumni) }}" method="POST"
                                  @submit="loading = true">
                                @csrf
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <svg x-show="loading" class="h-4 w-4 animate-spin"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                    <span x-text="loading ? 'Memproses…' : 'Jalankan Ulang Tracking'"></span>
                                </button>
                            </form>
                        </div>
                    @endif

                    <a
                        href="{{ route('tracking.index') }}"
                        class="inline-flex rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Summary Stats ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Hasil</p>
                <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $summary['total'] }}</h3>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-emerald-200">
                <p class="text-sm text-slate-500">Kemungkinan Kuat</p>
                <h3 class="mt-2 text-3xl font-bold text-emerald-600">{{ $summary['kemungkinan_kuat'] }}</h3>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-amber-200">
                <p class="text-sm text-slate-500">Perlu Verifikasi</p>
                <h3 class="mt-2 text-3xl font-bold text-amber-600">{{ $summary['perlu_verifikasi'] }}</h3>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-rose-200">
                <p class="text-sm text-slate-500">Tidak Cocok</p>
                <h3 class="mt-2 text-3xl font-bold text-rose-600">{{ $summary['tidak_cocok'] }}</h3>
            </div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Confidence Tertinggi</p>
                <h3 class="mt-2 text-3xl font-bold text-slate-900">
                    {{ number_format((float) $summary['confidence_tertinggi'], 2) }}
                </h3>
            </div>
        </div>

        {{-- ── Results List ──────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Daftar Hasil Tracking</h3>
                    <p class="text-sm text-slate-500">
                        Detail hasil tracking yang tersimpan untuk alumni ini.
                    </p>
                </div>
            </div>

            @if ($results->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-12 text-center">
                    @if ($alumni->status_pelacakan === \App\Models\Alumni::STATUS_SEDANG_DILACAK)
                        {{-- Processing state empty: different copy --}}
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50">
                            <svg class="h-6 w-6 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-semibold text-slate-900">Tracking sedang berjalan</h4>
                        <p class="mt-2 text-sm text-slate-500">
                            Hasil akan muncul di sini secara otomatis saat proses selesai.
                        </p>
                    @else
                        <h4 class="text-base font-semibold text-slate-900">Belum ada hasil tracking</h4>
                        <p class="mt-2 text-sm text-slate-500">
                            Jalankan proses tracking untuk mulai menyimpan hasil pencarian.
                        </p>
                        <div x-data="{ loading: false }">
                            <form action="{{ route('tracking.run', $alumni) }}" method="POST"
                                  class="mt-5" @submit="loading = true">
                                @csrf
                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:opacity-60"
                                >
                                    <svg x-show="loading" class="h-4 w-4 animate-spin"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                    </svg>
                                    <span x-text="loading ? 'Memproses…' : 'Jalankan Tracking'"></span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($results as $result)
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-base font-semibold text-slate-900">
                                            {{ $result->judul ?: 'Hasil Tracking' }}
                                        </h4>

                                        @if ($result->status_verifikasi === \App\Models\TrackingResult::STATUS_KEMUNGKINAN_KUAT)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                                {{ $result->status_verifikasi }}
                                            </span>
                                        @elseif ($result->status_verifikasi === \App\Models\TrackingResult::STATUS_PERLU_VERIFIKASI)
                                            <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                                {{ $result->status_verifikasi }}
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700">
                                                {{ $result->status_verifikasi }}
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-slate-600">
                                        Sumber: <span class="font-medium text-slate-900">{{ $result->trackingSource->nama_sumber ?? '-' }}</span>
                                    </p>

                                    @if ($result->query)
                                        <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700 ring-1 ring-slate-200">
                                            <span class="font-medium text-slate-900">Query:</span>
                                            {{ $result->query }}
                                        </div>
                                    @endif
                                </div>

                                <div class="rounded-2xl bg-slate-50 px-5 py-4 text-left ring-1 ring-slate-200 lg:min-w-44 lg:text-right">
                                    <p class="text-xs text-slate-500">Confidence Score</p>
                                    <p class="mt-1 text-2xl font-bold text-slate-900">
                                        {{ number_format((float) $result->confidence_score, 2) }}
                                    </p>
                                </div>
                            </div>

                            @if ($result->snippet)
                                <div class="mt-4">
                                    <p class="text-sm leading-6 text-slate-600">{{ $result->snippet }}</p>
                                </div>
                            @endif

                            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Nama Terdeteksi</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $result->nama_terdeteksi ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Afiliasi</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $result->afiliasi ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Jabatan</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $result->jabatan ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">Lokasi</p>
                                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $result->lokasi ?: '-' }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                @if ($result->url)
                                    <a
                                        href="{{ $result->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Buka Sumber
                                    </a>
                                @endif

                                @if ($result->tanggal_ditemukan)
                                    <p class="text-xs text-slate-400">
                                        Ditemukan pada {{ $result->tanggal_ditemukan->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@stack('scripts')