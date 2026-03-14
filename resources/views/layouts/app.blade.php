<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Alumni Tracker') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <div class="min-h-screen lg:flex">

        {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
        <aside class="w-full border-b border-slate-200 bg-white lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Alumni Tracker</h1>
                    <p class="text-sm text-slate-500">Sistem Pelacakan Alumni</p>
                </div>
            </div>

            <nav class="px-4 pb-6">
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('alumni.index') }}"
                       class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs('alumni.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Data Alumni
                    </a>
                    <a href="{{ route('tracking.index') }}"
                       class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs('tracking.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Tracking Alumni
                    </a>
                </div>
            </nav>
        </aside>

        {{-- ── Main Content ─────────────────────────────────────────────────── --}}
        <div class="flex-1">

            {{-- Topbar --}}
            <header class="border-b border-slate-200 bg-white">
                <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-slate-500">@yield('page-description', 'Kelola data alumni dan hasil pelacakan')</p>
                    </div>
                    <div class="text-sm text-slate-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </header>

            {{-- Flash Messages + Content --}}
            <main class="p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        {{ session('info') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        <p class="mb-2 font-semibold">Terjadi kesalahan:</p>
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- ── Profile Avatar — Fixed bottom-right ─────────────────────────────── --}}
    <div
        class="fixed bottom-6 right-6 z-50"
        x-data="{ open: false }"
        @click.outside="open = false"
    >
        {{-- Popup menu — appears above the avatar --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="absolute bottom-14 right-0 w-56 rounded-2xl bg-white shadow-lg ring-1 ring-slate-200"
        >
            {{-- Admin info header --}}
            <div class="border-b border-slate-100 px-4 py-3">
                <p class="text-xs text-slate-500">Masuk sebagai</p>
                <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">
                    {{ Auth::guard('admin')->user()->username }}
                </p>
                <p class="truncate text-xs text-slate-500">
                    {{ Auth::guard('admin')->user()->email }}
                </p>
            </div>

            {{-- Menu items --}}
            <div class="p-1.5">
                <a
                    href="{{ route('admin.profile') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50"
                    @click="open = false"
                >
                    {{-- Person icon --}}
                    <svg class="h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                    Edit Profil
                </a>

                <div class="my-1 border-t border-slate-100"></div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-rose-600 transition hover:bg-rose-50"
                    >
                        {{-- Logout icon --}}
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Avatar button --}}
        <button
            @click="open = !open"
            class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white shadow-lg ring-2 ring-white transition hover:bg-slate-700 focus:outline-none"
            :class="open ? 'ring-slate-400' : 'ring-white'"
            title="Profil Admin"
        >
            {{ strtoupper(substr(Auth::guard('admin')->user()->username ?? 'A', 0, 1)) }}
        </button>
    </div>

    @stack('scripts')
</body>
</html>