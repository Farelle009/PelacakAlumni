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