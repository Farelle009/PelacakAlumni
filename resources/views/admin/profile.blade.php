@extends('layouts.app')

@section('title', 'Profil Admin')
@section('page-title', 'Profil Admin')
@section('page-description', 'Kelola informasi akun dan keamanan Anda')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">

        {{-- ── Info Profil ──────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Informasi Akun</h3>
                <p class="mt-1 text-sm text-slate-500">Perbarui username dan email akun Anda.</p>
            </div>

            <div x-data="{ loading: false }">
                <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-5"
                      @submit="loading = true">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="username" class="mb-2 block text-sm font-medium text-slate-700">Username</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="{{ old('username', $admin->username) }}"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                {{ $errors->has('username')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                    : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                        >
                        @error('username')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $admin->email) }}"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                {{ $errors->has('email')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                    : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-slate-200 pt-2"></div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:opacity-60"
                    >
                        <svg x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span x-text="loading ? 'Menyimpan…' : 'Simpan Perubahan'"></span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Ubah Password ─────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Ubah Password</h3>
                <p class="mt-1 text-sm text-slate-500">Pastikan menggunakan password yang kuat dan unik.</p>
            </div>

            <div x-data="{ loading: false }">
                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-5"
                      @submit="loading = true">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-2 block text-sm font-medium text-slate-700">Password Saat Ini</label>
                        <input
                            type="password"
                            name="current_password"
                            id="current_password"
                            placeholder="••••••••"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                {{ $errors->has('current_password')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                    : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                        >
                        @error('current_password')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="mb-2 block text-sm font-medium text-slate-700">Password Baru</label>
                        <input
                            type="password"
                            name="password"
                            id="new_password"
                            placeholder="Minimal 8 karakter"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                {{ $errors->has('password')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                    : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                        >
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Ulangi password baru"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>

                    <div class="border-t border-slate-200 pt-2"></div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:opacity-60"
                    >
                        <svg x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span x-text="loading ? 'Menyimpan…' : 'Ubah Password'"></span>
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection