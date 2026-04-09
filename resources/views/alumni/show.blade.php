@extends('layouts.app')

@section('title', 'Detail Alumni')
@section('page-title', 'Detail Alumni')
@section('page-description', 'Lihat informasi alumni dan hasil tracking yang terkait')

@section('content')
    <div class="space-y-6">

        {{-- ── Alumni Info Card ─────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">Nama Lengkap</p>
                        <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $alumni->nama_lengkap }}</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                            <p class="text-sm text-slate-500">Kota</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $alumni->kota ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Email</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $alumni->email ?: '-' }}</p>
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
                                    $badgeClass = $statusMap[$alumni->status_pelacakan] ?? 'bg-slate-100 text-slate-700';
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

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('alumni.edit', $alumni) }}"
                        class="inline-flex rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        Edit
                    </a>
                    <a
                        href="{{ route('tracking.result', $alumni) }}"
                        class="inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700"
                    >
                        Lihat Hasil Tracking
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Data PDDIKTI Card ────────────────────────────────────────────── --}}
        @php
            // Cari data PDDIKTI yang cocok dengan nama dan NIM alumni saat ini
            $pddikti = \App\Models\AlumniDetail::whereRaw('LOWER(TRIM(nim)) = LOWER(TRIM(?))', [$alumni->nim])
                        ->whereRaw('LOWER(TRIM(nama)) = LOWER(TRIM(?))', [$alumni->nama_lengkap])
                        ->first();
        @endphp

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Verifikasi PDDIKTI</h3>
                    <p class="mt-1 text-sm text-slate-500">Informasi akademik mahasiswa berdasarkan pangkalan data DIKTI.</p>
                </div>
                
                {{-- Badge Status Terverifikasi --}}
                @if ($pddikti)
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Terverifikasi PDDIKTI
                    </span>
                @else
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/20">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Belum Terverifikasi
                    </span>
                @endif
            </div>

            @if ($pddikti)
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2 lg:grid-cols-4 rounded-xl border border-slate-200 p-5 bg-slate-50/50">
                    <div>
                        <p class="text-sm text-slate-500">Nama Lengkap</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $pddikti->nama ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">NIM / NPK</p>
                        <p class="mt-1 font-semibold text-slate-900">{{ $pddikti->nim ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Jenis Kelamin</p>
                        <p class="mt-1 font-medium text-slate-900">
                            {{ $pddikti->jenis_kelamin == 'L' ? 'Laki-Laki' : ($pddikti->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Status Mahasiswa Saat Ini</p>
                        <div class="mt-1">
                            @php
                                $statusLabel = strtolower($pddikti->status);
                                $statusClass = 'bg-slate-100 text-slate-700';
                                
                                if (str_contains($statusLabel, 'lulus')) {
                                    $statusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
                                } elseif (str_contains($statusLabel, 'aktif')) {
                                    $statusClass = 'bg-blue-50 text-blue-700 ring-blue-600/20';
                                } elseif (str_contains($statusLabel, 'keluar') || str_contains($statusLabel, 'hilang')) {
                                    $statusClass = 'bg-rose-50 text-rose-700 ring-rose-600/20';
                                }
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                {{ $pddikti->status ?: 'Tidak Diketahui' }}
                            </span>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-sm text-slate-500">Universitas / PT</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $pddikti->nama_pt ?: '-' }} <span class="text-xs font-normal text-slate-400">({{ $pddikti->kode_pt }})</span></p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-sm text-slate-500">Program Studi</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $pddikti->prodi ?: '-' }} <span class="text-xs font-normal text-slate-400">({{ $pddikti->jenjang }})</span></p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Tanggal Masuk</p>
                        <p class="mt-1 font-medium text-slate-900">{{ $pddikti->tanggal_masuk ? \Carbon\Carbon::parse($pddikti->tanggal_masuk)->format('d M Y') : '-' }}</p>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                    <p class="text-sm text-slate-500">Data mahasiswa ini belum ditarik dari sistem PDDIKTI atau tidak ditemukan.</p>
                    <a
                        href="{{ route('pddikti.index') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-medium text-slate-700 border border-slate-300 shadow-sm transition hover:bg-slate-50"
                    >
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Cari di PDDIKTI Sekarang
                    </a>
                </div>
            @endif
        </div>

        {{-- ── Summary + Tracking History ───────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- Summary Stats --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan Tracking</h3>
                <p class="mt-1 text-sm text-slate-500">Informasi singkat hasil pelacakan alumni.</p>

                <div class="mt-6 space-y-4">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-sm text-slate-500">Total Hasil</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ $alumni->trackingResults->count() }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-sm text-slate-500">Confidence Tertinggi</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ number_format((float) ($alumni->trackingResults->max('confidence_score') ?? 0), 2) }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="text-sm text-slate-500">Sumber Digunakan</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ $alumni->trackingResults->pluck('trackingSource.nama_sumber')->filter()->unique()->count() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tracking History --}}
            <div class="xl:col-span-2 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Riwayat Hasil Tracking</h3>
                        <p class="text-sm text-slate-500">Daftar hasil tracking yang tersimpan untuk alumni ini.</p>
                    </div>
                </div>

                @if ($alumni->trackingResults->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-10 text-center">
                        <p class="text-sm text-slate-500">Belum ada hasil tracking untuk alumni ini.</p>
                        <a
                            href="{{ route('tracking.index') }}"
                            class="mt-4 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700"
                        >
                            Buka Halaman Tracking
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($alumni->trackingResults->sortByDesc('created_at') as $result)
                            @php
                                $verifikasiMap = [
                                    \App\Models\TrackingResult::STATUS_KEMUNGKINAN_KUAT  => 'bg-emerald-50 text-emerald-700',
                                    \App\Models\TrackingResult::STATUS_PERLU_VERIFIKASI  => 'bg-amber-50 text-amber-700',
                                ];
                                $verifikasiClass = $verifikasiMap[$result->status_verifikasi] ?? 'bg-rose-50 text-rose-700';
                            @endphp
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="space-y-1">
                                        <h4 class="font-semibold text-slate-900">
                                            {{ $result->judul ?: 'Hasil Tracking' }}
                                        </h4>
                                        <p class="text-sm text-slate-600">
                                            Sumber: <span class="font-medium text-slate-900">{{ $result->trackingSource->nama_sumber ?? '-' }}</span>
                                        </p>
                                        @if ($result->nama_terdeteksi)
                                            <p class="text-sm text-slate-600">
                                                Nama Terdeteksi: <span class="font-medium text-slate-900">{{ $result->nama_terdeteksi }}</span>
                                            </p>
                                        @endif
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-left ring-1 ring-slate-200 lg:min-w-32 lg:text-right">
                                        <p class="text-xs text-slate-500">Confidence</p>
                                        <p class="mt-1 text-lg font-bold text-slate-900">
                                            {{ number_format((float) $result->confidence_score, 2) }}
                                        </p>
                                    </div>
                                </div>

                                @if ($result->snippet)
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $result->snippet }}</p>
                                @endif

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $verifikasiClass }}">
                                        {{ $result->status_verifikasi }}
                                    </span>

                                    @if ($result->afiliasi)
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                            {{ $result->afiliasi }}
                                        </span>
                                    @endif

                                    @if ($result->lokasi)
                                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                            {{ $result->lokasi }}
                                        </span>
                                    @endif

                                    @if ($result->url)
                                        <a
                                            href="{{ $result->url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-sm font-medium text-slate-700 underline underline-offset-4 hover:text-slate-900"
                                        >
                                            Buka Sumber
                                        </a>
                                    @endif
                                </div>

                                @if ($result->tanggal_ditemukan)
                                    <p class="mt-3 text-xs text-slate-400">
                                        Ditemukan: {{ $result->tanggal_ditemukan->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection