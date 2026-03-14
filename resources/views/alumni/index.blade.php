@extends('layouts.app')

@section('title', 'Data Alumni')
@section('page-title', 'Data Alumni')
@section('page-description', 'Kelola data alumni, cari data, dan lihat status pelacakan')

@section('content')
    <div class="space-y-6">

        {{-- ── Filter Bar ────────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <form action="{{ route('alumni.index') }}" method="GET"
                      class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Cari Alumni</label>
                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ $search }}"
                            placeholder="Nama, NIM, prodi, kota"
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
                            href="{{ route('alumni.index') }}"
                            class="inline-flex rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    </div>
                </form>

                <div class="flex-shrink-0">
                    <a
                        href="{{ route('alumni.create') }}"
                        class="inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700"
                    >
                        + Tambah Alumni
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Table Card ────────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Daftar Alumni</h3>
                    <p class="text-sm text-slate-500">Menampilkan data alumni yang tersimpan pada sistem.</p>
                </div>
            </div>

            @if ($alumni->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto max-w-md rounded-2xl border border-dashed border-slate-300 px-6 py-10">
                        <h4 class="text-base font-semibold text-slate-900">Belum ada data alumni</h4>
                        <p class="mt-2 text-sm text-slate-500">
                            Tambahkan data alumni baru untuk mulai menggunakan sistem.
                        </p>
                        <a
                            href="{{ route('alumni.create') }}"
                            class="mt-5 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700"
                        >
                            Tambah Alumni
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold text-slate-600">Nama</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-600">NIM</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-600">Program Studi</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-600">Tahun Lulus</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-600">Kota</th>
                                <th class="px-6 py-4 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-6 py-4 text-right font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($alumni as $item)
                                @php
                                    $statusMap = [
                                        \App\Models\Alumni::STATUS_TERIDENTIFIKASI  => 'bg-emerald-50 text-emerald-700',
                                        \App\Models\Alumni::STATUS_PERLU_VERIFIKASI => 'bg-amber-50 text-amber-700',
                                        \App\Models\Alumni::STATUS_TIDAK_DITEMUKAN  => 'bg-rose-50 text-rose-700',
                                        \App\Models\Alumni::STATUS_SEDANG_DILACAK   => 'bg-blue-50 text-blue-700',
                                    ];
                                    $badgeClass = $statusMap[$item->status_pelacakan] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <tr class="hover:bg-slate-50">

                                    {{-- Nama --}}
                                    <td class="px-6 py-4">
                                        <div>
                                            <a href="{{ route('alumni.show', $item) }}"
                                               class="font-medium text-slate-900 hover:underline">
                                                {{ $item->nama_lengkap }}
                                            </a>
                                            @if ($item->email)
                                                <p class="mt-1 text-xs text-slate-500">{{ $item->email }}</p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">{{ $item->nim }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $item->program_studi }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $item->tahun_lulus }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $item->kota ?: '-' }}</td>

                                    {{-- Status Badge --}}
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $badgeClass }}">
                                            @if ($item->status_pelacakan === \App\Models\Alumni::STATUS_SEDANG_DILACAK)
                                                <span class="relative flex h-2 w-2">
                                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                                </span>
                                            @endif
                                            {{ $item->status_pelacakan }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a
                                                href="{{ route('alumni.show', $item) }}"
                                                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Detail
                                            </a>
                                            <a
                                                href="{{ route('alumni.edit', $item) }}"
                                                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Edit
                                            </a>
                                            <div x-data="{ loading: false }">
                                                <form
                                                    action="{{ route('alumni.destroy', $item) }}"
                                                    method="POST"
                                                    x-ref="deleteForm"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="button"
                                                        :disabled="loading"
                                                        @click="
                                                            if (confirm('Hapus data alumni {{ addslashes($item->nama_lengkap) }}?')) {
                                                                loading = true;
                                                                $refs.deleteForm.submit();
                                                            }
                                                        "
                                                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-300 px-3 py-2 text-xs font-medium text-rose-700 transition hover:bg-rose-50 disabled:opacity-60"
                                                    >
                                                        <svg x-show="loading" class="h-3 w-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                                        </svg>
                                                        <span x-text="loading ? 'Menghapus…' : 'Hapus'"></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $alumni->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection