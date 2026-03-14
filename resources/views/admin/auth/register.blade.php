<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Alumni Tracker') }} — Daftar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Brand --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Alumni Tracker</h1>
            <p class="mt-1 text-sm text-slate-500">Sistem Pelacakan Alumni</p>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">

            <div class="mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Buat akun baru</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Sudah punya akun?
                    <a href="{{ route('admin.login') }}" class="font-medium text-slate-900 underline underline-offset-4 hover:text-slate-700">
                        Masuk di sini
                    </a>
                </p>
            </div>

            <div x-data="{ loading: false }">
                <form action="{{ route('admin.register.post') }}" method="POST" class="space-y-5"
                      @submit="loading = true">
                    @csrf

                    {{-- Username --}}
                    <div>
                        <label for="username" class="mb-2 block text-sm font-medium text-slate-700">Username</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="{{ old('username') }}"
                            placeholder="contoh: admin_baru"
                            autofocus
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                {{ $errors->has('username')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                    : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                        >
                        @error('username')
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
                            placeholder="admin@example.com"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-slate-800 outline-none transition focus:ring-2
                                {{ $errors->has('email')
                                    ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200'
                                    : 'border-slate-300 focus:border-slate-500 focus:ring-slate-200' }}"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
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

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Ulangi password"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg x-show="loading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span x-text="loading ? 'Mendaftar…' : 'Buat Akun'"></span>
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Alumni Tracker') }}
        </p>
    </div>

</body>
</html>