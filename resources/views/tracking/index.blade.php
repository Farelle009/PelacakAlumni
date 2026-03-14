@extends('layouts.app')

@section('title', 'Tracking Alumni')
@section('page-title', 'Tracking Alumni')
@section('page-description', 'Jalankan proses pelacakan alumni dan lihat sumber tracking yang aktif')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- ── Main Table Card ──────────────────────────────────────────── --}}
            <div class="xl:col-span-2 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Daftar Alumni untuk Tracking</h3>
                        <p class="text-sm text-slate-500">
                            Pilih alumni lalu jalankan proses tracking dari sumber aktif.
                        </p>
                    </div>
                </div>

                {{-- Filter Form --}}
                <form action="{{ route('tracking.index') }}" method="GET"
                      class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari Alumni</label>
                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ $search }}"
                            placeholder="Nama, NIM, program studi"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>

                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status Pelacakan</label>
                        <select
                            name="status"
                            id="status"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >
                            <option value="">Semua Status</option>
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <button
                            type="submit"
                            class="inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700"
                        >
                            Filter
                        </button>
                        <a
                            href="{{ route('tracking.index') }}"
                            class="inline-flex rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>

                @if ($alumni->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-12 text-center">
                        <h4 class="text-base font-semibold text-slate-900">Tidak ada data alumni</h4>
                        <p class="mt-2 text-sm text-slate-500">
                            Tambahkan data alumni terlebih dahulu sebelum menjalankan tracking.
                        </p>
                        <a
                            href="{{ route('alumni.create') }}"
                            class="mt-5 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700"
                        >
                            Tambah Alumni
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-4 text-left font-semibold text-slate-600">Nama</th>
                                    <th class="px-4 py-4 text-left font-semibold text-slate-600">Program Studi</th>
                                    <th class="px-4 py-4 text-left font-semibold text-slate-600">Status</th>
                                    <th class="px-4 py-4 text-left font-semibold text-slate-600">Hasil Tracking</th>
                                    <th class="px-4 py-4 text-right font-semibold text-slate-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($alumni as $item)
                                    <tr class="hover:bg-slate-50">

                                        {{-- Nama + NIM --}}
                                        <td class="px-4 py-4">
                                            <div>
                                                <p class="font-medium text-slate-900">{{ $item->nama_lengkap }}</p>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    NIM: {{ $item->nim }} • {{ $item->tahun_lulus }}
                                                </p>
                                            </div>
                                        </td>

                                        {{-- Program Studi --}}
                                        <td class="px-4 py-4 text-slate-600">{{ $item->program_studi }}</td>

                                        {{-- Status Badge --}}
                                        <td class="px-4 py-4">
                                            @php
                                                $statusMap = [
                                                    \App\Models\Alumni::STATUS_TERIDENTIFIKASI  => 'bg-emerald-50 text-emerald-700',
                                                    \App\Models\Alumni::STATUS_PERLU_VERIFIKASI => 'bg-amber-50 text-amber-700',
                                                    \App\Models\Alumni::STATUS_TIDAK_DITEMUKAN  => 'bg-rose-50 text-rose-700',
                                                    \App\Models\Alumni::STATUS_SEDANG_DILACAK   => 'bg-blue-50 text-blue-700',
                                                ];
                                                $badgeClass = $statusMap[$item->status_pelacakan]
                                                    ?? 'bg-slate-100 text-slate-700';
                                            @endphp

                                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $badgeClass }}">
                                                @if ($item->status_pelacakan === \App\Models\Alumni::STATUS_SEDANG_DILACAK)
                                                    {{-- Animated spinner dot --}}
                                                    <span class="relative flex h-2 w-2">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                                    </span>
                                                @endif
                                                {{ $item->status_pelacakan }}
                                            </span>
                                        </td>

                                        {{-- Hasil Tracking count --}}
                                        <td class="px-4 py-4 text-slate-600">
                                            {{ $item->tracking_results_count }}
                                        </td>

                                        {{-- Action Buttons --}}
                                        <td class="px-4 py-4">
                                            <div class="flex justify-end gap-2">

                                                {{-- Jalankan Tracking button with Alpine loading state --}}
                                                <div x-data="{ loading: false }">
                                                    <form
                                                        action="{{ route('tracking.run', $item) }}"
                                                        method="POST"
                                                        @submit="loading = true"
                                                    >
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            :disabled="loading"
                                                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-medium text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                                                        >
                                                            {{-- Spinner shown while loading --}}
                                                            <svg
                                                                x-show="loading"
                                                                class="h-3 w-3 animate-spin"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                                            </svg>
                                                            <span x-text="loading ? 'Memproses…' : 'Jalankan Tracking'"></span>
                                                        </button>
                                                    </form>
                                                </div>

                                                <a
                                                    href="{{ route('tracking.result', $item) }}"
                                                    class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                                >
                                                    Lihat Hasil
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 border-t border-slate-200 pt-4">
                        {{ $alumni->links() }}
                    </div>
                @endif
            </div>

            {{-- ── Sidebar Cards ─────────────────────────────────────────────── --}}
            <div class="space-y-6">

                {{-- Active Sources --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Sumber Tracking Aktif</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Sumber publik yang akan dipakai pada proses pelacakan.
                    </p>

                    <div class="mt-5 space-y-3">
                        @forelse ($activeSources as $source)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-medium text-slate-900">{{ $source->nama_sumber }}</h4>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $source->base_url ?: 'URL sumber belum diatur' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                        Aktif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                                Belum ada sumber aktif.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Guide --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Petunjuk Singkat</h3>
                    <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                        <p>1. Pilih alumni dari daftar yang tersedia.</p>
                        <p>2. Tekan tombol <span class="font-medium text-slate-900">Jalankan Tracking</span>.</p>
                        <p>3. Proses berjalan di background — status berubah menjadi
                           <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">sedang dilacak</span>.
                        </p>
                        <p>4. Buka halaman hasil setelah selesai untuk melihat confidence score dan status verifikasi.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection