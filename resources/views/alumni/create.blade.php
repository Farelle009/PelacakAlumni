@extends('layouts.app')

@section('title', 'Tambah Alumni')
@section('page-title', 'Tambah Alumni')
@section('page-description', 'Masukkan data alumni baru ke dalam sistem')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            {{-- Section header --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Informasi Alumni</h3>
                <p class="mt-1 text-sm text-slate-500">Lengkapi semua kolom yang tersedia, lalu simpan data.</p>
            </div>

            <div x-data="{ loading: false }">
                <form
                    action="{{ route('alumni.store') }}"
                    method="POST"
                    class="space-y-6"
                    @submit="loading = true"
                >
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        {{-- NIM --}}
                        <div>
                            <label for="nim" class="mb-2 block text-sm font-medium text-slate-700">NIM</label>
                            <input
                                type="text"
                                name="nim"
                                id="nim"
                                value="{{ old('nim') }}"
                                placeholder="Masukkan NIM"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('nim')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                            @error('nim')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama_lengkap" class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input
                                type="text"
                                name="nama_lengkap"
                                id="nama_lengkap"
                                value="{{ old('nama_lengkap') }}"
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('nama_lengkap')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                            @error('nama_lengkap')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Program Studi --}}
                        <div>
                            <label for="program_studi" class="mb-2 block text-sm font-medium text-slate-700">Program Studi</label>
                            <input
                                type="text"
                                name="program_studi"
                                id="program_studi"
                                value="{{ old('program_studi') }}"
                                placeholder="Masukkan program studi"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('program_studi')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                            @error('program_studi')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tahun Lulus --}}
                        <div>
                            <label for="tahun_lulus" class="mb-2 block text-sm font-medium text-slate-700">Tahun Lulus</label>
                            <input
                                type="number"
                                name="tahun_lulus"
                                id="tahun_lulus"
                                value="{{ old('tahun_lulus') }}"
                                min="1900"
                                max="{{ date('Y') }}"
                                placeholder="Contoh: 2024"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('tahun_lulus')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                            @error('tahun_lulus')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('email')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                            @error('email')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kota --}}
                        <div>
                            <label for="kota" class="mb-2 block text-sm font-medium text-slate-700">Kota</label>
                            <input
                                type="text"
                                name="kota"
                                id="kota"
                                value="{{ old('kota') }}"
                                placeholder="Masukkan kota"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('kota')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                            @error('kota')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Pelacakan --}}
                        <div class="md:col-span-2">
                            <label for="status_pelacakan" class="mb-2 block text-sm font-medium text-slate-700">Status Pelacakan</label>
                            <select
                                name="status_pelacakan"
                                id="status_pelacakan"
                                class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                    {{ $errors->has('status_pelacakan')
                                        ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                        : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                            >
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option }}"
                                        @selected(old('status_pelacakan', \App\Models\Alumni::STATUS_BELUM_DILACAK) === $option)>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_pelacakan')
                                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-200 pt-2"></div>

                    {{-- Submit Actions --}}
                    <div class="flex flex-wrap gap-3">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <span x-text="loading ? 'Menyimpan…' : 'Simpan Alumni'"></span>
                        </button>

                        <a
                            href="{{ route('alumni.index') }}"
                            class="inline-flex rounded-xl border border-slate-300 px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection