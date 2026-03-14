@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan data alumni, sumber tracking, dan hasil pelacakan terbaru')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Alumni</p>
                <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $totalAlumni }}</h3>
                <p class="mt-2 text-sm text-slate-600">Seluruh data alumni yang tersimpan.</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Sumber Aktif</p>
                <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $totalSumberAktif }}</h3>
                <p class="mt-2 text-sm text-slate-600">Sumber publik yang siap dipakai untuk tracking.</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Total Hasil Tracking</p>
                <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ $totalHasilTracking }}</h3>
                <p class="mt-2 text-sm text-slate-600">Semua hasil pencarian yang telah disimpan.</p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm text-slate-500">Rata-rata Confidence</p>
                <h3 class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($rataRataConfidence, 2) }}</h3>
                <p class="mt-2 text-sm text-slate-600">Rerata skor kecocokan hasil tracking.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Belum Dilacak</p>
                <h4 class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalBelumDilacak }}</h4>
                <div class="mt-4">
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                        Menunggu proses tracking
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-white p-5">
                <p class="text-sm text-slate-500">Teridentifikasi</p>
                <h4 class="mt-2 text-2xl font-semibold text-emerald-600">{{ $totalTeridentifikasi }}</h4>
                <div class="mt-4">
                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                        Kandidat kuat ditemukan
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-white p-5">
                <p class="text-sm text-slate-500">Perlu Verifikasi</p>
                <h4 class="mt-2 text-2xl font-semibold text-amber-600">{{ $totalPerluVerifikasi }}</h4>
                <div class="mt-4">
                    <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                        Perlu review manual
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-rose-200 bg-white p-5">
                <p class="text-sm text-slate-500">Tidak Ditemukan</p>
                <h4 class="mt-2 text-2xl font-semibold text-rose-600">{{ $totalTidakDitemukan }}</h4>
                <div class="mt-4">
                    <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700">
                        Belum ada kecocokan
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Alumni Terbaru</h3>
                        <p class="text-sm text-slate-500">5 data alumni yang terakhir ditambahkan.</p>
                    </div>

                    <a href="{{ route('alumni.index') }}"
                       class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                        Lihat Semua
                    </a>
                </div>

                @if ($alumniTerbaru->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada data alumni.
                    </div>
                @else
                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Nama</th>
                                        <th class="px-4 py-3 text-left font-semibold text-slate-600">NIM</th>
                                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Prodi</th>
                                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($alumniTerbaru as $item)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3">
                                                <a href="{{ route('alumni.show', $item) }}"
                                                   class="font-medium text-slate-900 hover:underline">
                                                    {{ $item->nama_lengkap }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-slate-600">{{ $item->nim }}</td>
                                            <td class="px-4 py-3 text-slate-600">{{ $item->program_studi }}</td>
                                            <td class="px-4 py-3">
                                                @if ($item->status_pelacakan === \App\Models\Alumni::STATUS_TERIDENTIFIKASI)
                                                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                                        {{ $item->status_pelacakan }}
                                                    </span>
                                                @elseif ($item->status_pelacakan === \App\Models\Alumni::STATUS_PERLU_VERIFIKASI)
                                                    <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                                        {{ $item->status_pelacakan }}
                                                    </span>
                                                @elseif ($item->status_pelacakan === \App\Models\Alumni::STATUS_TIDAK_DITEMUKAN)
                                                    <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700">
                                                        {{ $item->status_pelacakan }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                                        {{ $item->status_pelacakan }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Hasil Tracking Terbaru</h3>
                        <p class="text-sm text-slate-500">5 hasil tracking terakhir yang tersimpan.</p>
                    </div>

                    <a href="{{ route('tracking.index') }}"
                       class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                        Buka Tracking
                    </a>
                </div>

                @if ($hasilTerbaru->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada hasil tracking.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($hasilTerbaru as $hasil)
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="space-y-1">
                                        <h4 class="font-semibold text-slate-900">
                                            {{ $hasil->judul ?: 'Hasil Tracking' }}
                                        </h4>
                                        <p class="text-sm text-slate-600">
                                            Alumni:
                                            @if ($hasil->alumni)
                                                <a href="{{ route('alumni.show', $hasil->alumni) }}" class="font-medium hover:underline">
                                                    {{ $hasil->alumni->nama_lengkap }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </p>
                                        <p class="text-sm text-slate-600">
                                            Sumber: {{ $hasil->trackingSource->nama_sumber ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="text-left sm:text-right">
                                        <p class="text-xs text-slate-500">Confidence</p>
                                        <p class="text-lg font-bold text-slate-900">
                                            {{ number_format((float) $hasil->confidence_score, 2) }}
                                        </p>
                                    </div>
                                </div>

                                @if ($hasil->snippet)
                                    <p class="mt-3 text-sm leading-6 text-slate-600">
                                        {{ $hasil->snippet }}
                                    </p>
                                @endif

                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    @if ($hasil->status_verifikasi === \App\Models\TrackingResult::STATUS_KEMUNGKINAN_KUAT)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                            {{ $hasil->status_verifikasi }}
                                        </span>
                                    @elseif ($hasil->status_verifikasi === \App\Models\TrackingResult::STATUS_PERLU_VERIFIKASI)
                                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">
                                            {{ $hasil->status_verifikasi }}
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-medium text-rose-700">
                                            {{ $hasil->status_verifikasi }}
                                        </span>
                                    @endif

                                    @if ($hasil->url)
                                        <a href="{{ $hasil->url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="text-sm font-medium text-slate-700 underline underline-offset-4 hover:text-slate-900">
                                            Buka Sumber
                                        </a>
                                    @endif

                                    @if ($hasil->alumni)
                                        <a href="{{ route('tracking.result', $hasil->alumni) }}"
                                           class="text-sm font-medium text-slate-700 underline underline-offset-4 hover:text-slate-900">
                                            Lihat Detail
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <a href="{{ route('alumni.index') }}"
               class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-base font-semibold text-slate-900">Kelola Data Alumni</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Tambah, ubah, dan lihat detail data alumni.
                </p>
            </a>

            <a href="{{ route('alumni.create') }}"
               class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-base font-semibold text-slate-900">Tambah Alumni</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Input data alumni baru ke dalam sistem.
                </p>
            </a>

            <a href="{{ route('tracking.index') }}"
               class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                <h3 class="text-base font-semibold text-slate-900">Jalankan Tracking</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Lakukan proses pelacakan alumni dari sumber aktif.
                </p>
            </a>
        </div>
    </div>
@endsection